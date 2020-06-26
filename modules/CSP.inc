<?php
// CSP-header settings to add a bit of additional security and minimize
// data leaking.
// M. Oettinger 06/2020
//
// create a nonce for one-time use in a global variable as function.php
// uses inline javascript code for graph URL and the shortening
// (kind of a bad hack, but still much better than using unsafe-inline 
// in CSP headers!)
//
$GLOBALS['plotternonce'] = base64_encode(random_bytes(20));
// 
header_remove("X-Powered-By");
//
//
// CSP used to minimize Cross Site Scripting (XSS) and Data Injection attacks
//
$domain = preg_replace('#^https?://#', '', $fqdn);
$CSPRules = "default-src 'none' "
           .";  base-uri 'none'" 
           ."; img-src 'self' data: " 
           ."; form-action 'self'"
           ."; style-src 'self'"
           ."; worker-src 'self'"
           ."; frame-src 'self'"
           ."; frame-ancestors 'self'"
           ."; script-src 'self' 'nonce-" . $GLOBALS['plotternonce'] . "'";
header("Content-Security-Policy: " . $CSPRules);
header("X-Content-Security-Policy: " . $CSPRules);
header("X-WebKit-CSP: " . $CSPRules);
//
// HTTP Strict Transport Security (HSTS)
//
header("Strict-Transport-Security: max-age=31536000;includeSubDomains; preload");
//
// Referrer Policy - no referrers!
//
header("Referrer-Policy: no-referrer");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: sameorigin");
header("Feature-Policy: camera 'none'; microphone 'none'; geolocation 'none'; payment 'none'");

//
// (re-)enable XSS protection
//
header("X-XSS-Protection: 1; mode=block");
?>
