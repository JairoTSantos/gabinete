<?php


include '../src/views/includes/verificaLogado.php';

require_once '../vendor/autoload.php';


use Jairosantos\GabineteDigital\Controllers\OrgaoController;
use Jairosantos\GabineteDigital\Controllers\OficioController;

$orgaoController = new OrgaoController();
$oficioController = new OficioController();

$ano_busca = (isset($_GET['busca_ano'])) ? $_GET['busca_ano'] : date('Y');
$termo = (isset($_GET['termo'])) ? $_GET['termo'] : '';


?>


<div class="d-flex" id="wrapper">
    <?php include '../src/views/includes/sider_bar.php'; ?>
    <div id="page-content-wrapper">
        <?php include '../src/views/includes/top_menu.php'; ?>
        <div class="container-fluid p-2">
            <div class="card mb-2">
                <div class="card-body p-1">
                    <a class="btn btn-primary btn-sm custom-nav card-description" href="?secao=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                </div>
            </div>
            <div class="card mb-2 card-description">
                <div class="card-header bg-primary text-white px-2 py-1 card-background"><i class="bi bi-file-earmark-text"></i> Ofícios</div>
                <div class="card-body p-2">
                    <p class="card-text mb-2">Nesta seção, é possível adicionar e arquivar ofícios do sistema.</p>
                    <p class="card-text mb-0">Todos os campos são obrigatórios. Somente arquivos <b>PDF</b></p>
                </div>
            </div>

            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_salvar'])) {

                        $dados = [
                            'oficio_titulo' => htmlspecialchars($_POST['oficio_titulo'], ENT_QUOTES, 'UTF-8'),
                            'oficio_ano' => htmlspecialchars($_POST['oficio_ano'], ENT_QUOTES, 'UTF-8'),
                            'oficio_resumo' => htmlspecialchars($_POST['oficio_resumo'], ENT_QUOTES, 'UTF-8'),
                            'oficio_orgao' => htmlspecialchars($_POST['oficio_orgao'], ENT_QUOTES, 'UTF-8'),
                            'arquivo' => $_FILES['arquivo']
                        ];


                        if (isset($dados['arquivo']) && $dados['arquivo']['error'] === 0) {
                            $extensao = pathinfo($dados['arquivo']['name'], PATHINFO_EXTENSION);
                            if (strtolower($extensao) !== 'pdf') {
                                echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">Por favor, envie um arquivo no formato PDF.</div>';
                            } else {
                                $result = $oficioController->criarOficio($dados);
                                if ($result['status'] == 'success') {
                                    echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '. Aguarde...</div>';
                                } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request') {
                                    echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                                } else if ($result['status'] == 'error') {
                                    echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                                }
                            }
                        }
                    }
                    ?>

                    <form class="row g-2 form_custom" method="POST" enctype="multipart/form-data">
                        <div class="col-md-1 col-12">
                            <input type="text" class="form-control form-control-sm" name="oficio_titulo" placeholder="Número" data-mask="OF 000" required>
                        </div>
                        <div class="col-md-1 col-12">
                            <input type="text" class="form-control form-control-sm" name="oficio_ano" placeholder="Ano" data-mask="0000" required>
                        </div>
                        <div class="col-md-3 col-12">
                            <input type="text" class="form-control form-control-sm" name="oficio_resumo" placeholder="Resumo" required>
                        </div>
                        <div class="col-md-2 col-12">
                            <select class="form-select form-select-sm" id="orgao" name="oficio_orgao">
                                <option value="1000" selected>Órgão não informado</option>
                                <?php

                                $buscaOrgao = $orgaoController->listarOrgaos(1000, 1, 'ASC', 'orgao_nome', null, false);

                                if ($buscaOrgao['status'] === 'success') {
                                    foreach ($buscaOrgao['dados'] as $orgao) {
                                        echo '<option value="' . $orgao['orgao_id'] . '">' . $orgao['orgao_nome'] . '</option>';
                                    }
                                }
                                ?>

                                <option value="+">Novo órgão + </option>
                            </select>
                        </div>
                        <div class="col-md-3 col-12">
                            <input type="file" class="form-control form-control-sm" name="arquivo" required />
                        </div>
                        <div class="col-md-5 col-12">
                            <button type="submit" class="btn btn-success btn-sm" name="btn_salvar"><i class="bi bi-floppy-fill"></i> Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row ">
                <div class="col-12">
                    <div class="card shadow-sm mb-2">
                        <div class="card-body p-2">
                            <form class="row g-2 form_custom mb-0" method="GET" enctype="application/x-www-form-urlencoded">
                                <div class="col-md-1 col-3">
                                    <input type="hidden" name="secao" value="oficios" />
                                    <input type="text" class="form-control form-control-sm" name="busca_ano" placeholder="Ano" value="<?php echo $ano_busca ?>" data-mask="0000">
                                </div>
                                <div class="col-md-3 col-7">
                                    <input type="text" class="form-control form-control-sm" name="termo" value="<?php echo $termo ?>" placeholder="Buscar...">
                                </div>
                                <div class="col-md-1 col-2">
                                    <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-search"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped mb-0 custom-table">
                            <thead>
                                <tr>
                                    <th scope="col">Número</th>
                                    <th scope="col">Resumo</th>
                                    <th scope="col">Resumo</th>
                                    <th scope="col">Arquivado por</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $busca = $oficioController->listarOficios($ano_busca, $termo);

                                if ($busca['status'] == 'success') {
                                    foreach ($busca['dados'] as $oficio) {
                                        echo '<tr>';
                                        echo '<td style="white-space: nowrap;"><a href="?secao=oficio&id=' . $oficio['oficio_id'] . '">' . $oficio['oficio_titulo'] . '/' . $oficio['oficio_ano'] . '</a></td>';
                                        echo '<td style="white-space: nowrap;">' . $oficio['oficio_resumo'] . '</td>';
                                        echo '<td style="white-space: nowrap;">' . $oficio['orgao_nome'] . '</td>';
                                        echo '<td style="white-space: nowrap;">' . $oficio['usuario_nome'] . ' - ' . date('d/m', strtotime($oficio['oficio_criado_em'])) . '</td>';
                                        echo '</tr>';
                                    }
                                } else if ($busca['status'] == 'empty') {
                                    echo '<tr><td colspan="4">' . $busca['message'] . '</td></tr>';
                                } else if ($busca['status'] == 'error') {
                                    echo '<tr><td colspan="4">Erro ao carregar os dados.</td></tr>';
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
<script>
    $('#orgao').change(function() {
        if ($('#orgao').val() == '+') {
            if (window.confirm("Você realmente deseja inserir um novo órgão?")) {
                window.location.href = "?secao=orgaos";
            } else {
                $('#orgao').val(1000).change();
            }
        }
    });
</script>