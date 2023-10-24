<?php 

    require 'includes/config/database.php';
    $db = conectarDB();

    //Errores
    $errores = [];

    //Autenticar USR
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        /*
        echo "<pre>";
        var_dump($_POST);
        echo "</pre>";
        */
        $email = mysqli_real_escape_string($db, filter_var($_POST['email'], FILTER_VALIDATE_EMAIL));
        $password = mysqli_real_escape_string($db, $_POST['password']);

        if(!$email){
            $errores[] = "El email es obligatorio o no es valido";
        }
        if(!$password){
            $errores[] = "Contraseña obligatoria";
        }

        if(empty($errores)){
            //Revisar si USR existe
            $query = "SELECT * FROM usuarios WHERE email = '{$email}'";
            $resultado = mysqli_query($db, $query);

            

            if($resultado->num_rows){
                //Revisar password
                $usuario = mysqli_fetch_assoc($resultado);

                //Verificar PWD
                $auth = password_verify($password, $usuario['password']);
                
                if($auth){
                    //Iniciar sesion
                    session_start(); 
                    
                    //Rellenar arreglo sesion || Se puede guradar cualquier informacion con $_SESSION
                    $_SESSION['usuario'] = $usuario['email'];
                    $_SESSION['login'] = true;

                    header('location: /admin');

                    echo "<pre>";
                    var_dump($_SESSION);
                    echo "</pre>";
                }else{
                    $errores[] = 'Contraseña incorrecta';
                }
            }else{
                $errores[] = "El usuario no existe";
            }
        }
    }



    require 'includes/funciones.php';
    incTemplate('header');
?> 

<main class="contenedor seccion contenido-centrado">
    <h1>Iniciar sesión</h1>

    <?php foreach($errores as $error) : ?>

        <div class="alerta error">
            <?php echo $error ?>
        </div>

    <?php endforeach; ?>

    <form method="POST" class="formulario">
        <fieldset>
                <legend>Email & Contraseña</legend>

                <label for="email">E-mail</label>
                <input type="email" name="email" placeholder="Correo E-Mail" id="email" required>

                <label for="password">Contraseña</label>
                <input type="password" name="password" placeholder="Tu Contraseña" id="password" required>

        </fieldset>

        <div class="mgr">
            <input type="submit" value="Iniciar sesión" class="boton boton-verde">
        </div>
        
    </form>
</main>

<?php 
incTemplate('footer');
?> 