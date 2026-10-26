<?php
require_once 'includes/functions.php';

$id = $_POST['id'];

if (isset($_SESSION['warenkorb'][$id])) {
    $_SESSION['warenkorb'][$id]++;
} else {
    $_SESSION['warenkorb'][$id] = 1;
}

// Anzahl gesamt und Anzahl von diesem Gericht zurückgeben
echo json_encode([
    'gesamt' => warenkorb_anzahl(),
    'stueck' => $_SESSION['warenkorb'][$id]
]);