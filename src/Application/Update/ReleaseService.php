<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Application\Update;

use N3XT0R\XPub\Domain\Contracts\ReleaseProviderInterface;

final class ReleaseService implements ReleaseProviderInterface
{
    private const API_URL = 'https://api.github.com/repos/N3XT0R/WP-XPub/releases/latest';

    public function fetchLatestRelease(): ?array
    {
        $opts = [
            'http' => [
                'method' => 'GET',
                'header' => [
                    'User-Agent: WP-XPub',
                    'Accept: application/vnd.github.v3+json',
                ],
                'timeout' => 5,
            ],
        ];

        $context = stream_context_create($opts);
        $json = @file_get_contents(self::API_URL, false, $context);

        if ($json === false) {
            return null;
        }

        $data = json_decode($json, true);

        if (!is_array($data) || empty($data['tag_name'])) {
            return null;
        }

        return [
            'version' => ltrim($data['tag_name'], 'v'),
            'changelog' => $data['body'] ?? '',
            'download_url' => $data['assets'][0]['browser_download_url'] ?? '',
        ];
    }
}
