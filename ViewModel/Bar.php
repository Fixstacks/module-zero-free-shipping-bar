<?php
/**
 * Copyright © 2026 Roman Pasichnyk
 * SPDX-License-Identifier: OSL-3.0 OR AFL-3.0
 */

declare(strict_types=1);

namespace Zero\FreeShippingBar\ViewModel;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Zero\FreeShippingBar\Model\Config;
use Zero\FreeShippingBar\Model\Progress;

/**
 * The bar for the current cart: its text and percentage, in the cart's currency.
 */
class Bar implements ArgumentInterface
{
    public const PLACES = ['minicart', 'cart', 'checkout'];

    public function __construct(
        private readonly CheckoutSession $checkoutSession,
        private readonly Progress $progress,
        private readonly Config $config,
        private readonly PriceCurrencyInterface $priceCurrency,
        private readonly UrlInterface $url
    ) {
    }

    /**
     * @return array{text: string, percent: int, unlocked: bool, remaining: string, threshold: string}|null
     */
    public function bar(string $place): ?array
    {
        if (!in_array($place, self::PLACES, true) || !$this->config->shownIn($place)) {
            return null;
        }
        $quote = $this->checkoutSession->getQuote();
        $progress = $this->progress->of($quote);
        if ($progress === null) {
            return null;
        }
        $rate = (float) ($quote->getBaseToQuoteRate() ?: 1);
        $currency = (string) $quote->getQuoteCurrencyCode();
        $remaining = $this->priceCurrency->format($progress['remaining'] * $rate, false, PriceCurrencyInterface::DEFAULT_PRECISION, null, $currency);
        $threshold = $this->priceCurrency->format($progress['threshold'] * $rate, false, PriceCurrencyInterface::DEFAULT_PRECISION, null, $currency);

        return [
            'text' => $progress['unlocked']
                ? (string) __($this->config->textUnlocked())
                : str_replace('%1', $remaining, (string) __($this->config->textProgress())),
            'percent' => $progress['percent'],
            'unlocked' => $progress['unlocked'],
            'remaining' => $remaining,
            'threshold' => $threshold,
        ];
    }

    /**
     * The bar's own URL (HTMX): the checkout summary refetches it when the cart changes.
     */
    public function url(string $place): string
    {
        return $this->url->getUrl('freeshipping/bar', ['place' => $place]);
    }
}
