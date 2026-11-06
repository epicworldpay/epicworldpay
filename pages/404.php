<section class="not-found">
    <div class="container">
        <div class="not-found__visual reveal"><?= lottie('not-found', '', true) ?></div>
        <h1 class="reveal" style="--d:1">This page does not exist</h1>
        <p class="lead reveal" style="--d:2">The link may be wrong or the page may have moved.</p>
        <a class="btn btn--primary btn--lg reveal" style="--d:3" href="<?= e(url()) ?>">Back to home <?= icon('arrow-right') ?></a>
    </div>
</section>
