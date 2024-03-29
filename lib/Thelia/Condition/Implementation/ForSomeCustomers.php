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

namespace Thelia\Condition\Implementation;

use Thelia\Condition\Operators;
use Thelia\Coupon\FacadeInterface;
use Thelia\Exception\InvalidConditionValueException;
use Thelia\Exception\UnmatchableConditionException;
use Thelia\Model\Customer;
use Thelia\Model\CustomerQuery;

/**
 * Check a Checkout against its Product number.
 *
 * @author  Franck Allimant <franck@cqfdev.fr>
 */
class ForSomeCustomers extends ConditionAbstract
{
    public const CUSTOMERS_LIST = 'customers';

    public function __construct(FacadeInterface $facade)
    {
        $this->availableOperators = [
            self::CUSTOMERS_LIST => [
                Operators::IN,
                Operators::OUT,
            ],
        ];

        parent::__construct($facade);
    }

    public function getServiceId()
    {
        return 'thelia.condition.for_some_customers';
    }

    public function setValidatorsFromForm(array $operators, array $values)
    {
        $this->checkComparisonOperatorValue($operators, self::CUSTOMERS_LIST);

        // Use default values if data is not defined.
        if (!isset($operators[self::CUSTOMERS_LIST]) || !isset($values[self::CUSTOMERS_LIST])) {
            $operators[self::CUSTOMERS_LIST] = Operators::IN;
            $values[self::CUSTOMERS_LIST] = [];
        }

        // Be sure that the value is an array, make one if required
        if (!\is_array($values[self::CUSTOMERS_LIST])) {
            $values[self::CUSTOMERS_LIST] = [$values[self::CUSTOMERS_LIST]];
        }

        // Check that at least one product is selected
        if (empty($values[self::CUSTOMERS_LIST])) {
            throw new InvalidConditionValueException(
                __CLASS__,
                self::CUSTOMERS_LIST
            );
        }

        $this->operators = [self::CUSTOMERS_LIST => $operators[self::CUSTOMERS_LIST]];
        $this->values = [self::CUSTOMERS_LIST => $values[self::CUSTOMERS_LIST]];

        return $this;
    }

    public function isMatching()
    {
        if (null === $customer = $this->facade->getCustomer()) {
            throw new UnmatchableConditionException(UnmatchableConditionException::getMissingCustomerMessage());
        }

        return $this->conditionValidator->variableOpComparison(
            $customer->getId(),
            $this->operators[self::CUSTOMERS_LIST],
            $this->values[self::CUSTOMERS_LIST]
        );
    }

    public function getName()
    {
        return $this->translator->trans(
            'For one ore more customers',
            []
        );
    }

    public function getToolTip()
    {
        $toolTip = $this->translator->trans(
            'The coupon applies to some customers only',
            []
        );

        return $toolTip;
    }

    public function getSummary()
    {
        $i18nOperator = Operators::getI18n(
            $this->translator,
            $this->operators[self::CUSTOMERS_LIST]
        );

        $custStrList = '';

        $custIds = $this->values[self::CUSTOMERS_LIST];

        if (null !== $custList = CustomerQuery::create()->findPks($custIds)) {
            /** @var Customer $cust */
            foreach ($custList as $cust) {
                $custStrList .= $cust->getLastname().' '.$cust->getFirstname().' ('.$cust->getRef().'), ';
            }

            $custStrList = rtrim($custStrList, ', ');
        }

        $toolTip = $this->translator->trans(
            'Customer is %op% <strong>%customer_list%</strong>',
            [
                '%customer_list%' => $custStrList,
                '%op%' => $i18nOperator,
            ]
        );

        return $toolTip;
    }

    protected function generateInputs()
    {
        return [
            self::CUSTOMERS_LIST => [
                'availableOperators' => $this->availableOperators[self::CUSTOMERS_LIST],
                'value' => '',
                'selectedOperator' => Operators::IN,
            ],
        ];
    }

    public function drawBackOfficeInputs()
    {
        return $this->facade->getParser()->render(
            'coupon/condition-fragments/customers-condition.html',
            [
                'operatorSelectHtml' => $this->drawBackOfficeInputOperators(self::CUSTOMERS_LIST),
                'customers_field_name' => self::CUSTOMERS_LIST,
                'values' => $this->values[self::CUSTOMERS_LIST] ?? [],
            ]
        );
    }
}
