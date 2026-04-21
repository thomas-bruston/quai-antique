
-- Jeu de données 

-- Admin  : admin@quai-antique.fr / Admin@12345
-- Client : client@example.fr    / Client@12345

SET NAMES utf8mb4;
USE quai_antique;


-- Compte admin

INSERT INTO user (email, password, prenom, nom, nombre_convives_defaut, allergies, role) VALUES (
    'admin@quai-antique.fr',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Arnaud', 'Michant', 2, NULL, 'admin'
);


-- Compte client 

INSERT INTO user (email, password, prenom, nom, nombre_convives_defaut, allergies, role) VALUES (
    'client@example.fr',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Jean', 'Dupont', 4, 'Gluten, lactose', 'client'
);


-- Paramètres du restaurant

INSERT INTO restaurant_settings (heure_ouverture, heure_fermeture, max_convives)
VALUES ('19:00:00', '21:00:00', 50);


-- Catégories

INSERT INTO dish_category (category_id, titre) VALUES (1, 'Entrées'), (2, 'Plats'), (3, 'Desserts');


-- Plats

INSERT INTO dish (dish_id, category_id, titre, description, prix) VALUES
(1, 1, 'LA SAINT-JACQUES DE NORMANDIE', 'en carpaccio\ncrème glacée au géranium rosat, saké.', 32),
(2, 1, 'LE PANAIS', 'confit, pommes reine des Reinettes\nail noir et bière blanche sabayon tiède au sobacha', 28),
(3, 1, 'LES BERLINGOTS', 'camembert de Normandie A.O.P., topinambours rôtis\nconsommé lié café Mariposa et carvi', 30),
(4, 2, 'LA SELLE D\'AGNEAU DE L\'AVEYRON', 'imprégnée au Rooibos et kororima\nbonbon de blette, brousse de brebis, olives noires', 38),
(5, 2, 'LE MAQUEREAU DE PETITS BATEAUX', 'laqué au barbecue\nmoules et poireaux, algues marines et thé matcha', 36),
(6, 3, 'LE FROMAGE CUISINÉ', 'vieux comté 24 mois A.O.P, servi tiède\npoire, baie de verveine et vanille de Madagascar', 26),
(7, 3, 'LE MILLEFEUILLE BLANC ORIGINAL', 'vanille de Madagascar\njasmin et poivre de Voatsiperifey', 26),
(8, 3, 'LE MONT-BLANC', 'châtaigne, yuzu et whisky, baie des Bataks', 18);


-- Menus

INSERT INTO menu (menu_id, titre, nombre_sequences, prix) VALUES
(1, 'Mont Blanc', 4, 120),
(2, 'Grande Rocheuse', 5, 150),
(3, 'Aiguille Verte', 7, 190);


-- Liaison menuset plats

-- Mont Blanc (4 séquences)
INSERT INTO menu_dish (menu_id, dish_id) VALUES (1,1),(1,2),(1,4),(1,8);

-- Grande Rocheuse (5 séquences)
INSERT INTO menu_dish (menu_id, dish_id) VALUES (2,1),(2,3),(2,4),(2,6),(2,8);

-- Aiguille Verte (7 séquences)
INSERT INTO menu_dish (menu_id, dish_id) VALUES (3,1),(3,2),(3,3),(3,4),(3,6),(3,7),(3,8);


-- Réservations

INSERT INTO reservation (user_id, date, heure, nombre_convives, allergies) VALUES
(2, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '12:30:00', 4, 'Gluten, lactose'),
(2, DATE_ADD(CURDATE(), INTERVAL 10 DAY), '20:00:00', 2, NULL);


-- Galerie

INSERT INTO gallery (titre, photo) VALUES
('Création du Chef', 'caption.jpg'),
('Création du Chef', 'caption2.jpg'),
('Création du Chef', 'caption3.jpg'),
('Création du Chef', 'caption4.jpg'),
('Création du Chef', 'caption5.jpg'),
('Création du Chef', 'caption6.jpg'),
('Création du Chef', 'caption7.jpg'),
('Création du Chef', 'caption8.jpg'),
('Création du Chef', 'caption9.jpg'),
('Création du Chef', 'caption10.jpg'),
('Création du Chef', 'caption11.jpg'),
('Création du Chef', 'caption12.jpg'),
('Création du Chef', 'caption13.jpg'),
('Création du Chef', 'caption14.jpg'),
('Création du Chef', 'caption15.jpg'),
('Création du Chef', 'caption16.jpg'),
('Création du Chef', 'caption17.jpg'),
('Création du Chef', 'caption18.jpg'),
('Création du Chef', 'caption19.jpg'),
('Création du Chef', 'caption20.jpg'),
('Création du Chef', 'caption21.jpg'),
('Création du Chef', 'caption22.jpg');
