<?php
include("includes/connectBDD.php");

// Vérifiez si un ID de musique est spécifié dans l'URL
$id_musique = isset($_GET['id']) ? $_GET['id'] : die("ID de la musique non spécifié.");

// Connectez-vous à la base de données
$connexion = connecterBaseDeDonnees();

// Préparez et exécutez la requête SQL
$sql = "SELECT * FROM musique WHERE id_song = :id";
$stmt = $connexion->prepare($sql);
$stmt->bindParam(':id', $id_musique);
$stmt->execute();

// Récupérez le résultat de la requête
$musique = $stmt->fetch(PDO::FETCH_ASSOC);

// Vérifiez si la musique a été trouvée
if ($musique === false) {
    // Si la musique n'a pas été trouvée, affichez un message
    echo "Aucune musique ne correspond à l'ID spécifié.";
    exit();
}

// Fonction pour convertir la date du format AAAA-MM-JJ à JJ-MM-AAAA
function convertirDate($date) {
    // Divise la date en composants (AAAA-MM-JJ)
    $dateComponents = explode('-', $date);
    // Retourne la date dans le format JJ-MM-AAAA
    return $dateComponents[2] . '-' . $dateComponents[1] . '-' . $dateComponents[0];
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
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
            <?php if (!empty($musique['soundcloud_link'])): ?>
                <a href="<?php echo $musique['soundcloud_link']; ?>" target="_blank">
                    <img src="images/soundcloud.png" alt="SoundCloud">
                </a>
            <?php endif; ?>

            <?php if (!empty($musique['spotify_link'])): ?>
                <a href="<?php echo $musique['spotify_link']; ?>" target="_blank">
                    <img src="images/spotify.png" alt="Spotify">
                </a>
            <?php endif; ?>

            <?php if (!empty($musique['deezer_link'])): ?>
                <a href="<?php echo $musique['deezer_link']; ?>" target="_blank">
                    <img src="images/deezer.png" alt="Deezer">
                </a>
            <?php endif; ?>

            <?php if (!empty($musique['apple_link'])): ?>
                <a href="<?php echo $musique['apple_link']; ?>" target="_blank">
                    <img src="images/applemusic.png" alt="Apple Music">
                </a>
            <?php endif; ?>

            <?php if (!empty($musique['youtube_link'])): ?>
                <a href="<?php echo $musique['youtube_link']; ?>" target="_blank">
                    <img src="images/ytmusic.png" alt="YouTube Music">
                </a>
            <?php endif; ?>

            <?php if (!empty($musique['amazon_link'])): ?>
                <a href="<?php echo $musique['amazon_link']; ?>" target="_blank">
                    <img src="images/amazonmusic.png" alt="Amazon Music">
                </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="right">
        <h1><?php echo $musique['title_song']; ?></h1>
        <p><?php echo $musique['artist_song']; ?></p>
        <?php if (!empty($musique['feat_song'])): ?>
            <p>Feat : <?php echo $musique['feat_song']; ?></p>
        <?php endif; ?>
        <?php if (!empty($musique['description_song'])): ?>
            <p><?php echo $musique['description_song']; ?></p>
        <?php endif; ?>
        <?php if (!empty($musique['time_song'])): ?>
            <p>Durée : <?php echo $musique['time_song']; ?></p>
        <?php endif; ?>
        <?php if (!empty($musique['release_song'])): ?>
            <p>Sortie le <?php echo convertirDate($musique['release_song']); ?></p>
        <?php endif; ?>

        <!-- Bouton de retour -->
        <a href="index.php" class="button">Retour</a>
    </div>
</div>

</body>
</html>