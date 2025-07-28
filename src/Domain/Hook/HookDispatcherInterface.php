<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Domain\Hook;

use N3XT0R\XPub\Infrastructure\Wordpress\Hook\HookDefinition;

interface HookDispatcherInterface
{
    public function dispatch(HookDefinition $hook): void;
}
