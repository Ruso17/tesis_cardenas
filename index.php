<?php
session_start();
include 'php/conexion.php';

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $contraseña = $_POST['contraseña'];
    $hash = password_hash($contraseña, PASSWORD_DEFAULT);



    // Verificar si el usuario existe en la base de datos
    $sql = "SELECT u.*, r.rol FROM usuarios u
    JOIN roles r ON u.rol_id = r.id
    WHERE u.email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();

        // Verificar la contraseña
        if (password_verify($contraseña, $usuario['contraseña'])) {
            
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['apellido'] = $usuario['apellido'];
            $_SESSION['rol'] = $usuario['rol'];

            
            if ($usuario['rol'] == 'Administrador') {
                header("Location: php/admin.php");
            } else {
                header("Location: php/usuario.php");
            }
            exit();
        } else {
            $error_message = "Contraseña incorrecta";
        }
    } else {
        $error_message = "Usuario no encontrado";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CADMESOFT</title>
    <link rel="shortcut icon" href="css/icono.ico" />
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
            .error {
            color: white;
            width: calc(100% - 20px);
            background-color: #dc3545;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 5px;
            margin-top: 15px;
            display: inline-block; 
            box-sizing: border-box;
            text-align: center;
        }

        .password-container {
            position: relative;
            width: 95%;
        }

        /* Estilos para el input de contraseña */
        .password-container input {
            width: 100%;
            padding-right: 40px; /* Deja espacio para el ícono */
            box-sizing: border-box;
        }

        /* Estilos para el ícono de ojo */
        .password-container i {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #555; /* Cambia el color si lo deseas */
        }
    </style>
    </style>
</head>
<body>
    <br>
    <br>
    <img src="css/D.jpg" class="img-responsive">
    <div class="login-container">
        <h2>Iniciar sesión</h2>
        <form action="index.php" method="POST">
            <input type="text" name="email" placeholder="Email" required>
            <div class="password-container">
                <input type="password" id="password" name="contraseña" placeholder="Contraseña" required>
                <i class="fas fa-eye" id="togglePassword"></i>
            </div>
            <input type="submit" value="Iniciar Sesión">
        </form>
        <?php if ($error_message): ?>
        <p class='error'><?php echo $error_message; ?></p>
    <?php endif; ?>
    </div>
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function () {
            // Alterna el atributo 'type' entre 'password' y 'text'
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            // Cambia el ícono
            this.classList.toggle('fa-eye-slash');
        });
    </script>
    
</body>
</html>
