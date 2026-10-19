<?php
/**
 * Copyright © 2026 Roman Pasichnyk
 * SPDX-License-Identifier: OSL-3.0 OR AFL-3.0
 */

declare(strict_types=1);

namespace Zero\FreeShippingBar\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Zero\FreeShippingBar\Model\Config\Source\Threshold;

class Config
{
    private const PATH = 'zero_free_shipping_bar/general/';

    public function __construct(private readonly ScopeConfigInterface $scopeConfig)
    {
    }

    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::PATH . 'enabled', ScopeInterface::SCOPE_STORE);
    }

    /**
     * The subtotal (base currency) that unlocks free shipping; 0 when there is none to show.
     */
    public function threshold(): float
    {
        if ($this->value('threshold') === Threshold::AMOUNT) {
            return max(0.0, (float) $this->value('amount'));
        }
        if (!$this->scopeConfig->isSetFlag('carriers/freeshipping/active', ScopeInterface::SCOPE_STORE)) {
            return 0.0;
        }

        return max(0.0, (float) $this->scopeConfig->getValue('carriers/freeshipping/free_shipping_subtotal', ScopeInterface::SCOPE_STORE));
    }

    /**
     * Whether the Free Shipping method compares its minimum with the subtotal including tax.
     */
    public function thresholdIncludesTax(): bool
    {
        return $this->value('threshold') !== Threshold::AMOUNT
            && $this->scopeConfig->isSetFlag('carriers/freeshipping/tax_including', ScopeInterface::SCOPE_STORE);
    }

    public function shownIn(string $place): bool
    {
        return $this->scopeConfig->isSetFlag(self::PATH . 'in_' . $place, ScopeInterface::SCOPE_STORE);
    }

    public function textProgress(): string
    {
        return $this->value('text_progress') ?: 'Add %1 more for free shipping';
    }

    public function textUnlocked(): string
    {
        return $this->value('text_unlocked') ?: 'You\'ve unlocked free shipping!';
    }

    private function value(string $key): string
    {
        return trim((string) $this->scopeConfig->getValue(self::PATH . $key, ScopeInterface::SCOPE_STORE));
    }
}
