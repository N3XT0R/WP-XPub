<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Support;

class Version
{
    public static function get(): string
    {
        return defined('XPUB_VERSION') ? XPUB_VERSION : '0.0.0';
    }
}