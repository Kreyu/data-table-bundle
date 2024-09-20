# Profiler

The bundle has a built-in integration with the [Symfony Profiler](https://symfony.com/doc/current/profiler.html).

## Usage

If at least one data table was created, the toolbar will include a new tab:

![profiler_toolbar.png](/profiler_toolbar.png)

Clicking it will redirect you to the _Data Tables_ profiler tab:

![profiler_tab.png](/profiler_tab.png)

Here you can inspect every single part of each data table:

- quick overview - is this column sortable? is this filter applied?
- type class of each component;
- which options were passed;
- how those options got resolved;
- variables available in views, passed to the templates;
- data of each value row of the current page;

## Configuration

Because the amount of data collected for this integration can be massive,
the maximum depth of serialization can be adjusted in the bundle configuration:

```yaml
kreyu_data_table:
  profiler:
    max_depth: 3
```

Increasing the `max_depth` value will result in collecting and displaying deeper objects.
If you wish to disable this limitation completely, set this value to `-1`.

::: warning
Increasing the depth **will** result in the browser freezes after opening the profiler tab.
::: 
