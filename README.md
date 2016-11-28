For the purpose of simplify your work, we bring you some functionalities via this RESTful API.

This API stay simple and easy to use.

Authentication
==============

It do not have authentication system, but it could be implemented later.

Endpoints
=========

This API provides two distinct resources : Annonces, and Categories.

Annonces
--------

This is a list of all availables URL :

- GET api/annonces 			-> Get a list of annonces

- GET api/annonces/{id}		-> Get a specific annonce

- POST api/annonces			-> Post a new annonce

- PUT api/annonces/{id} 	-> Edit an existing annonce

- DELETE api/annonces/{id}  -> Delete an existing annonce

### GET api/annonces

Get a list of annonces by category and/or city.

Results are paginated : return a maximum of 10 annonces.

####Parameters

city		Search criteria
category	Search criteria
page		Requested page


### GET api/annonces/{id}

Get a specific annonce by its unique id.

####Parameters

id		unique id of an annonce.


### POST api/annonces

Post a new annonce.

####Parameters

title		
content
prix
category
city
photos


### PUT /api/annonces/{id}

Edit an existing annonce.

####Parameters

id
title		
content
prix
category
city
photos

### DELETE api/annonces/{id}

Delete an existing annonce.

####Parameters

id		unique id of an annonce.


Categories
----------

Categories endpoints :

- GET api/categories	-> Get a list of all availables categories

- POST api/categories	-> Add a new category

### GET api/categories

Get a list of all categories.

Like annonces, results are paginated, and return a maximum of 10 categories.

####Parameters

page		Requested page


### POST ai/categories

Add a new category

####Parameters

name	name of this new category