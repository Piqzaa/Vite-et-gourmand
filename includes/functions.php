<?php

function nav_item(string $lien, string $titre, string $current): string
{
    $classe = 'navbar__link';
    if ($current === $lien) {
        $classe .= ' navbar__link--active';
    }

    return <<<HTML
        <li>
            <a href="$lien" class="$classe">$titre</a>
        </li>
    HTML;
}

$current = basename($_SERVER['SCRIPT_NAME']);
?>