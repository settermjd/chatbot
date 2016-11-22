<?php

namespace ChatBot;

use Zend\Filter\PregReplace;
use Zend\Filter\StringTrim;
use Zend\Filter\StripNewlines;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;

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
        $this->versionInfo = $this->filterParameter($versionInfo);
        $this->referenceName = $this->filterParameter($referenceName);
        $this->methodSynopsis = $this->filterParameter($methodSynopsis);
        $this->methodDescription = $this->filterParameter($methodDescription);
    }

    /**
     * Clean up a parameter, removing unnecessary information
     * @param string $parameter
     * @return string
     */
    private function filterParameter($parameter)
    {
        $input = new Input('parameter');
        $input->getFilterChain()
            ->attach(new StringTrim())
            ->attach(new StripNewlines())
            ->attach(new PregReplace([
                'pattern' => '!\s+!',
                'replacement' => ' '
            ]));

        $inputFilter = new InputFilter();
        $inputFilter->add($input);
        $inputFilter->setData(['parameter' => $parameter]);

        return $inputFilter->getValue('parameter');
    }

   /**
    * @return string
    */
   public function getVersionInfo() {
      return $this->versionInfo;
   }

   /**
    * @return string
    */
   public function getReferenceName() {
      return $this->referenceName;
   }

   /**
    * @return string
    */
   public function getMethodSynopsis() {
      return $this->methodSynopsis;
   }

   /**
    * @return string
    */
   public function getMethodDescription() {
      return $this->methodDescription;
   }
}