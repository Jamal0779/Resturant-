<?php
require_once 'db.php';
require_once 'includes/functions.php';

// Klick auf "In den Warenkorb"
if (isset($_POST['id'])) {
    $id = $_POST['id'];

    if (isset($_SESSION['warenkorb'][$id])) {
        $_SESSION['warenkorb'][$id]++;
    } else {
        $_SESSION['warenkorb'][$id] = 1;
    }

    meldung('erfolg', 'Das Gericht ist jetzt im Warenkorb.');
    header('Location: index.php');
    exit;
}

$gerichte = $pdo->query('SELECT * FROM gerichte')->fetchAll();

require_once 'includes/header.php';
?>

<?php foreach (KATEGORIEN as $kategorie): ?>
    <h2><?= $kategorie ?></h2>

    <div class="gerichte">
        <?php foreach ($gerichte as $gericht): ?>
            <?php if ($gericht['kategorie'] == $kategorie): ?>
                <div class="gericht">
                    <img src="<?= bild($gericht['bild']) ?>">
                    <h3><?= $gericht['name'] ?></h3>
                    <p><?= $gericht['beschreibung'] ?></p>
                    <p class="preis"><?= preis($gericht['preis']) ?></p>

                    <form method="post">
                        <input type="hidden" name="id" value="<?= $gericht['id'] ?>">
                        <button>In den Warenkorb</button>
                    </form>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>

<?php require_once 'includes/footer.php'; ?>