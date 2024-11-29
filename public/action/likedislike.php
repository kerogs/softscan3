<?php

require_once '../../config.php';

// Récupérer les paramètres GET
$likeGet = isset($_GET['like']) ? (int)$_GET['like'] : 0;
$dislikeGet = isset($_GET['dislike']) ? (int)$_GET['dislike'] : 0;
$url = isset($_GET['url']) ? urldecode($_GET['url']) : null;

logs('../../server.log', "Like : $likeGet, dislike : $dislikeGet, url : $url", 200, "INFO");

if ($url) {
    try {
        // Lire les données du fichier JSON
        $statsFile = '../temp/stats.json';
        
        // Vérifier si le fichier existe
        if (!file_exists($statsFile)) {
            file_put_contents($statsFile, json_encode([]));
        }

        // Charger les données JSON existantes
        $stats = json_decode(file_get_contents($statsFile), true);

        // Vérifier si l'URL existe dans le fichier JSON
        $urlFound = false;
        foreach ($stats as &$entry) {
            if ($entry['url'] === $url) { // Comparer l'URL encodée
                $urlFound = true;

                // Vérifier le vote like ou dislike et l'incrémenter
                if ($likeGet === 1 && !isset($_COOKIE[urlencode($url)])) { // Vérifier avec l'URL encodée
                    $entry['like'] += 1;
                    // Enregistrer le vote dans un cookie avec la valeur 'like'
                    setcookie(urlencode($url), 'like', time() + (86400 * 30), "/"); // Expire dans 30 jours
                    logs('../../server.log', "Like ajouté pour l'URL: $url", 200, "INFO");
                } elseif ($dislikeGet === 1 && !isset($_COOKIE[urlencode($url)])) {
                    $entry['dislike'] += 1;
                    // Enregistrer le vote dans un cookie avec la valeur 'dislike'
                    setcookie(urlencode($url), 'dislike', time() + (86400 * 30), "/"); // Expire dans 30 jours
                    logs('../../server.log', "Dislike ajouté pour l'URL: $url", 200, "INFO");
                } else {
                    logs('../../server.log', "Vote déjà enregistré pour l'URL: $url", 400, "WARNING");
                }
                break;
            }
        }

        // Si l'URL n'existe pas, la créer dans le fichier JSON
        if (!$urlFound) {
            $newEntry = [
                'url' => $url, // Sauvegarder l'URL encodée
                'like' => $likeGet == 1 ? 1 : 0,
                'dislike' => $dislikeGet == 1 ? 1 : 0,
                'vue' => 0 // La vue n'est pas modifiée ici
            ];

            // Ajouter la nouvelle entrée
            $stats[] = $newEntry;

            // Enregistrer le vote dans un cookie
            setcookie(urlencode($url), $likeGet == 1 ? 'like' : 'dislike', time() + (86400 * 30), "/");
            logs('../../server.log', "Nouvelle URL ajoutée : $url", 201, "INFO");
        }

        // Sauvegarder les nouvelles données dans le fichier JSON
        if (file_put_contents($statsFile, json_encode($stats, JSON_PRETTY_PRINT))) {
            logs('../../server.log', "Données mises à jour pour l'URL : $url", 200, "INFO");
        } else {
            logs('../../server.log', "Erreur lors de la mise à jour des données pour l'URL : $url", 500, "ERROR");
        }

        // Rediriger vers view.php avec l'URL encodée
        if(is_numeric($_GET['index']) && $_GET['verticalscroll']) {
            header("Location: ../vertiscroll?dir=" . $_GET['vertidir'] .'&index='.$_GET['index']);
            exit();
        }
        else{
            header("Location: ../view.php?url=" . urlencode($url).'&viewCounter=noCount');
            exit();
        }
        
    } catch (Exception $e) {
        logs('../../server.log', "Erreur : " . $e->getMessage(), 500, "ERROR");
        exit("Erreur survenue. Veuillez vérifier les logs.");
    }
} else {
    logs('../../server.log', "URL non définie.", 400, "ERROR");
    exit("URL non définie.");
}
