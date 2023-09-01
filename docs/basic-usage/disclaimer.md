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

```php # src/Entity/Product.php
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

For the sake of simplicity, the Doctrine mapping is skipped in the code block above.

## Frontend

The examples contain screenshots using the built-in [Tabler UI Kit](https://tabler.io/) theme. 
