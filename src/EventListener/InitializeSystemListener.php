<?php

declare(strict_types=1);

namespace Hhcom\ContaoEventNewsRegistration\EventListener;

use Contao\CoreBundle\Routing\ScopeMatcher;
use Symfony\Component\HttpFoundation\RequestStack;
use Contao\Config;
use Contao\System;

class InitializeSystemListener
{
    protected $requestStack;
    private $scopeMatcher;

    public function __construct(RequestStack $requestStack, ScopeMatcher $scopeMatcher)
    {
        $this->requestStack = $requestStack;
        $this->scopeMatcher = $scopeMatcher;
    }

    public function __invoke(): void
    {
        if ($this->requestStack->getCurrentRequest()) {
            if ($this->scopeMatcher->isBackendRequest($this->requestStack->getCurrentRequest())) {
                $GLOBALS['TL_CSS'][] = 'bundles/contaoeventnewsregistration/css/be.css|static';
                $GLOBALS['TL_BODY'][] = '<script src="bundles/contaoeventnewsregistration/js/be.js"></script>';
            } else {
                if ( ! Config::get("deactivateJS")) {
                    $GLOBALS['TL_BODY'][] = '<script src="bundles/contaoeventnewsregistration/js/fe.js"></script>';
                }
                if ( ! Config::get("deactivateCSS")) {
                    $GLOBALS['TL_CSS'][] = 'bundles/contaoeventnewsregistration/css/fe.css|static';
                }
            }
        }
    }
}

?>