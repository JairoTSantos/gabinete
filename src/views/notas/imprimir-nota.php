<?php


include '../src/views/includes/verificaLogado.php';

require_once '../vendor/autoload.php';

use Jairosantos\GabineteDigital\Controllers\NotaTecnicaController;
use Jairosantos\GabineteDigital\Core\GetJson;
use Dotenv\Dotenv;


$dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();

$getjson = new GetJson();

$notaTecnicaController = new NotaTecnicaController;

$proposicaoGet = isset($_GET['proposicao']) ? $_GET['proposicao'] : null;

$buscaNota = $notaTecnicaController->buscarNotaTecnica('nota_proposicao', $proposicaoGet);

if ($buscaNota['status'] == 'not_found' || is_integer($proposicaoGet) || $buscaNota['status'] == 'error') {
    header('Location: ?secao=home');
    exit();
}

$buscaCD = $getjson->getJson('https://dadosabertos.camara.leg.br/api/v2/proposicoes/' . $proposicaoGet);

if (isset($buscaCD['status']) && $buscaCD['status'] == 404) {
    echo '<script> window.close();</script>';
    exit();
}

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
    }

    #nota_texto_print p {
        text-indent: 5em;
        text-align: justify;
    }
</style>
<div class="row">
    <div class="col-12">
        <div class="card mb-2 border-0 no-break">
            <div class="card-body p-2">
                <img src="./img/brasaooficialcolorido.png" style="width: 150px;" class="card-img-top mx-auto d-block" alt="...">
                <p class="card-text mb-0 text-center" style="font-size: 1.1em;">Câmara dos Deputados</p>
                <p class="card-text mb-4 text-center" style="font-size: 1em;">Gabinete do Deputado <?php echo $_ENV['NOME_DEPUTADO'] ?></p>
                <p class="card-text mb-2 mt-4 text-center" style="font-size: 1.4em;"><b><?php echo $buscaCD['dados']['siglaTipo'] . ' ' . $buscaCD['dados']['numero'] . '/' . $buscaCD['dados']['ano'] ?></b> </p>
                <p class="card-text mb-2 text-center" style="font-size: 1.2em;"><b><?php echo $buscaNota['dados'][0]['nota_titulo'] ?></b></p>
                <p class="card-text mb-4 text-center style=" font-size: 1.2em;">(<?php echo $buscaNota['dados'][0]['nota_resumo'] ?>)</p>
                <p class="card-text mb-0 text-center" style="font-size: 1.3em;"><b>Nota técnica</b></p>
                <p class="card-text mb-4 text-center" style="font-size: 0.8em;">criada por (<?php echo $buscaNota['dados'][0]['usuario_nome'] ?>)</p>
                <p class="card-text">
                <div id="nota_texto_print"><?php echo htmlspecialchars_decode($buscaNota['dados'][0]['nota_texto']); ?></p>
                </div>

            </div>
        </div>
    </div>
</div>