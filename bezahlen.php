<?php
require_once 'db.php';
require_once 'includes/functions.php';

// ohne Bestellung oder Warenkorb zurück zum Warenkorb
if (empty($_SESSION['bestellung']) || empty($_SESSION['warenkorb'])) {
    header('Location: cart.php');
    exit;
}

$fehler = '';

// Formular wurde abgeschickt
if (isset($_POST['nummer'])) {
    $inhaber = trim($_POST['inhaber']);
    $nummer = str_replace(' ', '', $_POST['nummer']);
    $ablauf = trim($_POST['ablauf']);
    $cvc = trim($_POST['cvc']);

    // Ablaufdatum "MM/JJ" in Monat und Jahr teilen
    $teile = explode('/', $ablauf);

    if ($inhaber == '') {
        $fehler = 'Bitte den Namen auf der Karte eingeben.';
    } elseif (strlen($nummer) != 16 || !ctype_digit($nummer)) {
        $fehler = 'Die Kartennummer muss 16 Ziffern haben.';
    } elseif (count($teile) != 2 || $teile[0] < 1 || $teile[0] > 12) {
        $fehler = 'Bitte das Ablaufdatum so eingeben: MM/JJ';
    } elseif ('20' . $teile[1] . str_pad($teile[0], 2, '0', STR_PAD_LEFT) < date('Ym')) {
        $fehler = 'Die Karte ist abgelaufen.';
    } elseif (strlen($cvc) != 3 || !ctype_digit($cvc)) {
        $fehler = 'Die CVC muss 3 Ziffern haben.';
    } else {
        // Zahlung ist "durch" – Kartendaten werden NICHT gespeichert
        bestellung_speichern($pdo, $_SESSION['bestellung'], 'Karte online (bezahlt)');
    }
}

require_once 'includes/header.php';
?>

<h2>Mit Karte bezahlen</h2>

<p>Zu zahlen: <b class="preis"><?= preis(warenkorb_summe($pdo)) ?></b></p>

<?php if ($fehler != ''): ?>
    <p class="meldung fehler"><?= $fehler ?></p>
<?php endif; ?>

<form method="post" class="formular">
    <label>Name auf der Karte</label>
    <input type="text" name="inhaber">

    <label>Kartennummer</label>
    <input type="text" name="nummer" maxlength="19" placeholder="1234 5678 9012 3456">

    <label>Gültig bis</label>
    <input type="text" name="ablauf" maxlength="5" placeholder="MM/JJ">

    <label>CVC</label>
    <input type="password" name="cvc" maxlength="3" placeholder="123">

    <br><br>
    <button>Jetzt bezahlen</button>
</form>

<p class="klein">Testprojekt: Es wird kein echtes Geld abgebucht und keine Kartendaten gespeichert.</p>

<?php require_once 'includes/footer.php'; ?>