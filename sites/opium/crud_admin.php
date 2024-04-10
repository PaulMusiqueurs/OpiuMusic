<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    // Rediriger vers la page de connexion
    header("Location: connexion.php");
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

// Vérifier si une action de suppression est demandée
if (isset($_GET['delete']) && $_GET['delete'] == 'admin' && isset($_GET['id'])) {
    // Afficher un message de confirmation pour la suppression
    echo "<script>
            if(confirm('Êtes-vous sûr de vouloir supprimer cet administrateur ?')) {
                window.location.href = 'crud_admin.php?confirmed_delete=admin&id=" . $_GET['id'] . "';
            } else {
                window.location.href = 'crud_admin.php';
            }
          </script>";
    exit();
}

// Vérifier si la suppression est confirmée
if (isset($_GET['confirmed_delete']) && $_GET['confirmed_delete'] == 'admin' && isset($_GET['id'])) {
    // Supprimer l'administrateur correspondant à l'ID fourni
    $id = $_GET['id'];
    $sql = "DELETE FROM authentifier WHERE admin_id = :id";
    $stmt = $connexion->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    // Rediriger vers la page actuelle pour actualiser la liste des administrateurs
    header("Location: crud_admin.php");
    exit();
}

// Récupérer tous les administrateurs depuis la table authentification
$sql = "SELECT admin_id, admin_mail FROM authentifier";
$result = $connexion->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Administrateurs</title>
    <link rel="stylesheet" href="assets/crud_admin.css">
</head>
<body>
    <div class="container">
        <h1>Liste des Administrateurs</h1>
        <!-- Bouton de retour à la page CRUD Son -->
        <a href="crud_son.php"><button class="btn-return">Retour à la page CRUD Son</button></a>
        
        <!-- Bouton "Ajouter admin" -->
        <a href="add_admin.php"><button class="btn-add">Ajouter admin</button></a>
        
        <table>
            <tr>
                <th>ID</th>
                <th>Adresse Email</th>
                <th>Actions</th>
            </tr>
            <?php
            // Vérifier si la variable $result est définie et non vide
            if (isset($result) && $result && $result->rowCount() > 0) {
                // Parcourir les résultats et afficher chaque administrateur dans une ligne du tableau
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . $row["admin_id"] . "</td>";
                    echo "<td>" . $row["admin_mail"] . "</td>";
                    echo "<td>
                            <a href='edit_admin.php?id=" . $row["admin_id"] . "'>Modifier</a> | 
                            <a href='?delete=admin&id=" . $row["admin_id"] . "'>Supprimer</a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>Aucun administrateur trouvé.</td></tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>