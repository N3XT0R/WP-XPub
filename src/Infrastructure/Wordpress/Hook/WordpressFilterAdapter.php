<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Hook;

class WordpressFilterAdapter
{
    public function apply(string $hook, $value)
    {
        return apply_filters($hook, $value);
    }
}