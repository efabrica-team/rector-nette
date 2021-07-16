<?php

namespace Rector\Nette\Tests\NodeFinder\FormFinder\Fixture;

use Nette\Application\UI\Form;

class FormWithGroupsAndContainers
{
    public function create(): Form
    {
        $form = new Form();
        $form->addGroup('Group 1');
        $form->addText('foo', 'Foo');
        $form->addTextArea('bar', 'Bar');
        $form->addGroup('Group 2');
        $form->addInteger('baz', 'Baz')->setRequired();
        $container = $form->addContainer('container');
        $container->addText('x')->setRequired();
        $container->addText('y');
        $subcontainer = $container->addContainer('subcontainer');
        $subcontainer->addText('z')->setRequired();

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
        "required": true
    },
    "container": {
        "type": "array",
        "required": false
    }
}
