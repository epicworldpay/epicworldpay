<?php

declare(strict_types=1);

const BOOT_SCRIPT = "document.documentElement.classList.add('js')";

function cfg(string $path, mixed $default = null): mixed
{
    static $config = null;
    $config ??= require dirname(__DIR__) . '/config.php';
    $value = $config;
    foreach (explode('.', $path) as $key) {
        if (!is_array($value) || !array_key_exists($key, $value)) {
            return $default;
        }
        $value = $value[$key];
    }
    return $value;
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function is_https(): bool
{
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
}

function base_path(): string
{
    static $base = null;
    $base ??= match (PHP_SAPI) {
        'cli' => rtrim((string) getenv('BASE_PATH'), '/'),
        'cli-server' => '',
        default => rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/'),
    };
    return $base;
}

function url(string $path = ''): string
{
    return base_path() . '/' . ltrim($path, '/');
}

function site_url(string $path = ''): string
{
    $root = rtrim((string) cfg('site_url', ''), '/');
    if ($root === '') {
        $host = preg_replace('/[^a-z0-9.:\-\[\]]/i', '', $_SERVER['HTTP_HOST'] ?? 'localhost');
        $root = (is_https() ? 'https' : 'http') . '://' . $host . base_path();
    }
    return $root . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    static $versions = [];
    $file = dirname(__DIR__) . '/assets/' . $path;
    $versions[$path] ??= is_file($file) ? substr((string) md5_file($file), 0, 10) : '1';
    return url('assets/' . $path) . '?v=' . $versions[$path];
}

function brand(): string
{
    return e(cfg('brand', 'EpicWorldPay'));
}

function todo(string $label): string
{
    return '<mark class="todo" title="Missing in config.php">' . e($label) . '</mark>';
}

function company(string $key): string
{
    $labels = [
        'legal_name' => 'Legal name',
        'address' => 'Registered address',
        'license' => 'License number',
        'authority' => 'Licensing authority',
        'trn' => 'TRN',
        'email' => 'Support email',
        'phone' => 'Phone',
    ];
    $value = trim((string) cfg('company.' . $key, ''));
    if ($value === '') {
        return todo($labels[$key] ?? $key);
    }
    if ($key === 'email') {
        return '<a href="mailto:' . e($value) . '">' . e($value) . '</a>';
    }
    if ($key === 'phone') {
        return '<a href="tel:' . e(preg_replace('/[^0-9+]/', '', $value)) . '">' . e($value) . '</a>';
    }
    return e($value);
}

function company_raw(string $key): string
{
    return trim((string) cfg('company.' . $key, ''));
}

function icon(string $name): string
{
    static $icons = [
        'arrow-right' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
        'check' => '<path d="M5 12.5l4.5 4.5L19 7.5"/>',
        'shield' => '<path d="M12 3l7 3v5.5c0 4.5-3 8-7 9.5-4-1.5-7-5-7-9.5V6l7-3z"/><path d="M9 12l2 2 4-4"/>',
        'lock' => '<rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/>',
        'refresh' => '<path d="M20 11a8 8 0 0 0-14.5-4.5L4 8"/><path d="M4 4v4h4"/><path d="M4 13a8 8 0 0 0 14.5 4.5L20 16"/><path d="M20 20v-4h-4"/>',
        'mail' => '<rect x="3" y="5" width="18" height="14" rx="2.5"/><path d="M4 7l8 6 8-6"/>',
        'pin' => '<path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/>',
        'receipt' => '<path d="M6 3h12v18l-3-2-3 2-3-2-3 2V3z"/><path d="M9 8h6M9 12h6"/>',
        'globe' => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.5 3.8 5.5 3.8 9s-1.3 6.5-3.8 9c-2.5-2.5-3.8-5.5-3.8-9S9.5 5.5 12 3z"/>',
        'zap' => '<path d="M13 3L5 13h6l-1 8 8-10h-6l1-8z"/>',
        'doc' => '<path d="M7 3h7l5 5v13H7z"/><path d="M14 3v5h5M10 13h6M10 17h6"/>',
        'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'chevron' => '<path d="M6 9l6 6 6-6"/>',
        'card' => '<rect x="3" y="5" width="18" height="14" rx="2.5"/><path d="M3 10h18M7 15h3"/>',
    ];
    return '<svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . ($icons[$name] ?? '') . '</svg>';
}

function logo_mark(): string
{
    static $n = 0;
    $id = 'lg' . (++$n);
    return '<svg class="logo__mark" viewBox="0 0 40 40" aria-hidden="true" focusable="false"><defs><linearGradient id="' . $id . '" x1="0" y1="0" x2="40" y2="40" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#2F6BFF"/><stop offset="1" stop-color="#36C8FF"/></linearGradient></defs><rect width="40" height="40" rx="11" fill="url(#' . $id . ')"/><path d="M12.5 11.5v17M12.5 11.5h14M12.5 20h9M12.5 28.5h14" stroke="#fff" stroke-width="4" stroke-linecap="round" fill="none"/><circle cx="27.6" cy="20" r="2.4" fill="#fff" fill-opacity=".85"/></svg>';
}

function lottie(string $name, string $class = '', bool $eager = false): string
{
    return '<div class="' . e(trim('lottie ' . $class)) . '" data-lottie="' . e(asset('lottie/' . $name . '.json')) . '"' . ($eager ? ' data-eager="true"' : '') . ' aria-hidden="true"></div>';
}

function routes(): array
{
    static $routes = null;
    $routes ??= require __DIR__ . '/routes.php';
    return $routes;
}

function page_meta(string $slug): array
{
    return routes()[$slug] ?? [
        'view' => '404',
        'name' => 'Page not found',
        'title' => 'Page not found - ' . cfg('brand'),
        'description' => 'The page you are looking for does not exist or has been moved.',
    ];
}

function render_page(string $slug): string
{
    $page = page_meta($slug);
    $view = $page['view'];
    ob_start();
    require dirname(__DIR__) . '/pages/' . $view . '.php';
    $content = (string) ob_get_clean();
    ob_start();
    require __DIR__ . '/layout.php';
    return (string) ob_get_clean();
}

function robots_txt(): string
{
    return "User-agent: *\nAllow: /\n\nSitemap: " . site_url('sitemap.xml') . "\n";
}

function sitemap_xml(): string
{
    $modified = date('Y-m-d', strtotime((string) cfg('updated')) ?: time());
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    foreach (array_keys(routes()) as $route) {
        $xml .= '  <url><loc>' . e(site_url((string) $route)) . '</loc><lastmod>' . $modified . '</lastmod></url>' . "\n";
    }
    return $xml . '</urlset>' . "\n";
}

function redirect(string $location, int $code = 303): never
{
    header('Location: ' . $location, true, $code);
    exit;
}

function content_security_policy(): string
{
    $hash = 'sha256-' . base64_encode(hash('sha256', BOOT_SCRIPT, true));
    return "default-src 'self'; img-src 'self' data:; style-src 'self' 'unsafe-inline'; script-src 'self' '" . $hash . "'; font-src 'self'; connect-src 'self'; manifest-src 'self'; form-action 'self'; base-uri 'self'; object-src 'none'";
}

function send_security_headers(): void
{
    header_remove('X-Powered-By');
    header('Content-Security-Policy: ' . content_security_policy() . "; frame-ancestors 'none'");
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
    if (is_https()) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

function nav_items(): array
{
    return [
        'services' => 'Services',
        'how-it-works' => 'How it works',
        'partners' => 'Partners',
        'compliance' => 'Compliance',
    ];
}

function legal_header(string $title, string $lead): string
{
    return '<section class="page-hero"><div class="hero__bg" aria-hidden="true"><span class="blob blob--1"></span><span class="hero__lines"></span></div>'
        . '<div class="container"><p class="eyebrow reveal"><span class="eyebrow__dot"></span> Legal</p>'
        . '<h1 class="reveal" style="--d:1">' . e($title) . '</h1>'
        . '<p class="lead reveal" style="--d:2">' . $lead . '</p>'
        . '<p class="legal-meta reveal" style="--d:3">' . icon('clock') . ' Last updated: ' . e(cfg('updated')) . '</p></div></section>';
}

function toc(array $sections): string
{
    $html = '<details class="toc" data-toc><summary>Contents ' . icon('chevron') . '</summary><ol>';
    $i = 1;
    foreach ($sections as $id => $label) {
        $html .= '<li><a href="#' . e($id) . '"><span>' . $i++ . '.</span> ' . e($label) . '</a></li>';
    }
    return $html . '</ol></details>';
}

function schema_graph(string $slug, array $page): array
{
    $home = site_url();
    $organization = [
        '@type' => 'Organization',
        '@id' => $home . '#organization',
        'name' => cfg('brand'),
        'url' => $home,
        'logo' => ['@type' => 'ImageObject', 'url' => site_url('assets/img/icon-512.png'), 'width' => 512, 'height' => 512],
        'image' => site_url('assets/img/og-image.png'),
        'description' => 'Payment service for selling subscriptions on websites, with payments processed by Stripe.',
    ];
    foreach (['legal_name' => 'legalName', 'trn' => 'taxID', 'email' => 'email', 'phone' => 'telephone', 'address' => 'address'] as $key => $property) {
        if (company_raw($key) !== '') {
            $organization[$property] = company_raw($key);
        }
    }
    $graph = [
        $organization,
        ['@type' => 'WebSite', '@id' => $home . '#website', 'url' => $home, 'name' => cfg('brand'), 'inLanguage' => 'en', 'publisher' => ['@id' => $home . '#organization']],
    ];
    if ($slug === '404') {
        return ['@context' => 'https://schema.org', '@graph' => $graph];
    }
    $pageUrl = site_url($slug);
    $webpage = [
        '@type' => 'WebPage',
        '@id' => $pageUrl . '#webpage',
        'url' => $pageUrl,
        'name' => $page['title'],
        'description' => $page['description'],
        'inLanguage' => 'en',
        'isPartOf' => ['@id' => $home . '#website'],
        'about' => ['@id' => $home . '#organization'],
    ];
    if ($slug !== '') {
        $webpage['breadcrumb'] = ['@id' => $pageUrl . '#breadcrumb'];
        $graph[] = [
            '@type' => 'BreadcrumbList',
            '@id' => $pageUrl . '#breadcrumb',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $home],
                ['@type' => 'ListItem', 'position' => 2, 'name' => $page['name'], 'item' => $pageUrl],
            ],
        ];
    }
    $graph[] = $webpage;
    return ['@context' => 'https://schema.org', '@graph' => $graph];
}
