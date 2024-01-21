---
# https://vitepress.dev/reference/default-theme-home-page
layout: home

hero:
  name: "DataTableBundle"
  text: for Symfony
  tagline: Streamlines creation process of the data tables
  image:
    src: /logo.png
    alt: DataTableBundle
  actions:
    - theme: brand
      text: Documentation
      link: /docs/introduction
    - theme: alt
      text: Reference
      link: /reference/types/data-table

features:
  - title: Type classes
    details: Class-based configuration, like in a Symfony Form component
  - title: Sorting, filtering, pagination
    details: Classic triforce of the data tables
  - title: Personalization
    details: User decides the order and visibility of columns
  - title: Persistence
    details: Saving applied data (e.g. filters) between requests
  - title: Exporting
    details: Popular formats, with or without applied pagination, filters and personalization - you name it
  - title: Theming
    details: Every template part of the bundle is customizable using Twig
  - title: Data source agnostic
    details: With Doctrine ORM supported out of the box
  - title: Asynchronicity
    details: Thanks to integration with Hotwire Turbo, data tables are asynchronous
---


<style>
:root {
  --vp-home-hero-image-background-image: linear-gradient(-50deg, rgba(50, 191, 252, 0.5) 25%, rgba(121, 134, 203, 0.5) 50%);
  --vp-home-hero-image-filter: blur(90px);
}
</style>