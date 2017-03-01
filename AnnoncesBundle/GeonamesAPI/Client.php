<?php
namespace AnnoncesBundle\GeonamesAPI;

use Symfony\Component\BrowserKit\Client as BaseClient;
use GuzzleHttp\Client as GuzzleClient;


class Client extends BaseClient
{
	protected $baseURI;

	public function __construct($baseURI)
	{
		$this->setBaseURI($baseURI);
	}

	public function send($request)
	{
		return $this->doRequest($request);
	}

	protected function doRequest($request)
	{
		$client = $this->getClient();

		return $client->request($request->getMethod(), $request->getUri());
	}

	protected function getClient()
	{
		return new GuzzleClient(array('base_uri' => $this->baseURI));
	}

	public function setBaseURI($baseURI)
	{
		$this->baseURI = $baseURI;
	}
}