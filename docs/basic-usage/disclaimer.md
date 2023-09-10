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
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Product
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private int $id;
    
    #[ORM\Column]
    private string $name;
    
    #[ORM\Column]
    private \DateTimeInterface $createdAt;
    
    public function getId(): int 
    {
        return $this->id;
    }
    
    public function getName(): string 
    {
        return $this->name;
    }
    
    public function getCreatedAt(): \DateTimeInterface 
    {
        return $this->createdAt;
    }
}
```

## Frontend

The examples contain screenshots using the built-in [Tabler UI Kit](https://tabler.io/) theme. 
