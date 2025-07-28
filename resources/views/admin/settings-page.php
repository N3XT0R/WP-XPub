<?php

/** @var string|null $value */

/** @var \N3XT0R\XPub\Infrastructure\Wordpress\I18n\Translator $translator */

?>
<form method="post" action="<?php
echo esc_url(admin_url('admin-post.php')); ?>">
    <input type="hidden" name="action" value="xpub_save_settings">
    <?php
    wp_nonce_field('xpub_save_settings'); ?>

    <h2><?php
        echo $translator->translateEscaped('Activate Publisher'); ?></h2>

    <fieldset>
        <legend><?php
            echo $translator->translateEscaped('Select active publishers:'); ?></legend>
        <?php
        foreach ($publishers as $publisher): ?>
            <?php
            $id = 'publisher_'.esc_attr($publisher['slug']); ?>
            <label for="<?php
            echo $id; ?>" style="display: block; margin-bottom: .5rem;">
                <input
                        type="checkbox"
                        id="<?php
                        echo $id; ?>"
                        name="active_publishers[]"
                        value="<?php
                        echo esc_attr($publisher['slug']); ?>"
                    <?php
                    echo in_array($publisher['slug'], $activePublisherSlugs, true) ? 'checked' : ''; ?>
                >
                <?php
                echo esc_html($publisher['name']); ?>
            </label>
        <?php
        endforeach; ?>
    </fieldset>

    <h2><?php
        echo $translator->translateEscaped('Configuration'); ?></h2>
    <?php
    foreach ($publishers as $publisher): ?>
        <?php
        if (in_array($publisher['slug'], $activePublisherSlugs, true)): ?>
            <fieldset style="margin-top: 2rem; padding: 1rem; border: 1px solid #ccc;">
                <legend>
                    <strong>
                        <?php
                        echo esc_html($publisher['name']); ?>
                        <?php
                        echo $translator->translateEscaped('Configuration'); ?>
                    </strong>
                </legend>

                <?php
                foreach ($publisher['config'] as $key => $value): ?>
                    <?php
                    $inputId = 'config_'.esc_attr($publisher['slug'].'_'.$key); ?>
                    <div style="margin-bottom: 1rem;">
                        <label for="<?php
                        echo $inputId; ?>" style="display: block; font-weight: bold; margin-bottom: .3rem;">
                            <?php
                            echo $translator->translateEscaped($key); ?>:
                        </label>
                        <input
                                type="text"
                                id="<?php
                                echo $inputId; ?>"
                                name="config[<?php
                                echo esc_attr($publisher['slug']); ?>][<?php
                                echo esc_attr($key); ?>]"
                                value="<?php
                                echo esc_attr($value); ?>"
                                style="width: 100%; max-width: 400px;"
                        >
                    </div>
                <?php
                endforeach; ?>
            </fieldset>
        <?php
        endif; ?>
    <?php
    endforeach; ?>

    <p style="margin-top: 2rem;">
        <button type="submit" class="button button-primary">
            <?php
            echo $translator->translateEscaped('Save settings'); ?>
        </button>
    </p>
</form>
