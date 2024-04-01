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
    // Declare class properties
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
        // Retrieve sales rule model object
        $objrules = $this->_salesRuleCoupon->create();

        // Retrieve sales rules collection
        $rules = $objrules->getCollection();

        // Filter active rules with coupon type 2 and auto generation enabled
        $rules->addFieldToFilter('is_active', 1);
        $rules->addFieldToFilter('coupon_type', 2);
        $rules->addFieldToFilter('use_auto_generation', 1)->toOptionArray();

        // Initialize option array
        $optionArrays = [];

        // Iterate through rules to build option array
        foreach ($rules as $rule) {
            $optionArray = [
                'value' => $rule['rule_id'],
                'label' => $rule['name']
            ];
            $optionArrays[] = $optionArray;
        }

        return $optionArrays;
    }
}
