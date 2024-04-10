<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/index.css">
    <title>OpiuMusic</title>

 <!-- Bandeau avec le titre "OpiuMusique" à gauche et les boutons à droite -->
 <div id="navbar">
        <div id="logo">OpiuMusique</div>
        <div>
            <!-- Bouton 'Qui sommes-nous ?' -->
            <a href="#"><button>Qui sommes-nous ?</button></a>
            <!-- Bouton 'Contact' -->
            <a href="#"><button>Contact</button></a>
            <!-- Bouton 'Admin' -->
            <a href="crud_son.php"><button id="admin-btn">Admin</button></a>
        </div>
    </div>

    <h1> </h1>

    <?php
    // Inclure le fichier de connexion à la base de données
    include("includes/connectBDD.php");

    // Récupération de la connexion à la base de données
    $connexion = connecterBaseDeDonnees();

    // Vérifier si la connexion est établie avec succès
    if (!$connexion) {
        die("Impossible de se connecter à la base de données.");
    }

    // Définir le nombre d'éléments par page
    $elements_par_page = 12;

    // Déterminer le numéro de la page à afficher
    $num_page = isset($_GET['page']) ? $_GET['page'] : 1;
    $debut = ($num_page - 1) * $elements_par_page;

    // Récupérer les musiques pour cette page
    $sql = "SELECT * FROM musique ORDER BY id_song ASC LIMIT $debut, $elements_par_page";
    $result = $connexion->query($sql);

    // Création d'un tableau pour stocker les données des musiques
    $musiques = array();

    // Vérifier si des musiques ont été récupérées avec succès
    if ($result) {
        // Parcourir les résultats et stocker les données dans le tableau des musiques
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $musique = array(
                'id' => $row['id_song'],
                'title' => $row['title_song'],
                'artist' => $row['artist_song'],
                'cover' => $row['cover_song'],
                'feat' => $row['feat_song']
            );
            $musiques[] = $musique;
        }
    }
    ?>

    <!-- Affichage des musiques associées aux objets -->
    <div class='objets-container'>
        <?php
        foreach ($musiques as $musique) {
            // Générer l'URL avec l'ID de la musique
            $url = "musique.php?id=" . $musique['id'];
            echo "<div class='col'>";
            echo "<a href='$url' class='music-link'>"; // Placer le lien ici
            // Utiliser l'ID de la musique pour récupérer l'image correspondante
            echo "<img src='images/" . $musique['id'] . ".jpg' class='objet-img'>";
            echo "<div class='title'>" . $musique['title'] . "</div>";
            echo "<div class='artist'>" . $musique['artist'] . "</div>";
            echo "<div class= 'feat'>" . $musique['feat'] . "</div>";
            echo "</a>"; // Fermer le lien
            echo "</div>";
        }
        ?>
    </div>

    <!-- Fonction JavaScript pour afficher la description -->
    <script>
        function afficherDescription(titre, artiste) {
            document.getElementById("description").innerHTML = "<h2>" + titre + "</h2><p>Artiste: " + artiste + "</p><p>Feat: " + feat + "</p>";
        }
    </script>

    <!-- Pagination -->
    <div class="pagination-container">
        <div class="pagination">
            <?php
            // Afficher la pagination si nécessaire
            $sql_count = "SELECT COUNT(*) as count FROM musique";
            $result_count = $connexion->query($sql_count);
            $row_count = $result_count->fetch(PDO::FETCH_ASSOC);
            $total_elements = $row_count['count'];
            $total_pages = ceil($total_elements / $elements_par_page);

            // Bouton pour aller à la première page
            if ($num_page > 1) {
                echo "<a href='?page=1'>&lt;&lt;</a>"; // &lt;&lt; représente <<
            }

            // Bouton pour reculer d'une page
            if ($num_page > 1) {
                echo "<a href='?page=".($num_page - 1)."'>&lt;</a>"; // &lt; représente <
            }

            // Lien pour la page actuelle
            echo "<span class='current-page'>$num_page</span>";

            // Bouton pour avancer d'une page
            if ($num_page < $total_pages) {
                echo "<a href='?page=".($num_page + 1)."'>&gt;</a>"; // &gt; représente >
            }

            // Bouton pour aller à la dernière page
            if ($num_page < $total_pages) {
                echo "<a href='?page=$total_pages'>&gt;&gt;</a>"; // &gt;&gt; représente >>
            }
            ?>
        </div>
    </div>

</body>
</html>
