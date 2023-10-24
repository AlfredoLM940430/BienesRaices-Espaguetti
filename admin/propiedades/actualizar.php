
<?php 

    require '../../includes/funciones.php';
    
    // Autenticado() desde funciones.php
    $auth = Autenticado();

    if(!$auth){
        header('Location: /');
    }

    //Valida ID valido
    $id = $_GET['id'];
    $id = filter_var($id, FILTER_VALIDATE_INT);

    if(!$id){
        header('Location: /admin');
    }

    //Data base
    require '../../includes/config/database.php';
    $db = conectarDB();

    // Consulta propiedades
    $consulta = "SELECT * FROM propiedades WHERE id = {$id}";
    $resultado = mysqli_query($db, $consulta);
    $propiedad = mysqli_fetch_assoc($resultado);
    
    //Consulta vendedores en db
    $consulta = "SELECT * FROM vendedores";
    $resultado = mysqli_query($db, $consulta);

    // Detectar errores
    $errores = [];

    $titulo = $propiedad['titulo']; 
    $precio = $propiedad['precio']; 
    $descripcion = $propiedad['descripcion']; 
    $habitaciones = $propiedad['habitaciones'];  
    $wc = $propiedad['wc'];  
    $estacionamiento = $propiedad['estacionamiento'];  
    $vendedores_id = $propiedad['vendedores_id']; 
    $imagenPropiedad = $propiedad['imagen'];  
    
    //Ejecucion del codigo al enviar el formulario
    if($_SERVER['REQUEST_METHOD'] === 'POST'){

        //mysqli_real_escape_string sanitizar datos antes de enviar a db
        $titulo = mysqli_real_escape_string($db, $_POST['titulo']);
        $precio = mysqli_real_escape_string($db, $_POST['precio']);
        $descripcion = mysqli_real_escape_string($db, $_POST['descripcion']);
        $habitaciones = mysqli_real_escape_string($db, $_POST['habitaciones']);
        $wc = mysqli_real_escape_string($db, $_POST['wc']);
        $estacionamiento = mysqli_real_escape_string($db, $_POST['estacionamiento']);
        $vendedores_id = mysqli_real_escape_string($db, $_POST['vendedor']);
        $creado = date('Y/m/d');

        $imagen = $_FILES['imagen'];

        if(!$titulo) {
            $errores [] = "Debes Añadir un Titulo";
        }

        if(!$precio) {
            $errores [] = "El Precio es Obligatorio";
        }
        
        if(strlen($descripcion < 10)) {
            $errores [] = "Es Obligatorio Añadir una Pequeña Descripcion";
        }

        if(!$habitaciones) {
            $errores [] = "El Numero de Habitaciones no debe estar Vacio";
        }

        if(!$wc) {
            $errores [] = "El Numero de baños no debe estar Vacio";
        }

        if(!$estacionamiento) {
            $errores [] = "El Numero de Espacios de en el Estacionamiento no debe estar Vacio";
        }

        if(!$vendedores_id) {
            $errores [] = "Elije un Vendedor";
        }

        // Validar img por tamaño
        $medida = 1000 * 1000;
        if($imagen['size'] > $medida){
            $errores [] = 'Imagen demasiado pesada';
        }

        // Revision para insertar
        if(empty($errores)){
            
            //Carpeta Imagenes
            $carpetaImagenes = '../../imagenes/';
            if(!is_dir($carpetaImagenes)){
                mkdir($carpetaImagenes);
            }

            $nombreImagen = '';
            
            //Eliminar imagen al agregar una nueva
            if($imagen['name']){
                //Elimina imagen
                unlink($carpetaImagenes . $propiedad['imagen']);
                //Guarda Nueva Imagen Nombre unico
                $nombreImagen = md5(uniqid(rand(), true)) . ".jpg";
                //Guardar imagen en servidor
                move_uploaded_file($imagen['tmp_name'], $carpetaImagenes . $nombreImagen);
            }else{
                //Si no existen cambios, guarda la misma *consultada desde el servidor*
                $nombreImagen = $propiedad['imagen'];
            }
            
            //Isertar en DataBase
            $query = "UPDATE propiedades SET titulo = '{$titulo}', precio = '{$precio}', imagen = '{$nombreImagen}', 
            descripcion = '{$descripcion}', habitaciones = {$habitaciones}, wc = {$wc}, estacionamiento = {$estacionamiento}, vendedores_id = {$vendedores_id} WHERE id = {$id}";
   
            $resultado = mysqli_query($db, $query);
            if($resultado){
                //Redireccionar al guardarse correctamente
                header('Location: /admin?mensaje=Registro Correcto&registro=2');
            } 
        }
    }

    incTemplate('header');
?> 

<main class="contenedor seccion">

    <h1>Actualizar Propiedad</h1>
    <a href="/admin" class="boton boton-verde">Volver</a>

    <?php foreach($errores as $error): ?>
        <div class="alerta error">
            <?php echo $error?>
        </div>  
    <?php endforeach; ?>

    <!-- enctype="multipart/form-data" -->
    <form class="formulario" method="POST" enctype="multipart/form-data">
        <fieldset>
            <legend>Inofrmacion General</legend>
            <label for="titulo">Titulo:</label>
            <input type="text" id="titulo" name="titulo" placeholder="Titulo Propiedad" value="<?php echo $titulo; ?>">

            <label for="precio">Precio:</label>
            <input type="number" id="precio" name="precio" placeholder="Precio Propiedad" value="<?php echo $precio; ?>">

            <label for="imagen">Imagen:</label>
            <input type="file" id="imagen" accept="image/jpeg, image/png" name='imagen'>

            <img src="/imagenes/<?php echo $imagenPropiedad; ?>" class="imagenAct">
            
            <label for="Descripcion">Descripcion:</label>
            <textarea id="descripcion" name="descripcion"><?php echo $descripcion; ?></textarea>
        </fieldset>

        <fieldset>
            <legend>Inofrmacion Propiedad</legend>
            <label for="habitaciones">Habitaciones:</label>
            <input type="number" id="habitaciones" name="habitaciones" placeholder="Ej: 3" min="1" max="9" value="<?php echo $habitaciones; ?>">

            <label for="wc">Baños:</label>
            <input type="number" id="wc" name="wc" placeholder="Ej: 3" value="<?php echo $wc; ?>">

            <label for="estacionamiento">Estacionamiento:</label>
            <input type="number" id="estacionamiento" name="estacionamiento" placeholder="Ej: 3" value="<?php echo $estacionamiento; ?>">
        </fieldset>

        <fieldset>
            <legend>Vendedor</legend>
            <select name="vendedor">
                <option value="">-- Seleccionar --</option>

                <?php while($row = mysqli_fetch_assoc($resultado)): ?>
                    <option <?php echo $vendedores_id === $row['id'] ? 'selected' : ''; ?> value="<?php echo $row['id']; ?>"><?php echo $row["nombre"] . " " . $row["apellido"]; ?></option>
                <?php endwhile; ?>
                    
            </select>
        </fieldset>

        <div class="mgr">
            <input type="submit" value="Actualizar Propiedad" class="boton boton-verde">
        </div>

    </form>
    
</main>

<?php 
    //Cerrar conexion
    mysqli_close($db);
    incTemplate('footer');
?> 
