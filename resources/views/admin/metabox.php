<?php

/** @var string|null $value */

/** @var \N3XT0R\XPub\Infrastructure\Wordpress\I18n\Translator $translator */
wp_nonce_field('xpub_meta_box', 'xpub_meta_box_nonce');
?>
<label for="xpub_custom_excerpt">
    <?= $translator->translateEscaped('Custom Text for Social Sharing') ?>
</label>

<textarea id="xpub_custom_excerpt"
          name="xpub_custom_excerpt"
          rows="3"
          style="width:100%;">
    <?= esc_textarea($value ?? '') ?>
</textarea>
