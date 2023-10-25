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
                    $Cliente = $_POST['Cliente'];
                    $Link_Cliente = $_POST['Link_Cliente'];
                    $Nombre_Proyecto = $_POST['Nombre_Proyecto'];
                    $Categoria = $_POST['Categoria'];
                    $Fecha = $_POST['Fecha'];
                    $Patrocinador = $_POST['Patrocinador_Principal'];
                    $Link_Patrocinador = $_POST['Link_Patrocinador'];
                    $Gerente_de_Proyecto = $_POST['Gerente_de_Proyecto'];
                    $Objetivo_del_Proyecto = $_POST['Objetivo_del_Proyecto'];
                    $Entregables = $_POST['Entregables'];
                    $Link_Proyecto = $_POST['Link_Proyecto'];
                    $Estado = $_POST['Estado'];
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
                        echo "<a href='agregar.php'><div style='background-color: white; color: black; padding: 10px 0px 10px 0px; width:200px; margin-left:650px; text-align:center;border-radius:20px; margin-top:50px; border:2px solid black;'>Volver a sección de añadir proyectos.</div></a>";
                        die();
                    }
                    # Verificar tamaño del archivo
                    $maxsize = 5 * 1024 * 1024;
                    if($filesize > $maxsize) {
                        echo "<div style='background-color: white; color: black; padding: 10px 0px 10px 0px; width:400px; margin-left:550px; text-align:center;border-radius:20px; margin-top:50px; border:2px solid black;'>";
                        echo "Error: El tamaño del archivo supera el límite (5MB).";
                        echo "</div>"; 
                        echo "<a href='agregar.php'><div style='background-color: white; color: black; padding: 10px 0px 10px 0px; width:200px; margin-left:650px; text-align:center;border-radius:20px; margin-top:50px; border:2px solid black;'>Volver a sección de añadir proyectos.</div></a>";
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
                    $result4 = $stmt0->fetchAll();

                    # Si el archivo del acta ya está siendo ocupado por algún proyecto, no se puede añadir proyecto.
                    if (count($result4) > 0) {
                        echo "<div style='background-color: white; color: black; padding: 10px 0px 10px 0px; width:400px; margin-left:550px; text-align:center;border-radius:20px; margin-top:50px; border:2px solid black;'>";
                        echo "No es posible agregar el archivo $Acta. Ya existe en la carpeta.";
                        echo "</div>";
                    } else {
                        # Ver si algún proyecto ya tiene el link introducido
                        $query = "SELECT Proyecto.ID, Proyecto.Nombre_Proyecto, Cliente.Nombre
                                    FROM Proyecto
                                        JOIN Cliente ON Proyecto.ID_Cliente=Cliente.ID
                                    WHERE Proyecto.Link=:Link_Proyecto";
                        $stmt = $pdo->prepare($query);
                        $stmt->bindValue(':Link_Proyecto', $Link_Proyecto, PDO::PARAM_STR);
                        $stmt->execute();
                        $result = $stmt->fetchAll();
                    
                        # Si hay algún proyecto con el link, no se puede continuar
                        if (count($result) > 0) {
                            $id_proyecto = $result[0]['ID'];
                            $nombre_proyect = $result[0]['Nombre_Proyecto'];
                            $nombre_cliente = $result[0]['Nombre'];
                            echo "<div style='background-color: white; color: black; padding: 10px 0px 10px 0px; width:400px; margin-left:550px; text-align:center;border-radius:20px; margin-top:50px; border:2px solid black;'>";
                            echo "El link de proyecto introducido ya está siendo ocupado por el proyecto Nº" . $id_proyecto . "
                            el cual tiene: <br> Nombre: " . $nombre_proyect . "<br> Cliente: " . $nombre_cliente;
                            echo "</div>"; 
                        } else {
                            # Ya que no está siendo usado, proceder
                        
                            # Ver si nombre de cliente introducido ya existe en la base de datos
                            $query1 = "SELECT ID FROM Cliente WHERE Nombre=:Cliente";
                            $stmt1 = $pdo->prepare($query1);
                            $stmt1->bindValue(':Cliente', $Cliente, PDO::PARAM_STR);
                            $stmt1->execute();
                            $result1 = $stmt1->fetchAll();
                        
                            # Si ya existe el cliente, usarlo reemplazando link
                            if (count($result1) > 0) {
                                # El cliente existe, reemplazar link
                                $id_cliente = $result1[0]['ID'];
                                $query2 = "UPDATE Cliente
                                            SET Link=:Link_Cliente
                                            WHERE ID=:id_cliente";
                                $stmt2 = $pdo->prepare($query2);
                                $stmt2->bindValue(':Link_Cliente', $Link_Cliente, PDO::PARAM_STR);
                                $stmt2->bindParam(':id_cliente', $id_cliente);
                                $stmt2->execute();
                            } else {
                                # El cliente no existe, insertar nuevo cliente
                                $query3 = "INSERT INTO Cliente (Link, Nombre) 
                                        VALUES (:Link_Cliente, :Cliente)";
                                $stmt3 = $pdo->prepare($query3);
                                $stmt3->bindValue(':Link_Cliente', $Link_Cliente, PDO::PARAM_STR);
                                $stmt3->bindValue(':Cliente', $Cliente, PDO::PARAM_STR);
                                $stmt3->execute();
                                $id_cliente = $pdo->lastInsertId();
                            }
                        
                            # Ver si nombre de patrocinador introducido ya existe en la base de datos
                            $query4 = "SELECT ID FROM Patrocinador WHERE Nombre=:Patrocinador";
                            $stmt4 = $pdo->prepare($query4);
                            $stmt4->bindValue(':Patrocinador', $Patrocinador, PDO::PARAM_STR);
                            $stmt4->execute();
                            $result2 = $stmt4->fetchAll();
                        
                            # Si ya existe el patrocinador, usarlo reemplazando link
                            if (count($result2) > 0) {
                                # El patrocinador existe, reemplazar link
                                $id_patrocinador = $result2[0]['ID'];
                                $query5 = "UPDATE Patrocinador
                                            SET Link=:Link_Patrocinador
                                            WHERE ID=:id_patrocinador";
                                $stmt5 = $pdo->prepare($query5);
                                $stmt5->bindValue(':Link_Patrocinador', $Link_Patrocinador, PDO::PARAM_STR);
                                $stmt5->bindParam(':id_patrocinador', $id_patrocinador);
                                $stmt5->execute();
                            } else {
                                # El patrocinador no existe, insertar nuevo patrocinador
                                $query6 = "INSERT INTO Patrocinador (Link, Nombre) 
                                        VALUES (:Link_Patrocinador, :Patrocinador)";
                                $stmt6 = $pdo->prepare($query6);
                                $stmt6->bindValue(':Link_Patrocinador', $Link_Patrocinador, PDO::PARAM_STR);
                                $stmt6->bindValue(':Patrocinador', $Patrocinador, PDO::PARAM_STR);
                                $stmt6->execute();
                                $id_patrocinador = $pdo->lastInsertId();
                            }

                            # Ids necesarios para solicitud y reemplazo de caracteres en link de acta
                            $Acta = str_replace(" ", "%20", $Acta);
                            $site_id = '0eef7ec9-9469-4230-a487-62fa78f630ad';
                            $folder_id = '01JDGSHHREWEUE6IKJTRBJTWGVY44VLR4Q';

                            # URL del endpoint de carga de archivos de la API de Microsoft Graph
                            $url = 'https://graph.microsoft.com/v1.0/sites/' . $site_id . '/drive/items/' . $folder_id . ':/'. $Acta .':/content';

                            $file = file_get_contents($filepath);

                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $url);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $file);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                "Authorization: Bearer $access_token",
                                "Content-Type: $filetype" 
                            ));

                            $response = curl_exec($ch);
                            curl_close($ch);
                            # Verificar que la respuesta de la API de Microsoft Graph sea válida;
                            # si no lo es, error de carga
                            if (!$response) {
                                echo "<div style='background-color: white; color: black; padding: 10px 0px 10px 0px; width:400px; margin-left:550px; text-align:center;border-radius:20px; margin-top:50px; border:2px solid black;'>";
                                echo 'Error al cargar el archivo en SharePoint';
                                echo "</div>"; 
                                echo "<a href='agregar.php'><div style='background-color: white; color: black; padding: 10px 0px 10px 0px; width:200px; margin-left:650px; text-align:center;border-radius:20px; margin-top:50px; border:2px solid black;'>Volver a sección de añadir proyectos.</div></a>";
                                die();
                            }
                            # Como proyecto no existe, realizar inserción
                            # Consulta para insertar valores introducidos a la tabla Proyecto
                            $query7 = "INSERT INTO Proyecto (Nombre_Proyecto, ID_Cliente, Categoría, Fecha, ID_Patrocinador, Gerente_de_Proyecto, Objetivo_del_Proyecto, Entregables, Acta, Link, Estado) VALUES (:Nombre_Proyecto, :id_cliente, :Categoria, :Fecha, :id_patrocinador, :Gerente_de_Proyecto, :Objetivo_del_Proyecto, :Entregables, :Acta, :Link_Proyecto, :Estado)";
                            $stmt7 = $pdo->prepare($query7);
                            $stmt7->bindValue(':Nombre_Proyecto', $Nombre_Proyecto, PDO::PARAM_STR);
                            $stmt7->bindParam(':id_cliente', $id_cliente);
                            $stmt7->bindValue(':Categoria', $Categoria, PDO::PARAM_STR);
                            $stmt7->bindValue(':Fecha', $Fecha);
                            $stmt7->bindParam(':id_patrocinador', $id_patrocinador);
                            $stmt7->bindValue(':Gerente_de_Proyecto', $Gerente_de_Proyecto, PDO::PARAM_STR);
                            $stmt7->bindValue(':Objetivo_del_Proyecto', $Objetivo_del_Proyecto, PDO::PARAM_STR);
                            $stmt7->bindValue(':Entregables', $Entregables, PDO::PARAM_STR);
                            $stmt7->bindValue(':Acta', $Link_Acta, PDO::PARAM_STR);
                            $stmt7->bindValue(':Link_Proyecto', $Link_Proyecto, PDO::PARAM_STR);
                            $stmt7->bindValue(':Estado', $Estado, PDO::PARAM_STR);
                            $stmt7->execute();
                        
                        
                            # Consulta para mostrar proyecto insertado
                            $query8="SELECT Proyecto.ID, Cliente.Nombre as Nombre_Cliente, Cliente.Link as Link_Cliente, Proyecto.Nombre_Proyecto, Proyecto.Categoría, Proyecto.Fecha,
                                            Patrocinador.Nombre as Nombre_Patrocinador, Patrocinador.Link as Link_Patrocinador, Proyecto.Gerente_de_Proyecto,
                                            Proyecto.Objetivo_del_Proyecto, Proyecto.Entregables, Proyecto.Acta,
                                            Proyecto.Link, Proyecto.Estado
                                        FROM Proyecto
                                            JOIN Cliente ON Proyecto.ID_Cliente=Cliente.ID
                                            JOIN Patrocinador ON Proyecto.ID_Patrocinador=Patrocinador.ID
                                        WHERE Proyecto.Link=:Link_Proyecto";
                            $stmt8 = $pdo->prepare($query8);
                            $stmt8->bindValue(':Link_Proyecto', $Link_Proyecto, PDO::PARAM_STR);
                            $stmt8->execute();

                            # Luego se obtienen los resultados.
                            $result3 = $stmt8->fetchAll();
                        
                            # Esta sección corresponde al header de la tabla
                            # Añadir tantas columnas como sea necesario
                            echo "<table>";
                            echo "<tr>
                                    <th> Proyecto Nº </th>
                                    <th> Cliente </th>
                                    <th> Nombre del Proyecto </th>
                                    <th> Categoría </th>
                                    <th> Fecha del proyecto (Año-Mes-Día) </th>
                                    <th> Patrocinador Principal </th>
                                    <th> Gerente de Proyecto </th>
                                    <th> Objetivo de Proyecto </th>
                                    <th> Entregables </th>
                                    <th> Acta </th>
                                    <th> Link OneDrive </th>
                                    <th> Estado </th>
                                  </tr>";
                        
                            # Resultados de la consulta
                            foreach ($result3 as $row) {
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
                        }
                    } 

                } else {
                    echo "<div style='background-color: white; color: black; padding: 10px 0px 10px 0px; width:400px; margin-left:550px; text-align:center;border-radius:20px; margin-top:50px; border:2px solid black;'>";
                    echo "Error: Hubo un problema al subir el archivo. Por favor, inténtelo de nuevo.";
                    echo "</div>";
                }
                echo "</table>";
                echo "<a href='agregar.php'><div style='background-color: white; color: black; padding: 10px 0px 10px 0px; width:200px; margin-left:650px; text-align:center;border-radius:20px; margin-top:50px; border:2px solid black;'>Volver a sección de añadir proyectos.</div></a>";
                
            } catch(Exception $e) {
                echo "<div style='background-color: white; color: black; padding: 10px 0px 10px 0px; width:400px; margin-left:550px; text-align:center;border-radius:20px; margin-top:50px; border:2px solid black;'>";
                echo "Ocurrió un error, inténtelo nuevamente por favor y consulte al administrador de la base de datos.";
                echo "</div>";
                echo "<a href='agregar.php'><div style='background-color: white; color: black; padding: 10px 0px 10px 0px; width:200px; margin-left:650px; text-align:center;border-radius:20px; margin-top:50px; border:2px solid black;'>Volver a sección de añadir proyectos.</div></a>";
                die(var_dump($e));
            }
        ?>
    </body>
</html>