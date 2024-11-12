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

    public function Logar($dados) {
        try {
            $usuario = $this->usuarioModel->buscar('usuario_email', $dados['email']);
            if ($usuario) {
                if ($usuario[0]['usuario_ativo']) {
                    if (password_verify($dados['senha'], $usuario[0]['usuario_senha'])) {
                        $duracaoEmHoras = getenv('SESSION_TIME');
                        $duracaoEmSegundos = $duracaoEmHoras * 3600;

                        ini_set('session.cookie_secure', 1);
                        session_set_cookie_params([
                            'lifetime' => $duracaoEmSegundos,
                            'path' => '/',
                            'domain' => $_SERVER['HTTP_HOST'],
                            'secure' => true,
                            'httponly' => true,
                            'samesite' => 'Strict'
                        ]);
                        session_start();
                        $_SESSION['usuario_nome'] = $usuario[0]['usuario_nome'];
                        $_SESSION['usuario_nivel'] = $usuario[0]['usuario_nivel'];
                        $_SESSION['usuario_id'] = $usuario[0]['usuario_id'];
                    } else {
                        return ['status' => 'wrong_password', 'message' => 'Senha incorreta.'];
                    }
                    $this->logger->novoLog('access_log', ' | Login feito por ' . $usuario[0]['usuario_nome']);

                    return ['status' => 'success', 'message' => 'Login feito com sucesso.'];
                } else {
                    return ['status' => 'deactived', 'message' => 'Usuário desativado.'];
                }
            } else {
                return ['status' => 'not_found', 'message' => 'Usuário não encontrado.'];
            }
        } catch (PDOException $e) {
            $this->logger->novoLog('login_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }
}
