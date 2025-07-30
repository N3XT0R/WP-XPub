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
        $result = [];

        foreach ($config as $key => $value) {
            $result[$key] = [
                'value' => $value,
                'purpose_type' => PurposeType::DEFAULT,
            ];
        }

        return $result;
    }
}
