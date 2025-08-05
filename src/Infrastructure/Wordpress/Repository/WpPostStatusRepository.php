<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Repository;

use N3XT0R\XPub\Domain\Repository\PostStatusRepositoryInterface;
use WP_Post;

final class WpPostStatusRepository implements PostStatusRepositoryInterface
{
    public function isPublishedAndNotOutdated(int $postId): bool
    {
        $post = get_post($postId);
        if (!$post instanceof WP_Post || $post->post_status !== 'publish') {
            return false;
        }

        $revisions = wp_get_post_revisions($postId);
        foreach ($revisions as $rev) {
            if ($rev->post_modified_gmt > $post->post_modified_gmt &&
                $rev->post_status !== 'publish'
            ) {
                return false;
            }
        }

        return true;
    }
}
