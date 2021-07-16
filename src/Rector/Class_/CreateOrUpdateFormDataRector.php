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
use Rector\Nette\NodeFinder\FormFinder;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

/**
 * @see https://doc.nette.org/en/3.1/form-presenter#toc-mapping-to-classes
 */
final class CreateOrUpdateFormDataRector extends AbstractRector implements ConfigurableRectorInterface
{
    public const FORM_DATA_CLASS_PARENT = 'form_data_class_parent';

    public const FORM_DATA_CLASS_TRAITS = 'form_data_class_traits';

    private ?string $formDataClassParent = '\Nette\Utils\ArrayHash';

    private array $formDataClassTraits = ['\Nette\SmartObject'];

    public function __construct(
        private FormFinder $formFinder,
        private ClassWithPublicPropertiesFactory $classWithPublicPropertiesFactory
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Create or update form data class with all fields of Form', [
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
                    self::FORM_DATA_CLASS_PARENT => null,
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
        if (array_key_exists(self::FORM_DATA_CLASS_PARENT, $configuration)) {
            Assert::nullOrString($configuration[self::FORM_DATA_CLASS_PARENT]);
            $this->formDataClassParent = $configuration[self::FORM_DATA_CLASS_PARENT];
        }
        if (array_key_exists(self::FORM_DATA_CLASS_TRAITS, $configuration)) {
            Assert::isArray($configuration[self::FORM_DATA_CLASS_TRAITS]);
            $this->formDataClassTraits = $configuration[self::FORM_DATA_CLASS_TRAITS];
        }
    }

    /**
     * @param Class_ $node
     */
    public function refactor(Node $node): ?Node
    {
        $className = $node->name->name;

        $fullClassName = $this->getName($node);
        $form = $this->formFinder->findFormVariable($node);
        if ($form === null) {
            return null;
        }

        $formFields = $this->formFinder->findFormFields($node, $form);
        $properties = [];
        foreach ($formFields as $fieldName => $fieldProperties) {
            $properties[$fieldName] = [
                'type' => $fieldProperties['type'],
                'nullable' => $fieldProperties['type'] === 'int' && $fieldProperties['required'] === false,
            ];
        }

        $formDataClassName = $className . 'FormData';
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

        $onSuccessCallback = $this->formFinder->findOnSuccessCallback($node, $form);
        if ($onSuccessCallback === null) {
            return null;
        }
        $valuesParam = $this->formFinder->findOnSuccessCallbackValuesParam($node, $onSuccessCallback);
        if ($valuesParam === null) {
            return null;
        }

        $valuesParam->type = new Identifier($fullFormDataClassName);
        return $node;
    }
}
