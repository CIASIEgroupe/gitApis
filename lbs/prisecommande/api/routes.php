<?php
$app->post('/commands', "Controller:newCommand");
$app->get('/commands/{id}', "Controller:command");
$app->get('/commands/{id}/items', "Controller:items");
$app->put('/commands/{id}/date', "Controller:updateDate");
$app->put('/commands/{id}/pay', "Controller:updatePay");
$app->post('/client/{id}/auth', "Controller:login");
$app->post('/client/register', "Controller:register");
$app->get('/client/{id}', "Controller:profile");
$app->get('/client/{id}/commands', "Controller:commands");