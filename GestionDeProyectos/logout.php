<?php
    session_start();

    // Invalida el token de acceso en la sesión
    unset($_SESSION['access_token']);
    
    // Invalida todas las demás variables de sesión asociadas con la sesión actual
    $_SESSION = array();
    session_destroy();
    
    // Redirige al usuario a la página de inicio de sesión de Microsoft
    $redirect_uri = urlencode('https://dobetter.cl/GestionDeProyectos/login.php');
    $logout_url = "https://login.microsoftonline.com/common/oauth2/v2.0/logout?post_logout_redirect_uri=$redirect_uri";
    header('Location: ' . $logout_url);
    exit;
    
?>
