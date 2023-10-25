
<!DOCTYPE html>
<html lang="en">
    <head>     
        <style>
            /*Área principal de agregar proyecto*/
            #contacto {
                background-image: url(assets/images/camion1.jpg);
            }

            /*Color de texto principal*/
            #colorTexto {
                color:white;
            }

            select{
               cursor: pointer;
               padding: 8px 12px;
               background-color: #fbff8f;
               border: 1px solid #010101;
               border-radius: 10px;
               width: 100%;
               height: 40px;
            }

            /*Sección entre cuerpo y footer*/
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
                                <li><a href="modificador.php">Modificar</a></li>
                                <li><a href="eliminar.php">Eliminar</a></li>
                                <li><a href="agregar.php" class="active">Añadir</a></li> 
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
    
        <!-- ***** Texto principal de página ***** -->
        <div class="page-heading" id="contacto">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <div class="top-text header-text" id="colorTexto">
                            <h6>Añada un nuevo proyecto</h6>
                            <h2>Rellene la información correspondiente al proyecto</h2>
                            <br><br>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-6 offset-lg-3">
                                <form id="contact" action="InsertarProyecto.php" method="post" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <fieldset>
                                                <input type="text" name="Cliente" id="Cliente" placeholder="Cliente" autocomplete="on" required>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-6">
                                            <fieldset>
                                                <input type="url" name="Link_Cliente" id="Link_Cliente" placeholder="Link de cliente" required>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-12">
                                            <fieldset>
                                                <input type="text" name="Nombre_Proyecto" id="Nombre_Proyecto" placeholder="Nombre del proyecto" required="">
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <div class="custom-file">
                                                   <input type="file" class="custom-file-input" id="fileInput" name="Acta" required>
                                                   <label class="custom-file-label" for="fileInput">Insertar acta del proyecto (doc, docx o pdf)</label>
                                                </div>
                                             </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <fieldset>
                                                <input type="date" name="Fecha" id="Fecha" required>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-6">
                                            <select name="Categoria" id="categoria">
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
                                                <input type="text" name="Gerente_de_Proyecto" id="Gerente_de_Proyecto" placeholder="Gerente de Proyecto" autocomplete="on" required>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-6">
                                            <fieldset>
                                                <input type="text" name="Patrocinador_Principal" id="Patrocinador_Principal" placeholder="Patrocinador principal" autocomplete="on" required>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-6">
                                            <fieldset>
                                                <input type="url" name="Link_Patrocinador" id="Link_Patrocinador" placeholder="LinkedIn de Patrocinador" required>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-12">
                                            <fieldset>
                                                <input type="url" name="Link_Proyecto" id="Link_Proyecto" placeholder="Link del proyecto" required>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-12">
                                            <fieldset>
                                                <textarea name="Objetivo_del_Proyecto" type="text" class="form-control" id="Objetivo_del_Proyecto" placeholder="Objetivos del proyecto" required=""></textarea>  
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-12">
                                            <fieldset>
                                                <textarea name="Entregables" type="text" class="form-control" id="Entregables" placeholder="Entregables del proyecto" required=""></textarea>  
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-12 offset-lg-2">
                                            <ul>
                                                <li><input type="checkbox" id="radio1" name="Estado" value="Sin iniciar"><span>Sin iniciar</span></li>
                                                <li><input type="checkbox" id="radio2" name="Estado" value="En curso"><span>En curso</span></li>
                                                <li><input type="checkbox" id="radio3" name="Estado" value="Finalizado"><span>Finalizado</span></li>
                                            </ul>
                                        </div>
                                        <div class="col-lg-12">
                                            <fieldset>
                                                <button type="submit" id="form-submit" class="main-button "><i class="fa fa-plus"></i> Añadir proyecto</button>
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
        <!-- ***** HASTA ACÁ CÓDIGO ***** -->
    
        <!-- ***** Separación entre cuerpo y footer ***** -->
        <div class="degradado col-lg-12"></div>
    
        <!-- ***** COMIENZO FOOTER ***** -->
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
                        <div class="helpful-links">
                            <h4>Links útiles</h4>
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
    </body>
</html>
