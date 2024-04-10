<?php
include("includes/connectBDD.php");

$id_musique = isset($_GET['id']) ? $_GET['id'] : die("ID de la musique non spécifié.");

$connexion = connecterBaseDeDonnees();
$sql = "SELECT * FROM musique WHERE id_song = :id";
$stmt = $connexion->prepare($sql);
$stmt->bindParam(':id', $id_musique);
$stmt->execute();
$musique = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la musique</title>
    <link rel="stylesheet" href="assets/musique.css">
    <style>
        /* Style du bouton de retour */
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="left">
        <img src="<?php echo $musique['cover_song']; ?>" alt="Cover de la musique" class="cover-image">
        <div class="links">
            <?php if (!empty($musique['soundcloud_link'])) echo '<a href="' . $musique['soundcloud_link'] . '" target="_blank"><img src="images/soundcloud.png" alt="SoundCloud"></a>'; ?>
            <?php if (!empty($musique['spotify_link'])) echo '<a href="' . $musique['spotify_link'] . '" target="_blank"><img src="images/spotify.png" alt="Spotify"></a>'; ?>
            <?php if (!empty($musique['deezer_link'])) echo '<a href="' . $musique['deezer_link'] . '" target="_blank"><img src="images/deezer.png" alt="Deezer"></a>'; ?>
            <?php if (!empty($musique['apple_link'])) echo '<a href="' . $musique['apple_link'] . '" target="_blank"><img src="images/applemusic.png" alt="Apple Music"></a>'; ?>
            <?php if (!empty($musique['youtube_link'])) echo '<a href="' . $musique['youtube_link'] . '" target="_blank"><img src="images/ytmusic.png" alt="YouTube Music"></a>'; ?>
            <?php if (!empty($musique['amazon_link'])) echo '<a href="' . $musique['amazon_link'] . '" target="_blank"><img src="images/amazonmusic.png" alt="Amazon Music"></a>'; ?>
        </div>
    </div>
    <div class="right">
        <h1><?php echo $musique['title_song']; ?></h1>
        <p>Artiste: <?php echo $musique['artist_song']; ?></p>
        <?php if (!empty($musique['feat_song'])) echo '<p>Feat: ' . $musique['feat_song'] . '</p>'; ?>
        <?php if (!empty($musique['description_song'])) echo '<p>Description: ' . $musique['description_song'] . '</p>'; ?>
        
        <!-- Bouton de retour -->
        <a href="index.php" class="button">Retour</a>
    </div>
</div>

</body>
</html>