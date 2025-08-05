<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Domain\Hook;

interface FilterDispatcherInterface
{
    /**
     * Dispatches a filter call (like apply_filters).
     *
     * @param  string  $filterName
     * @param  mixed  $value
     * @return mixed
     */
    public function filter(string $filterName, mixed $value): mixed;
}
