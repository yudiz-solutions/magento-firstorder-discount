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

namespace Yudiz\FirstOrder\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\SalesRule\Model\CouponFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Message\ManagerInterface;

class AutoApplyCoupon implements ObserverInterface
{
    /**
     * @var CouponFactory
     */
    protected $couponFactory;
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var \Magento\SalesRule\Model\RuleFactory 
     */
    protected $ruleCollectionFactory;
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var SessionManagerInterface
     */
    protected $sessionManager;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * Constructor
     *
     * @param CouponFactory $couponFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param CheckoutSession $CheckoutSession
     * @param \Magento\SalesRule\Model\RuleFactory $ruleCollectionFactory
     * @param sessionManager $sessionManager
     */
    public function __construct(
        CouponFactory $couponFactory,
        ScopeConfigInterface $scopeConfig,
        CheckoutSession $checkoutSession,
        \Magento\SalesRule\Model\RuleFactory $ruleCollectionFactory,
        SessionManagerInterface $sessionManager,
        ManagerInterface $messageManager
    ) {
        $this->couponFactory = $couponFactory;
        $this->scopeConfig = $scopeConfig;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->checkoutSession = $checkoutSession;
        $this->sessionManager = $sessionManager;
        $this->messageManager = $messageManager;
    }
    /**
     * Executes the logic for auto-applying a coupon code if the module is enabled and a coupon code is generated.
     *
     * @param Observer $observer The event observer object.
     * @return void
     */
    public function execute(Observer $observer)
    {
        // Check if the module is enabled
        $moduleEnabled = $this->scopeConfig->getValue('firstorder/general/enable', ScopeInterface::SCOPE_STORE);
        $autoApply = $this->scopeConfig->getValue('firstorder/general/auto_apply', ScopeInterface::SCOPE_STORE);
        $generatedCouponCode = '';

        if ($moduleEnabled == true && $autoApply == true) {
            $generatedCouponCode = $this->sessionManager->getData('generated_coupon_data');
            if ($generatedCouponCode) {
                $quote = $this->checkoutSession->getQuote();
                $previousCouponCode = $quote->getCouponCode();

                if ($previousCouponCode) {
                    // A coupon code was already applied
                    $quote->setCouponCode($generatedCouponCode)->collectTotals()->save();
                    if ($previousCouponCode != $generatedCouponCode) {
                        // Set success message
                        $successMessage = __("You used coupon code \"%1\".", $generatedCouponCode);
                        $this->messageManager->addSuccessMessage($successMessage);
                    }
                } else {
                    // No coupon code was applied previously
                    $quote->setCouponCode($generatedCouponCode)->collectTotals()->save();

                    // Check if the coupon code has been applied
                    if ($quote->getCouponCode() && $generatedCouponCode) {
                        $successMessage = __("You used coupon code \"%1\".", $generatedCouponCode);
                        $this->messageManager->addSuccessMessage($successMessage);
                    }
                }
            }
        } else {
            $this->checkoutSession->getQuote()->setCouponCode($generatedCouponCode)->collectTotals()->save();
        }
    }
}
