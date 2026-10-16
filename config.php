<?php
// Einstellungen für das ganze Projekt

const RESTAURANT_NAME = 'Sabor de México';
const LOGO = 'images/logo.svg';
const LIEFERZEIT = 'ca. 45 Minuten';

// Datenbank
const DB_HOST = 'localhost';
const DB_NAME = 'restaurant_db';
const DB_USER = 'root';
const DB_PASS = '';

const KATEGORIEN = ['Vorspeisen', 'Hauptgerichte', 'Desserts', 'Getränke'];

const STATUS_LISTE = ['neu', 'in_zubereitung', 'unterwegs', 'geliefert', 'storniert'];

// Login für das Restaurant: admin / restaurant123
const ADMIN_USER = 'admin';
const ADMIN_HASH = '$2y$10$O2Q29gXij0dO7FCO.rSkm.V6OfWtELJN6b728UfyPn9SjLOwY1kPO';

// Bilder
const BILDER_ORDNER = 'images/gerichte/';
const PLATZHALTER = 'images/placeholder.svg';
const MAX_BILD_GROESSE = 2000000; // 2 MB