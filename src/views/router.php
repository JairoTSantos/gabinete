<?php

$pagina = isset($_GET['secao']) ? $_GET['secao'] : 'home';

$rotas = [
    'home' => '../src/views/home/home.php',
    'usuarios' => '../src/views/usuarios/usuarios.php',
    'usuario' => '../src/views/usuarios/editar-usuario.php',
    'login' => '../src/views/login/login.php',
    'sair' => '../src/views/login/sair.php',
    'error' => '../src/views/error.php'
];

if (array_key_exists($pagina, $rotas)) {
    include $rotas[$pagina];
} else {
    include '../src/views/404.php';
}
