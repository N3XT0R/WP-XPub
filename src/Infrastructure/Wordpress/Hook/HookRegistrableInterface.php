<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Hook;

interface HookRegistrableInterface
{
    public function register(): void;
}