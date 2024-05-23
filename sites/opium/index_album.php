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
$num_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($num_page < 1) {
    $num_page = 1;
}
$debut = ($num_page - 1) * $elements_par_page;

// Définir les valeurs autorisées pour `sort_by` et `order`
$valid_sort_columns = ['id_album', 'title_album', 'artist_album', 'time_album', 'date_album'];
$valid_orders = ['asc', 'desc'];

// Filtrer et valider les paramètres `sort_by` et `order`
$sort_by = isset($_GET['sort_by']) && in_array($_GET['sort_by'], $valid_sort_columns) ? $_GET['sort_by'] : 'id_album';
$order = isset($_GET['order']) && in_array($_GET['order'], $valid_orders) ? $_GET['order'] : 'desc';

// Préparation de la requête SQL avec les paramètres filtrés
$sql = "SELECT * FROM album ORDER BY $sort_by $order LIMIT :debut, :elements_par_page";
$stmt = $connexion->prepare($sql);
$stmt->bindValue(':debut', $debut, PDO::PARAM_INT);
$stmt->bindValue(':elements_par_page', $elements_par_page, PDO::PARAM_INT);

// Exécuter la requête et récupérer les résultats
$stmt->execute();

// Création d'un tableau pour stocker les données des albums
$albums = [];

// Parcourir les résultats et les ajouter au tableau
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $album = [
        'id' => $row['id_album'],
        'title' => $row['title_album'],
        'artist' => $row['artist_album'],
        'cover' => $row['cover_album'],
        'type' => $row['type_album']
    ];
    $albums[] = $album;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>OpiumAlbum</title>
    <link rel="stylesheet" href="assets/index_album.css">
</head>

<body>
    <!-- Barre de navigation -->
    <div id="navbar">
        <div id="logo">OpiuMusique</div>

        <!-- Boutons de gauche -->
        <div id="left-buttons">
            <a href="index.php"><button>Titres</button></a>
            <a href="#"><button>Artistes</button></a>
            <a href="#"><button>Événement</button></a>
        </div>

        <!-- Bouton "Trier" avec menu déroulant -->
        <div id="dropdown">
            <button id="sort-btn">Trier</button>
            <div id="dropdown-content" style="display: none;">
                <a href="#" data-sort="id_album" data-order="desc">Date d'ajout</a>
                <a href="#" data-sort="title_album" data-order="asc">Titre</a>
                <a href="#" data-sort="artist_album" data-order="asc">Artiste</a>
                <a href="#" data-sort="time_album" data-order="asc">Durée</a>
                <a href="#" data-sort="date_album" data-order="desc">Date de sortie</a>
                <hr>
                <a href="#" data-order="toggle">Inverser l'ordre</a>
            </div>
        </div>

        <!-- Boutons de droite -->
        <div id="right-buttons">
            <a href="#"><button>Contact</button></a>
            <a href="crud_son.php"><button id="admin-btn">Admin</button></a>
        </div>
    </div>

    <!-- Espace entre la barre de navigation et le contenu -->
    <div style="height: 20px;"></div>

    <!-- Contenu -->
    <div class="objets-container">
        <?php
        foreach ($albums as $album) {
            $url = "album.php?id=" . $album['id'];
            echo "<div class='col'>";
            echo "<a href='$url' class='music-link'>";
            echo "<img src='images/album/" . $album['id'] . ".jpg' class='objet-img'>";
            echo "<div class='title'>" . $album['title'] . "</div>";
            echo "<div class='artist'>" . $album['artist'] . "</div>";
            echo "<div class='type'>" . $album['type'] . "</div>";
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
            $sql_count = "SELECT COUNT(*) as count FROM album";
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

                // Récupérer les paramètres `sort_by` et `order` de l'URL actuelle
                const currentSortBy = "<?php echo $sort_by; ?>";
                const currentOrder = "<?php echo $order; ?>";

                // Définir `sortBy` et `order` selon les attributs de l'option cliquée
                let sortBy = link.getAttribute("data-sort");
                let order = link.getAttribute("data-order");

                // Si "inverser l'ordre" est cliqué, inverser l'ordre actuel
                if (order === "toggle") {
                    order = currentOrder === "asc" ? "desc" : "asc";
                    // Garder la colonne de tri actuelle
                    sortBy = currentSortBy;
                } else {
                    // Si `sortBy` est indéfini, utiliser `currentSortBy`
                    if (!sortBy) {
                        sortBy = currentSortBy;
                    }
                    // Si `order` est indéfini, utiliser `currentOrder`
                    if (!order) {
                        order = currentOrder;
                    }
                }

                // Construire la nouvelle URL avec les paramètres de tri et de pagination
                const newUrl = `?sort_by=${sortBy}&order=${order}&page=<?php echo $num_page; ?>`;
                window.location.href = newUrl;
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