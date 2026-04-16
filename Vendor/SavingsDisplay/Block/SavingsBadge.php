<?php
declare(strict_types=1);

namespace Vendor\SavingsDisplay\Block;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product;
use Vendor\SavingsDisplay\Helper\Data as SavingsHelper;

class SavingsBadge extends AbstractProduct
{
    public function __construct(
        Context $context,
        private readonly SavingsHelper $savingsHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function isEnabled(): bool
    {
        return $this->savingsHelper->isEnabled();
    }

    public function showPercentage(): bool
    {
        return $this->savingsHelper->showPercentage();
    }

    public function showAmount(): bool
    {
        return $this->savingsHelper->showAmount();
    }

    public function getLabelText(): string
    {
        return $this->savingsHelper->getLabelText();
    }

    /**
     * @return array{amount: float, percentage: float, amount_formatted: string}|null
     */
    public function getSavingsData(): ?array
    {
        $product = $this->getProduct();
        if (!$product instanceof Product) {
            return null;
        }
        return $this->savingsHelper->getSavingsData($product);
    }

    public function getProduct(): ?Product
    {
        // Injected via layout or registry
        if ($this->hasData('product')) {
            return $this->getData('product');
        }
        // Fall back to registry (Magento 2 catalog product page)
        return $this->_coreRegistry->registry('current_product');
    }
}
