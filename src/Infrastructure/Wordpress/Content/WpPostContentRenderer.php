<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Content;

use N3XT0R\XPub\Domain\Contracts\RendersPostContentInterface;
use WP_Post;

final class WpPostContentRenderer implements RendersPostContentInterface
{
    public function render(?WP_Post $post): string
    {
        if (empty($post->post_content)) {
            return '';
        }
        
        setup_postdata($post);
        $content = apply_filters('the_content', $post->post_content);
        wp_reset_postdata();
        return wp_kses_post($content);
    }
}