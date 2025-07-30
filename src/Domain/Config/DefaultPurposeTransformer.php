<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Domain\Config;

final class DefaultPurposeTransformer implements ConfigTransformerInterface
{
    public function supports(array $config): bool
    {
        return !array_key_exists('grant_type', $config);
    }


    public function transform(array $config): array
    {
        return array_map(
            fn($v) => ['value' => $v, 'purpose_type' => PurposeType::DEFAULT],
            $config
        );
    }
}
