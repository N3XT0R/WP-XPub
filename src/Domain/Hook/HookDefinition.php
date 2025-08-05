<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Domain\Hook;

final readonly class HookDefinition
{
    /**
     * @var callable
     */
    public readonly mixed $callback;

    public function __construct(
        public string $hookName,
        callable $callback,
        public int $priority = 10,
        public int $acceptedArgs = 1,
    ) {
        $this->callback = $callback;
    }
}
