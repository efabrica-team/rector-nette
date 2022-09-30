<?php

class SomePresenter
{
    /** @var Something @inject */
    public $someProperty;
}

?>
-----
<?php

class SomePresenter
{
    /** @var Something */
    #[\Nette\DI\Attributes\Inject]
    public $someProperty;
}

?>
