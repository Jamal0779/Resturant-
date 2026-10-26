<?php
require_once __DIR__ . '/../config.php';

session_start();

// sichere Ausgabe
function e($text)
{
    return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
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

// Bestellung speichern, Warenkorb leeren und zum Beleg gehen
function bestellung_speichern($pdo, $kunde, $zahlungsart)
{
    $bestellnummer = date('Ymd') . '-' . rand(1000, 9999);
    $token = bin2hex(random_bytes(16));

    // jedes Gericht als eigene Zeile speichern
    foreach ($_SESSION['warenkorb'] as $id => $menge) {
        $abfrage = $pdo->prepare('SELECT * FROM gerichte WHERE id = ?');
        $abfrage->execute([$id]);
        $gericht = $abfrage->fetch();

        $speichern = $pdo->prepare('INSERT INTO bestellungen
            (bestellnummer, beleg_token, gericht_id, gerichtname, einzelpreis, menge, kundenname, lieferadresse, telefon, zahlungsart)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $speichern->execute([$bestellnummer, $token, $id, $gericht['name'], $gericht['preis'], $menge, $kunde['name'], $kunde['adresse'], $kunde['telefon'], $zahlungsart]);
    }

    $_SESSION['warenkorb'] = [];
    unset($_SESSION['bestellung']);

    // merken, damit der Kunde den Status auf jeder Seite sieht
    $_SESSION['letzte_bestellung'] = $token;
    unset($_SESSION['fertig_seit']);

    header('Location: beleg.php?token=' . $token);
    exit;
}

// Status als Text für den Kunden
function status_text($status)
{
    $texte = [
        'neu' => 'Bestellung ist eingegangen',
        'in_zubereitung' => 'Dein Essen wird zubereitet',
        'unterwegs' => 'Dein Essen ist unterwegs zu dir',
        'geliefert' => 'Geliefert – guten Appetit!',
        'storniert' => 'Bestellung wurde storniert'
    ];
    return $texte[$status];
}

// Gesamtpreis vom Warenkorb
function warenkorb_summe($pdo)
{
    $gesamt = 0;

    foreach ($_SESSION['warenkorb'] as $id => $menge) {
        $abfrage = $pdo->prepare('SELECT preis FROM gerichte WHERE id = ?');
        $abfrage->execute([$id]);
        $gesamt = $gesamt + $abfrage->fetchColumn() * $menge;
    }

    return $gesamt;
}