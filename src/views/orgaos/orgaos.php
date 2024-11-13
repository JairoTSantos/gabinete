<?php

include '../src/views/includes/verificaLogado.php';

require_once '../vendor/autoload.php';

use Jairosantos\GabineteDigital\Controllers\OrgaoController;
use Jairosantos\GabineteDigital\Controllers\OrgaoTipoController;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();

$orgaoController = new OrgaoController();
$orgaoTipoController = new OrgaoTipoController();

$itens = isset($_GET['itens']) ? (int) $_GET['itens'] : 10;
$pagina = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$ordenarPor = isset($_GET['ordenarPor']) ? htmlspecialchars($_GET['ordenarPor']) : 'orgao_nome';
$ordem = isset($_GET['ordem']) ? strtolower(htmlspecialchars($_GET['ordem'])) : 'asc';
$termo = isset($_GET['termo']) ? htmlspecialchars($_GET['termo']) : null;
$filtro = isset($_GET['filtro']) ? ($_GET['filtro'] == '1' ? true : false) : false;

?>

<div class="d-flex" id="wrapper">
    <?php include '../src/views/includes/sider_bar.php'; ?>
    <div id="page-content-wrapper">
        <?php include '../src/views/includes/top_menu.php'; ?>
        <div class="container-fluid p-2">
            <div class="card mb-2 ">
                <div class="card-body p-1">
                    <a class="btn btn-primary btn-sm custom-nav card-description" href="?pagina=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                </div>
            </div>
            <div class="card mb-2 card-description">
                <div class="card-header bg-primary text-white px-2 py-1 card-background"><i class="bi bi-building"></i> Órgãos e entidades</div>
                <div class="card-body p-2">
                    <p class="card-text mb-2">Nesta seção, é possível adicionar e editar os tipos de órgãos e entidades, garantindo a organização correta dessas informações no sistema.</p>
                    <p class="card-text mb-0">Todos os campos são obrigatórios</p>
                </div>
            </div>
            <div class="card shadow-sm mb-2 ">
                <div class="card-body p-0">
                    <nav class="navbar navbar-expand bg-body-tertiary p-0 ">
                        <div class="container-fluid p-0">
                            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                                <ul class="navbar-nav me-auto mb-0 mb-lg-0">
                                    <li class="nav-item">
                                        <a class="nav-link active p-1" aria-current="page" href="#">
                                            <button class="btn btn-success btn-sm" style="font-size: 0.850em;" id="btn_novo_tipo" type="button"><i class="bi bi-plus-circle-fill"></i> Novo tipo</button>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_salvar'])) {
                        $dados = [
                            'orgao_nome' => htmlspecialchars($_POST['nome'], ENT_QUOTES, 'UTF-8'),
                            'orgao_email' => htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8'),
                            'orgao_telefone' => htmlspecialchars($_POST['telefone'], ENT_QUOTES, 'UTF-8'),
                            'orgao_endereco' => htmlspecialchars($_POST['endereco'], ENT_QUOTES, 'UTF-8'),
                            'orgao_cep' => htmlspecialchars($_POST['cep'], ENT_QUOTES, 'UTF-8'),
                            'orgao_bairro' => htmlspecialchars($_POST['bairro'], ENT_QUOTES, 'UTF-8'),
                            'orgao_estado' => htmlspecialchars($_POST['estado'], ENT_QUOTES, 'UTF-8'),
                            'orgao_municipio' => htmlspecialchars($_POST['municipio'], ENT_QUOTES, 'UTF-8'),
                            'orgao_tipo' => htmlspecialchars($_POST['tipo'], ENT_QUOTES, 'UTF-8'),
                            'orgao_site' => htmlspecialchars($_POST['site'], ENT_QUOTES, 'UTF-8'),
                            'orgao_informacoes' => htmlspecialchars($_POST['informacoes'], ENT_QUOTES, 'UTF-8')
                        ];

                        $result = $orgaoController->criarOrgao($dados);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" role="alert" data-timeout="3">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" role="alert" data-timeout="3">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" role="alert" data-timeout="3">' . $result['message'] . '</div>';
                        }
                    }
                    ?>
                    <form class="row g-2 form_custom " id="form_novo" method="POST" enctype="application/x-www-form-urlencoded">
                        <div class="col-md-5 col-12">
                            <input type="text" class="form-control form-control-sm" name="nome" placeholder="Nome " required>
                        </div>
                        <div class="col-md-4 col-6">
                            <input type="email" class="form-control form-control-sm" name="email" placeholder="Email " required>
                        </div>
                        <div class="col-md-3 col-6">
                            <input type="text" class="form-control form-control-sm" name="telefone" placeholder="Telefone (somente números)" data-mask="(00) 00000-0000" maxlength="11">
                        </div>
                        <div class="col-md-4 col-12">
                            <input type="text" class="form-control form-control-sm" name="endereco" placeholder="Endereço ">
                        </div>
                        <div class="col-md-3 col-6">
                            <input type="text" class="form-control form-control-sm" name="bairro" placeholder="Bairro">
                        </div>
                        <div class="col-md-2 col-6">
                            <input type="text" class="form-control form-control-sm" name="cep" placeholder="CEP (somente números)" data-mask="00000-000" maxlength="8">
                        </div>
                        <div class="col-md-1 col-6">
                            <select class="form-select form-select-sm" id="estado" name="estado" required>
                                <option value="" selected>UF</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-6">
                            <select class="form-select form-select-sm" id="municipio" name="municipio" required>
                                <option value="" selected>Município</option>
                            </select>
                        </div>
                        <div class="col-md-3 col-12">
                            <select class="form-select form-select-sm" id="tipo" name="tipo" required>
                                <?php
                                $busca = $orgaoTipoController->listarOrgaosTipos();

                                if ($busca['status'] == 'success') {
                                    foreach ($busca['dados'] as $orgaoTipo) {
                                        if ($orgaoTipo['orgao_tipo_id'] == 1000) {
                                            echo '<option value="' . $orgaoTipo['orgao_tipo_id'] . '" selected>' . $orgaoTipo['orgao_tipo_nome'] . '</option>';
                                        } else {
                                            echo '<option value="' . $orgaoTipo['orgao_tipo_id'] . '">' . $orgaoTipo['orgao_tipo_nome'] . '</option>';
                                        }
                                    }
                                } else if ($busca['status'] == 'empty') {
                                    echo '<option>' . $busca['message'] . '</option>';
                                } else if ($busca['status'] == 'error') {
                                    echo '<option>' . $busca['message'] . '</option>';
                                }
                                ?>
                                <option value="+">Novo tipo + </option>
                            </select>
                        </div>
                        <div class="col-md-9 col-12">
                            <input type="text" class="form-control form-control-sm" name="site" placeholder="Site ou rede sociais">
                        </div>
                        <div class="col-md-12 col-12">
                            <textarea class="form-control form-control-sm" name="informacoes" rows="5" placeholder="Informações importantes desse órgão"></textarea>
                        </div>
                        <div class="col-md-4 col-6">
                            <button type="submit" class="btn btn-success btn-sm" name="btn_salvar"><i class="fa-regular fa-floppy-disk"></i> Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row ">
                <div class="col-12">
                    <div class="card shadow-sm mb-2">
                        <div class="card-body p-2">
                            <form class="row g-2 form_custom mb-0" method="GET" enctype="application/x-www-form-urlencoded">
                                <div class="col-md-2 col-6">
                                    <input type="hidden" name="secao" value="orgaos" />
                                    <select class="form-select form-select-sm" name="ordenarPor" required>
                                        <option value="orgao_nome" <?php echo $ordenarPor == 'orgao_nome' ? 'selected' : ''; ?>>Ordenar por | Nome</option>
                                        <option value="orgao_estado" <?php echo $ordenarPor == 'orgao_estado' ? 'selected' : ''; ?>>Ordenar por | Estado</option>
                                        <option value="orgao_municipio" <?php echo $ordenarPor == 'orgao_municipio' ? 'selected' : ''; ?>>Ordenar por | Muncípio</option>
                                        <option value="orgao_tipo_nome" <?php echo $ordenarPor == 'orgao_tipo_nome' ? 'selected' : ''; ?>>Ordenar por | Tipo</option>
                                        <option value="orgao_criado_em" <?php echo $ordenarPor == 'orgao_criado_em' ? 'selected' : ''; ?>>Ordenar por | Criação</option>
                                    </select>
                                </div>
                                <div class="col-md-2 col-6">
                                    <select class="form-select form-select-sm" name="ordem" required>
                                        <option value="asc" <?php echo $ordem == 'asc' ? 'selected' : ''; ?>>Ordem Crescente</option>
                                        <option value="desc" <?php echo $ordem == 'desc' ? 'selected' : ''; ?>>Ordem Decrescente</option>
                                    </select>
                                </div>
                                <div class="col-md-2 col-6">
                                    <select class="form-select form-select-sm" name="itens" required>
                                        <option value="5" <?php echo $itens == 5 ? 'selected' : ''; ?>>5 itens</option>
                                        <option value="10" <?php echo $itens == 10 ? 'selected' : ''; ?>>10 itens</option>
                                        <option value="25" <?php echo $itens == 25 ? 'selected' : ''; ?>>25 itens</option>
                                        <option value="50" <?php echo $itens == 50 ? 'selected' : ''; ?>>50 itens</option>
                                    </select>
                                </div>
                                <div class="col-md-2 col-6">
                                    <select class="form-select form-select-sm" name="filtro" required>
                                        <option value="0" <?php echo $filtro == 0 ? 'selected' : ''; ?>>Todos os estados</option>
                                        <option value="1" <?php echo $filtro == 1 ? 'selected' : ''; ?>>Somente <?php echo $_ENV['ESTADO_DEP'] ?></option>
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
                    <div class="table-responsive mb-0">
                        <table class="table table-hover table-bordered table-striped mb-0 custom-table">
                            <thead>
                                <tr>
                                    <th scope="col">Nome</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Telefone</th>
                                    <th scope="col">Endereço</th>
                                    <th scope="col">UF/Município</th>
                                    <th scope="col">Tipo</th>
                                    <th scope="col">Criado em | por</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                $busca = $orgaoController->listarOrgaos($itens, $pagina, $ordem, $ordenarPor, $termo, $filtro);

                                if ($busca['status'] == 'success') {
                                    $total_de_registros = count($busca['dados']);
                                    foreach ($busca['dados'] as $orgao) {
                                        if ($orgao['orgao_id'] <> 1000) {
                                            echo '<tr>';
                                            echo '<td style="white-space: nowrap;"><a href="?secao=orgao&id=' . $orgao['orgao_id'] . '">' . $orgao['orgao_nome'] . '</a></td>';
                                            echo '<td style="white-space: nowrap;">' . $orgao['orgao_email'] . '</td>';
                                            echo '<td style="white-space: nowrap;">' . $orgao['orgao_telefone'] . '</td>';
                                            echo '<td style="white-space: nowrap;">' . $orgao['orgao_endereco'] . '</td>';
                                            echo '<td style="white-space: nowrap;">' . $orgao['orgao_municipio'] . '/' . $orgao['orgao_estado'] . '</td>';
                                            echo '<td style="white-space: nowrap;">' . $orgao['orgao_tipo_nome'] . '</td>';
                                            echo '<td style="white-space: nowrap;">' . date('d/m/Y', strtotime($orgao['orgao_criado_em'])) . ' | ' . $orgao['usuario_nome'] . '</td>';
                                            echo '</tr>';
                                        }
                                    }
                                } else if ($busca['status'] == 'empty') {
                                    echo '<tr><td colspan="7">' . $busca['message'] . '</td></tr>';
                                } else if ($busca['status'] == 'error') {
                                    echo '<tr><td colspan="7">Erro ao carregar os dados.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <?php
                    if (isset($busca['total_paginas'])) {
                        $totalPagina = $busca['total_paginas'];
                    } else {
                        $totalPagina = 0;
                    }

                    if ($totalPagina > 0 && $totalPagina != 1) {
                        echo '<ul class="pagination custom-pagination mt-2 mb-0">';
                        echo '<li class="page-item ' . ($pagina == 1 ? 'active' : '') . '"><a class="page-link" href="?secao=orgaos&itens=' . $itens . '&pagina=1&ordenarPor=' . $ordenarPor . '&ordem=' . $ordem . (isset($termo) ? '&termo=' . $termo : '') . '">Primeira</a></li>';

                        for ($i = 1; $i < $totalPagina - 1; $i++) {
                            $pageNumber = $i + 1;
                            echo '<li class="page-item ' . ($pagina == $pageNumber ? 'active' : '') . '"><a class="page-link" href="?secao=orgaos&itens=' . $itens . '&pagina=' . $pageNumber . '&ordenarPor=' . $ordenarPor . '&ordem=' . $ordem . (isset($termo) ? '&termo=' . $termo : '') . '">' . $pageNumber . '</a></li>';
                        }

                        echo '<li class="page-item ' . ($pagina == $totalPagina ? 'active' : '') . '"><a class="page-link" href="?secao=orgaos&itens=' . $itens . '&pagina=' . $totalPagina . '&ordenarPor=' . $ordenarPor . '&ordem=' . $ordem . (isset($termo) ? '&termo=' . $termo : '') . '">Última</a></li>';
                        echo '</ul>';
                    }
                    ?>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        carregarEstados();
    });

    function carregarEstados() {
        $.getJSON('https://servicodados.ibge.gov.br/api/v1/localidades/estados?orderBy=nome', function(data) {
            const selectEstado = $('#estado');
            selectEstado.empty();
            selectEstado.append('<option value="" selected>UF</option>');
            data.forEach(estado => {
                selectEstado.append(`<option value="${estado.sigla}">${estado.sigla}</option>`);
            });
        });
    }

    function carregarMunicipios(estadoId) {
        $.getJSON(`https://servicodados.ibge.gov.br/api/v1/localidades/estados/${estadoId}/municipios?orderBy=nome`, function(data) {
            const selectMunicipio = $('#municipio');
            selectMunicipio.empty();
            selectMunicipio.append('<option value="" selected>Município</option>');
            data.forEach(municipio => {
                selectMunicipio.append(`<option value="${municipio.nome}">${municipio.nome}</option>`);
            });
        });
    }


    $('#estado').change(function() {
        const estadoId = $(this).val();
        if (estadoId) {
            $('#municipio').empty().append('<option value="">Aguarde...</option>');
            carregarMunicipios(estadoId);
        } else {
            $('#municipio').empty().append('<option value="" selected>Município</option>');
        }
    });


    $('#tipo').change(function() {
        if ($('#tipo').val() == '+') {
            if (window.confirm("Você realmente deseja inserir um novo tipo?")) {
                window.location.href = "?secao=orgaos-tipos";
            } else {
                $('#tipo').val(1000).change();
            }
        }
    });

    $('#btn_novo_tipo').click(function() {
        if (window.confirm("Você realmente deseja inserir um novo tipo?")) {
            window.location.href = "?secao=orgaos-tipos";
        } else {
            return false;
        }
    });
</script>