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
 * @copyright   Copyright (c) 2024 Yudiz (https://www.yudiz.com/)
 */

namespace Yudiz\FirstOrder\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Registry;

class GeneratedcodeData extends Template
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var SessionManagerInterface
     */
    protected $session;

    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param Registry $registry
     * @param SessionManagerInterface $session
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Registry $registry,
        SessionManagerInterface $session,
        array $data = []
    ) {
        // Initialize class properties
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->session = $session;
    }

    /**
     * Retrieve the generated coupon code from session
     *
     * @return string|null
     */
    public function getGeneratedCoupon()
    {
        // Check if the session data is available
        if ($this->session->getData('generated_coupon')) {
            return $this->session->getData('generated_coupon');
        }
    }
}
