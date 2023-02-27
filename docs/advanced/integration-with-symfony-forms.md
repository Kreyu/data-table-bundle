# Integration with Symfony Forms

Imagine a following requirement: display a list of products, but with name and quantity as a form inputs. 
Additionally, display a submit button below the table to update every product name & quantity based on their corresponding inputs value.

Let's start by creating a form type responsible for updating a product:

```php
// src/Form/Type/ProductType.php
namespace App\Form\Type;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('quantity', NumberType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Product::class);
    }
}
```

Then, create a form using created type. In this example, we're using form & data table builders to keep it simple.

```php
// src/Controller/ProductController.php
namespace App\Controller;

use App\Form\Type\ProductType;
use App\Repository\ProductRepository;
use Kreyu\Bundle\DataTableBundle\Column\Type\FormType;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextType;
use Kreyu\Bundle\DataTableBundle\DataTableControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    use DataTableControllerTrait;
    
    public function index(Request $request, ProductRepository $repository): Response
    {
        $query = $repository->createQueryBuilder('product');

        $form = $this->createForm(CollectionType::class, options: [
            'entry_type' => ProductType::class,        
        ]);
        
        $dataTable = $this->createDataTableBuilder($query)
            ->addColumn('id', NumberType::class)
            ->addColumn('name', FormType::class, [
                'form' => $form,
            ])
            ->addColumn('quantity', FormType::class, [
                'form' => $form,
                // Specifying form child path is optional.
                // By default, the column name is used.
                'form_child_path' => 'quantity',
            ])
            ->getDataTable();

        $dataTable->handleRequest($request);

        // Fill form with products on the current data table page.
        // Important: remember to do it AFTER handling the request,
        // as this is what determines the current page! 
        $form->setData($dataTable->getPagination()->getItems());
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $products = $form->getData();
            
            // Here $products is an ArrayIterator of updated App\Entity\Product entities.
            // You can flush the entity manager to save the changes.
            
            $repository->flush(); 
        }
        
        return $this->render('product/index.html.twig', [
            'data_table' => $dataTable->createView(),
            'form' => $form->createView(),
        ]);
    }
}
```

Now, let's handle the templating part:

```twig
{# templates/product/index.html.twig #}

{{ data_table_form_aware(data_table, form, form_variables={ attr: { id: form.vars.id } }) }}

<input type="submit" form="{{ form.vars.id }}" value="Submit"/>
```

Notice the use of [data_table_form_aware() function](../reference/twig.md#datatableformawaredatatableview-formview-datatablevariables-formvariables).
This takes care of wrapping only the table part in the form. 
Because we are rendering the submit button outside the form, the `form` attribute is used on the submit button, which links to the form by `id`.

## Rendering without helper function

If your data table is **NOT** using neither filtration, exporting nor personalization features, 
you can use the [data_table() function](../reference/twig.md#data_tabledata_table_view-variables) as usual, wrapping it in the form:
```twig
{# templates/product/index.html.twig #}

{{ form_start(form) }}
    {{ data_table(data_table) }}

    <div class="mt-2">
        <button class="btn btn-primary">Update</button>
    </div>
{{ form_end(form, { render_rest: false }) }} {# Important: notice the "render_rest" option! #}
```

!!! Warning

    Rendering like this is risky - if someone decides to enable one of mentioned features, the whole markup will totally break.
    If possible, use below method of wrapping only the table part in the form.

If your data table is using either a filtration, exporting or personalization feature, you **HAVE TO** render each 
part of the table individually, because the [data_table() function](../reference/twig.md#data_tabledata_table_view-variables) 
renders out whole data table with corresponding feature form, and HTML forms cannot be nested, and this will totally break the markup;

```twig
{# templates/product/index.html.twig #}

{{ data_table_action_bar(data_table) }}

{{ form_start(form) }}
    {{ data_table_table(data_table) }}

    <div class="mt-2">
        <button class="btn btn-primary">Update</button>
    </div>
{{ form_end(form, { render_rest: false }) }} {# Important: notice the "render_rest" option! #}

{{ data_table_pagination(data_table) }}
```

!!! Warning

    You **HAVE TO** disable rendering rest of the fields in the `form_end` helper by passing the `render_rest` option as `false`, 
    otherwise all the form fields will be rendered again below the table. This is because the Symfony Forms have no way 
    of knowing the data table has rendered its form fields, because the bundle manually creates each field `FormView` in the background.

!!! Note

    If your application uses [Symfony UX Turbo](https://symfony.com/bundles/ux-turbo/current/index.html#usage), remember to wrap
    the whole data table in `<turbo-frame>` like in the base HTML template!

## Passing the form to the data table type class

While the above example is simple, it's not really re-usable, due to the usage of data table builder in the controller. 
That's why it's recommended to pass the form to the data table type:

```php
// src/DataTable/Type/ProductType.php
namespace App\DataTable\Type;

use Kreyu\Bundle\DataTableBundle\Column\Type\FormType;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder->addColumn('id', NumberType::class);
        
        if (null !== $form = $options['form']) {
            $builder
                ->addColumn('name', FormType::class, [
                    'form' => $form,                
                ])
                ->addColumn('quantity', FormType::class, [
                    'form' => $form,                
                ])
            ;           
        } else {
            $builder
                ->addColumn('name', TextType::class)
                ->addColumn('quantity', NumberType::class)
            ;
        }
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('form', null)
            ->setAllowedTypes('form', ['null', FormInterface::class])
        ;
    }
}
```

and in the controller:

```php
// src/Controller/ProductController.php
namespace App\Controller;

use App\DataTable\Type as DataTable;
use App\Form\Type as Form;
use App\Repository\ProductRepository;
use Kreyu\Bundle\DataTableBundle\DataTableControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    use DataTableControllerTrait;
    
    public function index(Request $request, ProductRepository $repository): Response
    {
        $query = $repository->createQueryBuilder('product');

        $form = $this->createForm(CollectionType::class, options: [
            'entry_type' => Form\ProductType::class,        
        ]);
        
        // The data table with "name" and "quantity" columns displayed as a form inputs.
        $dataTable = $this->createDataTable(DataTable\ProductType::class, $query, [
            'form' => $form,
        ]);
        
        // The data table with "name" and "quantity" columns displayed regularly, because form is not passed.
        $dataTable = $this->createDataTable(DataTable\ProductType::class, $query);

        // ...
    }
}
```

By building it this way, it is possible to re-use same data table type with and without form integration.
