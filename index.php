<?php
/**
 * LogoutAll — v1.2
 * https://logoutall.troovapp.fr
 *
 * One page to sign out of many websites, with the user's consent.
 *
 * How it works (and why):
 *  - Nothing is done silently. Modern browsers block hidden cross-site
 *    logout requests (SameSite cookies), so each sign-out page is opened
 *    in a new tab by a real click of the user.
 *  - This page cannot know whether a sign-out succeeded (the browser
 *    forbids reading another site's response). The user confirms each
 *    site himself; the "remaining" list is therefore always truthful.
 *  - No cookie, no tracking, no database. The contact form sends one
 *    e-mail and keeps nothing on the server.
 *
 * To add a site: append an entry to $sites below.
 *   type "direct" : opening the URL signs the user out immediately.
 *   type "manual" : the site asks for a confirmation click.
 *   "sessions"    : optional page to sign out of ALL devices.
 *
 * Contact form: anti-spam is stateless (honeypot + signed time trap).
 * The signing secret is generated on first run in ".logoutall-secret.php"
 * next to this file. Never commit that file (add it to .gitignore).
 */

declare(strict_types=1);

/* ---------------------------------------------------------------------
 * Settings
 * ------------------------------------------------------------------- */
const CONTACT_TO      = 'contact@logoutall.troovapp.fr'; // where messages arrive
const CONTACT_FROM    = 'contact@logoutall.troovapp.fr'; // sender (must belong to this domain)
const GITHUB_URL      = 'https://github.com/medhashas/logoutall'; // public source code
const COPYRIGHT       = '© 2026 mH';
const FORM_MIN_SECONDS = 3;      // faster than this = robot
const FORM_MAX_SECONDS = 7200;   // form validity: 2 hours
const MSG_MIN_LENGTH   = 10;
const MSG_MAX_LENGTH   = 5000;

$nonce = base64_encode(random_bytes(16));

header('Content-Type: text/html; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: no-referrer');
header(
    "Content-Security-Policy: default-src 'none'; "
    . "script-src 'nonce-$nonce'; style-src 'nonce-$nonce'; "
    . "base-uri 'none'; form-action 'self'; frame-ancestors 'none'"
);

/* ---------------------------------------------------------------------
 * Sites
 * ------------------------------------------------------------------- */
$sites = [
    ['id' => 'google',        'name' => 'Google / YouTube / Gmail', 'type' => 'direct',
     'url' => 'https://accounts.google.com/Logout',
     'sessions' => 'https://myaccount.google.com/device-activity'],
    ['id' => 'microsoft',     'name' => 'Microsoft / Outlook',      'type' => 'direct',
     'url' => 'https://login.live.com/logout.srf'],
    ['id' => 'amazon',        'name' => 'Amazon',                   'type' => 'direct',
     'url' => 'https://www.amazon.com/gp/flex/sign-out.html'],
    ['id' => 'linkedin',      'name' => 'LinkedIn',                 'type' => 'direct',
     'url' => 'https://www.linkedin.com/m/logout/'],
    ['id' => 'dropbox',       'name' => 'Dropbox',                  'type' => 'direct',
     'url' => 'https://www.dropbox.com/logout',
     'sessions' => 'https://www.dropbox.com/account/security'],
    ['id' => 'netflix',       'name' => 'Netflix',                  'type' => 'direct',
     'url' => 'https://www.netflix.com/SignOut',
     'sessions' => 'https://www.netflix.com/ManageDevices'],
    ['id' => 'spotify',       'name' => 'Spotify',                  'type' => 'direct',
     'url' => 'https://www.spotify.com/logout/'],
    ['id' => 'paypal',        'name' => 'PayPal',                   'type' => 'direct',
     'url' => 'https://www.paypal.com/signout'],
    ['id' => 'github',        'name' => 'GitHub',                   'type' => 'manual',
     'url' => 'https://github.com/logout',
     'sessions' => 'https://github.com/settings/sessions'],
    ['id' => 'x',             'name' => 'X (Twitter)',              'type' => 'manual',
     'url' => 'https://x.com/logout',
     'sessions' => 'https://x.com/settings/sessions'],
    ['id' => 'facebook',      'name' => 'Facebook',                 'type' => 'manual',
     'url' => 'https://www.facebook.com/settings?tab=security'],
    ['id' => 'instagram',     'name' => 'Instagram',                'type' => 'manual',
     'url' => 'https://www.instagram.com/accounts/logout/'],
    ['id' => 'yahoo',         'name' => 'Yahoo',                    'type' => 'manual',
     'url' => 'https://login.yahoo.com/account/logout'],
    ['id' => 'wikipedia',     'name' => 'Wikipedia',                'type' => 'manual',
     'url' => 'https://en.wikipedia.org/wiki/Special:UserLogout'],
    ['id' => 'stackoverflow', 'name' => 'Stack Overflow',           'type' => 'manual',
     'url' => 'https://stackoverflow.com/users/logout'],
    ['id' => 'wordpress',     'name' => 'WordPress.com',            'type' => 'manual',
     'url' => 'https://wordpress.com/wp-login.php?action=logout'],
    ['id' => 'slack',         'name' => 'Slack',                    'type' => 'manual',
     'url' => 'https://slack.com/signout'],
    ['id' => 'atlassian',     'name' => 'Atlassian / Trello / Jira', 'type' => 'manual',
     'url' => 'https://id.atlassian.com/logout'],
];

/* ---------------------------------------------------------------------
 * Translations
 * ------------------------------------------------------------------- */
$i18n = [
    'en' => [
        'title'        => 'LogoutAll — sign out of your accounts in one place',
        'tagline'      => 'Choose the sites, then sign out of them one click at a time.',
        'notice'       => 'Nothing happens without your click. This page cannot see whether you are signed in anywhere, and it stores nothing.',
        'select_all'   => 'Select all',
        'start'        => 'Start signing out',
        'none'         => 'Select at least one site.',
        'direct'       => 'Signs out when opened',
        'manual'       => 'Confirmation needed on the site',
        'step2'        => 'Sign out',
        'step2_help'   => 'Open each site, check that you are signed out, then tick it.',
        'open_next'    => 'Open next site',
        'open'         => 'Open',
        'opened'       => 'Opened',
        'done'         => 'Signed out',
        'sessions'     => 'Sign out of all devices',
        'remaining'    => 'Still to finish: {n}',
        'all_done'     => 'All selected sites are marked as signed out.',
        'back'         => 'Back to the list',
        'footer'       => 'Open source',
        'footer_src'   => 'Source on GitHub',
        'other_lang'   => 'Français',
        'c_title'      => 'Contact',
        'c_intro'      => 'A site to add, a broken link, a suggestion? Write to us.',
        'c_name'       => 'Name (optional)',
        'c_email'      => 'Your e-mail',
        'c_message'    => 'Message',
        'c_send'       => 'Send',
        'c_privacy'    => 'Your message is sent by e-mail to the maintainer. Nothing is stored on this server.',
        'c_sent'       => 'Thank you, your message has been sent.',
        'c_err_email'  => 'Enter a valid e-mail address.',
        'c_err_msg'    => 'Your message must be between {min} and {max} characters.',
        'c_err_fast'   => 'That was too fast. Please send the form again.',
        'c_err_expired' => 'The form has expired. Please send it again.',
        'c_err_send'   => 'The message could not be sent. You can write directly to {email}.',
        'c_subject'    => 'LogoutAll contact',
    ],
    'fr' => [
        'title'        => 'LogoutAll — déconnectez vos comptes depuis une seule page',
        'tagline'      => 'Choisissez les sites, puis déconnectez-les un clic à la fois.',
        'notice'       => 'Rien ne se passe sans votre clic. Cette page ne peut pas voir si vous êtes connecté quelque part, et elle ne conserve rien.',
        'select_all'   => 'Tout sélectionner',
        'start'        => 'Lancer la déconnexion',
        'none'         => 'Sélectionnez au moins un site.',
        'direct'       => 'Déconnecte à l’ouverture',
        'manual'       => 'Confirmation à faire sur le site',
        'step2'        => 'Déconnexion',
        'step2_help'   => 'Ouvrez chaque site, vérifiez que vous êtes déconnecté, puis cochez-le.',
        'open_next'    => 'Ouvrir le site suivant',
        'open'         => 'Ouvrir',
        'opened'       => 'Ouvert',
        'done'         => 'Déconnecté',
        'sessions'     => 'Déconnecter tous les appareils',
        'remaining'    => 'Reste à terminer : {n}',
        'all_done'     => 'Tous les sites sélectionnés sont marqués comme déconnectés.',
        'back'         => 'Retour à la liste',
        'footer'       => 'Code ouvert',
        'footer_src'   => 'Source sur GitHub',
        'other_lang'   => 'English',
        'c_title'      => 'Contact',
        'c_intro'      => 'Un site à ajouter, un lien cassé, une suggestion ? Écrivez-nous.',
        'c_name'       => 'Nom (facultatif)',
        'c_email'      => 'Votre e-mail',
        'c_message'    => 'Message',
        'c_send'       => 'Envoyer',
        'c_privacy'    => 'Votre message est envoyé par e-mail au responsable du site. Rien n’est conservé sur ce serveur.',
        'c_sent'       => 'Merci, votre message a bien été envoyé.',
        'c_err_email'  => 'Saisissez une adresse e-mail valide.',
        'c_err_msg'    => 'Votre message doit contenir entre {min} et {max} caractères.',
        'c_err_fast'   => 'Envoi trop rapide. Merci de renvoyer le formulaire.',
        'c_err_expired' => 'Le formulaire a expiré. Merci de le renvoyer.',
        'c_err_send'   => 'Le message n’a pas pu être envoyé. Vous pouvez écrire directement à {email}.',
        'c_subject'    => 'Contact LogoutAll',
    ],
];

$lang = $_GET['lang'] ?? '';
if (!is_string($lang) || !isset($i18n[$lang])) {
    $accept = strtolower(substr((string) ($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? ''), 0, 2));
    $lang = isset($i18n[$accept]) ? $accept : 'en';
}
$t = $i18n[$lang];
$otherLang = $lang === 'fr' ? 'en' : 'fr';

/* ---------------------------------------------------------------------
 * Helpers
 * ------------------------------------------------------------------- */
function e(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function text_length(string $s): int
{
    return function_exists('mb_strlen') ? mb_strlen($s, 'UTF-8') : strlen($s);
}

/** Single-line value: no control characters (blocks e-mail header injection). */
function one_line(string $s, int $max): string
{
    $s = trim((string) preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $s));
    return function_exists('mb_substr') ? mb_substr($s, 0, $max, 'UTF-8') : substr($s, 0, $max);
}

function post_string(string $key): string
{
    $v = $_POST[$key] ?? '';
    return is_string($v) ? $v : '';
}

/** Secret used to sign the form's time stamp. Created once, never committed. */
function app_secret(): string
{
    $file = __DIR__ . '/.logoutall-secret.php';
    if (is_file($file)) {
        $secret = include $file;
        if (is_string($secret) && $secret !== '') {
            return $secret;
        }
    }
    $secret = bin2hex(random_bytes(32));
    if (@file_put_contents($file, "<?php return '" . $secret . "';\n", LOCK_EX) !== false) {
        return $secret;
    }
    // Folder not writable: weaker but stable fallback.
    return hash('sha256', __FILE__ . php_uname() . PHP_VERSION);
}

function form_token(): string
{
    $ts = (string) time();
    return $ts . '.' . hash_hmac('sha256', $ts, app_secret());
}

/** @return string '' if valid, otherwise 'fast' or 'expired'. */
function check_form_token(string $token): string
{
    $parts = explode('.', $token, 2);
    if (count($parts) !== 2 || !ctype_digit($parts[0])
        || !hash_equals(hash_hmac('sha256', $parts[0], app_secret()), $parts[1])) {
        return 'expired';
    }
    $age = time() - (int) $parts[0];
    if ($age < FORM_MIN_SECONDS) {
        return 'fast';
    }
    return $age > FORM_MAX_SECONDS ? 'expired' : '';
}

/* ---------------------------------------------------------------------
 * Contact form handling
 * ------------------------------------------------------------------- */
$contact = ['name' => '', 'email' => '', 'message' => ''];
$contactErrors = [];
$contactSent = isset($_GET['sent']);

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $contact['name']    = one_line(post_string('name'), 100);
    $contact['email']   = one_line(post_string('email'), 254);
    $contact['message'] = trim(str_replace(["\r\n", "\r"], "\n", post_string('message')));

    $isRobot = post_string('website') !== ''; // honeypot: humans never see this field

    if (!$isRobot) {
        if (filter_var($contact['email'], FILTER_VALIDATE_EMAIL) === false) {
            $contactErrors[] = $t['c_err_email'];
        }
        $len = text_length($contact['message']);
        if ($len < MSG_MIN_LENGTH || $len > MSG_MAX_LENGTH) {
            $contactErrors[] = strtr($t['c_err_msg'], [
                '{min}' => (string) MSG_MIN_LENGTH,
                '{max}' => (string) MSG_MAX_LENGTH,
            ]);
        }
        if (!$contactErrors) {
            $tokenState = check_form_token(post_string('token'));
            if ($tokenState !== '') {
                $contactErrors[] = $t['c_err_' . $tokenState];
            }
        }
        if (!$contactErrors) {
            $subject = '=?UTF-8?B?' . base64_encode($t['c_subject']) . '?=';
            $body = "Name: " . ($contact['name'] !== '' ? $contact['name'] : '-') . "\n"
                  . "E-mail: " . $contact['email'] . "\n"
                  . "Language: " . $lang . "\n\n"
                  . $contact['message'] . "\n";
            $headers = [
                'From: LogoutAll <' . CONTACT_FROM . '>',
                'Reply-To: ' . $contact['email'],
                'MIME-Version: 1.0',
                'Content-Type: text/plain; charset=UTF-8',
                'Content-Transfer-Encoding: 8bit',
            ];
            $ok = @mail(CONTACT_TO, $subject, $body, implode("\r\n", $headers), '-f' . CONTACT_FROM);
            if (!$ok) {
                $contactErrors[] = strtr($t['c_err_send'], ['{email}' => CONTACT_TO]);
            }
        }
    }

    // Post/Redirect/Get: a page refresh must not send the message twice.
    // Robots get the same "sent" answer, so they learn nothing.
    if (!$contactErrors) {
        header('Location: ?lang=' . $lang . '&sent=1#contact', true, 303);
        exit;
    }
}

$jsonFlags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE;
?>
<!DOCTYPE html>
<html lang="<?= e($lang) ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($t['title']) ?></title>
<meta name="description" content="<?= e($t['tagline']) ?>">
<style nonce="<?= e($nonce) ?>">
:root {
    --bg: #f6f7f9; --card: #ffffff; --text: #1b1f24; --muted: #5f6b7a;
    --line: #dfe3e8; --accent: #0b6bcb; --accent-text: #ffffff;
    --ok: #17794a; --warn: #9a5b00; --ok-bg: #e6f4ec; --warn-bg: #fdf1dc;
}
@media (prefers-color-scheme: dark) {
    :root {
        --bg: #14171b; --card: #1d2127; --text: #e8ebef; --muted: #9aa5b1;
        --line: #2f353d; --accent: #5aa9f5; --accent-text: #0d1116;
        --ok: #6fd3a0; --warn: #f0b962; --ok-bg: #173326; --warn-bg: #3a2c12;
    }
}
* { box-sizing: border-box; }
body {
    margin: 0; background: var(--bg); color: var(--text);
    font: 16px/1.5 system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
}
main { max-width: 720px; margin: 0 auto; padding: 24px 16px 48px; }
header { display: flex; justify-content: space-between; align-items: baseline; gap: 12px; }
h1 { font-size: 1.7rem; margin: 0; }
h2 { font-size: 1.25rem; margin: 0 0 4px; }
a { color: var(--accent); }
.tagline { color: var(--muted); margin: 4px 0 16px; }
.notice {
    border: 1px solid var(--line); border-left: 4px solid var(--accent);
    background: var(--card); border-radius: 8px; padding: 12px 14px; margin-bottom: 20px;
}
.card { background: var(--card); border: 1px solid var(--line); border-radius: 10px; }
.row {
    display: flex; align-items: center; gap: 12px; padding: 12px 14px;
    border-top: 1px solid var(--line); flex-wrap: wrap;
}
.row:first-child { border-top: 0; }
.row.master { font-weight: 600; }
.row label { display: flex; align-items: center; gap: 10px; flex: 1 1 220px; cursor: pointer; }
input[type=checkbox] { width: 20px; height: 20px; accent-color: var(--accent); flex: none; }
.badge { font-size: .78rem; padding: 2px 8px; border-radius: 999px; white-space: nowrap; }
.badge.direct { color: var(--ok); background: var(--ok-bg); }
.badge.manual { color: var(--warn); background: var(--warn-bg); }
.actions { display: flex; gap: 12px; align-items: center; flex-wrap: wrap; margin: 18px 0; }
button, .btn {
    font: inherit; border-radius: 8px; padding: 10px 16px; cursor: pointer;
    border: 1px solid var(--accent); background: var(--accent); color: var(--accent-text);
    text-decoration: none; display: inline-block;
}
button.secondary, .btn.secondary { background: transparent; color: var(--accent); }
.btn.small { padding: 6px 12px; font-size: .9rem; }
button:disabled { opacity: .5; cursor: default; }
.name { font-weight: 600; }
.sub { font-size: .85rem; color: var(--muted); }
.info { flex: 1 1 220px; }
.row.is-done .name { text-decoration: line-through; color: var(--muted); }
.status { font-weight: 600; }
.status.ok { color: var(--ok); }
.error { color: var(--warn); }
#contact { margin-top: 40px; }
.form { padding: 16px 14px; }
.field { margin-bottom: 14px; }
.field label { display: block; font-weight: 600; margin-bottom: 4px; }
.field input, .field textarea {
    width: 100%; font: inherit; color: var(--text); background: var(--bg);
    border: 1px solid var(--line); border-radius: 8px; padding: 10px 12px;
}
.field textarea { min-height: 140px; resize: vertical; }
.field input:focus, .field textarea:focus { outline: 2px solid var(--accent); outline-offset: 1px; }
.hp { position: absolute; left: -9999px; width: 1px; height: 1px; overflow: hidden; }
.alert { border-radius: 8px; padding: 10px 14px; margin: 0 0 14px; }
.alert.ok { color: var(--ok); background: var(--ok-bg); }
.alert.ko { color: var(--warn); background: var(--warn-bg); }
.alert ul { margin: 0; padding-left: 18px; }
footer { margin-top: 32px; color: var(--muted); font-size: .85rem; text-align: center; }
[hidden] { display: none !important; }
</style>
</head>
<body>
<main>
    <header>
        <h1>LogoutAll</h1>
        <a href="?lang=<?= e($otherLang) ?>"><?= e($t['other_lang']) ?></a>
    </header>
    <p class="tagline"><?= e($t['tagline']) ?></p>
    <p class="notice"><?= e($t['notice']) ?></p>

    <!-- Step 1: choose the sites -->
    <section id="step1">
        <div class="card">
            <div class="row master">
                <label>
                    <input type="checkbox" id="selectAll">
                    <span><?= e($t['select_all']) ?></span>
                </label>
            </div>
            <?php foreach ($sites as $s): ?>
            <div class="row">
                <label>
                    <input type="checkbox" class="pick" value="<?= e($s['id']) ?>">
                    <span class="name"><?= e($s['name']) ?></span>
                </label>
                <span class="badge <?= e($s['type']) ?>"><?= e($t[$s['type']]) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="actions">
            <button type="button" id="start"><?= e($t['start']) ?></button>
            <span class="error" id="pickError" role="alert" hidden><?= e($t['none']) ?></span>
        </div>
    </section>

    <!-- Step 2: sign out, one click at a time -->
    <section id="step2" hidden>
        <h2><?= e($t['step2']) ?></h2>
        <p class="tagline"><?= e($t['step2_help']) ?></p>
        <div class="actions">
            <button type="button" id="openNext"><?= e($t['open_next']) ?></button>
            <span class="status" id="summary" role="status" aria-live="polite"></span>
        </div>
        <div class="card" id="queue"></div>
        <div class="actions">
            <button type="button" class="secondary" id="back"><?= e($t['back']) ?></button>
        </div>
    </section>

    <!-- Contact -->
    <section id="contact">
        <h2><?= e($t['c_title']) ?></h2>
        <p class="tagline"><?= e($t['c_intro']) ?></p>

        <?php if ($contactSent && !$contactErrors): ?>
        <p class="alert ok" role="status"><?= e($t['c_sent']) ?></p>
        <?php endif; ?>

        <?php if ($contactErrors): ?>
        <div class="alert ko" role="alert">
            <ul>
                <?php foreach ($contactErrors as $err): ?>
                <li><?= e($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form class="card form" method="post" action="?lang=<?= e($lang) ?>#contact">
            <div class="field">
                <label for="c_name"><?= e($t['c_name']) ?></label>
                <input type="text" id="c_name" name="name" maxlength="100"
                       autocomplete="name" value="<?= e($contact['name']) ?>">
            </div>
            <div class="field">
                <label for="c_email"><?= e($t['c_email']) ?></label>
                <input type="email" id="c_email" name="email" maxlength="254" required
                       autocomplete="email" value="<?= e($contact['email']) ?>">
            </div>
            <div class="field">
                <label for="c_message"><?= e($t['c_message']) ?></label>
                <textarea id="c_message" name="message" required
                          minlength="<?= MSG_MIN_LENGTH ?>" maxlength="<?= MSG_MAX_LENGTH ?>"><?= e($contact['message']) ?></textarea>
            </div>
            <!-- Honeypot: hidden from humans, filled in by robots -->
            <div class="hp" aria-hidden="true">
                <label for="c_website">Website</label>
                <input type="text" id="c_website" name="website" tabindex="-1" autocomplete="off">
            </div>
            <input type="hidden" name="token" value="<?= e(form_token()) ?>">
            <button type="submit"><?= e($t['c_send']) ?></button>
            <p class="sub"><?= e($t['c_privacy']) ?></p>
        </form>
    </section>

    <footer>
        <?= e(COPYRIGHT) ?> · <?= e($t['footer']) ?> ·
        <a href="<?= e(GITHUB_URL) ?>" target="_blank" rel="noopener"><?= e($t['footer_src']) ?></a>
    </footer>
</main>

<script nonce="<?= e($nonce) ?>">
(function () {
    'use strict';

    var SITES = <?= json_encode($sites, $jsonFlags) ?>;
    var T = <?= json_encode($t, $jsonFlags) ?>;

    var step1 = document.getElementById('step1');
    var step2 = document.getElementById('step2');
    var selectAll = document.getElementById('selectAll');
    var picks = Array.prototype.slice.call(document.querySelectorAll('.pick'));
    var pickError = document.getElementById('pickError');
    var queueEl = document.getElementById('queue');
    var summary = document.getElementById('summary');
    var openNext = document.getElementById('openNext');

    // Current queue: [{site, opened, done, row, openLink, doneBox}]
    var queue = [];

    /* ---------- Step 1 ---------- */

    function syncMaster() {
        var checked = picks.filter(function (p) { return p.checked; }).length;
        selectAll.checked = checked === picks.length;
        selectAll.indeterminate = checked > 0 && checked < picks.length;
        if (checked > 0) { pickError.hidden = true; }
    }

    selectAll.addEventListener('change', function () {
        picks.forEach(function (p) { p.checked = selectAll.checked; });
        syncMaster();
    });
    picks.forEach(function (p) { p.addEventListener('change', syncMaster); });

    document.getElementById('start').addEventListener('click', function () {
        var ids = picks.filter(function (p) { return p.checked; })
                       .map(function (p) { return p.value; });
        if (ids.length === 0) { pickError.hidden = false; return; }
        buildQueue(SITES.filter(function (s) { return ids.indexOf(s.id) !== -1; }));
        step1.hidden = true;
        step2.hidden = false;
        window.scrollTo(0, 0);
    });

    document.getElementById('back').addEventListener('click', function () {
        step2.hidden = true;
        step1.hidden = false;
        window.scrollTo(0, 0);
    });

    /* ---------- Step 2 ---------- */

    function el(tag, className, text) {
        var node = document.createElement(tag);
        if (className) { node.className = className; }
        if (text) { node.textContent = text; }
        return node;
    }

    function externalLink(url, text, className) {
        var a = el('a', className, text);
        a.href = url;
        a.target = '_blank';
        a.rel = 'noopener noreferrer';
        return a;
    }

    function buildQueue(selected) {
        queue = [];
        queueEl.textContent = '';

        selected.forEach(function (site) {
            var item = { site: site, opened: false, done: false };

            var row = el('div', 'row');
            var info = el('div', 'info');
            info.appendChild(el('div', 'name', site.name));
            info.appendChild(el('div', 'sub', T[site.type]));
            if (site.sessions) {
                var sub = el('div', 'sub');
                sub.appendChild(externalLink(site.sessions, T.sessions, ''));
                info.appendChild(sub);
            }
            row.appendChild(info);

            var openLink = externalLink(site.url, T.open, 'btn small');
            openLink.addEventListener('click', function () { markOpened(item); });
            row.appendChild(openLink);

            var doneLabel = el('label', '');
            var doneBox = document.createElement('input');
            doneBox.type = 'checkbox';
            doneBox.addEventListener('change', function () {
                item.done = doneBox.checked;
                row.classList.toggle('is-done', item.done);
                refresh();
            });
            doneLabel.appendChild(doneBox);
            doneLabel.appendChild(el('span', '', T.done));
            row.appendChild(doneLabel);

            item.row = row;
            item.openLink = openLink;
            item.doneBox = doneBox;
            queue.push(item);
            queueEl.appendChild(row);
        });

        refresh();
    }

    function markOpened(item) {
        item.opened = true;
        item.openLink.textContent = T.opened;
        item.openLink.classList.add('secondary');
        refresh();
    }

    function nextToOpen() {
        for (var i = 0; i < queue.length; i++) {
            if (!queue[i].opened && !queue[i].done) { return queue[i]; }
        }
        return null;
    }

    // One site per click: browsers only allow one new tab per user gesture.
    openNext.addEventListener('click', function () {
        var item = nextToOpen();
        if (!item) { return; }
        window.open(item.site.url, '_blank', 'noopener,noreferrer');
        markOpened(item);
        item.row.scrollIntoView({ block: 'nearest' });
    });

    function refresh() {
        var remaining = queue.filter(function (q) { return !q.done; }).length;
        if (remaining === 0) {
            summary.textContent = T.all_done;
            summary.className = 'status ok';
        } else {
            summary.textContent = T.remaining.replace('{n}', String(remaining));
            summary.className = 'status';
        }
        openNext.disabled = nextToOpen() === null;
    }
})();
</script>
</body>
</html>
