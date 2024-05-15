<?php
session_start();

// Vérifier si l'utilisateur est connecté en tant qu'admin
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: connexion_admin.php");
    exit();
}

include("includes/connectBDD.php");

// Vérifier si l'ID de la musique est spécifié dans l'URL
if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    $id = htmlspecialchars(trim($_GET["id"])); // Échapper les caractères spéciaux pour éviter les attaques
    $sql = "SELECT * FROM album WHERE id_album = ?";
    
    // Établir la connexion à la base de données
    $connexion = connecterBaseDeDonnees();
    
    // Préparer et exécuter la requête
    if ($stmt = $connexion->prepare($sql)) {
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // Pré-remplir les champs du formulaire avec les données existantes
            $titre = isset($row["title_album"]) ? htmlspecialchars($row["title_album"]) : "";
            $artiste = isset($row["artist_album"]) ? htmlspecialchars($row["artist_album"]) : "";
            $time_song = isset($row["time_album"]) ? $row["time_album"] : "00:00:00";
            $type = isset($row["type_album"]) ? htmlspecialchars($row["time_album"]) : ""; 
            list($duree_heures, $duree_minutes, $duree_secondes) = explode(':', $time_album);
            $soundcloud_link = isset($row["alink_soundcloud"]) ? htmlspecialchars($row["alink_soundcloud"]) : "";
            $spotify_link = isset($row["alink_spotify"]) ? htmlspecialchars($row["alink_spotify"]) : "";
            $apple_link = isset($row["alink_apple"]) ? htmlspecialchars($row["alink_apple"]) : "";
            $deezer_link = isset($row["alink_deezer"]) ? htmlspecialchars($row["alink_deezer"]) : "";
            $amazon_link = isset($row["alink_amazon"]) ? htmlspecialchars($row["alink_amazon"]) : "";
            $youtube_link = isset($row["alink_youtube"]) ? htmlspecialchars($row["alink_youtube"]) : "";
            $cover_song = isset($row["cover_album"]) ? htmlspecialchars($row["cover_album"]) : ""; // Chemin de l'image actuelle
            $description = isset($row["description_album"]) ? htmlspecialchars($row["description_album"]) : ""; // Description existante
            $release_date = isset($row["date_album"]) ? htmlspecialchars($row["date_album"]) : ""; // Date de sortie existante
        } else {
            echo "Aucune musique trouvée.";
            exit();
        }
    } else {
        echo "Oops! Une erreur s'est produite. Veuillez réessayer plus tard.";
        exit();
    }
} else {
    echo "ID de la musique non spécifié.";
    exit();
}