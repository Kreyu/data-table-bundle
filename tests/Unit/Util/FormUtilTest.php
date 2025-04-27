<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Util;

use Kreyu\Bundle\DataTableBundle\Util\FormUtil;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormView;

class FormUtilTest extends TestCase
{
    public function testGetFormViewValueRecursiveWithNoChildren()
    {
        $formView = new FormView();
        $formView->vars['value'] = 'foo';

        $result = FormUtil::getFormViewValueRecursive($formView);

        $this->assertEquals('foo', $result);
    }

    public function testGetFormViewValueRecursiveWithChildren()
    {
        $formViewFrom = new FormView();
        $formViewFrom->vars['name'] = 'from';
        $formViewFrom->vars['value'] = '2020-01-01';

        $formViewTo = new FormView();
        $formViewTo->vars['name'] = 'to';
        $formViewTo->vars['value'] = '2020-12-31';

        $formView = new FormView();
        $formView->children = [
            $formViewFrom->vars['name'] => $formViewFrom,
            $formViewTo->vars['name'] => $formViewTo,
        ];

        $result = FormUtil::getFormViewValueRecursive($formView);

        $this->assertEquals(['from' => '2020-01-01', 'to' => '2020-12-31'], $result);
    }
}
