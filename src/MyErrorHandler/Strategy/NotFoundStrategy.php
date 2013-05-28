<?php

/**
 *
 * @author Stefano Torresi <webdeveloper@stefanotorresi.it>
 */

namespace MyErrorHandler\Strategy;

use Zend\EventManager\EventManagerInterface;
use Zend\Http\Header\Accept as AcceptHeader;
use Zend\I18n\Translator\Translator;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\View\Http\RouteNotFoundStrategy;
use Zend\Stdlib\ResponseInterface;
use Zend\View\Model;
use MyErrorHandler\Module as MyErrorHandler;

class NotFoundStrategy extends RouteNotFoundStrategy implements StrategyInterface
{
    const DEFAULT_MESSAGE = 'Page not found.';

    /**
     * @var Translator
     */
    protected  $translator;

    /**
     *
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
                MvcEvent::EVENT_DISPATCH, array($this, 'prepareNotFoundViewModel'), -99);
        $this->listeners[] = $events->attach(
                MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'prepareNotFoundViewModel'), -99);
        $this->listeners[] = $events->attach(
                MvcEvent::EVENT_DISPATCH, array($this, 'setViewModelErrorFlag'), -100);
        $this->listeners[] = $events->attach(
                MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'setViewModelErrorFlag'), -100);
    }

    /**
     * we may want to inform the view layer that we just have an error
     *
     * @param  MvcEvent $e
     * @return void
     */
    public function setViewModelErrorFlag(MvcEvent $e)
    {
        $error = $e->getError();
        $response = $e->getResponse();

        if ( empty($error) && $response->getStatusCode() != 404) {
            return;
        }

        if (!isset($e->getViewModel()->error)) {
            $e->getViewModel()->error = true;
        }
    }

    /**
     * Create and return a 404 view model
     *
     * @param  MvcEvent $e
     * @return void
     */
    public function prepareNotFoundViewModel(MvcEvent $e)
    {

        // Do nothing if the result is a response object
        $result = $e->getResult();
        if ($result instanceof ResponseInterface) {
            return;
        }

        $response = $e->getResponse();
        if ($response->getStatusCode() != 404) {
            // Only handle 404 responses
            return;
        }

        $request = $e->getRequest();
        $accept = $request->getHeader('Accept');

        if ($accept instanceof AcceptHeader &&
                0 === strpos($accept->getFieldValue(), 'application/json')) {
            $renderer = MyErrorHandler::RENDERER_JSON;
        } else {
            $renderer = MyErrorHandler::RENDERER_HTML;
        }

        if ($renderer == MyErrorHandler::RENDERER_HTML && !$request->isXmlHttpRequest()) {
            // Only handle XHR requests if output is HTML
            return;
        }

        $message = self::DEFAULT_MESSAGE;

        switch ($renderer) {
            case MyErrorHandler::RENDERER_JSON :
                $model = new Model\JsonModel();

                if ($this->translator) {
                    $message = $this->translator->translate($message, MyErrorHandler::TEXT_DOMAIN);
                }

                $model->setVariable('error', array(
                    'code'      => $response->getStatusCode(),
                    'message'   => $message,
                ));

                break;

            case MyErrorHandler::RENDERER_HTML :
            default :
                $model = new Model\ViewModel;
                $variables = $e->getViewModel()->getVariables();
                $model->setVariables($variables);
                $model->setVariables(array(
                            'message'   => $message,
                            'error'     => true
                        ))
                        ->setTemplate('error/plain')
                        ->setTerminal(true);
        }

        $e->setViewModel($model);

    }

    /**
     * @param Translator $translator
     */
    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;
    }
}
