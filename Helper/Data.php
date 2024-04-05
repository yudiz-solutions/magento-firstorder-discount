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

namespace Yudiz\FirstOrder\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Psr\Log\LoggerInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Constructor
     *
     * @param Context $context
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $inlineTranslation
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    /**
     * Send coupon email to the customer
     *
     * @param \Magento\Customer\Model\Data\Customer $customer
     * @param string $couponCode
     * @return void
     */
    public function sendCouponEmail(\Magento\Customer\Model\Data\Customer $customer, $couponCode)
    {
        try {
            // Retrieve store information
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $storeId = $this->storeManager->getStore()->getId();

            // Retrieve sender information from configuration
            $senderName = $this->scopeConfig->getValue('firstorder/email/firstorder_sender_name', $storeScope);
            $senderEmail = $this->scopeConfig->getValue('firstorder/email/firstorder_sender_email', $storeScope);

            // Prepare email variables
            $customerName = $customer->getFirstName() . " " . $customer->getLastName();
            $sendEmailTo = $customer->getEmail();
            $sender = ['name' => $senderName, 'email' => $senderEmail];
            $emailTemplateVariables = [
                'name' => $customerName,
                'couponCode' => $couponCode,
            ];
            
            // Suspend inline translation to prevent conflicts with email template
            $this->inlineTranslation->suspend();

            // Build email and send
            $transport = $this->transportBuilder
                ->setTemplateIdentifier('yudiz_firstorder_email_template_front')
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => $storeId,
                    ]
                )
                ->setTemplateVars($emailTemplateVariables)
                ->setFrom($sender)
                ->addTo($sendEmailTo);
            $transport->getTransport()
                ->sendMessage();

            // Resume inline translation
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            // Log any errors that occur during email sending
            $this->logger->error($e->getMessage());
        }
    }
}
