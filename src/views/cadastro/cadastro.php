<link href="css/cadastro.css" rel="stylesheet">
<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="centralizada text-center">

        <img src="public/img/logo_white.png" alt="" class="img_logo" />
        <h2 class="login_title">Gabinete Digital</h2>
        <h6 class="host"><?php echo $_SERVER['HTTP_HOST'] ?></h6>
        <?php

        require_once '../vendor/autoload.php';

        use Jairosantos\GabineteDigital\Controllers\UsuarioController;
use Jairosantos\GabineteDigital\Models\Usuario;

        $usuarioController = new UsuarioController();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_login'])) {
            if ($_POST['usuario_senha'] !== $_POST['usuario_senha2']) {
                echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">As senha não conferem</div>';
            } elseif (strlen($_POST['usuario_senha']) < 6) {
                echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">A senha tem menos de 6 caracteres</div>';
            } else {
                $usuario = [
                    'usuario_nome' => htmlspecialchars($_POST['usuario_nome'], ENT_QUOTES, 'UTF-8'),
                    'usuario_email' => htmlspecialchars($_POST['usuario_email'], ENT_QUOTES, 'UTF-8'),
                    'usuario_telefone' => htmlspecialchars($_POST['usuario_telefone'], ENT_QUOTES, 'UTF-8'),
                    'usuario_aniversario' => htmlspecialchars($_POST['usuario_aniversario'], ENT_QUOTES, 'UTF-8'),
                    'usuario_ativo' => 0,
                    'usuario_nivel' => 2,
                    'usuario_senha' => $_POST['usuario_senha'],
                    'foto' => (isset($_FILES['foto'])) ? $_FILES['foto'] : null
                ];

                $result = $usuarioController->criarUsuario($usuario);

                if ($result['status'] == 'success') {
                    echo '<div class="alert alert-success rounded-5 px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request' || $result['status'] == 'invalid_email') {
                    echo '<div class="alert alert-info px-2 rounded-5 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                } else if ($result['status'] == 'invalid_email' || $result['status'] == 'error' || $result['status'] == 'forbidden') {
                    echo '<div class="alert alert-danger rounded-5 px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . '</div>';
                }
            }
        }

        ?>
        <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
            <div class="col-md-12 col-12">
                <input type="text" class="form-control form-control-sm" name="usuario_nome" placeholder="Nome" required>
            </div>
            <div class="col-md-12 col-12">
                <input type="email" class="form-control form-control-sm" name="usuario_email" placeholder="Email" required>
            </div>
            <div class="col-md-6 col-6">
                <input type="text" class="form-control form-control-sm" name="usuario_telefone" placeholder="Celular (com DDD)" data-mask="(00) 00000-0000" maxlength="11" required>
            </div>
            <div class="col-md-6 col-6">
                <input type="text" class="form-control form-control-sm" name="usuario_aniversario" placeholder="dd/mm" data-mask="00/00" required>
            </div>
            <div class="col-md-6 col-6">
                <input type="password" class="form-control form-control-sm" name="usuario_senha" placeholder="Senha" required>
            </div>
            <div class="col-md-6 col-6">
                <input type="password" class="form-control form-control-sm" name="usuario_senha2" placeholder="Confirme a senha" required>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <button type="submit" name="btn_login" class="btn btn-primary">Salvar</button>
                <a type="button" href="?secao=login" class="btn btn-secondary">Voltar</a>

            </div>
        </form>
        <p class="mt-3 copyright">2024 | JS Digital System</p>
    </div>
</div>