<?php
namespace AnnoncesBundle\GeonamesAPI;

use GuzzleHttp\Psr7\Request;

class GeonamesInterface
{
	private $client;
	protected $baseURI = 'api.geonames.org/';
	
	public function __construct()
	{
		$this->client = new Client($this->baseURI);
	}
	
	public function checkCity($name)
	{
		$uri = 'search?name_equals='.$name.'&country=FR&type=json&username=treviller';

		$response = $this->client->send(new Request('GET', $uri));
		
		$body = $response->getBody();
			
		$result = json_decode($body, true);

		if(!empty($result['geonames']) && $result['geonames'][0]['name'] === $name)
			return true;
		else 
			return false;
	}
}
