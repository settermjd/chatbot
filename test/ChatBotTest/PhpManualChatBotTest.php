<?php

namespace ChatBotTest;

use ChatBot\PhpManualChatBot;
use ChatBot\PhpManualChatBotResponse;
use Guzzle\Tests\GuzzleTestCase;
use Guzzle\Service\Client as ServiceClient;
use Symfony\Component\DomCrawler\Crawler;

class PhpManualChatBotTest extends GuzzleTestCase
{
    private $client;
    private $chatBot;

    public function setUp()
    {
        $this->client = new ServiceClient();
        $this->setMockBasePath('./test/mock/responses');
    }

    public function testChatBotCanBeInitialised()
    {
        $this->setMockResponse($this->client, array('response-mysql-query'));
        $functionName = 'mysql-query';
        $this->chatBot = new PhpManualChatBot($this->client);

        $responseObject = new PhpManualChatBotResponse(
            '(PHP 4, PHP 5)',
            'mysql_query',
            'mixed mysql_query ( string $query [, resource $link_identifier = NULL ] )',
            'mysql_query() sendet eine einzelne Abfrage (mehrere Abfragen werden nicht unterstützt) zu dem momentan aktiven Schema auf dem Server, der mit der übergebenen Verbings-Kennung Verbindungs-Kennung assoziiert ist.'
        );

        $response = $this->chatBot->lookupFunction($functionName);
        $this->assertEquals($responseObject, $response);
    }

    /**
     * @expectedException \ChatBot\FunctionNotFoundException
     * @expectedExceptionMessage The function 'mysql-query' was not found in the PHP manual
     */
    public function testChatBotCanHandle404Response()
    {
        $this->setMockResponse($this->client, array('response-404'));
        $functionName = 'mysql-query';
        $this->chatBot = new PhpManualChatBot($this->client);

        $this->chatBot->lookupFunction($functionName);
    }

    public function testRequests()
    {
        $this->setMockResponse($this->client, array('response-mysql-query'));
        $request = $this->client->get(
            'http://php.net/manual/de/function.mysql-query.php'
        );

        /** @var \Guzzle\Http\Message\Response $response */
        $response = $request->send();

        $this->assertContainsOnly($request, $this->getMockedRequests());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('php.net', $response->getServer());
        $this->assertEquals('application/html', $response->getContentType());
        $crawler = new Crawler($response->getBody(true));
        $crawler = $crawler->filterXPath('//p[@class="verinfo"]');
        var_dump($crawler->first()->html());
    }
}