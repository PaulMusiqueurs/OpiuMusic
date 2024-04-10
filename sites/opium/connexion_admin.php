<?php
session_start();

// Inclure le fichier de connexion à la base de données
include("includes/connectBDD.php");

// Initialiser les variables pour stocker les erreurs
$emailErr = $passwordErr = "";
$email = $password = "";

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Valider et récupérer les données du formulaire
    if (empty($_POST["email"])) {
        $emailErr = "L'adresse email est requise";
    } else {
        $email = htmlspecialchars($_POST["email"]);
    }

    if (empty($_POST["password"])) {
        $passwordErr = "Le mot de passe est requis";
    } else {
        $password = htmlspecialchars($_POST["password"]);
    }

    // Vérifier les informations d'identification dans la base de données
    if (!empty($email) && !empty($password)) {
        $connexion = connecterBaseDeDonnees();
        $requete = $connexion->prepare("SELECT * FROM authentifier WHERE admin_mail = ?");
        $requete->execute(array($email));
        $result = $requete->fetch(PDO::FETCH_ASSOC);

        // Vérifier si l'utilisateur existe dans la base de données
        if ($result) {
            $hashed_password = $result["admin_pswd"];

            // Vérifier si le mot de passe correspond au mot de passe hashé dans la base de données
            if (password_verify($password, $hashed_password)) {
                // Démarrer la session et définir la variable de session loggedin à true
                $_SESSION["loggedin"] = true;
                $_SESSION["admin"] = true; // Définir l'administrateur comme connecté
                $_SESSION["email"] = $email;

                // Rediriger vers la page crud_son.php si l'utilisateur est authentifié
                header("location: crud_son.php");
                exit();
            } else {
                $passwordErr = "Votre identifiant ou votre mot de passe n'est pas valide";
            }
        } else {
            $emailErr = "Votre identifiant ou votre mot de passe n'est pas valide";
        }

        // Fermer la connexion et la requête
        $requete->closeCursor(); // Libère les ressources associées à la requête
        $connexion = null; // Ferme la connexion à la base de données
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin</title>
</head>
<body>
    <h1>Connexion Admin</h1>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="email">Adresse e-mail :</label><br>
        <input type="email" id="email" name="email" required>
        <span class="error"><?php echo $emailErr; ?></span><br><br>

        <label for="password">Mot de passe :</label><br>
        <input type="password" id="password" name="password" required>
        <span class="error"><?php echo $passwordErr; ?></span><br><br>

        <input type="submit" value="Se connecter">
    </form>
</body>
</html>