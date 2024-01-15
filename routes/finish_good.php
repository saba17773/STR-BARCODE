<?php

$app->get('/report/finish_good/withdraw', 'App\Controllers\WarehouseController::FGWithdraw');
$app->post('/api/v1/finish_good/withdraw/pdf', 'App\Controllers\WarehouseController::FGWithdrawPDF');