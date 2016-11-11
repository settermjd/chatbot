<?php

namespace ChatBot;

use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Service\Client;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class PhpManualChatBot
 * @package ChatBot
 */
class PhpManualChatBot
{
    const MANUAL_URI_PATTERN = 'http://php.net/manual/de/function.%s.php';
    const XPATH_VERSION_INFO = '//p[@class="verinfo"]';
    const XPATH_REFERENCE_NAME = '//span[@class="refname"]';
    const XPATH_METHOD_SYNOPSIS = '//div[@class="methodsynopsis dc-description"]';
    const XPATH_METHOD_DESCRIPTION = '//p[@class="para rdfs-comment"]';

    /**
     * @var Client
     */
    private $client;

    /**
     * PhpManualChatBot constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Looks up the details of a function in the PHP manual
     *
     * @param string $functionName
     * @return PhpManualChatBotResponse
     */
    public function lookupFunction($functionName)
    {
        try {
            /** @var \Guzzle\Http\Message\Response $response */
            $response = ($this->client->get(sprintf(
                self::MANUAL_URI_PATTERN, $functionName))
            )->send();
        } catch (ClientErrorResponseException $e) {
            throw new FunctionNotFoundException(sprintf(
                "The function '%s' was not found in the PHP manual", $functionName
            ));
        }

        $crawler = new Crawler($response->getBody(true));

        return new PhpManualChatBotResponse(
            $crawler->filterXPath(self::XPATH_VERSION_INFO)->text(),
            $crawler->filterXPath(self::XPATH_REFERENCE_NAME)->text(),
            $crawler->filterXPath(self::XPATH_METHOD_SYNOPSIS)->text(),
            $crawler->filterXPath(self::XPATH_METHOD_DESCRIPTION)->text()
        );
    }
}