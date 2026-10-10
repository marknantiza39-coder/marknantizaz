<?php
declare(strict_types=1);

/* ---- settings: edit these ---- */
const TO_EMAIL        = 'marknantiza39@gmail.com';      // where messages are delivered
const FROM_EMAIL      = 'no-reply@yourdomain.com';      // must be an address on YOUR hosting domain, or mail may be blocked
const SITE_NAME       = 'Mark Nantiza Portfolio';
const ALLOWED_ORIGINS = [];                             // e.g. ['https://your-username.github.io'] if the page lives on GitHub Pages. Empty = same-site only
const MIN_SECONDS     = 3;                              // forms submitted faster than this are treated as bots
const COOLDOWN        = 60;                             // seconds a visitor must wait between messages
/* ------------------------------ */

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');

function respond(int $code, array $body): void
{
    http_response_code($code);
    echo json_encode($body);
    exit;
}

function text_len(string $s): int
{
    return function_exists('mb_strlen') ? mb_strlen($s, 'UTF-8') : strlen($s);
}

// 1. Only accept POST
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    respond(405, ['ok' => false, 'error' => 'Method not allowed.']);
}

// 2. Cross-site requests: only allow the origins you list
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin !== '') {
    if (in_array($origin, ALLOWED_ORIGINS, true)) {
        header('Access-Control-Allow-Origin: ' . $origin);
        header('Vary: Origin');
    } else {
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $originHost = parse_url($origin, PHP_URL_HOST) ?: '';
        if ($originHost === '' || stripos($host, $originHost) !== 0) {
            respond(403, ['ok' => false, 'error' => 'Request not allowed.']);
        }
    }
}

// 3. Honeypot: real visitors never see or fill this field. Pretend success to bots.
if (!empty($_POST['website'])) {
    respond(200, ['ok' => true]);
}

// 4. Too-fast submissions are almost always bots
$ts = (int) ($_POST['ts'] ?? 0);
if ($ts > 0 && (microtime(true) * 1000 - $ts) < MIN_SECONDS * 1000) {
    respond(429, ['ok' => false, 'error' => 'Please wait a moment and try again.']);
}

// 5. Simple rate limit per visitor (one message per COOLDOWN seconds)
$ip   = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$lock = sys_get_temp_dir() . '/portfolio_' . hash('sha256', $ip) . '.t';
if (is_file($lock) && time() - (int) @filemtime($lock) < COOLDOWN) {
    respond(429, ['ok' => false, 'error' => 'You already sent a message. Please wait a minute.']);
}

// 6. Read and validate input
$name    = trim((string) ($_POST['name'] ?? ''));
$email   = trim((string) ($_POST['email'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));

if (preg_match('/[\r\n]/', $name . $email)) {          // blocks email header injection
    respond(400, ['ok' => false, 'error' => 'Invalid characters in name or email.']);
}
if ($name === '' || text_len($name) > 80) {
    respond(400, ['ok' => false, 'error' => 'Please enter your name (up to 80 characters).']);
}
if (strlen($email) > 254 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(400, ['ok' => false, 'error' => 'Please enter a valid email address.']);
}
if (text_len($message) < 10 || text_len($message) > 1000) {
    respond(400, ['ok' => false, 'error' => 'Your message must be 10 to 1000 characters.']);
}

// 7. Build and send the email
$subject = 'Portfolio message from ' . $name;
$subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
$body    = "Name: {$name}\nEmail: {$email}\n\n{$message}\n";
$headers = [
    'From'         => SITE_NAME . ' <' . FROM_EMAIL . '>',
    'Reply-To'     => $email,
    'Content-Type' => 'text/plain; charset=UTF-8',
    'X-Mailer'     => 'PHP/' . PHP_VERSION,
];

if (!mail(TO_EMAIL, $subject, $body, $headers)) {
    respond(500, ['ok' => false, 'error' => 'Could not send your message right now. Please email me directly.']);
}

@touch($lock);
respond(200, ['ok' => true]);
