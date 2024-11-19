<?php

include '../src/views/includes/verificaLogado.php';

require_once '../vendor/autoload.php';

use Jairosantos\GabineteDigital\Controllers\NotaTecnicaController;
use Jairosantos\GabineteDigital\Core\GetJson;

$getjson = new GetJson();

$notaTecnicaController = new NotaTecnicaController;

$proposicaoGet = isset($_GET['proposicao']) ? $_GET['proposicao'] : null;

$buscaNota = $notaTecnicaController->buscarNotaTecnica('nota_proposicao', $proposicaoGet);

$buscaCD = $getjson->getJson('https://dadosabertos.camara.leg.br/api/v2/proposicoes/' . $proposicaoGet);

$buscaAutorCD = $getjson->getJson('https://dadosabertos.camara.leg.br/api/v2/proposicoes/' . $proposicaoGet . '/autores');


?>


<div class="d-flex" id="wrapper">
    <?php include '../src/views/includes/sider_bar.php'; ?>
    <div id="page-content-wrapper">
        <?php include '../src/views/includes/top_menu.php'; ?>
        <div class="container-fluid p-2">
            <div class="card mb-2">
                <div class="card-body p-1">
                    <a class="btn btn-primary btn-sm custom-nav card-description" href="?pagina=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                    <a class="btn btn-success btn-sm custom-nav card-description" href="?secao=proposicoes" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>

                </div>
            </div>
            <div class="card mb-2 card-description">
                <div class="card-header bg-primary text-white px-2 py-1 card-background"><i class="bi bi-file-earmark-text"></i>Nota Técnica</div>
                <div class="card-body p-2">
                    <p class="card-text mb-2">Nesta seção, é possível adicionar e gerenciar notas técnicas para documentação e controle.</p>
                    <p class="card-text mb-0">Todos os campos são obrigatórios</p>
                </div>
            </div>
            <div class="card mb-2 ">
                <div class="card-body p-1">
                    <?php
                    if ($buscaNota['status'] == 'not_found' || is_integer($proposicaoGet) || $buscaNota['status'] == 'error') {
                        echo '<button class="btn btn-success btn-sm custom-nav card-description" disabled role="button"><i class="bi bi-printer-fill"></i> Imprimir nota</button>';
                    } else {
                        echo '<a class="btn btn-success btn-sm custom-nav card-description" href="?secao=imprimir-nota&proposicao=' . $proposicaoGet . '" target="_blank" role="button"><i class="bi bi-printer-fill"></i> Imprimir nota</a>';
                    }
                    ?>
                </div>
            </div>
            <div class="card mb-2 card-description">
                <div class="card-body p-2">
                    <?php

                    if (isset($buscaCD['status']) && $buscaCD['status'] == 404) {
                        echo 'Proposição não localizada';
                        exit();
                    }

                    $proposicao_titulo = $buscaCD['dados']['siglaTipo'] . ' ' . $buscaCD['dados']['numero'] . '/' . $buscaCD['dados']['ano'];
                    ?>

                    <h5 class="card-title mb-2"><?php echo $proposicao_titulo ?></h5>
                    <p class="card-text mb-2"><em><?php echo $buscaCD['dados']['ementa'] ?></em></p>

                    <?php

                    foreach ($buscaAutorCD['dados'] as $autor) {
                        if ($autor['proponente'] == 1) {
                            echo '<p class="card-text mb-2"><i class="bi bi-person"></i> Autor: ' . $autor['nome'] . '</p>';
                        }
                    }

                    ?>

                    <p class="card-text mb-2"><i class="bi bi-calendar"></i> Data de apresentação: <?php echo date('d/m', strtotime($buscaCD['dados']['dataApresentacao'])) ?> <?php echo ($buscaCD['dados']['statusProposicao']['descricaoSituacao'] == 'Arquivada') ? ' | <i class="bi bi-info-circle-fill"></i> <b>Arquivada</b>' : '' ?> </p>
                    <p class="card-text mb-2"><a href="<?php echo $buscaCD['dados']['urlInteiroTeor'] ?>" target="_blank"><i class="bi bi-file-earmark"></i> Ver inteiro teor</a></p>
                    <p class="card-text mb-0"><a href="https://www.camara.leg.br/proposicoesWeb/fichadetramitacao?idProposicao=<?php echo $buscaCD['dados']['id']  ?>" target="_blank"><i class="bi bi-box-arrow-up-right"></i> Página da CD</a></p>
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
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '. Aguarde...</div>';
                            echo '<script>
                            setTimeout(function(){
                                window.location.href = "?secao=nota&proposicao=' . $proposicaoGet . '";
                            }, 1000);</script>';
                        } else if ($result['status'] == 'bad_request') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        }
                    }


                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_atualizar'])) {
                        $dados = [
                            'nota_proposicao' => $proposicaoGet,
                            'nota_titulo' => htmlspecialchars($_POST['nota_titulo'], ENT_QUOTES, 'UTF-8'),
                            'nota_resumo' => htmlspecialchars($_POST['nota_resumo'], ENT_QUOTES, 'UTF-8'),
                            'nota_texto' => htmlspecialchars($_POST['nota_texto'], ENT_QUOTES, 'UTF-8')
                        ];

                        $result = $notaTecnicaController->atualizarNotaTecnica($proposicaoGet, $dados);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '. Aguarde...</div>';
                            echo '<script>
                            setTimeout(function(){
                                window.location.href = "?secao=nota&proposicao=' . $proposicaoGet . '";
                            }, 1000);</script>';
                        } else if ($result['status'] == 'bad_request') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        }
                    }

                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_apagar'])) {
                        $result = $notaTecnicaController->apagarNotaTecnica($proposicaoGet);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '. Aguarde...</div>';
                            echo '<script>
                                setTimeout(function(){
                                    window.location.href = "?secao=nota&proposicao=' . $proposicaoGet . '";
                                }, 1000);</script>';
                        } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request' || $result['status'] == 'invalid_email') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . '</div>';
                        }
                    }
                    ?>
                    <form class="row g-2 form_custom" method="POST">
                        <div class="col-md-6 col-12">
                            <input type="text" class="form-control form-control-sm" name="nota_titulo" placeholder="Título" value="<?php echo ($buscaNota['status'] != 'not_found') ? $buscaNota['dados'][0]['nota_titulo'] : ''  ?>" required>
                        </div>
                        <div class="col-md-4 col-12">
                            <input type="text" class="form-control form-control-sm" name="nota_resumo" placeholder="Resumo" value="<?php echo ($buscaNota['status'] != 'not_found') ? $buscaNota['dados'][0]['nota_resumo'] : '' ?>" required>
                        </div>
                        <div class="col-md-2 col-12">
                            <input type="text" class="form-control form-control-sm" disabled value="<?php echo ($buscaNota['status'] != 'not_found') ? $buscaNota['dados'][0]['usuario_nome'] . ' | ' . date('d/m - H:i', strtotime($buscaNota['dados'][0]['nota_criada_em'])) : '' ?>" required>
                        </div>
                        <div class="col-md-12 col-12">
                            <script>
                                tinymce.init({
                                    selector: 'textarea',
                                    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount fullscreen',
                                    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | alignleft aligncenter alignright alignjustify | numlist bullist indent outdent | emoticons charmap | removeformat | fullscreen',
                                    height: 400,
                                    language: 'pt_BR',
                                    content_css: "public/css/tinymce.css",
                                    setup: function(editor) {
                                        editor.on('init', function() {
                                            editor.getBody().style.fontSize = '10pt';
                                        });
                                    }
                                });
                            </script>
                            <textarea class="form-control form-control-sm" name="nota_texto" placeholder="Texto" rows="10"><?php echo ($buscaNota['status'] != 'not_found') ? $buscaNota['dados'][0]['nota_texto'] : '' ?></textarea>
                        </div>
                        <div class="col-md-6 col-12">
                            <?php
                            if ($buscaNota['status'] == 'not_found' || is_integer($proposicaoGet) || $buscaNota['status'] == 'error') {
                                echo '<button type="submit" class="btn btn-success btn-sm" name="btn_salvar"><i class="bi bi-floppy-fill"></i> Salvar</button>';
                            } else {
                                echo '<button type="submit" class="btn btn-success btn-sm" name="btn_atualizar"><i class="bi bi-floppy-fill"></i> Atualizar</button>&nbsp;';
                                echo '<button type="submit" class="btn btn-danger btn-sm" name="btn_apagar"><i class="bi bi-floppy-fill"></i> Apagar</button>';
                            }
                            ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>