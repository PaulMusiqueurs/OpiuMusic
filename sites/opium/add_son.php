<?php
// Vérifier si l'utilisateur est connecté en tant qu'admin
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: connexion_admin.php");
    exit();
}

// Inclure le fichier de connexion à la base de données
include("includes/connectBDD.php");

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $titre = $_POST["titre"];
    $artiste = $_POST["artiste"];
    $duree_heures = intval($_POST["duree_heures"]);
    $duree_minutes = intval($_POST["duree_minutes"]);
    $duree_secondes = intval($_POST["duree_secondes"]);
    $duree = sprintf("%02d:%02d:%02d", $duree_heures, $duree_minutes, $duree_secondes); // Formater la durée
    $tempo = intval($_POST["tempo"]);
    $description = $_POST["description"]; // Nouveau champ description
    $release_date = $_POST["release_date"]; // Nouveau champ date de sortie
    
    // Traitement de l'image
    $imageFileName = $_FILES["image"]["name"];
    $imageFilePath = "images/" . $imageFileName; // Chemin relatif de l'image
    move_uploaded_file($_FILES["image"]["tmp_name"], $imageFilePath);

    $invites = $_POST["invites"];
    $style = $_POST["style"];
    $soundcloud_link = $_POST["soundcloud_link"];
    $spotify_link = $_POST["spotify_link"];
    $apple_link = $_POST["apple_link"];
    $deezer_link = $_POST["deezer_link"];
    $amazon_link = $_POST["amazon_link"];
    $youtube_link = $_POST["youtube_link"];
    
    // Insérer les données dans la base de données en utilisant une requête préparée
    $connexion = connecterBaseDeDonnees();
    $requete = $connexion->prepare("INSERT INTO musique (title_song, artist_song, time_song, tempo_song, cover_song, feat_song, style_song, soundcloud_link, spotify_link, apple_link, deezer_link, amazon_link, youtube_link, description_song, release_song) VALUES (:titre, :artiste, :duree, :tempo, :imageFilePath, :invites, :style, :soundcloud_link, :spotify_link, :apple_link, :deezer_link, :amazon_link, :youtube_link, :description, :release_date)");
    $requete->bindParam(':titre', $titre, PDO::PARAM_STR);
    $requete->bindParam(':artiste', $artiste, PDO::PARAM_STR);
    $requete->bindParam(':duree', $duree, PDO::PARAM_STR);
    $requete->bindParam(':tempo', $tempo, PDO::PARAM_INT);
    $requete->bindParam(':imageFilePath', $imageFilePath, PDO::PARAM_STR); // Stocker le chemin relatif de l'image
    $requete->bindParam(':invites', $invites, PDO::PARAM_STR);
    $requete->bindParam(':style', $style, PDO::PARAM_STR);
    $requete->bindParam(':soundcloud_link', $soundcloud_link, PDO::PARAM_STR);
    $requete->bindParam(':spotify_link', $spotify_link, PDO::PARAM_STR);
    $requete->bindParam(':apple_link', $apple_link, PDO::PARAM_STR);
    $requete->bindParam(':deezer_link', $deezer_link, PDO::PARAM_STR);
    $requete->bindParam(':amazon_link', $amazon_link, PDO::PARAM_STR);
    $requete->bindParam(':youtube_link', $youtube_link, PDO::PARAM_STR);
    $requete->bindParam(':description', $description, PDO::PARAM_STR); // Nouveau champ description
    $requete->bindParam(':release_date', $release_date, PDO::PARAM_STR); // Nouveau champ date de sortie
    
    $requete->execute();

    // Récupérer l'ID de la dernière insertion
    $id_song = $connexion->lastInsertId();

    // Renommer le fichier image avec l'ID de la musique
    $newImageFileName = $id_song . ".jpg"; // ou tout autre extension d'image que vous utilisez
    $newImageFilePath = "images/" . $newImageFileName; // Nouveau chemin relatif de l'image
    rename($imageFilePath, $newImageFilePath); // Renommer le fichier

    // Mettre à jour le chemin de l'image dans la base de données avec le nouveau nom de fichier
    $requeteUpdateImage = $connexion->prepare("UPDATE musique SET cover_song = :newImageFilePath WHERE id_song = :id_song");
    $requeteUpdateImage->bindParam(':newImageFilePath', $newImageFilePath, PDO::PARAM_STR);
    $requeteUpdateImage->bindParam(':id_song', $id_song, PDO::PARAM_INT);
    $requeteUpdateImage->execute();

    $requete->closeCursor();
    $requeteUpdateImage->closeCursor();

    header("Location: crud_son.php?success=true");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une musique</title>
    <link rel="stylesheet" href="assets/add_son.css">
</head>
<body>

<h1>Ajouter une musique</h1>

<form action="add_son.php" method="post" enctype="multipart/form-data">
    <label for="titre">Titre :</label><br>
    <input type="text" id="titre" name="titre" required><br><br>

    <label for="artiste">Artiste :</label><br>
    <input type="text" id="artiste" name="artiste" required><br><br>

    <label for="duree_heures">Durée (heures) :</label><br>
    <input type="number" id="duree_heures" name="duree_heures" required><br><br>

    <label for="duree_minutes">Durée (minutes) :</label><br>
    <input type="number" id="duree_minutes" name="duree_minutes" required><br><br>
    
    <label for="duree_secondes">Durée (secondes) :</label><br>
    <input type="number" id="duree_secondes" name="duree_secondes" required><br><br>

    <label for="image">Image :</label><br>
    <input type="file" id="image" name="image" required><br><br>

    <label for="tempo">Tempo (bpm) :</label><br>
    <input type="text" id="tempo" name="tempo"><br><br>

    <label for="invites">Invité(e)s :</label><br>
    <input type="text" id="invites" name="invites"><br><br>
    
    <label for="style">Style de musique :</label><br>
    <input type="text" id="style" name="style"><br><br>
    
    <label for="soundcloud_link">Lien Soundcloud :</label><br>
    <input type="url" id="soundcloud_link" name="soundcloud_link"><br><br>
    
    <label for="spotify_link">Lien Spotify :</label><br>
    <input type="url" id="spotify_link" name="spotify_link"><br><br>
    
    <label for="apple_link">Lien Apple Music :</label><br>
    <input type="url" id="apple_link" name="apple_link"><br><br>
    
    <label for="deezer_link">Lien Deezer :</label><br>
    <input type="url" id="deezer_link" name="deezer_link"><br><br>
    
    <label for="amazon_link">Lien Amazon Music :</label><br>
    <input type="url" id="amazon_link" name="amazon_link"><br><br>
    
    <label for="youtube_link">Lien YouTube :</label><br>
    <input type="url" id="youtube_link" name="youtube_link"><br><br>
    
    <label for="description">Description :</label><br>
    <textarea id="description" name="description"></textarea><br><br>
    
    <label for="release_date">Date de sortie :</label><br>
    <input type="date" id="release_date" name="release_date" required><br><br>

    <input type="submit" value="Ajouter">
</form>

</body>
</html>