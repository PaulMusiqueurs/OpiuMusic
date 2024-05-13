<?php

// Configurez la session de manière sécurisée
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);


session_start();

// Inclure le fichier de connexion à la base de données
include("includes/connectBDD.php");

// Initialisez les variables pour stocker les erreurs
$emailErr = $passwordErr = "";
$email = $password = "";

// Vérifiez si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validez et récupérez les données du formulaire
    if (empty($_POST["email"])) {
        $emailErr = "L'adresse e-mail est requise";
    } else {
        // Nettoyez et validez l'email
        $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Vos identifiants sont incorrectes";
            $email = "";
        }
    }

    if (empty($_POST["password"])) {
        $passwordErr = "Le mot de passe est requis";
    } else {
        // Nettoyez le mot de passe
        $password = htmlspecialchars($_POST["password"]);
    }

    // Vérifiez les informations d'identification dans la base de données
    if (!empty($email) && !empty($password)) {
        $connexion = connecterBaseDeDonnees();
        $requete = $connexion->prepare("SELECT * FROM authentifier WHERE admin_mail = ?");
        $requete->execute([$email]);
        $result = $requete->fetch(PDO::FETCH_ASSOC);

        // Vérifiez si l'utilisateur existe dans la base de données
        if ($result) {
            $hashed_password = $result["admin_pswd"];

            // Vérifiez si le mot de passe correspond au mot de passe hashé dans la base de données
            if (password_verify($password, $hashed_password)) {
                // Démarrez la session et définissez les variables de session
                session_regenerate_id(); // Empêche la fixation de session
                $_SESSION["loggedin"] = true;
                $_SESSION["admin"] = true;
                $_SESSION["email"] = $email;

                // Redirigez vers la page `crud_son.php` si l'utilisateur est authentifié
                header("location: crud_son.php");
                exit();
            } else {
                $passwordErr = "Votre identifiant ou votre mot de passe n'est pas valide";
            }
        } else {
            $emailErr = "Votre identifiant ou votre mot de passe n'est pas valide";
        }

        // Fermez la requête et la connexion
        $requete->closeCursor(); // Libérez les ressources associées à la requête
        $connexion = null; // Fermez la connexion à la base de données
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="DENY">
    <meta http-equiv="X-XSS-Protection" content="1; mode=block">
    <title>Connexion Admin</title>
</head>
<body>
    <h1>Connexion Admin</h1>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="email">Adresse e-mail :</label><br>
        <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($email); ?>">
        <span class="error"><?php echo htmlspecialchars($emailErr); ?></span><br><br>

        <label for="password">Mot de passe :</label><br>
        <input type="password" id="password" name="password" required>
        <span class="error"><?php echo htmlspecialchars($passwordErr); ?></span><br><br>

        <input type="submit" value="Se connecter">
    </form>
</body>
</html>