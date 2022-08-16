<?php

namespace RectorNette\Tests\Rector\Class_\LatteVarTypesBasedOnPresenterTemplateParametersRector\Fixture;

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
templates/Some/default.latte
-----
<h1>{$title}</h1>
<span class="count">{$count}</span>
-----
{varType string $title}
{varType int $count}

<h1>{$title}</h1>
<span class="count">{$count}</span>
