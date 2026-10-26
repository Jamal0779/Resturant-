<?php
require_once 'db.php';
require_once 'includes/functions.php';

// nur für den Ladenbesitzer
login_pruefen();

$fehler = '';

// neues Angebot erstellen
if (isset($_POST['name'])) {
    $name = trim($_POST['name']);
    $preis = str_replace(',', '.', $_POST['preis']);

    $ausgewaehlt = [];
    if (isset($_POST['gerichte'])) {
        $ausgewaehlt = $_POST['gerichte'];
    }

    if ($name == '') {
        $fehler = 'Bitte einen Namen eingeben.';
    } elseif (count($ausgewaehlt) < 2) {
        $fehler = 'Bitte mindestens 2 Gerichte auswählen.';
    } elseif (!is_numeric($preis) || $preis <= 0) {
        $fehler = 'Bitte einen gültigen Preis eingeben.';
    } else {
        $namen = [];
        $normalpreis = 0;
        $bild = '';

        // ausgewählte Gerichte holen
        foreach ($ausgewaehlt as $id) {
            $abfrage = $pdo->prepare("SELECT * FROM gerichte WHERE id = ? AND kategorie != 'Angebote'");
            $abfrage->execute([$id]);
            $gericht = $abfrage->fetch();

            if ($gericht) {
                $namen[] = $gericht['name'];
                $normalpreis = $normalpreis + $gericht['preis'];

                // Bild vom ersten Gericht nehmen
                if ($bild == '') {
                    $bild = $gericht['bild'];
                }
            }
        }

        // z. B. "Tacos (3 Stück) + Cola 0,33 l + Churros – statt 18,60 €"
        $beschreibung = implode(' + ', $namen) . ' – statt ' . preis($normalpreis);
        $zutaten = implode(', ', $namen);

        $speichern = $pdo->prepare("INSERT INTO gerichte (name, kategorie, beschreibung, zutaten, preis, bild)
            VALUES (?, 'Angebote', ?, ?, ?, ?)");
        $speichern->execute([$name, $beschreibung, $zutaten, $preis, $bild]);

        meldung('erfolg', 'Das Angebot "' . $name . '" wurde erstellt.');
        header('Location: angebote.php');
        exit;
    }
}

// Angebot löschen (nur ausblenden, weil alte Bestellungen es noch brauchen)
if (isset($_POST['loeschen'])) {
    $loeschen = $pdo->prepare("UPDATE gerichte SET aktiv = 0 WHERE id = ? AND kategorie = 'Angebote'");
    $loeschen->execute([$_POST['loeschen']]);

    meldung('erfolg', 'Das Angebot wurde gelöscht.');
    header('Location: angebote.php');
    exit;
}

$gerichte = $pdo->query("SELECT * FROM gerichte WHERE aktiv = 1 AND kategorie != 'Angebote'")->fetchAll();
$angebote = $pdo->query("SELECT * FROM gerichte WHERE aktiv = 1 AND kategorie = 'Angebote'")->fetchAll();

require_once 'includes/header.php';
?>

<h2>Angebote</h2>

<?php if (empty($angebote)): ?>
    <p>Es gibt noch keine Angebote.</p>
<?php else: ?>
    <table>
        <tr>
            <th>Name</th>
            <th>Inhalt</th>
            <th>Preis</th>
            <th></th>
        </tr>
        <?php foreach ($angebote as $angebot): ?>
            <tr>
                <td><?= e($angebot['name']) ?></td>
                <td><?= e($angebot['beschreibung']) ?></td>
                <td><?= preis($angebot['preis']) ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="loeschen" value="<?= $angebot['id'] ?>">
                        <button>Löschen</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<h2>Neues Angebot erstellen</h2>

<?php if ($fehler != ''): ?>
    <p class="meldung fehler"><?= $fehler ?></p>
<?php endif; ?>

<form method="post" class="formular">
    <label>Name des Angebots</label>
    <input type="text" name="name" placeholder="z. B. Taco-Menü">

    <label>Gerichte auswählen (mindestens 2)</label>
    <div class="auswahl">
        <?php foreach (KATEGORIEN as $kategorie): ?>
            <?php foreach ($gerichte as $gericht): ?>
                <?php if ($gericht['kategorie'] == $kategorie): ?>
                    <label class="haken">
                        <input type="checkbox" name="gerichte[]" value="<?= $gericht['id'] ?>">
                        <?= e($gericht['name']) ?> <span class="klein">(<?= preis($gericht['preis']) ?>)</span>
                    </label>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>

    <label>Angebotspreis in €</label>
    <input type="text" name="preis" placeholder="z. B. 15,90">

    <br><br>
    <button>Angebot erstellen</button>
</form>

<?php require_once 'includes/footer.php'; ?>