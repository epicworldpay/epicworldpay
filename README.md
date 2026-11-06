# EpicWorldPay

Sito di EpicWorldPay: una pagina principale più Privacy e Termini. È scritto in PHP e viene pubblicato su GitHub Pages come sito statico.

## Avvio in locale

- Con XAMPP: avvia Apache e apri `http://localhost/`
- Senza XAMPP: `php -S localhost:8080 index.php` dalla cartella del sito

## Pubblicazione

Ogni push su `main` avvia il workflow `.github/workflows/pages.yml`, che genera le pagine HTML con `php tools/build.php` e le pubblica su GitHub Pages. Per provare la build in locale usa `php tools/build.php dist`; la cartella `dist` deve essere vuota o non esistere.

Impostazioni da fare una sola volta su GitHub, in Settings > Pages:

1. Source: GitHub Actions.
2. Custom domain: `epicworldpay.org`, poi Enforce HTTPS quando il certificato è pronto.
3. Dopo aver cambiato il dominio, rilancia il workflow da Actions > Deploy site > Run workflow.

Record DNS del dominio:

- A per `@`: 185.199.108.153, 185.199.109.153, 185.199.110.153, 185.199.111.153
- CNAME per `www`: epicworldpay.github.io

## Dati e impostazioni

- Dominio (`site_url`), email di assistenza e dati della società (EPIC WORLD GROUP - FZCO, licenza IFZA n. 55256, TRN) sono in `config.php`.
- Ogni dato mancante compare sul sito come etichetta gialla: quando non ne vedi più, i dati sono completi.
- In `config.php` ci sono anche la commissione (`fee`) e il prefisso del descrittore (`statement_prefix`).
- Il modulo contatti è dimostrativo: mostra la conferma di invio ma non spedisce e non salva i messaggi.

## Verifica Stripe

EpicWorldPay incassa abbonamenti per conto dei siti partner, quindi va attivato come piattaforma Stripe Connect. Il sito descrive già questo modello: ogni sito ha il proprio conto Stripe e riceve direttamente gli incassi, EpicWorldPay trattiene solo la commissione, e i Termini includono la clausola sul Connected Account Agreement e l'elenco delle attività non ammesse.

## Struttura

- `index.php` router con URL puliti, sitemap e robots
- `config.php` dati aziendali e impostazioni
- `includes/` funzioni, layout e rotte
- `pages/` home, privacy, terms e 404
- `assets/` CSS, JS, font, icone, animazioni Lottie e libreria lottie-web
- `tools/` build statica (`php tools/build.php`), animazioni (`php tools/lottie.php`) e icone (`php tools/brand.php`)
- `.github/workflows/pages.yml` pubblicazione automatica su GitHub Pages
