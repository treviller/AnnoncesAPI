For the purpose of simplify your work, we bring you some functionalities via this RESTful API.

This API stay simple and easy to use.

Installation
============

1) Dependencies
-------------

This bundle use Guzzle client, the FOSRestBundle and the JMSSerializer for work. It's necessary to add these to your composer :

```console
$composer require guzzlehttp/guzzle ^6.2
```

```console
$composer require friendsofsymfony/rest-bundle
```

```console
$composer require jms/serializer-bundle
```

2) Registering
--------------

Next, you need to register them :

```php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new MeteoBundle\MeteoBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new FOS\RestBundle\FOSRestBundle(),
        );

        // ...
    }

    // ...
}
```


3) Routing
----------

You have to register the routing file of this bundle in your project file 'routing.yml' :

```yaml

    annonces:
        resource: "@AnnoncesBundle/Resources/config/routing.yml"
        prefix: /

```

Now you can use this bundle !

Authentication
==============

It do not have authentication system, but it could be implemented later.

Endpoints
=========

This API provides four distinct resources : Annonces, Villes, Categories and Photos.

Annonces
--------

This is a list of all availables URL :

- GET api/annonces 			-> Get a list of adverts

- GET api/annonces/{id}		-> Get a specific advert

- POST api/annonces			-> Post a new advert

- PUT api/annonces/{id} 	-> Edit an existing advert

- DELETE api/annonces/{id}  -> Delete an existing advert

### GET api/annonces

Get a list of adverts by category and/or city.

Results are paginated : return a maximum of 10 adverts.

####Parameters

URL parameters:
city			Search criteria
category		Search criteria
page			Requested page


### GET api/annonces/{id}

Get a specific advert by its unique id.

####Parameters

URL parameters :
id		unique id of an advert.


### POST api/annonces

Post a new advert. You must specify a category by its name or its id and it's the same for the city. If id and name are specified, only id will be used.
If successfully executed, will return this advert will its associated id.

####Parameters

POST parameters :
title			Title of the advert		
content			Content of this advert
prix			Price (optional)
category		Name of the category
id_category		Id of the category
city			Name of the city
id_city			Id of the city
id_photo_1		Picture associated to this advert (optional)
id_photo_2		Picture associated to this advert (optional)
id_photo_3		Picture associated to this advert (optional)


### PUT /api/annonces/{id}

Edit an existing advert. You must specify its id as url parameter. All others parameters will be send as JSON data.

####Parameters

URL parameters :
id				Id of an existing advert

JSON parameters :
title			Title of this advert		
content			Content of this advert
prix			Price (optional)
category		Name of the category
id_category		Id of the category
city			Name of the city
id_city			Id of the city
id_photo_1		Picture associated to this advert (optional)
id_photo_2		Picture associated to this advert (optional)
id_photo_3		Picture associated to this advert (optional)


### DELETE api/annonces/{id}

Delete an existing advert.

####Parameters

URL parameters:
id				Unique id of an advert.



Categories
----------

Categories endpoints :

- GET api/categories	-> Get a list of all availables categories

- POST api/categories	-> Add a new category

### GET api/categories

Get a list of all categories.

Like annonces, results are paginated, and return a maximum of 10 categories.

####Parameters

URL parameters:
page		Requested page of results


### POST ai/categories

Add a new category. If successfully executed, will return this category with its id.

####Parameters

POST parameters:
name	Name of this new category



Villes
======

This resource have only one endpoint :

- GET api/villes

### GET api/villes

Get a list of all cities which have at least one advert.
Like annonces and categories, results are paginated. A request can return a maximum of 10 cities.

####Parameters

URL parameters:
page		Requested page of results



Photos
======

Like cities, photos have only one endpoint available:

- POST api/photos

### POST api/photos

Add pictures to database. This will be marked as "unassociated" until you associate them with an advert, or until pictures expired. Expiration delay : 15 minutes.
If pictures were successfully added, this request will return their id to let you assiociate them in an advert.

####Parameters

POST parameters:
photo1		Picture you want to add to database
photo2		Picture you want to add to database (optional)
photo3		Picture you want to add to database (optional)
