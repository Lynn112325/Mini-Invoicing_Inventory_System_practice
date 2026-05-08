<?php
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];;

$script_name = $_SERVER['SCRIPT_NAME'];
$path_parts = explode('/', trim($script_name, '/'));
$base_path = '/' . $path_parts[0];
define('BASE_URL', $protocol . "://" . $host . $base_path . "/");
