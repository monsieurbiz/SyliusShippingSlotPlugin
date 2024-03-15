# UPGRADE FROM 1.0.0 TO 1.1.0

- Add `form` in the events in the override template of the `/templates/bundles/SyliusShopBundle/Checkout/SelectShipping/_choice.html.twig`:

```diff
-{{ sylius_template_event('sylius.shop.checkout.select_shipping.before_method', {'method': method}) }}
+{{ sylius_template_event('sylius.shop.checkout.select_shipping.before_method', {'method': method, 'form': form}) }}
```

```diff
-{{ sylius_template_event('sylius.shop.checkout.select_shipping.after_method', {'method': method}) }}
+{{ sylius_template_event('sylius.shop.checkout.select_shipping.after_method', {'method': method, 'form': form}) }}
```

- The `shippingSlotConfig` class parameter in the `MonsieurBiz\SyliusShippingSlotPlugin\Entity\ShippingMethodTrait` trait is deprecated and will be removed in the next version. Use the `shippingSlotConfigs` class parameter instead to manage multiple shipping slot configs by shipping method.
- The methods `getShippingSlotConfig` and `setShippingSlotConfig` in `MonsieurBiz\SyliusShippingSlotPlugin\Entity\ShippingMethodInterface` interface are deprecated and will be removed in the next version. Use the methods `getShippingSlotConfigs` and `setShippingSlotConfigs` instead.
