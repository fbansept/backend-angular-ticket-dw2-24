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

$idUtilisateurAsupprimer = $_GET['id'];

$requete = $connexion->prepare("DELETE FROM utilisateur WHERE id = :id");

$requete->bindValue('id', $idUtilisateurAsupprimer);

$requete->execute();

echo '{"message" : "L\'utilisateur a bien été supprimé"}';
