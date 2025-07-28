<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Hook;

use N3XT0R\XPub\Domain\Hook\HookDispatcherInterface;

final class WordpressHookDispatcher implements HookDispatcherInterface
{
    public function dispatch(HookDefinition $hook): void
    {
        add_action($hook->hookName, $hook->callback, $hook->priority, $hook->acceptedArgs);
    }
}
