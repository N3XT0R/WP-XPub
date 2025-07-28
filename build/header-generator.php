<?php

declare(strict_types=1);

$file = __DIR__.'/../xpub.php';

$composer = json_decode(file_get_contents(__DIR__.'/../composer.json'), true);

// Git-Version holen
exec('git describe --tags --abbrev=0 2>/dev/null', $out, $exit);
$version = $exit === 0 ? trim($out[0]) : '0.1.0';

// Basisinfos aus composer.json
$name = $composer['name'] ?? 'wp-xpub';
$description = $composer['description'] ?? 'Flexible Multi-Channel Auto Publisher for WordPress';
$authorName = $composer['authors'][0]['name'] ?? 'Unknown';
$license = strtoupper($composer['license'] ?? 'GPLv3');

// WordPress-Plugin-Header erzeugen
$header = <<<PHP
/**
 * Plugin Name: WP-XPub
 * Description: {$description}
 * Version: {$version}
 * Author: {$authorName}
 * License: {$license}
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Domain Path: /languages
 * Text Domain: xpub
 * Requires at least: 6.0
 * Tested up to: 6.8.2
 * Requires PHP: 8.2
 * Stable tag: {$version}
 */

PHP;

// Datei einlesen
$code = file_get_contents($file);
if ($code === false) {
    fwrite(STDERR, "Could not read $file\n");
    exit(1);
}

// Suche nach bestehendem Header (zwischen <?php und erster Leerzeile oder Code)
$pattern = '/(<\?php\s*)(?:\/\*\*.*?\*\/\s*)?/s';
$replacement = '$1'.$header."\n\n";

// Ersetze oder füge ein
$newCode = preg_replace($pattern, $replacement, $code, 1);

if ($newCode === null) {
    fwrite(STDERR, "Regex error while updating header.\n");
    exit(1);
}

file_put_contents($file, $newCode);
echo "Plugin header injected (version: $version)\n";
