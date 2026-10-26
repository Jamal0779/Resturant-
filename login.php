<?php
require_once 'db.php';
require_once 'includes/functions.php';

$fehler = '';

// Formular wurde abgeschickt
if (isset($_POST['benutzer'])) {
    $benutzer = trim($_POST['benutzer']);
    $passwort = $_POST['passwort'];

    // 1. ist es das Restaurant (admin)?
    if ($benutzer == ADMIN_USER && password_verify($passwort, ADMIN_HASH)) {
        $_SESSION['login'] = true;

        meldung('erfolg', 'Du bist als Restaurant eingeloggt.');
        header('Location: index.php');
        exit;
    }

    // 2. ist es ein Kunde?
    $abfrage = $pdo->prepare('SELECT * FROM kunden WHERE email = ?');
    $abfrage->execute([$benutzer]);
    $kunde = $abfrage->fetch();

    if ($kunde && password_verify($passwort, $kunde['passwort'])) {
        $_SESSION['kunde'] = $kunde;

        meldung('erfolg', 'Hallo ' . $kunde['name'] . ', du bist eingeloggt.');
        header('Location: index.php');
        exit;
    }

    $fehler = 'E-Mail oder Passwort ist falsch.';
}

require_once 'includes/header.php';
?>

<h2>Login</h2>

<?php if ($fehler != ''): ?>
    <p class="meldung fehler"><?= $fehler ?></p>
<?php endif; ?>

<form method="post" class="formular">
    <label>E-Mail</label>
    <input type="text" name="benutzer">

    <label>Passwort</label>
    <input type="password" name="passwort">

    <br><br>
    <button>Einloggen</button>
</form>

<p>Noch kein Konto? <a href="register.php">Jetzt registrieren</a></p>

<?php require_once 'includes/footer.php'; ?>