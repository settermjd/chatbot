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
        $crawler = new Crawler();
        $this->chatBot = new PhpManualChatBot($this->client, $crawler);

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
        $crawler = new Crawler();
        $this->chatBot = new PhpManualChatBot($this->client, $crawler);

        $this->chatBot->lookupFunction($functionName);
    }
}