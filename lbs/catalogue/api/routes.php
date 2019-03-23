<?php
/*
GET: /categories
Accéder aux catégories

Exemple de retour, HTTP/1.1 200 Ok
{
    "type" : "collection",
    "date" : "31-12-2017",
    "categories": [{
    	"id"  : 1,
    	"nom" : "bio",
    	"description" : "sandwichs ingrédients bio"
    },
    {
    	"id"  : 2,
    	"nom" : "bio",
    	"description" : "sandwichs végétariens - peuvent contenir des produits laitiers"
    },
	...
	]
}

Erreur: HTTP/1.1 404 Not Found
{
  "type" : "error',
  "error" : 404,
  "message" : ressource non disponible : /categories"
}
*/
$app->get('/categories', "Controller:categories");

/*
GET: /categories/sandwichs
Accéder aux catégories et ses sandwichs

Exemple de retour, HTTP/1.1 200 Ok
{
    "type" : "collection",
    "date" : "31-12-2017",
    "categories": [{
    	"id"  : 1,
    	"nom" : "bio",
    	"description" : "sandwichs ingrédients bio",
    	"sandwichs": [{
			"id": 5,
			"nom": "jambon-beurre",
			"description": "le jambon-beurre traditionnel, avec des cornichons",
			"img": null,
			"prix": "5.25"
    	}]
    },
    ...
    ]
}

Erreur: HTTP/1.1 404 Not Found
{
  "type" : "error',
  "error" : 404,
  "message" : ressource non disponible : /categories/sandwichs"
}
*/
$app->get('/categories/sandwichs', "Controller:categoriesSandwichs");

/*
GET: /categories/{id}
Accéder à la catégorie

Exemple: /categories/1

Exemple de retour, HTTP/1.1 200 Ok
{
    "type" : "resource",
    "date" : "31-12-2017",
    "categorie": {
    	"id"  : 1,
    	"nom" : "végétarien",
    	"description" : "sandwichs végétariens - peuvent contenir des produits laitiers"
    },
    "links": [
		"sandwichs": "/categories/1/sandwichs",
		"self": "/categories/1"
    ]
}

Erreur: HTTP/1.1 404 Not Found
{
  "type" : "error',
  "error" : 404,
  "message" : ressource non disponible : /categories/1"
}
*/
$app->get('/categories/{id}', "Controller:categorie");

/*
GET: /categories/{id}/sandwichs
Accéder aux sandwichs de la catégorie

Exemple: /categories/1/sandwichs

Exemple de retour, HTTP/1.1 200 Ok
{
    "type" : "ressource",
    "date" : "31-12-2017",
    "categorie": {
    	"id"  : 1,
    	"nom" : "bio",
    	"description" : "sandwichs ingrédients bio et locaux",
    	"sandwichs": [{
			"id": 5,
			"nom": "jambon-beurre",
			"description": "le jambon-beurre traditionnel, avec des cornichons",
			"img": null,
			"prix": "5.25"
    	}]
    }
}

Erreur: HTTP/1.1 404 Not Found
{
  "type" : "error',
  "error" : 404,
  "message" : ressource non disponible : /categories/1/sandwichs"
}
*/
$app->get('/categories/{id}/sandwichs', "Controller:categorieSandwichs");

/*
POST: /categories
Enregistre la catégorie

Exemple de body
{
    "nom" : "végétarien",
    "description" : "sandwichs végétariens"
}

Exemple de retour, HTTP/1.1 201 Created
{
    "type" : "resource",
    "date" : "31-12-2017",
    "categorie": {
    	"id"  : 1,
    	"nom" : "végétarien",
    	"description" : "sandwichs végétariens"
    },
}

Headers:
Location: /categories/1

Erreur: HTTP/1.1 400 Bad Request
{
  "type" : "error',
  "error" : 400,
  "message" : "Bad Request /categories"
}
*/
$app->post('/categories', "Controller:newCategorie");

/*
PUT: /categories/{id}
Modifie les données de la ressources

Exemple: /categories/1

Exemple de body
{
    "nom" : "végétarien",
    "description" : "sandwichs végétariens - peuvent contenir des produits laitiers"
}

Exemple de retour, HTTP/1.1 200 Ok
{
    "type" : "resource",
    "date" : "31-12-2017",
    "categorie": {
    	"id"  : 1,
    	"nom" : "végétarien",
    	"description" : "sandwichs végétariens - peuvent contenir des produits laitiers"
    },
}

Erreurs: 
HTTP/1.1 400 Bad Request
{
  "type" : "error',
  "error" : 400,
  "message" : "Bad Request /categories/1"
}

HTTP/1.1 404 Not Found
{
  "type" : "error',
  "error" : 404,
  "message" : ressource non disponible : /categories/1"
}
*/
$app->put('/categories/{id}', "Controller:updateCategorie");

/*
GET: /sandwichs
Accéder aux sandwichs

Filtres possibles:
type_pain, prixMax et page

Exemple: /sandwichs?type_pain=baguette

Exemple de retour, HTTP/1.1 200 Ok
{
    "type" : "collection",
    "date" : "31-12-2017",
    "sandwichs": [{
			"id": 4,
			"nom": "le bucheron",
			"description": "un sandwich de bucheron: frites, fromage, saucisse, steack, lard grillé, mayo",
			"type_pain": "baguette campagne",
			"img": null,
			"prix": "6.00"
	    },
	    {
			"id": 5,
			"nom": "jambon-beurre",
			"description": "le jambon-beurre traditionnel, avec des cornichons",
			"type_pain": "baguette",
			"img": null,
			"prix": "5.25"
	    },
	    ...
	]
}

Erreur: HTTP/1.1 404 Not Found
{
  "type" : "error',
  "error" : 404,
  "message" : ressource non disponible : /sandwichs"
}
*/
$app->get('/sandwichs', "Controller:sandwichs");

/*
GET: /sandwichs/{id}
Accéder au sandwich

Exemple: /sandwichs/4

Exemple de retour, HTTP/1.1 200 Ok
{
    "type" : "resource",
    "date" : "31-12-2017",
    "sandwich": {
    	"id"  : 4,
    	"nom" : "le bucheron",
    	"description": "un sandwich de bucheron: frites, fromage, saucisse, steack, lard grillé, mayo",
		"type_pain": "baguette campagne",
		"img": null,
		"prix": "6.00"
    }
}

Erreur: HTTP/1.1 404 Not Found
{
  "type" : "error',
  "error" : 404,
  "message" : ressource non disponible : /sandwichs/4"
}
*/
$app->get('/sandwichs/{id}', "Controller:sandwich");

/*
POST: /sandwichs
Enregistre le sandwich

Exemple de body
{
    "nom" : "le bucheron",
    "description": "un sandwich de bucheron",
	"type_pain": "baguette campagne",
	"prix": "6.00"
}

Exemple de retour, HTTP/1.1 201 Created
{
    "type" : "resource",
    "date" : "31-12-2017",
    "sandwich": {
    	"id"  : 4,
    	"nom" : "le bucheron",
    	"description": "un sandwich de bucheron",
		"type_pain": "baguette campagne",
		"img": null,
		"prix": "6.00"
    }
}

Headers:
Location: /sandwichs/4

Erreur: HTTP/1.1 400 Bad Request
{
  "type" : "error',
  "error" : 400,
  "message" : "Bad Request /sandwichs"
}
*/
$app->post('/sandwichs', "Controller:newSandwich");

/*
PUT: /sandwichs/{id}
Modifie les données de la ressources

Exemple: /sandwichs/4

Exemple de body
{
    "nom" : "le bucheron",
    "description": "un sandwich de bucheron: frites, fromage, saucisse, steack, lard grillé, mayo",
	"type_pain": "baguette campagne",
	"prix": "6.00"
}

Exemple de retour, HTTP/1.1 200 Ok
{
    "type" : "resource",
    "date" : "31-12-2017",
    "sandwich": {
    	"id"  : 4,
    	"nom" : "le bucheron",
    	"description": "un sandwich de bucheron: frites, fromage, saucisse, steack, lard grillé, mayo",
		"type_pain": "baguette campagne",
		"img": null,
		"prix": "6.00"
    }
}

Erreurs: 
HTTP/1.1 400 Bad Request
{
  "type" : "error',
  "error" : 400,
  "message" : "Bad Request /sandwichs/4"
}

HTTP/1.1 404 Not Found
{
  "type" : "error',
  "error" : 404,
  "message" : ressource non disponible : /sandwichs/1"
}
*/
$app->put('/sandwichs/{id}', "Controller:updateSandwich");

/*
DELETE: /sandwichs/{id}
Supprime la ressources

Exemple: /sandwichs/4

Exemple de retour, HTTP/1.1 204 No Content

Erreurs: 
HTTP/1.1 400 Bad Request
{
  "type" : "error',
  "error" : 400,
  "message" : "Bad Request /sandwichs/4"
}

HTTP/1.1 404 Not Found
{
  "type" : "error',
  "error" : 404,
  "message" : ressource non disponible : /sandwichs/4"
}
*/
$app->delete('/sandwichs/{id}', "Controller:deleteSandwich");