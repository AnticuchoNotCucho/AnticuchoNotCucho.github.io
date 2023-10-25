<!DOCTYPE html>
<html lang="en">
    <head>
        <style>
            /* Alinea texto principal de modificador*/
            #lista {
                text-align: center;
                align-items: center;
            }

            /* Menu de modificador, 2 columnas y disposición para que se vea bien*/
            #menu1 {
                display:inherit;
                columns: 2;
            }

            /* Page heading de modificador (área de texto principal)*/
            #modifica {
                background-color: rgb(255, 255, 255);
                background-image: none;
                margin-top: 0px;
                padding-top: 150px;
                height:120px;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            /* Texto de distintos aspectos en el modificador*/
            .offset-lg-1 {
                color:white;
                opacity: 100%;
            }

            /* Color de checkbox de modificador*/
            .col-lg-12 .offset-lg-2 {
                color:white;
                opacity: 100%;
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
                        // Continuar con la sesión de usuario
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
        <!-- ***** Término de Preloader ***** -->

        <!-- ***** Comienzo de Header ***** -->
        <header class="header-area header-sticky wow slideInDown" data-wow-duration="0.75s" data-wow-delay="0s">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <nav class="main-nav">
                            <!-- ***** Comienzo de Logo ***** -->
                            <a href="index.php" class="logo"></a>
                            <!-- ***** Término de Logo ***** -->

                            <!-- ***** Comienzo de Menu ***** -->
                            <ul class="nav">
                                <li><a href="index.php" class="active">Buscar</a></li>
                                <li><a href="modificador.php">Modificar</a></li>
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

        <!-- ***** Texto principal ***** -->
        <div class="main-banner">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="top-text header-text">
                            <h6>Buscador de proyectos</h6>
                            <h2>Seleccione un filtro y busque</h2>
                        </div>
                    </div>

                    <!-- ***** Buscador por nombre de proyecto ***** -->
                    <div class="col-lg-6 offset-lg-3" id="formulario1">
                        <form id="search-form" name="gs" method="get" role="search" action="ConsultaNombre.php">
                             <div class="row">
                                <div class="col-lg-6 align-self-center">
                                    <fieldset>
                                        <input type="address" name="input1" class="searchText" placeholder="Buscar por nombre de proyecto" autocomplete="on" required>
                                    </fieldset>
                                </div>
                                <div class="col-lg-6 align-self-center">                        
                                    <fieldset>
                                        <button class="main-button" id="botonBusqueda"><i class="fa fa-search"></i> Buscar</button>
                                    </fieldset>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- ***** Buscador entre fechas ***** -->
                    <div class="col-lg-6 offset-lg-3" id="formulario4" style="display: none;">
                        <form id="search-form" name="gs" method="get" role="search" action="ConsultaFechas.php">
                            <div class="row">
                                <div class="col-lg-4 align-self-center">
                                    <fieldset>
                                        <input type="date" name="input1" class="searchText" placeholder="Fecha mínima" autocomplete="on" required>
                                        <label>Fecha mínima</label>
                                    </fieldset>
                                </div>
                                <div class="col-lg-4 align-self-center">
                                    <fieldset>
                                        <input type="date" name="input2" class="searchText" placeholder="Fecha máxima" autocomplete="on" required>
                                        <label>Fecha máxima</label>
                                    </fieldset>
                                </div>
                                <div class="col-lg-4 align-self-center">                        
                                    <fieldset>
                                        <button class="main-button"><i class="fa fa-search"></i> Buscar</button>
                                    </fieldset>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- ***** Buscador por nombre de cliente ***** -->
                    <div class="col-lg-6 offset-lg-3" id="formulario2" style="display: none;">
                        <form id="search-form" name="gs" method="get" role="search" action="ConsultaCliente.php">
                            <div class="row">
                                <div class="col-lg-6 align-self-center">
                                    <fieldset>
                                        <input type="address" name="input1" class="searchText" placeholder="Buscar por cliente" autocomplete="on" required>
                                    </fieldset>
                                </div>
                                <div class="col-lg-6 align-self-center">                        
                                    <fieldset>
                                        <button class="main-button"><i class="fa fa-search"></i> Buscar</button>
                                    </fieldset>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- ***** Buscador por palabras clave ***** -->
                    <div class="col-lg-6 offset-lg-3" id="formulario3" style="display: none;">
                        <form id="search-form" name="gs" method="get" role="search" action="ConsultaClave.php">
                            <div class="row">
                                <div class="col-lg-6 align-self-center">
                                    <fieldset>
                                        <input type="address" name="input1" class="searchText" placeholder="Buscar por palabra clave" autocomplete="on" required>
                                    </fieldset>
                                </div>
                                <div class="col-lg-6 align-self-center">                        
                                    <fieldset>
                                        <button class="main-button"><i class="fa fa-search"></i> Buscar</button>
                                    </fieldset>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- ***** Buscador por categoría de proyecto ***** -->
                    <div class="col-lg-6 offset-lg-3" id="formulario5" style="display: none;">
                        <form id="search-form" name="gs" method="get" role="search" action="ConsultaCategoria.php">
                             <div class="row">
                                <div class="col-lg-6 align-self-center">
                                    <select name="input1" id="categoria" class="searchText">
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
                                <div class="col-lg-6 align-self-center">                        
                                    <fieldset>
                                        <button class="main-button" id="botonBusqueda"><i class="fa fa-search"></i> Buscar</button>
                                    </fieldset>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- ***** Botones para seleccionar formulario/filtro ***** -->
                    <div class="col-lg-10 offset-lg-1">
                        <ul class="categories">
                            <li><a id="boton"><span id="bot1" class="icon" style="background-color: #2b2d42;"><img src="assets/images/proyecto.png" alt="Home"></span> Nombre proyecto</a></li>
                            <li><a id="boton1"><span id="bot2" class="icon"><img src="assets/images/cliente.png" alt="Vehicle"></span> Cliente</a></li>
                            <li><a id="boton2"><span id="bot3" class="icon"><img src="assets/images/palabraclave.png" alt="Shopping"></span> Palabras Clave</a></li>
                            <li><a id="boton4"><span id="bot5" class="icon"><img src="assets/images/categoria.png" alt="Categoria"></span> Categoría</a></li>
                            <li><a id="boton3"><span id="bot4" class="icon"><img src="assets/images/fechas.png" alt="Food"></span> Entre fechas</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!----    HASTA ACÁ BUSCADOR       !------>

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
        <script>
            document.getElementById("boton").addEventListener("click", function() {
                var formulario1 = document.getElementById("formulario1");
                var formulario2 = document.getElementById("formulario2");
                var formulario3 = document.getElementById("formulario3");
                var formulario4 = document.getElementById("formulario4");
                var formulario5 = document.getElementById("formulario5");
                var boton1 = document.getElementById("bot1");
                var boton2 = document.getElementById("bot2");
                var boton3 = document.getElementById("bot3");
                var boton4 = document.getElementById("bot4");
                var boton5 = document.getElementById("bot5");
                if (formulario1.style.display === "none") {
                    formulario1.style.display = "block";
                    formulario2.style.display = "none";
                    formulario3.style.display = "none";
                    formulario4.style.display = "none";
                    formulario5.style.display = "none";
                    boton1.style.backgroundColor="#2b2d42"
                    boton2.style.backgroundColor="#fff"
                    boton3.style.backgroundColor="#fff"
                    boton4.style.backgroundColor="#fff"
                    boton5.style.backgroundColor="#fff"         
                } else {
                    formulario1.style.display = "block";
                    formulario2.style.display = "none";
                    formulario3.style.display = "none";
                    formulario4.style.display = "none";
                    formulario5.style.display = "none";
                    boton1.style.backgroundColor="#2b2d42"
                    boton2.style.backgroundColor="#fff"
                    boton3.style.backgroundColor="#fff"
                    boton4.style.backgroundColor="#fff"
                    boton5.style.backgroundColor="#fff"     
                }
            });

            document.getElementById("boton1").addEventListener("click", function() {
                var formulario1 = document.getElementById("formulario1");
                var formulario2 = document.getElementById("formulario2");
                var formulario3 = document.getElementById("formulario3");
                var formulario4 = document.getElementById("formulario4");
                var formulario5 = document.getElementById("formulario5");
                var boton1 = document.getElementById("bot1");
                var boton2 = document.getElementById("bot2");
                var boton3 = document.getElementById("bot3");
                var boton4 = document.getElementById("bot4");
                var boton5 = document.getElementById("bot5");
                if (formulario2.style.display === "none") {
                    formulario2.style.display = "block";
                    formulario1.style.display = "none";
                    formulario3.style.display = "none";
                    formulario4.style.display = "none";
                    formulario5.style.display = "none";
                    boton2.style.backgroundColor="#2b2d42"
                    boton1.style.backgroundColor="#fff"
                    boton3.style.backgroundColor="#fff"
                    boton4.style.backgroundColor="#fff"
                    boton5.style.backgroundColor="#fff"  
                } else {
                    formulario2.style.display = "block";
                    formulario1.style.display = "none";
                    formulario3.style.display = "none";
                    formulario4.style.display = "none";
                    formulario5.style.display = "none";
                    boton2.style.backgroundColor="#2b2d42"
                    boton1.style.backgroundColor="#fff"
                    boton3.style.backgroundColor="#fff"
                    boton4.style.backgroundColor="#fff"
                    boton5.style.backgroundColor="#fff"  
                }   
            });

            document.getElementById("boton2").addEventListener("click", function() {
                var formulario1 = document.getElementById("formulario1");
                var formulario2 = document.getElementById("formulario2");
                var formulario3 = document.getElementById("formulario3");
                var formulario4 = document.getElementById("formulario4");
                var formulario5 = document.getElementById("formulario5");
                var boton1 = document.getElementById("bot1");
                var boton2 = document.getElementById("bot2");
                var boton3 = document.getElementById("bot3");
                var boton4 = document.getElementById("bot4");
                var boton5 = document.getElementById("bot5");
                if (formulario3.style.display === "none") {
                    formulario3.style.display = "block";
                    formulario1.style.display = "none";
                    formulario2.style.display = "none";
                    formulario4.style.display = "none";
                    formulario5.style.display = "none";
                    boton3.style.backgroundColor="#2b2d42"
                    boton2.style.backgroundColor="#fff"
                    boton1.style.backgroundColor="#fff"
                    boton4.style.backgroundColor="#fff"
                    boton5.style.backgroundColor="#fff"  
                } else {
                    formulario3.style.display = "block";
                    formulario1.style.display = "none";
                    formulario2.style.display = "none";
                    formulario4.style.display = "none";
                    formulario5.style.display = "none";
                    boton3.style.backgroundColor="#2b2d42"
                    boton2.style.backgroundColor="#fff"
                    boton1.style.backgroundColor="#fff"
                    boton4.style.backgroundColor="#fff"
                    boton5.style.backgroundColor="#fff"  
                }
            });

            document.getElementById("boton3").addEventListener("click", function() {
                var formulario1 = document.getElementById("formulario1");
                var formulario2 = document.getElementById("formulario2");
                var formulario3 = document.getElementById("formulario3");
                var formulario4 = document.getElementById("formulario4");
                var formulario5 = document.getElementById("formulario5");
                var boton1 = document.getElementById("bot1");
                var boton2 = document.getElementById("bot2");
                var boton3 = document.getElementById("bot3");
                var boton4 = document.getElementById("bot4");
                var boton5 = document.getElementById("bot5");
                if (formulario4.style.display === "none") {
                    formulario4.style.display = "block";
                    formulario1.style.display = "none";
                    formulario3.style.display = "none";
                    formulario2.style.display = "none";
                    formulario5.style.display = "none";
                    boton4.style.backgroundColor="#2b2d42"
                    boton2.style.backgroundColor="#fff"
                    boton3.style.backgroundColor="#fff"
                    boton1.style.backgroundColor="#fff"
                    boton5.style.backgroundColor="#fff"  
                } else {
                    formulario4.style.display = "block";
                    formulario1.style.display = "none";
                    formulario3.style.display = "none";
                    formulario2.style.display = "none";
                    formulario5.style.display = "none";
                    boton4.style.backgroundColor="#2b2d42"
                    boton2.style.backgroundColor="#fff"
                    boton3.style.backgroundColor="#fff"
                    boton1.style.backgroundColor="#fff"
                    boton5.style.backgroundColor="#fff"  
                }
            });
          
            document.getElementById("boton4").addEventListener("click", function() {
                var formulario1 = document.getElementById("formulario1");
                var formulario2 = document.getElementById("formulario2");
                var formulario3 = document.getElementById("formulario3");
                var formulario4 = document.getElementById("formulario4");
                var formulario5 = document.getElementById("formulario5");
                var boton1 = document.getElementById("bot1");
                var boton2 = document.getElementById("bot2");
                var boton3 = document.getElementById("bot3");
                var boton4 = document.getElementById("bot4");
                var boton5 = document.getElementById("bot5");
                if (formulario5.style.display === "none") {
                    formulario5.style.display = "block";
                    formulario1.style.display = "none";
                    formulario3.style.display = "none";
                    formulario2.style.display = "none";
                    formulario4.style.display = "none";
                    boton5.style.backgroundColor="#2b2d42"
                    boton2.style.backgroundColor="#fff"
                    boton3.style.backgroundColor="#fff"
                    boton1.style.backgroundColor="#fff"
                    boton4.style.backgroundColor="#fff"  
                } else {
                    formulario5.style.display = "block";
                    formulario1.style.display = "none";
                    formulario3.style.display = "none";
                    formulario2.style.display = "none";
                    formulario4.style.display = "none";
                    boton5.style.backgroundColor="#2b2d42"
                    boton2.style.backgroundColor="#fff"
                    boton3.style.backgroundColor="#fff"
                    boton1.style.backgroundColor="#fff"
                    boton4.style.backgroundColor="#fff"  
                }
            });
        </script>
    </body>
</html>
