<?php

namespace Jairosantos\GabineteDigital\Controllers;

use Jairosantos\GabineteDigital\Models\Profissao;
use Jairosantos\GabineteDigital\Core\Logger;
use PDOException;

class ProfissaoController {
    private $pessoaProfissaoModel;
    private $logger;

    public function __construct() {
        $this->pessoaProfissaoModel = new Profissao();
        $this->logger = new Logger();
    }

    public function criarPessoaProfissao($dados) {
        $camposObrigatorios = ['pessoas_profissoes_nome', 'pessoas_profissoes_descricao'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo]) || empty($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        try {
            $this->pessoaProfissaoModel->criar($dados);
            return ['status' => 'success', 'message' => 'Profissão criada com sucesso.'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'A profissão já está cadastrada.'];
            } else {
                $this->logger->novoLog('pessoa_profissao_error', $e->getMessage());
                return ['status' => 'error', 'message' => 'Erro interno do servidor'];
            }
        }
    }

    public function listarPessoasProfissoes() {
        try {
            $profissoes = $this->pessoaProfissaoModel->listar();

            if (empty($profissoes)) {
                return ['status' => 'empty', 'message' => 'Nenhuma profissão registrada.'];
            }

            return ['status' => 'success', 'dados' => $profissoes];
        } catch (PDOException $e) {
            $this->logger->novoLog('pessoa_profissao_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }

    public function buscarPessoaProfissao($coluna, $valor) {
        try {
            $profissao = $this->pessoaProfissaoModel->buscar($coluna, $valor);
            if ($profissao) {
                return ['status' => 'success', 'dados' => $profissao];
            } else {
                return ['status' => 'not_found', 'message' => 'Profissão não encontrada.'];
            }
        } catch (PDOException $e) {
            $this->logger->novoLog('pessoa_profissao_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }

    public function atualizarPessoaProfissao($pessoas_profissoes_id, $dados) {
        $camposObrigatorios = ['pessoas_profissoes_nome', 'pessoas_profissoes_descricao'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo]) || empty($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        try {
            $this->pessoaProfissaoModel->atualizar($pessoas_profissoes_id, $dados);
            return ['status' => 'success', 'message' => 'Profissão atualizada com sucesso.'];
        } catch (PDOException $e) {
            $this->logger->novoLog('pessoa_profissao_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }

    public function apagarPessoaProfissao($pessoas_profissoes_id) {
        try {
            $result = $this->buscarPessoaProfissao('pessoas_profissoes_id', $pessoas_profissoes_id);

            if ($result['status'] === 'not_found') {
                return ['status' => 'not_found', 'message' => 'Profissão não encontrada.'];
            }

            $this->pessoaProfissaoModel->apagar($pessoas_profissoes_id);
            return ['status' => 'success', 'message' => 'Profissão apagada com sucesso.'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'error', 'message' => 'Erro: Não é possível apagar a profissão. Existem registros dependentes.'];
            }

            $this->logger->novoLog('pessoa_profissao_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }
}
