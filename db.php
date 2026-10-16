<?php
// ============================================================
// db.php – Verbindung zur Datenbank mit PDO
// Danach steht in allen Seiten die Variable $pdo zur Verfügung.
// ============================================================

require_once __DIR__ . '/config.php';

// DSN = "Adresse" der Datenbank: Typ, Server, Datenbankname, Zeichensatz
$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';

$optionen = [
    // Bei einem Fehler wirft PDO eine Exception (die wir mit catch abfangen)
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    // Ergebnisse als Array mit Spaltennamen, z. B. $zeile['name']
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // Echte Prepared Statements der Datenbank verwenden (Schutz vor SQL-Injection)
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, DB_BENUTZER, DB_PASSWORT, $optionen);
} catch (PDOException $e) {
    // Fehler ins PHP-Fehlerprotokoll schreiben
    error_log('Datenbankfehler: ' . $e->getMessage());

    http_response_code(500);

    if (DEBUG) {
        // Nur beim Programmieren: technische Ursache anzeigen (ohne Passwort)
        echo '<p style="color:#b00020">Keine Verbindung zur Datenbank: '
            . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</p>';
    } else {
        // Für normale Besucher: keine technischen Details
        echo '<p style="color:#b00020">Die Seite ist gerade nicht erreichbar. Bitte später erneut versuchen.</p>';
    }
    exit;
}