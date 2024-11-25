<?php

use Jairosantos\GabineteDigital\Controllers\ComissoesController;

include '../src/views/includes/verificaLogado.php';

require_once '../vendor/autoload.php';

$comissoesController = new ComissoesController();

print_r($comissoesController->comissoesDoGabinete(false));