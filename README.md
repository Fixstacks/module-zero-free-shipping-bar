# Zero_FreeShippingBar

"Add $12.50 more for free shipping" with a progress bar in the mini-cart,
on the cart page and in the checkout summary; "You've unlocked free
shipping!" once the cart gets there. Configured in Stores > Configuration >
Zero > Free Shipping Bar.

## Install

Requires Magento Open Source 2.4.7–2.4.9 and PHP 8.2–8.4 with the GD extension built with WebP (`php -r 'var_dump(function_exists("imagewebp"));'` says `true`; Magento's own PHP requirements cover the rest). Without WebP in GD, Zero skips its WebP image copies and serves the originals.

```bash
composer require fixstacks/module-zero-free-shipping-bar
bin/magento setup:upgrade
bin/magento setup:di:compile           # production mode
bin/magento setup:static-content:deploy # production mode
bin/magento cache:flush
```

Composer installs `fixstacks/module-zero-core`, `fixstacks/module-zero-checkout` with it.

Part of [`fixstacks/zero`](https://fixstacks.com/zero), which installs the Zero theme and every Zero module at once.

## What it compares

The same figure the Free Shipping delivery method compares with its
"Minimum Order Amount": the cart subtotal after discounts, in the base
currency, plus tax when that method is set to include it. The threshold is
that method's minimum while the method is enabled (Sales > Delivery
Methods > Free Shipping); or an amount of your own, for stores where free
shipping comes from a cart rule or another carrier. A cart rule that gives
the whole order free shipping counts as reached. Without a threshold, or
for a cart with nothing to ship, the bar stays hidden.

Amounts show in the cart's currency.

## Where

- The mini-cart: above the items (Zero_Core's mini-cart content renders a
  `zero.minicart.top` child).
- The cart page: in the summary card, under its title (`cart.summary`).
- The checkout: at the top of the order summary; it refetches itself on
  `cart:updated`, which Zero Checkout's discount-code request now sends.

Each place can be turned off. The two texts are configurable (`%1` is the
missing amount). No JavaScript: a `<progress>` element and HTMX.

## Tests

- `Test/Unit/Model/ProgressTest.php`: the arithmetic, tax, a cart rule.
- `dev/tests/zero-e2e/tests/free-shipping-bar.spec.ts`: a cheap item shows
  the missing amount in the mini-cart and on the cart page; a bigger
  quantity unlocks it. Skipped where the bar is off.

## License

OSL-3.0 or AFL-3.0, your choice: see `NOTICE.md`.

---

<a href="https://vilmate.com"><picture>
  <source media="(prefers-color-scheme: dark)" srcset="https://raw.githubusercontent.com/Fixstacks/zero/main/docs/images/vilmate-logo-dark.svg">
  <img src="https://raw.githubusercontent.com/Fixstacks/zero/main/docs/images/vilmate-logo.svg" alt="Vilmate" height="34" align="right">
</picture></a>

Zero is made by [Fixstacks](https://fixstacks.com/zero) in partnership with [Vilmate](https://vilmate.com), a software development company.
Documentation: https://fixstacks.com/zero/docs. Demo store: https://zero.fixstacks.com.
