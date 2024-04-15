<?php

/**
 * Yudiz
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to a newer
 * version in the future.
 *
 * @category    Yudiz
 * @package     Yudiz_FirstOrder
 * @copyright   Copyright (c) 2024 Yudiz (https://www.Yudiz.com/)
 */

namespace Yudiz\FirstOrder\Model\Config\Source;

use Magento\SalesRule\Model\RuleFactory;

class CouponLoad implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var RuleFactory
     */
    protected $_salesRuleCoupon;

    /**
     * Constructor
     *
     * @param RuleFactory $salesRuleCoupon
     */
    public function __construct(
        RuleFactory $salesRuleCoupon
    ) {
        // Initialize class properties
        $this->_salesRuleCoupon = $salesRuleCoupon;
    }

    /**
     * Retrieve option array for select field
     *
     * @return array
     */
    public function toOptionArray()
    {
        $objrules = $this->_salesRuleCoupon->create();

        $rules = $objrules->getCollection();
        $rules->addFieldToFilter('is_active', 1);
        $rules->addFieldToFilter('coupon_type', 2);

        // Additional filter for rules starting and ending date
        $currentDate = date('Y-m-d');
        $rules->addFieldToFilter(
            'from_date',
            [
                ['lteq' => $currentDate],
                ['null' => true]
            ]
        );
        $rules->addFieldToFilter(
            'to_date',
            [
                ['gteq' => $currentDate],
                ['null' => true]
            ]
        );

        // Initialize option array
        $optionArrays = [];

        // Iterate through rules to build option array
        foreach ($rules->getData() as $rule) {
            $optionArray = [
                'value' => $rule['rule_id'],
                'label' => $rule['name']
            ];
            $optionArrays[] = $optionArray;
        }

        return $optionArrays;
    }
}
