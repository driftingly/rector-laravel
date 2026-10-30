<?php

// files returning a value, like config/*.php, are loaded before the Request facade is available

$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

return [
    'url' => 'https://' . $host,
    'proxies' => $_SERVER['TRUSTED_PROXIES'] ?? '*',
];
