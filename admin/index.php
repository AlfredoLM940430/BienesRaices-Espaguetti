<?php 

    require '../includes/funciones.php';
    // Autenticado() desde funciones.php
    $auth = Autenticado();

    if(!$auth){
        header('Location: /');
    }

    //Importar BD
    require '../includes/config/database.php';
    $db = conectarDB();

    //Query
    $query = "SELECT * FROM propiedades";

    //Consulta BD
    $Consulta = mysqli_query($db, $query);

    //Solicitud condicional
    $registro = $_GET['registro'] ?? null;

    //Formulario Eliminar, guardar ID seleccionado
    if($_SERVER['REQUEST_METHOD'] === 'POST'){

        $id = $_POST['id'];
        $id = filter_var($id, FILTER_VALIDATE_INT);
        
        if($id){

            //Eliminar IMG en servido
            $query = "SELECT imagen FROM propiedades WHERE id = {$id}";
            $resultado = mysqli_query($db, $query);
            $propiedad = mysqli_fetch_assoc($resultado);

            unlink('../imagenes/' . $propiedad['imagen']);

            //Eliminar en BD
            $query = "DELETE FROM propiedades WHERE id = {$id}";
            $resultado = mysqli_query($db, $query);

            if($resultado){
                header('Location: /admin?registro=3');
            }
        }
    }
    //Incluye template
    incTemplate('header');
?> 

    <main class="contenedor seccion">
        <h1>Administrador de Bienes Raices</h1>
        
        <?php if(intval($registro) === 1) : ?>
            <p class="alerta exito">Anuncio Creado Correctamente</p>
        <?php elseif(intval($registro) === 2) : ?>
            <p class="alerta exito">Anuncio Actualizado Correctamente</p>
        <?php elseif(intval($registro) === 3) : ?>
            <p class="alerta exito">Anuncio Eliminado Correctamente</p>
        <?php endif; ?>

        <div class="mgr">
            <a href="/admin/propiedades/crear.php" class="boton boton-verde">Nueva propiedad</a>
        </div>

        <table class="propiedades">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Titulo</th>
                    <th>Imagen</th>
                    <th>Precio</th>
                    <th>Acción</th>
                </tr>
            </thead>
            
            <!-- Resultado de la BD -->
            <tbody>
                <?php  while($propiedad = mysqli_fetch_assoc($Consulta)): ?>
                <tr>
                    <td><?php echo $propiedad['id']; ?></td>
                    <td><?php echo $propiedad['titulo']; ?></td>
                    <td><img class="imagen-tabla" src="/imagenes/<?php echo $propiedad['imagen']; ?>" alt="imagen-tabla"></td>
                    <td><?php echo '$' . number_format($propiedad['precio']); ?></td>
                    <td>
                        <a class="boton-amarillo-block" href="propiedades/actualizar.php?id=<?php echo $propiedad['id']; ?>">Actualizar</a>
                        <form method="POST" class="w-100">
                            <input type="hidden" name="id" value="<?php echo $propiedad['id']; ?>">
                            <input type="submit" class="boton-rojo-block" value="eliminar">   
                        </form>                       
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
    </main>

<?php 
    //Cerrar conexion
    mysqli_close($db);
    incTemplate('footer');
?> 