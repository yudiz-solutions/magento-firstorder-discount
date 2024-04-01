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

/**
 * Class ListMode
 *
 * @package Yudiz\FirstOrder\Model\Config\Source
 */
class ListMode implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'alphanum', 'label' => __('Alphanumeric')],
            ['value' => 'alpha', 'label' => __('Alphabetical')],
            ['value' => 'num', 'label' => __('Numeric')]
        ];
    }
}
