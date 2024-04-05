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

namespace Yudiz\FirstOrder\Observer\Customer;

use Magento\SalesRule\Model\CouponGenerator;
use Magento\SalesRule\Model\CouponFactory;
use Magento\SalesRule\Model\RuleFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Registry;
use Psr\Log\LoggerInterface;
use Yudiz\FirstOrder\Helper\Data as FirstOrderHelper;

class RegisterSuccess implements ObserverInterface
{
    // Declare class properties

    /**
     * @var CouponGenerator
     */
    protected $couponGenerator;

    /**
     * @var CouponFactory
     */
    protected $couponFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var SessionManagerInterface
     */
    protected $session;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var FirstOrderHelper
     */
    protected $firstOrderHelper;

    /**
     * Constructor
     *
     * @param CouponGenerator $couponGenerator
     * @param CouponFactory $couponFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param RuleFactory $ruleFactory
     * @param UrlInterface $urlBuilder
     * @param SessionManagerInterface $session
     * @param Registry $registry
     * @param LoggerInterface $logger
     * @param FirstOrderHelper $firstOrderHelper
     */
    public function __construct(
        CouponGenerator $couponGenerator,
        CouponFactory $couponFactory,
        ScopeConfigInterface $scopeConfig,
        RuleFactory $ruleFactory,
        UrlInterface $urlBuilder,
        SessionManagerInterface $session,
        Registry $registry,
        LoggerInterface $logger,
        FirstOrderHelper $firstOrderHelper
    ) {
        // Initialize class properties
        $this->couponGenerator = $couponGenerator;
        $this->couponFactory = $couponFactory;
        $this->scopeConfig = $scopeConfig;
        $this->ruleFactory = $ruleFactory;
        $this->urlBuilder = $urlBuilder;
        $this->session = $session;
        $this->registry = $registry;
        $this->logger = $logger;
        $this->firstOrderHelper = $firstOrderHelper;
    }

    /**
     * Execute observer action
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $customer = $observer->getData('customer');
            $ruleId = $this->scopeConfig->getValue("firstorder/general/coupon_code", ScopeInterface::SCOPE_STORE);
            $prefix = $this->scopeConfig->getValue("firstorder/general/prefix", ScopeInterface::SCOPE_STORE);
            $suffix = $this->scopeConfig->getValue("firstorder/general/suffix", ScopeInterface::SCOPE_STORE);
            $format = $this->scopeConfig->getValue("firstorder/general/list_mode", ScopeInterface::SCOPE_STORE);
            $length = $this->scopeConfig->getValue("firstorder/general/length", ScopeInterface::SCOPE_STORE);
            $qty = 1;
            $rule = $this->loadRuleById($ruleId);

            if ($rule) {
                $generatedCoupon = $this->generateCouponCodes($rule, $qty, $prefix, $suffix, $format, $length);
                $this->session->start();
                $this->session->setData('generated_coupon', $generatedCoupon[0]);

                   // Send email
                $this->firstOrderHelper->sendCouponEmail($customer, $generatedCoupon[0]);
            } else {
                $this->logger->error('Rule not found with ID: ' . $ruleId);
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * Load rule by ID
     *
     * @param int $ruleId
     * @return \Magento\SalesRule\Model\Rule|null
     */
    protected function loadRuleById($ruleId)
    {
        try {
            $rule = $this->ruleFactory->create()->load($ruleId);
            return $rule->getId() ? $rule : null;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return null;
        }
    }

    /**
     * Generate coupon codes
     *
     * @param \Magento\SalesRule\Model\Rule $rule
     * @param int $qty
     * @param string $prefix
     * @param string $suffix
     * @param string $format
     * @param int $length
     * @return array
     */
    protected function generateCouponCodes($rule, $qty, $prefix, $suffix, $format, $length)
    {
        try {
            $codes = $this->couponGenerator->generateCodes(
                [
                    'rule_id' => $rule->getId(),
                    'qty' => $qty,
                    'length' => $length,
                    'format' => $format,
                    'prefix' => $prefix,
                    'suffix' => $suffix,
                ]
            );

            return $codes;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return [];
       
        }
    }
}
