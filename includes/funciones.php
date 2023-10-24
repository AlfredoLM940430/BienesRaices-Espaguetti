<?php 
require 'app.php';

function incTemplate(string $nombre, bool $inicio = false){
    include TEMPLATES_URL . "/{$nombre}.php";
}

function Autenticado() : bool {
        
    //Informacion alamacenada en $_SESSION desde login L.40
    session_start();
    $auth = $_SESSION['login'];

    if($auth){
        return true;
    }

    return false;       
}
