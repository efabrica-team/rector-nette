<?php

namespace Rector\Nette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Fixture;

use Rector\Nette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Source;

class TestPresenter extends Source\BasePresenterWithConstructor
{
    /**
     * @inject
     */
    public \Nette\Security\Passwords $passwords;

    public function actionTest()
    {
        $this->passwords->hash('test');
    }
}
?>
-----
<?php

namespace Rector\Nette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Fixture;

use Rector\Nette\Tests\Rector\Property\NetteInjectToConstructorInjectionRector\Source;

class TestPresenter extends Source\BasePresenterWithConstructor
{
    public function __construct(string $baseParameter, private \Nette\Security\Passwords $passwords)
    {
        parent::__construct($baseParameter);
    }

    public function actionTest()
    {
        $this->passwords->hash('test');
    }
}
?>
