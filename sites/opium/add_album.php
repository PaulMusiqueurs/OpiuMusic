<?php
// Vérifier si l'utilisateur est connecté en tant qu'admin
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: connexion_admin.php");
    exit();
}

// Inclure le fichier de connexion à la base de données
include("includes/connectBDD.php");

if ($_SERVER["RESQUEST_METHOD"] == "POST") {
    $titre = $_POST["titre"];
    $artiste = $_POST["artiste"];
    $description = $_POST["description"];
    $release_date = $_POST["release_date"];

    $imageFileName = $_FILES["image"]["name"];
    $imageFilePath = "images/" . $imageFileName;
    move_uploaded_file($_FILES["image"]["tmp_name"], $imageFilePath);

    $style = $_POST["style"];
    $soundcloud_link = $_POST["soundcloud_link"];
    $spotify_link = $_POST["spotify_link"];
    $apple_link = $_POST["apple_link"];
    $deezer_link = $_POST["deezer_link"];
    $amazon_link = $_POST["amazon_link"];
    $youtube_link = $_POST["youtube_link"];


    $connexion = connecterBaseDeDonnees();
    $requete = $connexion->prepare("INSERT INTO album (title_album, artist_album, cover_album, description_album, date_album, alink_soundcloud, alink_spotify, alink_apple, alink_deezer, alink_amazon, alink_youtube) VALUES (:titre, :artiste, :imageFilePath, :description, :release_date, :soundcloud_link, :spotify_link, :apple_link, :deezer_link, :amazon_link, :youtube_link)");
    $requete->bindParam(':titre', $titre, PDO::PARAM_STR);
    $requete->bindParam(':artiste', $artiste, PDO::PARAM_STR);
    $requete->bindParam(':imageFilePath', $imageFilePath, PDO::PARAM_STR); // Stocker le chemin relatif de l'image
    $requete->bindParam(':soundcloud_link', $soundcloud_link, PDO::PARAM_STR);
    $requete->bindParam(':spotify_link', $spotify_link, PDO::PARAM_STR);
    $requete->bindParam(':apple_link', $apple_link, PDO::PARAM_STR);
    $requete->bindParam(':deezer_link', $deezer_link, PDO::PARAM_STR);
    $requete->bindParam(':amazon_link', $amazon_link, PDO::PARAM_STR);
    $requete->bindParam(':youtube_link', $youtube_link, PDO::PARAM_STR);
    $requete->bindParam(':description', $description, PDO::PARAM_STR); // Nouveau champ description
    $requete->bindParam(':release_date', $release_date, PDO::PARAM_STR); // Nouveau champ date de sortie
    
    $requete->execute();
}