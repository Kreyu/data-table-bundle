---
order: 6
---

# Introduction

This bundle aims to streamline the creation process of the data tables in Symfony applications.

!!! Note
Despite the name, this bundle has **no correlation** with [jQuery Data Tables](https://datatables.net/).
!!!

## Features

- Class based configuration with [types](features/type-classes.md) similar to [Symfony Forms](https://symfony.com/doc/current/forms.html)
- Triforce of the data tables — [paginating](features/pagination.md), [sorting](features/sorting.md) and [filtering](features/filtering.md)
- [Personalization](features/personalization.md) where the user decides the order and visibility of columns
- [Persistence](features/persistence.md) to save user-applied pagination, sorting, filters and personalization
- Customizable [exporting](features/exporting.md), respecting personalization and applied filters
- [Theming](features/theming.md) of every part of the bundle
- [Data source agnostic](features/proxy-queries.md) with Doctrine ORM supported out of the box
- Built-in [integration with Symfony UX Turbo](features/symfony-ux-turbo.md) for asynchronicity
