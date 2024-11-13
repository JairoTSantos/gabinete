<?php

namespace Jairosantos\GabineteDigital\Controllers;

use Jairosantos\GabineteDigital\Models\Usuario;
use Jairosantos\GabineteDigital\Core\Logger;
use PDOException;
use Dotenv\Dotenv;

class LoginController {
    private $usuarioModel;
    private $logger;

    public function __construct() {
        $this->usuarioModel = new Usuario();
        $this->logger = new Logger();

        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
    }

    public function Logar($email, $senha) {
        try {
            $result = $this->usuarioModel->buscar('usuario_email', $email);

            if ($_ENV['MASTER_EMAIL'] == $email && $_ENV['MASTER_PASS'] == $senha) {
                session_start();
                $_SESSION['usuario_id'] = 10000;
                $_SESSION['usuario_nome'] = $_ENV['MASTER_USER'];
                $_SESSION['usuario_nivel'] = 1;
                $_SESSION['usuario_foto'] = null;
                $this->logger->novoLog('login_access', ' - ' . $_ENV['MASTER_USER']);
                return ['status' => 'success', 'message' => 'Usuário verificado com sucesso.'];
                exit;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return ['status' => 'invalid_email', 'message' => 'Email inválido.'];
                exit;
            }

            if (empty($result)) {
                return ['status' => 'not_found', 'message' => 'Usuário não encontrado.'];
                exit;
            }

            if (!$result[0]['usuario_ativo']) {
                return ['status' => 'deactivated', 'message' => 'Usuário desativado.'];
                exit;
            }

            if (password_verify($senha, $result[0]['usuario_senha'])) {
                session_start();
                $_SESSION['usuario_id'] = $result[0]['usuario_id'];
                $_SESSION['usuario_nome'] = $result[0]['usuario_nome'];
                $_SESSION['usuario_nivel'] = $result[0]['usuario_nivel'];
                $_SESSION['usuario_foto'] = $result[0]['usuario_foto'];
                $this->logger->novoLog('login_access', ' - ' . $result[0]['usuario_nome']);
                return ['status' => 'success', 'message' => 'Usuário verificado com sucesso.'];
                exit;
            } else {
                return ['status' => 'wrong_password', 'message' => 'Senha incorreta.'];
                exit;
            }
        } catch (PDOException $e) {
            $this->logger->novoLog('login_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor.'];
            exit;
        }
    }
}
