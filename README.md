[![Banner of Sylius Shipping Slot plugin](docs/images/banner.jpg)](https://monsieurbiz.com/agence-web-experte-sylius)

<h1 align="center">Shipping Slot</h1>

[![Shipping Slot Plugin license](https://img.shields.io/github/license/monsieurbiz/SyliusShippingSlotPlugin?public)](https://github.com/monsieurbiz/SyliusShippingSlotPlugin/blob/master/LICENSE.txt)
[![Tests](https://github.com/monsieurbiz/SyliusShippingSlotPlugin/actions/workflows/tests.yaml/badge.svg)](https://github.com/monsieurbiz/SyliusShippingSlotPlugin/actions/workflows/tests.yaml)
[![Security](https://github.com/monsieurbiz/SyliusShippingSlotPlugin/actions/workflows/security.yaml/badge.svg)](https://github.com/monsieurbiz/SyliusShippingSlotPlugin/actions/workflows/security.yaml)
[![Flex Recipe](https://github.com/monsieurbiz/SyliusShippingSlotPlugin/actions/workflows/recipe.yaml/badge.svg)](https://github.com/monsieurbiz/SyliusShippingSlotPlugin/actions/workflows/recipe.yaml)

This plugin allows you to choose a delivery date and time.

## Installation

Install the plugin with `composer`:

`composer require monsieurbiz/sylius-shipping-slot-plugin`

If you are using Symfony Flex, the recipe will automatically do some actions.

<details>
<summary>For the installation without Flex, follow these additional steps</summary>
<p>
1. Add the plugin to your `config/bundles.php` file:

```php
return [
    // ...
    MonsieurBiz\SyliusShippingSlotPlugin\MonsieurBizSyliusShippingSlotPlugin::class => ['all' => true],
];
```

2. Import the plugin's configuration by creating a new file `config/packages/monsieurbiz_sylius_shipping_slot_plugin.yaml` with the following content:

```yaml
imports:
    - { resource: "@MonsieurBizSyliusShippingSlotPlugin/Resources/config/config.yaml" }
```

3. Import the plugin's routing by creating a new file `config/routes/monsieurbiz_sylius_shipping_slot_plugin.yaml` with the following content:

```yaml
monsieurbiz_sylius_shipping_slot_plugin:
    resource: "@MonsieurBizSyliusShippingSlotPlugin/Resources/config/routing.yaml"
```

4. Copy the override template from the plugin to your `templates` directory:

```bash
mkdir -p templates/bundles/; cp -Rv vendor/monsieurbiz/sylius-shipping-slot-plugin/src/Resources/views/SyliusShopBundle templates/bundles/
````
</p>
</details>

After that, follow the next steps:

1. Your `Order` entity should implement the `MonsieurBiz\SyliusShippingSlotPlugin\Entity\OrderInterface` and use the `MonsieurBiz\SyliusShippingSlotPlugin\Entity\OrderTrait` trait:

```diff
namespace App\Entity\Order;

use Doctrine\ORM\Mapping as ORM;
+use MonsieurBiz\SyliusShippingSlotPlugin\Entity\OrderInterface as MonsieurBizOrderInterface;
+use MonsieurBiz\SyliusShippingSlotPlugin\Entity\OrderTrait;
use Sylius\Component\Core\Model\Order as BaseOrder;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_order")
 */
#[ORM\Entity]
#[ORM\Table(name: 'sylius_order')]
-class Order extends BaseOrder
+class Order extends BaseOrder implements MonsieurBizOrderInterface
{
+    use OrderTrait;
}
```

2. Your `ProductVariant` entity should implement the `MonsieurBiz\SyliusShippingSlotPlugin\Entity\ProductVariantInterface` and use the `MonsieurBiz\SyliusShippingSlotPlugin\Entity\ProductVariantTrait` trait:

```diff
namespace App\Entity\Product;

use Doctrine\ORM\Mapping as ORM;
+use MonsieurBiz\SyliusShippingSlotPlugin\Entity\ProductVariantInterface as MonsieurBizProductVariantInterface;
+use MonsieurBiz\SyliusShippingSlotPlugin\Entity\ProductVariantTrait;
use Sylius\Component\Core\Model\ProductVariant as BaseProductVariant;
use Sylius\Component\Product\Model\ProductVariantTranslationInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_product_variant")
 */
#[ORM\Entity]
#[ORM\Table(name: 'sylius_product_variant')]
-class ProductVariant extends BaseProductVariant
+class ProductVariant extends BaseProductVariant implements MonsieurBizProductVariantInterface
{
+    use ProductVariantTrait;
+
    protected function createTranslation(): ProductVariantTranslationInterface
    {
        return new ProductVariantTranslation();
    }
}
```

3. Your `Shipment` entity should implement the `MonsieurBiz\SyliusShippingSlotPlugin\Entity\ShipmentInterface` and use the `MonsieurBiz\SyliusShippingSlotPlugin\Entity\ShipmentTrait` trait:

```diff
namespace App\Entity\Shipping;

use Doctrine\ORM\Mapping as ORM;
+use MonsieurBiz\SyliusShippingSlotPlugin\Entity\ShipmentInterface as MonsieurBizShipmentInterface;
+use MonsieurBiz\SyliusShippingSlotPlugin\Entity\ShipmentTrait;
use Sylius\Component\Core\Model\Shipment as BaseShipment;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_shipment")
 */
#[ORM\Entity]
#[ORM\Table(name: 'sylius_shipment')]
-class Shipment extends BaseShipment
+class Shipment extends BaseShipment implements MonsieurBizShipmentInterface
{
+    use ShipmentTrait;
}
```

4. Your `ShippingMethod` entity should implement the `MonsieurBiz\SyliusShippingSlotPlugin\Entity\ShippingMethodInterface` and use the `MonsieurBiz\SyliusShippingSlotPlugin\Entity\ShippingMethodTrait` trait:

```diff
namespace App\Entity\Shipping;

use Doctrine\ORM\Mapping as ORM;
+use MonsieurBiz\SyliusShippingSlotPlugin\Entity\ShippingMethodInterface as MonsieurBizShippingMethodInterface;
+use MonsieurBiz\SyliusShippingSlotPlugin\Entity\ShippingMethodTrait;
use Sylius\Component\Core\Model\ShippingMethod as BaseShippingMethod;
use Sylius\Component\Shipping\Model\ShippingMethodTranslationInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_shipping_method")
 */
#[ORM\Entity]
#[ORM\Table(name: 'sylius_shipping_method')]
-class ShippingMethod extends BaseShippingMethod
+class ShippingMethod extends BaseShippingMethod implements MonsieurBizShippingMethodInterface
{
+    use ShippingMethodTrait {
+        ShippingMethodTrait::__construct as private shippingMethodTraitConstruct;
+    }
+
+    public function __construct()
+    {
+        parent::__construct();
+        $this->shippingMethodTraitConstruct();
+    }
+
    protected function createTranslation(): ShippingMethodTranslationInterface
    {
        return new ShippingMethodTranslation();
    }
}
```

5. Update your database schema with the following command:

```bash
bin/console doctrine:migrations:migrate
```

6. Generate the migration and update your database schema:

```bash
bin/console doctrine:migrations:diff
bin/console doctrine:migrations:migrate
```

## Sponsors


- Glacier1891
- WahsWash

## Contributing

You can open an issue or a Pull Request if you want! 😘  
Thank you!
