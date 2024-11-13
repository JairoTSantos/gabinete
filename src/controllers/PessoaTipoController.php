<?php

namespace Jairosantos\GabineteDigital\Controllers;

use Jairosantos\GabineteDigital\Models\PessoaTipo;
use Jairosantos\GabineteDigital\Core\Logger;
use PDOException;

class PessoaTipoController {
    private $pessoaTipoModel;
    private $logger;

    public function __construct() {
        $this->pessoaTipoModel = new PessoaTipo();
        $this->logger = new Logger();
    }

    public function criarPessoaTipo($dados) {
        $camposObrigatorios = ['pessoa_tipo_nome', 'pessoa_tipo_descricao'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo]) || empty($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        try {
            $this->pessoaTipoModel->criar($dados);
            return ['status' => 'success', 'message' => 'Tipo de pessoa criado com sucesso.'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O tipo de pessoa já está cadastrado.'];
            } else {
                $this->logger->novoLog('pessoa_tipo_error', $e->getMessage());
                return ['status' => 'error', 'message' => 'Erro interno do servidor'];
            }
        }
    }

    public function listarPessoasTipos() {
        try {
            $pessoasTipos = $this->pessoaTipoModel->listar();

            if (empty($pessoasTipos)) {
                return ['status' => 'empty', 'message' => 'Nenhum tipo de pessoa registrado.'];
            }

            return ['status' => 'success', 'dados' => $pessoasTipos];
        } catch (PDOException $e) {
            $this->logger->novoLog('pessoa_tipo_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }

    public function buscarPessoaTipo($coluna, $valor) {
        try {
            $pessoaTipo = $this->pessoaTipoModel->buscar($coluna, $valor);
            if ($pessoaTipo) {
                return ['status' => 'success', 'dados' => $pessoaTipo];
            } else {
                return ['status' => 'not_found', 'message' => 'Tipo de pessoa não encontrado.'];
            }
        } catch (PDOException $e) {
            $this->logger->novoLog('pessoa_tipo_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }

    public function atualizarPessoaTipo($pessoa_tipo_id, $dados) {
        $camposObrigatorios = ['pessoa_tipo_nome', 'pessoa_tipo_descricao'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo]) || empty($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        try {
            $this->pessoaTipoModel->atualizar($pessoa_tipo_id, $dados);
            return ['status' => 'success', 'message' => 'Tipo de pessoa atualizado com sucesso.'];
        } catch (PDOException $e) {
            $this->logger->novoLog('pessoa_tipo_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }

    public function apagarPessoaTipo($pessoa_tipo_id) {
        try {
            $result = $this->buscarPessoaTipo('pessoa_tipo_id', $pessoa_tipo_id);

            if ($result['status'] === 'not_found') {
                return ['status' => 'not_found', 'message' => 'Tipo de pessoa não encontrado.'];
            }

            $this->pessoaTipoModel->apagar($pessoa_tipo_id);
            return ['status' => 'success', 'message' => 'Tipo de pessoa apagado com sucesso.'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'error', 'message' => 'Erro: Não é possível apagar o tipo de pessoa. Existem registros dependentes.'];
            }

            $this->logger->novoLog('pessoa_tipo_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }
}
