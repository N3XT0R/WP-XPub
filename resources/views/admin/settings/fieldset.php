<?php
/**
 * @var array $publisher
 * @var \N3XT0R\XPub\Infrastructure\Wordpress\I18n\Translator $translator
 */

?>
<fieldset style="margin-top: 2rem; padding: 1rem; border: 1px solid #ccc;">
    <legend><strong><?= esc_html($publisher['name']) ?> <?= $translator->translateEscaped(
                'Configuration'
            ) ?></strong></legend>

    <?php
    foreach ($publisher['config'] as $purposeType => $group): ?>
        <h4 style="margin-top: 1.5rem;"><?= esc_html(
                ucfirst($purposeType)
            ) ?> <?= $translator->translateEscaped('Settings') ?></h4>
        <?php
        foreach ($group as $key => $value): ?>
            <?php
            $inputId = 'config_'.esc_attr($publisher['slug'].'_'.$purposeType.'_'.$key); ?>
            <div style="margin-bottom: 1rem;">
                <label for="<?= $inputId ?>"
                       style="display: block; font-weight: bold; margin-bottom: .3rem;">
                    <?= $translator->translateEscaped($key) ?>:
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
    <?php
    endforeach; ?>
</fieldset>