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
	        table, th, td{
	        	border: 1px solid black;
	        	border-radius:0px;
	        	text-align: left;
	        	padding: 5px;
                background-color:white;
            }
            table {
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
            th:nth-child(12) {
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
            .expandable-content {
                display: none;
            }
        </style>
        <script>
            document.addEventListener("DOMContentLoaded", function(event) {
                var expandableCells = document.querySelectorAll('.expandable-cell');
                for (var i = 0; i < expandableCells.length; i++) {
                    var expandableContent = expandableCells[i].querySelector('.expandable-content');
                    var expandButton = expandableCells[i].querySelector('.expandable-button');
                    var collapsibleButton = expandableCells[i].querySelector('.collapsible-button');
                    
                    expandButton.addEventListener('click', function() {
                        this.parentElement.querySelector('.expandable-content').style.display = 'block';
                        this.style.display = 'none';
                        this.parentElement.querySelector('.collapsible-button').style.display = 'inline-block';
                    });

                    collapsibleButton.addEventListener('click', function() {
                        this.parentElement.querySelector('.expandable-content').style.display = 'none';
                        this.parentElement.querySelector('.expandable-button').style.display = 'inline-block';
                        this.style.display = 'none';
                    });
                }
            });
        </script>
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
            
                # Captura y preparación de cada una de las variables
                if(isset($_FILES["Acta"]) && $_FILES["Acta"]["error"] == 0) {
                    $allowed = array("doc" => "image/doc", "docx" => "image/docx", "pdf" => "image/pdf");
                    $Link = $_POST['Link'];    
                    $Acta = $_FILES["Acta"]["name"];
                    $filetype = $_FILES["Acta"]["type"];
                    $filesize = $_FILES["Acta"]["size"];
                    $filepath = $_FILES["Acta"]["tmp_name"];
                
                    # Verificar tipo de archivo permitido
                    $ext = pathinfo($Acta, PATHINFO_EXTENSION);
                    if(!array_key_exists($ext, $allowed)) {
                        echo "<div style='background-color: white; color: black; padding: 10px 0px 10px 0px; width:400px; margin-left:550px; text-align:center;border-radius:20px; margin-top:50px; border:2px solid black;'>";
                        echo "Error: Selecciona un formato válido de archivo, por favor.";
                        echo "</div>"; 
                        echo "<a href='modificador.php'><div style='background-color: white; color: black; padding: 10px 0px 10px 0px; width:200px; margin-left:650px; text-align:center;border-radius:20px; margin-top:50px; border:2px solid black;'>Volver a modificador de proyectos.</div></a>";
                        die();
                    }
                    
                    # Verificar tamaño del archivo
                    $maxsize = 5 * 1024 * 1024;
                    if($filesize > $maxsize) {
                        echo "<div style='background-color: white; color: black; padding: 10px 0px 10px 0px; width:400px; margin-left:550px; text-align:center;border-radius:20px; margin-top:50px; border:2px solid black;'>";
                        echo "Error: El tamaño del archivo supera el límite.";
                        echo "</div>"; 
                        echo "<a href='modificador.php'><div style='background-color: white; color: black; padding: 10px 0px 10px 0px; width:200px; margin-left:650px; text-align:center;border-radius:20px; margin-top:50px; border:2px solid black;'>Volver a modificador de proyectos.</div></a>";
                        die();
                    }
                
                    $Link_Acta = "https://dobetter1.sharepoint.com/sites/DoBetterSpA/Documentos%20Compartidos/Proyectos/" . $Acta;
                    $Link_Acta = str_replace(" ", "%20", $Link_Acta);
    
                    # Obtener acta del proyecto
                    $query0 = "SELECT ID
                    FROM Proyecto
                    WHERE Acta=:Link_Acta";
                    $stmt0 = $pdo->prepare($query0);
                    $stmt0->bindValue(':Link_Acta', $Link_Acta, PDO::PARAM_STR);
                    $stmt0->execute();
                    $result0 = $stmt0->fetchAll();

                    # Si el archivo del acta ya está en la bd, no se agrega.
                    if(count($result0) > 0) {
                        echo "<div style='background-color: white; color: black; padding: 10px 0px 10px 0px; width:400px; margin-left:550px; text-align:center;border-radius:20px; margin-top:50px; border:2px solid black;'>";
                        echo "No es posible añadir nueva acta; " . $Acta . " ya existe.";
                        echo "</div>";
                        echo "<a href='modificador.php'><div style='background-color: white; color: black; padding: 10px 0px 10px 0px; width:200px; margin-left:650px; text-align:center;border-radius:20px; margin-top:50px; border:2px solid black;'>Volver a modificador de proyectos.</div></a>";
                    } else {
                        # Ver si existe algún proyecto con el link introducido
                        $query3 = "SELECT ID
                                    FROM Proyecto
                                    WHERE Link=:Link";
                        $stmt3 = $pdo->prepare($query3);
                        $stmt3->bindValue(':Link', $Link, PDO::PARAM_STR);
                        $stmt3->execute();
                        $result2 = $stmt3->fetchAll();
                    
                        if (count($result2) < 1) {
                            echo "<div style='background-color: white; color: black; padding: 10px 0px 10px 0px; width:400px; margin-left:550px; text-align:center;border-radius:20px; margin-top:50px; border:2px solid black;'>";
                            echo "No existe ningún proyecto con el link introducido.";
                            echo "</div>";
                            echo "<a href='modificador.php'><div style='background-color: white; color: black; padding: 10px 0px 10px 0px; width:200px; margin-left:650px; text-align:center;border-radius:20px; margin-top:50px; border:2px solid black;'>Volver a modificador de proyectos.</div></a>";
                        } else {
                            # Consulta para extraer acta de un cierto proyecto
                            $query="SELECT Acta FROM Proyecto WHERE Link=:Link";
                            $stmt = $pdo->prepare($query);
                            $stmt->bindValue(':Link', $Link, PDO::PARAM_STR);
                            $stmt->execute();
                            $result = $stmt->fetchAll();
                            
                            # Se obtiene acta del proyecto
                            $path = $result[0]['Acta'];

                            # Se elimina archivo de sharepoint
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
                                echo 'Error al eliminar el archivo antiguo: ' . curl_error($ch);
                                echo "</div>";
                                echo "<a href='modificador.php'><div style='background-color: white; color: black; padding: 10px 0px 10px 0px; width:200px; margin-left:650px; text-align:center;border-radius:20px; margin-top:50px; border:2px solid black;'>Volver a sección de eliminar proyectos.</div></a>";
                                die();
                            } 
                        
                            # Cerrar la conexión cURL
                            curl_close($ch);
                        
                            # Ids necesarios para solicitud y reemplazo de caracteres en link de acta
                            $Acta = str_replace(" ", "%20", $Acta);
                            
                            # URL del endpoint de carga de archivos de la API de Microsoft Graph
                            $subida_url = 'https://graph.microsoft.com/v1.0/sites/' . $site_id . '/drive/items/' . $folder_id . ':/'. $Acta .':/content';
                            
                            $file = file_get_contents($filepath);
                            
                            $ch1 = curl_init();
                            curl_setopt($ch1, CURLOPT_URL, $subida_url);
                            curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch1, CURLOPT_CUSTOMREQUEST, "PUT");
                            curl_setopt($ch1, CURLOPT_POSTFIELDS, $file);
                            curl_setopt($ch1, CURLOPT_HTTPHEADER, array(
                                "Authorization: Bearer $access_token",
                                "Content-Type: $filetype" 
                            ));
                        
                            $response1 = curl_exec($ch1);
                            curl_close($ch1);
                            # Verificar que la respuesta de la API de Microsoft Graph sea válida;
                            # si no lo es, error de carga
                            if (!$response1) {
                                echo "<div style='background-color: white; color: black; padding: 10px 0px 10px 0px; width:400px; margin-left:550px; text-align:center;border-radius:20px; margin-top:50px; border:2px solid black;'>";
                                echo 'Error al cargar nueva acta en SharePoint';
                                echo "</div>"; 
                                echo "<a href='modificador.php'><div style='background-color: white; color: black; padding: 10px 0px 10px 0px; width:200px; margin-left:650px; text-align:center;border-radius:20px; margin-top:50px; border:2px solid black;'>Volver a sección de añadir proyectos.</div></a>";
                                die();
                            }

                            # Consulta para actualizar archivo del proyecto
                            $query1 = "UPDATE Proyecto SET Acta=:Acta WHERE Link=:Link";
                            $stmt1 = $pdo->prepare($query1);
                            $stmt1->bindValue(':Link', $Link, PDO::PARAM_STR);
                            $stmt1->bindValue(':Acta', $Link_Acta, PDO::PARAM_STR);
                            $stmt1->execute();


                            # Consulta para mostrar cómo quedaron los nuevos datos en el proyecto
                            $query2="SELECT Proyecto.ID, Cliente.Nombre as Nombre_Cliente, Cliente.Link as Link_Cliente, Proyecto.Nombre_Proyecto, Proyecto.Categoría, Proyecto.Fecha,
                                            Patrocinador.Nombre as Nombre_Patrocinador, Patrocinador.Link as Link_Patrocinador, Proyecto.Gerente_de_Proyecto,
                                            Proyecto.Objetivo_del_Proyecto, Proyecto.Entregables, Proyecto.Acta,
                                            Proyecto.Link, Proyecto.Estado
                                        FROM Proyecto
                                            JOIN Cliente ON Proyecto.ID_Cliente=Cliente.ID
                                            JOIN Patrocinador ON Proyecto.ID_Patrocinador=Patrocinador.ID
                                        WHERE Proyecto.Link=:Link";
                            $stmt2 = $pdo->prepare($query2);
                            $stmt2->bindValue(':Link', $Link, PDO::PARAM_STR);
                            $stmt2->execute();
                        
                            # Luego se obtienen los resultados.
                            $result1 = $stmt2->fetchall();
                        
                            # Esta sección corresponde al header de la tabla
                            # Añadir tantas columnas como sea necesario
                            echo "<table>";
                            echo "<tr>
                                  <th> Proyecto Nº </th>
                                  <th> Cliente </th>
                                  <th> Nombre del Proyecto </th>
                                  <th> Categoría del proyecto </th>
                                  <th> Fecha del proyecto (Año-Mes-Día) </th>
                                  <th> Patrocinador Principal </th>
                                  <th> Gerente de Proyecto </th>
                                  <th> Objetivo de Proyecto </th>
                                  <th> Entregables </th>
                                  <th> Acta </th>
                                  <th> Link </th>
                                  <th> Estado </th>        
                                </tr>";
                        
                            # Resultados de la consulta
                            foreach ($result1 as $row) {
                                echo "<tr>";
                                echo "<td>" . nl2br($row['ID']) . "</td>";
                            
                                if (isset($row['Link_Cliente'])) {
                                    echo "<td><a href='" . $row['Link_Cliente'] . "'target='_blank'>" . nl2br($row['Nombre_Cliente']) . "</a></td>";
                                } else {
                                    echo "<td>" . nl2br($row['Nombre_Cliente']) . "</td>";
                                }
                            
                                echo "<td>" . nl2br($row['Nombre_Proyecto']) . "</td>";
                                echo "<td>" . nl2br($row['Categoría']) . "</td>";
                                echo "<td>" . nl2br($row['Fecha']) . "</td>";
                            
                                if (isset($row['Link_Patrocinador'])) {
                                    echo "<td><a href='" . $row['Link_Patrocinador'] . "'target='_blank'>" . nl2br($row['Nombre_Patrocinador']) . "</a></td>";
                                } else {
                                    echo "<td>" . nl2br($row['Nombre_Patrocinador']) . "</td>";
                                }
                            
                                echo "<td>" . nl2br($row['Gerente_de_Proyecto']) . "</td>";

                                echo "<td>";
                                if (strlen($row['Objetivo_del_Proyecto']) > 100) {
                                    echo "<div class='expandable-cell'>";
                                    echo "<span class='expandable-content' style='display:none'>" . nl2br($row['Objetivo_del_Proyecto']) . "</span>";
                                    echo "<button class='expandable-button'>Mostrar</button>";
                                    echo "<button class='collapsible-button' style='display:none'>Ocultar</button>";
                                    echo "</div>";
                                } else {
                                    echo nl2br($row['Objetivo_del_Proyecto']);
                                }
                                echo "</td>";
                            
                                echo "<td>";
                                if (strlen($row['Entregables']) > 100) {
                                    echo "<div class='expandable-cell'>";
                                    echo "<span class='expandable-content' style='display:none'>" . nl2br($row['Entregables']) . "</span>";
                                    echo "<button class='expandable-button'>Mostrar</button>";
                                    echo "<button class='collapsible-button' style='display:none'>Ocultar</button>";
                                    echo "</div>";
                                } else {
                                    echo nl2br($row['Entregables']);
                                }
                                echo "</td>";
                            
                            
                                if (!empty($row['Acta'])) {
                                    echo "<td><a href='" . $row['Acta'] . "'target='_blank'>Descargar Acta</a></td>";
                                } else {
                                    echo "<td></td>";
                                }
                            
                                if (!empty($row['Link'])) {
                                    echo "<td><a href='" . $row['Link'] . "'target='_blank'>One Drive</a></td>";
                                } else {
                                    echo "<td></td>";
                                }
                            
                                echo "<td>" . nl2br($row['Estado']) . "</td>";
                                echo "</tr>";
                            }
                            echo "</table>";
                            echo "<a href='modificador.php'><div style='background-color: white; color: black; padding: 10px 0px 10px 0px; width:200px; margin-left:650px; text-align:center;border-radius:20px; margin-top:50px; border:2px solid black;'>Volver a modificador de proyectos.</div></a>";
                        }
                    }
                } else {
                    echo "<div style='background-color: white; color: black; padding: 10px 0px 10px 0px; width:400px; margin-left:550px; text-align:center;border-radius:20px; margin-top:50px; border:2px solid black;'>";
                    echo "Error: Hubo un problema al subir el archivo. Por favor, inténtalo de nuevo.";
                    echo "</div>";
                    echo "<a href='modificador.php'><div style='background-color: white; color: black; padding: 10px 0px 10px 0px; width:200px; margin-left:650px; text-align:center;border-radius:20px; margin-top:50px; border:2px solid black;'>Volver a modificador de proyectos.</div></a>";
                }
            } catch(Exception $e) {
                echo "<div style='background-color: white; color: black; padding: 10px 0px 10px 0px; width:400px; margin-left:550px; text-align:center;border-radius:20px; margin-top:50px; border:2px solid black;'>";
                echo "Ocurrió un error, inténtelo nuevamente por favor.";
                echo "</div>";
                echo "<a href='modificador.php'><div style='background-color: white; color: black; padding: 10px 0px 10px 0px; width:200px; margin-left:650px; text-align:center;border-radius:20px; margin-top:50px; border:2px solid black;'>Volver a modificador de proyectos.</div></a>";
                die();
            }
        ?>
    </body>
</html>