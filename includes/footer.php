<?php
require_once __DIR__ . '/../assets/php/config/db.php';
$pdo = getDB();
$horaires = $pdo->query('SELECT * FROM horaire ORDER BY horaire_id ASC')->fetchAll();
?>
<footer class="footer">
    <div class="footer__container">
        <div class="footer__col">
            <p class="footer__logo">Vite <em>&</em> Gourmand</p>
            <p class="footer__desc">
                Traiteur bordelais depuis 1999. Julie &amp; José cuisinent avec
                passion pour tous vos événements.
            </p>
        </div>
        <div class="footer__col">
            <h4 class="footer__heading">Horaires</h4>
            <ul class="footer__hours">
                <?php foreach ($horaires as $h): ?>
                <li>
                    <span><?= ucfirst($h['jour_semaine']) ?></span>
                    <span>
                        <?php if ($h['heure_ouverture'] === $h['heure_fermeture']): ?>
                            Fermé
                        <?php else: ?>
                            <?= substr($h['heure_ouverture'], 0, 5) ?>h – <?= substr($h['heure_fermeture'], 0, 5) ?>h
                        <?php endif; ?>
                    </span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="footer__col">
            <h4 class="footer__heading">Navigation</h4>
            <ul class="footer__nav">
                <li><a href="index.php">Accueil</a></li>
                <li><a href="menus.php">Nos menus</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </div>
        <div class="footer__col">
            <h4 class="footer__heading">Légal</h4>
            <ul class="footer__nav">
                <li><a href="mentions-legales.php">Mentions légales</a></li>
                <li><a href="cgv.php">Conditions générales de vente</a></li>
            </ul>
        </div>
    </div>
    <div class="footer__bottom">
        <p>&copy; 2025 Vite &amp; Gourmand · Bordeaux · Tous droits réservés</p>
    </div>
</footer>