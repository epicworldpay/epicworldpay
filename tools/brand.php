<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$chrome = getenv('CHROME_PATH') ?: 'C:/Program Files/Google/Chrome/Application/chrome.exe';
$work = sys_get_temp_dir() . '/epicworldpay-brand';
if (!is_dir($work)) {
    mkdir($work, 0775, true);
}

function mark_svg(int $size, bool $rounded, bool $compact = false, float $inset = 0): string
{
    $radius = $rounded ? 11 : 0;
    $stroke = $compact ? 5 : 4;
    $scale = 1 - $inset * 2;
    $offset = 20 - 20 * $scale;
    $dot = $compact ? '' : '<circle cx="27.6" cy="20" r="2.4" fill="#fff" fill-opacity=".9"/>';
    return '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 40 40">'
        . '<defs><linearGradient id="g" x1="0" y1="0" x2="40" y2="40" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#2F6BFF"/><stop offset="1" stop-color="#36C8FF"/></linearGradient></defs>'
        . '<rect width="40" height="40" rx="' . $radius . '" fill="url(#g)"/>'
        . '<g transform="translate(' . $offset . ' ' . $offset . ') scale(' . $scale . ')">'
        . '<path d="M12.5 11.5v17M12.5 11.5h14M12.5 20h9M12.5 28.5h14" stroke="#fff" stroke-width="' . $stroke . '" stroke-linecap="round" fill="none"/>'
        . $dot . '</g></svg>';
}

function render(string $chrome, string $html, string $target, int $width, int $height, string $work): void
{
    $file = $work . '/' . pathinfo($target, PATHINFO_FILENAME) . '.html';
    file_put_contents($file, $html);
    $url = 'file:///' . str_replace('\\', '/', $file);
    $cmd = '"' . $chrome . '" --headless=new --disable-gpu --hide-scrollbars --force-device-scale-factor=1 --default-background-color=00000000 --virtual-time-budget=3000 --window-size=' . $width . ',' . $height . ' --screenshot="' . $target . '" "' . $url . '" 2>&1';
    exec($cmd, $output, $code);
    if (!is_file($target)) {
        fwrite(STDERR, "Render fallito per $target\n" . implode("\n", $output) . "\n");
        exit(1);
    }
    printf("%-28s %6.1f KB\n", basename($target), filesize($target) / 1024);
}

function svg_page(string $svg, int $size): string
{
    return '<!doctype html><html><head><meta charset="utf-8"><style>html,body{margin:0;background:transparent}svg{display:block;width:' . $size . 'px;height:' . $size . 'px}</style></head><body>' . $svg . '</body></html>';
}

function ico(array $pngs, string $target): void
{
    $count = count($pngs);
    $header = pack('vvv', 0, 1, $count);
    $directory = '';
    $data = '';
    $offset = 6 + 16 * $count;
    foreach ($pngs as $size => $file) {
        $bytes = (string) file_get_contents($file);
        $directory .= pack('CCCCvvVV', $size >= 256 ? 0 : $size, $size >= 256 ? 0 : $size, 0, 0, 1, 32, strlen($bytes), $offset);
        $data .= $bytes;
        $offset += strlen($bytes);
    }
    file_put_contents($target, $header . $directory . $data);
    printf("%-28s %6.1f KB\n", basename($target), filesize($target) / 1024);
}

$img = $root . '/assets/img';
if (!is_dir($img)) {
    mkdir($img, 0775, true);
}

file_put_contents($img . '/favicon.svg', mark_svg(40, true));
echo "favicon.svg\n";

render($chrome, svg_page(mark_svg(16, true, true), 16), $work . '/fav-16.png', 16, 16, $work);
render($chrome, svg_page(mark_svg(32, true, true), 32), $work . '/fav-32.png', 32, 32, $work);
render($chrome, svg_page(mark_svg(48, true), 48), $work . '/fav-48.png', 48, 48, $work);
ico([16 => $work . '/fav-16.png', 32 => $work . '/fav-32.png', 48 => $work . '/fav-48.png'], $root . '/favicon.ico');

render($chrome, svg_page(mark_svg(180, false, false, 0.08), 180), $root . '/apple-touch-icon.png', 180, 180, $work);
render($chrome, svg_page(mark_svg(192, true), 192), $img . '/icon-192.png', 192, 192, $work);
render($chrome, svg_page(mark_svg(512, true), 512), $img . '/icon-512.png', 512, 512, $work);
render($chrome, svg_page(mark_svg(512, false, false, 0.14), 512), $img . '/icon-maskable-512.png', 512, 512, $work);

$font = 'file:///' . str_replace('\\', '/', $root) . '/assets/fonts/manrope.woff2';
$mark = mark_svg(84, true);
$og = <<<HTML
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><style>
@font-face{font-family:Manrope;src:url("$font") format("woff2");font-weight:200 800}
*{box-sizing:border-box}
html,body{margin:0;width:1200px;height:630px;overflow:hidden}
body{position:relative;font-family:Manrope,sans-serif;color:#fff;background:radial-gradient(700px 420px at 92% 8%,rgba(54,200,255,.55),transparent 60%),radial-gradient(600px 400px at 0% 100%,rgba(47,107,255,.55),transparent 60%),linear-gradient(135deg,#071230 0%,#0f2a86 55%,#2459e6 100%)}
.grid{position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.06) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.06) 1px,transparent 1px);background-size:48px 48px;-webkit-mask-image:radial-gradient(ellipse 80% 80% at 70% 40%,#000 20%,transparent 75%)}
.copy{position:absolute;left:72px;top:64px;width:640px}
.brand{display:flex;align-items:center;gap:18px;font-size:46px;font-weight:800;letter-spacing:-.04em}
.brand span{color:#8fd0ff}
.brand svg{width:72px;height:72px;filter:drop-shadow(0 12px 24px rgba(0,0,0,.35))}
h1{margin:60px 0 0;font-size:66px;line-height:1.04;letter-spacing:-.045em;font-weight:800}
h1 em{font-style:normal;background:linear-gradient(100deg,#8fd0ff,#36c8ff);-webkit-background-clip:text;color:transparent}
p{margin:26px 0 0;font-size:25px;line-height:1.45;color:#c9d8ff;font-weight:600;width:560px}
.chips{position:absolute;left:72px;bottom:56px;display:flex;gap:10px}
.chips div{padding:9px 16px;border:1px solid rgba(255,255,255,.22);border-radius:12px;background:rgba(255,255,255,.08);font-size:17px;font-weight:700;color:#e4ecff}
.stage{position:absolute;right:70px;top:130px;width:380px;height:380px}
.ring{position:absolute;inset:0;border-radius:50%;border:2px dashed rgba(255,255,255,.22)}
.ring2{position:absolute;inset:50px;border-radius:50%;border:2px solid rgba(255,255,255,.12)}
.card{position:absolute;left:40px;top:118px;width:300px;height:188px;border-radius:24px;background:linear-gradient(135deg,#2f6bff,#36c8ff);transform:rotate(-7deg);box-shadow:0 40px 70px -25px rgba(0,0,0,.6),inset 0 0 0 1.5px rgba(255,255,255,.3)}
.card.back{left:70px;top:88px;transform:rotate(9deg);background:linear-gradient(135deg,#9fbcff,#c8d9ff);box-shadow:none;opacity:.9}
.chip{position:absolute;left:30px;top:52px;width:44px;height:34px;border-radius:8px;background:rgba(255,255,255,.92)}
.dots{position:absolute;left:30px;top:112px;display:flex;gap:22px}
.dots i{display:flex;gap:6px}
.dots b{width:8px;height:8px;border-radius:50%;background:rgba(255,255,255,.9)}
.name{position:absolute;left:30px;bottom:26px;width:96px;height:10px;border-radius:5px;background:rgba(255,255,255,.6)}
.logo{position:absolute;right:24px;bottom:20px;width:36px;height:36px;border-radius:10px;background:#fff}
.badge{position:absolute;right:20px;top:72px;width:76px;height:76px;border-radius:50%;background:#12b886;border:5px solid #fff;box-shadow:0 18px 30px -10px rgba(0,0,0,.45);display:grid;place-items:center}
.toast{position:absolute;left:-60px;bottom:6px;display:flex;align-items:center;gap:14px;padding:14px 20px;border-radius:18px;background:rgba(255,255,255,.95);color:#0b1734;box-shadow:0 30px 50px -20px rgba(0,0,0,.55)}
.toast strong{display:block;font-size:18px}
.toast small{display:block;font-size:14px;color:#5f6e8c;font-weight:600}
.toast em{font-style:normal;font-weight:800;color:#0ca678;font-size:19px;margin-left:8px}
.toast span.ic{width:42px;height:42px;border-radius:12px;background:#e3f8f0;display:grid;place-items:center}
</style></head><body>
<div class="grid"></div>
<div class="copy">
<div class="brand">$mark<div>EpicWorld<span>Pay</span></div></div>
<h1>Your website subscriptions, <em>on autopilot.</em></h1>
<p>Secure checkout, automatic renewals and payment recovery. Processed by Stripe.</p>
</div>
<div class="chips"><div>Visa</div><div>Mastercard</div><div>Apple Pay</div><div>Google Pay</div><div>SEPA</div></div>
<div class="stage">
<div class="ring"></div><div class="ring2"></div>
<div class="card back"></div>
<div class="card"><div class="chip"></div><div class="dots"><i><b></b><b></b><b></b><b></b></i><i><b></b><b></b><b></b><b></b></i><i><b></b><b></b><b></b><b></b></i></div><div class="name"></div><div class="logo"></div></div>
<div class="badge"><svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg></div>
<div class="toast"><span class="ic"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0ca678" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 11a8 8 0 0 0-14.5-4.5L4 8"/><path d="M4 4v4h4"/><path d="M4 13a8 8 0 0 0 14.5 4.5L20 16"/><path d="M20 20v-4h-4"/></svg></span><div><strong>Renewal successful</strong><small>Monthly subscription</small></div><em>+€19.00</em></div>
</div>
</body></html>
HTML;
render($chrome, $og, $img . '/og-image.png', 1200, 630, $work);

$manifest = [
    'name' => 'EpicWorldPay',
    'short_name' => 'EpicWorldPay',
    'description' => 'Recurring payments for websites: secure checkout, automatic renewals and payments processed by Stripe.',
    'lang' => 'en',
    'start_url' => './',
    'scope' => './',
    'display' => 'standalone',
    'background_color' => '#ffffff',
    'theme_color' => '#2f6bff',
    'icons' => [
        ['src' => 'assets/img/icon-192.png', 'sizes' => '192x192', 'type' => 'image/png'],
        ['src' => 'assets/img/icon-512.png', 'sizes' => '512x512', 'type' => 'image/png'],
        ['src' => 'assets/img/icon-maskable-512.png', 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'maskable'],
    ],
];
file_put_contents($root . '/site.webmanifest', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n");
echo "site.webmanifest\n";
