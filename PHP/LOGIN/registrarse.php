<!doctype html>
<html lang="es">
    <head>
        <title>NixoList - Registrarse</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
            crossorigin="anonymous"
        />
        <link href="../../css/styles.css" rel="stylesheet"/>
        
        <style>
            .textoerror { 
                color: #ff6b6b; /* Rojo claro para que resalte en el fondo gris */
                font-size: 0.85rem; 
                display: block; 
                margin-top: 5px; 
            }
        </style>
    </head>
<body class="formregistro">
    
    <div class="container d-flex justify-content-center align-items-center w-100">
        <div class="card shadow p-4 formuregistro" style="width: 22rem;">
            <h3 class="text-center mb-4 formregtext">Registrarse</h3>

            <form action="crearUsuario.php" method="POST" id="form">
                <div class="mb-3">
                    <label for="usuario" class="form-label">Usuario</label>
                    <input type="text" class="form-control" id="usuario" name="usuario" placeholder="Ingresa tu usuario">
                    <span id="error-usu" class="textoerror"></span>

                    <?php if (isset($_GET['error']) && $_GET['error'] === 'usuario'): ?>
                        <script>
                            document.addEventListener('DOMContentLoaded', () => {
                                const input = document.getElementById('usuario');
                                const error = document.getElementById('error-usu');

                                error.textContent = 'Este usuario ya está en uso';
                                input.classList.add('is-invalid');
                            });
                        </script>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="text" class="form-control" id="email" name="email" placeholder="Ingresa tu email">
                    <span id="error-email" class="textoerror"></span>
                    
                    <?php if (isset($_GET['error']) && $_GET['error'] === 'email'): ?>
                        <script>
                            document.addEventListener('DOMContentLoaded', () => {
                                const emailInput = document.getElementById('email');
                                const errorEmail = document.getElementById('error-email');

                                errorEmail.textContent = 'Este email ya está en uso';
                                emailInput.classList.add('is-invalid');
                            });
                        </script>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="contrasena" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="contrasena" name="contrasena" placeholder="Ingresa tu contraseña">
                    <span id="error-contra" class="textoerror"></span>
                    
                    <a href="index.php" id="registroTexto"><p id="registroTexto" class="mt-2">¿Ya tienes cuenta? Inicia sesión</p></a>
                </div>

                <button type="submit" class="btn w-100 mb-2 mt-3 boton-sesion">Registrarse</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../js/validaciones.js"></script>
</body>
</html>