<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\I18n;

use N3XT0R\XPub\Domain\Contracts\TranslatesMessagesInterface;

class Translator implements TranslatesMessagesInterface
{
    private const TEXT_DOMAIN = 'xpub';

    public static function register(string $pluginFile): void
    {
        load_plugin_textdomain(
            self::TEXT_DOMAIN,
            false,
            dirname(plugin_basename($pluginFile)).'/languages'
        );
    }

    public function translate(string $message): string
    {
        return __($message, self::TEXT_DOMAIN);
    }

    public function translateWithContext(string $message, string $context): string
    {
        return _x($message, $context, self::TEXT_DOMAIN);
    }

    public function translatePlural(string $singular, string $plural, int $count): string
    {
        return _n($singular, $plural, $count, self::TEXT_DOMAIN);
    }

    public function translateFormatted(string $message, array $params = []): string
    {
        return vsprintf(__($message, self::TEXT_DOMAIN), $params);
    }

    public function translateEscaped(string $message): string
    {
        return esc_html(__($message, self::TEXT_DOMAIN));
    }
}
