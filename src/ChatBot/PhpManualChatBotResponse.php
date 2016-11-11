<?php

namespace ChatBot;

/**
 * Class PhpManualChatBotResponse
 * @package ChatBot
 */
class PhpManualChatBotResponse
{
    /**
     * @var string
     */
    private $versionInfo;

    /**
     * @var string
     */
    private $referenceName;

    /**
     * @var string
     */
    private $methodSynopsis;

    /**
     * @var string
     */
    private $methodDescription;

    /**
     * PhpManualChatBotResponse constructor.
     * @param string $versionInfo
     * @param string $referenceName
     * @param string $methodSynopsis
     * @param string $methodDescription
     */
    public function __construct(
        $versionInfo,
        $referenceName,
        $methodSynopsis,
        $methodDescription
    ) {
        $this->versionInfo = $versionInfo;
        $this->referenceName = $referenceName;
        $this->methodSynopsis = $methodSynopsis;
        $this->methodDescription = $methodDescription;
    }
}