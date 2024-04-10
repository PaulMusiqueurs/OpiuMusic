<?php
// Inclure le fichier de connexion à la base de données
include("includes/connectBDD.php");

try {
    // Connexion à la base de données
    $connexion = connecterBaseDeDonnees();

    // Vérifier si l'utilisateur est connecté
    session_start();
    if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
        header("Location: connexion.php");
        exit();
    }

    // Vérifier si le formulaire d'ajout a été soumis
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["email"]) && isset($_POST["password"])) {
        $email = htmlspecialchars($_POST["email"]);
        $password = htmlspecialchars($_POST["password"]);

        // Hash du mot de passe
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Ajouter l'administrateur à la base de données en utilisant une requête préparée
        $sql = "INSERT INTO authentifier (admin_mail, admin_pswd) VALUES (:email, :password)";
        $stmt = $connexion->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->execute();

        // Rediriger vers la page crud_admin.php après l'ajout réussi
        header("Location: crud_admin.php");
        exit();
    }
} catch(PDOException $e) {
    // En cas d'erreur de connexion, affichez un message d'erreur
    echo "Erreur de connexion à la base de données: " . $e->getMessage();
    exit(); // Arrête le script en cas d'erreur
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Administrateur</title>
</head>
<body>
    <h1>Ajouter un Administrateur</h1>
    <form method="post" action="">
        <label for="email">Adresse Email :</label>
        <input type="email" id="email" name="email" required><br><br>
        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" required><br><br>
        <input type="submit" value="Ajouter">
    </form>
</body>
</html>