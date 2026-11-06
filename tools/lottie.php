<?php

declare(strict_types=1);

const EASES = [
    'inout' => [0.45, 0, 0.55, 1],
    'soft' => [0.33, 0, 0.67, 1],
    'out' => [0.16, 1, 0.3, 1],
    'in' => [0.55, 0, 0.9, 0.45],
    'back' => [0.34, 1.56, 0.64, 1],
    'linear' => [0, 0, 1, 1],
];

const PALETTE = [
    'blue' => '#2F6BFF',
    'deep' => '#1C47BF',
    'sky' => '#36C8FF',
    'b50' => '#F2F6FF',
    'b100' => '#E3ECFF',
    'b200' => '#C8D9FF',
    'b300' => '#9FBCFF',
    'navy' => '#0B1734',
    'white' => '#FFFFFF',
    'mint' => '#12B886',
    'mintl' => '#D9F6EA',
];

function num(mixed $v): mixed
{
    return is_float($v) ? round($v, 3) : $v;
}

function nums(mixed $v): mixed
{
    return is_array($v) ? array_map('nums', $v) : num($v);
}

function col(string $name, float $alpha = 1.0): array
{
    $hex = ltrim(PALETTE[$name] ?? $name, '#');
    return [
        round(hexdec(substr($hex, 0, 2)) / 255, 3),
        round(hexdec(substr($hex, 2, 2)) / 255, 3),
        round(hexdec(substr($hex, 4, 2)) / 255, 3),
        $alpha,
    ];
}

function prop(mixed $value): array
{
    if (is_array($value) && isset($value['a'], $value['k'])) {
        return $value;
    }
    return ['a' => 0, 'k' => nums($value)];
}

function anim(array $keys, string $default = 'inout'): array
{
    $frames = [];
    $keys = array_values($keys);
    $last = count($keys) - 1;
    foreach ($keys as $i => $key) {
        $value = $key[1];
        $frame = ['t' => $key[0], 's' => nums(is_array($value) ? $value : [$value])];
        if ($i < $last) {
            $ease = $key[2] ?? $default;
            if ($ease === 'hold') {
                $frame['h'] = 1;
            } else {
                [$x1, $y1, $x2, $y2] = EASES[$ease];
                $frame['o'] = ['x' => [$x1], 'y' => [$y1]];
                $frame['i'] = ['x' => [$x2], 'y' => [$y2]];
            }
        }
        $frames[] = $frame;
    }
    return ['a' => 1, 'k' => $frames];
}

function prop3(mixed $value, float $z = 0): array
{
    $pad = fn (array $v): array => count($v) === 2 ? [$v[0], $v[1], $z] : $v;
    if (is_array($value) && isset($value['a'], $value['k'])) {
        if ($value['a'] === 1) {
            foreach ($value['k'] as $i => $frame) {
                $value['k'][$i]['s'] = $pad($frame['s']);
            }
        } else {
            $value['k'] = $pad($value['k']);
        }
        return $value;
    }
    return prop($pad($value));
}

function rect(array $size, mixed $radius = 0, array $pos = [0, 0]): array
{
    return ['ty' => 'rc', 'd' => 1, 's' => prop($size), 'p' => prop($pos), 'r' => prop($radius), 'nm' => 'rect'];
}

function ellipse(array $size, array $pos = [0, 0]): array
{
    return ['ty' => 'el', 'd' => 1, 's' => prop($size), 'p' => prop($pos), 'nm' => 'ellipse'];
}

function circle(float $diameter, array $pos = [0, 0]): array
{
    return ellipse([$diameter, $diameter], $pos);
}

function path(array $vertices, bool $closed = false, ?array $in = null, ?array $out = null): array
{
    $zero = array_fill(0, count($vertices), [0, 0]);
    return [
        'ty' => 'sh',
        'ks' => ['a' => 0, 'k' => ['i' => nums($in ?? $zero), 'o' => nums($out ?? $zero), 'v' => nums($vertices), 'c' => $closed]],
        'nm' => 'path',
    ];
}

function fill(mixed $color, mixed $opacity = 100): array
{
    return ['ty' => 'fl', 'c' => prop($color), 'o' => prop($opacity), 'r' => 1, 'nm' => 'fill'];
}

function stroke(mixed $color, mixed $width, mixed $opacity = 100, ?array $dash = null): array
{
    $item = ['ty' => 'st', 'c' => prop($color), 'o' => prop($opacity), 'w' => prop($width), 'lc' => 2, 'lj' => 2, 'ml' => 4, 'nm' => 'stroke'];
    if ($dash !== null) {
        $item['d'] = [
            ['n' => 'd', 'nm' => 'dash', 'v' => prop($dash[0])],
            ['n' => 'g', 'nm' => 'gap', 'v' => prop($dash[1])],
            ['n' => 'o', 'nm' => 'offset', 'v' => prop($dash[2] ?? 0)],
        ];
    }
    return $item;
}

function gradient(array $stops, array $from, array $to): array
{
    $k = [];
    foreach ($stops as [$offset, $color]) {
        $rgb = col($color);
        array_push($k, $offset, $rgb[0], $rgb[1], $rgb[2]);
    }
    return ['ty' => 'gf', 'o' => prop(100), 'r' => 1, 's' => prop($from), 'e' => prop($to), 't' => 1, 'g' => ['p' => count($stops), 'k' => prop($k)], 'nm' => 'gradient'];
}

function trimp(mixed $start, mixed $end, mixed $offset = 0): array
{
    return ['ty' => 'tm', 's' => prop($start), 'e' => prop($end), 'o' => prop($offset), 'm' => 1, 'nm' => 'trim'];
}

function tr(array $t = []): array
{
    return [
        'ty' => 'tr',
        'p' => prop($t['p'] ?? [0, 0]),
        'a' => prop($t['a'] ?? [0, 0]),
        's' => prop($t['s'] ?? [100, 100]),
        'r' => prop($t['r'] ?? 0),
        'o' => prop($t['o'] ?? 100),
        'sk' => prop(0),
        'sa' => prop(0),
        'nm' => 'transform',
    ];
}

function group(array $items, array $t = [], string $name = 'group'): array
{
    $count = count($items);
    $items[] = tr($t);
    return ['ty' => 'gr', 'it' => $items, 'nm' => $name, 'np' => $count, 'bm' => 0];
}

function layer(string $name, array $shapes, array $t = []): array
{
    return [
        'ddd' => 0,
        'ty' => 4,
        'nm' => $name,
        'sr' => 1,
        'ks' => [
            'o' => prop($t['o'] ?? 100),
            'r' => prop($t['r'] ?? 0),
            'p' => prop3($t['p'] ?? [0, 0]),
            'a' => prop3($t['a'] ?? [0, 0]),
            's' => prop3($t['s'] ?? [100, 100], 100),
        ],
        'ao' => 0,
        'shapes' => $shapes,
        'ip' => 0,
        'st' => 0,
        'bm' => 0,
    ];
}

function comp(string $name, int $w, int $h, int $frames, array $layers): array
{
    $layers = array_values(array_reverse($layers));
    foreach ($layers as $i => $layer) {
        $layers[$i]['ind'] = $i + 1;
        $layers[$i]['op'] = $frames;
    }
    return ['v' => '5.9.0', 'fr' => 60, 'ip' => 0, 'op' => $frames, 'w' => $w, 'h' => $h, 'nm' => $name, 'ddd' => 0, 'assets' => [], 'layers' => $layers];
}

function polar(float $deg, float $r, array $c = [0, 0]): array
{
    $a = deg2rad($deg);
    return [$c[0] + $r * cos($a), $c[1] + $r * sin($a)];
}

function star(float $size): array
{
    $k = $size * 0.24;
    return path([[0, -$size], [$k, -$k], [$size, 0], [$k, $k], [0, $size], [-$k, $k], [-$size, 0], [-$k, -$k]], true);
}

function arrowhead(float $deg, float $r, float $size): array
{
    $p = polar($deg, $r);
    $d = polar($deg + 90, 1);
    $n = polar($deg, 1);
    $tip = [$p[0] + $d[0] * $size, $p[1] + $d[1] * $size];
    $b1 = [$p[0] + $n[0] * $size * 0.95 - $d[0] * $size * 0.2, $p[1] + $n[1] * $size * 0.95 - $d[1] * $size * 0.2];
    $b2 = [$p[0] - $n[0] * $size * 0.95 - $d[0] * $size * 0.2, $p[1] - $n[1] * $size * 0.95 - $d[1] * $size * 0.2];
    return path([$tip, $b1, $b2], true);
}

function sparkle(array $pos, float $size, string $color, int $start, int $frames, int $life = 50): array
{
    $mid = $start + (int) round($life * 0.4);
    $end = $start + $life;
    $keys = [[0, [0, 0], 'hold'], [$start, [0, 0], 'out'], [$mid, [100, 100], 'inout'], [$end, [0, 0], 'hold'], [$frames, [0, 0]]];
    if ($start === 0) {
        $keys = [[0, [0, 0], 'out'], [$mid, [100, 100], 'inout'], [$end, [0, 0], 'hold'], [$frames, [0, 0]]];
    }
    return layer('sparkle', [group([star($size), fill(col($color))])], [
        'p' => $pos,
        's' => anim($keys),
        'r' => anim([[0, 0, 'linear'], [$frames, 90]]),
    ]);
}

function float_keys(array $pos, float $amp, int $frames, float $dx = 0): array
{
    return anim([[0, $pos], [(int) ($frames / 2), [$pos[0] + $dx, $pos[1] - $amp]], [$frames, $pos]]);
}

function check_path(float $s = 1.0): array
{
    return path([[-12 * $s, 1 * $s], [-4 * $s, 9 * $s], [12 * $s, -8 * $s]]);
}

function e_mark(float $scale, string $color, float $width): array
{
    $s = $scale;
    return group([
        path([[-7.5 * $s, -8.5 * $s], [-7.5 * $s, 8.5 * $s]]),
        path([[-7.5 * $s, -8.5 * $s], [6.5 * $s, -8.5 * $s]]),
        path([[-7.5 * $s, 0], [1.5 * $s, 0]]),
        path([[-7.5 * $s, 8.5 * $s], [6.5 * $s, 8.5 * $s]]),
        stroke(col($color), $width),
    ]);
}

function toggle_x(array $on, array $off, float $from, float $to): array
{
    return anim([[0, [$from, 0], 'hold'], [$on[0], [$from, 0], 'back'], [$on[1], [$to, 0], 'hold'], [$off[0], [$to, 0], 'inout'], [$off[1], [$from, 0]]]);
}

function toggle_color(array $on, array $off, string $a, string $b): array
{
    return anim([[0, col($a), 'hold'], [$on[0], col($a), 'inout'], [$on[1], col($b), 'hold'], [$off[0], col($b), 'inout'], [$off[1], col($a)]]);
}

function hero(): array
{
    $F = 240;
    $c = [250, 250];
    $layers = [];
    $layers[] = layer('glow', [group([circle(260), fill(col('b100'))])], [
        'p' => $c,
        'o' => anim([[0, 55], [120, 95], [240, 55]]),
        's' => anim([[0, [96, 96]], [120, [104, 104]], [240, [96, 96]]]),
    ]);
    $layers[] = layer('orbit', [group([circle(420), stroke(col('b200'), 2, 100, [6, 10, anim([[0, 0, 'linear'], [$F, -160]])])])], ['p' => $c]);
    $layers[] = layer('orbit-inner', [group([circle(310), stroke(col('b100'), 2)])], ['p' => $c]);
    $layers[] = layer('pulse', [group([circle(250), stroke(col('blue'), 2)])], [
        'p' => $c,
        's' => anim([[0, [82, 82], 'out'], [119, [132, 132], 'hold'], [120, [82, 82], 'out'], [239, [132, 132]]]),
        'o' => anim([[0, 40, 'out'], [119, 0, 'hold'], [120, 40, 'out'], [239, 0]]),
    ]);
    $r = 180;
    $head = polar(-90 + 0.22 * 360, $r);
    $layers[] = layer('renewal', [
        group([circle(18, $head), stroke(col('blue'), 4), fill(col('white'))]),
        group([circle($r * 2), trimp(0, 22), stroke(col('blue'), 6)]),
        group([circle($r * 2), trimp(50, 58), stroke(col('sky'), 4, 70)]),
    ], ['p' => $c, 'r' => anim([[0, 0, 'linear'], [$F, 360]])]);
    foreach ([[208, 0, 'b300', 12], [300, 72, 'sky', 9], [252, 144, 'blue', 8]] as [$x, $delay, $color, $d]) {
        $layers[] = layer('particle', [group([circle($d), fill(col($color))])], [
            'p' => anim([[0, [$x, 392], 'hold'], [$delay, [$x, 392], 'soft'], [$delay + 96, [$x + 6, 300], 'hold'], [$F, [$x + 6, 300]]]),
            'o' => anim([[0, 0, 'hold'], [$delay, 0, 'soft'], [$delay + 30, 100, 'soft'], [$delay + 96, 0, 'hold'], [$F, 0]]),
        ]);
    }
    $layers[] = layer('shadow', [group([ellipse([200, 26]), fill(col('navy'), 9)])], [
        'p' => [258, 368],
        's' => anim([[0, [100, 100]], [120, [84, 84]], [240, [100, 100]]]),
    ]);
    $layers[] = layer('card-back', [
        group([rect([218, 138], 20), stroke(col('white'), 1.5, 35)]),
        group([rect([218, 138], 20), gradient([[0, 'b300'], [1, 'b200']], [-109, -69], [109, 69])]),
    ], [
        'p' => anim([[0, [282, 234]], [120, [284, 226]], [240, [282, 234]]]),
        'r' => anim([[0, 9], [120, 12], [240, 9]]),
    ]);
    $dots = [];
    for ($g = 0; $g < 4; $g++) {
        for ($j = 0; $j < 4; $j++) {
            $dots[] = circle(5.5, [-96 + $g * 48 + $j * 9, 18]);
        }
    }
    $dots[] = fill(col('white'), 88);
    $waves = [];
    foreach ([7, 12, 17] as $i => $radius) {
        $waves[] = group([circle($radius * 2), trimp(14, 36), stroke(col('white'), 2.6)], [
            'p' => [-58, -20],
            'o' => anim([[0, 35], [30 + $i * 12, 100], [90 + $i * 12, 35, 'hold'], [120, 35], [150 + $i * 12, 100], [210 + $i * 12, 35, 'hold'], [240, 35]]),
        ]);
    }
    $card = array_merge([
        group([e_mark(1.05, 'blue', 2.6), group([rect([26, 26], 7), fill(col('white'))])], ['p' => [86, 44]]),
        group([rect([72, 7], 3.5), fill(col('white'), 60)], ['p' => [-62, 46]]),
        group([rect([30, 7], 3.5), fill(col('white'), 60)], ['p' => [28, 46]]),
        group($dots),
    ], $waves, [
        group([
            path([[-17, -4], [-6, -4]]), path([[6, -4], [17, -4]]), path([[-17, 4], [-6, 4]]), path([[6, 4], [17, 4]]),
            stroke(col('b300'), 1.5),
        ], ['p' => [-84, -20]]),
        group([rect([34, 26], 6), fill(col('white'), 92)], ['p' => [-84, -20]]),
        group([circle(60, [94, -8]), stroke(col('white'), 2, 16)]),
        group([circle(100, [72, -30]), fill(col('white'), 10)]),
        group([rect([232, 146], 18), stroke(col('white'), 1.5, 28)]),
        group([rect([236, 150], 20), gradient([[0, 'blue'], [1, 'sky']], [-118, -75], [118, 75])]),
    ]);
    $layers[] = layer('card', $card, [
        'p' => anim([[0, [250, 262]], [120, [250, 250]], [240, [250, 262]]]),
        'r' => anim([[0, -6], [120, -3], [240, -6]]),
    ]);
    $badgeScale = anim([[0, [0, 0], 'hold'], [40, [0, 0], 'back'], [64, [100, 100], 'hold'], [200, [100, 100], 'in'], [218, [0, 0], 'hold'], [$F, [0, 0]]]);
    $layers[] = layer('badge', [
        group([
            group([check_path(1.0), trimp(0, anim([[0, 0, 'hold'], [56, 0, 'out'], [80, 100, 'hold'], [$F, 100]])), stroke(col('white'), 5)]),
            group([circle(56), stroke(col('white'), 4), fill(col('mint'))]),
            group([circle(56, [0, 5]), fill(col('navy'), 14)]),
        ], ['s' => $badgeScale]),
        group([circle(56), stroke(col('mint'), 3)], [
            's' => anim([[0, [100, 100], 'hold'], [46, [100, 100], 'out'], [100, [230, 230], 'hold'], [$F, [230, 230]]]),
            'o' => anim([[0, 0, 'hold'], [46, 70, 'out'], [100, 0, 'hold'], [$F, 0]]),
        ]),
    ], ['p' => anim([[0, [356, 180]], [120, [357, 168]], [240, [356, 180]]])]);
    $layers[] = sparkle([96, 170], 13, 'sky', 20, $F, 70);
    $layers[] = sparkle([410, 356], 10, 'blue', 110, $F, 70);
    $layers[] = sparkle([150, 392], 7, 'b300', 160, $F, 60);
    $layers[] = sparkle([414, 112], 9, 'sky', 64, $F, 70);
    return comp('hero', 500, 500, $F, $layers);
}

function window_frame(float $w, float $h): array
{
    $hw = $w / 2;
    $hh = $h / 2;
    return [
        group([circle(8, [-$hw + 18, -$hh + 15]), circle(8, [-$hw + 31, -$hh + 15]), circle(8, [-$hw + 44, -$hh + 15]), fill(col('b200'))]),
        group([rect([96, 14], 7, [10, -$hh + 15]), fill(col('b50'))]),
        group([path([[-$hw, -$hh + 30], [$hw, -$hh + 30]]), stroke(col('b100'), 2)]),
        group([rect([$w, $h], 16), stroke(col('b200'), 2), fill(col('white'))]),
    ];
}

function grow_line(float $w, float $h, float $x, float $y, string $color, int $start, int $end = 200, float $opacity = 100): array
{
    return group([rect([$w, $h], $h / 2, [$w / 2, 0]), fill(col($color), $opacity)], [
        'p' => [$x, $y],
        's' => anim([[0, [0, 100], 'hold'], [$start, [0, 100], 'out'], [$start + 26, [100, 100], 'hold'], [$end, [100, 100], 'in'], [$end + 14, [0, 100], 'hold'], [240, [0, 100]]]),
    ]);
}

function pop_scale(int $in, int $out, int $frames, string $ease = 'back'): array
{
    return anim([[0, [0, 0], 'hold'], [$in, [0, 0], $ease], [$in + 20, [100, 100], 'hold'], [$out, [100, 100], 'in'], [$out + 14, [0, 0], 'hold'], [$frames, [0, 0]]]);
}

function step_connect(): array
{
    $F = 240;
    $layers = [];
    $layers[] = layer('window', window_frame(244, 172), ['p' => float_keys([200, 152], 3, $F)]);
    $layers[] = layer('lines', [
        grow_line(118, 12, -98, -28, 'b200', 14),
        grow_line(170, 8, -98, -6, 'b100', 28),
        grow_line(140, 8, -98, 10, 'blue', 42, 200, 28),
        grow_line(96, 8, -98, 26, 'b100', 56),
    ], ['p' => float_keys([200, 152], 3, $F)]);
    $on = [96, 112];
    $off = [204, 218];
    $layers[] = layer('toggle', [
        group([circle(10), fill(toggle_color($on, $off, 'b200', 'mint'))], ['p' => [140, 0]]),
        group([rect([82, 8], 4), fill(toggle_color($on, $off, 'b100', 'mintl'))], ['p' => [80, 0]]),
        group([
            group([circle(22), fill(col('white'))]),
            group([circle(22, [0, 2]), fill(col('navy'), 14)]),
        ], ['p' => toggle_x($on, $off, -12, 12)]),
        group([rect([58, 32], 16), fill(toggle_color($on, $off, 'b200', 'blue'))]),
    ], ['p' => float_keys([128, 206], 3, $F)]);
    $layers[] = layer('badge', [
        group([
            group([check_path(0.72), trimp(0, anim([[0, 0, 'hold'], [118, 0, 'out'], [138, 100, 'hold'], [$F, 100]])), stroke(col('white'), 4)]),
            group([circle(42), stroke(col('white'), 3.5), fill(col('mint'))]),
        ], ['s' => pop_scale(112, 204, $F)]),
        group([circle(42), stroke(col('mint'), 3)], [
            's' => anim([[0, [100, 100], 'hold'], [114, [100, 100], 'out'], [160, [210, 210], 'hold'], [$F, [210, 210]]]),
            'o' => anim([[0, 0, 'hold'], [114, 70, 'out'], [160, 0, 'hold'], [$F, 0]]),
        ]),
    ], ['p' => float_keys([318, 68], 3, $F)]);
    $layers[] = sparkle([352, 110], 8, 'sky', 120, $F, 50);
    $layers[] = sparkle([60, 70], 7, 'b300', 30, $F, 60);
    return comp('step-connect', 400, 300, $F, $layers);
}

function step_checkout(): array
{
    $F = 240;
    $panel = [232, 150];
    $layers = [];
    $layers[] = layer('panel', [
        group([rect([22, 22], 6), fill(col('blue'))], ['p' => [-43, -84]]),
        group([rect([56, 8], 4), fill(col('b200'))], ['p' => [8, -84]]),
        group([rect([66, 14], 7), fill(col('navy'), 85)], ['p' => [-18, -56]]),
        group([rect([38, 8], 4), fill(col('b200'))], ['p' => [-32, -38]]),
        group([rect([18, 12], 3), fill(col('b300'))], ['p' => [-44, -6]]),
        group([rect([118, 26], 8), stroke(col('b100'), 1.5), fill(col('b50'))], ['p' => [0, -6]]),
        group([rect([26, 6], 3, [-36, 0]), rect([20, 6], 3, [34, 0]), fill(col('b200'))], ['p' => [0, 28]]),
        group([rect([118, 26], 8), stroke(col('b100'), 1.5), fill(col('b50'))], ['p' => [0, 28]]),
        group([rect([150, 228], 22), stroke(col('b200'), 2), fill(col('white'))]),
    ], ['p' => $panel]);
    $digits = [];
    foreach ([0, 1, 2, 3] as $g) {
        $x = -28 + $g * 23;
        $digits[] = group([circle(4, [0, 0]), circle(4, [5, 0]), circle(4, [10, 0]), circle(4, [15, 0]), fill(col('navy'), 70)], [
            'p' => [$x, -6],
            's' => anim([[0, [0, 0], 'hold'], [74 + $g * 6, [0, 0], 'back'], [86 + $g * 6, [100, 100], 'hold'], [206, [100, 100], 'inout'], [216, [0, 0], 'hold'], [$F, [0, 0]]]),
        ]);
    }
    $layers[] = layer('digits', $digits, ['p' => $panel]);
    $layers[] = layer('button', [
        group([check_path(0.62), trimp(0, anim([[0, 0, 'hold'], [124, 0, 'out'], [144, 100, 'hold'], [206, 100, 'inout'], [214, 0, 'hold'], [$F, 0]])), stroke(col('white'), 4)]),
        group([rect([44, 6], 3), fill(col('white'), 70)], ['o' => anim([[0, 100, 'hold'], [116, 100, 'inout'], [124, 0, 'hold'], [208, 0, 'inout'], [220, 100, 'hold'], [$F, 100]])]),
        group([rect([118, 32], 11), fill(anim([[0, col('blue'), 'hold'], [116, col('blue'), 'inout'], [128, col('mint'), 'hold'], [206, col('mint'), 'inout'], [218, col('blue'), 'hold'], [$F, col('blue')]]))]),
    ], [
        'p' => [$panel[0], $panel[1] + 80],
        's' => anim([[0, [100, 100], 'hold'], [100, [100, 100], 'inout'], [108, [92, 92], 'back'], [122, [100, 100], 'hold'], [$F, [100, 100]]]),
    ]);
    $confetti = [];
    foreach ([[-62, -22, 'mint'], [-40, -44, 'sky'], [0, -52, 'blue'], [40, -44, 'mint'], [62, -22, 'sky'], [20, -60, 'b300']] as $i => [$dx, $dy, $color]) {
        $confetti[] = group([circle($i % 2 ? 7 : 9), fill(col($color))], [
            'p' => anim([[0, [0, 0], 'hold'], [122, [0, 0], 'out'], [160, [$dx, $dy], 'hold'], [$F, [$dx, $dy]]]),
            'o' => anim([[0, 0, 'hold'], [122, 100, 'soft'], [164, 0, 'hold'], [$F, 0]]),
            's' => anim([[0, [100, 100], 'hold'], [122, [100, 100], 'soft'], [164, [30, 30], 'hold'], [$F, [30, 30]]]),
        ]);
    }
    $layers[] = layer('confetti', $confetti, ['p' => [$panel[0], $panel[1] + 80]]);
    $layers[] = layer('card', [
        group([rect([16, 12], 3), fill(col('white'), 90)], ['p' => [-28, -10]]),
        group([rect([52, 5], 2.5), fill(col('white'), 70)], ['p' => [-8, 12]]),
        group([rect([92, 58], 10), gradient([[0, 'blue'], [1, 'sky']], [-46, -29], [46, 29])]),
    ], [
        'p' => anim([[0, [-70, 176], 'hold'], [16, [-70, 176], 'out'], [52, [128, 178], 'inout'], [66, [190, 154], 'in'], [76, [222, 146], 'hold'], [$F, [222, 146]]]),
        'r' => anim([[0, -16, 'hold'], [16, -16, 'out'], [52, -6, 'inout'], [76, 0, 'hold'], [$F, 0]]),
        's' => anim([[0, [100, 100], 'hold'], [56, [100, 100], 'in'], [76, [72, 72], 'hold'], [$F, [72, 72]]]),
        'o' => anim([[0, 100, 'hold'], [64, 100, 'soft'], [76, 0, 'hold'], [$F, 0]]),
    ]);
    $layers[] = sparkle([330, 216], 8, 'mint', 128, $F, 50);
    $layers[] = sparkle([126, 70], 7, 'sky', 40, $F, 60);
    return comp('step-checkout', 400, 300, $F, $layers);
}

function coin(float $s = 1.0): array
{
    return [
        group([circle(22 * $s), stroke(col('blue'), max(2, 3 * $s))]),
        group([circle(38 * $s), stroke(col('b300'), max(2, 3 * $s)), fill(col('white'))]),
    ];
}

function wallet_bounce(array $times, int $frames): array
{
    $keys = [[0, [100, 100], 'hold']];
    foreach ($times as $t) {
        $keys[] = [$t, [100, 100], 'out'];
        $keys[] = [$t + 6, [104, 94], 'out'];
        $keys[] = [$t + 18, [100, 100], 'hold'];
    }
    $keys[] = [$frames, [100, 100]];
    return anim($keys);
}

function falling_coin(float $x, int $t, float $from, float $to, float $scale, int $frames): array
{
    return layer('coin', coin($scale), [
        'p' => anim([[0, [$x, $from], 'hold'], [$t, [$x, $from], 'in'], [$t + 30, [$x, $to], 'hold'], [$frames, [$x, $to]]]),
        's' => anim([[0, [100, 100], 'hold'], [$t, [100, 100], 'soft'], [$t + 10, [36, 100], 'soft'], [$t + 20, [100, 100], 'soft'], [$t + 30, [40, 100], 'hold'], [$frames, [40, 100]]]),
    ]);
}

function f_payout(): array
{
    $F = 180;
    $layers = [];
    $layers[] = layer('lip', [group([rect([100, 22], 9), fill(col('deep'))])], ['p' => [100, 92]]);
    $layers[] = falling_coin(94, 14, -24, 128, 0.72, $F);
    $layers[] = falling_coin(106, 82, -24, 128, 0.72, $F);
    $layers[] = layer('wallet', [
        group([circle(9), fill(col('white'))], ['p' => [32, 2]]),
        group([rect([42, 28], 10), fill(col('deep'), 45)], ['p' => [42, 2]]),
        group([rect([40, 6], 3), fill(col('white'), 60)], ['p' => [-26, 18]]),
        group([rect([112, 72], 15), stroke(col('white'), 1.5, 25)]),
        group([rect([116, 76], 16), gradient([[0, 'blue'], [1, 'sky']], [-58, -38], [58, 38])]),
    ], ['p' => [100, 166], 'a' => [0, 38], 's' => wallet_bounce([44, 112], $F)]);
    $layers[] = sparkle([152, 66], 8, 'sky', 46, $F, 44);
    $layers[] = sparkle([50, 74], 7, 'blue', 114, $F, 44);
    return comp('f-payout', 200, 200, $F, $layers);
}

function step_payout(): array
{
    $F = 240;
    $layers = [];
    $layers[] = layer('glow', [group([circle(220), fill(col('b100'))])], [
        'p' => [200, 186],
        'o' => anim([[0, 50], [120, 90], [240, 50]]),
    ]);
    $layers[] = layer('lip', [group([rect([166, 28], 12), fill(col('deep'))])], ['p' => [200, 148]]);
    foreach ([[186, 20], [214, 72], [198, 124]] as [$x, $t]) {
        $layers[] = falling_coin($x, $t, -30, 196, 1.0, $F);
    }
    $layers[] = layer('wallet', [
        group([circle(12), fill(col('white'))], ['p' => [52, 4]]),
        group([rect([66, 40], 14), fill(col('deep'), 45)], ['p' => [66, 4]]),
        group([rect([62, 8], 4), fill(col('white'), 60)], ['p' => [-42, 26]]),
        group([rect([38, 8], 4), fill(col('white'), 38)], ['p' => [-54, 8]]),
        group([rect([176, 108], 20), stroke(col('white'), 1.5, 25)]),
        group([rect([180, 112], 22), gradient([[0, 'blue'], [1, 'sky']], [-90, -56], [90, 56])]),
    ], ['p' => [200, 256], 'a' => [0, 56], 's' => wallet_bounce([50, 102, 154], $F)]);
    $layers[] = sparkle([288, 116], 9, 'sky', 54, $F, 44);
    $layers[] = sparkle([112, 104], 8, 'blue', 106, $F, 44);
    $layers[] = sparkle([296, 150], 6, 'b300', 158, $F, 44);
    return comp('step-payout', 400, 300, $F, $layers);
}

function f_checkout(): array
{
    $F = 180;
    $layers = [];
    $layers[] = layer('card', [
        group([rect([20, 15], 4), fill(col('white'), 88)], ['p' => [-38, -14]]),
        group([rect([60, 6], 3), fill(col('white'), 65)], ['p' => [-18, 14]]),
        group([rect([28, 6], 3), fill(col('white'), 40)], ['p' => [-34, 26]]),
        group([rect([124, 82], 13), gradient([[0, 'blue'], [1, 'sky']], [-62, -41], [62, 41])]),
    ], ['p' => float_keys([86, 100], 5, $F), 'r' => anim([[0, -9], [90, -5], [180, -9]])]);
    $shackle = path([[-8, -2], [-8, -9], [8, -9], [8, -2]], false, [[0, 0], [0, 0], [0, -10.7], [0, 0]], [[0, 0], [0, -10.7], [0, 0], [0, 0]]);
    $layers[] = layer('lock', [
        group([circle(5, [0, 3]), rect([2.6, 7], 1.3, [0, 8]), fill(col('white'))]),
        group([rect([26, 20], 5, [0, 6]), fill(col('blue'))]),
        group([$shackle, stroke(col('blue'), 4.5)], ['p' => anim([[0, [0, -6], 'hold'], [40, [0, -6], 'back'], [56, [0, 0], 'hold'], [150, [0, 0], 'inout'], [166, [0, -6], 'hold'], [$F, [0, -6]]])]),
        group([circle(62), stroke(col('b100'), 2), fill(col('white'))]),
        group([circle(62, [0, 5]), fill(col('navy'), 12)]),
    ], [
        'p' => [140, 128],
        's' => anim([[0, [100, 100], 'hold'], [54, [100, 100], 'out'], [60, [109, 109], 'out'], [74, [100, 100], 'hold'], [$F, [100, 100]]]),
    ]);
    $layers[] = layer('ring', [group([circle(62), stroke(col('blue'), 3)])], [
        'p' => [140, 128],
        's' => anim([[0, [100, 100], 'hold'], [56, [100, 100], 'out'], [96, [160, 160], 'hold'], [$F, [160, 160]]]),
        'o' => anim([[0, 0, 'hold'], [56, 60, 'out'], [96, 0, 'hold'], [$F, 0]]),
    ]);
    $layers[] = sparkle([168, 70], 8, 'sky', 60, $F, 50);
    return comp('f-checkout', 200, 200, $F, $layers);
}

function f_recurring(): array
{
    $F = 180;
    $r = 52;
    $layers = [];
    $layers[] = layer('arcs', [
        group([arrowhead(-90 + 0.40 * 360, $r, 12), stroke(col('blue'), 3), fill(col('blue'))]),
        group([arrowhead(-90 + 0.90 * 360, $r, 12), stroke(col('sky'), 3), fill(col('sky'))]),
        group([circle($r * 2), trimp(3, 40), stroke(col('blue'), 10)]),
        group([circle($r * 2), trimp(53, 90), stroke(col('sky'), 10)]),
    ], ['p' => [100, 100], 'r' => anim([[0, 0, 'inout'], [90, 180, 'inout'], [180, 360]])]);
    $layers[] = layer('euro', [
        group([
            group([circle(19), trimp(0, 80.5, 125), stroke(col('white'), 3.2)]),
            group([path([[-12, -3], [1, -3]]), path([[-12, 3], [1, 3]]), stroke(col('white'), 2.6)]),
        ], ['p' => [1, 0]]),
        group([circle(42), fill(col('blue'))]),
        group([circle(42), fill(col('b100'))], [
            's' => anim([[0, [100, 100], 'out'], [60, [150, 150], 'hold'], [90, [100, 100], 'out'], [150, [150, 150], 'hold'], [$F, [100, 100]]]),
            'o' => anim([[0, 100, 'out'], [60, 0, 'hold'], [90, 100, 'out'], [150, 0, 'hold'], [$F, 100]]),
        ]),
    ], ['p' => [100, 100], 's' => anim([[0, [100, 100]], [90, [108, 108]], [180, [100, 100]]])]);
    return comp('f-recurring', 200, 200, $F, $layers);
}

function shield_path(): array
{
    return path(
        [[0, -80], [60, -58], [60, -6], [0, 82], [-60, -6], [-60, -58]],
        true,
        [[0, 0], [0, 0], [0, 0], [34, -22], [0, 44], [0, 0]],
        [[0, 0], [0, 0], [0, 44], [-34, -22], [0, 0], [0, 0]]
    );
}

function f_shield(): array
{
    $F = 300;
    $c = [120, 124];
    $layers = [];
    $layers[] = layer('glow', [group([circle(200), fill(col('b100'))])], [
        'p' => $c,
        'o' => anim([[0, 40], [150, 90], [300, 40]]),
        's' => anim([[0, [94, 94]], [150, [104, 104]], [300, [94, 94]]]),
    ]);
    $layers[] = layer('orbit', [group([circle(212), stroke(col('b200'), 2, 100, [5, 9])])], ['p' => $c, 'r' => anim([[0, 0, 'linear'], [$F, 90]])]);
    $layers[] = layer('shield-fill', [group([shield_path(), gradient([[0, 'white'], [1, 'b100']], [0, -80], [0, 82])])], [
        'p' => $c,
        'o' => anim([[0, 0, 'hold'], [26, 0, 'soft'], [56, 100, 'hold'], [270, 100, 'soft'], [290, 0, 'hold'], [$F, 0]]),
    ]);
    $layers[] = layer('shield-line', [group([shield_path(), trimp(0, anim([[0, 0, 'hold'], [4, 0, 'inout'], [54, 100, 'hold'], [270, 100, 'inout'], [290, 0, 'hold'], [$F, 0]])), stroke(col('blue'), 8)])], ['p' => $c]);
    $layers[] = layer('core', [
        group([path([[-16, 0], [-4, 12], [18, -12]]), trimp(0, anim([[0, 0, 'hold'], [62, 0, 'out'], [86, 100, 'hold'], [$F, 100]])), stroke(col('white'), 8)], ['p' => [0, 2]]),
        group([shield_path(), gradient([[0, 'sky'], [1, 'blue']], [0, -80], [0, 82])], ['s' => [58, 58]]),
    ], ['p' => $c, 's' => pop_scale(40, 268, $F)]);
    $layers[] = sparkle([44, 64], 9, 'sky', 70, $F, 60);
    $layers[] = sparkle([200, 60], 7, 'blue', 110, $F, 60);
    $layers[] = sparkle([206, 186], 10, 'b300', 150, $F, 70);
    return comp('f-shield', 240, 240, $F, $layers);
}

function success(): array
{
    $F = 100;
    $layers = [];
    $layers[] = layer('burst', [group([circle(120), stroke(col('mint'), 4)])], [
        'p' => [100, 100],
        's' => anim([[0, [60, 60], 'out'], [44, [156, 156], 'hold'], [$F, [156, 156]]]),
        'o' => anim([[0, 0, 'soft'], [8, 80, 'out'], [44, 0, 'hold'], [$F, 0]]),
    ]);
    $particles = [];
    $colors = ['mint', 'sky', 'blue', 'mint', 'sky', 'blue', 'mint', 'sky'];
    for ($i = 0; $i < 8; $i++) {
        $angle = -90 + $i * 45;
        $particles[] = group([circle($i % 2 ? 8 : 11), fill(col($colors[$i]))], [
            'p' => anim([[0, polar($angle, 40), 'hold'], [14, polar($angle, 40), 'out'], [48, polar($angle, 88), 'hold'], [$F, polar($angle, 88)]]),
            's' => anim([[0, [0, 0], 'hold'], [14, [100, 100], 'soft'], [48, [0, 0], 'hold'], [$F, [0, 0]]]),
        ]);
    }
    $layers[] = layer('particles', $particles, ['p' => [100, 100]]);
    $layers[] = layer('circle', [group([circle(112), fill(col('mint'))])], [
        'p' => [100, 100],
        's' => anim([[0, [0, 0], 'back'], [24, [100, 100], 'hold'], [$F, [100, 100]]]),
    ]);
    $layers[] = layer('check', [group([path([[-24, 2], [-8, 18], [26, -16]]), trimp(0, anim([[0, 0, 'hold'], [18, 0, 'out'], [44, 100, 'hold'], [$F, 100]])), stroke(col('white'), 11)])], ['p' => [100, 100]]);
    return comp('success', 200, 200, $F, $layers);
}

function question_mark(): array
{
    return [
        group([circle(5.4, [0, 14]), fill(col('b300'))]),
        group([path([[-7, -5], [0, -12], [7, -5], [0, 3], [0, 7]], false, [[0, 0], [-4, 0], [0, -4], [3, -3], [0, 0]], [[0, -4], [4, 0], [0, 4], [0, 0], [0, 0]]), stroke(col('b300'), 3.6)]),
    ];
}

function not_found(): array
{
    $F = 240;
    $layers = [];
    $layers[] = layer('glow', [group([circle(240), fill(col('b100'))])], ['p' => [150, 150], 'o' => anim([[0, 50], [120, 90], [240, 50]])]);
    $lines = [];
    foreach ([[-46, 70, 'navy', 60, 10], [-24, 92, 'b100', 100, 8], [-8, 78, 'b100', 100, 8], [8, 92, 'b100', 100, 8], [24, 60, 'b100', 100, 8], [44, 84, 'b200', 100, 8]] as [$y, $w, $color, $o, $h]) {
        $lines[] = group([rect([$w, $h], $h / 2, [$w / 2, 0]), fill(col($color), $o)], ['p' => [-46, $y]]);
    }
    $layers[] = layer('doc', array_merge($lines, [group([rect([132, 164], 16), stroke(col('b200'), 2.5), fill(col('white'))])]), ['p' => float_keys([150, 152], 4, $F)]);
    $layers[] = layer('q1', question_mark(), ['p' => float_keys([246, 74], 8, $F), 'o' => anim([[0, 100], [120, 40], [240, 100]])]);
    $layers[] = layer('q2', question_mark(), ['p' => float_keys([56, 222], 6, $F), 's' => [80, 80], 'o' => anim([[0, 40], [120, 100], [240, 40]])]);
    $layers[] = layer('magnifier', [
        group([
            group([circle(16, [-12, -12]), fill(col('white'), 80)]),
            group([circle(64), stroke(col('blue'), 9), fill(col('b50'), 75)]),
            group([rect([14, 44], 7, [0, 56]), fill(col('navy'))]),
        ], ['r' => -40]),
    ], [
        'p' => anim([[0, [128, 118]], [60, [184, 140]], [120, [168, 196]], [180, [112, 170]], [240, [128, 118]]], 'soft'),
        'r' => anim([[0, -6], [60, 6], [120, -4], [180, 8], [240, -6]], 'soft'),
    ]);
    return comp('not-found', 300, 300, $F, $layers);
}

$out = dirname(__DIR__) . '/assets/lottie';
if (!is_dir($out)) {
    mkdir($out, 0775, true);
}

$files = [
    'hero' => hero(),
    'step-connect' => step_connect(),
    'step-checkout' => step_checkout(),
    'step-payout' => step_payout(),
    'f-checkout' => f_checkout(),
    'f-recurring' => f_recurring(),
    'f-payout' => f_payout(),
    'f-shield' => f_shield(),
    'success' => success(),
    'not-found' => not_found(),
];

foreach ($files as $name => $data) {
    $json = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
    file_put_contents($out . '/' . $name . '.json', $json);
    printf("%-14s %6.1f KB\n", $name, strlen($json) / 1024);
}
