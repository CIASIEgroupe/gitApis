<?php
/*
GET: /commands
Accéder aux commands

Exemple: /commands?size=20&page=7&s=2

Exemple de retour, HTTP/1.1 200 Ok
{
    "type" : "collection",
    "count": 1503,
    "size": 20,
    "links": {
		"next": "/commands?page=2&size=20",
		"prev": "/commands?page=1&size=20",
		"last": "/commands?page=75&size=20",
		"first": "/commands?page=1&size=20",
    },
    "commandes": [
        {
            "command": {
                "id": "bd74eae7-9f06-464b-8d8b-859d726f117d",
                "nom": "Pinto",
                "created_at": "2018-10-21 17:39:40",
                "livraison": "2018-10-22 16:04:58",
                "status": 2
            },
            "links": {
                "self": {
                    "href": "/command/bd74eae7-9f06-464b-8d8b-859d726f117d"
                }
            }
        },
        {
            "command": {
                "id": "f89a53b2-ef39-4061-b872-d44b296dfa5b",
                "nom": "Berger",
                "created_at": "2018-10-21 17:39:38",
                "livraison": "2018-10-22 16:10:31",
                "status": 2
            },
            "links": {
                "self": {
                    "href": "/command/f89a53b2-ef39-4061-b872-d44b296dfa5b"
                }
            }
        },
        ...
    ]
}

Erreur: HTTP/1.1 404 Not Found
{
  "type" : "error',
  "error" : 404,
  "message" : ressource non disponible : /commands"
}
*/
$app->get('/commands', "Controller:commandes");

/*
GET: /commands/{id}
Accéder à la commandes

Exemple: /commands?size=20&page=7&s=2

Exemple de retour, HTTP/1.1 200 Ok
{
    "type": "resource",
    "links": {
        "self": "/commands/bd74eae7-9f06-464b-8d8b-859d726f117d"
    },
    "command": {
        "id": "bd74eae7-9f06-464b-8d8b-859d726f117d",
        "created_at": "2018-10-21 17:39:40",
        "livraison": "2018-10-22 16:04:58",
        "nom": "Pinto",
        "mail": "Pinto@gmail.com",
        "montant": null
    },
    "items": [
        {
            "uri": "/sandwichs/5",
            "libelle": "jambon-beurre",
            "tarif": "5.25",
            "quantite": 2
        }
    ]
}

Erreur: HTTP/1.1 404 Not Found
{
  "type" : "error',
  "error" : 404,
  "message" : ressource non disponible : /commands/bd74eae7-9f06-464b-8d8b-859d726f117d"
}
*/
$app->get('/commands/{id}', "Controller:commande");

/*
PUT: /commands/{id}
Modifie le statut de la commande

Exemple: /commands/bd74eae7-9f06-464b-8d8b-859d726f117d

Exemple de body
{
    "status": 3
}

Retour, HTTP/1.1 204 No Content

Erreur: HTTP/1.1 404 Not Found
{
  "type" : "error',
  "error" : 404,
  "message" : ressource non disponible : /commands/bd74eae7-9f06-464b-8d8b-859d726f117d"
}
*/
$app->put('/commands/{id}', "Controller:updateState");