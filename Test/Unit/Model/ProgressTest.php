<?php
/**
 * Copyright © 2026 Roman Pasichnyk
 * SPDX-License-Identifier: OSL-3.0 OR AFL-3.0
 */

declare(strict_types=1);

namespace Zero\FreeShippingBar\Test\Unit\Model;

use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use PHPUnit\Framework\TestCase;
use Zero\FreeShippingBar\Model\Config;
use Zero\FreeShippingBar\Model\Progress;

class ProgressTest extends TestCase
{
    public function testCountsDownToTheThreshold(): void
    {
        $progress = new Progress($this->config(100.0));

        $bar = $progress->of($this->quote(subtotal: 37.5));

        self::assertSame(['threshold' => 100.0, 'amount' => 37.5, 'remaining' => 62.5, 'percent' => 37, 'unlocked' => false], $bar);
    }

    public function testReachedAtTheThresholdAndBeyond(): void
    {
        $progress = new Progress($this->config(100.0));

        self::assertTrue($progress->of($this->quote(subtotal: 100.0))['unlocked']);
        $bar = $progress->of($this->quote(subtotal: 180.0));
        self::assertSame(100, $bar['percent']);
        self::assertSame(0.0, $bar['remaining']);
    }

    public function testAddsTaxWhenTheMethodIncludesIt(): void
    {
        $progress = new Progress($this->config(100.0, includesTax: true));

        self::assertSame(95.0, $progress->of($this->quote(subtotal: 80.0, tax: 15.0))['amount']);
    }

    public function testACartRuleCountsAsReached(): void
    {
        $progress = new Progress($this->config(100.0));

        self::assertTrue($progress->of($this->quote(subtotal: 10.0, freeShipping: true))['unlocked']);
    }

    public function testHiddenWithoutAThresholdOrForVirtualCarts(): void
    {
        self::assertNull((new Progress($this->config(0.0)))->of($this->quote(subtotal: 10.0)));
        self::assertNull((new Progress($this->config(100.0)))->of($this->quote(subtotal: 10.0, virtual: true)));
        self::assertNull((new Progress($this->config(100.0, enabled: false)))->of($this->quote(subtotal: 10.0)));
    }

    public function testHiddenForAnEmptyCart(): void
    {
        self::assertNull((new Progress($this->config(100.0)))->of($this->quote(subtotal: 0.0, items: 0)));
    }

    private function config(float $threshold, bool $includesTax = false, bool $enabled = true): Config
    {
        $config = $this->createMock(Config::class);
        $config->method('isEnabled')->willReturn($enabled);
        $config->method('threshold')->willReturn($threshold);
        $config->method('thresholdIncludesTax')->willReturn($includesTax);

        return $config;
    }

    private function quote(float $subtotal, float $tax = 0.0, bool $freeShipping = false, bool $virtual = false, int $items = 2): Quote
    {
        $address = $this->createMock(Address::class);
        $address->method('getBaseSubtotalWithDiscount')->willReturn($subtotal);
        // getBaseTaxAmount and getFreeShipping are magic getters.
        $address->method('__call')->willReturnCallback(static fn (string $name) => match ($name) {
            'getBaseTaxAmount' => $tax,
            'getFreeShipping' => $freeShipping,
            default => null,
        });
        $quote = $this->createMock(Quote::class);
        $quote->method('isVirtual')->willReturn($virtual);
        $quote->method('getItemsCount')->willReturn($items);
        $quote->method('getShippingAddress')->willReturn($address);

        return $quote;
    }
}
