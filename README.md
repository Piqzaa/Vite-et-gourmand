# Vite & Gourmand — Application Web Traiteur

Application web de commande en ligne pour le traiteur bordelais Vite & Gourmand.  
Développée dans le cadre du TP Développeur Web et Web Mobile (Bac+2 RNCP5) — Studi 2025.

---

## Stack technique

- **Front-end** : HTML5, SCSS (BEM), JavaScript ES Modules
- **Back-end** : PHP 8+ avec PDO
- **Base de données relationnelle** : MySQL / MariaDB
- **Environnement local** : Laragon
- **Versioning** : Git / GitHub

---

## Prérequis

- [Laragon](https://laragon.org/download/) (Full recommandé)
- PHP 8.0+
- MySQL 8.0 / MariaDB 10.4+
- Git

---

## Installation en local

### 1. Cloner le dépôt

```bash
git clone https://github.com/piqzaa/vite-et-gourmand.git
cd vite-et-gourmand
```

### 2. Placer le projet dans Laragon

Copie le dossier dans `C:/laragon/www/` ou clone directement dedans.

L'application sera accessible sur :

```
http://vite-et-gourmand.test
```

ou

```
http://localhost/Vite-et-gourmand
```

### 3. Créer la base de données

Ouvre phpMyAdmin (`http://localhost/phpmyadmin`) et crée une base nommée `vite_et_gourmand`.

Ensuite importe le fichier SQL :

```
Database/vite_et_gourmand.sql
```

Ce fichier contient la structure complète des tables et les données de seed (menus, plats, horaires, utilisateurs de test).

### 4. Configurer la connexion BDD

Ouvre `assets/php/config/db.php` et vérifie les constantes :

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'vite_et_gourmand');
define('DB_USER', 'root');
define('DB_PASS', '');
define('BASE_URL', '/Vite-et-gourmand');
```

Adapte `BASE_URL` selon ton environnement Laragon.

### 5. Compiler le SCSS (optionnel)

Le CSS compilé est déjà présent dans `assets/css/`.  
Si tu veux modifier le SCSS, utilise l'extension **Live Sass Compiler** dans VS Code.

---

## Comptes de test

| Rôle           | Email                     | Mot de passe |
| -------------- | ------------------------- | ------------ |
| Administrateur | jose@viteetgourmand.fr    | Admin1234!   |
| Employé        | employe@viteetgourmand.fr | Employe1234! |
| Utilisateur    | client@mail.com           | Client1234!  |

---

## Structure du projet

```
Vite-et-gourmand/
├── assets/
│   ├── css/          ← CSS compilé depuis SCSS
│   ├── img/          ← Images
│   ├── js/
│   │   ├── main.js   ← Point d'entrée JS
│   │   └── modules/  ← Modules JS (burger, filter, stepper...)
│   ├── php/
│   │   ├── config/   ← db.php (connexion PDO + constantes)
│   │   ├── includes/ ← session.php, mailer.php
│   │   ├── auth/     ← login, logout, register, reset-password
│   │   ├── commande/ ← create, annuler, update-statut
│   │   ├── user/     ← update profil
│   │   ├── admin/    ← create-employe, toggle-employe, get-stats
│   │   ├── menu/     ← create, update, delete
│   │   ├── plat/     ← create, delete
│   │   ├── avis/     ← create, moderer
│   │   ├── horaires/ ← update
│   │   └── contact/  ← send
│   │   └── api/      ← Endpoints JSON pour le filtrage dynamique (Fetch API)
│   └── scss/         ← Sources SCSS (BEM)
├── includes/
│   ├── head.php      ← Balises <head>
│   ├── header.php    ← Navigation
│   ├── footer.php    ← Pied de page dynamique
│   └── layout.php    ← layout pour patter ob_start
├── Database/
│   └── vite_et_gourmand.sql
├── index.php
├── menus.php
├── menu-create.php
├── menu-edit.php
├── menu-detail.php
├── commande.php
├── connexion.php
├── inscription.php
├── espace-utilisateur.php
├── espace-employe.php
├── espace-admin.php
├── contact.php
├── mentions-legales.php
├── cgv.php
├── plat-create.php
├── reset-password.php
└── README.md
```

---

## Fonctionnalités principales

**Visiteur**

- Consultation des menus avec filtres dynamiques (prix, thème, régime, personnes)
- Création de compte
- Page de contact

**Utilisateur connecté**

- Commande de menu en 3 étapes (stepper)
- Calcul automatique du prix (remise 10% dès +5 personnes, frais livraison hors Bordeaux)
- Espace personnel : suivi commandes, modification profil, annulation, avis

**Employé**

- Gestion des commandes (statuts, annulation avec motif)
- CRUD menus et plats
- Modération des avis clients
- Gestion des horaires

**Administrateur**

- Tout ce que peut faire un employé
- Création et désactivation de comptes employés
- Tableau de bord statistiques (CA, commandes par menu, panier moyen)
- Graphique filtrable par menu et période

---

## Mails transactionnels

En développement, les mails sont interceptés par **Mailpit** (intégré à Laragon).  
Interface accessible sur : `http://localhost:8025`

Mails envoyés automatiquement :

- Bienvenue à l'inscription
- Confirmation de commande
- Notification retour de matériel
- Invitation à laisser un avis (commande terminée)
- Lien de réinitialisation du mot de passe

## API — Filtrage des menus

L'application utilise une API interne pour filtrer les menus sans recharger la page.

**Endpoint :** `GET /assets/php/api/get-menus.php`

### Paramètres acceptés (Query String)

| Paramètre   | Type    | Description                   |
| ----------- | ------- | ----------------------------- |
| `prix_min`  | `float` | Prix minimum du menu          |
| `prix_max`  | `float` | Prix maximum du menu          |
| `theme`     | `int`   | ID du thème (table `theme`)   |
| `regime`    | `int`   | ID du régime (table `regime`) |
| `personnes` | `int`   | Capacité minimum de personnes |

### Exemple de réponse (JSON)

```json
[
  {
    "menu_id": 1,
    "titre": "Buffet Champêtre",
    "description": "Un assortiment de produits du terroir...",
    "nombre_personne_min": 10,
    "prix_base": "25.00",
    "stock_disponible": 50,
    "theme": "Mariage",
    "regime": "Classique"
  }
]
```
