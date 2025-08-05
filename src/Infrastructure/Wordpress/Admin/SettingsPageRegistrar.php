<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Admin;

use N3XT0R\XPub\Domain\Service\Admin\PublisherSettingsServiceInterface;
use N3XT0R\XPub\Infrastructure\Wordpress\Hook\HookRegistrableInterface;
use N3XT0R\XPub\Infrastructure\Wordpress\React\Components\XPubSettingsAppLoader;
use N3XT0R\XPub\Infrastructure\Wordpress\View\View;

final class SettingsPageRegistrar implements HookRegistrableInterface
{
    public function __construct(
        private PublisherSettingsServiceInterface $service,
        private XPubSettingsAppLoader $appLoader,
    ) {
    }

    public function register(): void
    {
        add_action('admin_menu', [$this, 'addOptionsPage']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
    }

    public function addOptionsPage(): void
    {
        add_options_page(
            __('XPUB Einstellungen', 'xpub-multi-channel-publisher'),
            'XPUB',
            'manage_options',
            'xpub-settings',
            [$this, 'renderSettingsPage']
        );
    }

    public function renderSettingsPage(): void
    {
        View::render('layouts.admin', [
            'title' => __('XPUB Einstellungen', 'xpub-multi-channel-publisher'),
            'content' => fn() => View::render('admin.react-settings-page', $this->service->getSettingsViewData()),
        ]);
    }

    public function enqueueAssets(string $hook): void
    {
        if ($hook !== 'settings_page_xpub-settings') {
            return;
        }

        $this->appLoader->register();
    }
}
