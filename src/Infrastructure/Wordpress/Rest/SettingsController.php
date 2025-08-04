<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Rest;

use N3XT0R\XPub\Application\Service\Admin\PublisherSettingsService;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

class SettingsController
{
    public function __construct(private PublisherSettingsService $service)
    {
    }

    public function register(): void
    {
        register_rest_route('xpub/v1', '/settings', [
            'methods' => 'GET',
            'callback' => [$this, 'getSettings'],
            'permission_callback' => fn() => current_user_can('manage_options'),
        ]);

        register_rest_route('xpub/v1', '/settings', [
            'methods' => 'POST',
            'callback' => [$this, 'saveSettings'],
            'permission_callback' => fn() => current_user_can('manage_options'),
        ]);
    }

    public function getSettings(WP_REST_Request $request): WP_REST_Response
    {
        $data = $this->service->getSettingsViewData();
        $data['nonce'] = wp_create_nonce('xpub_save_settings');
        $data['actionUrl'] = admin_url('admin-post.php');
        $data['restUrl'] = rest_url();

        return new WP_REST_Response($data);
    }

    public function saveSettings(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        $params = $request->get_json_params();
        $active = isset($params['active_publishers']) && is_array($params['active_publishers'])
            ? array_map('sanitize_text_field', $params['active_publishers'])
            : [];
        $configs = isset($params['config']) && is_array($params['config']) ? $params['config'] : [];

        try {
            $this->service->saveSettings($active, $configs);
        } catch (\Throwable $e) {
            return new WP_Error('xpub_settings_save_failed', $e->getMessage(), ['status' => 500]);
        }

        return new WP_REST_Response(['success' => true]);
    }
}
