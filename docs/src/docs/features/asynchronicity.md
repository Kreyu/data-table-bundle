# Asynchronicity

[Symfony UX Turbo](https://symfony.com/bundles/ux-turbo/current/index.html) is a Symfony bundle integrating the [Hotwire Turbo](https://turbo.hotwired.dev/) library in Symfony applications.
It allows having the same user experience as with [Single Page Apps](https://en.wikipedia.org/wiki/Single-page_application) but without having to write a single line of JavaScript!

This bundle provides integration that works out-of-the-box.

## The magic part

Make sure your application uses the [Symfony UX Turbo](https://symfony.com/bundles/ux-turbo/current/index.html).
You don't have to configure anything extra, your data tables automatically work asynchronously!
The magic comes from the [base template](https://github.com/Kreyu/data-table-bundle/blob/main/src/Resources/views/themes/base.html.twig),
which wraps the whole table in the `<turbo-frame>` tag:

```twig
{# @KreyuDataTable/themes/base.html.twig #}
{% block kreyu_data_table %}
    <turbo-frame id="{{ data_table_turbo_identifier(data_table) }}">
        {# ... #}
    </turbo-frame>
{% endblock %}
```

This ensures every data table is wrapped in its own frame, making them work asynchronously.

<div class="tip custom-block" style="padding-top: 8px;">

This integration also works on other built-in templates, because they all extend the base one.
If you're making a data table theme from scratch, make sure the table is wrapped in the Turbo frame, as shown above.

</div>

For more information, see [official documentation about the Turbo frames](https://symfony.com/bundles/ux-turbo/current/index.html#decomposing-complex-pages-with-turbo-frames).
