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

                # Se reciben desde el input 'Patrocinador_Principal' y 'Link'
                $Patrocinador=$_GET['Patrocinador_Principal'];
                $Link=$_GET['Link'];

                # Se ve si existe el link introducido
                $query = "SELECT ID FROM Proyecto WHERE Link=:Link";
                $stmt = $pdo->prepare($query);
                $stmt->bindValue(':Link', $Link, PDO::PARAM_STR);
                $stmt->execute();
                $result = $stmt->fetchAll();

                if (count($result) < 1) {
                    echo "<div style='background-color: white; color: black; padding: 10px 0px 10px 0px; width:400px; margin-left:550px; text-align:center;border-radius:20px; margin-top:50px; border:2px solid black;'>";
                    echo "No existe ningún proyecto con el link de proyecto introducido. No es posible realizar modificaciones.";
                    echo "</div>";
                    echo "<a href='modificador.php'><div style='background-color: white; color: black; padding: 10px 0px 10px 0px; width:200px; margin-left:650px; text-align:center;border-radius:20px; margin-top:50px; border:2px solid black;'>Volver a modificador de proyectos.</div></a>";
                } else {
                    # Se ve si el nombre que se quiere poner ya está ocupado
                    $query1 = "SELECT ID FROM Patrocinador WHERE Nombre=:Patrocinador";
                    $stmt1 = $pdo->prepare($query1);
                    $stmt1->bindValue(':Patrocinador', $Patrocinador, PDO::PARAM_STR);
                    $stmt1->execute();
                    $result1 = $stmt1->fetchAll();

                    # Si no está, se añade con el link del patrocinador cuyo nombre se quiere cambiar,
                    # Por otro lado, si está, se usa ese patrocinador

                    # Nombre de patrocinador no está ocupado
                    if (count($result1) <= 0) {
                        # Se obtiene ID de patrocinador para proyecto de antes
                        $query2="SELECT ID_Patrocinador FROM Proyecto WHERE Link=:Link";
                        $stmt2 = $pdo->prepare($query2);
                        $stmt2->bindValue(':Link', $Link, PDO::PARAM_STR);
                        $stmt2->execute();
                        $result3 = $stmt2->fetchAll();
                        $id_patrocinador = $result3[0]['ID_Patrocinador'];

                        # Ver cuántos proyectos tienen este id
                        $query3="SELECT ID FROM Proyecto WHERE ID_Patrocinador=:id_patrocinador";
                        $stmt3 = $pdo->prepare($query3);
                        $stmt3->bindParam(':id_patrocinador', $id_patrocinador);
                        $stmt3->execute();
                        $result2 = $stmt3->fetchAll();
                    
                        # Si un proyecto o menos, entonces modificar el nombre, pues fue error de tipeo
                        if (count($result2) <= 1) {
                            # Eliminar de bd patrocinador
                            $query4="UPDATE Patrocinador 
                                    SET Nombre=:Patrocinador WHERE ID=:id_patrocinador";
                            $stmt4 = $pdo->prepare($query4);
                            $stmt4->bindValue(':Patrocinador', $Patrocinador, PDO::PARAM_STR);
                            $stmt4->bindParam(':id_patrocinador', $id_patrocinador);
                            $stmt4->execute();
                        
                        } else {
                            # Si es más de un proyecto, el antiguo nombre lo están ocupando
                            # más proyectos, así que se creará nuevo patrocinador para asignarle al proyecto

                            # Se obtiene link del nombre del patrocinador que se quiere modificar
                            $query5 = "SELECT Link FROM Patrocinador WHERE ID=:id_patrocinador";
                            $stmt5 = $pdo->prepare($query5);
                            $stmt5->bindParam(':id_patrocinador', $id_patrocinador);
                            $stmt5->execute();
                            $result3 = $stmt5->fetchAll();
                            $link_patrocinador = $result3[0]['Link'];

                            # Se insertan valores de link y nombre nuevo
                            $query6="INSERT INTO Patrocinador VALUES (:link_patrocinador, :Patrocinador)";
                            $stmt6 = $pdo->prepare($query6);
                            $stmt6->bindValue(':link_patrocinador', $link_patrocinador, PDO::PARAM_STR);
                            $stmt6->bindValue(':Patrocinador', $Patrocinador, PDO::PARAM_STR);
                            $stmt6->execute();
                            $id = $pdo->lastInsertId();
                        
                            # Se actualiza id del patrocinador del proyecto en cuestión
                            $query7="UPDATE Proyecto 
                                    SET ID_Patrocinador=:id WHERE Link=:Link";
                            $stmt7 = $pdo->prepare($query7);
                            $stmt7->bindParam(':id', $id);
                            $stmt7->bindValue(':Link', $Link, PDO::PARAM_STR);
                            $stmt7->execute();
                        }
                    } else {
                        # Nombre de patrocinador está ocupado
                    
                        # Se obtiene id de patrocinador
                        $id_patrocinado = $result1[0]['ID'];
                    
                        # Se obtiene ID de patrocinador para proyecto de antes
                        $query8="SELECT ID_Patrocinador FROM Proyecto WHERE Link=:Link";
                        $stmt8 = $pdo->prepare($query8);
                        $stmt8->bindValue(':Link', $Link, PDO::PARAM_STR);
                        $stmt8->execute();
                        $result4 = $stmt8->fetchAll();
                        $id_patrocinador = $result4[0]['ID_Patrocinador'];

                        # Ver cuántos proyectos tienen este id
                        $query9="SELECT ID FROM Proyecto WHERE ID_Patrocinador=:id_patrocinador";
                        $stmt9 = $pdo->prepare($query9);
                        $stmt9->bindParam(':id_patrocinador', $id_patrocinador);
                        $stmt9->execute();
                        $result5 = $stmt9->fetchAll();
                    
                        # Si hay un proyecto, entonces se borrará el patrocinador correspondiente al antiguo nombre
                        if (count($result5) <= 1) {
                            # Se obtiene nombre de cliente para proyecto de antes
                            $query10="SELECT Nombre FROM Patrocinador WHERE ID=:id_patrocinador";
                            $stmt10 = $pdo->prepare($query10);
                            $stmt10->bindParam(':id_patrocinador', $id_patrocinador);
                            $stmt10->execute();
                            $result6 = $stmt10->fetchAll();
                            $nombre_patrocinador = $result6[0]['Nombre'];
                        
                            # Se realiza cambio de ID_Patrocinador en Proyecto correspondiente link introducido
                            $query11="UPDATE Proyecto 
                                        SET ID_Patrocinador=:id_patrocinado 
                                        WHERE Link=:Link";
                            $stmt11 = $pdo->prepare($query11);
                            $stmt11->bindParam(':id_patrocinado', $id_patrocinado);
                            $stmt11->bindValue(':Link', $Link, PDO::PARAM_STR);
                            $stmt11->execute();

                            if($nombre_patrocinador != $Patrocinador) {
                                # Se borra aquel proyecto que al actualizar quedó sin ser ocupado
                                $query12="DELETE FROM Patrocinador 
                                            WHERE ID=:id_patrocinador";
                                $stmt12 = $pdo->prepare($query12);
                                $stmt12->bindParam(':id_patrocinador', $id_patrocinador);
                                $stmt12->execute();
                            }
                        } else {
                            $query13="UPDATE Proyecto 
                                        SET ID_Patrocinador=:id_patrocinado
                                        WHERE Link=:Link";
                            $stmt13 = $pdo->prepare($query13);
                            $stmt13->bindParam(':id_patrocinado', $id_patrocinado);
                            $stmt13->bindValue(':Link', $Link, PDO::PARAM_STR);
                            $stmt13->execute();
                        }
                    }
                
                    # Consulta para mostrar cómo quedaron los nuevos datos en el proyecto
                    $query14="SELECT Proyecto.ID, Cliente.Nombre as Nombre_Cliente, Cliente.Link as Link_Cliente, Proyecto.Nombre_Proyecto, Proyecto.Categoría, Proyecto.Fecha,
                                     Patrocinador.Nombre as Nombre_Patrocinador, Patrocinador.Link as Link_Patrocinador, Proyecto.Gerente_de_Proyecto,
                                     Proyecto.Objetivo_del_Proyecto, Proyecto.Entregables, Proyecto.Acta,
                                     Proyecto.Link, Proyecto.Estado
                                FROM Proyecto
                                    JOIN Cliente ON Proyecto.ID_Cliente=Cliente.ID
                                    JOIN Patrocinador ON Proyecto.ID_Patrocinador=Patrocinador.ID
                                WHERE Proyecto.Link=:Link";
                    $stmt14 = $pdo->prepare($query14);
                    $stmt14->bindValue(':Link', $Link, PDO::PARAM_STR);
                    $stmt14->execute();

                    # Luego se obtienen los resultados.
                    $result7 = $stmt14->fetchall();
                
                    # Esta sección corresponde al header de la tabla, agregar o quitar cuantas columnas
                    # se quieran mostrar de la tabla de la base de datos.
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
                    foreach ($result7 as $row) {
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