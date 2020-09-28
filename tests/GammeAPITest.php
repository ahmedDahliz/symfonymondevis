<?php


namespace App\Tests;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class GammeAPITest extends WebTestCase
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
            ['HTTP_Accept'=> 'application/json', 'HTTP_Authorization'=> "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE1OTAxNjc0OTgsImV4cCI6MTU5MDE3NDY5OCwicm9sZXMiOlsiUk9MRV9BRE1JTiIsIlJPTEVfVVNFUiJdLCJ1c2VybmFtZSI6ImFkbWluQGFkbWluLmNvIn0.J0JwoW_hSS62diBz6rco82WR7SfiZQ2_MAvNIB6g-fltj-FQmEkMBn2uYLpxEYPnBNDWKXWz65KqMioorqwhVUMX9UYf0ExytzQOuC6_NIK4uD4iNppcfLxwT1di51yR_I0Y6WHc4hrWrCyilWzO3xIIuUIi6_UKp1vEU7EfHOirzIhVQ0LebLLey5DYJBBh6yL9g1PAUOnTixgSw6oLFMUMjf2_LJrJeRo4fMJJJsrQV7_NzDSOZNZxweIBxAiXsNIVBZBtTvjTfdkLM7cCXScXlBhVAFp_5lkeoWyRcovVhYLlGkCpwtTRbgx7qZ1wm0u2RhlR3LfhYCgvUXya98Q0FEad_g4D6GwoLSPpty82InLsB3m2wFATnc9OYzYqTN-dXEksvkyGnG1N84mY7UJ_dRYsrrCKK4nphOaCMSzdmnoG7i5C_ezgfGUCmCQcvWtw6DWehY1aN0-7zGR2s6P-hJxc-ijvjV9b3-NrdSFODm9MiWYDYGDfosFyvnu1HtJ-jMfJNUH43H-9lJAL7BAsvBrWWIjYJei0acaGU5NwFOuaEgMmer1ARCjo-YWCivajFDCQG9RCZfux-3dGYaF9d2V3iy30iSAJDMwzRlmgfXQCNxCpc0yVmC0H8PSdqQ6bo2I2eVouzF903EQecRzZ225xMgZ647Yd_QjgTYM"]
            ,json_encode($data)
        );
    }

    public function testGetOneComponentFromAPIRoute()
    {

        $this->callRoute('GET','/api/panel/1');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->callRoute('GET','/api/panel/1');
        $this->assertEquals(1, json_decode($this->client->getResponse()->getContent())->id);

        $this->callRoute('GET','/api/panel/100');
        $this->assertEquals(Response::HTTP_NOT_FOUND,  $this->client->getResponse()->getStatusCode());

        $this->callRoute('GET','/api/panel/100');
        $this->assertEquals('Cet panneau n\'existe pas !',  json_decode($this->client->getResponse()->getContent()));

    }

    public function testGetAllComponentsFromAPIRoute()
    {

        $this->callRoute('GET','/api/panel');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->callRoute('GET','/api/panel');
        $this->assertEquals('array', gettype(json_decode($this->client->getResponse()->getContent())));

    }

    public function testUpdateComponentWithAPIRoute()
    {
        $this->callRoute('PUT','/api/panel/2');
        $this->assertEquals(Response::HTTP_NOT_FOUND,  $this->client->getResponse()->getStatusCode());

        $this->callRoute('GET','/api/panel/2');
        $this->assertEquals('Cet panneau n\'existe pas !',  json_decode($this->client->getResponse()->getContent()));

    }

    public function testDeleteComponentWithAPIRoute()
    {
        $this->callRoute('DELETE','/api/panel/1');
        $this->assertEquals(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());

        $this->callRoute('DELETE','/api/panel/1');
        $this->assertEquals('', $this->client->getResponse()->getContent());

        $this->callRoute('DELETE','/api/panel/100');
        $this->assertEquals(Response::HTTP_NOT_FOUND,  $this->client->getResponse()->getStatusCode());

        $this->callRoute('DELETE','/api/panel/100');
        $this->assertEquals('Cet panneau n\'existe pas !',  json_decode($this->client->getResponse()->getContent()));

    }

}
