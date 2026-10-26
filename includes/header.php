<?php require_once __DIR__ . '/functions.php'; ?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e(RESTAURANT_NAME) ?></title>
    <link rel="stylesheet" href="css/style.css?v=12">
</head>
<body>

<header>
    <img src="<?= LOGO ?>" alt="Logo" class="logo">
    <h1><?= e(RESTAURANT_NAME) ?></h1>
</header>

<nav>
    <a href="index.php">Speisekarte</a>
    <a href="cart.php">Warenkorb (<span id="anzahl"><?= warenkorb_anzahl() ?></span>)</a>

    <?php if (!empty($_SESSION['login'])): ?>
        <a href="orders.php">Bestellungen</a>
        <a href="dishes.php">Speisekarte verwalten</a>
        <a href="angebote.php">Angebote</a>
        <a href="logout.php">Logout</a>
    <?php elseif (!empty($_SESSION['kunde'])): ?>
        <a href="logout.php">Logout (<?= e($_SESSION['kunde']['name']) ?>)</a>
    <?php else: ?>
        <a href="login.php">Login</a>
        <a href="register.php">Registrieren</a>
    <?php endif; ?>
</nav>

<?php
// Status der letzten Bestellung auf jeder Seite zeigen (nicht für den Ladenbesitzer)
$letzte = false;
if (!empty($_SESSION['letzte_bestellung']) && empty($_SESSION['login'])) {
    require_once __DIR__ . '/../db.php';
    $abfrage = $pdo->prepare('SELECT bestellnummer, status FROM bestellungen WHERE beleg_token = ?');
    $abfrage->execute([$_SESSION['letzte_bestellung']]);
    $letzte = $abfrage->fetch();
}

// bei "geliefert" oder "storniert": nach 5 Minuten nicht mehr zeigen
if ($letzte && ($letzte['status'] == 'geliefert' || $letzte['status'] == 'storniert')) {
    if (!isset($_SESSION['fertig_seit'])) {
        $_SESSION['fertig_seit'] = time();
    }

    if (time() - $_SESSION['fertig_seit'] > 5 * 60) {
        unset($_SESSION['letzte_bestellung']);
        unset($_SESSION['fertig_seit']);
        $letzte = false;
    }
}
?>

<?php if ($letzte): ?>
    <div class="statusleiste">
        Deine Bestellung <?= $letzte['bestellnummer'] ?>: <b><?= status_text($letzte['status']) ?></b>
        &nbsp; <a href="">Aktualisieren</a>
    </div>
<?php endif; ?>

<main>

<?php if (!empty($_SESSION['meldung'])): ?>
    <p class="meldung <?= e($_SESSION['meldung']['typ']) ?>">
        <?= e($_SESSION['meldung']['text']) ?>
    </p>
    <?php unset($_SESSION['meldung']); ?>
<?php endif; ?>