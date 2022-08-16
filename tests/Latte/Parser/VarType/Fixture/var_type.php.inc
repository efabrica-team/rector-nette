{varType RectorNette\Tests\Rector\Latte\RenameMethodLatteRector\Source\SomeClass $abcd}
{varType string $text}
{varType int $number}

<div class="container">
    {$text} - {$number}
</div>
-----
Array
(
    [0] => RectorNette\ValueObject\LatteVariableType Object
        (
            [name:RectorNette\ValueObject\LatteVariableType:private] => abcd
            [type:RectorNette\ValueObject\LatteVariableType:private] => RectorNette\Tests\Rector\Latte\RenameMethodLatteRector\Source\SomeClass
        )

    [1] => RectorNette\ValueObject\LatteVariableType Object
        (
            [name:RectorNette\ValueObject\LatteVariableType:private] => text
            [type:RectorNette\ValueObject\LatteVariableType:private] => string
        )

    [2] => RectorNette\ValueObject\LatteVariableType Object
        (
            [name:RectorNette\ValueObject\LatteVariableType:private] => number
            [type:RectorNette\ValueObject\LatteVariableType:private] => int
        )

)
