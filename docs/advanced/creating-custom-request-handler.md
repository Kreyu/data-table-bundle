# Creating custom request handler

This bundle comes with [HttpFoundation request handler](../src/Request/HttpFoundationRequestHandler.php) built-in.
However, it's common to create custom request handlers to solve specific purposes in your projects.

To create a custom request handler, create a class that implements [RequestHandlerInterface](../src/Request/RequestHandlerInterface.php):

```php
// src/DataTable/Request/ExcelRequestHandler.php
namespace App\DataTable\Request;

use Kreyu\Bundle\DataTableBundle\Request\RequestHandlerInterface;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingField;

class ExcelRequestHandler implements RequestHandlerInterface
{
    public function handle(DataTableInterface $dataTable, mixed $request = null): void
    {
        $sortingData = new SortingData([
            new SortingField($request['name'], $request['direction']),
        ]);
        
        $dataTable->sort($sortingData); 
    }
}
```

The interface only contains a single `handle()` method, will all the necessary data: a data table and a request object.

From here, for example, you can call `sort()`, `paginate()`, `personalize()` and `filter()` methods with data extracted from the request object.
