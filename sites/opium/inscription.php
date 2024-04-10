<?php
session_start();
include 'includes\fonction_hash.php';

// Connexion à la base de données
$host = "localhost";
$dsn = 'mysql:dbname=authentification;host=127.0.0.1';
$user = 'root';
$password = '';

$message = ""; // Variable pour stocker le message d'erreur

if (!empty($_POST)) {

    $dbh = new PDO($dsn, $user, $password);

    // Récupération des données du formulaire
    $login = $_POST['email'];
    $password = $_POST['password'];
    $hashed_password = hashPassword($password);

    // Vérification si l'email est déjà dans la base de données
    $query = $dbh->prepare("SELECT * FROM authentifier WHERE user_login = ?");
    $query->execute([$login]);
    $user = $query->fetch();

    if ($user) {
        // L'utilisateur existe déjà dans la base de données
        $message = "Cette email n'est pas valide.";
    } else {
        // Insertion des données dans la base de données
        $sql = "INSERT INTO authentifier (user_login, user_password) VALUES (?, ?)";
        $stmt = $dbh->prepare($sql);

        if ($stmt->execute([$login, $hashed_password])) {
            // Définir une variable de session pour indiquer que l'utilisateur est connecté
            $_SESSION['user_id'] = $login;

            $message = "Enregistrement réussi.";
            header("Location: welcome.php"); // Redirection vers la page de bienvenue
            exit;
        } else {
            $errorInfo = $stmt->errorInfo();
            $message = "Erreur lors de l'enregistrement : " . $errorInfo[2];
        }
    }

    $dbh = null;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Projet1</title>
    <link rel="stylesheet" href="assets\inscription.css">
</head>
<body>
    <div class="container">
        <form action="inscription.php" method="post">
            <h2>Inscription</h2>
            <label for="email">Adresse Email:</label>
            <input type="email" name="email" required>
            <label for="password">Mot de passe:</label>
            <input type="password" name="password" required>
            <button type="submit">S'inscrire</button>
            <a href="authentification.php">Déjà inscrit ?</a>
            <?php if (!empty($message)): ?>
                <p><?php echo $message; ?></p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>