<?php 
    require 'includes/funciones.php';
    incTemplate('header');

    //Valida ID valido
    $id = $_GET['id'];
    $id = filter_var($id, FILTER_VALIDATE_INT);

    if(!$id){
        header('Location: /');
    }

    //Data base
    require 'includes/config/database.php';
    $db = conectarDB();

    $consulta = "SELECT * FROM propiedades WHERE id = {$id}";
    $resultado = mysqli_query($db, $consulta);

    if(!$resultado->num_rows){
        header('Location: /');
    }

    $propiedad = mysqli_fetch_assoc($resultado);
?> 

<main class="contenedor seccion contenido-centrado">
    <h1><?php echo $propiedad['titulo']; ?></h1>

    <picture>
        <img loading="lazy" src="/imagenes/<?php echo $propiedad['imagen']; ?>" alt="anuncio">
    </picture>

    <div class="resumen-propiedad">
        <p class="precio"><?php echo '$' . number_format($propiedad['precio']);?></p>
        <ul class="iconos-caracteristicas">
            <li>
                <img class="icono" loading="lazy" src="build/img/icono_wc.svg" alt="icono wc">
                <p><?php echo number_format($propiedad['wc']);?></p>
            </li>
            <li>
                <img class="icono" loading="lazy" src="build/img/icono_estacionamiento.svg" alt="icono estacionamiento">
                <p><?php echo number_format($propiedad['estacionamiento']);?></p>
            </li>
            <li>
                <img class="icono"  loading="lazy" src="build/img/icono_dormitorio.svg" alt="icono habitaciones">
                <p><?php echo number_format($propiedad['habitaciones']);?></p>
            </li>
        </ul>

        <p><?php echo ($propiedad['descripcion']);?>
    </div>
</main>

<?php 
    //Cerrar conexion
    mysqli_close($db);
    incTemplate('footer');
?> 
