<?php
/**
 * Copyright © 2026 Roman Pasichnyk
 * SPDX-License-Identifier: OSL-3.0 OR AFL-3.0
 */

declare(strict_types=1);

namespace Zero\FreeShippingBar\Model;

use Magento\Quote\Model\Quote;

/**
 * How far a cart is from free shipping: the same figure the Free Shipping
 * method compares with its minimum (the subtotal after discounts, plus tax
 * when the method is set to include it), in the base currency. A cart rule
 * that gives free shipping counts as reached.
 */
class Progress
{
    public function __construct(private readonly Config $config)
    {
    }

    /**
     * @return array{threshold: float, amount: float, remaining: float, percent: int, unlocked: bool}|null
     *     null: nothing to show (the bar is off, there is no threshold, the cart is empty or nothing in it ships)
     */
    public function of(Quote $quote): ?array
    {
        if (!$this->config->isEnabled() || $quote->isVirtual() || !(int) $quote->getItemsCount()) {
            return null;
        }
        $threshold = $this->config->threshold();
        if ($threshold <= 0) {
            return null;
        }
        $address = $quote->getShippingAddress();
        $amount = max(0.0, (float) $address->getBaseSubtotalWithDiscount());
        if ($this->config->thresholdIncludesTax()) {
            $amount += (float) $address->getBaseTaxAmount();
        }
        $unlocked = (bool) $address->getFreeShipping() || $amount >= $threshold;

        return [
            'threshold' => $threshold,
            'amount' => $amount,
            'remaining' => $unlocked ? 0.0 : $threshold - $amount,
            'percent' => $unlocked ? 100 : (int) floor($amount / $threshold * 100),
            'unlocked' => $unlocked,
        ];
    }
}
