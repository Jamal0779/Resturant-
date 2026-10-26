<?php
require_once 'includes/functions.php';

unset($_SESSION['login']);
unset($_SESSION['kunde']);

meldung('erfolg', 'Du bist ausgeloggt.');
header('Location: index.php');
exit;