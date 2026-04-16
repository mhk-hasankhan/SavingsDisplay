<?php
declare(strict_types=1);

namespace Vendor\SavingsDisplay\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Data extends AbstractHelper
{
    private const XML_PATH_ENABLED             = 'savings_display/general/enabled';
    private const XML_PATH_SHOW_PERCENTAGE     = 'savings_display/general/show_percentage';
    private const XML_PATH_SHOW_AMOUNT         = 'savings_display/general/show_amount';
    private const XML_PATH_LABEL_TEXT          = 'savings_display/general/label_text';
    private const XML_PATH_MIN_THRESHOLD       = 'savings_display/general/min_savings_threshold';

    public function __construct(
        Context $context,
        private readonly PriceCurrencyInterface $priceCurrency
    ) {
        parent::__construct($context);
    }

    public function isEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function showPercentage(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SHOW_PERCENTAGE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function showAmount(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SHOW_AMOUNT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getLabelText(?int $storeId = null): string
    {
        $label = $this->scopeConfig->getValue(
            self::XML_PATH_LABEL_TEXT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        return $label ?: 'You save';
    }

    public function getMinThreshold(?int $storeId = null): float
    {
        return (float) $this->scopeConfig->getValue(
            self::XML_PATH_MIN_THRESHOLD,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get savings data for a product.
     * Returns null if no savings apply or below threshold.
     *
     * @return array{amount: float, percentage: float, amount_formatted: string}|null
     */
    public function getSavingsData(Product $product): ?array
    {
        $regularPrice = (float) $product->getPriceInfo()
            ->getPrice('regular_price')
            ->getValue();

        $finalPrice = (float) $product->getPriceInfo()
            ->getPrice('final_price')
            ->getValue();

        if ($regularPrice <= 0 || $finalPrice >= $regularPrice) {
            return null;
        }

        $savings = $regularPrice - $finalPrice;

        if ($savings < $this->getMinThreshold()) {
            return null;
        }

        $percentage = round(($savings / $regularPrice) * 100, 1);

        return [
            'amount'           => $savings,
            'percentage'       => $percentage,
            'amount_formatted' => $this->priceCurrency->format($savings, false),
            'regular_price'    => $regularPrice,
            'final_price'      => $finalPrice,
        ];
    }
}
