<?php

declare(strict_types=1);

namespace App\Notification\Check;

use App\Container\EnvironmentAwareTrait;
use App\Entity\Api\Notification;
use App\Enums\FlashLevels;
use App\Enums\GlobalPermissions;
use App\Event\GetNotifications;

final class ProfilerAdvisorCheck
{
    use EnvironmentAwareTrait;

    public function __invoke(GetNotifications $event): void
    {
        // This notification is for full administrators only.
        $acl = $event->getRequest()->getAcl();
        if (!$acl->isAllowed(GlobalPermissions::All)) {
            return;
        }

        if (!$this->environment->isDocker() || !$this->environment->isDevelopment()) {
            return;
        }

        if (!$this->environment->isProfilingExtensionEnabled()) {
            return;
        }

        $event->addNotification(
            new Notification(
                __('The performance profiling extension is currently enabled on this installation.'),
                __(
                    'You can track the execution time and memory usage of any AzuraCast page or application ' .
                    'from the profiler page.',
                ),
                FlashLevels::Info,
                __('Profiler Control Panel'),
                '/?' . http_build_query(
                    [
                        'SPX_UI_URI' => '/',
                        'SPX_KEY' => $this->environment->getProfilingExtensionHttpKey(),
                    ]
                ),
            )
        );

        if ($this->environment->isProfilingExtensionAlwaysOn()) {
            $event->addNotification(
                new Notification(
                    __('Performance profiling is currently enabled for all requests.'),
                    __(
                        'This can have an adverse impact on system performance. ' .
                        'You should disable this when possible.'
                    ),
                    FlashLevels::Warning
                )
            );
        }
    }
}
