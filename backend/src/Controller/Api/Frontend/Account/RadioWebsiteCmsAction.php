<?php

declare(strict_types=1);

namespace App\Controller\Api\Frontend\Account;

use App\Controller\SingleActionInterface;
use App\Http\Response;
use App\Http\ServerRequest;
use App\Service\GuzzleFactory;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

final class RadioWebsiteCmsAction implements SingleActionInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly GuzzleFactory $guzzleFactory,
    ) {
    }

    public function __invoke(
        ServerRequest $request,
        Response $response,
        array $params
    ): ResponseInterface {
        $user = $request->getUser();

        // Find the admin-generated API key for this user.
        $apiKey = $this->em->createQuery(
            <<<'DQL'
            SELECT e FROM App\Entity\ApiKey e
            WHERE e.user = :user
            AND e.comment LIKE :adminGenerated
            DQL
        )
            ->setParameter('user', $user)
            ->setParameter('adminGenerated', '%Admin-generated%')
            ->setMaxResults(1)
            ->getOneOrNullResult();

        if ($apiKey === null) {
            return $response->withStatus(403)->withJson([
                'success' => false,
                'message' => 'No admin-generated API key found for your account.',
            ]);
        }

        try {
            $client = $this->guzzleFactory->buildClient([
                'timeout' => 15,
            ]);

            $ssoResponse = $client->post('https://serviceapi.radio.io/streamingSSO', [
                'form_params' => [
                    'token' => $apiKey->id,
                ],
            ]);

            if ($ssoResponse->getStatusCode() !== 200) {
                return $response->withStatus(502)->withJson([
                    'success' => false,
                    'message' => 'The CMS service returned an error. Please try again later.',
                ]);
            }

            $body = json_decode((string)$ssoResponse->getBody(), true, 512, JSON_THROW_ON_ERROR);

            if (empty($body['redirectTo'])) {
                return $response->withStatus(502)->withJson([
                    'success' => false,
                    'message' => 'Invalid response from the CMS service.',
                ]);
            }

            return $response->withJson([
                'success' => true,
                'redirectTo' => $body['redirectTo'],
            ]);
        } catch (GuzzleException $e) {
            return $response->withStatus(502)->withJson([
                'success' => false,
                'message' => 'Unable to connect to the CMS service: ' . $e->getMessage(),
            ]);
        } catch (\JsonException) {
            return $response->withStatus(502)->withJson([
                'success' => false,
                'message' => 'Invalid response from the CMS service.',
            ]);
        }
    }
}
