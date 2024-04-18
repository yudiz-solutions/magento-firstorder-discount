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

namespace Yudiz\FirstOrder\Controller\Customer;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Controller\Result\JsonFactory;

class DestroySession extends Action
{
    /**
     * @var SessionManagerInterface
     */
    protected $session;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * Constructor
     *
     * @param Context $context
     * @param SessionManagerInterface $session
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        SessionManagerInterface $session,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        // Initialize class properties
        $this->session = $session;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * Execute action to destroy session
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $coupondata = $this->session->getData('generated_coupon');
        $this->session->setData('generated_coupon_data', $coupondata);

        // Set the specific session variable to null to destroy it
        $this->session->setData('generated_coupon', null);

        // Create JSON result object
        $result = $this->resultJsonFactory->create();
        // Return success response in JSON format
        return $result->setData(['success' => true]);
    }
}
