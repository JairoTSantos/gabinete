<?php

namespace Jairosantos\GabineteDigital\Controllers;

use Jairosantos\GabineteDigital\Core\Logger;
use Jairosantos\GabineteDigital\Models\Proposicao;
use Jairosantos\GabineteDigital\Core\GetJson;

use Dotenv\Dotenv;


$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();


class ComissoesController {
    private $proposicaoModel;
    private $logger;
    private $getjson;


    public function __construct() {
        $this->proposicaoModel = new Proposicao();
        $this->logger = new Logger();
        $this->getjson = new GetJson();
    }


    public function comissoesDoGabinete($filtro) {

        if ($filtro) {
            $comissoesJson = $this->getjson->getJson('https://dadosabertos.camara.leg.br/api/v2/deputados/' . $_ENV['ID_DEPUTADO'] . '/orgaos?itens=100&ordem=ASC&ordenarPor=dataInicio');
        } else {
            $comissoesJson = $this->getjson->getJson('https://dadosabertos.camara.leg.br/api/v2/deputados/' . $_ENV['ID_DEPUTADO'] . '/orgaos?dataInicio=' . $_ENV['ANO_PRIMEIRO_MANDATO'] . '-01-01&dataFim=' . date('Y-m-d') . '&itens=100&ordem=ASC&ordenarPor=dataInicio');
        }

        if (isset($comissoesJson['error'])) {
            return $comissoesJson['error'];
            exit;
        } else if (isset($comissoesJson['status']) && $comissoesJson['status'] == '404') {
            return $comissoesJson['title'];
            exit;
        } else {
            return $comissoesJson;
        }
    }
}
