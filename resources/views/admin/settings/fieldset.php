<?php
/**
 * @var array $publisher
 * @var \N3XT0R\XPub\Infrastructure\Wordpress\I18n\Translator $translator
 */

use N3XT0R\XPub\Infrastructure\Wordpress\View\View;

?>
<fieldset style="margin-top: 2rem; padding: 1rem; border: 1px solid #ccc;">
    <legend><strong><?= esc_html($publisher['name']) ?> <?= $translator->translateEscaped(
                'Configuration'
            ) ?></strong></legend>

    <?php
    foreach ($publisher['config'] as $purposeType => $group):
        View::render(
            'admin.settings.purpose-type.'.$purposeType,
            [
                'group' => $group,
                'publisher' => $publisher,
                'purposeType' => $purposeType,
            ]
        );
    endforeach; ?>
</fieldset>