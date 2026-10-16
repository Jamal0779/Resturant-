<?php require_once __DIR__ . '/functions.php'; ?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e(RESTAURANT_NAME) ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <img src="<?= LOGO ?>" alt="Logo" class="logo">
    <h1><?= e(RESTAURANT_NAME) ?></h1>
</header>

<nav>
    <a href="index.php">Speisekarte</a>
    <a href="cart.php">Warenkorb (<?= warenkorb_anzahl() ?>)</a>

    <?php if (!empty($_SESSION['login'])): ?>
        <a href="orders.php">Bestellungen</a>
        <a href="dishes.php">Speisekarte verwalten</a>
        <a href="logout.php">Logout</a>
    <?php else: ?>
        <a href="login.php">Login</a>
    <?php endif; ?>
</nav>

<main>

<?php if (!empty($_SESSION['meldung'])): ?>
    <p class="meldung <?= e($_SESSION['meldung']['typ']) ?>">
        <?= e($_SESSION['meldung']['text']) ?>
    </p>
    <?php unset($_SESSION['meldung']); ?>
<?php endif; ?>