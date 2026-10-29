<script setup>
    import ActionTypeOptions from "./options/action.md";
</script>

# FormActionType

The [`FormActionType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/Type/FormActionType.php) represents an action rendered as a submit button to a hidden form, which allows the action to use any HTTP method. 

## Options

### `action`

- **type**: `string` or `\Closure`
- **default**: `'#'`

Sets the value that will be used as a form's `action` attribute.

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\FormActionType;

$builder
    ->addAction('send', FormActionType::class, [
        'action' => $this->urlGenerator->generate('sms_send'),
    ])
;
```

### `method`

- **type**: `string` or `\Closure`
- **default**: `'GET'`

Sets the value that will be used as a form's `method` attribute.

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\FormActionType;

$builder
    ->addAction('send', FormActionType::class, [
        'method' => 'POST',
    ])
;
```

### `button_attr`

- **type**: `array` or `\Closure`
- **default**: `[]`

An array of attributes used to render the form submit button.

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\ButtonActionType;

$builder
    ->addAction('remove', ButtonActionType::class, [
        'button_attr' => [
            'class' => 'btn btn-danger',
        ],
    ])
;
```

### `csrf_token_id`

- **type**: `null`, `string` or `\Closure` (if using as a row action)
- **default**: `null`

Adds a hidden `_token` field with a CSRF token for this id.

Requires `symfony/security-csrf` and `framework.csrf_protection` enabled:

```bash
composer require symfony/security-csrf
```

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\FormActionType;

$builder
    ->addRowAction('delete', FormActionType::class, [
        'method' => 'POST',
        'action' => fn (Post $post) => $this->urlGenerator->generate('post_delete', [
            'id' => $post->getId(),
        ]),
        'csrf_token_id' => 'delete',
    ])
;
```

Check the token in the controller (`#[IsCsrfTokenValid]` needs Symfony 7.1+):

```php
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsCsrfTokenValid;

#[Route('/posts/{id}/delete', name: 'post_delete', methods: ['POST'])]
#[IsCsrfTokenValid('delete')]
public function delete(Post $post): Response
{
    // ...
}
```

Symfony 6.x → in an `AbstractController`, call `$this->isCsrfTokenValid('delete', $request->request->get('_token'))`.

As a row action, a closure gets the row data and returns the token id, e.g. `fn (Post $post) => 'delete'.$post->getId()`.

## Inherited options

<ActionTypeOptions/>
