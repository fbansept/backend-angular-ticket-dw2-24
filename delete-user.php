<?php

include('header-init.php');

$idUtilisateurAsupprimer = $_GET['id'];

$requete = $connexion->prepare("DELETE FROM utilisateur WHERE id = :id");

$requete->bindValue('id', $idUtilisateurAsupprimer);

$requete->execute();

echo '{"message" : "L\'utilisateur a bien été supprimé"}';
