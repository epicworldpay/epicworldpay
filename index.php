<?php

declare(strict_types=1);

if (PHP_SAPI === 'cli-server') {
    $root = str_replace('\\', '/', __DIR__);
    $file = realpath(__DIR__ . (string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
    if ($file !== false && is_file($file)) {
        $relative = substr(str_replace('\\', '/', $file), strlen($root));
        if (str_starts_with($relative, '/assets/') || in_array($relative, ['/favicon.ico', '/apple-touch-icon.png', '/site.webmanifest'], true)) {
            return false;
        }
    }
}

require __DIR__ . '/includes/functions.php';

$path = rawurldecode((string) (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/'));
if (base_path() !== '' && str_starts_with($path, base_path())) {
    $path = substr($path, strlen(base_path()));
}
$slug = trim($path, '/');
if ($slug === 'index.php') {
    $slug = '';
}

if ($slug === 'robots.txt') {
    header('Content-Type: text/plain; charset=utf-8');
    echo robots_txt();
    exit;
}

if ($slug === 'sitemap.xml') {
    header('Content-Type: application/xml; charset=utf-8');
    echo sitemap_xml();
    exit;
}

if ($slug !== '' && $path !== '/' . $slug && isset(routes()[$slug])) {
    redirect(url($slug), 301);
}

if (!isset(routes()[$slug])) {
    http_response_code(404);
    $slug = '404';
}

send_security_headers();
header('Content-Type: text/html; charset=utf-8');
echo render_page($slug);
