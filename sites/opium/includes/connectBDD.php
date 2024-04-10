<?php

function connecterBaseDeDonnees() {
    // Informations de connexion à la base de données
    $serveur = "db"; // ou l'adresse IP du serveur de base de données
    $utilisateur = "root"; // nom d'utilisateur pour la base de données
    $motDePasse = "root"; // mot de passe de la base de données
    $baseDeDonnees = "authentification"; // nom de la base de données
    $port = 3306; // port sur lequel MySQL écoute (par défaut 3306)

    try {
        // Connexion à la base de données avec PDO
        $connexion = new PDO("mysql:host=$serveur;dbname=$baseDeDonnees;port=$port;charset=utf8", $utilisateur, $motDePasse);

        // Activer le mode d'exception PDO pour des erreurs de requête SQL
        $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $connexion;
    } catch (PDOException $e) {
        // En cas d'erreur de connexion, l'exception est levée
        // Pas besoin d'afficher de message ici
        throw $e; // Propage l'exception pour la gérer à un niveau supérieur
    }
}
?>