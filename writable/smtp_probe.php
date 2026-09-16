<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "fsockopen mailhog:1025 => ";
$fp = @fsockopen('mailhog', 1025, $errno, $errstr, 5);
echo $fp ? "OK\n" : "FAIL $errno $errstr\n";
if ($fp) {
    fclose($fp);
}

echo "getenv email.SMTPHost=" . var_export(getenv('email.SMTPHost'), true) . "\n";
echo "getenv EMAIL_SMTPHOST=" . var_export(getenv('email.SMTPHost'), true) . "\n";
