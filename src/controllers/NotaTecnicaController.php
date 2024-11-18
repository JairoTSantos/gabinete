<?php

namespace Jairosantos\GabineteDigital\Controllers;

use Jairosantos\GabineteDigital\Models\NotaTecnica;
use Jairosantos\GabineteDigital\Core\Logger;
use PDOException;

class NotaTecnicaController {
    private $notaTecnicaModel;
    private $logger;
    private $usuario_id;

    public function __construct() {
        $this->notaTecnicaModel = new NotaTecnica();
        $this->logger = new Logger();
        $this->usuario_id = (isset($_SESSION['usuario_id'])) ? $_SESSION['usuario_id'] : 1000;
    }

    public function criarNotaTecnica($dados) {
        $camposObrigatorios = ['nota_proposicao', 'nota_titulo', 'nota_resumo', 'nota_texto'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        $dados['nota_criada_por'] = $this->usuario_id;

        try {
            $this->notaTecnicaModel->criar($dados);
            return ['status' => 'success', 'message' => 'Nota técnica criada com sucesso.'];
        } catch (PDOException $e) {
            $this->logger->novoLog('nota_tecnica_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }

    public function listarNotasTecnicas() {
        try {
            $result = $this->notaTecnicaModel->listar();
            if (empty($result)) {
                return ['status' => 'empty', 'message' => 'Nenhuma nota técnica encontrada.'];
            }
            return ['status' => 'success', 'dados' => $result];
        } catch (PDOException $e) {
            $this->logger->novoLog('nota_tecnica_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }

    public function buscarNotaTecnica($coluna, $valor) {
        try {
            $nota = $this->notaTecnicaModel->buscar($coluna, $valor);
            if ($nota) {
                return ['status' => 'success', 'dados' => $nota];
            } else {
                return ['status' => 'not_found', 'message' => 'Nota técnica não encontrada.'];
            }
        } catch (PDOException $e) {
            $this->logger->novoLog('nota_tecnica_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }

    public function atualizarNotaTecnica($nota_id, $dados) {
        try {
            $this->notaTecnicaModel->atualizar($nota_id, $dados);
            return ['status' => 'success', 'message' => 'Nota técnica atualizada com sucesso.'];
        } catch (PDOException $e) {
            $this->logger->novoLog('nota_tecnica_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }

    public function apagarNotaTecnica($nota_id) {
        try {
            $this->notaTecnicaModel->apagar($nota_id);
            return ['status' => 'success', 'message' => 'Nota técnica apagada com sucesso.'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'error', 'message' => 'Erro: Não é possível apagar a nota técnica. Existem registros dependentes.'];
            }

            $this->logger->novoLog('nota_tecnica_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }
}