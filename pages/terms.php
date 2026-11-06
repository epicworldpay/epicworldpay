<?php

$brand = brand();
$prefix = e(cfg('statement_prefix', 'EPICWORLD'));
$sections = [
    'scope' => 'Scope',
    'eligibility' => 'Eligibility and activation',
    'stripe' => 'Stripe payment services',
    'service' => 'The service',
    'obligations' => 'Partner obligations',
    'prohibited' => 'Prohibited businesses',
    'fees' => 'Fees and payments',
    'refunds' => 'Refunds and cancellations',
    'disputes' => 'Disputes',
    'suspension' => 'Suspension and termination',
    'term' => 'Term',
    'data' => 'Personal data',
    'ip' => 'Intellectual property',
    'liability' => 'Limitation of liability',
    'changes' => 'Changes',
    'law' => 'Governing law and jurisdiction',
    'contact-terms' => 'Contact',
];
?>
<?= legal_header('Terms of Service', 'The terms that govern the use of ' . $brand . ' by partner websites that sell subscriptions to their customers.') ?>

<div class="container legal">
    <?= toc($sections) ?>
    <article class="prose">
        <h2 id="scope">1. Scope</h2>
        <p>These Terms govern the relationship between <?= company('legal_name') ?>, registered at <?= company('address') ?>, trade license no. <?= company('license') ?> issued by <?= company('authority') ?>, TRN <?= company('trn') ?> ("<?= $brand ?>", "we") and any business that uses the service to sell subscriptions on its own website ("Partner", "you"). End customers are the people who buy a subscription from a Partner.</p>

        <h2 id="eligibility">2. Eligibility and activation</h2>
        <p>The service is available only to businesses and professionals aged 18 or over. To activate it, the Partner provides true and complete information about its business and website. The website must clearly show the products or services sold, prices, renewal frequency, terms of sale, refund policy and a customer support contact. <?= $brand ?> may refuse activation if these requirements are not met.</p>

        <h2 id="stripe">3. Stripe payment services</h2>
        <p>Payment processing services for Partners on <?= $brand ?> are provided by Stripe and are subject to the <a href="https://stripe.com/connect-account/legal" target="_blank" rel="noopener">Stripe Connected Account Agreement</a>, which includes the <a href="https://stripe.com/legal/ssa" target="_blank" rel="noopener">Stripe Terms of Service</a> (collectively, the "Stripe Services Agreement"). By agreeing to these Terms or continuing to operate as a Partner on <?= $brand ?>, you agree to be bound by the Stripe Services Agreement, as the same may be modified by Stripe from time to time.</p>
        <p>As a condition of <?= $brand ?> enabling payment processing services through Stripe, you agree to provide <?= $brand ?> accurate and complete information about you and your business, and you authorize <?= $brand ?> to share it and transaction information related to your use of the payment processing services provided by Stripe.</p>
        <p>Each Partner has its own verified Stripe account. Payments from end customers are credited to that account and paid out to the Partner's bank account on Stripe's payout schedule. <?= $brand ?> never holds Partner funds, except for its own fees.</p>

        <h2 id="service">4. The service</h2>
        <p><?= $brand ?> provides Partners with a subscription checkout, management of renewals and payment retries, email receipts, handling of refunds and disputes and support for end customers about charges. On the end customer's bank statement the charge appears as <strong><?= $prefix ?>*</strong> followed by the Partner's name.</p>

        <h2 id="obligations">5. Partner obligations</h2>
        <ul>
            <li>sell only lawful products and services, described clearly and truthfully;</li>
            <li>show the price, renewal frequency, any trial period and how to cancel before payment;</li>
            <li>obtain the customer's explicit consent to recurring payments and let them cancel easily;</li>
            <li>publish a refund policy and respect the consumer rights provided by law;</li>
            <li>support its own customers and keep its account details up to date;</li>
            <li>not use the service to accept payments on behalf of other businesses or websites that have not been approved.</li>
        </ul>

        <h2 id="prohibited">6. Prohibited businesses</h2>
        <p><?= $brand ?> cannot be used for any business prohibited by law, by card network rules or by the <a href="https://stripe.com/legal/restricted-businesses" target="_blank" rel="noopener">Stripe list of restricted businesses</a>, including:</p>
        <ul>
            <li>adult content and services, including sexually explicit material and adult dating;</li>
            <li>gambling, betting, lotteries and paid sweepstakes;</li>
            <li>weapons, ammunition, drugs, unlicensed pharmaceuticals, tobacco and e-cigarettes;</li>
            <li>counterfeit goods or products that infringe copyright or trademarks;</li>
            <li>unlicensed financial services, cryptocurrencies, money transmission and debt collection;</li>
            <li>pyramid schemes, multi level marketing and get rich quick offers;</li>
            <li>sale of fake followers, reviews or engagement and any deceptive business practice.</li>
        </ul>

        <h2 id="fees">7. Fees and payments</h2>
        <p><?= $brand ?> charges a fee of <?= e(cfg('fee')) ?> of the amount of each successful transaction, unless otherwise agreed in writing, with no monthly fees. The fee is deducted automatically when each payment is made. Stripe processing fees are separate and governed by the Stripe Services Agreement. All amounts are exclusive of applicable taxes and an invoice is issued for our fees.</p>

        <h2 id="refunds">8. Refunds and cancellations</h2>
        <p><strong>End customers.</strong> Customers can cancel a subscription at any time through the link included in every receipt or by writing to <?= company('email') ?>. Cancellation stops all future renewals and the service remains active until the end of the period already paid. Refunds follow the Partner's refund policy and the rights provided by law, including the 14 day right of withdrawal for consumers in the European Union where applicable. For duplicate charges, charges made after a confirmed cancellation or technical errors, <?= $brand ?> refunds the customer directly. Refunds are returned to the original payment method, usually within 10 business days depending on the customer's bank.</p>
        <p><strong>Partners.</strong> When a Partner refunds a payment, the <?= $brand ?> fee on that transaction is returned in proportion to the amount refunded.</p>

        <h2 id="disputes">9. Disputes</h2>
        <p>Disputes opened by end customers with their bank are handled under the rules of Stripe and the card networks. The Partner provides the required evidence promptly and bears the disputed amounts and the related fees charged by Stripe. If a Partner's dispute or refund rate exceeds the thresholds allowed by the card networks, <?= $brand ?> may require corrective measures or suspend the service.</p>

        <h2 id="suspension">10. Suspension and termination</h2>
        <p><?= $brand ?> may suspend or terminate the service if these Terms or the Stripe Services Agreement are breached, if the Partner's Stripe account is closed, in case of fraudulent activity or risk to end customers, or at the request of an authority. Except in urgent cases, the Partner is notified before suspension. When the service is terminated, active subscriptions are stopped and no further charges are made.</p>

        <h2 id="term">11. Term</h2>
        <p>The agreement has no fixed term. The Partner may terminate it at any time by written notice, without penalties. <?= $brand ?> may terminate it with 30 days' notice, without prejudice to section 10.</p>

        <h2 id="data">12. Personal data</h2>
        <p>Personal data is processed as described in our <a href="<?= e(url('privacy')) ?>">Privacy Policy</a>. For the data of end customers, of which the Partner is the controller, <?= $brand ?> acts as a processor under article 28 of the GDPR where applicable.</p>

        <h2 id="ip">13. Intellectual property</h2>
        <p>The <?= $brand ?> brand, software and website content belong to <?= $brand ?> or its licensors. Partners keep ownership of their own content, trademarks and data.</p>

        <h2 id="liability">14. Limitation of liability</h2>
        <p>To the extent permitted by law, <?= $brand ?> is not liable for indirect damages or loss of profit, nor for services provided by Stripe, banks or card networks. The total liability of <?= $brand ?> is limited to the fees paid by the Partner in the 12 months before the event, except in cases of wilful misconduct or gross negligence.</p>

        <h2 id="changes">15. Changes</h2>
        <p><?= $brand ?> may update these Terms by notifying significant changes by email at least 30 days before they take effect. A Partner who does not accept them may terminate the agreement at no cost before that date.</p>

        <h2 id="law">16. Governing law and jurisdiction</h2>
        <p>These Terms are governed by the laws of the United Arab Emirates as applied in the Emirate of Dubai. The courts of Dubai have exclusive jurisdiction over any dispute, without prejudice to the mandatory rights granted to consumers by the law of their country of residence.</p>

        <h2 id="contact-terms">17. Contact</h2>
        <p>For any question about these Terms, write to <?= company('email') ?> or use our <a href="<?= e(url('#contact')) ?>">contact form</a>.</p>
    </article>
</div>
