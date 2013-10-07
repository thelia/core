<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : info@thelia.net                                                      */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 3 of the License                */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*	    along with this program. If not, see <http://www.gnu.org/licenses/>.         */
/*                                                                                   */
/*************************************************************************************/
namespace Thelia\TaxEngine\TaxType;

use Thelia\Exception\TaxEngineException;
use Thelia\Model\FeatureProductQuery;
use Thelia\Model\Product;
use Thelia\Type\FloatType;

/**
 *
 * @author Etienne Roudeix <eroudeix@openstudio.fr>
 *
 */
class FeatureFixAmountTaxType extends BaseTaxType
{
    public function calculate($untaxedPrice)
    {
        return $this->getRequirement("amount");
    }

    public function pricePercentRetriever()
    {
        return 0;
    }

    public function fixAmountRetriever(Product $product)
    {
        $featureId = $this->getRequirement("featureId");

        $query = FeatureProductQuery::create()
            ->filterByProduct($product)
            ->filterByFeatureId($featureId)
            ->findOne();

        $taxAmount = $query->getFreeTextValue();

        $testInt = new FloatType();
        if(!$testInt->isValid($taxAmount)) {
            throw new TaxEngineException('Feature value does not match FLOAT format', TaxEngineException::FEATURE_BAD_EXPECTED_VALUE);
        }

        return $taxAmount;
    }

    public function getRequirementsList()
    {
        return array(
            'featureId' => new ModelType('Feature'),
        );
    }
}
