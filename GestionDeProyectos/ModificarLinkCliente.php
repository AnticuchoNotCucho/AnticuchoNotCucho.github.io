<!DOCTYPE html>
<html>
    <head>
        <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <meta charset="UTF-8">
        <style>
	        body {
                background-image:url(assets/images/camion3.jpg);
                color: black;
                border-top-left-radius:20px;
            }    
	        table, th, td{
	        	border: 1px solid black;
	        	border-radius:0px;
	        	text-align: left;
	        	padding: 5px;
                background-color:white;
            }
            table {
                margin-left:600px;
                border-radius:20px;
                background-color:black;
                padding:2px;
            }
            th {
              background-color: #FFF33A ;
            }
            th:nth-child(1) {
                border-top-left-radius:20px;
            }
            th:nth-child(2) {
                border-top-right-radius:20px;
            }
            tr:nth-child(2) td:first-child {
                border-bottom-left-radius: 20px;
            }
            tr:nth-child(2) td:last-child {
                border-bottom-right-radius: 20px;
            }
            /* Para cambiar color de textos href*/
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
                
                # Se reciben desde el input 'new_Link' y 'Link'.
                $new_Link=$_GET['new_Link'];
                $Cliente=$_GET['Cliente'];
            
                # Consulta para saber si existe algún cliente con el nombre introducido
                $query2="SELECT ID 
                            FROM Cliente
                            WHERE Nombre=:Cliente";
                $stmt2 = $pdo->prepare($query2);
                $stmt2->bindValue(':Cliente', $Cliente, PDO::PARAM_STR);
                $stmt2->execute();
                $result1=$stmt2->fetchAll();
            
                if(count($result1) < 1)  {
                    echo "<div style='background-color: white; color: black; padding: 10px 0px 10px 0px; width:400px; margin-left:550px; text-align:center;border-radius:20px; margin-top:50px; border:2px solid black;'>";
                    echo "No existe ningún cliente con el nombre introducido. No es posible realizar modificaciones.";
                    echo "</div>";
                } else {
                    # Consulta para actualizar link del proyecto
                    $query="UPDATE Cliente
                            SET Link=:new_Link
                            WHERE Nombre=:Cliente";
                    $stmt = $pdo->prepare($query);
                    $stmt->bindValue(':new_Link', $new_Link, PDO::PARAM_STR);
                    $stmt->bindValue(':Cliente', $Cliente, PDO::PARAM_STR);
                    $stmt->execute();
                    
                    # Consulta para mostrar cómo quedaron los nuevos datos en el proyecto
                    $query1="SELECT Nombre, Link
                                FROM Cliente
                                WHERE Nombre=:Cliente";
                    $stmt1 = $pdo->prepare($query1);
                    $stmt1->bindValue(':Cliente', $Cliente, PDO::PARAM_STR);
                    $stmt1->execute();
                    
                    # Luego se obtienen los resultados.
                    $result = $stmt1->fetchAll();
                
                    # Esta sección corresponde al header de la tabla, agregar o quitar cuantas columnas
                    # se quieran mostrar de la tabla de la base de datos.
                    echo "<table>";
                    echo "<tr>
                          <th> Nombre de Cliente </th>
                          <th> Link Cliente</th>      
                        </tr>";       
                
                    # Resultados de la consulta
                    foreach ($result as $row) {
                        echo "<tr>";
                        echo "<td>" . nl2br($row['Nombre']) . "</td>";
                        echo "<td><a href='" . $row['Link'] . "'>".$row['Link']."</a></td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                }
                echo "<a href='modificador.php'><div style='background-color: white; color: black; padding: 10px 0px 10px 0px; width:200px; margin-left:650px; text-align:center;border-radius:20px; margin-top:50px; border:2px solid black;'>Volver a modificador de proyectos.</div></a>";
            }
            # Esta sección imprime un error en caso de haber uno.
            catch(PDOException $e){
                echo "<div style='background-color: white; color: black; padding: 10px 0px 10px 0px; width:400px; margin-left:550px; text-align:center;border-radius:20px; margin-top:50px; border:2px solid black;'>";
                echo "Ocurrió un error, inténtelo nuevamente por favor.";
                echo "</div>";
                echo "<a href='modificador.php'><div style='background-color: white; color: black; padding: 10px 0px 10px 0px; width:200px; margin-left:650px; text-align:center;border-radius:20px; margin-top:50px; border:2px solid black;'>Volver a modificador de proyectos.</div></a>";
                die();
            }
        ?>
    </body>
</html>