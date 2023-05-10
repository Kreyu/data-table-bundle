---
order: a
---

# Disclaimer

This chapter explains how to quickly start using the bundle.

!!! Note
The articles are **not** representing every bundle feature to the fullest.  
Instead, they contain links to the reference section, where you can about each feature in depth.
!!! 

## Entities

The articles assume, that the project uses [Doctrine ORM](https://www.doctrine-project.org/projects/orm.html) and contains a Product entity:

{% code title="src/Entity/Product.php" lineNumbers="true" %}
```php
class Product
{
    private int $id;
    private string $name;
    private \DateTimeInterface $createdAt;
    
    public function getId(): int {}
    public function getName(): string {}
    public function getCreatedAt(): \DateTimeInterface {}
}
```
{% endcode %}

For the sake of simplicity, the Doctrine mapping is skipped in the code block above.

## Frontend

The examples contain screenshots using the built-in [Tabler UI Kit](https://tabler.io/) theme.  
Therefore, it assumes that the project has the [Tabler UI Kit](https://tabler.io/) styles and scripts included.
