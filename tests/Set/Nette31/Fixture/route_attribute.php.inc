<?php

class SomePresenter
{
    /**
     * @crossOrigin
     */
    public $someProperty;
}

?>
-----
<?php

class SomePresenter
{
    #[\Nette\Application\Attributes\CrossOrigin]
    public $someProperty;
}

?>
