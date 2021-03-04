<?php

/*
 * This file is part of the Thelia package.
 * http://www.thelia.net
 *
 * (c) OpenStudio <info@thelia.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Thelia\Core\Stack;

use Propel\Runtime\ActiveQuery\ModelCriteria;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Thelia\Core\HttpFoundation\Request as TheliaRequest;
use Thelia\Core\Translation\Translator;
use Thelia\Log\Tlog;
use Thelia\Model\ConfigQuery;
use Thelia\Model\Lang;
use Thelia\Model\LangQuery;

/**
 * Class ParamInitMiddleware.
 *
 * @author manuel raynaud <manu@raynaud.io>
 */
class ParamInitMiddleware implements HttpKernelInterface
{
    /**
     * @var HttpKernelInterface
     */
    protected $app;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    public function __construct(HttpKernelInterface $app, Translator $translator, EventDispatcherInterface $eventDispatcher)
    {
        $this->app = $app;
        $this->translator = $translator;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        if ($type == HttpKernelInterface::MASTER_REQUEST) {
            $response = $this->initParam($request);

            if ($response instanceof Response) {
                return $response;
            }
        }

        return $this->app->handle($request, $type, $catch);
    }

    protected function initParam(TheliaRequest $request)
    {
        $lang = $this->detectLang($request);

        if ($lang instanceof Response) {
            return $lang;
        }

        if ($lang) {
            $request->getSession()->setLang($lang);
        }

        return null;
    }

    /**
     * @return \Thelia\Model\Lang|null
     */
    protected function detectLang(TheliaRequest $request)
    {
        // first priority => lang parameter present in request (get or post)
        $requestedLangCodeOrLocale = $request->query->get('lang');

        // add a fallback on locale parameter
        if (null === $requestedLangCodeOrLocale) {
            $requestedLangCodeOrLocale = $request->query->get('locale');
        }

        // The lang parameter may contains a lang code (fr, en, ru) for Thelia < 2.2,
        // or a locale (fr_FR, en_US, etc.) for Thelia > 2.2.beta1
        if (null !== $requestedLangCodeOrLocale) {
            if (\strlen($requestedLangCodeOrLocale) > 2) {
                $lang = LangQuery::create()->filterByActive(true)->findOneByLocale($requestedLangCodeOrLocale);
            } else {
                $lang = LangQuery::create()->filterByActive(true)->findOneByCode($requestedLangCodeOrLocale);
            }

            if (\is_null($lang)) {
                return Lang::getDefaultLanguage();
            }

            // if each lang has its own domain, we redirect the user to the proper one.
            if (ConfigQuery::isMultiDomainActivated()) {
                $domainUrl = $lang->getUrl();

                if (!empty($domainUrl)) {
                    // if lang domain is different from current domain, redirect to the proper one
                    if (rtrim($domainUrl, '/') != $request->getSchemeAndHttpHost()) {
                        return new RedirectResponse($domainUrl, 301);
                    }
                    //the user is currently on the proper domain, nothing to change
                    return null;
                }

                Tlog::getInstance()->warning('The domain URL for language '.$lang->getTitle().' (id '.$lang->getId().') is not defined.');

                return Lang::getDefaultLanguage();
            }
            // one domain for all languages, the lang has to be set into session
            return $lang;
        }

        // Next, check if lang is defined in the current session. If not we have to set one.
        if (null === $request->getSession()->getLang(false)) {
            if (ConfigQuery::isMultiDomainActivated()) {
                // find lang with domain
                return LangQuery::create()->filterByUrl($request->getSchemeAndHttpHost(), ModelCriteria::LIKE)->findOne();
            }

            // At this point, set the lang to the default one.
            return Lang::getDefaultLanguage();
        }

        return null;
    }
}
