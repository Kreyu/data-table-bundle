# Responsive

The data tables can adapt to narrow viewports by hiding columns below a configurable breakpoint and exposing them through a **collapsible row** per data row. This is useful for tables with many columns that would otherwise overflow on phones and tablets.

[[toc]]

## Prerequisites

To begin with, make sure the [Symfony UX integration is enabled](../installation.md#enable-the-symfony-ux-integration).
The **responsive** controller is enabled by default. If you have explicitly disabled it in your `assets/controllers.json`, re-enable it:

```json
{
    "controllers": {
        "@kreyu/data-table-bundle": {
            "responsive": {
                "enabled": true
            }
        }
    }
}
```

Additionally, the responsive feature relies on [Turbo](https://turbo.hotwired.dev/) to reload the data table frame when the active breakpoint changes on resize. Turbo is also a requirement of the [asynchronicity](asynchronicity.md) feature.

## Toggling the feature

By default, the responsive feature is **disabled** for every data table.

You can change this setting globally using the package configuration file, or use the `responsive_enabled` option:

::: code-group
```yaml [Globally (YAML)]
kreyu_data_table:
  responsive:
    enabled: true
```

```php [Globally (PHP)]
use Symfony\Config\KreyuDataTableConfig;

return static function (KreyuDataTableConfig $config) {
    $config->responsive()->enabled(true);
};
```

```php [For data table type]
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductDataTableType extends AbstractDataTableType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'responsive_enabled' => true,
        ]);
    }
}
```

```php [For specific data table]
use App\DataTable\Type\ProductDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;

    public function index()
    {
        $dataTable = $this->createDataTable(
            type: ProductDataTableType::class,
            query: $query,
            options: [
                'responsive_enabled' => true,
            ],
        );
    }
}
```
:::

::: warning The `responsive` configuration lives at the root level
Unlike most other features, responsive options are defined at `kreyu_data_table.responsive.*`, **not** under `kreyu_data_table.defaults.*`. Breakpoints are global by design and shared across every data table type that opts into responsiveness.
:::

## Configuring breakpoints

Breakpoints are declared as an associative array of `name => max width in pixels`. A column associated with a breakpoint remains visible as long as the resolved active breakpoint is greater than or equal to it.

The default breakpoints follow Bootstrap conventions:

| Name | Max width (px) |
|------|----------------|
| `sm` | 576            |
| `md` | 768            |
| `lg` | 992            |
| `xl` | 1200           |

You can override them globally, or per data table using the `responsive_breakpoints` option:

::: code-group
```yaml [Globally (YAML)]
kreyu_data_table:
  responsive:
    enabled: true
    breakpoints:
      sm: 576
      md: 768
      lg: 992
      xl: 1200
      xxl: 1400
```

```php [Globally (PHP)]
use Symfony\Config\KreyuDataTableConfig;

return static function (KreyuDataTableConfig $config) {
    $config->responsive()
        ->enabled(true)
        ->breakpoints([
            'sm' => 576,
            'md' => 768,
            'lg' => 992,
            'xl' => 1200,
            'xxl' => 1400,
        ])
    ;
};
```

```php [For data table type]
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductDataTableType extends AbstractDataTableType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'responsive_enabled' => true,
            'responsive_breakpoints' => [
                'sm' => 480,
                'md' => 720,
                'lg' => 1024,
            ],
        ]);
    }
}
```
:::

::: tip Order does not matter
Breakpoints are automatically sorted from the smallest to the largest value at runtime, so you can declare them in any order.
:::

## Controlling column visibility

Each column can opt into being hidden below a minimum breakpoint using the `visible_from` option.

| Value              | Behavior                                                                    |
|--------------------|-----------------------------------------------------------------------------|
| `null` *(default)* | The column is always visible, regardless of the active breakpoint.          |
| `'sm'`, `'md'`, …  | The column is visible only when the active breakpoint is `>= visible_from`. |
| `false`            | The column is always moved into the collapsible row.                        |

```php src/DataTable/Type/ProductDataTableType.php
use Kreyu\Bundle\DataTableBundle\Column\Type\ActionsColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addColumn('id', NumberColumnType::class, [
                // Always visible.
            ])
            ->addColumn('name', TextColumnType::class, [
                // Always visible.
            ])
            ->addColumn('description', TextColumnType::class, [
                // Hidden below the `lg` breakpoint.
                'visible_from' => 'lg',
            ])
            ->addColumn('actions', ActionsColumnType::class, [
                // Always in the collapsible row, regardless of viewport width.
                'visible_from' => false,
            ])
        ;
    }
}
```

::: warning `visible_from` requires responsive mode
Setting `visible_from` to anything other than `null` on a data table that is not responsive-enabled (or referencing an unknown breakpoint name) throws an `InvalidArgumentException` at build time. Always enable `responsive_enabled` before using `visible_from`, and make sure any string value matches a declared breakpoint.
:::

## Activating a responsive theme

The default themes do not render collapsible rows. To get the full responsive output, switch to one of the responsive themes:

- `@KreyuDataTable/themes/bootstrap_5_responsive.html.twig` — extends the Bootstrap 5 theme.
- `@KreyuDataTable/themes/tabler_responsive.html.twig` — extends the Tabler theme.

::: code-group
```yaml [YAML]
# config/packages/kreyu_data_table.yaml
kreyu_data_table:
  defaults:
    themes:
      - '@KreyuDataTable/themes/bootstrap_5_responsive.html.twig'
  responsive:
    enabled: true
```

```php [PHP]
use Symfony\Config\KreyuDataTableConfig;

return static function (KreyuDataTableConfig $config) {
    $config->defaults()->themes([
        '@KreyuDataTable/themes/bootstrap_5_responsive.html.twig',
    ]);

    $config->responsive()->enabled(true);
};
```
:::

### Using responsive blocks in a custom theme

If you maintain your own theme, you can reuse the shared responsive blocks via Twig's `{% use %}` tag. Each block is importable with an alias and can be selectively rendered depending on the `responsive_enabled` variable:

```twig
{% extends '@KreyuDataTable/themes/bootstrap_5.html.twig' %}
{% use '@KreyuDataTable/themes/_responsive.html.twig' with
    kreyu_data_table as _responsive_kreyu_data_table,
    kreyu_data_table_attributes as _responsive_kreyu_data_table_attributes,
    table_head_row as _responsive_table_head_row,
    table_body_value_row as _responsive_table_body_value_row
%}

{% block kreyu_data_table %}{{ responsive_enabled|default(false) ? block('_responsive_kreyu_data_table') : parent() }}{% endblock %}
{% block kreyu_data_table_attributes %}{{ responsive_enabled|default(false) ? block('_responsive_kreyu_data_table_attributes') : parent() }}{% endblock %}
{% block table_head_row %}{{ responsive_enabled|default(false) ? block('_responsive_table_head_row') : parent() }}{% endblock %}
{% block table_body_value_row %}{{ responsive_enabled|default(false) ? block('_responsive_table_body_value_row') : parent() }}{% endblock %}
```

The collapsible row layout can be further customized by overriding the `responsive_toggle_header`, `responsive_toggle_cell` and `responsive_collapsible_row` blocks.

## How it works

When responsive mode is enabled, the data table goes through the following lifecycle on every request:

1. **Server-side estimation.** On the first render, the bundle asks the configured [`DeviceDetectorInterface`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Responsive/DeviceDetectorInterface.php) (by default [`UserAgentDeviceDetector`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Responsive/UserAgentDeviceDetector.php)) to map the incoming User-Agent to a `Device` (`Phone`, `Tablet` or `Desktop`). This device is then translated to a breakpoint via a simple fallback: `Phone` → smallest configured breakpoint, `Tablet` → median one, `Desktop` → largest one.
2. **Initial render.** Columns whose `visible_from` breakpoint is greater than the active one are moved into a collapsible row. The rendered container is marked with the `kreyu-dt-responsive-pending` class so it stays invisible until the client confirms the active breakpoint — this avoids a flash of incorrectly sized content.
3. **Client-side measurement.** The Stimulus controller (`kreyu--data-table-bundle--responsive`) observes the `<turbo-frame>` width with a [`ResizeObserver`](https://developer.mozilla.org/en-US/docs/Web/API/ResizeObserver). On `connect()`, if the measured breakpoint matches the server's, it simply removes the `kreyu-dt-responsive-pending` class to reveal the table. Otherwise, it performs a Turbo reload with `?_breakpoint=<name>` appended to the frame URL so the server can re-render with the correct breakpoint.
4. **Ongoing resize handling.** Subsequent resizes go through a 250 ms debounce; a reload is triggered only when the computed breakpoint changes, not on every pixel change.

The request handler reads the `_breakpoint` query parameter when present; otherwise it falls back to the server-side estimation. This keeps URLs shareable — a link captured on a desktop keeps rendering the "desktop" layout when pasted into another desktop browser.

## Extending

### Customizing device detection

The User-Agent-based detector is deliberately conservative. If you have better information about the client (e.g. a CDN header, a device-class cookie, a user preference), provide your own implementation of [`DeviceDetectorInterface`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Responsive/DeviceDetectorInterface.php):

```php
namespace App\DataTable\Responsive;

use Kreyu\Bundle\DataTableBundle\Responsive\Device;
use Kreyu\Bundle\DataTableBundle\Responsive\DeviceDetectorInterface;
use Symfony\Component\HttpFoundation\Request;

final class CloudFrontDeviceDetector implements DeviceDetectorInterface
{
    public function detect(Request $request): Device
    {
        return match ($request->headers->get('CloudFront-Is-Mobile-Viewer')) {
            'true' => Device::Phone,
            default => match ($request->headers->get('CloudFront-Is-Tablet-Viewer')) {
                'true' => Device::Tablet,
                default => Device::Desktop,
            },
        };
    }
}
```

With autowiring enabled, aliasing the interface to your service is enough:

```yaml
# config/services.yaml
services:
    Kreyu\Bundle\DataTableBundle\Responsive\DeviceDetectorInterface:
        alias: App\DataTable\Responsive\CloudFrontDeviceDetector
```

### Breakpoint names

The [`Breakpoint`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Responsive/Breakpoint.php) class exposes the default breakpoint names (`Breakpoint::SM`, `MD`, `LG`, `XL`) as constants, which can be used instead of raw strings in your column definitions:

```php
use Kreyu\Bundle\DataTableBundle\Responsive\Breakpoint;

$builder->addColumn('description', TextColumnType::class, [
    'visible_from' => Breakpoint::LG,
]);
```

## Troubleshooting

::: warning My column is never visible
Ensure both of the following:
- `responsive_enabled` is `true` on the data table (or globally).
- A responsive theme is configured in `defaults.themes` (e.g. `@KreyuDataTable/themes/bootstrap_5_responsive.html.twig`). Without it, the default theme will not render collapsible rows, so hidden columns simply disappear.
:::

::: warning The breakpoint does not update when I resize the window
Verify that:
- Turbo is installed and the page is not served outside of its scope (the controller needs to locate the `<turbo-frame>` wrapping the table).
- The `responsive` Stimulus controller is enabled in `assets/controllers.json` (it is enabled by default).
- Your browser supports [`ResizeObserver`](https://caniuse.com/resizeobserver) — all evergreen browsers do.
:::

::: warning I get "The 'visible_from' option references unknown breakpoint '…'"
The string passed to `visible_from` must match a breakpoint name declared in `responsive_breakpoints` (or the globally configured ones). Either use one of the defaults (`sm`, `md`, `lg`, `xl`), or make sure your custom breakpoint is registered before referencing it.
:::
