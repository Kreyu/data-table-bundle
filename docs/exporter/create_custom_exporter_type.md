# How to Create a Custom Exporter Type

This bundle comes with multiple exporter types, all of which are integrating the [PhpSpreadsheet](https://phpspreadsheet.readthedocs.io/).
However, it's common to create custom exporter types to solve specific purposes in your projects.

## Creating Type Class

Exporter types are PHP classes that implement [ExporterTypeInterface](../../src/Exporter/Type/ExporterTypeInterface.php), but you should instead extend from [AbstractType](../../src/Exporter/Type/AbstractType.php),
which already implements that interface and provides some utilities.

By convention, they are stored in the `src/DataTable/Exporter/Type/` directory:

```php
// src/DataTable/Exporter/Type/CustomType.php
namespace App\DataTable\Exporter\Type;

use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\AbstractType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomType extends AbstractType
{
    public function export(DataTableView $view, array $options = []): File
    {
        $contents = ...
        
        // use the "getTempnam()" helper method from AbstractType to generate a filename
        // to store the temporarily store the exported file based on the exporter options. 
        $tempnam = $this->getTempnam($options);
        
        $filesystem = new \Symfony\Component\Filesystem\Filesystem();
        $filesystem->dumpFile($tempnam);
        
        return new File($tempnam);
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        // ...
    }
}
```

These are the most important methods that a column type class can define:

`export()`

It handles all the export logic by retrieving the [DataTableView](../../src/DataTableView.php) with exporter options, and outputting an instance of [File](https://github.com/symfony/symfony/blob/6.3/src/Symfony/Component/HttpFoundation/File/File.php).

`configureOptions()`

It defines the options configurable when using the exporter type, which are also passed to the `export` method.
Options are inherited from parent types, but you can create any custom option you need.

`getParent()`

If your custom type is based on another type (i.e. they share some functionality), add this method to return the fully-qualified class name of that original type.
**Do not use PHP inheritance for this**. This bundle will call all the column type methods and type extensions of the parent before calling the ones defined in your custom type.

Otherwise, if your custom type is build from scratch, you can omit `getParent()`.

By default, the `AbstractType` class returns the generic [ExporterType](../../src/Exporter/Type/ExporterType.php) type, which is the root parent for all exporter types.
