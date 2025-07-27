<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'XPUB Settings' ?></title>
</head>
<body>
<div class="wrap">
    <h1><?= $title ?? 'XPUB Settings' ?></h1>
    <?php
    \N3XT0R\XPub\Support\View::slot($content) ?>
</div>
</body>
</html>
