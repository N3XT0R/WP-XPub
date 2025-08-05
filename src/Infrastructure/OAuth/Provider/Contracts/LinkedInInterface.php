<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\OAuth\Provider\Contracts;

interface LinkedInInterface
{
    public function getAuthorUrn(): ?string;
}
