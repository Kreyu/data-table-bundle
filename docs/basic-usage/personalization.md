---
order: i
---

# Personalization

Although the product table shown in the examples is tiny, imagine that it actually contains dozens of other columns — making it quickly unreadable! 
In addition, each user may prefer a different order of these columns. 
This is where the personalization functionality comes to the rescue, allowing you to freely show or hide the columns, and even determine their order.

## Enabling the personalization feature

The personalization feature is disabled for each data table by default. 
There are multiple ways to configure the personalization feature, but for now, let's do it globally. 
Navigate to the package configuration file (or create one if it doesn't exist) and change it like so:

+++ YAML
```yaml # config/packages/kreyu_data_table.yaml
kreyu_data_table:
  defaults:
    personalization:
      enabled: true
```
+++ PHP
```php # config/packages/kreyu_data_table.php
use Symfony\Config\KreyuDataTableConfig;

return static function (KreyuDataTableConfig $config) {
    $defaults = $config->defaults();
    $defaults->personalization()->enabled(true);
};
```
+++

The personalization feature may look really handy, but try refreshing the page after applying the personalization - it's gone! 
Now imagine configuring it on every request as the user - nightmare :ghost:
This can be solved by [enabling the persistence feature](../basic-usage/persisting-applied-data.md), 
which will save the personalization data (and even the applied pagination, sorting and filters if you wish!) between requests, per user.
