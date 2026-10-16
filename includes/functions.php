<?php
require_once __DIR__ . '/../config.php';

session_start();

// sichere Ausgabe
function e($text)
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// 9.5 -> 9,50 €
function preis($betrag)
{
    return number_format($betrag, 2, ',', '.') . ' €';
}

// Meldung für die nächste Seite merken
function meldung($typ, $text)
{
    $_SESSION['meldung'] = ['typ' => $typ, 'text' => $text];
}

// CSRF-Schutz
function csrf_token()
{
    if (empty($_SESSION['token'])) {
        $_SESSION['token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['token'];
}

function csrf_pruefen()
{
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
        die('Ungültige Anfrage.');
    }
}

// Seiten für das Restaurant nur mit Login
function login_pruefen()
{
    if (empty($_SESSION['login'])) {
        header('Location: login.php');
        exit;
    }
}

function warenkorb_anzahl()
{
    if (empty($_SESSION['warenkorb'])) {
        return 0;
    }
    return array_sum($_SESSION['warenkorb']);
}

// Bild vom Gericht oder Platzhalter
function bild($datei)
{
    if ($datei != '' && file_exists(BILDER_ORDNER . $datei)) {
        return BILDER_ORDNER . $datei;
    }
    return PLATZHALTER;
}