<?php
require_once 'db.php';
require_once 'includes/functions.php';

$fehler = '';

// Formular wurde abgeschickt
if (isset($_POST['email'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $passwort = $_POST['passwort'];
    $strasse = trim($_POST['strasse']);
    $hausnummer = trim($_POST['hausnummer']);
    $plz = trim($_POST['plz']);
    $ort = trim($_POST['ort']);
    $telefon = trim($_POST['telefon']);

    // gibt es die E-Mail schon?
    $abfrage = $pdo->prepare('SELECT * FROM kunden WHERE email = ?');
    $abfrage->execute([$email]);
    $vorhanden = $abfrage->fetch();

    if ($name == '' || $email == '' || $strasse == '' || $hausnummer == '' || $plz == '' || $ort == '' || $telefon == '') {
        $fehler = 'Bitte alle Felder ausfüllen.';
    } elseif (strlen($passwort) < 6) {
        $fehler = 'Das Passwort muss mindestens 6 Zeichen haben.';
    } elseif ($vorhanden) {
        $fehler = 'Mit dieser E-Mail gibt es schon ein Konto.';
    } else {
        $hash = password_hash($passwort, PASSWORD_DEFAULT);

        $speichern = $pdo->prepare('INSERT INTO kunden (name, email, passwort, strasse, hausnummer, plz, ort, telefon)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $speichern->execute([$name, $email, $hash, $strasse, $hausnummer, $plz, $ort, $telefon]);

        meldung('erfolg', 'Dein Konto wurde angelegt. Du kannst dich jetzt einloggen.');
        header('Location: login.php');
        exit;
    }
}

require_once 'includes/header.php';
?>

<h2>Registrieren</h2>

<?php if ($fehler != ''): ?>
    <p class="meldung fehler"><?= $fehler ?></p>
<?php endif; ?>

<form method="post" class="formular">
    <label>Name</label>
    <input type="text" name="name">

    <label>E-Mail</label>
    <input type="email" name="email">

    <label>Passwort (mindestens 6 Zeichen)</label>
    <input type="password" name="passwort">

    <label>Straße</label>
    <input type="text" name="strasse">

    <label>Hausnummer</label>
    <input type="text" name="hausnummer">

    <label>PLZ</label>
    <input type="text" name="plz" maxlength="5">

    <label>Ort</label>
    <input type="text" name="ort">

    <label>Telefon</label>
    <input type="text" name="telefon">

    <br><br>
    <button>Konto anlegen</button>
</form>

<p>Schon ein Konto? <a href="login.php">Zum Login</a></p>

<?php require_once 'includes/footer.php'; ?>