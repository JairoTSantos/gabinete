<?php

include '../src/views/includes/verificaLogado.php';


require_once '../vendor/autoload.php';

use Jairosantos\GabineteDigital\Controllers\ProposicaoController;

$proposicaoController = new ProposicaoController();

$itens = isset($_GET['itens']) ? (int) $_GET['itens'] : 10;

$ano = isset($_GET['ano']) ? (int) $_GET['ano'] : date('Y');

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
                <div class="card-header bg-primary text-white px-2 py-1 card-background"><i class="bi bi-people-fill"></i> Medidas provisórias</div>
                <div class="card-body p-2">
                    <p class="card-text mb-0">Medidas provisórias publicadas no ano.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <form class="row g-2 form_custom mb-0" method="GET" enctype="application/x-www-form-urlencoded">
                        <input type="hidden" name="secao" value="medidas-provisorias" />
                        <div class="col-md-1 col-12">
                            <input type="text" class="form-control form-control-sm" name="ano" placeholder="Ano" data-mask="0000" value="<?php echo $ano ?>">
                        </div>

                        <div class="col-md-2 col-6">
                            <select class="form-select form-select-sm" name="itens" required>
                                <option value="5" <?php echo $itens == 5 ? 'selected' : ''; ?>>5 itens</option>
                                <option value="10" <?php echo $itens == 10 ? 'selected' : ''; ?>>10 itens</option>
                                <option value="25" <?php echo $itens == 25 ? 'selected' : ''; ?>>25 itens</option>
                                <option value="50" <?php echo $itens == 50 ? 'selected' : ''; ?>>50 itens</option>
                            </select>
                        </div>

                        <div class="col-md-1 col-2">
                            <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-search"></i></button>
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
                                    <th scope="col">Proposição</th>
                                    <th scope="col">Data</th>
                                    <th scope="col" class="text-center">Emendar?</th>
                                    <th scope="col">Ementa/Resumo</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php

                                $buscaMP = $proposicaoController->medidasProvisorias($ano);


                                $itensPorPagina = $itens;
                                $paginaAtual = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;

                                if ($buscaMP['status'] == 'success') {

                                    $materias = $buscaMP['dados']['PesquisaBasicaMateria']['Materias']['Materia'];

                                    usort($materias, function ($a, $b) {
                                        return $b['Numero'] <=> $a['Numero'];
                                    });

                                    $totalMaterias = count($materias);
                                    $totalPaginas = ceil($totalMaterias / $itensPorPagina);

                                    $materiasPagina = array_slice($materias, ($paginaAtual - 1) * $itensPorPagina, $itensPorPagina);



                                    foreach ($materiasPagina as $MP) {

                                        $emendas = $proposicaoController->medidasProvisoriasEmendas($MP['Codigo']);

                                        $autores = $proposicaoController->medidasProvisoriasEmendasAutores($emendas);

                                       // if(in_array('Acácio Favacho', $autores)){
                                            echo $MP['DescricaoIdentificacao'];
                                             print_r($autores);
                                      //  }else{
                                     //       echo 'error';
                                     //   }

                                        echo '<tr>';
                                        echo '<td style="white-space: nowrap; justify-content: center; align-items: center;"><a href="">' . $MP['DescricaoIdentificacao'] . '</a></td>';

                                        $dataPublicacao = strtotime($MP['Data']);
                                        $dataLimite = strtotime('+6 days', $dataPublicacao);
                                        $dataAtual = time();

                                        $status = ($dataAtual > $dataLimite) ? 'Não' : 'Sim';

                                        echo '<td style="white-space: nowrap; justify-content: center; align-items: center;">' . date('d/m', $dataPublicacao) . '</td>';
                                        echo '<td style="white-space: nowrap; justify-content: center; align-items: center;" class="text-center">' . $status . '</td>';
                                        echo '<td style="white-space: nowrap; justify-content: center; align-items: center;">' . $MP['Ementa'] . '</td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="3">' . $buscaMP['message'] . '</td></tr>';
                                }
                                ?>


                            </tbody>
                        </table>
                    </div>
                    <?php
                    if (isset($totalPaginas) && $totalPaginas > 1) {
                        echo '<ul class="pagination custom-pagination mt-2 mb-0">';
                        echo '<li class="page-item ' . ($paginaAtual == 1 ? 'disabled' : '') . '">
                                <a class="page-link" href="?secao=medidas-provisorias&pagina=1&ano=' . $ano . '&itens=' . $itens . '">Primeira</a>
                              </li>';

                        for ($i = 1; $i <= $totalPaginas; $i++) {
                            echo '<li class="page-item ' . ($paginaAtual == $i ? 'active' : '') . '">
                                    <a class="page-link" href="?secao=medidas-provisorias&pagina=' . $i . '&ano=' . $ano . '&itens=' . $itens . '">' . $i . '</a>
                                  </li>';
                        }

                        echo '<li class="page-item ' . ($paginaAtual == $totalPaginas ? 'disabled' : '') . '">
                                <a class="page-link" href="?secao=medidas-provisorias&pagina=' . $totalPaginas . '&ano=' . $ano . '&itens=' . $itens . '">Última</a>
                              </li>';
                        echo '</ul>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>