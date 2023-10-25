<!DOCTYPE html>
<html lang="en">
    <head>
        <style>
            /*Elementos de menú distribuidos de manera inherit en dos columnas*/
            .menu {
                display:inherit;
                columns: 2;
            }

            /*Color de letra de Modificar en consulta*/
            div.offset-lg-1 h5 {
                color:white;
            }
            
            /*Labels de checkbox y para seleccionar archivos*/
            div.list-item div.item label {
                color:white;
            }

            /*Labels para seleccionar archivos*/
            div.list-item div.item form label.custom-file-label {
                color:black;
            }

            /*Permiten que sea mismo margen para todos los elementos del menú*/
            #last_right {
                margin-bottom: 0px;
            }
            #last_left {
                margin-bottom: 0px;
            }

            /*Fondo principal*/
            #modificador {
                background-image: url(assets/images/damas.jpg);
                background-size: cover;
                
            }

            /*Color del texto principal y distribución*/
            #colorText {
                color: rgb(255, 255, 255);
                text-align: center;
            }
            
            select{
               cursor: pointer;
               padding: 8px 12px;
               background-color: #fbff8f;
               border: 1px solid #010101;
               border-radius: 10px;
               width: 100%;
               height: 45px;
            }

            /* Sección de separación entre index y modificador*/
            .degradado {
                height: 400px;
                background-size: cover;
                background-image: url(assets/images/degradado1.jpg);
                padding: 0px;
                margin: 0px;
            }
        </style>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

        <title>Biblioteca de proyectos DoBetter</title>

        <!-- Bootstrap CSS -->
        <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">


        <!-- Archivos CSS adicionales -->
        <link rel="stylesheet" href="assets/css/fontawesome.css">
        <link rel="stylesheet" href="assets/css/templatemo-plot-listing.css">
        <link rel="stylesheet" href="assets/css/animated.css">
        <link rel="stylesheet" href="assets/css/owl.css">

        <!-- Logo de la página, ícono Do better -->
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
        ?>
        <!-- ***** Comienzo de Preloader ***** -->
        <div id="js-preloader" class="js-preloader">
            <div class="preloader-inner">
                <span class="dot"></span>
                <div class="dots">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
        <!-- ***** Final de Preloader ***** -->

        <!-- ***** Comienzo de Header ***** -->
        <header class="header-area header-sticky wow slideInDown" data-wow-duration="0.75s" data-wow-delay="0s">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <nav class="main-nav">
                            <!-- ***** Comienzo de Logo ***** -->
                            <a href="index.php" class="logo"></a>
                            <!-- ***** Final de Logo ***** -->

                            <!-- ***** Comienzo de Menu ***** -->
                            <ul class="nav">
                                <li><a href="index.php">Buscar</a></li>
                                <li><a href="modificador.php" class="active">Modificar</a></li>
                                <li><a href="eliminar.php">Eliminar</a></li>
                                <li><a href="agregar.php">Añadir</a></li> 
                                <li><div class="main-white-button"><a href="logout.php"><i class="fa fa-sign-out"></i> Cerrar sesión</a></div></li> 
                            </ul>
                            <a class='menu-trigger'><span>Menu</span></a>
                            <!-- ***** Final de Menu ***** -->
                        </nav>
                    </div>
                </div>
            </div>
        </header>
        <!-- ***** Final de Header ***** -->

        <div class="page-heading" id="modificador">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="top-text header-text" id="colorText">
                            <h6>Modifique aspectos de proyectos</h6>
                            <h2>Elija algún detalle y modifíquelo</h2>
                        </div>
                    </div>
                    <!-- ***** Comienzo de menú y formularios ***** -->
                    <div class="listing-page">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="naccs">
                                        <div class="grid">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="menu">
                                                        <!-- ***** Se crea el menú; display inherit y dos columnas para formato ***** -->
                                                        <div class="thumb active">
                                                            <span class="icon"><img src="assets/images/cliente.png" alt=""></span>
                                                            Cliente del proyecto
                                                        </div>
                                                        <div class="thumb">                 
                                                            <span class="icon"><img src="assets/images/patrocinador.png" alt=""></span>
                                                            Patrocinador principal
                                                        </div>
                                                        <div class="thumb">                 
                                                            <span class="icon"><img src="assets/images/objetivo.png" alt=""></span>
                                                            Objetivo
                                                        </div>
                                                        <div class="thumb">                 
                                                            <span class="icon"><img src="assets/images/fechas.png" alt=""></span>
                                                            Fecha de inicio
                                                        </div>
                                                        <div class="thumb">                 
                                                            <span class="icon"><img src="assets/images/archivo.png" alt=""></span>
                                                            Acta de constitución
                                                        </div>
                                                        <div class="thumb">                 
                                                            <span class="icon"><img src="assets/images/linkedin.png" alt=""></span>
                                                            LinkedIn Patrocinador
                                                        </div>
                                                        <div class="thumb">                 
                                                            <span class="icon"><img src="assets/images/categoria.png" alt=""></span>
                                                            Categoría del proyecto
                                                        </div>
                                                        <div class="thumb">                 
                                                            <span class="icon"><img src="assets/images/proyecto.png" alt=""></span>
                                                            Nombre del proyecto
                                                        </div>
                                                        <div class="thumb">                 
                                                            <span class="icon"><img src="assets/images/gerente.png" alt=""></span>
                                                            Gerente del proyecto
                                                        </div>
                                                        <div class="thumb">                 
                                                            <span class="icon"><img src="assets/images/entregables.png" alt=""></span>
                                                            Entregables
                                                        </div>
                                                        <div class="thumb">                 
                                                            <span class="icon"><img src="assets/images/estado.png" alt=""></span>
                                                            Estado del proyecto
                                                        </div>
                                                        <div class="thumb">                 
                                                            <span class="icon"><img src="assets/images/link.png" alt=""></span>
                                                            Link del proyecto
                                                        </div>
                                                        <div class="thumb" id="last_left">                 
                                                            <span class="icon"><img src="assets/images/LinkCliente.png" alt=""></span>
                                                            Link de cliente
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <ul class="nacc">
                                                        <!-- Lisado de items -->
                                                    
                                                        <!-- ***** Modificador de nombre de cliente ***** -->
                                                        <li class="active">
                                                            <div>
                                                                <div class="col-lg-12">
                                                                    <div class="list-item">
                                                                        <div class="item">
                                                                            <div class="row">
                                                                                <div class="col-lg-12">
                                                                                    <div class="offset-lg-1">
                                                                                        <br>
                                                                                        <h5> Modifique el nombre del cliente a partir del link del proyecto.</h5>
                                                                                        <br>
                                                                                    </div>
                                                                                    <div class="row">
                                                                                        <div class="col-lg-12">
                                                                                            <form id="contact" action="ModificarCliente.php" method="get" enctype="multipart/form-data">
                                                                                                <div class="row">
                                                                                                    <div class="col-lg-6">
                                                                                                        <fieldset>
                                                                                                            <input type="text" name="Cliente" id="Cliente" placeholder="Nuevo nombre de cliente" autocomplete="on" required>
                                                                                                        </fieldset>
                                                                                                    </div>
                                                                                                    <div class="col-lg-6">
                                                                                                        <fieldset>
                                                                                                            <input type="url" name="Link" id="Link" placeholder="Link del proyecto" required>
                                                                                                        </fieldset>
                                                                                                    </div>
                                                                                                    <div class="col-lg-6">
                                                                                                        <fieldset>
                                                                                                            <button type="submit" id="form-submit" class="main-button "><i class="fa fa-cogs"></i> Modificar proyecto</button>
                                                                                                        </fieldset>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </form>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    
                                                        <!-- ***** Modificador de nombre de patrocinador ***** -->
                                                        <li>
                                                            <div>
                                                                <div class="col-lg-12">
                                                                    <div class="list-item">
                                                                        <div class="item">
                                                                            <div class="row">
                                                                                <div class="col-lg-12">
                                                                                    <div class="offset-lg-1">
                                                                                        <br>
                                                                                        <h5> Modifique el nombre del patrocinador principal a partir del link del proyecto.</h5>
                                                                                        <br>
                                                                                    </div>
                                                                                    <div class="row">
                                                                                        <div class="col-lg-12">
                                                                                            <form id="contact" action="ModificarPatrocinador.php" method="get" enctype="multipart/form-data">
                                                                                                <div class="row">
                                                                                                    <div class="col-lg-6">
                                                                                                        <fieldset>
                                                                                                            <input type="text" name="Patrocinador_Principal" id="Patrocinador_Principal" placeholder="Nuevo nombre de patrocinador" autocomplete="on" required>
                                                                                                        </fieldset>
                                                                                                    </div>
                                                                                                    <div class="col-lg-6">
                                                                                                        <fieldset>
                                                                                                            <input type="url" name="Link" id="Link" placeholder="Link del proyecto" required>
                                                                                                        </fieldset>
                                                                                                    </div>
                                                                                                    <div class="col-lg-6">
                                                                                                        <fieldset>
                                                                                                            <button type="submit" id="form-submit" class="main-button "><i class="fa fa-cogs"></i> Modificar proyecto</button>
                                                                                                        </fieldset>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </form>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    
                                                        <!-- ***** Modificador de objetivo del proyecto ***** -->
                                                        <li>
                                                            <div>
                                                                <div class="col-lg-12">
                                                                    <div class="list-item">
                                                                        <div class="item">
                                                                            <div class="row">
                                                                                <div class="col-lg-12">
                                                                                    <div class="offset-lg-1">
                                                                                        <br>
                                                                                        <h5> Modifique el objetivo del proyecto a partir del link de este.</h5>
                                                                                        <br>
                                                                                    </div>
                                                                                    <div class="row">
                                                                                        <div class="col-lg-12">
                                                                                            <form id="contact" action="ModificarObjetivo.php" method="get" enctype="multipart/form-data">
                                                                                                <div class="row">
                                                                                                    <div class="col-lg-6">
                                                                                                        <fieldset>
                                                                                                            <textarea name="Objetivo_del_Proyecto" type="text" class="form-control" id="Objetivo_del_Proyecto" placeholder="Nuevo objetivo del proyecto" required=""></textarea>  
                                                                                                        </fieldset>
                                                                                                    </div>
                                                                                                    <div class="col-lg-6">
                                                                                                        <br>
                                                                                                        <fieldset>
                                                                                                            <input type="url" name="Link" id="Link" placeholder="Link del proyecto" required>
                                                                                                        </fieldset>
                                                                                                    </div>
                                                                                                    <div class="col-lg-6">
                                                                                                        <fieldset>
                                                                                                            <button type="submit" id="form-submit" class="main-button "><i class="fa fa-cogs"></i> Modificar proyecto</button>
                                                                                                        </fieldset>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </form>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    
                                                        <!-- ***** Modificador de fecha del proyecto ***** -->
                                                        <li>
                                                            <div>
                                                                <div class="col-lg-12">
                                                                    <div class="list-item">
                                                                        <div class="item">
                                                                            <div class="row">
                                                                                <div class="col-lg-12">
                                                                                    <div class="offset-lg-1">
                                                                                        <br>
                                                                                        <h5> Modifique la fecha del proyecto introduciendo la nueva fecha que le quiere asignar al proyecto cuyo link introducirá.</h5>
                                                                                        <br>
                                                                                    </div>
                                                                                    <div class="row">
                                                                                        <div class="col-lg-12">
                                                                                            <form id="contact" action="ModificarFecha.php" method="get" enctype="multipart/form-data">
                                                                                                <div class="row">
                                                                                                    <div class="col-lg-6">
                                                                                                        <fieldset>
                                                                                                            <input type="date" name="Fecha" id="Fecha" required>
                                                                                                        </fieldset>
                                                                                                    </div>
                                                                                                    <div class="col-lg-6">
                                                                                                        <fieldset>
                                                                                                            <input type="url" name="Link" id="Link" placeholder="Link del proyecto" required>
                                                                                                        </fieldset>
                                                                                                    </div>
                                                                                                    <div class="col-lg-6">
                                                                                                        <fieldset>
                                                                                                            <button type="submit" id="form-submit" class="main-button "><i class="fa fa-cogs"></i> Modificar proyecto</button>
                                                                                                        </fieldset>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </form>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    
                                                        <!-- ***** Modificador de acta de constitución ***** -->
                                                        <li>
                                                            <div>
                                                                <div class="col-lg-12">
                                                                    <div class="list-item">
                                                                        <div class="item">
                                                                            <div class="row">
                                                                                <div class="col-lg-12">
                                                                                    <div class="offset-lg-1">
                                                                                        <br>
                                                                                        <h5> Modifique el acta de constitución del proyecto a partir del link de este.</h5>
                                                                                        <br>
                                                                                    </div>
                                                                                    <div class="row">
                                                                                        <div class="col-lg-12">
                                                                                            <form id="contact" action="ModificarArchivo.php" method="post" enctype="multipart/form-data">
                                                                                                <div class="row">
                                                                                                    <div class="col-lg-6">
                                                                                                        <div class="form-group">
                                                                                                            <div class="custom-file">
                                                                                                                <input type="file" class="custom-file-input" id="Acta" name="Acta" required>
                                                                                                                <label class="custom-file-label" for="Acta">Seleccionar nueva acta...</label>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="col-lg-6">
                                                                                                        <fieldset>
                                                                                                            <input type="url" name="Link" id="Link" placeholder="Link del proyecto" required>
                                                                                                        </fieldset>
                                                                                                    </div>
                                                                                                    <div class="col-lg-6">
                                                                                                        <fieldset>
                                                                                                            <button type="submit" id="form-submit" class="main-button "><i class="fa fa-cogs"></i> Modificar proyecto</button>
                                                                                                        </fieldset>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </form>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    
                                                        <!-- ***** Modificador de LinkedIn del patrocinador principal ***** -->
                                                        <li>
                                                            <div>
                                                                <div class="col-lg-12">
                                                                    <div class="list-item">
                                                                        <div class="item">
                                                                            <div class="row">
                                                                                <div class="col-lg-12">
                                                                                    <div class="offset-lg-1">
                                                                                        <br>
                                                                                        <h5> Modifique el linkedIn del patrocinador a partir del nombre de este.</h5>
                                                                                        <br>
                                                                                    </div>
                                                                                    <div class="row">
                                                                                        <div class="col-lg-12">
                                                                                            <form id="contact" action="ModificarLinkedIn.php" method="get" enctype="multipart/form-data">
                                                                                                <div class="row">
                                                                                                    <div class="col-lg-6">
                                                                                                        <fieldset>
                                                                                                            <input type="url" name="new_Link" id="new_Link" placeholder="Nuevo link del patrocinador" required>
                                                                                                        </fieldset>
                                                                                                    </div>
                                                                                                    <div class="col-lg-6">
                                                                                                        <fieldset>
                                                                                                            <input type="text" name="Patrocinador_Principal" id="Patrocinador_Principal" placeholder="Nombre de patrocinador principal" autocomplete="on" required>
                                                                                                        </fieldset>
                                                                                                    </div>
                                                                                                    <div class="col-lg-6">
                                                                                                        <fieldset>
                                                                                                            <button type="submit" id="form-submit" class="main-button "><i class="fa fa-cogs"></i> Modificar proyecto</button>
                                                                                                        </fieldset>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </form>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    
                                                        <!-- ***** Modificador de categoría del proyecto ***** -->
                                                        <li>
                                                            <div>
                                                                <div class="col-lg-12">
                                                                    <div class="list-item">
                                                                        <div class="item">
                                                                            <div class="row">
                                                                                <div class="col-lg-12">
                                                                                    <div class="offset-lg-1">
                                                                                        <br>
                                                                                        <h5> Modifique la categoría del proyecto a partir del link de este.</h5>
                                                                                        <br>
                                                                                    </div>
                                                                                    <div class="row">
                                                                                        <div class="col-lg-12">
                                                                                            <form id="contact" action="ModificarCategoria.php" method="post" enctype="multipart/form-data">
                                                                                                <div class="row">
                                                                                                    <div class="col-lg-6">
                                                                                                        <select name="Categoria" id="Categoria">
                                                                                                            <option value="Sin rubro específico" selected>Sin rubro específico</option>
                                                                                                            <option value="Minería">Minería</option>
                                                                                                            <option value="Oil & Gas">Oil & Gas</option>
                                                                                                            <option value="Energía">Energía</option>
                                                                                                            <option value="Retail">Retail</option>
                                                                                                            <option value="Construcción">Construcción</option>
                                                                                                            <option value="Consumo masivo">Consumo masivo</option>
                                                                                                            <option value="Logística">Logística</option>
                                                                                                        </select>
                                                                                                    </div>
                                                                                                    <div class="col-lg-6">
                                                                                                        <fieldset>
                                                                                                            <input type="url" name="Link" id="Link" placeholder="Link del proyecto" required>
                                                                                                        </fieldset>
                                                                                                    </div>
                                                                                                    <div class="col-lg-6">
                                                                                                        <fieldset>
                                                                                                            <button type="submit" id="form-submit" class="main-button "><i class="fa fa-cogs"></i> Modificar proyecto</button>
                                                                                                        </fieldset>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </form>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    
                                                        <!-- ***** Modificador de nombre de proyecto ***** -->
                                                        <li>
                                                            <div>
                                                                <div class="col-lg-12">
                                                                    <div class="list-item">
                                                                        <div class="item">
                                                                            <div class="row">
                                                                                <div class="col-lg-12">
                                                                                    <div class="offset-lg-1">
                                                                                        <br>
                                                                                        <h5> Modifique el nombre del proyecto a partir del link del proyecto.</h5>
                                                                                        <br>
                                                                                    </div>
                                                                                    <div class="row">
                                                                                        <div class="col-lg-12">
                                                                                            <form id="contact" action="ModificarNombre.php" method="get" enctype="multipart/form-data">
                                                                                                <div class="row">
                                                                                                    <div class="col-lg-6">
                                                                                                        <fieldset>
                                                                                                           <input type="text" name="Nombre_Proyecto" id="Nombre_Proyecto" placeholder="Nuevo nombre del proyecto" autocomplete="on" required>
                                                                                                        </fieldset>
                                                                                                    </div>
                                                                                                    <div class="col-lg-6">
                                                                                                        <fieldset>
                                                                                                            <input type="url" name="Link" id="Link" placeholder="Link del proyecto" required>
                                                                                                        </fieldset>
                                                                                                    </div>
                                                                                                    <div class="col-lg-6">
                                                                                                        <fieldset>
                                                                                                            <button type="submit" id="form-submit" class="main-button "><i class="fa fa-cogs"></i> Modificar proyecto</button>
                                                                                                        </fieldset>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </form>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    
                                                        <!-- ***** Modificador de nombre de gerente ***** -->
                                                        <li>
                                                            <div>
                                                                <div class="col-lg-12">
                                                                    <div class="list-item">
                                                                        <div class="item">
                                                                            <div class="row">
                                                                                <div class="col-lg-12">
                                                                                    <div class="offset-lg-1">
                                                                                        <br>
                                                                                        <h5> Modifique el nombre del gerente del proyecto a partir del link del proyecto.</h5>
                                                                                        <br>
                                                                                    </div>
                                                                                    <div class="row">
                                                                                        <div class="col-lg-12">
                                                                                            <form id="contact" action="ModificarGerente.php" method="get" enctype="multipart/form-data">
                                                                                                <div class="row">
                                                                                                    <div class="col-lg-6">
                                                                                                        <fieldset>
                                                                                                            <input type="text" name="Gerente_de_Proyecto" id="Gerente_de_Proyecto" placeholder="Nuevo nombre de gerente de proyecto" autocomplete="on" required>
                                                                                                        </fieldset>
                                                                                                    </div>
                                                                                                    <div class="col-lg-6">
                                                                                                        <fieldset>
                                                                                                            <input type="url" name="Link" id="Link" placeholder="Link del proyecto" required>
                                                                                                        </fieldset>
                                                                                                    </div>
                                                                                                    <div class="col-lg-6">
                                                                                                        <fieldset>
                                                                                                            <button type="submit" id="form-submit" class="main-button "><i class="fa fa-cogs"></i> Modificar proyecto</button>
                                                                                                        </fieldset>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </form>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li> 
                                                    
                                                        <!-- ***** Modificador de entregables del proyecto ***** -->
                                                        <li>
                                                            <div>
                                                                <div class="col-lg-12">
                                                                    <div class="list-item">
                                                                        <div class="item">
                                                                            <div class="row">
                                                                                <div class="col-lg-12">
                                                                                    <div class="offset-lg-1">
                                                                                        <br>
                                                                                        <h5> Modifique los entregables del proyecto a partir del link de este.</h5>
                                                                                        <br>
                                                                                    </div>
                                                                                    <div class="row">
                                                                                        <div class="col-lg-12">
                                                                                            <form id="contact" action="ModificarEntregables.php" method="get" enctype="multipart/form-data">
                                                                                                <div class="row">
                                                                                                    <div class="col-lg-6">
                                                                                                        <fieldset>
                                                                                                            <textarea name="Entregables" type="text" class="form-control" id="Entregables" placeholder="Nuevos entregables del proyecto" required=""></textarea>  
                                                                                                        </fieldset>
                                                                                                    </div>
                                                                                                    <div class="col-lg-6">
                                                                                                        <br>
                                                                                                        <fieldset>
                                                                                                            <input type="url" name="Link" id="Link" placeholder="Link del proyecto" required>
                                                                                                        </fieldset>
                                                                                                    </div>
                                                                                                    <div class="col-lg-6">
                                                                                                        <fieldset>
                                                                                                            <button type="submit" id="form-submit" class="main-button "><i class="fa fa-cogs"></i> Modificar proyecto</button>
                                                                                                        </fieldset>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </form>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>   
                                                    
                                                        <!-- ***** Modificador de estado del proyecto ***** -->
                                                        <li>
                                                            <div>
                                                                <div class="col-lg-12">
                                                                    <div class="list-item">
                                                                        <div class="item">
                                                                            <div class="row">
                                                                                <div class="col-lg-12">
                                                                                    <div class="offset-lg-1">
                                                                                        <br>
                                                                                        <h5> Modifique el estado del proyecto a partir del link de este.</h5>
                                                                                        <br>
                                                                                    </div>
                                                                                    <div class="row">
                                                                                        <div class="col-lg-12">
                                                                                            <form id="contact" action="ModificarEstado.php" method="post" enctype="multipart/form-data">
                                                                                                <div class="row">
                                                                                                    <div class="col-lg-12 offset-lg-2">
                                                                                                        <fieldset>
                                                                                                            <input type="checkbox" id="1" name="Estado" value="Sin iniciar"><label>Sin iniciar</label>
                                                                                                            <input type="checkbox" id="2" name="Estado" value="En curso"><label>En curso</label>
                                                                                                            <input type="checkbox" id="3" name="Estado" value="Finalizado"><label>Finalizado</label>
                                                                                                        </fieldset>
                                                                                                    </div>
                                                                                                    <div class="col-lg-6 offset-lg-2">
                                                                                                        <fieldset>
                                                                                                            <input type="url" name="Link" id="Link" placeholder="Link del proyecto" required>
                                                                                                        </fieldset>
                                                                                                    </div>
                                                                                                    <div class="col-lg-6 offset-lg-2">
                                                                                                        <fieldset>
                                                                                                            <button type="submit" id="form-submit" class="main-button "><i class="fa fa-cogs"></i> Modificar proyecto</button>
                                                                                                        </fieldset>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </form>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    
                                                        <!-- ***** Modificador de link del proyecto ***** -->
                                                        <li>
                                                            <div>
                                                                <div class="col-lg-12">
                                                                    <div class="list-item">
                                                                        <div class="item">
                                                                            <div class="row">
                                                                                <div class="col-lg-12">
                                                                                    <div class="offset-lg-1">
                                                                                        <br>
                                                                                        <h5> Modifique el link único del proyecto a partir del link que desea cambiar.</h5>
                                                                                        <br>
                                                                                    </div>
                                                                                    <div class="row">
                                                                                        <div class="col-lg-12">
                                                                                            <form id="contact" action="ModificarLink.php" method="get" enctype="multipart/form-data">
                                                                                                <div class="row">
                                                                                                    <div class="col-lg-6">
                                                                                                        <fieldset>
                                                                                                            <input type="url" name="new_Link" id="new_Link" placeholder="Nuevo link del proyecto" required>
                                                                                                        </fieldset>
                                                                                                    </div>
                                                                                                    <div class="col-lg-6">
                                                                                                        <fieldset>
                                                                                                            <input type="url" name="Link" id="Link" placeholder="Actual link del proyecto" required>
                                                                                                        </fieldset>
                                                                                                    </div>
                                                                                                    <div class="col-lg-6">
                                                                                                        <fieldset>
                                                                                                            <button type="submit" id="form-submit" class="main-button "><i class="fa fa-cogs"></i> Modificar proyecto</button>
                                                                                                        </fieldset>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </form>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    
                                                        <!-- ***** Modificador de link del cliente ***** -->
                                                        <li>
                                                            <div>
                                                                <div class="col-lg-12">
                                                                    <div class="list-item">
                                                                        <div class="item">
                                                                            <div class="row">
                                                                                <div class="col-lg-12">
                                                                                    <div class="offset-lg-1">
                                                                                        <br>
                                                                                        <h5> Modifique el link del cliente a partir del nombre de este.</h5>
                                                                                        <br>
                                                                                    </div>
                                                                                    <div class="row">
                                                                                        <div class="col-lg-12">
                                                                                            <form id="contact" action="ModificarLinkCliente.php" method="get" enctype="multipart/form-data">
                                                                                                <div class="row">
                                                                                                    <div class="col-lg-6">
                                                                                                        <fieldset>
                                                                                                            <input type="url" name="new_Link" id="new_Link" placeholder="Nuevo link del cliente" required>
                                                                                                        </fieldset>
                                                                                                    </div>
                                                                                                    <div class="col-lg-6">
                                                                                                        <fieldset>
                                                                                                            <input type="text" name="Cliente" id="Cliente" placeholder="Nombre de cliente" autocomplete="on" required>
                                                                                                        </fieldset>
                                                                                                    </div>
                                                                                                    <div class="col-lg-6">
                                                                                                        <fieldset>
                                                                                                            <button type="submit" id="form-submit" class="main-button "><i class="fa fa-cogs"></i> Modificar proyecto</button>
                                                                                                        </fieldset>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </form>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li><!-- ***** HASTA ACÁ MENÚ Y FORMULARIOS PARA MODIFICAR ***** -->    
                                                    </ul>           
                                                </div>          
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!----    Separación entre cuerpo y footer       !------>
        <div class="degradado col-lg-12"></div>

        <!----    COMIENZO FOOTER      !------>
        <footer>
            <div class="container">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="about">
                            <div class="logo">
                                <img src="assets/images/logo.png" alt="LogoDoBetter">
                            </div>
                            <p>Empresa de consultoría experta en optimización de procesos en cadena de suministro.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 offset-lg-4">
                        <h4>Links útiles</h4>
                        <div class="helpful-links">
                            <div class="row">
                                <div class="col-lg-6 col-sm-6">
                                    <ul>
                                        <li><a href="index.php">Buscar</a></li>
                                        <li><a href="modificador.php">Modificar</a></li>
                                        <li><a href="eliminar.php">Eliminar</a></li>
                                    </ul>
                                </div>
                                <div class="col-lg-6">
                                    <ul>
                                        <li><a href="agregar.php">Añadir</a></li>
                                        <li><a href="https://dobetter.cl/">DoBetter</a></li>
                                        <li><a href="#">Políticas de privacidad</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="sub-footer">
                            <p>Copyright © 2023 Do Better. Todos los derechos reservados.</p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Scripts -->
        <script src="vendor/jquery/jquery.min.js"></script>
        <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="assets/js/owl-carousel.js"></script>
        <script src="assets/js/animation.js"></script>
        <script src="assets/js/imagesloaded.js"></script>
        <script src="assets/js/custom.js"></script>
        <script src="assets/js/tabs.js"></script>
        <script>
            document.getElementById("Acta").onchange = function() {
                var fileName = this.value.split("\\").pop();
                document.querySelector(".custom-file-label").innerHTML = fileName;
            };
        </script>
    </body>
</html>