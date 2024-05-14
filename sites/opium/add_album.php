<?php
// Vérifier si l'utilisateur est connecté en tant qu'admin
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: connexion_admin.php");
    exit();
}

// Inclure le fichier de connexion à la base de données
include("includes/connectBDD.php");

// Initialiser les variables pour stocker les valeurs des champs
$titre = $artiste = $description = $release_date = $imageFilePath = $type = "";
$soundcloud_link = $spotify_link = $apple_link = $deezer_link = $amazon_link = $youtube_link = "";

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les valeurs des champs
    $titre = $_POST["titre"];
    $artiste = $_POST["artiste"];
    $description = $_POST["description"];
    $release_date = $_POST["release_date"];
    $type = $_POST["type"];
    $time_album = date("Y-m-d H:i:s");
    
    // Compter le nombre de musiques sélectionnées
    $number = count($_POST["musique_select"]);

    $imageFileName = $_FILES["image"]["name"];
    $imageFilePath = "images/album/" . $imageFileName;
    move_uploaded_file($_FILES["image"]["tmp_name"], $imageFilePath);

    $soundcloud_link = $_POST["soundcloud_link"];
    $spotify_link = $_POST["spotify_link"];
    $apple_link = $_POST["apple_link"];
    $deezer_link = $_POST["deezer_link"];
    $amazon_link = $_POST["amazon_link"];
    $youtube_link = $_POST["youtube_link"];

    // Insérer dans la base de données
    $connexion = connecterBaseDeDonnees();
    if (!$connexion) {
        // Gérer l'erreur de connexion
        echo "Erreur de connexion à la base de données";
        exit();
    }

    $requete = $connexion->prepare("INSERT INTO album (title_album, artist_album, type_album, time_album, description_album, cover_album, date_album, number_album, alink_soundcloud, alink_spotify, alink_apple, alink_deezer, alink_amazon, alink_youtube) VALUES (:titre, :artiste, :type, :time_album, :description, :imageFilePath, :release_date, :number, :soundcloud_link, :spotify_link, :apple_link, :deezer_link, :amazon_link, :youtube_link)");
    $requete->bindParam(':titre', $titre, PDO::PARAM_STR);
    $requete->bindParam(':artiste', $artiste, PDO::PARAM_STR);
    $requete->bindParam(':type', $type, PDO::PARAM_STR);
    $requete->bindParam(':time_album', $time_album, PDO::PARAM_STR);
    $requete->bindParam(':description', $description, PDO::PARAM_STR);
    $requete->bindParam(':imageFilePath', $imageFilePath, PDO::PARAM_STR);
    $requete->bindParam(':release_date', $release_date, PDO::PARAM_STR);
    $requete->bindParam(':number', $number, PDO::PARAM_INT);
    $requete->bindParam(':soundcloud_link', $soundcloud_link, PDO::PARAM_STR);
    $requete->bindParam(':spotify_link', $spotify_link, PDO::PARAM_STR);
    $requete->bindParam(':apple_link', $apple_link, PDO::PARAM_STR);
    $requete->bindParam(':deezer_link', $deezer_link, PDO::PARAM_STR);
    $requete->bindParam(':amazon_link', $amazon_link, PDO::PARAM_STR);
    $requete->bindParam(':youtube_link', $youtube_link, PDO::PARAM_STR);
    $requete->execute();

    // Récupérer l'ID de la dernière insertion
    $id_album = $connexion->lastInsertId();

    // Insérer les musiques sélectionnées dans la table album_musique
    foreach ($_POST["musique_select"] as $id_song) {
        $requeteInsertMusique = $connexion->prepare("INSERT INTO album_musique (id_album, id_song) VALUES (:id_album, :id_song)");
        $requeteInsertMusique->bindParam(':id_album', $id_album, PDO::PARAM_INT);
        $requeteInsertMusique->bindParam(':id_song', $id_song, PDO::PARAM_INT);
        $requeteInsertMusique->execute();
    }

    // Renommer le fichier image avec l'ID de l'album
    $newImageFileName = $id_album . ".jpg";
    $newImageFilePath = "images/album/" . $newImageFileName;
    rename($imageFilePath, $newImageFilePath);

    // Mettre à jour le chemin de l'image dans la base de données avec le nouveau nom de fichier
    $requeteUpdateImage = $connexion->prepare("UPDATE album SET cover_album = :newImageFilePath WHERE id_album = :id_album");
    $requeteUpdateImage->bindParam(':newImageFilePath', $newImageFilePath, PDO::PARAM_STR);
    $requeteUpdateImage->bindParam(':id_album', $id_album, PDO::PARAM_INT);
    $requeteUpdateImage->execute();

    // Fermer les requêtes
    $requete->closeCursor();
    $requeteUpdateImage->closeCursor();

    header("Location: crud_son.php?success=true");
    exit();
}

// Récupérer les titres de musique depuis la base de données pour le champ de sélection de musique
$connexion = connecterBaseDeDonnees();
if (!$connexion) {
    // Gérer l'erreur de connexion
    echo "Erreur de connexion à la base de données";
    exit();
}

$requeteMusique = $connexion->query("SELECT id_song, title_song FROM musique");
if (!$requeteMusique) {
    // Gérer l'erreur de requête
    echo "Erreur lors de la récupération des titres de musique";
    exit();
}

$musiques = $requeteMusique->fetchAll(PDO::FETCH_ASSOC);
$requeteMusique->closeCursor();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une musique</title>
    <link rel="stylesheet" href="assets/add_album.css">
</head>
<body>

<h1>Ajouter une musique</h1>

<form action="add_album.php" method="post" enctype="multipart/form-data">
    <label for="titre">Titre :</label><br>
    <input type="text" id="titre" name="titre" required><br><br>

    <label for="artiste">Artiste :</label><br>
    <input type="text" id="artiste" name="artiste" required><br><br>

    <label for="type">Type :</label><br>
    <select id="type" name="type" required>
        <option value="Album">Album</option>
        <option value="EP">EP</option>
        <option value="Single">Single</option>
        <option value="Mixtape">Mixtape</option>
    </select><br><br>

    <label for="image">Image :</label><br>
    <input type="file" id="image" name="image" required><br><br>

    <div id="musiques">
        <div class="musique">
        <label for="musique_select_1">Musique 1 :</label><br>
        <select id="musique_select_1" name="musique_select[]" required>
            <option value="">Sélectionner une musique</option>
            <?php foreach ($musiques as $musique) { ?>
                <option value="<?php echo $musique["id_song"]; ?>"><?php echo $musique["title_song"]; ?></option>
            <?php } ?>
        </select>
        </div>
    </div>
    <button type="button" id="ajouterMusique">+</button><br><br>

    <!-- Les liens vers les plateformes de streaming -->
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
    
    <!-- La description et la date de sortie -->
    <label for="description">Description :</label><br>
    <textarea id="description" name="description"></textarea><br><br>
    
    <label for="release_date">Date de sortie :</label><br>
    <input type="date" id="release_date" name="release_date" required><br><br>

    <!-- Champ pour le nombre de musiques -->
    <label for="number">Nombre de musiques :</label><br>
    <input type="number" id="number" name="number" required><br><br>

    <!-- Bouton de soumission -->
    <input type="submit" value="Ajouter">
</form>

<script>
    // Fonction pour cloner et ajouter un champ de sélection de musique supplémentaire
    document.getElementById('ajouterMusique').addEventListener('click', function() {
        var musiquesDiv = document.getElementById('musiques');
        var nouvelleMusique = musiquesDiv.querySelector('.musique').cloneNode(true);
        var index = document.querySelectorAll('.musique').length + 1;

        // Modifier l'id et le label de la nouvelle musique
        nouvelleMusique.querySelector('label').textContent = 'Musique ' + index + ' :';
        nouvelleMusique.querySelector('select').id = 'musique_select_' + index;

        // Créer un bouton pour supprimer la nouvelle musique
        var deleteButton = document.createElement('button');
        deleteButton.textContent = '-';
        deleteButton.type = 'button';
        deleteButton.className = 'supprimerMusique';
        deleteButton.addEventListener('click', function() {
            nouvelleMusique.remove();
            // Mettre à jour les indices des champs de musique restants
            var musiques = document.querySelectorAll('.musique');
            for (var i = 0; i < musiques.length; i++) {
                musiques[i].querySelector('label').textContent = 'Musique ' + (i + 1) + ' :';
                musiques[i].querySelector('select').id = 'musique_select_' + (i + 1);
            }
        });
        nouvelleMusique.appendChild(deleteButton);

        // Ajouter la nouvelle musique après le dernier champ de musique existant
        musiquesDiv.appendChild(nouvelleMusique);
    });
</script>

</body>
</html>