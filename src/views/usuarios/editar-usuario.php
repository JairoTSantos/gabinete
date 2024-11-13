<?php

include '../src/views/includes/verificaLogado.php';

require_once '../vendor/autoload.php';

use Jairosantos\GabineteDigital\Controllers\UsuarioController;

$usuarioController = new UsuarioController();

$usuarioGet = $_GET['id'];

$buscaUsuario = $usuarioController->buscarUsuario('usuario_id', $usuarioGet);

if ($buscaUsuario['status'] == 'not_found' || is_integer($usuarioGet) || $buscaUsuario['status'] == 'error') {
    header('Location: ?secao=usuarios');
}

?>

<div class="d-flex" id="wrapper">
    <?php include '../src/views/includes/sider_bar.php'; ?>
    <div id="page-content-wrapper">
        <?php include '../src/views/includes/top_menu.php'; ?>
        <div class="container-fluid p-2">
            <div class="card mb-2 ">
                <div class="card-body p-1">
                    <a class="btn btn-primary btn-sm custom-nav card-description" href="?secao=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                    <a class="btn btn-success btn-sm custom-nav card-description" href="?secao=usuarios" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow-sm mb-2 card-background">
                        <div class="card-body p-2">
                            <div class="row">
                                <div class="col-12 col-md-1">
                                    <?php
                                    if (isset($buscaUsuario['dados'][0]['usuario_foto'])) {
                                        echo '<img src="' . $buscaUsuario['dados'][0]['usuario_foto'] . '" class="img-thumbnail img-crop" alt="...">';
                                    } else {
                                        echo '<img src="public/img/not_found.jpg" class="img-thumbnail img-crop" alt="...">';
                                    }
                                    ?>
                                </div>
                                <div class="col-12 col-md-11 mt-2 ">
                                    <h5 class="card-title"><?php echo $buscaUsuario['dados'][0]['usuario_nome']  ?></h5>
                                    <p class="card-text mb-1"><i class="bi bi-envelope-fill"></i> <?php echo $buscaUsuario['dados'][0]['usuario_email']  ?></p>
                                    <p class="card-text mb-1"><i class="bi bi-phone-fill"></i> <?php echo $buscaUsuario['dados'][0]['usuario_telefone']  ?></p>
                                    <p class="card-text mb-1"><i class="bi bi-cake-fill"></i> <?php echo $buscaUsuario['dados'][0]['usuario_aniversario']  ?></p>
                                    <p class="card-text mb-1"><?php echo ($buscaUsuario['dados'][0]['usuario_nivel']) ? 'Administrador' : 'Assessor'  ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_atualizar'])) {
                        $usuario = [
                            'usuario_nome' => htmlspecialchars($_POST['usuario_nome'], ENT_QUOTES, 'UTF-8'),
                            'usuario_email' => htmlspecialchars($_POST['usuario_email'], ENT_QUOTES, 'UTF-8'),
                            'usuario_telefone' => htmlspecialchars($_POST['usuario_telefone'], ENT_QUOTES, 'UTF-8'),
                            'usuario_aniversario' => htmlspecialchars($_POST['usuario_aniversario'], ENT_QUOTES, 'UTF-8'),
                            'usuario_ativo' => htmlspecialchars($_POST['usuario_ativo'], ENT_QUOTES, 'UTF-8'),
                            'usuario_nivel' => htmlspecialchars($_POST['usuario_nivel'], ENT_QUOTES, 'UTF-8'),
                            'foto' => $_FILES['foto']
                        ];

                        $result = $usuarioController->atualizarUsuario($usuarioGet, $usuario);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '. Aguarde...</div>';
                            echo '<script>
                            setTimeout(function(){
                                window.location.href = "?secao=usuario&id=' . $usuarioGet . '";
                            }, 1000);</script>';
                        } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request' || $result['status'] == 'invalid_email') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error' || $result['status'] == 'forbidden') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . '</div>';
                        }
                    }

                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_apagar'])) {
                        $result = $usuarioController->apagarUsuario($usuarioGet);
                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '. Aguarde...</div>';
                            echo '<script>
                                setTimeout(function(){
                                    window.location.href = "?secao=usuarios";
                                }, 1000);</script>';
                        } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request' || $result['status'] == 'invalid_email') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . '</div>';
                        }
                    }

                    ?>

                    <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
                        <div class="col-md-6 col-12">
                            <input type="text" class="form-control form-control-sm" name="usuario_nome" placeholder="Nome" value="<?php echo $buscaUsuario['dados'][0]['usuario_nome'] ?>" required>
                        </div>
                        <div class="col-md-2 col-12">
                            <input type="email" class="form-control form-control-sm" name="usuario_email" placeholder="Email" value="<?php echo $buscaUsuario['dados'][0]['usuario_email'] ?>" required>
                        </div>
                        <div class="col-md-2 col-6">
                            <input type="text" class="form-control form-control-sm" name="usuario_telefone" placeholder="Celular (com DDD)" data-mask="(00) 00000-0000" maxlength="11" value="<?php echo $buscaUsuario['dados'][0]['usuario_telefone'] ?>" required>
                        </div>
                        <div class="col-md-2 col-6">
                            <input type="text" class="form-control form-control-sm" name="usuario_aniversario" data-mask="00/00" placeholder="Aniversário (dd/mm)" value="<?php echo $buscaUsuario['dados'][0]['usuario_aniversario'] ?>" required>
                        </div>
                        <div class="col-md-1 col-6">
                            <select class="form-select form-select-sm" name="usuario_ativo" required>
                                <option value="1" <?= $buscaUsuario['dados'][0]['usuario_ativo'] == 1 ? 'selected' : '' ?>>Ativado</option>
                                <option value="0" <?= $buscaUsuario['dados'][0]['usuario_ativo'] == 0 ? 'selected' : '' ?>>Desativado</option>
                            </select>
                        </div>
                        <div class="col-md-1 col-6">
                            <select class="form-select form-select-sm" name="usuario_nivel" required>
                                <option value="1" <?= $buscaUsuario['dados'][0]['usuario_nivel'] == 1 ? 'selected' : '' ?>>Administrador</option>
                                <option value="2" <?= $buscaUsuario['dados'][0]['usuario_nivel'] == 2 ? 'selected' : '' ?>>Assessor</option>
                            </select>
                        </div>
                        <div class="col-md-3 col-12">
                            <div class="file-upload">
                                <input type="file" id="file-input" name="foto" style="display: none;" />
                                <button id="file-button" type="button" class="btn btn-primary btn-sm"><i class="bi bi-camera-fill"></i> Escolher Foto</button>
                                <button type="submit" class="btn btn-success btn-sm" name="btn_atualizar"><i class="bi bi-floppy-fill"></i> Atualizar</button>
                                <button type="submit" class="btn btn-danger btn-sm" name="btn_apagar"><i class="bi bi-trash-fill"></i> Apagar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped mb-0 custom-table">
                            <thead>
                                <tr>
                                    <th scope="col">Últimos acessos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $pastaLogs = '../logs';
                                if (is_dir($pastaLogs)) {
                                    $arquivosLog = glob($pastaLogs . '/*_login_access.log');
                                    $linhasFiltradas = [];
                                    foreach ($arquivosLog as $arquivoLog) {
                                        $linhas = file($arquivoLog, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                                        $linhasFiltradas = array_merge($linhasFiltradas, array_filter($linhas, function ($linha) {
                                            return strpos($linha, $_SESSION['usuario_nome']) !== false;
                                        }));
                                    }
                                    rsort($linhasFiltradas);
                                    if (!empty($linhasFiltradas)) {
                                        foreach ($linhasFiltradas as $linha) {
                                            list($dataHora, $usuario) = explode(' - ', $linha, 2);
                                            echo "<tr><td>" . date('d/m \à\s H:i', strtotime($dataHora)) . "</td></tr>";
                                        }
                                    } else {
                                        echo "<tr><td>Nenhum registro encontrado.<td></tr>";
                                    }
                                } else {
                                    echo "<tr><td>Pasta de logs não encontrada.<td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>