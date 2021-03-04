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

namespace Thelia\Model;

use Propel\Runtime\Exception\PropelException;
use Thelia\Exception\TaxEngineException;
use Thelia\Model\Base\Tax as BaseTax;
use Thelia\TaxEngine\BaseTaxType;

class Tax extends BaseTax
{
    /**
     * Provides a form-and-javascript-safe version of the type, which is a fully qualified classname, with \.
     */
    public static function escapeTypeName($name)
    {
        return str_replace('\\', '-', $name);
    }

    public static function unescapeTypeName($name)
    {
        return str_replace('-', '\\', $name);
    }

    public function getTaxRuleCountryPosition()
    {
        try {
            $taxRuleCountryPosition = $this->getVirtualColumn(TaxRuleQuery::ALIAS_FOR_TAX_RULE_COUNTRY_POSITION);
        } catch (PropelException $e) {
            throw new PropelException('Virtual column `'.TaxRuleQuery::ALIAS_FOR_TAX_RULE_COUNTRY_POSITION.'` does not exist in Tax::getTaxRuleCountryPosition');
        }

        return $taxRuleCountryPosition;
    }

    public function getTypeInstance()
    {
        $class = $this->getType();

        /* test type */
        if (!class_exists($class)) {
            throw new TaxEngineException('Recorded type `'.$class.'` does not exists', TaxEngineException::BAD_RECORDED_TYPE);
        }
        /** @var \Thelia\TaxEngine\BaseTaxType $instance */
        $instance = new $class();

        if (!$instance instanceof BaseTaxType) {
            throw new TaxEngineException('Recorded type `'.$class.'` does not extends BaseTaxType', TaxEngineException::BAD_RECORDED_TYPE);
        }

        $instance->loadRequirements($this->getRequirements());

        return $instance;
    }

    public function setRequirements($requirements)
    {
        return parent::setSerializedRequirements(base64_encode(json_encode($requirements)));
    }

    public function getRequirements()
    {
        $requirements = json_decode(base64_decode(parent::getSerializedRequirements()), true);

        if (json_last_error() != \JSON_ERROR_NONE || !\is_array($requirements)) {
            throw new TaxEngineException('BAD RECORDED REQUIREMENTS', TaxEngineException::BAD_RECORDED_REQUIREMENTS);
        }

        return $requirements;
    }
}
