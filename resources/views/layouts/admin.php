<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>
        <?php
        echo $title ?? 'XPUB Settings';
        ?>
    </title>
</head>
<body>
<div class="wrap">
    <h1
    <?php
    echo $title ?? 'XPUB Settings';
    \N3XT0R\XPub\Infrastructure\Wordpress\View\View::slot($content)
    ?>
</div>
</body>
</html>
