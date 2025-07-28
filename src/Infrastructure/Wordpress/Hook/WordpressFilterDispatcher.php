<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Hook;

use N3XT0R\XPub\Domain\Hooks\FilterDispatcherInterface;

class WordpressFilterDispatcher implements FilterDispatcherInterface
{
    public function filter(string $filterName, mixed $value): mixed
    {
        return apply_filters($filterName, $value);
    }
}