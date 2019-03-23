<?php

/*
POST: /commands
Enregistre une commande

Exemple de body, HTTP/1.1 201 Created
{
    "nom": "Maillard",
    "mail": "louis@mail.com",
    "livraison": {
		"date": "2019-03-23",
		"livraison": "09:00:00"
    },
    "items":[{
		"uri": "/sandwichs/4", "q": 1
   	},
   	{
		"uri": "/sandwichs/5", "q": 2
   	},
   	...
   	]
}

Si le token JWT est présent dans le header Authorization, la commande sera liée à l'utilisateur

Exemple de retour, HTTP/1.1 201 Created
{
    "type": "resource",
    "date": "23-03-2019",
    "commande": {
        "nom": "Maillard",
        "mail": "louis@mail.com",
        "livraison": {
            "date": "2019-03-23",
            "heure": "09:00:00"
        },
        "id": "7d34faaa-4d7a-11e9-8536-0242ac130007",
        "token": "f07e12217e66f313f0e320f877780a3559d320b77473d8db5a308edf6aa2fc7d",
        "montant": 11.25,
        "items": [
            {
                "uri": "/sandwichs/4",
                "q": 1
            },
            {
                "uri": "/sandwichs/5",
                "q": 2
            }
        ]
    }
}

Erreurs: 
HTTP/1.1 400 Bad Request
{
  "type" : "error',
  "error" : 400,
  "message" : "Bad Request /commands"
}

*/
$app->post('/commands', "Controller:newCommand");

/*
GET: /commands/{id}
Accéde à une commande

Exemple: /commands/7d34faaa-4d7a-11e9-8536-0242ac130007

Le token retourné lors de la création doit être présent dans le paramètre de l'url "token" ou dans le header X-lbs-token

Retour, HTTP/1.1 200 Ok
{
    "type": "resource",
    "date": "23-03-2019",
    "command": {
        "id": "7d34faaa-4d7a-11e9-8536-0242ac130007",
        "created_at": "2019-03-23 14:46:49",
        "updated_at": "2019-03-23 14:46:49",
        "livraison": "2019-03-23 09:00:00",
        "montant": "11.25",
        "remise": null,
        "token": "f07e12217e66f313f0e320f877780a3559d320b77473d8db5a308edf6aa2fc7d",
        "status": 1
    },
    "links": {
        "self": "/commands/7d34faaa-4d7a-11e9-8536-0242ac130007",
        "items": "/commands/7d34faaa-4d7a-11e9-8536-0242ac130007/items"
    }
}

Erreurs:
HTTP/1.1 401 Unauthorized
{
    "type": "error",
    "error": "401",
    "message": "Token missing or wrong"
}

HTTP/1.1 404 Not Found
{
  "type" : "error',
  "error" : 404,
  "message" : "Not Found /commands/7d34faaa-4d7a-11e9-8536-0242ac130007"
}

*/
$app->get('/commands/{id}', "Controller:command");

/*
PUT: /commands/{id}
Modification d'une commande

2 filtres sont utilisables: update=pay ou update=date en fonction de ce qu'on veut modifier

Exemple: /commands/7d34faaa-4d7a-11e9-8536-0242ac130007?update=pay

Exemple de body
{
	"ref_paiement": ...,
	"date_paiement": ...,
	"mode_paiement": ...
}

Retour, HTTP/1.1 204 No Content

Token JWT dans le header Authorization pour le système de fidelisation du client

Erreurs: 
HTTP/1.1 401 Unauthorized
{
	"type":"error",
	"error":"401",
	"message":"Header Authorization missing or wrong"
}

HTTP/1.1 404 Not Found
{
  "type" : "error',
  "error" : 404,
  "message" : "Not Found /commands/7d34faaa-4d7a-11e9-8536-0242ac130007"
}
*/
$app->put('/commands/{id}', "Controller:updateCommand");

/*
POST: /clients/{id}/auth
Authentification du client

Exemple de body
{
	"mail": "louis@mail.com",
	"password": "louis"
}

Exemple de retour, HTTP/1.1 201 Created
{
    "type": "resource",
    "date": "22-03-2019",
    "client": {
    	"id": 5,
        "mail": "louis@mail.com"
    },
    "links": [{
    	"self": "/clients/5"
    },
	{
		"commands": "/clients/5/commands"
	}]
}

Token JWT dans le header Authorization

Erreurs: 
HTTP/1.1 401 Unauthorized
{
  "type" : "error',
  "error" : 400,
  "message" : "Bad Request /clients/5/auth"
}
*/
$app->post('/clients/{id}/auth', "Controller:login");

/*
POST: /clients
Enregistre le client

Exemple de body
{
	"mail": "louis@mail.com",
	"password": "louis"
}

Retour, HTTP/1.1 201 Created

Erreurs: 
HTTP/1.1 400 Bad Request
{
  "type" : "error',
  "error" : 400,
  "message" : "Bad Request /clients"
}

HTTP/1.1 400 Bad Request
{
  "type" : "error',
  "error" : 400,
  "message" : "Mail déjà utilisé"
}
*/
$app->post('/clients', "Controller:register");

/*
GET: /clients/{id}
Accéde à un client

Exemple: /clients/5

Le token JWT doit être présent dans le header Authorization

Retour, HTTP/1.1 200 Ok
{
    "type": "resource",
    "date": "23-03-2019",
    "client": {
        "id": 5,
        "mail": "louis@mail.com",
        "cumul": 0,
        "created_at": "2019-03-22 00:00:00"
    },
    "links": {
        "self": "/client/5",
        "commands": "/client/5/commands"
    }
}

Erreurs:
HTTP/1.1 401 Unauthorized
{
	"type":"error",
	"error":"401",
	"message":"Header Authorization missing or wrong"
}

HTTP/1.1 404 Not Found
{
  "type" : "error',
  "error" : 404,
  "message" : "Not Found /clients/5"
}

*/
$app->get('/clients/{id}', "Controller:profile");

/*
GET: /clients/{id}/commands
Accéde aux commandes du client

Exemple: /clients/5/commands

Le token JWT doit être présent dans le header Authorization

Retour, HTTP/1.1 200 Ok
{
    "type": "collection",
    "date": "23-03-2019",
    "commands": [
        {
            "id": "7d34faaa-4d7a-11e9-8536-0242ac130007",
            "created_at": "2019-03-23 14:46:49",
            "updated_at": "2019-03-23 14:46:49",
            "livraison": "2019-03-23 09:00:00",
            "montant": "11.25",
            "remise": null,
            "token": "f07e12217e66f313f0e320f877780a3559d320b77473d8db5a308edf6aa2fc7d",
            "status": 1
        }
    ]
}

Erreurs:
HTTP/1.1 401 Unauthorized
{
	"type":"error",
	"error":"401",
	"message":"Header Authorization missing or wrong"
}

HTTP/1.1 404 Not Found
{
  "type" : "error',
  "error" : 404,
  "message" : "Not Found /clients/5/commands"
}

*/
$app->get('/clients/{id}/commands', "Controller:commands");