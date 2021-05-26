{varType Rector\Nette\Tests\Rector\Latte\RenameMethodLatteRector\Source\SomeClass $abcd}
{varType string $text}
{varType int $number}

<div class="container">
    {$text} - {$number}
</div>
-----
Array
(
    [0] => Rector\Nette\ValueObject\LatteVariableType Object
        (
            [name:Rector\Nette\ValueObject\LatteVariableType:private] => abcd
            [type:Rector\Nette\ValueObject\LatteVariableType:private] => Rector\Nette\Tests\Rector\Latte\RenameMethodLatteRector\Source\SomeClass
        )

    [1] => Rector\Nette\ValueObject\LatteVariableType Object
        (
            [name:Rector\Nette\ValueObject\LatteVariableType:private] => text
            [type:Rector\Nette\ValueObject\LatteVariableType:private] => string
        )

    [2] => Rector\Nette\ValueObject\LatteVariableType Object
        (
            [name:Rector\Nette\ValueObject\LatteVariableType:private] => number
            [type:Rector\Nette\ValueObject\LatteVariableType:private] => int
        )

)
