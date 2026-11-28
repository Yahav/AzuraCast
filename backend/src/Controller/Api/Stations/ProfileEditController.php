<?php

declare(strict_types=1);

namespace App\Controller\Api\Stations;

use App\Controller\Api\Admin\StationsController;
use App\Entity\Api\Status;
use App\Entity\Interfaces\EntityGroupsInterface;
use App\Enums\GlobalPermissions;
use App\Enums\StationPermissions;
use App\Http\Response;
use App\Http\ServerRequest;
use App\OpenApi;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

/**
 * This controller handles the specific "Edit Profile" function on a station's profile, which has different permissions
 * and possible actions than the Admin Station Edit function.
 */
#[
    OA\Get(
        path: '/station/{station_id}/profile/edit',
        operationId: 'getStationProfileEdit',
        summary: 'Get the editable profile for the current station.',
        tags: [OpenApi::TAG_STATIONS],
        parameters: [
            new OA\Parameter(ref: OpenApi::REF_STATION_ID_REQUIRED),
        ],
        responses: [
            // TODO API Response Body
            new OpenApi\Response\Success(),
            new OpenApi\Response\AccessDenied(),
            new OpenApi\Response\NotFound(),
            new OpenApi\Response\GenericError(),
        ]
    ),
    OA\Put(
        path: '/station/{station_id}/profile/edit',
        operationId: 'putStationProfileEdit',
        summary: 'Save the station profile for the current station.',
        tags: [OpenApi::TAG_STATIONS],
        parameters: [
            new OA\Parameter(ref: OpenApi::REF_STATION_ID_REQUIRED),
        ],
        responses: [
            // TODO API Response Body
            new OpenApi\Response\Success(),
            new OpenApi\Response\AccessDenied(),
            new OpenApi\Response\NotFound(),
            new OpenApi\Response\GenericError(),
        ]
    )
]
final class ProfileEditController extends StationsController
{
    public function getProfileAction(
        ServerRequest $request,
        Response $response
    ): ResponseInterface {
        $station = $request->getStation();

        return $response->withJson(
            $this->toArray($station, $this->getContext($request))
        );
    }

    public function putProfileAction(
        ServerRequest $request,
        Response $response
    ): ResponseInterface {
        // Explicitly filter out some configuration sections if the user cannot modify them.
        $requestBody = (array)$request->getParsedBody();

        // Caster.fm: the settings hidden from station owners in the form are also refused here.
        // They keep their stored value.
        if (!$request->getAcl()->isAllowed(GlobalPermissions::All)) {
            $requestBody = self::withoutAdminOnlyConfig($requestBody);
        }

        $this->editRecord($requestBody, $request->getStation(), $this->getContext($request));

        return $response->withJson(Status::updated());
    }

    /**
     * Caster.fm: configuration keys only a global administrator may set.
     *
     * This mirrors the form: every field wrapped in `v-if="isAdministrator"` in
     * frontend/components/Admin/Stations/Form/{Frontend,Backend,Streamers}Form.vue is listed here,
     * plus the keys that have no form field and are managed through admin-only pages.
     * A key that is not listed is accepted, the same way a field that is not hidden is shown.
     *
     * When hiding a field in one of those forms, add its key here. When upstream adds a
     * configuration key, decide whether station owners may set it (see the update steps in the
     * workspace CLAUDE.md).
     */
    private const array ADMIN_ONLY_CONFIG = [
        'frontend_config' => [
            // FrontendForm.vue
            'port',
            'max_listeners',
            'custom_config',
        ],
        'backend_config' => [
            // BackendForm.vue: "Audio Processing"
            'enable_auto_cue',
            'enable_replaygain_metadata',
            'audio_processing_method',
            'post_processing_include_live',
            'master_me_preset',
            'master_me_loudness_target',
            'stereo_tool_license_key',
            // BackendForm.vue: "Advanced Configuration" (the fields marked isAdministrator; the
            // sequential queue reset, character set and artist duplicate prevention are open)
            'share_encoders',
            'use_manual_autodj',
            'write_playlists_to_liquidsoap',
            'telnet_port',
            'autodj_queue_length',
            'performance_mode',
            'duplicate_prevention_time_range',
            // StreamersForm.vue
            'dj_port',
            'dj_mount_point',
            // No form field: set through the admin-only Liquidsoap and Stereo Tool pages.
            'custom_config_top',
            'custom_config_pre_playlists',
            'custom_config_pre_fade',
            'custom_config_pre_live',
            'custom_config',
            'custom_config_bottom',
            'stereo_tool_configuration_path',
        ],
    ];

    /**
     * @param array<array-key, mixed> $requestBody
     * @return array<array-key, mixed>
     */
    private static function withoutAdminOnlyConfig(array $requestBody): array
    {
        foreach (self::ADMIN_ONLY_CONFIG as $section => $adminOnlyKeys) {
            if (!array_key_exists($section, $requestBody)) {
                continue;
            }

            if (!is_array($requestBody[$section])) {
                // Not a key/value list: leave the stored section alone.
                unset($requestBody[$section]);
                continue;
            }

            // Keys are trimmed when the configuration is merged, so compare them trimmed.
            $requestBody[$section] = array_filter(
                $requestBody[$section],
                static fn($key) => !in_array(trim((string)$key), $adminOnlyKeys, true),
                ARRAY_FILTER_USE_KEY
            );
        }

        return $requestBody;
    }

    private function getContext(ServerRequest $request): array
    {
        $context = [
            AbstractNormalizer::GROUPS => [
                EntityGroupsInterface::GROUP_ID,
                EntityGroupsInterface::GROUP_GENERAL,
            ],
        ];

        $acl = $request->getAcl();
        $station = $request->getStation();

        if (
            $acl->isAllowed(
                StationPermissions::Broadcasting,
                $station
            )
        ) {
            $context[AbstractNormalizer::GROUPS][] = EntityGroupsInterface::GROUP_BROADCAST_MANAGER;
        }

        if ($acl->isAllowed(GlobalPermissions::Stations)) {
            $context[AbstractNormalizer::GROUPS][] = EntityGroupsInterface::GROUP_ALL;
        }

        return $context;
    }
}
