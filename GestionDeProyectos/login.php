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
            #autenticacion {
                background-color: white;
                border-radius: 10px;
                margin: 0 auto;
                margin-top:100px;
                max-width: 400px;
                padding: 20px;
                text-align: center;
            }

            div .login {
                background-color: #FFF523;
                color: black;
                margin: 0 auto;
                width:200px;
                padding: 10px 0px 10px 0px;
                text-align:center;
                border-radius:20px; 
                margin-top:50px; 
                border:2px solid black;
                font-family: Helvetica, sans-serif;
                font-size: 16px;
            }
            div .login:active {
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
            img {
                background-color:white;
            }
            a {
                color:black;
                text-decoration: none;
                font-family: Helvetica, sans-serif;
            }
        </style>
    </head>
    <body>
        <?php
            session_start();
            // Obtener token de acceso
            if(isset($_GET['code'])) {
                $auth_code = $_GET['code'];
                $tenant = "fce8a92a-077e-4173-8e55-f264aeec1e48";
                $client_id = "aae537ed-7b44-47a8-af21-c7a45d363990";
                $client_secret = "yxd8Q~cFnjLhy.08LGIhJegbvqWSi2lRGgQmDbeF";
                $redirect_uri = "https://dobetter.cl/GestionDeProyectos/login.php";
                
                // Obtener token de acceso de Azure AD
                $url = "https://login.microsoftonline.com/$tenant/oauth2/v2.0/token";
                $post_fields = "grant_type=authorization_code&client_id=$client_id&client_secret=$client_secret&code=$auth_code&redirect_uri=$redirect_uri";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $token_response = curl_exec($ch);
                curl_close($ch);
                
                // Decodificar token de acceso
                $token_data = json_decode($token_response, true);
                $access_token = $token_data['access_token'];
                
                $lifetime = 3600; // 1 hora
                $expiration_time = time() + $lifetime;
                
                // Almacenar token de acceso en la sesión
                $_SESSION['access_token'] = $access_token;
                $_SESSION['token_expiration_time'] = $expiration_time;
                
                // Redirigir al usuario a la página principal
                header('Location: https://dobetter.cl/GestionDeProyectos/index.php');
                exit;
            }
        ?>
        <div id="autenticacion" class="col-lg-6 offset-lg-3">
            <img src="assets/images/logo.png" alt="LogoDoBetter">
            <h3> Bienvenido al gestionador de proyectos de Do Better </h3>
            <h5> Para acceder debe iniciar sesión con su cuenta corporativa de Do Better, luego será redirigido a la página principal. </h5>
            <a href='https://login.microsoftonline.com/fce8a92a-077e-4173-8e55-f264aeec1e48/oauth2/v2.0/authorize?client_id=aae537ed-7b44-47a8-af21-c7a45d363990&response_type=code&redirect_uri=https://dobetter.cl/GestionDeProyectos/login.php&response_mode=query&scope=openid%20profile%20email'><div class="login">Inicio de sesión con Microsoft.</div></a>
        </div>
    </body>
</html>