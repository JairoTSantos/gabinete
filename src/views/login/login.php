<link href="css/login.css" rel="stylesheet">
<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="centralizada text-center">
        <img src="public/img/logo_white.png" alt="" class="img_logo" />
        <h2 class="login_title">Gabinete Digital</h2>
        <h6 class="host"><?php echo $_SERVER['HTTP_HOST'] ?></h6>
        <?php

        require_once '../vendor/autoload.php';

        use Jairosantos\GabineteDigital\Controllers\LoginController;

        $loginController = new LoginController();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_logar'])) {


            $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
            $senha = htmlspecialchars($_POST['senha'], ENT_QUOTES, 'UTF-8');


            $resultado = $loginController->Logar($email, $senha);

            if ($resultado['status'] == 'success') {
                echo '<div class="alert alert-success px-2 py-1 mb-2  rounded-5 custom-alert" data-timeout="3" role="alert">' . $resultado['message'] . '. Aguarde...</div>';
                echo '<script>
                setTimeout(function(){
                    window.location.href = "?secao=home";
                }, 1000);</script>';
            } else if ($resultado['status'] == 'not_found' || $resultado['status'] == 'deactivated') {
                echo '<div class="alert alert-info px-2 py-1 mb-2  rounded-5 custom-alert" data-timeout="3" role="alert">' . $resultado['message'] . '</div>';
            } else if ($resultado['status'] == 'wrong_password' || $resultado['status'] == 'error' || $resultado['status'] == 'deactived') {
                echo '<div class="alert alert-danger px-2 rounded-5 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $resultado['message'] . '</div>';
            }
        }
        ?>

        <form id="form_login" method="post" enctype="application/x-www-form-urlencoded" class="form-group">
            <div class="form-group">
                <input type="email" class="form-control" name="email" id="email" placeholder="E-mail" value="jairojeffersont@gmail.com" required>
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="senha" id="senha" placeholder="Senha" value="intell01" required>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <button type="submit" name="btn_logar" class="btn">Entrar</button>
            </div>
        </form>
        <p class="mt-3 link">Esqueceu a senha? | <a href="?secao=cadastro">Faça seu cadastro</a></p>
        <p class="mt-3 copyright"><?php echo date('Y') ?> | JS Digital System</p>
    </div>

</div>