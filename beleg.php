<?php
require_once 'db.php';
require_once 'includes/functions.php';

// alle Gerichte dieser Bestellung holen
$abfrage = $pdo->prepare('SELECT * FROM bestellungen WHERE beleg_token = ?');
$abfrage->execute([$_GET['token']]);
$positionen = $abfrage->fetchAll();

require_once 'includes/header.php';
?>

<?php if (empty($positionen)): ?>

    <p>Beleg nicht gefunden.</p>

<?php else: ?>

    <?php $kunde = $positionen[0]; ?>

    <h2>Beleg</h2>
    <p class="nicht-drucken">Danke für deine Bestellung!</p>

    <p>
        <b><?= RESTAURANT_NAME ?></b><br>
        Bestellnummer: <?= $kunde['bestellnummer'] ?><br>
        Datum: <?= date('d.m.Y H:i', strtotime($kunde['bestelldatum'])) ?>
    </p>

    <p>
        <b>Lieferung an:</b><br>
        <?= e($kunde['kundenname']) ?><br>
        <?= e($kunde['lieferadresse']) ?><br>
        Telefon: <?= e($kunde['telefon']) ?>
    </p>

    <table>
        <tr>
            <th>Gericht</th>
            <th>Menge</th>
            <th>Preis</th>
            <th>Summe</th>
        </tr>

        <?php $gesamt = 0; ?>

        <?php foreach ($positionen as $position): ?>
            <?php
            $summe = $position['einzelpreis'] * $position['menge'];
            $gesamt = $gesamt + $summe;
            ?>
            <tr>
                <td><?= $position['gerichtname'] ?></td>
                <td><?= $position['menge'] ?></td>
                <td><?= preis($position['einzelpreis']) ?></td>
                <td><?= preis($summe) ?></td>
            </tr>
        <?php endforeach; ?>

        <tr>
            <th colspan="3">Gesamt</th>
            <th><?= preis($gesamt) ?></th>
        </tr>
    </table>

    <p>
        Zahlungsart: <?= e($kunde['zahlungsart']) ?><br>
        Lieferzeit: <?= LIEFERZEIT ?>
    </p>

    <div class="nicht-drucken">
        <button onclick="window.print()">Drucken / Als PDF speichern</button>
        <a href="index.php" class="button">Zur Speisekarte</a>
    </div>

<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>