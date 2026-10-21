CREATE DATABASE IF NOT EXISTS restaurant_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
 
USE restaurant_db;
 
CREATE TABLE gerichte (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    kategorie VARCHAR(50) NOT NULL,
    beschreibung VARCHAR(255) NOT NULL,
    zutaten VARCHAR(255) NOT NULL,
    preis DECIMAL(6,2) NOT NULL,
    bild VARCHAR(255) NULL,
    aktiv TINYINT(1) NOT NULL DEFAULT 1,
    erstellt_am DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    aktualisiert_am DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB;
 
CREATE TABLE bestellungen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bestellnummer VARCHAR(20) NOT NULL,
    beleg_token CHAR(32) NOT NULL,
    gericht_id INT NOT NULL,
    gerichtname VARCHAR(100) NOT NULL,
    einzelpreis DECIMAL(6,2) NOT NULL,
    menge INT NOT NULL,
    kundenname VARCHAR(100) NOT NULL,
    lieferadresse VARCHAR(255) NOT NULL,
    telefon VARCHAR(30) NOT NULL,
    bestelldatum DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM('neu', 'in_zubereitung', 'unterwegs', 'geliefert', 'storniert') NOT NULL DEFAULT 'neu',
    INDEX (bestellnummer),
    INDEX (beleg_token),
    FOREIGN KEY (gericht_id) REFERENCES gerichte(id)
        ON DELETE RESTRICT
) ENGINE = InnoDB;
 
INSERT INTO gerichte (name, kategorie, beschreibung, zutaten, preis, bild) VALUES
('Nachos mit Käse', 'Vorspeisen', 'Knusprige Chips mit geschmolzenem Käse', 'Tortilla-Chips, Käse, Jalapeños, Salsa', 5.90, 'nachos.png'),
('Guacamole mit Chips', 'Vorspeisen', 'Frische Avocadocreme', 'Avocado, Tomate, Zwiebel, Limette, Chips', 5.50, 'guacamole.png'),
('Tacos (3 Stück)', 'Hauptgerichte', 'Mit Rindfleisch und frischem Koriander', 'Maistortillas, Rindfleisch, Zwiebel, Koriander', 10.90, 'tacos.png'),
('Burrito', 'Hauptgerichte', 'Gefüllt mit Hähnchen und Reis', 'Weizentortilla, Hähnchen, Reis, Bohnen, Käse', 11.90, 'burrito.png'),
('Quesadilla', 'Hauptgerichte', 'Vegetarisch, mit viel Käse', 'Weizentortilla, Käse, Paprika, Mais', 9.50, 'quesadilla.png'),
('Chili con Carne', 'Hauptgerichte', 'Würzig und herzhaft', 'Rinderhack, Bohnen, Mais, Tomaten', 10.50, 'chili.png'),
('Churros', 'Desserts', 'Mit Zimtzucker und Schokoladensoße', 'Teig, Zucker, Zimt, Schokoladensoße', 4.90, 'churros.png'),
('Flan', 'Desserts', 'Mexikanischer Karamellpudding', 'Eier, Milch, Zucker, Karamell', 4.50, 'flan.png'),
('Horchata 0,4 l', 'Getränke', 'Kalter Reisdrink mit Zimt', 'Reis, Milch, Zimt, Zucker', 3.50, 'horchata.png'),
('Limonade 0,4 l', 'Getränke', 'Hausgemacht mit Limette', 'Limette, Wasser, Zucker', 3.00, 'limonade.png'),
('Cola 0,33 l', 'Getränke', 'Flasche', 'Cola', 2.80, 'cola.png'),
('Cola Zero 0,33 l', 'Getränke', 'Flasche', 'Cola ohne Zucker', 2.80, 'cola_zero.png'),
('Fanta 0,33 l', 'Getränke', 'Flasche', 'Orangenlimonade', 2.80, 'orangenlimonade.png'),
('Sprite 0,33 l', 'Getränke', 'Flasche', 'Zitronenlimonade', 2.80, 'zitronenlimonade.png'),
('Wasser still 0,5 l', 'Getränke', 'Flasche', 'Mineralwasser', 2.50, 'wasser_still.png'),
('Wasser mit Kohlensäure 0,5 l', 'Getränke', 'Flasche', 'Mineralwasser', 2.50, 'wasser_sprudel.png'),
('Apfelschorle 0,5 l', 'Getränke', 'Flasche', 'Apfelsaft, Mineralwasser', 3.20, 'apfelschorle.png'),
('Eistee Pfirsich 0,5 l', 'Getränke', 'Flasche', 'Tee, Pfirsich, Zucker', 3.00, 'eistee_pfirsich.png');

 
INSERT INTO bestellungen (bestellnummer, beleg_token, gericht_id, gerichtname, einzelpreis, menge, kundenname, lieferadresse, telefon, bestelldatum, status) VALUES
('20261014-3851', 'a3f9c2e17b4d8f60e2c5a91b7d3f4e08', 4, 'Pizza Margherita', 9.50, 2, 'Max Mustermann', 'Hauptstraße 12, 10115 Berlin', '030 1234567', '2026-10-14 18:30:00', 'geliefert'),
('20261014-3851', 'a3f9c2e17b4d8f60e2c5a91b7d3f4e08', 11, 'Cola 0,33 l', 2.80, 2, 'Max Mustermann', 'Hauptstraße 12, 10115 Berlin', '030 1234567', '2026-10-14 18:30:00', 'geliefert'),
('20261015-0472', '5b7e1d9c3a2f8064b1e7c9d2a5f3086e', 6, 'Spaghetti Bolognese', 11.50, 1, 'Anna Schmidt', 'Gartenweg 5, 10117 Berlin', '0171 2345678', '2026-10-15 12:05:00', 'in_zubereitung'),
('20261015-0472', '5b7e1d9c3a2f8064b1e7c9d2a5f3086e', 9, 'Tiramisu', 5.90, 1, 'Anna Schmidt', 'Gartenweg 5, 10117 Berlin', '0171 2345678', '2026-10-15 12:05:00', 'in_zubereitung'),
('20261015-0472', '5b7e1d9c3a2f8064b1e7c9d2a5f3086e', 12, 'Wasser 0,5 l', 2.50, 1, 'Anna Schmidt', 'Gartenweg 5, 10117 Berlin', '0171 2345678', '2026-10-15 12:05:00', 'in_zubereitung'),
('20261015-7316', 'e4c80f2a9d6b3175c8a2e9f1047bd36c', 7, 'Hähnchen mit Reis', 13.90, 1, 'Ali Yilmaz', 'Lindenallee 8, 10119 Berlin', '0152 3456789', '2026-10-15 12:40:00', 'neu'),
('20261015-7316', 'e4c80f2a9d6b3175c8a2e9f1047bd36c', 3, 'Bruschetta', 6.50, 2, 'Ali Yilmaz', 'Lindenallee 8, 10119 Berlin', '0152 3456789', '2026-10-15 12:40:00', 'neu');
