<?php
$app->get('/commands', "Controller:commandes");

$app->get('/commands/{id}', "Controller:commande");

$app->put('/commands/{id}', "Controller:updateState");