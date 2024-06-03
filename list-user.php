<?php

include('header-init.php');

//include('extraction-jwt.php');

$requete = $connexion->query('SELECT u.id , u.email, u.firstname, u.lastname, r.name as role
                              FROM utilisateur as u
                              JOIN role as r ON u.id_role = r.id');

$utilisateurs = $requete->fetchAll();

echo json_encode($utilisateurs);
