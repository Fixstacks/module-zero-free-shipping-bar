<?php
/**
 * Copyright © 2026 Roman Pasichnyk
 * SPDX-License-Identifier: OSL-3.0 OR AFL-3.0
 */

declare(strict_types=1);

namespace Zero\FreeShippingBar\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Threshold implements OptionSourceInterface
{
    public const CARRIER = 'carrier';
    public const AMOUNT = 'amount';

    public function toOptionArray(): array
    {
        return [
            ['value' => self::CARRIER, 'label' => __('The Free Shipping method\'s minimum order amount')],
            ['value' => self::AMOUNT, 'label' => __('An amount of my own')],
        ];
    }
}
