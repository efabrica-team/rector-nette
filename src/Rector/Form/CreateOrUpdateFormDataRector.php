<?php

declare(strict_types=1);

namespace Rector\Nette\Rector\Form;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Expression;
use PHPStan\Type\ObjectType;
use Rector\Core\Rector\AbstractRector;
use Rector\FileSystemRector\ValueObject\AddedFileWithContent;
use Rector\Nette\NodeFactory\ClassWithPublicPropertiesFactory;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see https://doc.nette.org/en/3.1/form-presenter#toc-mapping-to-classes
 */
final class CreateOrUpdateFormDataRector extends AbstractRector
{
    public function __construct(
        private ClassWithPublicPropertiesFactory $classWithPublicPropertiesFactory
    ) {
    }


    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Create or update form data class with all fields of Form', [/* TODO */]);
    }

    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @param Class_ $node
     */
    public function refactor(Node $node): ?Node
    {
        $className = $node->name->name;
        $fullClassName = $this->getName($node);
        $form = $this->findForm($node);
        if ($form === null) {
            return null;
        }

        $formFields = $this->findFormFields($node, $form);

        $formDataClassName = $className . 'FormData';
        $class = $this->classWithPublicPropertiesFactory->createNode(
            $fullClassName . 'FormData',
            $formFields,
            '\Nette\Utils\ArrayHash',   // TODO configurable in rule
            ['\Nette\SmartObject']  // TODO configurable in rule
        );

        $printedClassContent = "<?php\n\n" . $this->betterStandardPrinter->print($class) . "\n";

        $smartFileInfo = $this->file->getSmartFileInfo();
        $targetFilePath = $smartFileInfo->getRealPathDirectory() . '/' . $formDataClassName . '.php';

        $addedFileWithContent = new AddedFileWithContent($targetFilePath, $printedClassContent);
        $this->removedAndAddedFilesCollector->addAddedFile($addedFileWithContent);

        return null;
    }


    /**
     * @TODO extract to some service
     */
    private function findForm(Class_ $node): ?Variable
    {
        foreach ($node->getMethods() as $method) {
            foreach ($method->stmts as $stmt) {
                if (! $stmt instanceof Expression) {
                    continue;
                }

                if (! $stmt->expr instanceof Assign) {
                    continue;
                }

                $var = $stmt->expr->var;
                $expr = $stmt->expr->expr;

                if (! $var instanceof Variable) {
                    continue;
                }

                if (! $this->isObjectType($expr, new ObjectType('Nette\Forms\Form'))) {
                    continue;
                }

                return $var;
            }
        }
        return null;
    }

    /**
     * @TODO extract to some service
     */
    private function findFormFields(Class_ $node, Variable $form): array
    {
        $formFields = [];
        foreach ($node->getMethods() as $method) {
            foreach ($method->stmts as $stmt) {
                if (! $stmt instanceof Expression) {
                    continue;
                }

                if (! $stmt->expr instanceof MethodCall) {
                    continue;
                }

                $methodCall = $stmt->expr;

                if (! $methodCall->var instanceof Variable) {   // TODO fluent calls like $form->addText()->setRequired() not pass this condition
                    continue;
                }

                if ($methodCall->var->name !== $form->name) {
                    continue;
                }

                // skip groups, renderers, translator etc.
                if (! $this->isObjectType($methodCall, new ObjectType('Nette\Forms\Controls\BaseControl'))) {
                    continue;
                }

                // skip submit buttons
                if ($this->isObjectType($methodCall, new ObjectType('Nette\Forms\Controls\SubmitButton'))) {
                    continue;
                }


                $arg = $methodCall->args[0] ?? null;
                if (! $arg) {
                    continue;
                }
                $name = $arg->value;
                if ($name instanceof String_) {
                    $formFields[$name->value] = 'string'; // TODO remap method to type, also we need check if field is required (int is send as null so it is nullable, text is empty string), check select (numeric and string keys) with prompt
                }
            }
        }
        return $formFields;
    }
}
