<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Admin;

use N3XT0R\XPub\Infrastructure\Wordpress\Hook\HookRegistrableInterface;
use N3XT0R\XPub\Infrastructure\Wordpress\View\View;

class MetaBox implements HookRegistrableInterface
{
    public function register(): void
    {
        add_action('add_meta_boxes', [$this, 'add']);
        add_action('save_post', [$this, 'save']);
    }

    public static function add(): void
    {
        add_meta_box(
            'xpub_custom_excerpt',
            __('XPUB: Custom Share Text', 'xpub'),
            [self::class, 'render'],
            'post',
            'normal',
            'default'
        );
    }

    public static function render(\WP_Post $post): void
    {
        $value = get_post_meta($post->ID, '_xpub_custom_excerpt', true);
        View::render('admin.metabox', ['value' => $value]);
    }

    public static function save(int $postId): void
    {
        if (!isset($_POST['xpub_meta_box_nonce']) || !wp_verify_nonce($_POST['xpub_meta_box_nonce'], 'xpub_meta_box')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $postId)) {
            return;
        }

        $value = sanitize_textarea_field($_POST['xpub_custom_excerpt'] ?? '');
        update_post_meta($postId, '_xpub_custom_excerpt', $value);
    }
}