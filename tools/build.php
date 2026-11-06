<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$out = rtrim(str_replace('\\', '/', $argv[1] ?? $root . '/dist'), '/');

if (is_dir($out) && count(scandir($out) ?: []) > 2) {
    fwrite(STDERR, "The output folder must be empty or missing: {$out}\n");
    exit(1);
}

require $root . '/includes/functions.php';

function put_file(string $file, string $contents): void
{
    if (!is_dir(dirname($file))) {
        mkdir(dirname($file), 0775, true);
    }
    file_put_contents($file, $contents);
}

function copy_file(string $from, string $to): void
{
    if (!is_dir(dirname($to))) {
        mkdir(dirname($to), 0775, true);
    }
    copy($from, $to);
}

$files = [];
foreach (array_keys(routes()) as $slug) {
    $files[($slug === '' ? 'index' : $slug) . '.html'] = render_page((string) $slug);
}
$files['404.html'] = render_page('404');
$files['robots.txt'] = robots_txt();
$files['sitemap.xml'] = sitemap_xml();

foreach ($files as $name => $contents) {
    put_file($out . '/' . $name, $contents);
}

$assets = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/assets', FilesystemIterator::SKIP_DOTS));
foreach ($assets as $file) {
    $relative = str_replace('\\', '/', substr($file->getPathname(), strlen($root) + 1));
    copy_file($file->getPathname(), $out . '/' . $relative);
    $files[$relative] = true;
}

foreach (['favicon.ico', 'apple-touch-icon.png', 'site.webmanifest'] as $name) {
    copy_file($root . '/' . $name, $out . '/' . $name);
    $files[$name] = true;
}

fwrite(STDOUT, 'Built ' . count($files) . ' files in ' . $out . ' with base path "' . (base_path() === '' ? '/' : base_path()) . "\"\n");
