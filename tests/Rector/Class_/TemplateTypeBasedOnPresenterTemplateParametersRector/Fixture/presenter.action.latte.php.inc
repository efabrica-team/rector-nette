<?php

namespace Rector\Nette\Tests\Rector\Class_\TemplateTypeBasedOnPresenterTemplateParametersRector\Fixture;

use Nette\Application\UI\Presenter;

class SomePresenter extends Presenter
{
    public function renderDefault(): void
    {
        $this->template->title = 'My title';
        $this->template->count = 123;
    }
}

?>
-----
-----
SomeDefaultTemplate.php
-----
-----
<?php

namespace Rector\Nette\Tests\Rector\Class_\TemplateTypeBasedOnPresenterTemplateParametersRector\Fixture;

class SomeDefaultTemplate extends \Nette\Bridges\ApplicationLatte\Template
{
    public string $title;
    public int $count;
}
-----
templates/Some.default.latte
-----
<h1>{$title}</h1>
<span class="count">{$count}</span>
-----
{templateType Rector\Nette\Tests\Rector\Class_\TemplateTypeBasedOnPresenterTemplateParametersRector\Fixture\SomeDefaultTemplate}

<h1>{$title}</h1>
<span class="count">{$count}</span>
