<?php

namespace RectorNette\Tests\Rector\Class_\FormDataRector\Fixture;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

class MyFormFactory
{
    public function create()
    {
        $form = new Form();

        $form->addText('foo', 'Foo');
        $form->addText('bar', 'Bar')->setRequired();
        $form->onSuccess[] = function (Form $form, ArrayHash $values) {
            // do something
        };
    }
}

?>
-----
<?php

namespace RectorNette\Tests\Rector\Class_\FormDataRector\Fixture;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

class MyFormFactory
{
    public function create()
    {
        $form = new Form();

        $form->addText('foo', 'Foo');
        $form->addText('bar', 'Bar')->setRequired();
        $form->onSuccess[] = function (Form $form, \RectorNette\Tests\Rector\Class_\FormDataRector\Fixture\MyFormFactoryFormData $values) {
            // do something
        };
    }
}

?>
-----
MyFormFactoryFormData.php
-----
-----
<?php

namespace RectorNette\Tests\Rector\Class_\FormDataRector\Fixture;

class MyFormFactoryFormData extends \Nette\Utils\ArrayHash
{
    use \Nette\SmartObject;
    public string $foo;
    public string $bar;
}
