<?php

namespace Jairosantos\GabineteDigital\Controllers;

use Jairosantos\GabineteDigital\Models\OrgaoTipo;
use Jairosantos\GabineteDigital\Core\Logger;
use PDOException;

class OrgaoTipoController {
    private $orgaoTipoModel;
    private $logger;
    private $usuario_id;

    public function __construct() {
        $this->orgaoTipoModel = new OrgaoTipo();
        $this->logger = new Logger();
        $this->usuario_id = $_SESSION['usuario_id'];
    }

    public function criarOrgaoTipo($dados) {
        $camposObrigatorios = ['orgao_tipo_nome', 'orgao_tipo_descricao'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo]) || empty($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        try {

            $dados['orgao_tipo_criador_por'] = $this->usuario_id;

            $this->orgaoTipoModel->criar($dados);
            return ['status' => 'success', 'message' => 'Tipo de órgão criado com sucesso.'];
        } catch (PDOException $e) {

            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O tipo já está cadastrado.'];
            } else {
                $this->logger->novoLog('orgao_tipo_error', $e->getMessage());
                return ['status' => 'error', 'message' => 'Erro interno do servidor'];
            }
        }
    }

    public function listarOrgaosTipos() {
        try {
            $orgaosTipos = $this->orgaoTipoModel->listar();

            if (empty($orgaosTipos)) {
                return ['status' => 'empty', 'message' => 'Nenhum tipo de órgão registrado.'];
            }

            return ['status' => 'success', 'dados' => $orgaosTipos];
        } catch (PDOException $e) {
            $this->logger->novoLog('orgao_tipo_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }

    public function buscarOrgaoTipo($coluna, $valor) {
        try {
            $orgaoTipo = $this->orgaoTipoModel->buscar($coluna, $valor);
            if ($orgaoTipo) {
                return ['status' => 'success', 'dados' => $orgaoTipo];
            } else {
                return ['status' => 'not_found', 'message' => 'Tipo de órgão não encontrado.'];
            }
        } catch (PDOException $e) {
            $this->logger->novoLog('orgao_tipo_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }

    public function atualizarOrgaoTipo($orgao_tipo_id, $dados) {
        $camposObrigatorios = ['orgao_tipo_nome', 'orgao_tipo_descricao'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo]) || empty($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        try {
            $this->orgaoTipoModel->atualizar($orgao_tipo_id, $dados);
            return ['status' => 'success', 'message' => 'Tipo de órgão atualizado com sucesso.'];
        } catch (PDOException $e) {
            $this->logger->novoLog('orgao_tipo_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }

    public function apagarOrgaoTipo($orgao_tipo_id) {
        try {
            $result = $this->buscarOrgaoTipo('orgao_tipo_id', $orgao_tipo_id);

            if ($result['status'] === 'not_found') {
                return ['status' => 'not_found', 'message' => 'Tipo de órgão não encontrado.'];
            }

            $this->orgaoTipoModel->apagar($orgao_tipo_id);
            return ['status' => 'success', 'message' => 'Tipo de órgão apagado com sucesso.'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'error', 'message' => 'Erro: Não é possível apagar o tipo de órgão. Existem registros dependentes.'];
            }

            $this->logger->novoLog('orgao_tipo_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }
}
