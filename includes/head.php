<?php
// Headers de sécurité HTTP
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Permissions-Policy: geolocation=(), microphone=(), camera=()");
?>
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta
    name="description"
    content="<?= isset($description) ? htmlspecialchars($description) : "Découvrez tous les menus traiteur de Vite & Gourmand, votre solution de repas rapide et délicieux à Bordeaux. Commandez en ligne pour une livraison rapide ou un retrait en magasin." ?>"
  />
  <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src https://fonts.gstatic.com; img-src 'self' data:;">
  <title><?= isset($title) ? htmlspecialchars($title) . ' — Vite & Gourmand' : 'Vite & Gourmand' ?></title>
  <link rel="icon" type="image/png" sizes="32x32" href="assets/img/favicon.png">
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=DM+Sans:wght@300;400;500&display=swap"
    rel="stylesheet"
  />
  <link rel="stylesheet" href="assets/css/main.css" />
  <script src="assets/js/main.js" type="module" defer></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js" type="module" defer></script>
</head>