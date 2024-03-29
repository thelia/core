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

namespace Thelia\Rewriting;

use Thelia\Model\RewritingUrlQuery;
use Thelia\Tools\URL;

/**
 * Class RewritingRetriever.
 *
 * @author Etienne Roudeix <eroudeix@openstudio.fr>
 *
 * This class provides methods to retrieve a rewritten URL from a query
 */
class RewritingRetriever
{
    protected $search;
    protected $rewritingUrlQuery;

    public $url;
    public $rewrittenUrl;

    public function __construct()
    {
        $this->rewritingUrlQuery = new RewritingUrlQuery();
    }

    /**
     * @param null $viewId
     */
    public function loadViewUrl($view, $viewLocale = null, $viewId = null): void
    {
        $this->search = $this->rewritingUrlQuery->getViewUrlQuery($view, $viewLocale, $viewId);

        $allParametersWithoutView = [];
        if (null !== $viewLocale) {
            $allParametersWithoutView['lang'] = $viewLocale;
        }
        if (null !== $viewId) {
            $allParametersWithoutView[$view.'_id'] = $viewId;
        }

        $this->rewrittenUrl = null;
        $this->url = URL::getInstance()->viewUrl($view, $allParametersWithoutView);
        if ($this->search !== null) {
            $this->rewrittenUrl = URL::getInstance()->absoluteUrl(
                $this->search->getUrl()
            );
        }
    }

    /**
     * @param null  $viewId
     * @param array $viewOtherParameters
     */
    public function loadSpecificUrl($view, $viewLocale, $viewId = null, $viewOtherParameters = []): void
    {
        if (empty($viewOtherParameters)) {
            $this->loadViewUrl($view, $viewLocale, $viewId);

            return;
        }

        $this->search = $this->rewritingUrlQuery->getSpecificUrlQuery($view, $viewLocale, $viewId, $viewOtherParameters);

        $allParametersWithoutView = $viewOtherParameters;
        $allParametersWithoutView['lang'] = $viewLocale;
        if (null !== $viewId) {
            $allParametersWithoutView[$view.'_id'] = $viewId;
        }

        $this->rewrittenUrl = null;
        $this->url = URL::getInstance()->viewUrl($view, $allParametersWithoutView);
        if ($this->search !== null) {
            $this->rewrittenUrl = $this->search->getUrl();
        }
    }

    public function toString()
    {
        return $this->rewrittenUrl === null ? $this->url : $this->rewrittenUrl;
    }
}
