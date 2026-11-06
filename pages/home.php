<?php

$prefix = e(cfg('statement_prefix', 'EPICWORLD'));
$fee = e(cfg('fee', ''));

$services = [
    ['f-checkout', 'Subscription payments', 'Secure checkout processed by Stripe: your customers pay by card, Apple Pay, Google Pay or SEPA, monthly or yearly.'],
    ['f-recurring', 'Full management', 'Automatic renewals, smart retries on failed payments, receipts, refunds and disputes, all in one platform.'],
    ['f-payout', 'Direct payouts', 'Funds go to your website\'s Stripe account and from there to your bank account. ' . cfg('brand') . ' only keeps its own fee.'],
];
$steps = [
    ['step-connect', 'Your website signs up', 'Create your account and complete identity verification with Stripe. We check that your website follows our rules.'],
    ['step-checkout', 'Customers subscribe', 'Add the checkout to your website. Customers pay in seconds and renewals run on their own, with an email receipt every time.'],
    ['step-payout', 'You get paid', 'Payments land in your website\'s Stripe account and then in your bank account, without ever passing through our hands.'],
];
$terms = [
    ['Payment processing', 'Stripe'],
    ['Settlement account', 'Owned by the partner'],
    ['Payouts', 'Directly to the website'],
    ['Bank statement', $prefix . '* website name'],
    ['Refunds and disputes', 'Handled by ' . brand()],
    ['Monthly fee', 'None'],
    ['Transaction fee', $fee . ' per transaction'],
];
$features = [
    ['refresh', 'Automatic renewals', 'Monthly and yearly plans, free trials and automatic retries when a payment fails.'],
    ['globe', 'EU VAT', 'Automatic VAT calculation based on the customer\'s country, powered by Stripe Tax.'],
    ['receipt', 'Receipts and support', 'An email receipt for every payment and customer support for charges, cancellations and refunds.'],
    ['shield', 'Fraud and disputes', 'Stripe fraud screening on every payment and dispute handling with the evidence required.'],
];
?>
<section class="hero">
    <div class="hero__bg" aria-hidden="true">
        <span class="blob blob--1"></span>
        <span class="blob blob--2"></span>
        <span class="hero__lines"></span>
    </div>
    <div class="container hero__inner">
        <div class="hero__copy">
            <p class="eyebrow reveal"><span class="eyebrow__dot"></span> Recurring payments for websites</p>
            <h1 class="reveal" style="--d:1">Collect your website's subscriptions, <span class="grad-text">we handle the rest</span></h1>
            <p class="lead reveal" style="--d:2"><?= brand() ?> is the payment service websites use to sell subscriptions: checkout, renewals, refunds and disputes in one place, with payments processed by Stripe.</p>
            <div class="hero__actions reveal" style="--d:3">
                <a class="btn btn--primary btn--lg" href="#contact">Become a partner <?= icon('arrow-right') ?></a>
                <a class="btn btn--ghost btn--lg" href="#how-it-works">How it works</a>
            </div>
        </div>
        <div class="hero__visual reveal" style="--d:2">
            <div class="hero__stage">
                <?= lottie('hero', 'hero__lottie', true) ?>
                <div class="float-card float-card--a" aria-hidden="true">
                    <span class="float-card__icon"><?= icon('refresh') ?></span>
                    <span><strong>Renewal successful</strong><small>Monthly subscription</small></span>
                    <em>+€19.00</em>
                </div>
                <div class="float-card float-card--b" aria-hidden="true">
                    <span class="float-card__icon float-card__icon--blue"><?= icon('zap') ?></span>
                    <span><strong>Payout on the way</strong><small>To the website's account</small></span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section" id="services">
    <div class="container">
        <header class="section-head reveal">
            <p class="eyebrow"><span class="eyebrow__dot"></span> What we offer</p>
            <h2>Everything you need to sell subscriptions</h2>
        </header>
        <div class="grid-3">
            <?php foreach ($services as $i => [$animation, $title, $text]): ?>
                <article class="card reveal" style="--d:<?= $i ?>">
                    <div class="card__icon"><?= lottie($animation) ?></div>
                    <h3><?= e($title) ?></h3>
                    <p><?= e($text) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section--tint" id="how-it-works">
    <div class="container">
        <header class="section-head reveal">
            <p class="eyebrow"><span class="eyebrow__dot"></span> How it works</p>
            <h2>Three steps, zero hassle</h2>
        </header>
        <ol class="grid-3 steps">
            <?php foreach ($steps as $i => [$animation, $title, $text]): ?>
                <li class="card step reveal" style="--d:<?= $i ?>">
                    <div class="step__visual"><?= lottie($animation, 'step__lottie') ?></div>
                    <span class="step__num"><?= $i + 1 ?></span>
                    <h3><?= e($title) ?></h3>
                    <p><?= e($text) ?></p>
                </li>
            <?php endforeach; ?>
        </ol>
    </div>
</section>

<section class="section" id="partners">
    <div class="container split">
        <div class="reveal">
            <p class="eyebrow"><span class="eyebrow__dot"></span> For partners</p>
            <h2>You focus on your website, we handle payments</h2>
            <p class="lead">A complete payment system for your subscribers, with no fixed costs and no complicated integrations.</p>
            <ul class="checklist">
                <li><?= icon('check') ?> Checkout and recurring payments ready to use</li>
                <li><?= icon('check') ?> Renewals, refunds and disputes handled for you</li>
                <li><?= icon('check') ?> EU VAT calculated automatically</li>
                <li><?= icon('check') ?> Transparent fee, no monthly plan</li>
            </ul>
            <a class="btn btn--primary" href="#contact">Become a partner <?= icon('arrow-right') ?></a>
        </div>
        <div class="panel reveal" style="--d:2">
            <h3 class="panel__title"><?= icon('doc') ?> Service terms</h3>
            <dl>
                <?php foreach ($terms as [$label, $value]): ?>
                    <div><dt><?= e($label) ?></dt><dd><?= $value ?></dd></div>
                <?php endforeach; ?>
            </dl>
        </div>
    </div>
</section>

<section class="section section--tint" id="compliance">
    <div class="container split">
        <div class="compliance__visual reveal"><?= lottie('f-shield') ?></div>
        <div class="reveal" style="--d:1">
            <p class="eyebrow"><span class="eyebrow__dot"></span> Compliance</p>
            <h2>How <?= brand() ?> operates</h2>
            <ul class="checklist">
                <li><?= icon('check') ?> Every website completes Stripe identity verification before it can accept payments.</li>
                <li><?= icon('check') ?> Card data is handled only by Stripe, a PCI DSS Level 1 certified provider.</li>
                <li><?= icon('check') ?> Strong Customer Authentication with 3D Secure, as required by the PSD2 directive.</li>
                <li><?= icon('check') ?> We do not accept businesses prohibited by Stripe, such as adult content, gambling, weapons or illegal products.</li>
            </ul>
            <p class="note">Payments are processed by Stripe. <?= brand() ?> never stores card details and never holds partner funds, except for its own fees. The full rules are in our <a href="<?= e(url('terms')) ?>">Terms of Service</a>.</p>
        </div>
    </div>
</section>

<section class="section" id="integrations">
    <div class="container">
        <header class="section-head reveal">
            <p class="eyebrow"><span class="eyebrow__dot"></span> Integrations</p>
            <h2>The payment methods your customers use every day</h2>
        </header>
        <ul class="brands reveal">
            <?php foreach (cfg('payment_methods', []) as $method): ?>
                <li><?= e($method) ?></li>
            <?php endforeach; ?>
        </ul>
        <div class="grid-4">
            <?php foreach ($features as $i => [$ico, $title, $text]): ?>
                <article class="feature reveal" style="--d:<?= $i ?>">
                    <span class="feature__icon"><?= icon($ico) ?></span>
                    <h3><?= e($title) ?></h3>
                    <p><?= e($text) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section--tint" id="contact">
    <div class="container split split--top">
        <div class="reveal">
            <p class="eyebrow"><span class="eyebrow__dot"></span> Become a partner</p>
            <h2>Activate <?= brand() ?> on your website</h2>
            <p class="lead">Tell us about your website and what you sell: we reply within <?= e(cfg('reply_time')) ?>.</p>
            <ul class="contact-list">
                <li><?= icon('mail') ?> <span><?= company('email') ?></span></li>
                <li><?= icon('pin') ?> <span><?= company('address') ?></span></li>
                <li><?= icon('doc') ?> <span><?= company('legal_name') ?>, license no. <?= company('license') ?></span></li>
            </ul>
            <p class="note"><?= icon('card') ?> Seen an <strong><?= $prefix ?>*</strong> charge on your bank statement? Write to us with the date and amount: we will tell you which subscription it belongs to and how to cancel it.</p>
        </div>
        <div class="form-card reveal" style="--d:2">
            <form method="post" novalidate data-form>
                <div class="field">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" autocomplete="name" required minlength="2" maxlength="100" placeholder="John Smith" data-error="Please enter your name.">
                </div>
                <div class="field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" autocomplete="email" required maxlength="190" placeholder="john@yourwebsite.com" data-error="Please enter a valid email address.">
                </div>
                <div class="field">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" required minlength="10" maxlength="5000" placeholder="Tell us about your website, what you sell and how many subscribers you have." data-error="Please write a message of at least 10 characters."></textarea>
                </div>
                <button type="submit" class="btn btn--primary btn--lg btn--block">Send message <?= icon('arrow-right') ?></button>
                <p class="form-note">By sending this form you confirm you have read our <a href="<?= e(url('privacy')) ?>">Privacy Policy</a>.</p>
            </form>
            <div class="success" tabindex="-1" hidden data-form-success>
                <div class="success__lottie"><?= lottie('success') ?></div>
                <h3>Message sent</h3>
                <p>Thank you for contacting <?= brand() ?>.</p>
            </div>
        </div>
    </div>
</section>
