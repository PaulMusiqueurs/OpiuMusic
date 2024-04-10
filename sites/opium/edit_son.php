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
    $sql = "SELECT * FROM musique WHERE id_song = ?";
    
    // Établir la connexion à la base de données
    $connexion = connecterBaseDeDonnees();
    
    // Préparer et exécuter la requête
    if ($stmt = $connexion->prepare($sql)) {
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // Pré-remplir les champs du formulaire avec les données existantes
            $titre = isset($row["title_song"]) ? htmlspecialchars($row["title_song"]) : "";
            $artiste = isset($row["artist_song"]) ? htmlspecialchars($row["artist_song"]) : "";
            $time_song = isset($row["time_song"]) ? $row["time_song"] : "00:00:00";
            list($duree_heures, $duree_minutes, $duree_secondes) = explode(':', $time_song);
            $tempo = isset($row["tempo_song"]) ? htmlspecialchars($row["tempo_song"]) : "";
            $invites = isset($row["feat_song"]) ? htmlspecialchars($row["feat_song"]) : "";
            $style = isset($row["style_song"]) ? htmlspecialchars($row["style_song"]) : "";
            $soundcloud_link = isset($row["soundcloud_link"]) ? htmlspecialchars($row["soundcloud_link"]) : "";
            $spotify_link = isset($row["spotify_link"]) ? htmlspecialchars($row["spotify_link"]) : "";
            $apple_link = isset($row["apple_link"]) ? htmlspecialchars($row["apple_link"]) : "";
            $deezer_link = isset($row["deezer_link"]) ? htmlspecialchars($row["deezer_link"]) : "";
            $amazon_link = isset($row["amazon_link"]) ? htmlspecialchars($row["amazon_link"]) : "";
            $youtube_link = isset($row["youtube_link"]) ? htmlspecialchars($row["youtube_link"]) : "";
            $cover_song = isset($row["cover_song"]) ? htmlspecialchars($row["cover_song"]) : ""; // Chemin de l'image actuelle
            $description = isset($row["description_song"]) ? htmlspecialchars($row["description_song"]) : ""; // Description existante
            $release_date = isset($row["release_song"]) ? htmlspecialchars($row["release_song"]) : ""; // Date de sortie existante
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

// Traitement de la soumission du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Supprimer l'image précédente si une nouvelle image est soumise
    if ($_FILES['image']['name']) {
        // Chemin de l'image précédente
        $chemin_image_precedente = $cover_song;

        // Supprimer l'image précédente du serveur
        if (file_exists($chemin_image_precedente)) {
            unlink($chemin_image_precedente);
        }
    }

    // Prepare the update query with all columns including description and release date
    $stmt = $connexion->prepare("UPDATE musique SET title_song=?, artist_song=?, time_song=?, tempo_song=?, feat_song=?, style_song=?, soundcloud_link=?, spotify_link=?, apple_link=?, deezer_link=?, amazon_link=?, youtube_link=?, cover_song=?, description_song=?, release_song=? WHERE id_song=?");
    
    // Télécharger et enregistrer la nouvelle image sur le serveur
    if ($_FILES['image']['name']) {
        $dossier_images = "images/";
        $nom_fichier_image = $id . ".jpg"; // Nom de fichier basé sur l'ID de la musique
        $chemin_fichier_image = $dossier_images . $nom_fichier_image;
        move_uploaded_file($_FILES["image"]["tmp_name"], $chemin_fichier_image);
        $cover_song = $chemin_fichier_image;
    }

    $stmt->execute([
        $_POST['titre'],
        $_POST['artiste'],
        $_POST['duree_heures'] . ":" . $_POST['duree_minutes'] . ":" . $_POST['duree_secondes'],
        $_POST['tempo'],
        $_POST['invites'],
        $_POST['style'],
        $_POST['soundcloud_link'],
        $_POST['spotify_link'],
        $_POST['apple_link'],
        $_POST['deezer_link'],
        $_POST['amazon_link'],
        $_POST['youtube_link'],
        $cover_song, // Nouveau chemin de l'image
        $_POST['description'], // Nouveau champ description
        $_POST['release_date'], // Nouveau champ date de sortie
        $_POST['id']
    ]);

    header("Location: crud_son.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une musique</title>
    <link rel="stylesheet" href="assets/edit_son.css">
</head>
<body>

<h1>Modifier une musique</h1>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $id; ?>" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    
    <label for="titre">Titre :</label><br>
    <input type="text" id="titre" name="titre" value="<?php echo $titre; ?>" required><br><br>

    <label for="artiste">Artiste :</label><br>
    <input type="text" id="artiste" name="artiste" value="<?php echo $artiste; ?>" required><br><br>

    <label for="duree_heures">Durée (heures) :</label><br>
    <input type="number" id="duree_heures" name="duree_heures" value="<?php echo $duree_heures; ?>" required><br><br>

    <label for="duree_minutes">Durée (minutes) :</label><br>
    <input type="number" id="duree_minutes" name="duree_minutes" value="<?php echo $duree_minutes; ?>" required><br><br>

    <label for="duree_secondes">Durée (secondes) :</label><br>
    <input type="number" id="duree_secondes" name="duree_secondes" value="<?php echo $duree_secondes; ?>" required><br><br>

    <label for="tempo">Tempo (bpm) :</label><br>
    <input type="text" id="tempo" name="tempo" value="<?php echo $tempo; ?>"><br><br>

    <label for="image">Image :</label><br>
    <input type="file" id="image" name="image"><br><br>
    <!-- Champ pour télécharger une nouvelle image -->

    <label for="invites">Invité(e)s :</label><br>
    <input type="text" id="invites" name="invites" value="<?php echo $invites; ?>"><br><br>

    <!-- Informations supplémentaires -->
    <label for="style">Style de musique :</label><br>
    <input type="text" id="style" name="style" value="<?php echo $style; ?>"><br><br>

    <label for="soundcloud_link">Soundcloud :</label><br>
    <input type="text" id="soundcloud_link" name="soundcloud_link" value="<?php echo $soundcloud_link; ?>"><br><br>

    <label for="spotify_link">Spotify :</label><br>
    <input type="text" id="spotify_link" name="spotify_link" value="<?php echo $spotify_link; ?>"><br><br>

    <label for="apple_link">Apple Music :</label><br>
    <input type="text" id="apple_link" name="apple_link" value="<?php echo $apple_link; ?>"><br><br>

    <label for="deezer_link">Deezer :</label><br>
    <input type="text" id="deezer_link" name="deezer_link" value="<?php echo $deezer_link; ?>"><br><br>

    <label for="amazon_link">Amazon Music :</label><br>
    <input type="text" id="amazon_link" name="amazon_link" value="<?php echo $amazon_link; ?>"><br><br>

    <label for="youtube_link">YouTube :</label><br>
    <input type="text" id="youtube_link" name="youtube_link" value="<?php echo $youtube_link; ?>"><br><br>

    <!-- Nouveaux champs -->
    <label for="description">Description :</label><br>
    <textarea id="description" name="description"><?php echo $description; ?></textarea><br><br>

    <label for="release_date">Date de sortie :</label><br>
    <input type="date" id="release_date" name="release_date" value="<?php echo $release_date; ?>"><br><br>

    <input type="submit" value="Modifier">
</form>

</body>
</html>