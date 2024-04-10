<?php
// Fonction pour hasher les mots de passe
function hashPassword($password) {
    // Utilisation de l'algorithme bcrypt pour le hachage
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    return $hashed_password;
}
?>