<?php

function nav_item(string $lien, string $titre): string
{
    $classe = 'navbar__link';
    if ((basename($_SERVER['SCRIPT_NAME']) === $lien)) {
        $classe .= ' navbar__link--active';
    }

    return <<<HTML
        <li>
            <a href="$lien" class="$classe">$titre</a>
        </li>
    HTML;
}

?>