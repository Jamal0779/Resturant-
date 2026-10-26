<?php
require_once 'db.php';
require_once 'includes/functions.php';

// nur für den Ladenbesitzer
login_pruefen();

$fehler = '';

// leeres Gericht (für "Neues Gericht")
$gericht = ['id' => '', 'name' => '', 'kategorie' => '', 'beschreibung' => '', 'zutaten' => '', 'preis' => '', 'bild' => ''];

// mit ?id=... wird ein vorhandenes Gericht bearbeitet
if (isset($_GET['id'])) {
    $abfrage = $pdo->prepare('SELECT * FROM gerichte WHERE id = ?');
    $abfrage->execute([$_GET['id']]);
    $gericht = $abfrage->fetch();

    if (!$gericht) {
        die('Gericht nicht gefunden.');
    }
}

// Formular wurde abgeschickt
if (isset($_POST['name'])) {
    $gericht['name'] = trim($_POST['name']);
    $gericht['kategorie'] = $_POST['kategorie'];
    $gericht['beschreibung'] = trim($_POST['beschreibung']);
    $gericht['zutaten'] = trim($_POST['zutaten']);
    $gericht['preis'] = str_replace(',', '.', trim($_POST['preis']));

    $hochgeladen = isset($_FILES['bild']) && $_FILES['bild']['error'] == 0;

    if ($gericht['name'] == '' || $gericht['beschreibung'] == '') {
        $fehler = 'Bitte Name und Beschreibung ausfüllen.';
    } elseif (!in_array($gericht['kategorie'], KATEGORIEN) || $gericht['kategorie'] == 'Angebote') {
        $fehler = 'Bitte eine Kategorie auswählen.';
    } elseif (!is_numeric($gericht['preis']) || $gericht['preis'] <= 0) {
        $fehler = 'Bitte einen gültigen Preis eingeben, z. B. 9,50';
    } elseif ($hochgeladen && $_FILES['bild']['size'] > MAX_BILD_GROESSE) {
        $fehler = 'Das Bild ist zu groß (höchstens 2 MB).';
    } elseif ($hochgeladen && !getimagesize($_FILES['bild']['tmp_name'])) {
        $fehler = 'Die Datei ist kein Bild.';
    } else {
        // neues Bild speichern
        if ($hochgeladen) {
            $endung = strtolower(pathinfo($_FILES['bild']['name'], PATHINFO_EXTENSION));

            if (!in_array($endung, ['jpg', 'jpeg', 'png', 'webp'])) {
                $fehler = 'Nur JPG, PNG oder WEBP erlaubt.';
            } else {
                $dateiname = uniqid() . '.' . $endung;
                move_uploaded_file($_FILES['bild']['tmp_name'], BILDER_ORDNER . $dateiname);
                $gericht['bild'] = $dateiname;
            }
        }
    }

    if ($fehler == '') {
        if ($gericht['id'] == '') {
            // neues Gericht
            $speichern = $pdo->prepare('INSERT INTO gerichte (name, kategorie, beschreibung, zutaten, preis, bild)
                VALUES (?, ?, ?, ?, ?, ?)');
            $speichern->execute([$gericht['name'], $gericht['kategorie'], $gericht['beschreibung'], $gericht['zutaten'], $gericht['preis'], $gericht['bild']]);

            meldung('erfolg', 'Das Gericht "' . $gericht['name'] . '" wurde angelegt.');
        } else {
            // vorhandenes Gericht ändern
            $speichern = $pdo->prepare('UPDATE gerichte SET name = ?, kategorie = ?, beschreibung = ?, zutaten = ?, preis = ?, bild = ?
                WHERE id = ?');
            $speichern->execute([$gericht['name'], $gericht['kategorie'], $gericht['beschreibung'], $gericht['zutaten'], $gericht['preis'], $gericht['bild'], $gericht['id']]);

            meldung('erfolg', 'Das Gericht "' . $gericht['name'] . '" wurde gespeichert.');
        }

        header('Location: dishes.php');
        exit;
    }
}

require_once 'includes/header.php';
?>

<h2><?= $gericht['id'] == '' ? 'Neues Gericht' : 'Gericht bearbeiten' ?></h2>

<?php if ($fehler != ''): ?>
    <p class="meldung fehler"><?= $fehler ?></p>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="formular">
    <label>Name</label>
    <input type="text" name="name" value="<?= e($gericht['name']) ?>">

    <label>Kategorie</label>
    <select name="kategorie">
        <?php foreach (KATEGORIEN as $kategorie): ?>
            <?php if ($kategorie != 'Angebote'): ?>
                <option <?= $kategorie == $gericht['kategorie'] ? 'selected' : '' ?>><?= $kategorie ?></option>
            <?php endif; ?>
        <?php endforeach; ?>
    </select>

    <label>Beschreibung</label>
    <input type="text" name="beschreibung" value="<?= e($gericht['beschreibung']) ?>">

    <label>Zutaten (freiwillig)</label>
    <input type="text" name="zutaten" value="<?= e($gericht['zutaten']) ?>">

    <label>Preis in €</label>
    <input type="text" name="preis" value="<?= e(str_replace('.', ',', $gericht['preis'])) ?>" placeholder="z. B. 9,50">

    <label>Bild (JPG, PNG oder WEBP, höchstens 2 MB)</label>
    <?php if ($gericht['bild'] != ''): ?>
        <img src="<?= bild($gericht['bild']) ?>" class="mini"><br>
        <span class="klein">Nur ein neues Bild wählen, wenn du es ändern willst.</span>
    <?php endif; ?>
    <input type="file" name="bild" accept=".jpg,.jpeg,.png,.webp">

    <br><br>
    <button>Speichern</button>
    <a href="dishes.php" class="button">Abbrechen</a>
</form>

<?php require_once 'includes/footer.php'; ?>