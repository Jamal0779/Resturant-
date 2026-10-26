<?php
require_once 'db.php';
require_once 'includes/functions.php';

// nur für den Ladenbesitzer
login_pruefen();

// Status ändern
if (isset($_POST['status'])) {
    $status = $_POST['status'];
    $bestellnummer = $_POST['bestellnummer'];

    if (in_array($status, STATUS_LISTE)) {
        $aendern = $pdo->prepare('UPDATE bestellungen SET status = ? WHERE bestellnummer = ?');
        $aendern->execute([$status, $bestellnummer]);

        meldung('erfolg', 'Status von Bestellung ' . $bestellnummer . ' wurde geändert.');
    }

    header('Location: orders.php');
    exit;
}

// alle Zeilen holen, neueste zuerst
$zeilen = $pdo->query('SELECT * FROM bestellungen ORDER BY bestelldatum DESC, id')->fetchAll();

// Zeilen nach Bestellnummer sortieren: eine Bestellung = mehrere Gerichte
$bestellungen = [];
foreach ($zeilen as $zeile) {
    $bestellungen[$zeile['bestellnummer']][] = $zeile;
}

require_once 'includes/header.php';
?>

<h2>Bestellungen</h2>

<?php if (empty($bestellungen)): ?>
    <p>Es gibt noch keine Bestellungen.</p>
<?php endif; ?>

<?php foreach ($bestellungen as $nummer => $positionen): ?>
    <?php $kunde = $positionen[0]; ?>

    <div class="bestellung">
        <h3>Bestellung <?= $nummer ?></h3>

        <p>
            <?= date('d.m.Y H:i', strtotime($kunde['bestelldatum'])) ?><br>
            <b><?= e($kunde['kundenname']) ?></b>, <?= e($kunde['lieferadresse']) ?><br>
            Telefon: <?= e($kunde['telefon']) ?><br>
            Zahlungsart: <?= e($kunde['zahlungsart']) ?>
        </p>

        <table>
            <tr>
                <th>Gericht</th>
                <th>Menge</th>
                <th>Summe</th>
            </tr>

            <?php $gesamt = 0; ?>

            <?php foreach ($positionen as $position): ?>
                <?php
                $summe = $position['einzelpreis'] * $position['menge'];
                $gesamt = $gesamt + $summe;
                ?>
                <tr>
                    <td><?= e($position['gerichtname']) ?></td>
                    <td><?= $position['menge'] ?></td>
                    <td><?= preis($summe) ?></td>
                </tr>
            <?php endforeach; ?>

            <tr>
                <th colspan="2">Gesamt</th>
                <th><?= preis($gesamt) ?></th>
            </tr>
        </table>

        <form method="post" class="status">
            <input type="hidden" name="bestellnummer" value="<?= $nummer ?>">

            <label>Status:</label>
            <select name="status">
                <?php foreach (STATUS_LISTE as $s): ?>
                    <option value="<?= $s ?>" <?= $s == $kunde['status'] ? 'selected' : '' ?>>
                        <?= str_replace('_', ' ', $s) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button>Speichern</button>
            <a href="beleg.php?token=<?= $kunde['beleg_token'] ?>" class="button">Beleg</a>
        </form>
    </div>
<?php endforeach; ?>

<?php require_once 'includes/footer.php'; ?>