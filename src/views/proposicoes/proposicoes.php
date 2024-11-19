<?php

include '../src/views/includes/verificaLogado.php';

require_once '../vendor/autoload.php';

use Jairosantos\GabineteDigital\Controllers\ProposicaoController;

$proposicaoController = new ProposicaoController();

$itens = isset($_GET['itens']) ? (int) $_GET['itens'] : 10;
$ano = isset($_GET['ano']) ? (int) $_GET['ano'] : date('Y');
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'PL';
$pagina = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$ordenarPor = isset($_GET['ordenarPor']) && in_array(htmlspecialchars($_GET['ordenarPor']), ['proposicao_id', 'proposicao_numero']) ? htmlspecialchars($_GET['ordenarPor']) : 'proposicao_numero';
$ordem = isset($_GET['ordem']) ? strtolower(htmlspecialchars($_GET['ordem'])) : 'desc';
$termo = isset($_GET['termo']) ? htmlspecialchars($_GET['termo']) : '';

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
            <div class="row ">
                <div class="col-12">
                    <div class="card shadow-sm mb-2">
                        <div class="card-body p-2">
                            <form class="row g-2 form_custom mb-0" method="GET" enctype="application/x-www-form-urlencoded">
                                <input type="hidden" name="secao" value="proposicoes" />
                                <div class="col-md-1 col-12">
                                    <input type="text" class="form-control form-control-sm" name="ano" placeholder="Ano" data-mask="0000" value="<?php echo $ano ?>">
                                </div>
                                <div class="col-md-2 col-6">
                                    <select class="form-select form-select-sm" name="ordem" required>
                                        <option value="asc" <?php echo $ordem == 'asc' ? 'selected' : ''; ?>>Ordem Crescente</option>
                                        <option value="desc" <?php echo $ordem == 'desc' ? 'selected' : ''; ?>>Ordem Decrescente</option>
                                    </select>
                                </div>
                                <div class="col-md-1 col-6">
                                    <select class="form-select form-select-sm" name="itens" required>
                                        <option value="5" <?php echo $itens == 5 ? 'selected' : ''; ?>>5 itens</option>
                                        <option value="10" <?php echo $itens == 10 ? 'selected' : ''; ?>>10 itens</option>
                                        <option value="25" <?php echo $itens == 25 ? 'selected' : ''; ?>>25 itens</option>
                                        <option value="50" <?php echo $itens == 50 ? 'selected' : ''; ?>>50 itens</option>
                                    </select>
                                </div>
                                <div class="col-md-1 col-12">
                                    <select class="form-select form-select-sm" name="tipo" required>
                                        <option value="PL" <?php echo $tipo == 'PL' ? 'selected' : ''; ?>>PL</option>
                                        <option value="PEC" <?php echo $tipo == 'PEC' ? 'selected' : ''; ?>>PEC</option>
                                        <option value="REQ" <?php echo $tipo == 'REQ' ? 'selected' : ''; ?>>REQ</option>
                                    </select>
                                </div>
                                <div class="col-md-3 col-10">
                                    <input type="text" class="form-control form-control-sm" name="termo" placeholder="Buscar...">
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
                                    <th scope="col">Proposição</th>
                                    <th scope="col">Ementa</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php

                                $buscaProposicoes = $proposicaoController->proposicoesGabinete($itens, $pagina, $ordenarPor, $ordem, $tipo, $ano, $termo);

                                if ($buscaProposicoes['status'] == 'success') {
                                    foreach ($buscaProposicoes['dados'] as $proposicao) {
                                        echo '<tr>';
                                        echo '<td style="white-space: nowrap; justify-content: center; align-items: center;"><a href="?secao=nota&proposicao=' . $proposicao['proposicao_id'] . '">' . $proposicao['proposicao_titulo'] . '</a></td>';
                                        echo '<td style="justify-content: center; align-items: center;">' . $proposicao['proposicao_ementa'] . '</td>';

                                        echo '</tr>';
                                    }
                                } else if ($buscaProposicoes['status'] == 'empty' || $buscaProposicoes['status'] == 'error') {
                                    echo '<tr><td colspan="6">' . $buscaProposicoes['message'] . '</td></tr>';
                                }
                                
                                ?>

                            </tbody>
                        </table>
                    </div>
                    <?php
                    if (isset($buscaProposicoes['total_paginas'])) {
                        $totalPagina = $buscaProposicoes['total_paginas'];
                    } else {
                        $totalPagina = 0;
                    }

                    if ($totalPagina > 0 && $totalPagina != 1) {
                        echo '<ul class="pagination custom-pagination mt-2 mb-0">';
                        echo '<li class="page-item ' . ($pagina == 1 ? 'active' : '') . '"><a class="page-link" href="?secao=proposicoes&itens=' . $itens . '&tipo=' . $tipo . '&ano=' . $ano . '&pagina=1&ordenarPor=' . $ordenarPor . '&ordem=' . $ordem . (isset($termo) ? '&termo=' . $termo : '') . '">Primeira</a></li>';

                        for ($i = 1; $i < $totalPagina - 1; $i++) {
                            $pageNumber = $i + 1;
                            echo '<li class="page-item ' . ($pagina == $pageNumber ? 'active' : '') . '"><a class="page-link" href="?secao=proposicoes&itens=' . $itens . '&tipo=' . $tipo . '&ano=' . $ano . '&pagina=' . $pageNumber . '&ordenarPor=' . $ordenarPor . '&ordem=' . $ordem . (isset($termo) ? '&termo=' . $termo : '') . '">' . $pageNumber . '</a></li>';
                        }

                        echo '<li class="page-item ' . ($pagina == $totalPagina ? 'active' : '') . '"><a class="page-link" href="?secao=proposicoes&itens=' . $itens . '&tipo=' . $tipo . '&ano=' . $ano . '&pagina=' . $totalPagina . '&ordenarPor=' . $ordenarPor . '&ordem=' . $ordem . (isset($termo) ? '&termo=' . $termo : '') . '">Última</a></li>';
                        echo '</ul>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>