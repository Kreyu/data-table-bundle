<script setup>
const props = defineProps({
    example: {
        type: String,
    },
})
</script>

Since Turbo v8, hovering over the links for more than 100ms **will prefetch their content**. This is enabled by default,
and this bundle is no exception. If you wish to disable prefetching for a specific link (e.g. pages with expensive rendering),
you can set the `data-turbo-prefetch` attribute to `false`, for example:

<slot></slot>

Alternatively you can disable it application wide using a meta tag:

```html
<meta name="turbo-prefetch" content="false">
```

For more information, see [official documentation about the prefetching links on hover](https://turbo.hotwired.dev/handbook/drive#prefetching-links-on-hover).
