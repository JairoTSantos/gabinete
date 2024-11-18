<?php

include '../src/views/includes/verificaLogado.php';

require_once '../vendor/autoload.php';

use Jairosantos\GabineteDigital\Controllers\NotaTecnicaController;

$notaTecnicaController = new NotaTecnicaController;

$proposicaoGet = isset($_GET['proposicao']) ? $_GET['proposicao'] : null;

if (!$proposicaoGet) {
    header('Location: ?secao=home');
    exit; // Adiciona o `exit` para garantir que o script pare após o redirecionamento
}


?>
<div class="d-flex" id="wrapper">
    <?php include '../src/views/includes/sider_bar.php'; ?>
    <div id="page-content-wrapper">
        <?php include '../src/views/includes/top_menu.php'; ?>
        <div class="container-fluid p-2">
            <div class="card mb-2">
                <div class="card-body p-1">
                    <a class="btn btn-primary btn-sm custom-nav card-description" href="?pagina=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                </div>
            </div>
            <div class="card mb-2 card-description">
                <div class="card-header bg-primary text-white px-2 py-1 card-background"><i class="bi bi-file-earmark-text"></i> Adicionar Nota Técnica</div>
                <div class="card-body p-2">
                    <p class="card-text mb-2">Nesta seção, é possível adicionar e gerenciar notas técnicas para documentação e controle.</p>
                    <p class="card-text mb-0">Todos os campos são obrigatórios</p>
                </div>
            </div>
            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_salvar'])) {
                        $dados = [
                            'nota_proposicao' => $proposicaoGet,
                            'nota_titulo' => htmlspecialchars($_POST['nota_titulo'], ENT_QUOTES, 'UTF-8'),
                            'nota_resumo' => htmlspecialchars($_POST['nota_resumo'], ENT_QUOTES, 'UTF-8'),
                            'nota_texto' => htmlspecialchars($_POST['nota_texto'], ENT_QUOTES, 'UTF-8')
                        ];

                        $result = $notaTecnicaController->criarNotaTecnica($dados);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'bad_request') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        }
                    }
                    ?>
                    <form class="row g-2 form_custom" id="form_nova_nota" method="POST">

                        <div class="col-md-6 col-12">
                            <input type="text" class="form-control form-control-sm" name="nota_titulo" placeholder="Título" required>
                        </div>
                        <div class="col-md-6 col-12">
                            <input type="text" class="form-control form-control-sm" name="nota_resumo" placeholder="Resumo" required>
                        </div>
                        <div class="col-md-12 col-12">
                            <textarea class="form-control form-control-sm" name="nota_texto" placeholder="Texto" rows="10" required></textarea>
                        </div>
                        <div class="col-md-1 col-12">
                            <button type="submit" class="btn btn-success btn-sm" name="btn_salvar"><i class="bi bi-floppy-fill"></i> Salvar</button>
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
                                    <th scope="col">Título</th>
                                    <th scope="col">Resumo</th>
                                    <th scope="col">Criado por - em</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $busca = $notaTecnicaController->listarNotasTecnicas();
                                if ($busca['status'] == 'success') {
                                    foreach ($busca['dados'] as $notaTecnica) {
                                        echo '<tr>';
                                        echo '<td style="white-space: nowrap;"><a href="?secao=nota-tecnica&id=' . $notaTecnica['nota_id'] . '">' . $notaTecnica['nota_titulo'] . '</a></td>';
                                        echo '<td style="white-space: nowrap;">' . $notaTecnica['nota_resumo'] . '</td>';
                                        echo '<td style="white-space: nowrap;">' . $notaTecnica['usuario_nome'] . ' - ' . date('d/m', strtotime($notaTecnica['nota_criada_em'])) . '</td>';
                                        echo '</tr>';
                                    }
                                } else if ($busca['status'] == 'empty') {
                                    echo '<tr><td colspan="3">' . $busca['message'] . '</td></tr>';
                                } else if ($busca['status'] == 'error') {
                                    echo '<tr><td colspan="3">Erro ao carregar os dados.</td></tr>';
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