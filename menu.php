<header>

        <div class="navbar navbar-expand-lg navbar-dark bg-dark ">
            <div class="container">
                <a href="<?php echo SITE_URL ?>" class="navbar-brand">
                    <strong>Tienda Online</strong>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarHeader">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a href="#" class="nav-link active">Catálogo</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link ">Contacto</a>
                        </li>

                    </ul>

                    <a href="checkout.php" class="btn btn-primary me-2"><i class="fa-solid fa-cart-shopping"></i> Carrito <span id="num_cart" class="badge bg-secondary"><?php echo $num_cart ?></span></a>


                    <?php if (isset($_SESSION["user_name"])) { ?>



                        <div class="dropdown">
                            <button class="btn btn-success dropdown-toggle" type="button" id="dropdownMenu2" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-user"></i> <?php echo $_SESSION["user_name"] ?>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
                                <li><a href="compras.php" class="dropdown-item" type="button">Mis Compras</a></li>
                                <li><button id="CerrarSesion" class="dropdown-item" type="button">Cerrar Sesión</button></li>
                            </ul>
                        </div>





                    <?php } else { ?>

                        <a href="login.php" class="btn btn-success">Ingresar</a>

                    <?php } ?>
                </div>

            </div>
        </div>
    </header>