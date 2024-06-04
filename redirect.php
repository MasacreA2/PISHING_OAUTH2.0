<?php
// Iniciar sesión
session_start();

// Cargar la biblioteca de Google API PHP Client
require_once __DIR__ . '/vendor/autoload.php';

// Configurar las credenciales de OAuth 2.0
$clientID = 'TU CLIENTE ID OJO  DE TU APLICACION WEB  ';
$clientSecret = 'TU CLIENTE SECRETO LO MISMO DE TU APLICACION WEB ';
$redirectUri = 'http://localhost/CAPTOKEN/redirect.php'; // URL de redireccionamiento después de la autorización

// Crear un cliente de Google API PHP
$client = new Google\Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);


// Establecer los alcances necesarios

$client->addScope('email');
$client->addScope('profile');

// Solicitar acceso sin conexión para obtener el refresh token
$client->setAccessType('offline');

// Manejar el proceso de autenticación
if (isset($_GET['code'])) {
    // Intercambiar el código de autorización por un token de acceso y un refresh token
    $accessToken = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    // Obtener el token de acceso y el refresh token
    if (isset($accessToken['access_token'])) {
        $accessToken = $accessToken['access_token'];
        $refreshToken = $client->getRefreshToken();
    } else {
        // Si no se obtiene un token de acceso, mostrar un mensaje de error
        echo "Error: No se pudo obtener el token de acceso.";
        exit();
    }

    // Guardar el token de acceso y el refresh token en la sesión
    $_SESSION['access_token'] = $accessToken;
    $_SESSION['refresh_token'] = $refreshToken;

    // Insertar los tokens en la base de datos
    // Conexión a la base de datos (reemplaza con tus propias credenciales)
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "token_users";

    // Crear conexión
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar la conexión
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Insertar los tokens en la base de datos
    $accessToken = json_encode($accessToken);
    $refreshToken = json_encode($refreshToken);
    $sql = "INSERT INTO tokens (token, refresh_token) VALUES ('$accessToken', '$refreshToken')";

    if ($conn->query($sql) === TRUE) {
        echo "Tokens insertados correctamente en la base de datos.";
    } else {
        echo "Error al insertar los tokens en la base de datos: " . $conn->error;
    }

    $conn->close();

    // Redirigir al usuario a la página principal
    header('Location: index.php');
    exit();
} else {
    // Si no se recibe un código de autorización, mostrar un mensaje de error
    echo "Error: No se recibió ningún código de autorización.";
}
?>
