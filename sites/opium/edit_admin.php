<?php
// Vérifier si l'utilisateur est connecté en tant qu'admin
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: connexion_admin.php");
    exit();
}

// Inclure le fichier de connexion à la base de données
include("includes/connectBDD.php");

// Vérifier si la connexion à la base de données est établie avec succès
try {
    $connexion = connecterBaseDeDonnees();
} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
    // Arrêter l'exécution du script ou effectuer une autre action appropriée
    exit();
}

// Vérifier si l'utilisateur est connecté
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: connexion.php");
    exit();
}

// Vérifier si un identifiant d'administrateur est fourni dans l'URL
if (!isset($_GET['id'])) {
    echo "Identifiant d'administrateur non spécifié.";
    exit();
}

// Récupérer l'identifiant de l'administrateur depuis l'URL
$id_admin = htmlspecialchars($_GET['id']);

// Vérifier si le formulaire de modification a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["email"]) && isset($_POST["password"])) {
    $email = htmlspecialchars($_POST["email"]);
    $password = htmlspecialchars($_POST["password"]);

    // Hash du mot de passe
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Mettre à jour l'administrateur dans la base de données en utilisant une requête préparée
    $sql = "UPDATE authentifier SET admin_mail = :email, admin_pswd = :password WHERE admin_id = :id";
    $stmt = $connexion->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':id', $id_admin, PDO::PARAM_INT); // Assurer que l'identifiant est un entier
    $stmt->execute();

    // Rediriger vers la page de gestion des administrateurs après la modification
    header("Location: crud_admin.php");
    exit();
}

// Récupérer les informations de l'administrateur à modifier depuis la base de données
$sql_select = "SELECT admin_mail FROM authentifier WHERE admin_id = :id";
$stmt_select = $connexion->prepare($sql_select);
$stmt_select->bindParam(':id', $id_admin, PDO::PARAM_INT); // Assurer que l'identifiant est un entier
$stmt_select->execute();
$admin = $stmt_select->fetch(PDO::FETCH_ASSOC);

// Vérifier si l'administrateur existe
if (!$admin) {
    echo "Administrateur introuvable.";
    exit();
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Administrateur</title>
</head>
<body>
    <h1>Modifier Administrateur</h1>
    <form method="post" action="">
        <label for="email">Adresse Email :</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($admin['admin_mail']); ?>" required><br><br>
        <label for="password">Nouveau Mot de passe :</label>
        <input type="password" id="password" name="password" required><br><br>
        <input type="submit" value="Modifier">
    </form>
</body>
</html>