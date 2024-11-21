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
                <div class="card-header bg-primary text-white px-2 py-1 card-background"><i class="bi bi-people-fill"></i> Proposições do gabinete</div>
                <div class="card-body p-2">
                    <p class="card-text mb-0">Proposições de autoria do deputado.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped mb-0 custom-table">
                            <thead>
                                <tr>
                                    <th scope="col">Proposição</th>
                                    <th scope="col">Ementa/Resumo</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php

                                $buscaMP = $proposicaoController->medidasProvisorias(2024);

                                if ($buscaMP['status'] == 'success') {

                                    usort($buscaMP['dados']['PesquisaBasicaMateria']['Materias']['Materia'], function ($a, $b) {
                                        return $b['Numero'] <=> $a['Numero'];
                                    });

                                    foreach ($buscaMP['dados']['PesquisaBasicaMateria']['Materias']['Materia'] as $MP) {
                                        echo '<tr>';
                                        echo '<td style="white-space: nowrap; justify-content: center; align-items: center;">' . $MP['DescricaoIdentificacao'] . '</td>';
                                        echo '<td>' . $MP['Ementa'] . '</td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="2">' . $buscaMP['message'] . '</td></tr>';
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