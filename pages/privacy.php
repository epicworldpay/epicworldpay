<?php

$brand = brand();
$sections = [
    'controller' => 'Data controller',
    'data' => 'Data we process',
    'purposes' => 'Purposes and legal bases',
    'roles' => 'Roles of websites, Stripe and EpicWorldPay',
    'recipients' => 'Recipients',
    'transfers' => 'International transfers',
    'retention' => 'Retention',
    'security' => 'Security',
    'rights' => 'Your rights',
    'cookies' => 'Cookies',
    'children' => 'Children',
    'changes' => 'Changes',
];
?>
<?= legal_header('Privacy Policy', 'How ' . $brand . ' collects, uses and protects personal data under the UAE Personal Data Protection Law (Federal Decree-Law No. 45 of 2021) and, for users in the European Union, the EU General Data Protection Regulation 2016/679 (GDPR).') ?>

<div class="container legal">
    <?= toc($sections) ?>
    <article class="prose">
        <h2 id="controller">1. Data controller</h2>
        <p>The data controller is <?= company('legal_name') ?>, registered at <?= company('address') ?>, trade license no. <?= company('license') ?> issued by <?= company('authority') ?>. For any request about personal data, write to <?= company('email') ?>.</p>

        <h2 id="data">2. Data we process</h2>
        <ul>
            <li><strong>Website visitors</strong>: technical data logged by our server, such as IP address, browser, pages visited, date and time.</li>
            <li><strong>Contact requests</strong>: name, email address and the content of your message.</li>
            <li><strong>Partner websites</strong>: contact person details, company and tax details, billing details and information about the business.</li>
            <li><strong>Customers of partner websites</strong>: name, email address, country, subscription details, payment and refund history and non-sensitive card details such as card brand and last four digits. Full card numbers and security codes are processed only by Stripe.</li>
        </ul>

        <h2 id="purposes">3. Purposes and legal bases</h2>
        <div class="table-wrap">
            <table>
                <thead><tr><th scope="col">Purpose</th><th scope="col">Legal basis</th></tr></thead>
                <tbody>
                    <tr><td>Replying to contact requests</td><td>Pre-contractual steps and legitimate interest (GDPR art. 6.1 b and f)</td></tr>
                    <tr><td>Providing the service to partner websites and support</td><td>Performance of a contract (GDPR art. 6.1 b)</td></tr>
                    <tr><td>Verifying websites, preventing fraud and keeping the platform secure</td><td>Legitimate interest and legal obligations (GDPR art. 6.1 c and f)</td></tr>
                    <tr><td>Accounting and tax obligations</td><td>Legal obligation (GDPR art. 6.1 c)</td></tr>
                    <tr><td>Managing subscriptions on behalf of partner websites</td><td>Processing as a processor on behalf of the website (GDPR art. 28)</td></tr>
                </tbody>
            </table>
        </div>
        <p>We do not use personal data for advertising profiles and we never sell it to third parties.</p>

        <h2 id="roles">4. Roles of websites, Stripe and <?= $brand ?></h2>
        <p>When you buy a subscription from a partner website, that website is the controller of your data and <?= $brand ?> processes it on the website's behalf. Stripe processes payment data as an independent controller, including to prevent fraud and meet anti money laundering obligations, as described in the <a href="https://stripe.com/privacy" target="_blank" rel="noopener">Stripe Privacy Policy</a>.</p>

        <h2 id="recipients">5. Recipients</h2>
        <p>Personal data is processed by authorised staff and may be shared with Stripe, with our hosting and email providers acting as processors, with our accounting and legal advisers, with the partner website you bought from and with public authorities when required by law.</p>

        <h2 id="transfers">6. International transfers</h2>
        <p><?= $brand ?> is based in the United Arab Emirates and some providers, including Stripe, may process data in other countries. For data of users in the European Union, transfers take place with the safeguards required by the GDPR, such as the Standard Contractual Clauses approved by the European Commission or an adequacy decision.</p>

        <h2 id="retention">7. Retention</h2>
        <ul>
            <li>Server logs: up to 90 days, unless needed to investigate abuse.</li>
            <li>Contact requests: up to 24 months after the last communication.</li>
            <li>Contract and billing data: for the duration of the contract and then for the period required by applicable tax and accounting rules.</li>
            <li>Data of partner websites' customers: for the duration of the relationship with the website, then deleted or returned to the website, unless the law requires otherwise.</li>
        </ul>

        <h2 id="security">8. Security</h2>
        <p>We use encrypted connections, restricted and monitored access, backups and trusted providers. Full card details never pass through or are stored on our systems.</p>

        <h2 id="rights">9. Your rights</h2>
        <p>You can ask at any time to access, correct or delete your data, to restrict or object to its processing and to receive it in a portable format. Write to <?= company('email') ?> and we will reply within 30 days. If you live in the European Union you can also lodge a complaint with the data protection authority of your country.</p>

        <h2 id="cookies">10. Cookies</h2>
        <p>This website does not set any cookies, neither technical nor profiling, and does not use third party analytics or advertising tools. Fonts, animations and scripts are served from our own servers without contacting external services, so no consent banner is needed. During payment on the Stripe checkout, Stripe may use cookies that are strictly necessary for the security of the transaction, as described in the <a href="https://stripe.com/legal/cookies-policy" target="_blank" rel="noopener">Stripe Cookie Policy</a>.</p>

        <h2 id="children">11. Children</h2>
        <p>Our service is not intended for anyone under 18 and we do not knowingly collect data about them.</p>

        <h2 id="changes">12. Changes</h2>
        <p>We may update this policy. The date of the latest change is shown at the top of this page and, for significant changes, we notify partner websites by email.</p>
    </article>
</div>
