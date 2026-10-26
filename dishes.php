<?php
require_once 'db.php';
require_once 'includes/functions.php';

// nur für den Ladenbesitzer
login_pruefen();

// Gericht ein- oder ausblenden
if (isset($_POST['umschalten'])) {
    $umschalten = $pdo->prepare('UPDATE gerichte SET aktiv = 1 - aktiv WHERE id = ?');
    $umschalten->execute([$_POST['umschalten']]);

    meldung('erfolg', 'Die Speisekarte wurde geändert.');
    header('Location: dishes.php');
    exit;
}

// Gericht löschen
if (isset($_POST['loeschen'])) {
    $id = (int) $_POST['loeschen'];

    // wurde das Gericht schon einmal bestellt?
    $abfrage = $pdo->prepare('SELECT COUNT(*) FROM bestellungen WHERE gericht_id = ?');
    $abfrage->execute([$id]);
    $bestellt = $abfrage->fetchColumn();

    if ($bestellt > 0) {
        // nicht löschen, sonst fehlen alte Bestellungen -> nur ausblenden
        $ausblenden = $pdo->prepare('UPDATE gerichte SET aktiv = 0 WHERE id = ?');
        $ausblenden->execute([$id]);

        meldung('fehler', 'Das Gericht wurde schon bestellt und kann nicht gelöscht werden. Es wurde ausgeblendet.');
    } else {
        $loeschen = $pdo->prepare('DELETE FROM gerichte WHERE id = ?');
        $loeschen->execute([$id]);

        meldung('erfolg', 'Das Gericht wurde gelöscht.');
    }

    header('Location: dishes.php');
    exit;
}

// alle Gerichte außer Angebote (die haben eine eigene Seite)
$gerichte = $pdo->query("SELECT * FROM gerichte WHERE kategorie != 'Angebote' ORDER BY kategorie, name")->fetchAll();

require_once 'includes/header.php';
?>

<h2>Speisekarte verwalten</h2>

<p><a href="dish_form.php" class="button">+ Neues Gericht</a></p>

<table>
    <tr>
        <th>Bild</th>
        <th>Name</th>
        <th>Kategorie</th>
        <th>Preis</th>
        <th>Auf der Speisekarte</th>
        <th></th>
    </tr>

    <?php foreach ($gerichte as $gericht): ?>
        <tr>
            <td><img src="<?= bild($gericht['bild']) ?>" class="mini"></td>
            <td><?= e($gericht['name']) ?></td>
            <td><?= e($gericht['kategorie']) ?></td>
            <td class="nicht-umbrechen"><?= preis($gericht['preis']) ?></td>
            <td><?= $gericht['aktiv'] ? 'Ja' : 'Nein' ?></td>
            <td>
                <div class="knoepfe">
                    <a href="dish_form.php?id=<?= $gericht['id'] ?>" class="button">Bearbeiten</a>

                    <form method="post">
                        <input type="hidden" name="umschalten" value="<?= $gericht['id'] ?>">
                        <button class="grau"><?= $gericht['aktiv'] ? 'Ausblenden' : 'Einblenden' ?></button>
                    </form>

                    <form method="post" onsubmit="return confirm('Wirklich löschen?')">
                        <input type="hidden" name="loeschen" value="<?= $gericht['id'] ?>">
                        <button class="rand">Löschen</button>
                    </form>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<?php require_once 'includes/footer.php'; ?>