<?php

namespace RectorNette\Tests\NodeFinder\FormFinder\Fixture;

use Nette\Application\UI\Form;

class SimpleForm
{
    public function create(): Form
    {
        $form = new Form();
        $form->addText('foo', 'Foo');
        $form->addTextArea('bar', 'Bar');
        $form->addInteger('baz', 'Baz');

        return $form;
    }
}
-----
{
    "foo": {
        "type": "string",
        "required": false
    },
    "bar": {
        "type": "string",
        "required": false
    },
    "baz": {
        "type": "int",
        "required": false
    }
}
