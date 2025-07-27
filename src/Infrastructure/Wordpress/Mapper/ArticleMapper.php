<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Mapper;

use N3XT0R\XPub\Domain\Entity\Article;

final class ArticleMapper
{
    public static function fromPost(\WP_Post $post): Article
    {
        return new Article($post->ID, $post->post_title, $post->post_content);
    }
}
