<!DOCTYPE html>
<html>
    <head>
        <style> 
            body {
                background-color:#31344B;
                background-size: cover;
                font-size:20px;
            }

            /*#979797 */
            #problema {
                background-color:white;
                border-radius: 10px;
                margin: 0 auto;
                margin-top:100px;
                max-width: 400px;
                padding: 20px;
                text-align: center;
            }

            div .error {
                background-color: #FFF523;
                color: black;
                margin: 0 auto;
                width:400px;
                padding: 10px 0px 10px 0px;
                text-align:center;
                border-radius:20px; 
                margin-top:20px; 
                border:2px solid black;
                font-family: Helvetica, sans-serif;
                font-size: 16px;
            }
            div .error:active {
                background-color: #FFF300;
                border-color: white;
            }
            h3 {
                color:black;
                font-family: Helvetica, sans-serif;
            }
            h5 {
                color:black;
                font-family: Helvetica, sans-serif;
            }
            a {
                text-align:center;
                color:black;
                text-decoration: none;
                font-family: Helvetica, sans-serif;
            }
        </style>
    </head>
    <body>
        <?php
            session_start();
            if (isset($_SESSION['access_token']) && isset($_SESSION['token_expiration_time'])) {
                $access_token = $_SESSION['access_token'];
                $expiration_time = $_SESSION['token_expiration_time'];
                
                // Llamada a la API de Microsoft para obtener información del usuario
                $url = 'https://graph.microsoft.com/v1.0/me';
                $headers = array(
                    'Authorization: Bearer ' . $access_token,
                    'Content-Type: application/json'
                );
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                $response = curl_exec($ch);
                curl_close($ch);
              
                $user_data = json_decode($response, true);
              
                if (time() > $expiration_time) {
                    // El token ha expirado
                    // Redirigir al usuario a la página de inicio de sesión
                    header('Location: https://dobetter.cl/GestionDeProyectos/login.php');
                    exit;
                  }
                if (isset($user_data['error'])) {
                    // El token no es válido
                    // Redirigir al usuario a la página de inicio de sesión
                    header('Location: https://dobetter.cl/GestionDeProyectos/login.php');
                    exit;
                } else {
                    // El token es válido
                    // Continuar con la sesión de usuario
                }
            } else {
                // El token de acceso no está almacenado en la sesión
                // Redirigir al usuario a la página de inicio de sesión
                header('Location: https://dobetter.cl/GestionDeProyectos/login.php');
                exit;
            }
        ?>
        <div id="problema" class="col-lg-6 offset-lg-3">
            <h3>No tiene permiso para acceder a la sección solicitada</h3>
            <h5>Solo consultores y administradores pueden realizar esta acción</h5>
            <a href='index.php'><div class="error">Volver a buscador de proyectos</div></a>
        </div>
    </body>
</html>