<?php
// Vérifier si l'utilisateur est connecté en tant qu'admin
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: connexion_admin.php");
    exit();
}

// Inclure le fichier de connexion à la base de données
include("includes/connectBDD.php");

// Récupérer la connexion à la base de données
$connexion = connecterBaseDeDonnees();

// Vérifier si la connexion est établie avec succès
if (!$connexion) {
    die("Impossible de se connecter à la base de données.");
}

// Supprimer la musique si une requête de suppression est reçue
if(isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql_delete = "DELETE FROM musique WHERE id_song = :delete_id";
    $stmt = $connexion->prepare($sql_delete);
    $stmt->bindParam(':delete_id', $delete_id);
    if($stmt->execute()) {
        // Musique supprimée avec succès
        echo "<script>alert('Musique supprimée avec succès.');</script>";
    } else {
        // Erreur lors de la suppression de la musique
        echo "<script>alert('Erreur lors de la suppression de la musique. Veuillez réessayer.');</script>";
    }
}

// Récupérer toutes les musiques depuis la base de données
$sql = "SELECT * FROM musique";
$result = $connexion->query($sql);

// Récupérer tous les albums depuis la base de données
$sql_albums = "SELECT * FROM album";
$result_albums = $connexion->query($sql_albums);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste OpiuMusique</title>
    <link rel="stylesheet" href="assets/crud_son.css">
    <script>
        function confirmDelete() {
            return confirm("Êtes-vous sûr de vouloir supprimer cette musique ?");
        }
    </script>
</head>
<body>

<!-- Bouton de retour à la page index.php -->
<a href="index.php" id="return-button"><button>Retour à la page d'accueil</button></a>

<!-- Bouton Liste admin -->
<a href="crud_admin.php" id="admin-list-button"><button>Liste admin</button></a>

<a href="add_son.php"><button>Ajouter Musique</button></a>

<a href="add_album.php"><button>Ajouter Album</button></a>

<?php
// Utilisation de rowCount() pour obtenir le nombre de lignes retournées
if ($result->rowCount() > 0) {
    echo "<h1>Vous avez " . $result->rowCount() . " musiques</h1>";
} else {
    echo "<h1>Vous n'avez aucune musique</h1>";
}
?>

<!-- Sélecteur pour afficher les musiques ou les albums -->
<div>
    <button onclick="showMusics()">Musiques</button>
    <button onclick="showAlbums()">Albums</button>
</div>

<!-- Tableau pour afficher les musiques -->
<div id="musicTable">
    <?php
    if ($result->rowCount() > 0) {
        echo "<table>";
        echo "<tr>";
        echo "<th>ID</th>";
        echo "<th>Titre</th>";
        echo "<th>Artiste</th>";
        echo "<th>Date de sortie</th>";
        echo "<th>Style</th>";
        echo "<th>Description</th>";
        echo "<th>Durée</th>";
        echo "<th>Tempo</th>";
        echo "<th>Image</th>";
        echo "<th>Collaborations</th>";
        echo "<th>Spotify</th>";
        echo "<th>Soundcloud</th>";
        echo "<th>Deezer</th>";
        echo "<th>YouTube Music</th>";
        echo "<th>Amazon</th>";
        echo "<th>Apple</th>";
        echo "<th>Actions</th>";
        echo "</tr>";
        
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $row["id_song"] . "</td>";
            echo "<td>" . $row["title_song"] . "</td>";
            echo "<td>" . $row["artist_song"] . "</td>";
            echo "<td>" . $row["release_song"] . "</td>";
            echo "<td>" . $row["style_song"] . "</td>";
            echo "<td>" . $row["description_song"] . "</td>";
            echo "<td>" . $row["time_song"] . "</td>";
            echo "<td>" . $row["tempo_song"] . "</td>";
            echo "<td><img src='" . $row["cover_song"] . "' width='100' height='100'></td>"; // Utiliser le chemin stocké dans la base de données
            echo "<td>" . $row["feat_song"] . "</td>";
            echo "<td>";
            if (!empty($row["spotify_link"])) {
                echo "<a href='" . $row["spotify_link"] . "'><button>Spotify</button></a>";
            }
            echo "</td>";
            echo "<td>";
            if (!empty($row["soundcloud_link"])) {
                echo "<a href='" . $row["soundcloud_link"] . "'><button>Soundcloud</button></a>";
            }
            echo "</td>";
            echo "<td>";
            if (!empty($row["deezer_link"])) {
                echo "<a href='" . $row["deezer_link"] . "'><button>Deezer</button></a>";
            }
            echo "</td>";
            echo "<td>";
            if (!empty($row["youtube_link"])) {
                echo "<a href='" . $row["youtube_link"] . "'><button>YouTube Music</button></a>";
            }
            echo "</td>";
            echo "<td>";
            if (!empty($row["amazon_link"])) {
                echo "<a href='" . $row["amazon_link"] . "'><button>Amazon</button></a>";
            }
            echo "</td>";
            echo "<td>";
            if (!empty($row["apple_link"])) {
                echo "<a href='" . $row["apple_link"] . "'><button>Apple</button></a>";
            }
            echo "</td>";
            echo "<td>";
            echo "<a href='edit_son.php?id=" . $row["id_song"] . "'>Modifier</a> | ";
            echo "<a href='?delete_id=" . $row["id_song"] . "' onclick='return confirmDelete();'>Supprimer</a>";
            echo "</td>";
            echo "</tr>";    
        }
        echo "</table>";
    } else {
        echo "Aucune musique trouvée.";
    }
    ?>
</div>

<!-- Tableau pour afficher les albums -->
<div id="albumTable" style="display: none;">
    <?php
    if ($result_albums->rowCount() > 0) {
        echo "<h1>Liste des Albums</h1>";
        echo "<table>";
        echo "<tr>";
        echo "<th>ID</th>";
        echo "<th>Titre</th>";
        echo "<th>Artiste</th>";
        echo "<th>Durée</th>";
        echo "<th>Description</th>";
        echo "<th>Image</th>";
        echo "<th>Date</th>";
        echo "<th>Nombre de titres</th>";
        echo "<th>Soundcloud</th>";
        echo "<th>Spotify</th>";
        echo "<th>Deezer</th>";
        echo "<th>Amazon</th>";
        echo "<th>Apple</th>";
        echo "<th>YouTube Music</th>";
        echo "</tr>";
        
        while ($row = $result_albums->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $row["id_album"] . "</td>";
            echo "<td>" . $row["title_album"] . "</td>";
            echo "<td>" . $row["artist_album"] . "</td>";
            echo "<td>" . $row["duration_album"] . "</td>";
            echo "<td>" . $row["description_album"] . "</td>";
            echo "<td><img src='" . $row["cover_album"] . "' width='100' height='100'></td>";
            echo "<td>" . $row["release_album"] . "</td>";
            echo "<td>" . $row["track_count"] . "</td>";
            echo "<td>";
            if (!empty($row["soundcloud_link"])) {
                echo "<a href='" . $row["soundcloud_link"] . "'><button>Soundcloud</button></a>";
            }
            echo "</td>";
            echo "<td>";
            if (!empty($row["spotify_link"])) {
                echo "<a href='" . $row["spotify_link"] . "'><button>Spotify</button></a>";
            }
            echo "</td>";
            echo "<td>";
            if (!empty($row["deezer_link"])) {
                echo "<a href='" . $row["deezer_link"] . "'><button>Deezer</button></a>";
            }
            echo "</td>";
            echo "<td>";
            if (!empty($row["amazon_link"])) {
                echo "<a href='" . $row["amazon_link"] . "'><button>Amazon</button></a>";
            }
            echo "</td>";
            echo "<td>";
            if (!empty($row["apple_link"])) {
                echo "<a href='" . $row["apple_link"] . "'><button>Apple</button></a>";
            }
            echo "</td>";
            echo "<td>";
            if (!empty($row["youtube_link"])) {
                echo "<a href='" . $row["youtube_link"] . "'><button>YouTube Music</button></a>";
            }
            echo "</td>";
            echo "</tr>";    
        }
        echo "</table>";
    } else {
        echo "Aucun album trouvé.";
    }
    ?>
</div>

<script>
    function showMusics() {
        document.getElementById('musicTable').style.display = 'block';
        document.getElementById('albumTable').style.display = 'none';
    }

    function showAlbums() {
        document.getElementById('musicTable').style.display = 'none';
        document.getElementById('albumTable').style.display = 'block';
    }
</script>

</body>
</html>