-- ==============================================
-- VITE & GOURMAND - Données de test
-- ==============================================

-- Insertion des thèmes de référence
INSERT INTO theme (libelle) VALUES 
('Noël'), 
('Pâques'), 
('Classique'), 
('Évènement');

-- Insertion des régimes de référence
INSERT INTO regime (libelle) VALUES 
('Classique'), 
('Végétarien'), 
('Vegan');

-- Insertion des allergènes de référence
INSERT INTO allergene (libelle) VALUES 
('Gluten'), 
('Lactose'), 
('Arachides'), 
('Crustacés');

-- Insertion des horaires d'ouverture
INSERT INTO horaire (jour_semaine, heure_ouverture, heure_fermeture, est_ferme) VALUES 
('lundi', '08:00:00', '18:00:00', 0),
('mardi', '08:00:00', '18:00:00', 0),
('mercredi', '08:00:00', '18:00:00', 0),
('jeudi', '08:00:00', '18:00:00', 0),
('vendredi', '08:00:00', '19:00:00', 0),
('samedi', '09:00:00', '17:00:00', 0),
('dimanche', '00:00:00', '00:00:00', 1);

-- ============================================
-- COMPTES DE TEST (mdp hashés en bcrypt)
-- ============================================

-- Client test
-- Email: client@mail.com | MDP: Test1234!
INSERT INTO utilisateur (nom, prenom, gsm, email, password, ville, role, actif) VALUES 
('Dupont', 'Jean', '0601020304', 'client@mail.com', '$2y$10$qMo27tXboC1IXpE6L70erOcgxuExnEqteVm6EHIwP8xMSKB6N2kJK', 'Bordeaux', 'utilisateur', 1);

-- Employé test
-- Email: employe@vite-gourmand.fr | MDP: Staff1234!
INSERT INTO utilisateur (nom, prenom, gsm, email, password, ville, role, actif) VALUES 
('Martin', 'Alice', '0605060708', 'employe@vite-gourmand.fr', '$2y$10$ggyPsrlCkeBAY6yPCa926.g9eKD5dnHocTfGF5xUnVcpFv38KNL9.', 'Bordeaux', 'employe', 1);

-- Admin test
-- Email: jose@vite-gourmand.fr | MDP: Admin1234!
INSERT INTO utilisateur (nom, prenom, gsm, email, password, ville, role, actif) VALUES 
('Admin', 'José', '0600000000', 'jose@vite-gourmand.fr', '$2y$10$Tf0IbFBpaHZG1KLmAsprpeJ4Oaa92/WGdiw4UkrsRNy0jeEpOot9m', 'Bordeaux', 'admin', 1);

-- ============================================
-- MENU DE TEST 
-- ============================================

-- MENU 1 : Prestige de Noël

-- Création des plats
INSERT INTO plat (libelle, type, image_path) VALUES 
('Foie Gras de canard', 'entrée', 'foie_gras.jpg'),
('Chapon rôti aux marrons', 'plat', 'chapon.jpg'),
('Bûche chocolat noir', 'dessert', 'buche.jpg');

-- Création du menu (thème Noël = 1, régime Classique = 1)
INSERT INTO menu (titre, description, nombre_personne_min, prix_base, stock_disponible, theme_id, regime_id) VALUES 
('Menu Prestige de Noël', 'Un menu festif avec des produits du terroir bordelais.', 4, 120.00, 10, 1, 1);

-- Liaison menu <-> plats (menu_id=1, plat_id=1,2,3)
INSERT INTO compose_menu (menu_id, plat_id) VALUES 
(1, 1), 
(1, 2), 
(1, 3);


-- MENU 2 : Végétarien Printanier
INSERT INTO plat (libelle, type, image_path) VALUES 
('Velouté d''asperges vertes', 'entrée', 'veloute_asperges.jpg'),
('Risotto aux champignons', 'plat', 'risotto.jpg'),
('Tarte citron meringuée', 'dessert', 'tarte_citron.jpg');

INSERT INTO menu (titre, description, nombre_personne_min, prix_base, stock_disponible, theme_id, regime_id) VALUES 
('Menu Végétarien Printanier', 'Des saveurs fraîches et de saison, sans protéine animale.', 2, 85.00, 15, 3, 2);

-- Liaison menu 2 <-> plats (plat_id = 4, 5, 6)
INSERT INTO compose_menu (menu_id, plat_id) VALUES 
(2, 4), 
(2, 5), 
(2, 6);

-- MENU 3 : Classique du Terroir
INSERT INTO plat (libelle, type, image_path) VALUES 
('Terrine de campagne', 'entrée', 'terrine.jpg'),
('Magret de canard aux figues', 'plat', 'magret.jpg'),
('Cannelés bordelais', 'dessert', 'canneles.jpg');

INSERT INTO menu (titre, description, nombre_personne_min, prix_base, stock_disponible, theme_id, regime_id) VALUES 
('Menu Classique du Terroir', 'Les incontournables de la gastronomie bordelaise.', 4, 95.00, 12, 3, 1);

-- Liaison menu 3 <-> plats (plat_id = 7, 8, 9)
INSERT INTO compose_menu (menu_id, plat_id) VALUES 
(3, 7), 
(3, 8), 
(3, 9);

-- Allergènes pour le menu 3
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES 
(7, 1), -- Terrine contient du gluten (pain)
(9, 2); -- Cannelés contiennent du lactose
-- Ajout allergène Lactose sur la Bûche (plat_id=3, allergene_id=2)
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES 
(3, 2);

-- ============================================
-- AVIS DE TEST (affichage accueil)
-- ============================================

-- D'abord on crée une commande "terminée" pour chaque avis
-- (car on peut pas laisser un avis sans commande liée)

INSERT INTO commande (date_commande, date_prestation, heure_prestation, adresse_livraison, nombre_personnes, prix_total_ttc, statut, utilisateur_id, menu_id, pret_materiel, materiel_rendu) VALUES 
('2026-03-15 14:30:00', '2026-03-20', '19:00:00', '12 Rue des Lilas, Bordeaux', 6, 150.00, 'terminée', 1, 1, 0, 0),
('2026-03-22 10:15:00', '2026-03-28', '20:00:00', '45 Avenue de la République, Bordeaux', 4, 95.00, 'terminée', 1, 3, 1, 1),
('2026-04-10 16:45:00', '2026-04-15', '12:30:00', '8 Place Gambetta, Bordeaux', 2, 85.00, 'terminée', 1, 2, 0, 0);

-- Maintenant les avis validés (est_valide = 1)

-- Avis 1 : Note 3/5 (celui de ta capture)
INSERT INTO avis (note, commentaire, est_valide, date_publication, utilisateur_id, commande_id) VALUES 
(3, 'Repas très savoureux, manquant un petit peu de sel, mais reste très bon.', 1, '2026-04-03 02:01:01', 1, 1);

-- Avis 2 : Note 5/5
INSERT INTO avis (note, commentaire, est_valide, date_publication, utilisateur_id, commande_id) VALUES 
(5, 'Miam, je me suis régalé, très bon repas pris avec ma femme et mes 2 enfants, très bon moment, merci !', 1, '2026-04-04 19:55:46', 1, 2);

-- Avis 3 : Note 5/5
INSERT INTO avis (note, commentaire, est_valide, date_publication, utilisateur_id, commande_id) VALUES 
(5, 'Excellent, meilleur plat que j''ai pu manger chez un traiteur !', 1, '2026-04-26 23:42:18', 1, 3);