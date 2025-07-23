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
    View::slot($content) ?>
</div>
</body>
</html>
