<?php

namespace Jairosantos\GabineteDigital\Controllers;

use Jairosantos\GabineteDigital\Core\Logger;
use Jairosantos\GabineteDigital\Models\Proposicao;
use Jairosantos\GabineteDigital\Core\GetJson;
use PDOException;



class ProposicaoController {
    private $proposicaoModel;
    private $logger;
    private $getjson;


    public function __construct() {
        $this->proposicaoModel = new Proposicao();
        $this->logger = new Logger();
        $this->getjson = new GetJson();
    }

    public function inserirProposicoes($ano) {

        $proposicoesJson = $this->getjson->getJson('https://dadosabertos.camara.leg.br/arquivos/proposicoes/json/proposicoes-' . $ano . '.json');
        $dados = [];

        try {

            foreach ($proposicoesJson['dados'] as $proposicao) {
                $dados[] = [
                    'proposicao_id' => $proposicao['id'],
                    'proposicao_numero' => $proposicao['numero'],
                    'proposicao_titulo' => $proposicao['siglaTipo'] . ' ' . $proposicao['numero'] . '/' . $proposicao['ano'],
                    'proposicao_ano' => ($proposicao['ano'] == 0) ? $ano : $proposicao['ano'],
                    'proposicao_tipo' => $proposicao['siglaTipo'],
                    'proposicao_ementa' => $proposicao['ementa'],
                    'proposicao_apresentacao' => $proposicao['dataApresentacao'],
                    'proposicao_arquivada' => 0
                ];
            }

            $this->proposicaoModel->inserirProposicao($dados);

            return ['status' => 'success', 'message' => 'Proposições do ' . $ano . ' inseridas com sucesso.'];
        } catch (PDOException $e) {
            $this->logger->novoLog('proposicao_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }

    public function inserirProposicoesAutores($ano) {

        $proposicoesJson = $this->getjson->getJson('https://dadosabertos.camara.leg.br/arquivos/proposicoesAutores/json/proposicoesAutores-' . $ano . '.json');
        $dados = [];
        $contador = 0;

        try {
            foreach ($proposicoesJson['dados'] as $proposicao) {
                $dados[] = [
                    'proposicao_id' => $proposicao['idProposicao'],
                    'proposicao_autor_id' => (empty($proposicao['idDeputadoAutor'])) ? 0 : $proposicao['idDeputadoAutor'],
                    'proposicao_autor_nome' => $proposicao['nomeAutor'],
                    'proposicao_autor_partido' => (empty($proposicao['siglaPartidoAutor'])) ? '' : $proposicao['siglaPartidoAutor'],
                    'proposicao_autor_estado' => (empty($proposicao['siglaUFAutor'])) ? '' : $proposicao['siglaUFAutor'],
                    'proposicao_autor_proponente' => $proposicao['proponente'],
                    'proposicao_autor_assinatura' => $proposicao['ordemAssinatura'],
                    'proposicao_autor_ano' => $ano
                ];
                $contador++;
            }

            $this->proposicaoModel->inserirProposicaoAutor($dados);

            return ['status' => 'success', 'message' => $contador . ' proposições do ano de ' . $ano . ' inseridas com sucesso.'];
        } catch (PDOException $e) {
            $this->logger->novoLog('proposicao_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }

    public function proposicoesGabinete($itens, $pagina, $ordenarPor, $ordem, $tipo, $ano, $termo) {
        try {
            $proposicoes = $this->proposicaoModel->proposicoesGabinete($itens, $pagina, $ordenarPor, $ordem, $tipo, $ano, $termo);

            if (empty($proposicoes)) {
                return ['status' => 'empty',  'message' => 'Nenhuma proposição encontrada'];
            }

            $total = (isset($proposicoes[0]['total'])) ? $proposicoes[0]['total'] : 0;
            $totalPaginas = ceil($total / $itens);

            return ['status' => 'success', 'dados' => $proposicoes, 'total_paginas' => $totalPaginas];
        } catch (PDOException $e) {
            $this->logger->novoLog('proposicao_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }
}
