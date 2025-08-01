<?php
/**
 * @var array $group
 * @var array $publisher
 * @var \N3XT0R\XPub\Infrastructure\Wordpress\I18n\Translator $translator
 * @var string $purposeType
 */

?>

<h4 style="margin-top: 1.5rem;">OAuth <?= $translator->translateEscaped('Settings') ?></h4>

<?php
foreach ($group as $key => $value): ?>
    <?php
    $inputId = 'config_'.esc_attr($publisher['slug'].'_'.$purposeType.'_'.$key);
    $inputType = match ($key) {
        'clientSecret' => 'password',
        'redirectUri', 'urlAccessToken', 'urlAuthorize', 'urlResourceOwnerDetails' => 'url',
        default => 'text',
    };
    ?>
    <div style="margin-bottom: 1rem;">
        <label for="<?= $inputId ?>"
               style="display: block; font-weight: bold; margin-bottom: .3rem;">
            <?= $translator->translateEscaped($key) ?>:
        </label>
        <input
                type="<?= $inputType ?>"
                id="<?= $inputId ?>"
                name="config[<?= esc_attr($publisher['slug']) ?>][<?= esc_attr($key) ?>]"
                value="<?= esc_attr($value) ?>"
                style="width: 100%; max-width: 400px;"
            <?= $key === 'clientSecret' ? 'autocomplete="off"' : '' ?>
        >
    </div>
<?php
endforeach;


if (!empty($group['clientId'])): ?>
    <div style="margin-top: 1rem;">
        <button type="button"
                class="button button-secondary"
                onclick="xpubStartOAuthPopup('<?= esc_js($publisher['slug']) ?>')">
            <?= $translator->translateEscaped('Authenticate with').' '.$publisher['name'] ?>
        </button>
    </div>
<?php
endif; ?>
