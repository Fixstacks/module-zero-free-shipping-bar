<?php
/**
 * Copyright © 2026 Roman Pasichnyk
 * SPDX-License-Identifier: OSL-3.0 OR AFL-3.0
 */

declare(strict_types=1);

namespace Zero\FreeShippingBar\Controller\Bar;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\Layout;
use Magento\Framework\View\Result\LayoutFactory;
use Zero\Core\Controller\PrivateFragmentActionInterface;
use Zero\FreeShippingBar\ViewModel\Bar;

/**
 * HTMX: the bar for the current cart, at one of its places (the checkout
 * summary refetches it on `cart:updated`).
 */
class Index implements PrivateFragmentActionInterface
{
    private const BLOCK_NAME = 'zero.free.shipping.bar';

    public function __construct(
        private readonly LayoutFactory $resultLayoutFactory,
        private readonly RequestInterface $request
    ) {
    }

    /**
     * @return Layout
     */
    public function execute()
    {
        $place = (string) $this->request->getParam('place', 'checkout');
        $result = $this->resultLayoutFactory->create();
        $result->addDefaultHandle();
        $result->getLayout()->getBlock(self::BLOCK_NAME)
            ?->setData('place', in_array($place, Bar::PLACES, true) ? $place : 'checkout');
        $result->getLayout()->addOutputElement(self::BLOCK_NAME);

        return $result;
    }
}
