<?php

function sendMail(string $to, string $toName, string $subject, string $body): bool {
    $headers = "From: Vite & Gourmand <noreply@viteetgourmand.fr>\r\n"
             . "Reply-To: contact@viteetgourmand.fr\r\n"
             . "Content-Type: text/plain; charset=UTF-8\r\n"
             . "MIME-Version: 1.0";

    return mail($to, $subject, $body, $headers);
}

function mailBienvenue(string $to, string $prenom): bool {
    $subject = 'Bienvenue chez Vite & Gourmand !';
    $body    = "Bonjour $prenom,\n\n"
             . "Votre compte a bien été créé sur Vite & Gourmand.\n"
             . "Vous pouvez dès maintenant parcourir nos menus et passer commande.\n\n"
             . "À bientôt,\n"
             . "Julie & José\n"
             . "Vite & Gourmand";

    return sendMail($to, $prenom, $subject, $body);
}

function mailConfirmationCommande(string $to, string $prenom, array $commande): bool {
    $subject = 'Confirmation de votre commande #' . $commande['id'];
    $body    = "Bonjour $prenom,\n\n"
             . "Votre commande a bien été reçue. Voici le récapitulatif :\n\n"
             . "Menu : " . $commande['menu'] . "\n"
             . "Date de prestation : " . $commande['date'] . "\n"
             . "Adresse : " . $commande['adresse'] . "\n"
             . "Nombre de personnes : " . $commande['personnes'] . "\n"
             . "Total : " . $commande['total'] . "€\n\n"
             . "Vous recevrez une confirmation dès que notre équipe aura validé votre commande.\n\n"
             . "À bientôt,\n"
             . "Julie & José\n"
             . "Vite & Gourmand";

    return sendMail($to, $prenom, $subject, $body);
}

function mailRetourMateriel(string $to, string $prenom): bool {
    $subject = 'Retour de matériel — Vite & Gourmand';
    $body    = "Bonjour $prenom,\n\n"
             . "Votre commande est terminée. Du matériel vous a été prêté lors de la prestation.\n\n"
             . "Merci de le restituer dans un délai de 10 jours ouvrés.\n"
             . "Passé ce délai, des frais de 600€ vous seront facturés conformément aux CGV.\n\n"
             . "Pour organiser le retour, contactez-nous :\n"
             . "Email : contact@viteetgourmand.fr\n"
             . "Tél : 05 56 87 42 13\n\n"
             . "Cordialement,\n"
             . "Julie & José\n"
             . "Vite & Gourmand";

    return sendMail($to, $prenom, $subject, $body);
}

function mailCommandeTerminee(string $to, string $prenom, int $commandeId): bool {
    $subject = 'Votre commande est terminée — Donnez votre avis !';
    $body    = "Bonjour $prenom,\n\n"
             . "Nous espérons que votre prestation s'est déroulée à merveille !\n\n"
             . "Votre avis nous est précieux. Connectez-vous à votre espace personnel\n"
             . "pour laisser un commentaire sur votre commande #$commandeId :\n\n"
             . "Vite & Gourmand — Espace utilisateur\n\n"
             . "Merci pour votre confiance,\n"
             . "Julie & José\n"
             . "Vite & Gourmand";

    return sendMail($to, $prenom, $subject, $body);
}

function mailResetPassword(string $to, string $prenom, string $resetUrl): bool {
    $subject = 'Réinitialisation de votre mot de passe';
    $body    = "Bonjour $prenom,\n\n"
             . "Vous avez demandé à réinitialiser votre mot de passe.\n\n"
             . "Cliquez sur le lien ci-dessous (valable 1 heure) :\n"
             . $resetUrl . "\n\n"
             . "Si vous n'êtes pas à l'origine de cette demande, ignorez cet email.\n\n"
             . "Cordialement,\n"
             . "Vite & Gourmand";

    return sendMail($to, $prenom, $subject, $body);
}
