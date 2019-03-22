<?php
$app->post('/commands', "Controller:newCommand");
$app->get('/commands/{id}', "Controller:command");
$app->get('/commands/{id}/items', "Controller:items");
$app->put('/commands/{id}', "Controller:updateCommand");
$app->post('/clients/{id}/auth', "Controller:login");
$app->post('/clients', "Controller:register");
$app->get('/clients/{id}', "Controller:profile");
$app->get('/clients/{id}/commands', "Controller:commands");