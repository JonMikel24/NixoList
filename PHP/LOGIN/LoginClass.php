<?php
class LoginClass {
    private $usuario;
    private $contrasena;
    private $conexion;

    public function __construct($usuario, $contrasena) {
        $this->usuario = trim($usuario);
        $this->contrasena = trim($contrasena);

        require_once("../conexion.php");
        $this->conexion = $conexion;

        $this->verificarUsuario();
    }

    private function verificarUsuario() {

        $stmt = $this->conexion->prepare(
            "SELECT id_usuario, username, password_hash, bio, avatar, banner
             FROM usuarios 
             WHERE username = ?"
        );

        $stmt->bind_param("s", $this->usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {

            $fila = $resultado->fetch_assoc();

            if (password_verify($this->contrasena, $fila['password_hash'])) {

                session_start();
                

                $_SESSION['Usuario'] = $fila['username'];
                $_SESSION['id_usuario'] = $fila['id_usuario'];
                $_SESSION['Biografia'] = $fila['bio'];
                $_SESSION['Banner'] = !empty($fila['banner']) 
                    ? $fila['banner'] 
                    : '../Recursos/Banners/banner_default.jpg';
                $_SESSION['valid'] = true;
    
                $_SESSION['Foto'] = !empty($fila['avatar']) 
                    ? $fila['avatar'] 
                    : '../img/default-avatar.png';


                header("Location: ../paginas/index.php");
                exit();

            } else {
                    session_start();
                    $_SESSION['error_login'] = "Contraseña incorrecta.";
                    header("Location: index.php");
                    exit();
            }

        } else {
                session_start();
                $_SESSION['error_login'] = "El usuario no existe.";
                header("Location: index.php");
                exit();
        }

        $stmt->close();
        $this->conexion->close();
    }
    
}
?>
