<?php

namespace App\Service;

use Exception;

class FileService {
    private string $uploadDir;

    public function __construct(string $uploadDir) {
        $this->uploadDir = rtrim($uploadDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    /**
     * Upload un fichier image
     * 
     * @param array $file Le contenu de $_FILES['key']
     * @param string $prefix Préfixe pour le nom du fichier
     * @return string Le nom du fichier généré
     * @throws Exception
     */
    public function uploadImage(array $file, string $prefix = 'img_'): string {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Erreur lors du transfert du fichier.");
        }

        $fileSize = $file['size'];

        // Validation du format via le contenu réel (pas l'extension déclarée par le client)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $fileType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($fileType, $allowedTypes)) {
            throw new Exception("Format d'image non supporté (JPG, PNG, WEBP acceptés).");
        }

        // Validation de la taille (ex: 2Mo)
        if ($fileSize > 2 * 1024 * 1024) {
            throw new Exception("Le fichier est trop volumineux (max 2Mo).");
        }

        // Génération d'un nom unique
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFileName = uniqid($prefix, true) . '.' . $extension;

        // Création du dossier s'il n'existe pas
        if (!is_dir($this->uploadDir)) {
            if (!mkdir($this->uploadDir, 0755, true)) {
                throw new Exception("Impossible de créer le dossier d'upload.");
            }
        }

        // Déplacement du fichier temporaire
        if (!move_uploaded_file($file['tmp_name'], $this->uploadDir . $newFileName)) {
            throw new Exception("L'enregistrement du fichier a échoué.");
        }

        return $newFileName;
    }

    /**
     * Supprime un fichier physique
     */
    public function deleteFile(string $fileName): bool {
        $path = $this->uploadDir . $fileName;
        if (file_exists($path) && is_file($path)) {
            return unlink($path);
        }
        return false;
    }

    /**
     * Retourne le chemin complet du dossier d'upload
     */
    public function getUploadDir(): string {
        return $this->uploadDir;
    }
}
