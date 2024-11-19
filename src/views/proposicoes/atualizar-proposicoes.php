<?php

include '../src/views/includes/verificaLogado.php';

require_once '../vendor/autoload.php';


use Jairosantos\GabineteDigital\Controllers\ProposicaoController;

$proposicaoController = new ProposicaoController();

?>

<div class="d-flex" id="wrapper">
    <?php include '../src/views/includes/sider_bar.php'; ?>
    <div id="page-content-wrapper">
        <?php include '../src/views/includes/top_menu.php'; ?>
        <div class="container-fluid p-2">
            <div class="card mb-2 ">
                <div class="card-body p-1">
                    <a class="btn btn-primary btn-sm custom-nav card-description" href="?secao=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                </div>
            </div>
            <div class="card mb-2 card-description ">
                <div class="card-header bg-primary text-white px-2 py-1 card-background"><i class="bi bi-database-fill-gear"></i> Atualizar proposições</div>
                <div class="card-body p-2">
                    <p class="card-text mb-0">Selecione o ano que deseja atualizar as proposições
                </div>
            </div>

            <div class="row ">
                <div class="col-md-3 col-12">
                    <div class="card shadow-sm mb-2 card-description">
                        <div class="card-header bg-primary text-white px-2 py-1">Atualizar proposições</div>
                        <div class="card-body p-2">


                            <?php

                            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_salvar_proposicoes'])) {
                                $ano_proposicao = $_POST['ano_proposicao'];
                                $result = $proposicaoController->inserirProposicoes($ano_proposicao);

                                if ($result['status'] == 'success') {
                                    echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                                } else if ($result['status'] == 'error') {
                                    echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . '</div>';
                                }
                            }

                            ?>

                            <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="application/x-www-form-urlencoded">
                                <div class="col-md-10 col-9">
                                    <select class="form-select form-select-sm" name="ano_proposicao" required>
                                        <?php
                                        $ano_atual = date("Y"); // Obtém o ano atual
                                        for ($ano = 1950; $ano <= $ano_atual; $ano++) {
                                            $selected = ($ano == $ano_atual) ? 'selected' : '';
                                            echo "<option value='$ano' $selected>$ano</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-2 col-3">
                                    <button type="submit" class="btn btn-primary btn-sm" name="btn_salvar_proposicoes"><i class="bi bi-floppy-fill"></i> Salvar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-12">
                    <div class="card shadow-sm mb-2 card-description">
                        <div class="card-header bg-success text-white px-2 py-1">Atualizar autores</div>
                        <div class="card-body p-2">
                            <?php

                            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_salvar_autores'])) {
                                $ano_proposicao_autores = $_POST['ano_proposicao_autores'];
                                $result = $proposicaoController->inserirProposicoesAutores($ano_proposicao_autores);

                                if ($result['status'] == 'success') {
                                    echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                                } else if ($result['status'] == 'error') {
                                    echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . '</div>';
                                }
                            }

                            ?>

                            <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
                                <div class="col-md-10 col-9">
                                    <select class="form-select form-select-sm" name="ano_proposicao_autores" required>
                                        <?php
                                        $ano_atual = date("Y");
                                        for ($ano = 1950; $ano <= $ano_atual; $ano++) {
                                            $selected = ($ano == $ano_atual) ? 'selected' : '';
                                            echo "<option value='$ano' $selected>$ano</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-2 col-3">
                                    <button type="submit" class="btn btn-success btn-sm" name="btn_salvar_autores"><i class="bi bi-floppy-fill"></i> Salvar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>