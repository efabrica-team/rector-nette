<?php

namespace Rector\Nette\Tests\NodeFinder\FormFinder\Fixture;

use Nette\Application\UI\Form;

class FormWithRequiredFields
{
    public function create(): Form
    {
        $form = new Form();
        $form->addText('foo', 'Foo');
        $form->addTextArea('bar', 'Bar')->setRequired();
        $form->addInteger('baz', 'Baz')->setRequired();

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
        "required": true
    },
    "baz": {
        "type": "int",
        "required": true
    }
}
