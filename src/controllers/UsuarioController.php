<?php

namespace Jairosantos\GabineteDigital\Controllers;

use Jairosantos\GabineteDigital\Core\UploadFile;
use Jairosantos\GabineteDigital\Models\Usuario;
use Jairosantos\GabineteDigital\Core\Logger;
use PDOException;

class UsuarioController {
    private $usuarioModel;
    private $uploadFile;
    private $pasta_foto;
    private $logger;
    private $usuario_nivel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
        $this->uploadFile = new UploadFile();
        $this->pasta_foto = 'arquivos/fotos_usuarios/';
        $this->logger = new Logger();
        $this->usuario_nivel = (isset($_SESSION['usuario_nivel'])) ? $_SESSION['usuario_nivel'] : 1;
    }

    public function criarUsuario($dados) {
        $camposObrigatorios = ['usuario_nome', 'usuario_email', 'usuario_telefone', 'usuario_senha', 'usuario_nivel', 'usuario_ativo', 'usuario_aniversario'];

        if ($this->usuario_nivel != 1) {
            return ['status' => 'forbidden', 'message' => 'Você não tem autorização para inserir novos usuários.'];
        }


        if (!filter_var($dados['usuario_email'], FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'invalid_email', 'message' => 'Email inválido.'];
        }

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        if (isset($dados['foto']['tmp_name']) && !empty($dados['foto']['tmp_name'])) {
            $uploadResult = $this->uploadFile->salvarArquivo($this->pasta_foto, $dados['foto']);

            if ($uploadResult['status'] == 'upload_ok') {
                $dados['usuario_foto'] = $this->pasta_foto . $uploadResult['filename'];
            } else {

                if ($uploadResult['status'] == 'error') {
                    return ['status' => 'error', 'message' => 'Erro ao fazer upload.'];
                }
            }
        }

        try {
            $this->usuarioModel->criar($dados);
            return ['status' => 'success', 'message' => 'Usuário inserido com sucesso.'];
        } catch (PDOException $e) {

            if (isset($dados['usuario_foto']) && !empty($dados['usuario_foto'])) {
                unlink($dados['usuario_foto']);
            }

            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O e-mail já está cadastrado.'];
            } else {
                $this->logger->novoLog('user_error', $e->getMessage());
                return ['status' => 'error', 'message' => 'Erro interno do servidor'];
            }
        }
    }

    public function listarUsuarios() {
        try {
            $usuarios = $this->usuarioModel->listar();

            if (empty($usuarios)) {
                return ['status' => 'empty',  'message' => 'Nenhum usuário registrado'];
            }

            return ['status' => 'success', 'dados' => $usuarios];
        } catch (PDOException $e) {
            $this->logger->novoLog('user_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }

    public function buscarUsuario($coluna, $valor) {
        try {
            $usuario = $this->usuarioModel->buscar($coluna, $valor);
            if ($usuario) {
                return ['status' => 'success', 'dados' => $usuario];
            } else {
                return ['status' => 'not_found', 'message' => 'Usuário não encontrado.'];
            }
        } catch (PDOException $e) {
            $this->logger->novoLog('user_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }

    public function atualizarUsuario($usuario_id, $dados) {

        if ($this->usuario_nivel != 1) {
            return ['status' => 'forbidden', 'message' => 'Você não tem autorização para atualizar usuários.'];
        }

        if (!filter_var($dados['usuario_email'], FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'invalid_email', 'message' => 'Email inválido.'];
        }


        if (isset($dados['foto']['tmp_name']) && !empty($dados['foto']['tmp_name'])) {
            $uploadResult = $this->uploadFile->salvarArquivo($this->pasta_foto, $dados['foto']);
            if ($uploadResult['status'] == 'upload_ok') {
                $dados['usuario_foto'] = $this->pasta_foto . $uploadResult['filename'];
            } else {
                if ($uploadResult['status'] == 'error') {
                    return ['status' => 'error', 'message' => 'Erro ao fazer upload.'];
                }
            }
        } else {
            $dados['usuario_foto'] = null;
        }

        try {
            $this->usuarioModel->atualizar($usuario_id, $dados);
            return ['status' => 'success', 'message' => 'Usuário atualizado com sucesso.'];
        } catch (PDOException $e) {
            $this->logger->novoLog('user_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }

    public function apagarUsuario($usuario_id) {
        try {

            if ($this->usuario_nivel != 1) {
                return ['status' => 'forbidden', 'message' => 'Você não tem autorização para apagar esse usuário.'];
            }

            $result = $this->buscarUsuario('usuario_id', $usuario_id);

            if ($result['dados'][0]['usuario_foto'] != null) {
                unlink($result['dados'][0]['usuario_foto']);
            }

            $this->usuarioModel->apagar($usuario_id);
            return ['status' => 'success', 'message' => 'Usuário apagado com sucesso.'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'error', 'message' => 'Erro: Não é possível apagar o usuário. Existem registros dependentes.'];
            }

            $this->logger->novoLog('user_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }
}
