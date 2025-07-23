<form method="post" action="<?= esc_url(admin_url('admin-post.php')) ?>">
    <input type="hidden" name="action" value="xpub_save_settings">
    <?php
    wp_nonce_field('xpub_save_settings'); ?>

    <h2>Publisher aktivieren</h2>
    <fieldset>
        <legend>Wähle aktive Publisher:</legend>
        <?php
        foreach ($publishers as $publisher): ?>
            <?php
            $id = 'publisher_'.esc_attr($publisher['slug']); ?>
            <label for="<?= $id ?>" style="display: block; margin-bottom: .5rem;">
                <input
                        type="checkbox"
                        id="<?= $id ?>"
                        name="active_publishers[]"
                        value="<?= esc_attr($publisher['slug']) ?>"
                    <?= in_array($publisher['slug'], $activePublisherSlugs, true) ? 'checked' : '' ?>
                >
                <?= esc_html($publisher['name']) ?>
            </label>
        <?php
        endforeach; ?>
    </fieldset>

    <h2>Konfiguration</h2>
    <?php
    foreach ($publishers as $publisher): ?>
        <?php
        if (in_array($publisher['slug'], $activePublisherSlugs, true)): ?>
            <fieldset style="margin-top: 2rem; padding: 1rem; border: 1px solid #ccc;">
                <legend><strong><?= esc_html($publisher['name']) ?> Konfiguration</strong></legend>
                <?php
                foreach ($publisher['config'] as $key => $value): ?>
                    <?php
                    $inputId = 'config_'.esc_attr($publisher['slug'].'_'.$key); ?>
                    <div style="margin-bottom: 1rem;">
                        <label for="<?= $inputId ?>" style="display: block; font-weight: bold; margin-bottom: .3rem;">
                            <?= esc_html($key) ?>:
                        </label>
                        <input
                                type="text"
                                id="<?= $inputId ?>"
                                name="config[<?= esc_attr($publisher['slug']) ?>][<?= esc_attr($key) ?>]"
                                value="<?= esc_attr($value) ?>"
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
        <button type="submit" class="button button-primary">Einstellungen speichern</button>
    </p>
</form>
