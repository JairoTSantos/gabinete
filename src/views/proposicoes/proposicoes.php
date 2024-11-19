<?php

require_once '../vendor/autoload.php';


use Jairosantos\GabineteDigital\Controllers\ProposicaoController;

$proposicao = new ProposicaoController();

print_r($proposicao->inserirProposicoesAutores(2024));

?>