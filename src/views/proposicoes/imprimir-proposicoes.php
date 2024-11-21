<?php

include '../src/views/includes/verificaLogado.php';

require_once '../vendor/autoload.php';

use Jairosantos\GabineteDigital\Controllers\ProposicaoController;
use Jairosantos\GabineteDigital\Controllers\NotaTecnicaController;

$proposicaoController = new ProposicaoController();
$notaController = new NotaTecnicaController();

$itens = 1000;
$arquivada = isset($_GET['arquivada']) ? (int) $_GET['arquivada'] : 0;

$ano = isset($_GET['ano']) ? (int) $_GET['ano'] : date('Y');
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'PL';
$pagina = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$ordenarPor = isset($_GET['ordenarPor']) && in_array(htmlspecialchars($_GET['ordenarPor']), ['proposicao_id', 'proposicao_numero']) ? htmlspecialchars($_GET['ordenarPor']) : 'proposicao_id';
$ordem = isset($_GET['ordem']) ? strtolower(htmlspecialchars($_GET['ordem'])) : 'desc';
$termo = isset($_GET['termo']) ? htmlspecialchars($_GET['termo']) : '';

?>


<script>
    window.print();

    window.onafterprint = function() {
        window.close();
    };
</script>

<style>
    body {
        background-image: none;
        font-size: 12px;
    }

    a {
        color: black;
    }

    #nota_texto_print p {
        text-indent: 5em;
        text-align: justify;
    }

    /* Forçar impressão em formato retrato */
    @media print {
        @page {
            size: landscape;
            /* Define o tamanho da página para retrato */
        }
    }
</style>


<div class="card mb-2 border-0 no-break">
    <div class="card-body p-2">
        <img src="./img/brasaooficialcolorido.png" style="width: 150px;" class="card-img-top mx-auto d-block" alt="...">
        <p class="card-text mb-0 text-center" style="font-size: 1.1em;">Câmara dos Deputados</p>
        <p class="card-text mb-4 text-center" style="font-size: 1em;">Gabinete do Deputado <?php echo $_ENV['NOME_DEPUTADO'] ?></p>
        <p class="card-text mb-1 mt-2 text-center" style="font-size: 1.4em;"><b>Lista simples de proposições <?php echo (empty($termo)) ? '(' . $ano . ')' : '(' . $termo . ')' ?></b></p>
        <p class="card-text mb-1 mt-o text-center" style="font-size: 1.1em;"><em>(<?php echo ($tipo == 'PL') ? 'Projetos de Lei' : 'Requerimentos' ?>) (<?php echo ($arquivada == 0) ? 'Em tramitação' : 'Arquivados' ?></em>)</p>
    </div>
</div>

<div class="card mb-2 border-0  no-break">
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

                    $buscaProposicoes = $proposicaoController->proposicoesGabinete($itens, $pagina, $ordenarPor, $ordem, $tipo, $ano, $termo, $arquivada);

                    if ($buscaProposicoes['status'] == 'success') {
                        foreach ($buscaProposicoes['dados'] as $proposicao) {

                            $nota = $notaController->buscarNotaTecnica('nota_proposicao', $proposicao['proposicao_id']);

                            if ($nota['status'] == 'success') {
                                $apelido = $nota['dados'][0]['nota_titulo'] . '<br>';
                                $ementa = $nota['dados'][0]['nota_resumo'] . '<br><br><em>' . $proposicao['proposicao_ementa'] . '</em>';
                            } else {
                                $apelido = '';
                                $ementa = $proposicao['proposicao_ementa'];
                            }

                            echo '<tr>';
                            echo '<td style="white-space: nowrap; justify-content: center; align-items: center;"><a href="?secao=nota&proposicao=' . $proposicao['proposicao_id'] . '">' . ($proposicao['proposicao_aprovada'] ? '<i class="bi bi-check-circle-fill"></i> ' . $proposicao['proposicao_titulo'] : $proposicao['proposicao_titulo']) . '</a></td>';
                            echo '<td style="justify-content: center; align-items: center;"><b>' . $apelido . '</b>' . $ementa . '</td>';
                            echo '</tr>';
                        }
                    } else if ($buscaProposicoes['status'] == 'empty' || $buscaProposicoes['status'] == 'error') {
                        echo '<tr><td colspan="6">' . $buscaProposicoes['message'] . '</td></tr>';
                    }

                    ?>

                </tbody>
            </table>
        </div>

    </div>
</div>