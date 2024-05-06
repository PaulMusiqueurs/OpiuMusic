<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/index.css">
    <title>OpiuMusique</title>
</head>

<body>

    <!-- Barre de navigation -->
    <div id="navbar">
        <div id="logo">OpiuMusique</div>

        <!-- Les boutons de gauche -->
        <div id="left-buttons">
            <a href="#"><button>Album</button></a>
            <a href="#"><button>Artistes</button></a>
            <a href="#"><button>Événement</button></a>
        </div>

        <!-- Bouton "Trier" avec menu déroulant -->
        <div id="dropdown">
            <button id="sort-btn">Trier</button>
            <div id="dropdown-content">
                <a href="#" data-sort="id_song" data-order="desc">Date d'ajout</a>
                <a href="#" data-sort="title_song" data-order="asc">Titre</a>
                <a href="#" data-sort="artist_song" data-order="asc">Artiste</a>
                <a href="#" data-sort="time_song" data-order="asc">Durée</a>
                <a href="#" data-sort="release_song" data-order="desc">Date de sortie</a>
                <hr>
                <a href="#" data-order="toggle">Inverser l'ordre</a>
            </div>
        </div>

        <!-- Les boutons de droite -->
        <div id="right-buttons">
            <a href="#"><button>Contact</button></a>
            <a href="crud_son.php"><button id="admin-btn">Admin</button></a>
        </div>
    </div>

    <!-- Contenu -->
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
    $elements_par_page = 9;

    // Déterminer le numéro de la page à afficher
    $num_page = isset($_GET['page']) ? $_GET['page'] : 1;
    $debut = ($num_page - 1) * $elements_par_page;

    // Récupérer les paramètres de tri de l'URL
    $sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'id_song'; // Par défaut, trier par date d'ajout
    $order = isset($_GET['order']) ? $_GET['order'] : 'desc'; // Par défaut, ordre décroissant

    // Modifiez la requête SQL pour inclure le tri choisi par l'utilisateur
    $sql = "SELECT * FROM musique ORDER BY $sort_by $order LIMIT $debut, $elements_par_page";
    $result = $connexion->query($sql);

    // Création d'un tableau pour stocker les données des musiques
    $musiques = array();

    // Vérifier si des musiques ont été récupérées avec succès
    if ($result) {
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

    <!-- Affichage des musiques -->
    <div class="objets-container">
        <?php
        foreach ($musiques as $musique) {
            $url = "musique.php?id=" . $musique['id'];
            echo "<div class='col'>";
            echo "<a href='$url' class='music-link'>";
            echo "<img src='images/" . $musique['id'] . ".jpg' class='objet-img'>";
            echo "<div class='title'>" . $musique['title'] . "</div>";
            echo "<div class='artist'>" . $musique['artist'] . "</div>";
            echo "<div class='feat'>" . $musique['feat'] . "</div>";
            echo "</a>";
            echo "</div>";
        }
        ?>
    </div>

    <!-- Pagination -->
    <div class="pagination-container">
        <div class="pagination">
            <?php
            // Requête SQL pour compter le nombre total d'éléments
            $sql_count = "SELECT COUNT(*) as count FROM musique";
            $result_count = $connexion->query($sql_count);
            $row_count = $result_count->fetch(PDO::FETCH_ASSOC);
            $total_elements = $row_count['count'];
            $total_pages = ceil($total_elements / $elements_par_page);

            // Afficher les boutons de pagination
            if ($num_page > 1) {
                echo "<a href='?page=1&sort_by=$sort_by&order=$order'>&lt;&lt;</a>"; // <<
                echo "<a href='?page=" . ($num_page - 1) . "&sort_by=$sort_by&order=$order'>&lt;</a>"; // <
            }

            echo "<span class='current-page'>$num_page</span>";

            if ($num_page < $total_pages) {
                echo "<a href='?page=" . ($num_page + 1) . "&sort_by=$sort_by&order=$order'>&gt;</a>"; // >
                echo "<a href='?page=$total_pages&sort_by=$sort_by&order=$order'>&gt;&gt;</a>"; // >>
            }
            ?>
        </div>
    </div>

    <!-- Script JavaScript -->
    <script>
        // Gérer l'affichage du menu déroulant
        document.getElementById("sort-btn").addEventListener("click", function() {
            const dropdownContent = document.getElementById("dropdown-content");
            dropdownContent.style.display = dropdownContent.style.display === "none" ? "block" : "none";
        });

        // Gérer les clics sur les options du menu déroulant
        document.querySelectorAll("#dropdown-content a").forEach(function(link) {
            link.addEventListener("click", function(event) {
                event.preventDefault();
                const sortBy = link.getAttribute("data-sort");
                const currentOrder = "<?php echo $order; ?>";
                let order = link.getAttribute("data-order");

                if (order === "toggle") {
                    order = currentOrder === "asc" ? "desc" : "asc";
                }

                window.location.href = "?sort_by=" + sortBy + "&order=" + order + "&page=<?php echo $num_page; ?>";
            });
        });

        // Masquer le menu déroulant en dehors des clics
        document.addEventListener("click", function(event) {
            const dropdownContent = document.getElementById("dropdown-content");
            const sortBtn = document.getElementById("sort-btn");
            if (event.target !== sortBtn && event.target !== dropdownContent) {
                dropdownContent.style.display = "none";
            }
        });
    </script>

</body>

</html>