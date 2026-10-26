<?php
require_once 'db.php';
require_once 'includes/functions.php';

$fehler = '';

// eingeloggter Kunde: Felder schon ausfüllen
$kunde = ['name' => '', 'strasse' => '', 'hausnummer' => '', 'plz' => '', 'ort' => '', 'telefon' => ''];
if (isset($_SESSION['kunde'])) {
    $kunde = $_SESSION['kunde'];
}

// Formular wurde abgeschickt
if (isset($_POST['name'])) {
    $name = trim($_POST['name']);
    $strasse = trim($_POST['strasse']);
    $hausnummer = trim($_POST['hausnummer']);
    $plz = trim($_POST['plz']);
    $ort = trim($_POST['ort']);
    $telefon = trim($_POST['telefon']);
    $zahlungsart = $_POST['zahlungsart'];

    // Adresse zusammensetzen, z. B. "Hauptstraße 12, 10115 Berlin"
    $adresse = $strasse . ' ' . $hausnummer . ', ' . $plz . ' ' . $ort;

    if ($name == '' || $strasse == '' || $hausnummer == '' || $plz == '' || $ort == '' || $telefon == '') {
        $fehler = 'Bitte alle Felder ausfüllen.';
    } elseif ($zahlungsart != 'Bar bei Lieferung' && $zahlungsart != 'Karte online') {
        $fehler = 'Bitte eine Zahlungsart auswählen.';
    } elseif (empty($_SESSION['warenkorb'])) {
        $fehler = 'Dein Warenkorb ist leer.';
    } else {
        $daten = ['name' => $name, 'adresse' => $adresse, 'telefon' => $telefon];

        if ($zahlungsart == 'Bar bei Lieferung') {
            bestellung_speichern($pdo, $daten, 'Bar bei Lieferung');
        }

        // Karte: Daten merken und zur Bezahlseite
        $_SESSION['bestellung'] = $daten;
        header('Location: bezahlen.php');
        exit;
    }
}

require_once 'includes/header.php';
?>

<?php if (empty($_SESSION['warenkorb'])): ?>

    <p>Dein Warenkorb ist leer.</p>
    <a href="index.php" class="button">Zur Speisekarte</a>

<?php else: ?>

    <h2>Bestellen</h2>

    <?php if ($fehler != ''): ?>
        <p class="meldung fehler"><?= $fehler ?></p>
    <?php endif; ?>

    <form method="post" class="formular">
        <label>Name</label>
        <input type="text" name="name" value="<?= e($kunde['name']) ?>">

        <label>Straße</label>
        <input type="text" name="strasse" value="<?= e($kunde['strasse']) ?>">

        <label>Hausnummer</label>
        <input type="text" name="hausnummer" value="<?= e($kunde['hausnummer']) ?>">

        <label>PLZ</label>
        <input type="text" name="plz" maxlength="5" value="<?= e($kunde['plz']) ?>">

        <label>Ort</label>
        <input type="text" name="ort" value="<?= e($kunde['ort']) ?>">

        <label>Telefon</label>
        <input type="text" name="telefon" value="<?= e($kunde['telefon']) ?>">

        <label>Zahlungsart</label>
        <select name="zahlungsart">
            <option>Bar bei Lieferung</option>
            <option>Karte online</option>
        </select>

        <br><br>
        <button>Weiter</button>
    </form>

<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>