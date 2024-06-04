<?php

/**
 * Pour PHP Storm
 * @var object $utilisateur
 */

include('header-init.php');
include('extraction-jwt.php');

if ($utilisateur->role != 'Administrateur') {
    echo '{"message":"Vous n\'avez pas les droits nécessaires"}';
    http_response_code(403);
    exit();
}

$requete = $connexion->query('SELECT u.id , u.email, u.firstname, u.lastname, r.name as role
                              FROM utilisateur as u
                              JOIN role as r ON u.id_role = r.id');

$utilisateurs = $requete->fetchAll();

echo json_encode($utilisateurs);
