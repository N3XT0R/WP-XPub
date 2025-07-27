<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Domain\Contracts;

interface SlugAwareInterface
{
    public function setSlug(string $slug): void;
}