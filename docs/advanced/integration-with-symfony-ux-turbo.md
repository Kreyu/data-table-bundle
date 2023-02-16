# Integration with Symfony UX Turbo

Symfony UX Turbo is a Symfony bundle integrating the [Hotwire Turbo](https://turbo.hotwired.dev/) library in Symfony applications. 
It is part of [the Symfony UX initiative](https://symfony.com/ux).

Symfony UX Turbo allows having the same user experience as with [Single Page Apps](https://en.wikipedia.org/wiki/Single-page_application) 
but without having to write a single line of JavaScript!

_...read more about the bundle on [the official documentation](https://symfony.com/bundles/ux-turbo/current/index.html)_.

This bundle provides integration that works out-of-the-box.

## The magic part

To begin with, make sure your application uses the [Symfony UX Turbo](https://symfony.com/bundles/ux-turbo/current/index.html#usage).

The next step is... voilà! ✨ You don't have to configure anything extra, now your data tables work asynchronously!

The magic comes from the [@KreyuDataTable/themes/base.html.twig](https://github.com/Kreyu/data-table-bundle/blob/main/src/Resources/views/themes/base.html.twig) 
template, which wraps the whole bundle in the `<turbo-frame>` tag:

```twig
{% block kreyu_data_table %}
    <turbo-frame id="kreyu_data_table_{{ name }}">
        {# ... #}
    </turbo-frame>
{% endblock %}
```

!!! Note

    This also works out-of-the-box when using other built-in templates, because they all extend the base one.
    If you're making a data table theme from scratch, make sure the table is wrapped in the Turbo frame, as shown above.

For more information, see [official documentation about the Turbo frames](https://symfony.com/bundles/ux-turbo/current/index.html#decomposing-complex-pages-with-turbo-frames). 
