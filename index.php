<?php
require_once 'db.php';
require_once 'includes/functions.php';

// nur Gerichte zeigen, die aktiv sind (gelöschte Angebote nicht)
$gerichte = $pdo->query('SELECT * FROM gerichte WHERE aktiv = 1')->fetchAll();

require_once 'includes/header.php';
?>

<?php foreach (KATEGORIEN as $kategorie): ?>
    <?php
    // Kategorie ohne Gerichte (z. B. noch keine Angebote) überspringen
    if (!in_array($kategorie, array_column($gerichte, 'kategorie'))) {
        continue;
    }
    ?>

    <h2><?= $kategorie ?></h2>

    <div class="gerichte">
        <?php foreach ($gerichte as $gericht): ?>
            <?php if ($gericht['kategorie'] == $kategorie): ?>
                <?php
                // wie oft ist das Gericht schon im Warenkorb?
                $stueck = '';
                if (isset($_SESSION['warenkorb'][$gericht['id']])) {
                    $stueck = $_SESSION['warenkorb'][$gericht['id']] . 'x';
                }
                ?>
                <div class="gericht">
                    <img src="<?= bild($gericht['bild']) ?>">
                    <h3><?= $gericht['name'] ?></h3>
                    <p><?= $gericht['beschreibung'] ?></p>
                    <p class="preis"><?= preis($gericht['preis']) ?></p>

                    <div class="bestellen">
                        <button data-name="<?= e($gericht['name']) ?>" onclick="hinzufuegen(<?= $gericht['id'] ?>, this)">In den Warenkorb</button>
                        <span class="stueck" id="stueck<?= $gericht['id'] ?>"><?= $stueck ?></span>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>

<div id="toast"></div>

<script>
function hinzufuegen(id, knopf) {
    fetch('warenkorb_add.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id
    })
    .then(antwort => antwort.json())
    .then(daten => {
        // Zahl oben im Menü und neben dem Knopf ändern
        document.getElementById('anzahl').textContent = daten.gesamt;
        document.getElementById('stueck' + id).textContent = daten.stueck + 'x';

        // Meldung unten rechts 2 Sekunden zeigen
        let toast = document.getElementById('toast');
        toast.textContent = knopf.dataset.name + ' wurde zum Warenkorb hinzugefügt';
        toast.style.display = 'block';

        clearTimeout(toast.timer);
        toast.timer = setTimeout(() => toast.style.display = 'none', 2000);
    });
}
</script>

<?php require_once 'includes/footer.php'; ?>