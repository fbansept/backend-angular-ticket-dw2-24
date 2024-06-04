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

$json = file_get_contents('php://input');

$utilisateur = json_decode($json);

$requete = $connexion->prepare("SELECT id 
                                FROM role 
                                WHERE name = :name");

$requete->bindValue("name", $utilisateur->role);
$requete->execute();

$role = $requete->fetch();

if (!$role) {
    http_response_code(400);
    echo '{"message" : "Ce role n\'exite pas"}';
    exit();
}


//si il n'y a pas de parametre "id" dans l'url
if (!isset($_GET['id'])) {
    http_response_code(400);
    echo '{"message" : "Il manque dans l\url l\'identifiant de l`\'utilisateur à modifier"}';
    exit();
}

//on recupère l'ancien utilisateur dans la base de données
$requete = $connexion->prepare("SELECT * 
                                FROM utilisateur 
                                WHERE id = :id");

$requete->bindValue("id", $_GET['id']);
$requete->execute();
$utilisateurBdd = $requete->fetch();

//si l'utilisateur n'existe pas on envoie une erreur 404
if (!$utilisateurBdd) {
    http_response_code(404);
    echo '{"message" : "L\'utilisateur n\'existe pas / plus"}';
    exit();
}

//si l'utilisateur n'a pas fourni de nouveau mot de passe
//on affecte l'ancien mot de passe
if ($utilisateur->password == '') {
    $utilisateur->password = $utilisateurBdd['password'];
} else {
    //sinon on hash le nouveau mot de passe fourni
    $utilisateur->password =
        password_hash($utilisateur->password, PASSWORD_DEFAULT);
}

$requete = $connexion->prepare("UPDATE utilisateur 
                                SET email = :email, 
                                    password = :password, 
                                    firstname = :firstname, 
                                    lastname = :lastname, 
                                    id_role = :id_role
                                WHERE id = :id");

$requete->bindValue("email", $utilisateur->email);
$requete->bindValue("password", $utilisateur->password);
$requete->bindValue("firstname", $utilisateur->firstname);
$requete->bindValue("lastname", $utilisateur->lastname);
$requete->bindValue("id_role", $role['id']);
$requete->bindValue("id", $_GET['id']);

$requete->execute();

echo '{"message" : "Modification réussie"}';
