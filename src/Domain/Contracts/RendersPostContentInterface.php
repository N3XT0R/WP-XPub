<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Domain\Contracts;

interface RendersPostContentInterface
{
    public function render(\WP_Post $post): string;
}