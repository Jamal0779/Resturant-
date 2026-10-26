<?php
require_once 'db.php';
require_once 'includes/functions.php';

// ein Gericht entfernen
if (isset($_POST['entfernen'])) {
    $id = $_POST['entfernen'];
    unset($_SESSION['warenkorb'][$id]);

    header('Location: cart.php');
    exit;
}

// Warenkorb leeren
if (isset($_POST['leeren'])) {
    $_SESSION['warenkorb'] = [];

    header('Location: cart.php');
    exit;
}

require_once 'includes/header.php';
?>

<h2>Warenkorb</h2>

<?php if (empty($_SESSION['warenkorb'])): ?>

    <p>Dein Warenkorb ist leer.</p>
    <a href="index.php" class="button">Zur Speisekarte</a>

<?php else: ?>

    <table>
        <tr>
            <th>Gericht</th>
            <th>Preis</th>
            <th>Menge</th>
            <th>Summe</th>
            <th></th>
        </tr>

        <?php $gesamt = 0; ?>

        <?php foreach ($_SESSION['warenkorb'] as $id => $menge): ?>
            <?php
            $abfrage = $pdo->prepare('SELECT * FROM gerichte WHERE id = ?');
            $abfrage->execute([$id]);
            $gericht = $abfrage->fetch();

            $summe = $gericht['preis'] * $menge;
            $gesamt = $gesamt + $summe;
            ?>
            <tr>
                <td><?= $gericht['name'] ?></td>
                <td><?= preis($gericht['preis']) ?></td>
                <td><?= $menge ?></td>
                <td><?= preis($summe) ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="entfernen" value="<?= $id ?>">
                        <button>Entfernen</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>

        <tr>
            <th colspan="3">Gesamt</th>
            <th colspan="2"><?= preis($gesamt) ?></th>
        </tr>
    </table>

    <br>

    <form method="post">
        <button name="leeren">Warenkorb leeren</button>
    </form>

    <br>

    <a href="index.php" class="button">Weiter einkaufen</a>
    <a href="checkout.php" class="button">Jetzt bestellen</a>

<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>