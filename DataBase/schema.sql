-- ==============================================
-- VITE & GOURMAND - Schéma de base de données
-- ==============================================

-- Table utilisateur
CREATE TABLE utilisateur (
    utilisateur_id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    gsm VARCHAR(20) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    adresse_postale VARCHAR(255),
    ville VARCHAR(50),
    role VARCHAR(20) DEFAULT 'utilisateur',
    actif TINYINT(1) NOT NULL DEFAULT 1,
    reset_token VARCHAR(64) NULL,
    reset_token_expire DATETIME NULL
);

-- Table theme
CREATE TABLE theme (
    theme_id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL
);

-- Table regime
CREATE TABLE regime (
    regime_id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL
);

-- Table menu
CREATE TABLE menu (
    menu_id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(100) NOT NULL,
    description TEXT,
    nombre_personne_min INT NOT NULL,
    prix_base DECIMAL(10, 2) NOT NULL,
    stock_disponible INT DEFAULT 0,
    conditions_particulieres TEXT,
    theme_id INT,
    regime_id INT,
    FOREIGN KEY (theme_id) REFERENCES theme(theme_id),
    FOREIGN KEY (regime_id) REFERENCES regime(regime_id)
);

-- Table plat
CREATE TABLE plat (
    plat_id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(100) NOT NULL,
    type ENUM('entrée', 'plat', 'dessert') NOT NULL,
    image_path VARCHAR(255)
);

-- Table compose_menu (pivot menu <-> plat)
CREATE TABLE compose_menu (
    menu_id INT,
    plat_id INT,
    PRIMARY KEY (menu_id, plat_id),
    CONSTRAINT fk_menu_menu FOREIGN KEY (menu_id) REFERENCES menu(menu_id) ON DELETE CASCADE,
    CONSTRAINT fk_menu_plat FOREIGN KEY (plat_id) REFERENCES plat(plat_id) ON DELETE CASCADE
);

-- Table allergene
CREATE TABLE allergene (
    allergene_id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL
);

-- Table plat_allergene (pivot plat <-> allergene)
CREATE TABLE plat_allergene (
    plat_id INT,
    allergene_id INT,
    PRIMARY KEY (plat_id, allergene_id),
    CONSTRAINT fk_plat_allergene_plat FOREIGN KEY (plat_id) REFERENCES plat(plat_id) ON DELETE CASCADE,
    CONSTRAINT fk_plat_allergene_allergene FOREIGN KEY (allergene_id) REFERENCES allergene(allergene_id) ON DELETE CASCADE
);

-- Table commande
CREATE TABLE commande (
    commande_id INT AUTO_INCREMENT PRIMARY KEY,
    date_commande DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_prestation DATE NOT NULL,
    heure_prestation TIME NOT NULL,
    adresse_livraison VARCHAR(255) NOT NULL,
    est_hors_bordeaux BOOLEAN DEFAULT FALSE,
    nombre_personnes INT NOT NULL,
    prix_total_ttc DECIMAL(10,2) NOT NULL,
    statut ENUM('en attente', 'accepté', 'en préparation', 'en cours de livraison', 'livré', 'en attente du retour de matériel', 'terminée', 'annulée') DEFAULT 'en attente',
    motif_annulation TEXT,
    pret_materiel BOOLEAN DEFAULT FALSE,
    materiel_rendu BOOLEAN DEFAULT FALSE,
    utilisateur_id INT,
    menu_id INT,
    CONSTRAINT fk_commande_utilisateur FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(utilisateur_id),
    CONSTRAINT fk_commande_menu FOREIGN KEY (menu_id) REFERENCES menu(menu_id)
);

-- Table suivi_commande (historique des statuts)
CREATE TABLE suivi_commande (
    suivi_id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL,
    statut VARCHAR(50) NOT NULL,
    commentaire TEXT,
    date_modif DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (commande_id) REFERENCES commande(commande_id) ON DELETE CASCADE
);

-- Table avis
CREATE TABLE avis (
    avis_id INT AUTO_INCREMENT PRIMARY KEY,
    note INT CHECK (note BETWEEN 1 AND 5),
    commentaire TEXT,
    est_valide BOOLEAN DEFAULT FALSE,
    date_publication DATETIME DEFAULT CURRENT_TIMESTAMP,
    utilisateur_id INT,
    commande_id INT,
    CONSTRAINT fk_avis_utilisateur FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(utilisateur_id),
    CONSTRAINT fk_avis_commande FOREIGN KEY (commande_id) REFERENCES commande(commande_id) ON DELETE CASCADE
);

-- Table horaire
CREATE TABLE horaire (
    horaire_id INT AUTO_INCREMENT PRIMARY KEY,
    jour_semaine ENUM('lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche') NOT NULL,
    heure_ouverture TIME NOT NULL,
    heure_fermeture TIME NOT NULL,
    est_ferme BOOLEAN DEFAULT FALSE
);