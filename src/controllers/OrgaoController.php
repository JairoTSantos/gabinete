<?php

namespace Jairosantos\GabineteDigital\Controllers;

use Jairosantos\GabineteDigital\Models\Orgao;
use Jairosantos\GabineteDigital\Core\Logger;
use PDOException;

class OrgaoController {
    private $orgaoModel;
    private $logger;
    private $usuario_id;

    public function __construct() {
        $this->orgaoModel = new Orgao();
        $this->logger = new Logger();
        $this->usuario_id = (isset($_SESSION['usuario_id'])) ? $_SESSION['usuario_id'] : 1000;
    }

    public function criarOrgao($dados) {
        $camposObrigatorios = ['orgao_nome', 'orgao_endereco', 'orgao_municipio', 'orgao_estado', 'orgao_email', 'orgao_tipo'];

        if (!filter_var($dados['orgao_email'], FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'invalid_email', 'message' => 'Email inválido.'];
        }

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        $dados['orgao_criado_por'] = $this->usuario_id;

        try {
            $this->orgaoModel->criar($dados);
            return ['status' => 'success', 'message' => 'Órgão inserido com sucesso.'];
        } catch (PDOException $e) {

            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O órgão já está cadastrado.'];
            } else {
                $this->logger->novoLog('orgao_error', $e->getMessage());
                return ['status' => 'error', 'message' => 'Erro interno do servidor'];
            }
        }
    }

    public function listarOrgaos($itens, $pagina, $ordem, $ordenarPor, $termo, $filtro) {
        try {
            $result = $this->orgaoModel->listar($itens, $pagina, $ordem, $ordenarPor, $termo, $filtro);

            $total = (isset($result[0]['total'])) ? $result[0]['total'] : 0;
            $totalPaginas = ceil($total / $itens);

            if (empty($result)) {
                return ['status' => 'empty', 'message' => 'Nenhum órgão encontrado.'];
            }

            return ['status' => 'success', 'total_paginas' => $totalPaginas, 'dados' => $result];
        } catch (PDOException $e) {
            $this->logger->novoLog('orgao_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor.'];
        }
    }

    public function buscarOrgao($coluna, $valor) {
        try {
            $orgao = $this->orgaoModel->buscar($coluna, $valor);
            if ($orgao) {
                return ['status' => 'success', 'dados' => $orgao];
            } else {
                return ['status' => 'not_found', 'message' => 'Órgão não encontrado.'];
            }
        } catch (PDOException $e) {
            $this->logger->novoLog('orgao_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }

    public function atualizarOrgao($orgao_id, $dados) {
        if (!filter_var($dados['orgao_email'], FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'invalid_email', 'message' => 'Email inválido.'];
        }

        try {
            $this->orgaoModel->atualizar($orgao_id, $dados);
            return ['status' => 'success', 'message' => 'Órgão atualizado com sucesso.'];
        } catch (PDOException $e) {
            $this->logger->novoLog('orgao_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }

    public function apagarOrgao($orgao_id) {
        try {
            $result = $this->buscarOrgao('orgao_id', $orgao_id);

            if ($result['dados'][0]['orgao_foto'] != null) {
                unlink($result['dados'][0]['orgao_foto']);
            }

            $this->orgaoModel->apagar($orgao_id);
            return ['status' => 'success', 'message' => 'Órgão apagado com sucesso.'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'error', 'message' => 'Erro: Não é possível apagar o órgão. Existem registros dependentes.'];
            }

            $this->logger->novoLog('orgao_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }
}
