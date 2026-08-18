# Vite & Gourmand — Application Web Traiteur

Application web de commande en ligne pour le traiteur bordelais Vite & Gourmand.  
Développée dans le cadre du TP Développeur Web et Web Mobile (Bac+2 RNCP5) — Studi 2025.

---

## Stack technique

- **Front-end** : HTML5, SCSS (BEM), JavaScript ES Modules (API DOM, aucune `innerHTML`)
- **Back-end** : PHP 8+ orienté objet (MVC, PSR-4, injection de dépendances)
- **Base de données relationnelle** : MySQL 8 (accès via PDO + requêtes préparées)
- **Base de données NoSQL** : MongoDB (journalisation des logs applicatifs)
- **Conteneurisation** : Docker / Docker Compose (services MySQL, MongoDB, PHP, phpMyAdmin, Mailpit)
- **Versioning** : Git / GitHub

---

## Prérequis

- **Option A (recommandée)** : Docker Desktop + Docker Compose
- **Option B (alternative)** : [Laragon](https://laragon.org/download/) (Full recommandé), PHP 8.0+, MySQL 8.0, extension MongoDB (`mongodb`), Composer
- Git

---

## Installation en local

### Option A — Avec Docker (recommandé)

```bash
git clone https://github.com/piqzaa/vite-et-gourmand.git
cd vite-et-gourmand
docker compose up -d --build
```

Le `docker-compose.yml` démarre :

- `db` — MySQL 8, initialisé avec `DataBase/schema.sql` + `DataBase/seed.sql`
- `mongo` — MongoDB (port `27017`, volume persistant), utilisé pour les logs
- `php` — Apache + PHP 8.2 avec extension MongoDB
- `phpmyadmin` — interface web (port `8081`)
- `mailpit` — interceptor de mails (interface `8025`)

L'application est alors accessible sur : `http://localhost:8080`

### Option B — Avec Laragon

Copie le dossier dans `C:/laragon/www/` ou clone directement dedans.

L'application sera accessible sur :

```
http://vite-et-gourmand.test
```

ou

```
http://localhost/Vite-et-gourmand
```

### Créer la base de données (sans Docker)

1. Ouvrir phpMyAdmin (`http://localhost/phpmyadmin`) ou utiliser le client MySQL de votre choix.

2. Créer la base nommée :
   vite_et_gourmand

3. Importer d’abord la **structure** (schéma) puis les **données de seed** :

- Fichier schéma (structure des tables) :
  Database/schema.sql
- Fichier de seed (données initiales : menus, plats, horaires, utilisateurs de test) :
  Database/seed.sql

### Configurer la connexion BDD

Ouvre `config/db.php` (ou `.env` sous Docker) et vérifie les constantes :

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'vite_et_gourmand');
define('DB_USER', 'root');
define('DB_PASS', '');
define('BASE_URL', '/Vite-et-gourmand');
```

Adapte `BASE_URL` selon ton environnement Laragon.

### Compiler le SCSS (optionnel)

Le CSS compilé est déjà présent dans `assets/css/`.  
Si tu veux modifier le SCSS, utilise l'extension **Live Sass Compiler** dans VS Code.

---

## 📦 Installation de la base de données

1. Créer une base de données vide `vite_gourmand` dans phpMyAdmin
2. Importer `database/schema.sql` (structure des tables)
3. Importer `database/seed.sql` (données de test)

### Comptes de test disponibles

| Rôle    | Email                    | Mot de passe |
| ------- | ------------------------ | ------------ |
| Client  | client@mail.com          | Test1234!    |
| Employé | employe@vite-gourmand.fr | Staff1234!   |
| Admin   | jose@vite-gourmand.fr    | Admin1234!   |

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
│   └── scss/         ← Sources SCSS (BEM)
├── config/           ← db.php (connexion PDO + constantes)
├── DataBase/         ← Scripts SQL (schema, seed)
├── docker/           ← Dockerfile PHP, configs
├── docker-compose.yml← Orchestration (MySQL, MongoDB, PHP, phpMyAdmin, Mailpit)
├── src/
│   ├── Controller/   ← Logique des pages
│   ├── Entity/       ← Objets métier
│   ├── Repository/   ← Accès aux données (SQL via PDO, NoSQL via MongoDB)
│   └── Service/      ← Services (Mail, Logger, Auth, Commande, Security...)
├── views/            ← Fichiers de vue (HTML/PHP)
├── vendor/           ← Dépendances Composer
├── composer.json
├── index.php         ← Front Controller
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

En développement, les mails sont interceptés par **Mailpit** (service Docker).  
Interface accessible sur : `http://localhost:8025`

Mails envoyés automatiquement :

- Bienvenue à l'inscription
- Confirmation de commande
- Notification retour de matériel
- Invitation à laisser un avis (commande terminée)
- Lien de réinitialisation du mot de passe

## Journalisation NoSQL (MongoDB)

Les logs applicatifs (authentification, envoi du formulaire de contact, erreurs critiques…) sont enregistrés dans une base **MongoDB** (`vite_et_gourmand_logs`, collection `app_logs`) via le service `LoggerService`. L'accès NoSQL est donc dissocié du stockage relationnel SQL (MySQL) utilisé pour les données métier et les statistiques du tableau de bord.

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
