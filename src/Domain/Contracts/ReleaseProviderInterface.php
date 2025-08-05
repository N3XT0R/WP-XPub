<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Domain\Contracts;

interface ReleaseProviderInterface
{
    /**
     * Returns the latest release information from an upstream provider.
     *
     * @return array{
     *     version: string,
     *     download_url: string,
     *     changelog?: string
     * }|null
     */
    public function fetchLatestRelease(): ?array;
}
