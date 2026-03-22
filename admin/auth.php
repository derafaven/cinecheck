<?php
session_start();
function isLoggedIn(): bool { return !empty($_SESSION['cc_admin']); }
function requireLogin(): void { if (!isLoggedIn()) { header('Location: index.php'); exit; } }
function getAuth(): array { return json_decode(file_get_contents(__DIR__ . '/../config/auth.json'), true) ?? []; }
function saveAuth(array $data): void { file_put_contents(__DIR__ . '/../config/auth.json', json_encode($data, JSON_PRETTY_PRINT)); }
