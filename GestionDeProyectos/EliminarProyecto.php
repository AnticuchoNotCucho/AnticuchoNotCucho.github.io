<!DOCTYPE html>
<html>
    <?php
    header('Content-Type: text/html; charset=UTF-8');
    ?>
    <head>
        <meta charset="UTF-8">
        <style>
            body {
                background-image:url(assets/images/camion3.jpg);
                color: black;
                border-top-left-radius:20px;
            }    
	        a {
                color:#8A7100;
            }
        </style>
        <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon">
        <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
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
                    // Verificar si el usuario es miembro del grupo
                    $group_id = 'ad74d3ed-4d2f-4f36-9425-6ab6547dd0c5';
                    $url = 'https://graph.microsoft.com/v1.0/groups/' . $group_id . '/members/' . $user_data['id'];
                    $headers = array(
                        'Authorization: Bearer ' . $access_token,
                        'Content-Type: application/json'
                    );
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    $response = curl_exec($ch);
                    curl_close($ch);
                
                    $member_data = json_decode($response, true);
                
                    if (isset($member_data['error'])) {
                        // El usuario no es miembro del grupo
                        // Redirigir al usuario a la página de inicio de sesión
                        header('Location: https://dobetter.cl/GestionDeProyectos/ErrorPermiso.php');
                        exit;
                    } else {
                        // El usuario es miembro del grupo
                        // Continuar con la sesión de usuario y permitir el acceso al archivo PHP
                    }
                }
            } else {
                // El token de acceso no está almacenado en la sesión
                // Redirigir al usuario a la página de inicio de sesión
                header('Location: https://dobetter.cl/GestionDeProyectos/login.php');
                exit;
            }

            header('Content-Type: text/html; charset=UTF-8');
            
            # DATOS DE CONEXIÓN A LA BASE DE DATOS
            $host='rds-db-serviciosweb.ct7iedmxopcu.us-east-1.rds.amazonaws.com';
            $dbname='GestionDeProyectos';
            $username='admin';
            $password ='DoBetterBI';
            $puerto=3306;
                    
            try {
                # Conexión a bd mediante PDO (evitar inyecciones)
                $pdo = new PDO("mysql:host=$host;port=$puerto;dbname=$dbname;charset=utf8", $username, $password);
                
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
                # Se recibe desde el input 'input1'
                $variable1=$_GET['input1'];
            
                # Se obtiene información para luego eliminar registros que no sirvan
                $query="SELECT ID_Cliente, ID_Patrocinador, Acta FROM Proyecto WHERE Link=:valor1";
                $stmt = $pdo->prepare($query);
                $stmt->bindValue(':valor1', $variable1, PDO::PARAM_STR);
                $stmt->execute();
                $result = $stmt->fetchAll();
            
                # Si no hay proyectos con el link, no se podrá eliminar nada
                if(count($result) < 1) {
                    echo "<div style='background-color: white; color: black; padding: 10px 0px 10px 0px; width:400px; margin-left:550px; text-align:center;border-radius:20px; margin-top:50px; border:2px solid black;'>";
                    echo "No existe ningún proyecto con el link que está ingresando.";
                    echo "</div>";
                    echo "<a href='eliminar.php'><div style='background-color: white; color: black; padding: 10px 0px 10px 0px; width:200px; margin-left:650px; text-align:center;border-radius:20px; margin-top:50px; border:2px solid black;'>Volver a sección de eliminar proyectos.</div></a>";
                    die();
                } else {
                    # Hay algún proyecto con el link, por lo que se obtienen los siguientes valores
                    $id_cliente = $result[0]['ID_Cliente'];
                    $id_patrocinador = $result[0]['ID_Patrocinador'];
                    $path = $result[0]['Acta'];

                    # Se extrae nombre del archivo únicamente
                    $file_name = basename($path);

                    # Por si acaso se reemplazan caracteres problemáticos
                    $file_name = str_replace(" ", "%20", $file_name);

                    $site_id = '0eef7ec9-9469-4230-a487-62fa78f630ad';
                    $folder_id = '01JDGSHHREWEUE6IKJTRBJTWGVY44VLR4Q';

                    # URL del endpoint de carga de archivos de la API de Microsoft Graph
                    $url = 'https://graph.microsoft.com/v1.0/sites/' . $site_id . '/drive/items/' . $folder_id . ':/'. $file_name;

                    # Configuración de la solicitud HTTP con el encabezado de autorización
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Authorization: Bearer ' . $access_token
                    ));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    # Ejecutar la solicitud HTTP
                    $response = curl_exec($ch);

                    # Verificar si hubo errores
                    if (curl_errno($ch)) {
                        echo "<div style='background-color: white; color: black; padding: 10px 0px 10px 0px; width:400px; margin-left:550px; text-align:center;border-radius:20px; margin-top:50px; border:2px solid black;'>";
                        echo 'Error al eliminar el archivo: ' . curl_error($ch);
                        echo "</div>";
                        echo "<a href='eliminar.php'><div style='background-color: white; color: black; padding: 10px 0px 10px 0px; width:200px; margin-left:650px; text-align:center;border-radius:20px; margin-top:50px; border:2px solid black;'>Volver a sección de eliminar proyectos.</div></a>";
                        die();
                    } 

                    # Cerrar la conexión cURL
                    curl_close($ch);

                    # Se obtienen todos los proyectos con mismo ID_Cliente que el proyecto borrado
                    $query2="SELECT ID FROM Proyecto WHERE ID_Cliente=:id_cliente";
                    $stmt2 = $pdo->prepare($query2);
                    $stmt2->bindParam(':id_cliente', $id_cliente);
                    $stmt2->execute();
                    $result1 = $stmt2->fetchAll();
                
                    # Se obtienen todos los proyectos con mismo ID_Patrocinador que el proyecto borrado
                    $query3="SELECT ID FROM Proyecto WHERE ID_Patrocinador=:id_patrocinador";
                    $stmt3 = $pdo->prepare($query3);
                    $stmt3->bindParam(':id_patrocinador', $id_patrocinador);
                    $stmt3->execute();
                    $result2 = $stmt3->fetchAll();
                
                    # Se elimina proyecto con el link ingresado
                    $query4 = "DELETE FROM Proyecto WHERE Link=:valor1";
                    $stmt4 = $pdo->prepare($query4);
                    $stmt4->bindValue(':valor1', $variable1, PDO::PARAM_STR);
                    $stmt4->execute();
                
                    # Si no existe ningún proyecto, se elimina el cliente en cuestión de la bd
                    if(count($result1) == 1) {
                        # Se elimina cliente
                        $query5 = "DELETE FROM Cliente WHERE ID=:id_cliente";
                        $stmt5 = $pdo->prepare($query5);
                        $stmt5->bindParam(':id_cliente', $id_cliente);
                        $stmt5->execute();
                    }
                
                    # Si no existe ningún proyecto, se elimina el patrocinador en cuestión de la bd
                    if(count($result2) == 1) {
                        # Se elimina patrocinador
                        $query6 = "DELETE FROM Patrocinador WHERE ID=:id_patrocinador";
                        $stmt6 = $pdo->prepare($query6);
                        $stmt6->bindParam(':id_patrocinador', $id_patrocinador);
                        $stmt6->execute();
                    }
                    echo "<div style='background-color: white; color: black; padding: 10px 0px 10px 0px; width:400px; margin-left:550px; text-align:center;border-radius:20px; margin-top:50px; border:2px solid black;'>";
                    echo "Proyecto eliminado con éxito.";
                    echo "</div>";
                }
                echo "<a href='eliminar.php'><div style='background-color: white; color: black; padding: 10px 0px 10px 0px; width:200px; margin-left:650px; text-align:center;border-radius:20px; margin-top:50px; border:2px solid black;'>Volver a eliminador de proyectos.</div></a>";
            } catch(Exception $e) {
                echo "<div style='background-color: white; color: black; padding: 10px 0px 10px 0px; width:400px; margin-left:550px; text-align:center;border-radius:20px; margin-top:50px; border:2px solid black;'>";
                echo "Ocurrió un error, inténtelo nuevamente por favor.";
                echo "</div>";
                echo "<a href='eliminar.php'><div style='background-color: white; color: black; padding: 10px 0px 10px 0px; width:200px; margin-left:650px; text-align:center;border-radius:20px; margin-top:50px; border:2px solid black;'>Volver a sección de eliminar proyectos.</div></a>";
                die();
            }
        ?>
    </body>
</html>