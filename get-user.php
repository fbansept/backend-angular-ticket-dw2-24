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

if(!isset($_GET['id'])) {
    http_response_code(400);
    echo '{"message" : "il manque l\'identifiant dans l\'url"}';
    exit();
}

$idUser = $_GET['id'];

$requete = $connexion->prepare('SELECT u.id, u.email, u.firstname, u.lastname, r.name AS role
                                FROM utilisateur AS u
                                JOIN role AS r ON u.id_role = r.id
                                WHERE u.id = :id');

$requete->bindValue('id', $idUser);

$requete->execute();

$utilisateur = $requete->fetch();

if (!$utilisateur) {
    http_response_code(404);
    echo '{"message" : "utilisateur introuvable"}';
    exit();
}

echo json_encode($utilisateur);
