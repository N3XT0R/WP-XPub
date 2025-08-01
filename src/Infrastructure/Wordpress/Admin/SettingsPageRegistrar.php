<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Admin;

use N3XT0R\XPub\Application\Service\Admin\PublisherSettingsService;
use N3XT0R\XPub\Infrastructure\Wordpress\Hook\HookRegistrableInterface;
use N3XT0R\XPub\Infrastructure\Wordpress\View\View;

final class SettingsPageRegistrar implements HookRegistrableInterface
{
    public function __construct(private PublisherSettingsService $service)
    {
    }

    public function register(): void
    {
        add_action('admin_menu', [$this, 'addOptionsPage']);
    }

    public function addOptionsPage(): void
    {
        add_options_page(
            'XPUB Einstellungen',
            'XPUB',
            'manage_options',
            'xpub-settings',
            [$this, 'renderSettingsPage']
        );
    }

    public function renderSettingsPage(): void
    {
        View::render('layouts.admin', [
            'title' => 'XPUB Einstellungen',
            'content' => fn() => View::render('admin.settings-page', $this->service->getSettingsViewData()),
        ]);
    }

}

