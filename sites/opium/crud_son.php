<?php
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: connexion_admin.php");
    exit();
}

include("includes/connectBDD.php");
$connexion = connecterBaseDeDonnees();

if (!$connexion) {
    die("Impossible de se connecter à la base de données.");
}

if(isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql_delete = "DELETE FROM musique WHERE id_song = :delete_id";
    $stmt = $connexion->prepare($sql_delete);
    $stmt->bindParam(':delete_id', $delete_id);
    if($stmt->execute()) {
        echo "<script>alert('Musique supprimée avec succès.');</script>";
    } else {
        echo "<script>alert('Erreur lors de la suppression de la musique. Veuillez réessayer.');</script>";
    }
}

$sql = "SELECT * FROM musique";
$result = $connexion->query($sql);

$sql_albums = "
    SELECT 
        a.*, 
        GROUP_CONCAT(m.title_song SEPARATOR ', ') as titles
    FROM 
        album a
    LEFT JOIN 
        album_musique am ON a.id_album = am.id_album
    LEFT JOIN 
        musique m ON am.id_song = m.id_song
    GROUP BY 
        a.id_album
";
$result_albums = $connexion->query($sql_albums);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste OpiuMusique</title>
    <link rel="stylesheet" href="assets/crud_son.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script>
        function confirmDelete() {
            return confirm("Êtes-vous sûr de vouloir supprimer cette musique ?");
        }

        $(document).ready(function() {
            $('#musicTable table').DataTable();
            $('#albumTable table').DataTable();
        });
    </script>
</head>
<body>

<a href="index.php" id="return-button"><button>Retour à la page d'accueil</button></a>
<a href="crud_admin.php" id="admin-list-button"><button>Liste admin</button></a>
<a href="add_son.php"><button>Ajouter Musique</button></a>
<a href="add_album.php"><button>Ajouter Album</button></a>

<?php
if ($result->rowCount() > 0) {
    echo "<h1>Vous avez " . $result->rowCount() . " musiques</h1>";
} else {
    echo "<h1>Vous n'avez aucune musique</h1>";
}
?>

<div>
    <button onclick="showMusics()">Musiques</button>
    <button onclick="showAlbums()">Albums</button>
</div>

<div id="musicTable">
    <?php
    if ($result->rowCount() > 0) {
        echo "<table>";
        echo "<thead><tr>";
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
        echo "</tr></thead><tbody>";
        
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
            echo "<td><img src='" . $row["cover_song"] . "' width='100' height='100'></td>";
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
        echo "</tbody></table>";
    } else {
        echo "Aucune musique trouvée.";
    }
    ?>
</div>

<div id="albumTable" style="display: none;">
    <?php
    if ($result_albums->rowCount() > 0) {
        echo "<h1>Liste des Albums</h1>";
        echo "<table>";
        echo "<thead><tr>";
        echo "<th>ID</th>";
        echo "<th>Titre</th>";
        echo "<th>Artiste</th>";
        echo "<th>Type</th>";
        echo "<th>Durée</th>";
        echo "<th>Description</th>";
        echo "<th>Image</th>";
        echo "<th>Date de sortie</th>";
        echo "<th>Nombre de titres</th>";
        echo "<th>Musiques</th>";
        echo "<th>Soundcloud</th>";
        echo "<th>Spotify</th>";
        echo "<th>Deezer</th>";
        echo "<th>Amazon</th>";
        echo "<th>Apple</th>";
        echo "<th>YouTube Music</th>";
        echo "<th>Actions</th>";
        echo "</tr></thead><tbody>";
        
        while ($row = $result_albums->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $row["id_album"] . "</td>";
            echo "<td>" . $row["title_album"] . "</td>";
            echo "<td>" . $row["artist_album"] . "</td>";
            echo "<td>" . $row["type_album"] . "</td>";
            echo "<td>" . $row["time_album"] . "</td>";
            echo "<td>" . $row["description_album"] . "</td>";
            echo "<td><img src='" . $row["cover_album"] . "' width='100' height='100'></td>";
            echo "<td>" . $row["date_album"] . "</td>";
            echo "<td>" . $row["number_album"] . "</td>";
            echo "<td>" . $row["titles"] . "</td>";
            echo "<td>";
            if (!empty($row["alink_soundcloud"])) {
                echo "<a href='" . $row["alink_soundcloud"] . "'><button>Soundcloud</button></a>";
            }
            echo "</td>";
            echo "<td>";
            if (!empty($row["alink_spotify"])) {
                echo "<a href='" . $row["alink_spotify"] . "'><button>Spotify</button></a>";
            }
            echo "</td>";
            echo "<td>";
            if (!empty($row["alink_deezer"])) {
                echo "<a href='" . $row["alink_deezer"] . "'><button>Deezer</button></a>";
            }
            echo "</td>";
            echo "<td>";
            if (!empty($row["alink_amazon"])) {
                echo "<a href='" . $row["alink_amazon"] . "'><button>Amazon</button></a>";
            }
            echo "</td>";
            echo "<td>";
            if (!empty($row["alink_apple"])) {
                echo "<a href='" . $row["alink_apple"] . "'><button>Apple</button></a>";
            }
            echo "</td>";
            echo "<td>";
            if (!empty($row["alink_youtube"])) {
                echo "<a href='" . $row["alink_youtube"] . "'><button>YouTube Music</button></a>";
            }
            echo "</td>";
            echo "<td>";
            echo "<a href='edit_album.php?id=" . $row["id_album"] . "'>Modifier</a> | ";
            echo "<a href='?delete_id=" . $row["id_album"] . "' onclick='return confirmDelete();'>Supprimer</a>";
            echo "</td>";
            echo "</tr>";    
        }
        echo "</tbody></table>";
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