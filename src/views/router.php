<?php

$pagina = isset($_GET['secao']) ? $_GET['secao'] :  include '../src/views/404.php';;

$rotas = [
    'home' => '../src/views/home/home.php',
    'usuarios' => '../src/views/usuarios/usuarios.php',
    'usuario' => '../src/views/usuarios/editar-usuario.php',
    'login' => '../src/views/login/login.php',
    'sair' => '../src/views/login/sair.php',
    'error' => '../src/views/error.php',
    'cadastro' => '../src/views/cadastro/cadastro.php',
    'orgaos' => '../src/views/orgaos/orgaos.php',
    'orgao' => '../src/views/orgaos/editar-orgao.php',
    'ficha-orgao' => '../src/views/orgaos/ficha-orgao.php',
    'orgaos-tipos' => '../src/views/orgaos/orgaos-tipos.php',
    'orgao-tipo' => '../src/views/orgaos/editar-orgao-tipo.php',
    'pessoas' => '../src/views/pessoas/pessoas.php',
    'pessoa' => '../src/views/pessoas/editar-pessoa.php',
    'pessoas-tipos' => '../src/views/pessoas/pessoas-tipos.php',
    'pessoa-tipo' => '../src/views/pessoas/editar-pessoa-tipo.php',
    'profissoes' => '../src/views/pessoas/profissoes.php',
    'profissao' => '../src/views/pessoas/editar-profissao.php',
    'nota' => '../src/views/notas/nota.php',
    'imprimir-nota' => '../src/views/notas/imprimir-nota.php',
    'oficios' => '../src/views/oficios/oficios.php',
    'oficio' => '../src/views/oficios/editar-oficio.php',
    'proposicoes' => '../src/views/proposicoes/proposicoes.php',
    'atualizar-proposicoes' => '../src/views/proposicoes/atualizar-proposicoes.php'
];

if (array_key_exists($pagina, $rotas)) {
    include $rotas[$pagina];
} else {
    include '../src/views/404.php';
}
