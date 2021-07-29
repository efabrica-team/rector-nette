<?php

declare(strict_types=1);

namespace Rector\Nette\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\NodeFactory\ClassWithPublicPropertiesFactory;
use Rector\Core\Rector\AbstractRector;
use Rector\FileSystemRector\ValueObject\AddedFileWithContent;
use Rector\Nette\NodeFinder\FormFieldsFinder;
use Rector\Nette\NodeFinder\FormOnSuccessCallbackFinder;
use Rector\Nette\NodeFinder\FormOnSuccessCallbackValuesParamFinder;
use Rector\Nette\NodeFinder\FormVariableFinder;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

/**
 * @see https://doc.nette.org/en/3.1/form-presenter#toc-mapping-to-classes
 * @see \Rector\Nette\Tests\Rector\Class_\FormDataRector\FormDataRectorTest
 */
final class FormDataRector extends AbstractRector implements ConfigurableRectorInterface
{
    public const FORM_DATA_CLASS_PARENT = 'form_data_class_parent';

    public const FORM_DATA_CLASS_TRAITS = 'form_data_class_traits';

    private string $formDataClassParent = 'Nette\Utils\ArrayHash';

    /**
     * @var string[]
     */
    private array $formDataClassTraits = ['Nette\SmartObject'];

    public function __construct(
        private FormVariableFinder $formVariableFinder,
        private FormFieldsFinder $formFieldsFinder,
        private FormOnSuccessCallbackFinder $formOnSuccessCallbackFinder,
        private FormOnSuccessCallbackValuesParamFinder $formOnSuccessCallbackValuesParamFinder,
        private ClassWithPublicPropertiesFactory $classWithPublicPropertiesFactory
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Create form data class with all fields of Form', [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
class MyFormFactory
{
    public function create()
    {
        $form = new Form();

        $form->addText('foo', 'Foo');
        $form->addText('bar', 'Bar')->setRequired();
        $form->onSuccess[] = function (Form $form, ArrayHash $values) {
            // do something
        }
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class MyFormFactoryFormData
{
    public string $foo;
    public string $bar;
}

class MyFormFactory
{
    public function create()
    {
        $form = new Form();

        $form->addText('foo', 'Foo');
        $form->addText('bar', 'Bar')->setRequired();
        $form->onSuccess[] = function (Form $form, MyFormFactoryFormData $values) {
            // do something
        }
    }
}
CODE_SAMPLE
                ,
                [
                    self::FORM_DATA_CLASS_PARENT => '',
                    self::FORM_DATA_CLASS_TRAITS => [],
                ]
            ),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    public function configure(array $configuration): void
    {
        if (isset($configuration[self::FORM_DATA_CLASS_PARENT])) {
            Assert::string($configuration[self::FORM_DATA_CLASS_PARENT]);
            $this->formDataClassParent = $configuration[self::FORM_DATA_CLASS_PARENT];
        }
        if (isset($configuration[self::FORM_DATA_CLASS_TRAITS])) {
            Assert::isArray($configuration[self::FORM_DATA_CLASS_TRAITS]);
            $this->formDataClassTraits = $configuration[self::FORM_DATA_CLASS_TRAITS];
        }
    }

    /**
     * @param Class_ $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($node->name === null) {
            return null;
        }

        $shortClassName = $this->nodeNameResolver->getShortName($node);

        $fullClassName = $this->getName($node);
        $form = $this->formVariableFinder->find($node);
        if ($form === null) {
            return null;
        }

        $formFields = $this->formFieldsFinder->find($node, $form);
        if ($formFields === []) {
            return null;
        }

        $properties = [];
        foreach ($formFields as $formField) {
            $properties[$formField->getName()] = [
                'type' => $formField->getType(),
                'nullable' => $formField->getType() === 'int' && $formField->isRequired() === false,
            ];
        }

        $formDataClassName = $shortClassName . 'FormData';
        $fullFormDataClassName = '\\' . $fullClassName . 'FormData';
        $formDataClass = $this->classWithPublicPropertiesFactory->createNode(
            $fullFormDataClassName,
            $properties,
            $this->formDataClassParent,
            $this->formDataClassTraits
        );

        $printedClassContent = "<?php\n\n" . $this->betterStandardPrinter->print($formDataClass) . "\n";

        $smartFileInfo = $this->file->getSmartFileInfo();
        $targetFilePath = $smartFileInfo->getRealPathDirectory() . '/' . $formDataClassName . '.php';

        $addedFileWithContent = new AddedFileWithContent($targetFilePath, $printedClassContent);
        $this->removedAndAddedFilesCollector->addAddedFile($addedFileWithContent);

        $onSuccessCallback = $this->formOnSuccessCallbackFinder->find($node, $form);
        if ($onSuccessCallback === null) {
            return null;
        }
        $valuesParam = $this->formOnSuccessCallbackValuesParamFinder->find($node, $onSuccessCallback);
        if ($valuesParam === null) {
            return null;
        }

        $valuesParam->type = new Identifier($fullFormDataClassName);
        return $node;
    }
}
