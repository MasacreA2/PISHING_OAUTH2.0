<?php
// Iniciar sesión
session_start();

// Cargar la biblioteca de Google API PHP Client
require_once __DIR__ . '/vendor/autoload.php';

// Configurar las credenciales de OAuth 2.0
$clientID = 'TU CLIENTE  ID DE LA APLICACION WEB ';
$clientSecret = 'TU CLIENTE SECRETO DE LA APLICACION WEB ';
$redirectUri = 'http://localhost/CAPTOKEN/redirect.php'; // URL de redireccionamiento después de la autorización

// Crear un cliente de Google API PHP
$client = new Google\Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);

// Establecer los alcances necesarios
$client->addScope('email');
$client->addScope('profile');
$client->addScope('https://www.googleapis.com/auth/contacts.readonly');
$client->addScope('https://www.googleapis.com/auth/gmail.readonly');
$client->addScope('https://www.googleapis.com/auth/userinfo.email');
$client->addScope('https://www.googleapis.com/auth/userinfo.profile');
$client->addScope('https://www.googleapis.com/auth/gmail.send');
$client->addScope('https://www.googleapis.com/auth/user.phonenumbers.read');
$client->addScope('https://www.googleapis.com/auth/user.birthday.read');


// Solicitar acceso sin conexión para obtener el refresh token
$client->setAccessType('offline');

// Manejar el proceso de autenticación
if (isset($_GET['code'])) {
    // Intercambiar el código de autorización por un token de acceso
    $client->fetchAccessTokenWithAuthCode($_GET['code']);

    // Obtener el token de acceso
    $accessToken = $client->getAccessToken();

    // Guardar el token de acceso en la sesión
    $_SESSION['access_token'] = $accessToken;

    // Redirigir al usuario a la página principal
    header('Location: index.php');
    exit();
} else {
    // Generar la URL de autenticación de Google
    $authUrl = $client->createAuthUrl();

    // Redirigir al usuario a la página de autenticación de Google
    header('Location: ' . $authUrl);
    exit();
}

?>
