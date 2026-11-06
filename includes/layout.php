<?php

$canonical = site_url($slug === '404' ? '' : $slug);
$ogImage = site_url('assets/img/og-image.png');
?><!doctype html>
<html lang="en" data-lottie-lib="<?= e(asset('vendor/lottie_light.min.js')) ?>">
<head>
<meta charset="utf-8">
<meta http-equiv="Content-Security-Policy" content="<?= e(content_security_policy()) ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<script><?= BOOT_SCRIPT ?></script>
<title><?= e($page['title']) ?></title>
<meta name="description" content="<?= e($page['description']) ?>">
<?php if ($slug === '404'): ?>
<meta name="robots" content="noindex, follow">
<?php else: ?>
<meta name="robots" content="index, follow, max-image-preview:large">
<link rel="canonical" href="<?= e($canonical) ?>">
<link rel="alternate" hreflang="en" href="<?= e($canonical) ?>">
<?php endif; ?>
<meta name="theme-color" content="#2f6bff">
<meta name="format-detection" content="telephone=no">
<meta property="og:type" content="website">
<meta property="og:locale" content="en_US">
<meta property="og:site_name" content="<?= brand() ?>">
<meta property="og:title" content="<?= e($page['title']) ?>">
<meta property="og:description" content="<?= e($page['description']) ?>">
<meta property="og:url" content="<?= e($canonical) ?>">
<meta property="og:image" content="<?= e($ogImage) ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="<?= brand() ?>, payments and subscriptions for websites">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= e($page['title']) ?>">
<meta name="twitter:description" content="<?= e($page['description']) ?>">
<meta name="twitter:image" content="<?= e($ogImage) ?>">
<link rel="icon" href="<?= e(url('favicon.ico')) ?>" sizes="48x48">
<link rel="icon" href="<?= e(asset('img/favicon.svg')) ?>" type="image/svg+xml">
<link rel="apple-touch-icon" href="<?= e(url('apple-touch-icon.png')) ?>">
<link rel="manifest" href="<?= e(url('site.webmanifest')) ?>">
<link rel="preload" href="<?= e(url('assets/fonts/manrope.woff2')) ?>" as="font" type="font/woff2" crossorigin>
<link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
<script src="<?= e(asset('js/app.js')) ?>" defer></script>
<script type="application/ld+json"><?= json_encode(schema_graph($slug, $page), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?></script>
</head>
<body class="page-<?= e($view) ?>">
<a class="skip-link" href="#main">Skip to content</a>

<header class="site-header" data-header>
    <div class="container site-header__inner">
        <a class="logo" href="<?= e(url()) ?>" aria-label="<?= brand() ?>, go to home page">
            <?= logo_mark() ?>
            <span>EpicWorld<b>Pay</b></span>
        </a>
        <nav class="nav" aria-label="Main navigation">
            <?php foreach (nav_items() as $anchor => $label): ?>
                <a href="<?= e(url('#' . $anchor)) ?>"><?= e($label) ?></a>
            <?php endforeach; ?>
        </nav>
        <a class="btn btn--primary btn--sm site-header__cta" href="<?= e(url('#contact')) ?>">Become a partner <?= icon('arrow-right') ?></a>
        <button class="burger" type="button" aria-label="Open menu" aria-expanded="false" aria-controls="mobile-menu" data-burger>
            <span></span><span></span><span></span>
        </button>
    </div>
    <nav class="mobile-menu" id="mobile-menu" aria-label="Mobile navigation" data-mobile-menu hidden>
        <div class="container">
            <?php foreach (nav_items() as $anchor => $label): ?>
                <a href="<?= e(url('#' . $anchor)) ?>"><?= e($label) ?> <?= icon('arrow-right') ?></a>
            <?php endforeach; ?>
            <a class="btn btn--primary btn--block" href="<?= e(url('#contact')) ?>">Become a partner <?= icon('arrow-right') ?></a>
        </div>
    </nav>
</header>

<main id="main">
<?= $content ?>
</main>

<footer class="site-footer">
    <div class="container">
        <div class="site-footer__top">
            <a class="logo logo--light" href="<?= e(url()) ?>" aria-label="<?= brand() ?>, go to home page">
                <?= logo_mark() ?>
                <span>EpicWorld<b>Pay</b></span>
            </a>
            <nav class="site-footer__links" aria-label="Legal links">
                <a href="<?= e(url('privacy')) ?>">Privacy Policy</a>
                <a href="<?= e(url('terms')) ?>">Terms of Service</a>
                <a href="<?= e(url('#contact')) ?>">Contact</a>
            </nav>
        </div>
        <div class="site-footer__bottom">
            <p>&copy; <?= date('Y') ?> <?= brand() ?>. All rights reserved. <?= brand() ?> is a service of <?= company('legal_name') ?>, <?= company('address') ?>. Trade license no. <?= company('license') ?> issued by <?= company('authority') ?>. TRN <?= company('trn') ?>.</p>
            <p><?= icon('lock') ?> Payments are processed by Stripe. <?= brand() ?> never stores card details.</p>
        </div>
    </div>
</footer>
</body>
</html>
