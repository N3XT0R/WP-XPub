<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\I18n;

use N3XT0R\XPub\Domain\Contracts\TranslatesMessagesInterface;

class Translator implements TranslatesMessagesInterface
{

    public function translate(string $message): string
    {
        return __($message, 'xpub-multi-channel-publisher');
    }

    public function translateWithContext(string $message, string $context): string
    {
        return _x($message, $context, 'xpub-multi-channel-publisher');
    }

    public function translatePlural(string $singular, string $plural, int $count): string
    {
        return _n($singular, $plural, $count, 'xpub-multi-channel-publisher');
    }

    public function translateFormatted(string $message, array $params = []): string
    {
        return vsprintf(__($message, 'xpub-multi-channel-publisher'), $params);
    }

    public function translateEscaped(string $message): string
    {
        return esc_html(__($message, 'xpub-multi-channel-publisher'));
    }
}
