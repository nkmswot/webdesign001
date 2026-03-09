<?php
/**
 * contact.php — WebDesign001 Contact Form Handler
 * Place this file in the root of your server alongside contact.html
 * Sends submissions to contact@webdesign001.net via PHP mail()
 */

/* ── Config ─────────────────────────────────── */
define('RECIPIENT',  'contact@webdesign001.net');
define('FROM_DOMAIN','webdesign001.net');
define('RATE_LIMIT',  3);          // max submissions per IP per hour
define('RATE_WINDOW', 3600);       // seconds (1 hour)

/* ── CORS / Headers ──────────────────────────── */
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

// Only accept POST from same origin
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['success' => false, 'error' => 'Method not allowed.']));
}

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowed = ['https://webdesign001.net', 'https://www.webdesign001.net'];
if (!in_array($origin, $allowed, true)) {
    // Also allow empty origin (direct curl / server-side tests)
    if (!empty($origin)) {
        http_response_code(403);
        exit(json_encode(['success' => false, 'error' => 'Forbidden origin.']));
    }
}
if (!empty($origin)) {
    header('Access-Control-Allow-Origin: ' . $origin);
}

/* ── Rate limiting (file-based, no DB needed) ── */
function check_rate_limit(string $ip): bool {
    $dir  = sys_get_temp_dir() . '/wd001_rl/';
    if (!is_dir($dir)) mkdir($dir, 0700, true);
    $file = $dir . md5($ip) . '.json';
    $now  = time();
    $data = [];
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true) ?? [];
    }
    // Remove entries older than window
    $data = array_filter($data, fn($t) => ($now - $t) < RATE_WINDOW);
    $data = array_values($data);
    if (count($data) >= RATE_LIMIT) {
        return false; // rate limited
    }
    $data[] = $now;
    file_put_contents($file, json_encode($data), LOCK_EX);
    return true;
}

$ip = $_SERVER['HTTP_CF_CONNECTING_IP']    // Cloudflare
   ?? $_SERVER['HTTP_X_FORWARDED_FOR']     // load balancer
   ?? $_SERVER['REMOTE_ADDR']
   ?? '0.0.0.0';
$ip = explode(',', $ip)[0]; // take first IP if comma-list

if (!check_rate_limit(trim($ip))) {
    http_response_code(429);
    exit(json_encode([
        'success' => false,
        'error'   => 'Too many requests. Please wait before sending another message.'
    ]));
}

/* ── Parse input ─────────────────────────────── */
$raw = file_get_contents('php://input');
$body = json_decode($raw, true);

// Fallback to form-encoded POST
if (!$body) {
    $body = $_POST;
}

function clean(mixed $v, int $max = 500): string {
    return mb_substr(strip_tags(trim((string)($v ?? ''))), 0, $max);
}

$name    = clean($body['name']    ?? '', 100);
$email   = clean($body['email']   ?? '', 200);
$subject = clean($body['subject'] ?? '', 200);
$message = clean($body['message'] ?? '', 5000);
$honeypot = clean($body['website'] ?? '', 10); // honeypot field

/* ── Validation ──────────────────────────────── */
$errors = [];

if (strlen($name) < 2) {
    $errors[] = 'Name must be at least 2 characters.';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
}
if (strlen($message) < 10) {
    $errors[] = 'Message must be at least 10 characters.';
}
if (!empty($honeypot)) {
    // Silent discard — bots fill the honeypot, humans don't see it
    http_response_code(200);
    exit(json_encode(['success' => true]));
}

if ($errors) {
    http_response_code(422);
    exit(json_encode(['success' => false, 'errors' => $errors]));
}

/* ── Build email ─────────────────────────────── */
$to      = RECIPIENT;
$subLine = $subject
    ? '[WD001 Contact] ' . $subject . ' — from ' . $name
    : '[WD001 Contact] Message from ' . $name;

$safeName  = addslashes($name);
$safeEmail = filter_var($email, FILTER_SANITIZE_EMAIL);
$date      = date('D, d M Y H:i:s T');

$textBody = <<<TEXT
New contact form submission — WebDesign001.net
===============================================

Name:    {$name}
Email:   {$email}
Subject: {$subject}
Date:    {$date}
IP:      {$ip}

Message:
--------
{$message}

---
Sent via contact.html on WebDesign001.net
TEXT;

$htmlBody = '<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"/><title>Contact Form</title></head>
<body style="margin:0;padding:0;background:#f0f4f8;font-family:Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4f8;padding:32px 16px;">
  <tr><td align="center">
    <table width="560" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 16px rgba(0,0,0,.08);">
      <tr>
        <td style="background:linear-gradient(135deg,#6366f1,#8b5cf6);padding:28px 32px;">
          <p style="margin:0;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:rgba(255,255,255,.7);">New Contact Form Submission</p>
          <h1 style="margin:6px 0 0;font-size:22px;font-weight:800;color:#ffffff;">WebDesign001.net</h1>
        </td>
      </tr>
      <tr>
        <td style="padding:28px 32px;">
          <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
              <td style="padding:8px 0;border-bottom:1px solid #e8ecf0;">
                <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;display:block;margin-bottom:3px;">Name</span>
                <span style="font-size:15px;color:#1e293b;font-weight:600;">' . htmlspecialchars($name) . '</span>
              </td>
            </tr>
            <tr>
              <td style="padding:8px 0;border-bottom:1px solid #e8ecf0;">
                <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;display:block;margin-bottom:3px;">Email</span>
                <a href="mailto:' . htmlspecialchars($safeEmail) . '" style="font-size:15px;color:#6366f1;font-weight:600;text-decoration:none;">' . htmlspecialchars($safeEmail) . '</a>
              </td>
            </tr>
            <tr>
              <td style="padding:8px 0;border-bottom:1px solid #e8ecf0;">
                <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;display:block;margin-bottom:3px;">Subject</span>
                <span style="font-size:15px;color:#1e293b;font-weight:600;">' . htmlspecialchars($subject ?: '(no subject)') . '</span>
              </td>
            </tr>
            <tr>
              <td style="padding:8px 0;border-bottom:1px solid #e8ecf0;">
                <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;display:block;margin-bottom:3px;">Date</span>
                <span style="font-size:15px;color:#1e293b;">' . htmlspecialchars($date) . '</span>
              </td>
            </tr>
            <tr>
              <td style="padding:16px 0 0;">
                <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;display:block;margin-bottom:10px;">Message</span>
                <div style="background:#f8fafc;border-left:3px solid #6366f1;border-radius:0 8px 8px 0;padding:16px 20px;font-size:15px;line-height:1.7;color:#334155;white-space:pre-wrap;">' . htmlspecialchars($message) . '</div>
              </td>
            </tr>
          </table>

          <div style="margin-top:24px;padding-top:20px;border-top:1px solid #e8ecf0;">
            <a href="mailto:' . htmlspecialchars($safeEmail) . '?subject=Re: ' . htmlspecialchars($subLine) . '"
               style="display:inline-block;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;font-size:14px;font-weight:700;padding:11px 24px;border-radius:8px;text-decoration:none;">
              &#8617; Reply to ' . htmlspecialchars($name) . '
            </a>
          </div>
        </td>
      </tr>
      <tr>
        <td style="background:#f8fafc;padding:16px 32px;border-top:1px solid #e8ecf0;">
          <p style="margin:0;font-size:12px;color:#94a3b8;">
            Sent from <a href="https://webdesign001.net/contact.html" style="color:#6366f1;">webdesign001.net/contact.html</a>
            &nbsp;|&nbsp; IP: ' . htmlspecialchars($ip) . '
          </p>
        </td>
      </tr>
    </table>
  </td></tr>
</table>
</body></html>';

/* ── Mail headers ────────────────────────────── */
$boundary = '----=_WD001_' . md5(uniqid('', true));
$fromAddr = 'noreply@' . FROM_DOMAIN;
$fromLine = 'WebDesign001 Contact <' . $fromAddr . '>';

$headers  = "From: {$fromLine}\r\n";
$headers .= "Reply-To: {$safeName} <{$safeEmail}>\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n";
$headers .= "X-Priority: 1\r\n";

$mailBody  = "--{$boundary}\r\n";
$mailBody .= "Content-Type: text/plain; charset=UTF-8\r\n";
$mailBody .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
$mailBody .= $textBody . "\r\n\r\n";
$mailBody .= "--{$boundary}\r\n";
$mailBody .= "Content-Type: text/html; charset=UTF-8\r\n";
$mailBody .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
$mailBody .= $htmlBody . "\r\n\r\n";
$mailBody .= "--{$boundary}--";

/* ── Send ────────────────────────────────────── */
$sent = mail($to, $subLine, $mailBody, $headers);

if ($sent) {
    http_response_code(200);
    echo json_encode(['success' => true]);
} else {
    // mail() failed — log it and return 500
    error_log("WD001 contact mail failed: from={$email}, subject={$subLine}");
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Mail could not be sent. Please try again or email us directly at ' . RECIPIENT
    ]);
}
