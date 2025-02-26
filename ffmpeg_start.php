<?php

require_once __DIR__ . '/config.php';

// FFMPEG
// ! IMG FFMPEG CONV

use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;

// Initialiser FFMpeg avec gestion d'erreurs
$ffmpeg = FFMpeg::create([
    'ffmpeg.binaries'  => __DIR__ . '/dist/ffmpeg/bin/ffmpeg.exe',   // Chemin absolu vers ffmpeg.exe
    'ffprobe.binaries' => __DIR__ . '/dist/ffmpeg/bin/ffprobe.exe',  // Chemin absolu vers ffprobe.exe
    'timeout'          => 3600,  // Timeout (1 heure)
    'ffmpeg.threads'   => 12,    // Nombre de threads à utiliser (optionnel)
]);

// Configuration des chemins
$sourceDir = rtrim($path . '/public/public_data/', '/');
$destDir = rtrim($path . '/public/temp/thumbnail/', '/');

// Fonction pour scanner les dossiers
function scanDirectory($dir)
{
    $files = [];
    try {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $files[] = $file->getPathname();
            }
        }
    } catch (Exception $e) {
        logs(__DIR__ . '/server.log', "Erreur lors du scan du dossier : " . $e->getMessage(), 500, "ERROR");
    }
    return $files;
}

// Fonction pour vérifier si un fichier est une vidéo
function isVideo($file)
{
    $videoExtensions = ['mp4', 'avi', 'mov', 'mkv', 'flv', 'webm'];
    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    return in_array($extension, $videoExtensions);
}

// Fonction principale pour générer les miniatures

function generateThumbnails($sourceDir, $destDir, $ffmpeg)
{
    $files = scanDirectory($sourceDir);
    $totalFiles = count($files); // Total des fichiers scannés
    $successCount = 0;
    $failureCount = 0;
    $processedCount = 0; // Compteur pour suivre les fichiers traités

    foreach ($files as $file) {
        if (isVideo($file)) {
            // Construire le chemin relatif de la vidéo
            $relativePath = str_replace($sourceDir, '', $file);
            $thumbnailPath = rtrim($destDir . pathinfo($relativePath, PATHINFO_DIRNAME), '/');

            // Créer les répertoires manquants dans temp/thumbnail/
            if (!is_dir($thumbnailPath)) {
                if (!mkdir($thumbnailPath, 0777, true)) {
                    logs(__DIR__ . '/server.log', "Error while creating thumbnail directory : $thumbnailPath", 500, "ERROR");
                    continue;
                }
            }

            // Générer la miniature
            $thumbnailFile = $thumbnailPath . '/' . pathinfo($relativePath, PATHINFO_FILENAME) . '.jpg';
            if (!file_exists($thumbnailFile)) {  // Évite de régénérer la miniature si elle existe déjà
                try {
                    // Ouvrir la vidéo
                    $video = $ffmpeg->open($file);

                    // Extraire une frame à la 2ème seconde
                    $frame = $video->frame(TimeCode::fromSeconds(2));

                    // Sauvegarder avec compression (réduction de la qualité à 50%)
                    $frame->save($thumbnailFile, ['quality' => 50]);

                    logs(__DIR__ . '/server.log', "Thumbnail created for: $file", 200, "INFO");
                    $successCount++;
                } catch (Exception $e) {
                    logs(__DIR__ . '/server.log', "Failed to create thumbnail for: $file. Error: " . $e->getMessage(), 500, "ERROR");
                    $failureCount++;
                }
            }

            // Met à jour le compteur de fichiers traités
            $processedCount++;

            // Ajoute un log toutes les 8 vidéos traitées
            if ($processedCount % 8 == 0 || $processedCount === $totalFiles) {
                $percentage = round(($processedCount / $totalFiles) * 100, 2);
                logs(__DIR__ . '/server.log', "Progress: $processedCount/$totalFiles files processed ($percentage%)", 200, "INFO");
            }
        }
    }

    // Log the summary
    logs(__DIR__ . '/server.log', "Thumbnail generation completed. Successes: $successCount, Failures: $failureCount", 200, "INFO");
}


// Exécuter la génération des miniatures
generateThumbnails($sourceDir, $destDir, $ffmpeg);
