<?php


namespace App\Tests;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ComponentAPITest extends WebTestCase
{
    private $client;

    function setUp(): void
    {
        $this->client = static::createClient();
    }

    protected function callRoute($method, $route, $data = [])
    {
        $this->client->request(
            $method,
            $route,
            [], [],
            ['HTTP_Accept'=> 'application/json', 'HTTP_Authorization'=> "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE1OTA0ODk5NDYsImV4cCI6MTU5MDQ5NzE0Niwicm9sZXMiOlsiUk9MRV9BRE1JTiIsIlJPTEVfVVNFUiJdLCJ1c2VybmFtZSI6ImFkbWluQGFkbWluLmNvIn0.aMQa5UvqQhNuLWNe5tEerTH6G_IsECbl2-w6DZG9MT2F5kpP1Z2pHnz3psdIxM0_3BVX7JWr32B6LyQVv5c2SdjmJi6fz-rlZTPmaRsSdNN1qIOqfFwIMbj8muB8lgVnRUFqO8FGp4zstVsCueWr0kCETxi_YG3vvbDmkE3i5rVEsQi9JC45XOdm0YFfbCj3wve88iO1oxnC-7rL50qlX_jthf8bUM8gBrosmwjWmCvC9oTU7Topuh5Nb-vZv9rjhMMqNLqBLRw9hUZ2f5c1XqX-ST1r4FWkFHWSC7leUksvKekF4UhH2kyub2_mN7vCZLi-9jo35UsOZkCFNlQ4qGkK9IUo0VGcNTZskzx2n07o5lYG-5YSoN6t_QZ0djvd9iO7FXoqg0LH-Nekd9Dn6kZ1XPAy-tY4AETWo45SYm_J9RrWmlU-wXxDRYrE2hJXOev1adphMrRz9AgZ13jBg-1XnOCiXUvGiVcebsJ431lohGRJC-QIdAM67XS-sKVUo1KeBSEJYVylnd4r90YyudgOEtUFIRnPCiqPcHOeLMrq48k3lHSvaRJkMQiae8kKTaUGHFZwGa4DrakxYe2Nb5UfbxLwEx2D0aiS4-DWPv0NPtDUu1_lWOxHxSThkS2iGjIQgaGJtuQ1W6epNccTQTZwzpT8frzQ1TW7F4i5ymE"]
            ,json_encode($data)
        );
    }

    public function testGetOneComponentFromAPIRoute()
    {

        $this->callRoute('GET','/api/components/15');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->callRoute('GET','/api/components/15');
        $this->assertEquals(15, json_decode($this->client->getResponse()->getContent())->id);

        $this->callRoute('GET','/api/components/100');
        $this->assertEquals(Response::HTTP_NOT_FOUND,  $this->client->getResponse()->getStatusCode());

        $this->callRoute('GET','/api/components/100');
        $this->assertEquals('Ce composant n\'existe pas !',  json_decode($this->client->getResponse()->getContent()));

    }

    public function testGetAllComponentsFromAPIRoute()
    {

        $this->callRoute('GET','/api/components');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->callRoute('GET','/api/components');
        $this->assertEquals('array', gettype(json_decode($this->client->getResponse()->getContent())));

    }

    public function testGetAllComponentsByTypeFromAPIRoute()
    {

        $this->callRoute('GET','/api/components/byType');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->callRoute('GET','/api/components/byType');
        $this->assertEquals('object', gettype(json_decode($this->client->getResponse()->getContent())));

    }

    public function testUpdateComponentWithAPIRoute()
    {
        $this->callRoute('PUT','/api/components/2');
        $this->assertEquals(Response::HTTP_NOT_FOUND,  $this->client->getResponse()->getStatusCode());

        $this->callRoute('GET','/api/components/2');
        $this->assertEquals('Ce composant n\'existe pas !',  json_decode($this->client->getResponse()->getContent()));

    }

//    public function testDeleteComponentWithAPIRoute()
//    {
//        $this->callRoute('DELETE','/api/components/14');
//        $this->assertEquals(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());
//
//        $this->callRoute('DELETE','/api/components/14');
//        $this->assertEquals('', $this->client->getResponse()->getContent());
//
//        $this->callRoute('DELETE','/api/components/100');
//        $this->assertEquals(Response::HTTP_NOT_FOUND,  $this->client->getResponse()->getStatusCode());
//
//        $this->callRoute('DELETE','/api/components/100');
//        $this->assertEquals('Ce composant n\'existe pas !',  json_decode($this->client->getResponse()->getContent()));
//
//    }

}