<?php
use Nette\Security\Passwords;

class TestPresenter extends BasePresenter
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

abstract class BasePresenter
{
    public function __construct()
    {
    }
}
?>
-----
<?php
use Nette\Security\Passwords;

class TestPresenter extends BasePresenter
{
    public function __construct(private \Nette\Security\Passwords $passwords)
    {
        parent::__construct();
    }

    public function actionTest()
    {
        $this->passwords->hash('test');
    }
}

abstract class BasePresenter
{
    public function __construct()
    {
    }
}
?>
