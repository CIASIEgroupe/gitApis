<?php
$app->get('/categories', "Controller:categories");
$app->get('/categories/sandwichs', "Controller:categoriesSandwichs");
$app->get('/categories/{id}', "Controller:categorie");
$app->get('/categories/{id}/sandwichs', "Controller:categorieSandwichs");
$app->post('/categories', "Controller:newCategorie");
$app->put('/categories/{id}', "Controller:updateCategorie");
$app->get('/sandwichs', "Controller:sandwichs");
$app->get('/sandwichs/{id}', "Controller:sandwich");
$app->post('/sandwichs', "Controller:newSandwich");
$app->put('/sandwich', "Controller:updateSandwich");
$app->delete('/sandwich/{id}', "Controller:deleteSandwich");