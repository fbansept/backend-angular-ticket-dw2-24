<?php

include('header-init.php');

$json = file_get_contents('php://input');

$utilisateur = json_decode($json);

$requete = $connexion->prepare("SELECT u.id, u.email, u.password, u.firstname, u.lastname, r.name AS role
                                FROM utilisateur AS u
                                JOIN role AS r ON u.id_role = r.id
                                WHERE u.email = :email");
$requete->bindValue("email", $utilisateur->email);
$requete->execute();
$utilisateurBdd = $requete->fetch();

//si l'email de l'utilisateur n'existe pas ou qu'il s'est trompé de mot de passe
if (!$utilisateurBdd || !password_verify($utilisateur->password, $utilisateurBdd['password'])) {

    echo '{"message":"Login ou mot de passe incorect"}';
    http_response_code(403);
    exit();
}

function base64UrlEncode($data)
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

$header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);


$payload = json_encode([
    'id' => $utilisateurBdd['id'],
    'role' => $utilisateurBdd['role'],
    'email' => $utilisateurBdd['email'],
    'firstname' => $utilisateurBdd['firstname'],
    'lastname' => $utilisateurBdd['lastname']
]);


// Encoder en Base64 URL-safe
$base64UrlHeader = base64UrlEncode($header);
$base64UrlPayload = base64UrlEncode($payload);

// Créer la signature
$signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, 'votre_cle_secrete', true);
$base64UrlSignature = base64UrlEncode($signature);

// Assembler le token
$jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

echo '{"jwt" : "' . $jwt . '"}';
