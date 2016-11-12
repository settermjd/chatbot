<?php

namespace ChatBot;

use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Service\ClientInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class PhpManualChatBot
 * @package ChatBot
 */
class PhpManualChatBot
{
    const MANUAL_URI_PATTERN = 'http://php.net/manual/en/function.%s.php';
    const XPATH_VERSION_INFO = '//p[@class="verinfo"]';
    const XPATH_REFERENCE_NAME = '//span[@class="refname"]';
    const XPATH_METHOD_SYNOPSIS = '//div[@class="methodsynopsis dc-description"]';
    const XPATH_METHOD_DESCRIPTION = '//p[@class="para rdfs-comment"]';

    /**
     * @var Client
     */
    private $client;
    private $crawler;

    /**
     * PhpManualChatBot constructor.
     * @param ClientInterface $client
     * @param CrawlerInterface $crawler
     */
    public function __construct(ClientInterface $client, Crawler $crawler)
    {
        $this->client = $client;
        $this->crawler = $crawler;
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

        $this->crawler->add($response->getBody(true));

        return new PhpManualChatBotResponse(
            $this->crawler->filterXPath(self::XPATH_VERSION_INFO)->text(),
            $this->crawler->filterXPath(self::XPATH_REFERENCE_NAME)->text(),
            $this->crawler->filterXPath(self::XPATH_METHOD_SYNOPSIS)->text(),
            $this->crawler->filterXPath(self::XPATH_METHOD_DESCRIPTION)->text()
        );
    }
}