<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyErrorHandler\Strategy;

use Zend\I18n\Translator\Translator;

interface StrategyInterface
{
    public function setTranslator(Translator $translator);
}
