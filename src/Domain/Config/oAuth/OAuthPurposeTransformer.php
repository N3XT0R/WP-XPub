<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Domain\Config\oAuth;

use N3XT0R\XPub\Domain\Config\ConfigTransformerInterface;
use N3XT0R\XPub\Domain\Config\PurposeType;

class OAuthPurposeTransformer implements ConfigTransformerInterface
{
    public function supports(array $config): bool
    {
        return isset($config['grant_type']);
    }

    public function transform(array $config): array
    {
        return array_map(
            fn($v) => ['value' => $v, 'purpose_type' => PurposeType::OAUTH],
            $config
        );
    }
}