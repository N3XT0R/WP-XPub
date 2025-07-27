<?php

/** @var string|null $value */

/** @var \N3XT0R\XPub\Infrastructure\Wordpress\I18n\Translator $translator */
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
