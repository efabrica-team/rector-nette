<?php

namespace Rector\Nette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Fixture;

use DateTimeInterface;

class ClassWithInjects8Parent
{
    /**
     * @var DateTimeInterface
     * @inject
     */
    public $dateTime;
}

class ClassWithInjects8 extends ClassWithInjects8Parent
{
    /**
     * @var \Rector\Nette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Source\SomeProduct
     * @inject
     */
    public $property;
}

?>
-----
<?php

namespace Rector\Nette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Fixture;

use DateTimeInterface;

class ClassWithInjects8Parent
{
    public function __construct(private \DateTimeInterface $dateTime)
    {
    }
}

class ClassWithInjects8 extends ClassWithInjects8Parent
{
    public function __construct(\DateTimeInterface $dateTime, private \Rector\Nette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Source\SomeProduct $property)
    {
        parent::__construct($dateTime);
    }
}

?>
