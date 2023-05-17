---
order: j
---

# Persisting applied data

In complex applications, it can be very helpful to retain data such as applied personalization, filters, applied sorting or at least the currently displayed page. The bundle comes with the persistence feature, which can be freely configured for each feature individually.

Let's focus on persisting the applied personalization data first.&#x20;

## Prerequisites

For a basic usage, we're assuming that the persistence data will be saved to a **cache**, and are saved individually per **user**. 
Therefore, make sure the [Symfony Cache](https://symfony.com/doc/current/components/cache.html) and [Security](https://symfony.com/doc/current/security.html) components are installed and enabled. 
The bundle will automatically use them for persistence.

## Enabling the persistence feature

The personalization [persistence feature](../features/persistence.md) is disabled for each data table by default.
There are multiple ways to configure the persistence feature, but let's do it globally. 
Navigate to the package configuration file (or create one if it doesn't exist) and change it like so:

+++ YAML
```yaml # config/packages/kreyu_data_table.yaml
kreyu_data_table:
  defaults:
    personalization:
      persistence_enabled: true
```
+++ PHP
```php # config/packages/kreyu_data_table.php
use Symfony\Config\KreyuDataTableConfig;

return static function (KreyuDataTableConfig $config) {
    $defaults = $config->defaults();
    $defaults->personalization()->persistenceEnabled(true);
};
```
+++

Assuming that the user is authenticated, apply the personalization data again, refresh the page... the applied personalization is still there!

This basic example barely scratches the surface of the [persistence feature](../features/persistence.md). 
You can also persist applied pagination (e.g. current page), sorting, filters, 
use different adapters (to, for example, save the data to the database, instead of cache), 
or even use different subject providers (to, for example, not rely on authenticated user, but on the request IP).

There's still one thing to walk through — let's [translate the data table to multiple languages](../basic-usage/internationalization.md).
