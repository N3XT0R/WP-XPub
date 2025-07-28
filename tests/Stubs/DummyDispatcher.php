<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Tests\Stubs;

use N3XT0R\XPub\Domain\Hook\HookDispatcherInterface;
use N3XT0R\XPub\Infrastructure\Wordpress\Hook\HookDefinition;

final class DummyDispatcher implements HookDispatcherInterface
{
    public array $dispatched = [];

    public function dispatch(HookDefinition $hook): void
    {
        $this->dispatched[] = $hook;
    }
}
