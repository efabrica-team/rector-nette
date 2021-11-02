<?php

declare(strict_types=1);

namespace Rector\Nette\FileProcessor;

use Rector\Core\Contract\Processor\FileProcessorInterface;
use Rector\Core\ValueObject\Application\File;
use Rector\Core\ValueObject\Configuration;
use Rector\Nette\Contract\Rector\NeonRectorInterface;
use Rector\Nette\NeonParser\NeonNodeTraverserFactory;
use Rector\Nette\NeonParser\NeonParser;
use Rector\Nette\NeonParser\Printer\FormatPreservingNeonPrinter;

final class NeonFileProcessor implements FileProcessorInterface
{
    /**
     * @param NeonRectorInterface[] $neonRectors
     */
    public function __construct(
        private NeonParser $neonParser,
        private NeonNodeTraverserFactory $neonNodeTraverserFactory,
        private FormatPreservingNeonPrinter $formatPreservingNeonPrinter,
        private array $neonRectors,
    ) {
    }

    public function process(File $file, Configuration $configuration): void
    {
        $fileContent = $file->getFileContent();

        $neonNode = $this->neonParser->parseString($fileContent);

        $neonNodeTraverser = $this->neonNodeTraverserFactory->create();
        foreach ($this->neonRectors as $neonRector) {
            $neonNodeTraverser->addNeonNodeVisitor($neonRector);
        }

        $neonNode = $neonNodeTraverser->traverse($neonNode);
        $changedFileContent = $this->formatPreservingNeonPrinter->printNode($neonNode, $fileContent);

        $file->changeFileContent($changedFileContent);
    }

    public function supports(File $file, Configuration $configuration): bool
    {
        $fileInfo = $file->getSmartFileInfo();
        return $fileInfo->hasSuffixes($this->getSupportedFileExtensions());
    }

    /**
     * @return string[]
     */
    public function getSupportedFileExtensions(): array
    {
        return ['neon'];
    }
}
