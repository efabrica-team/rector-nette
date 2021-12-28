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
use Rector\ChangesReporting\ValueObjectFactory\FileDiffFactory;
use Rector\Core\ValueObject\Error\SystemError;
use Rector\Core\ValueObject\Reporting\FileDiff;
use Rector\Parallel\ValueObject\Bridge;

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
        private FileDiffFactory $fileDiffFactory
    ) {
    }

    /**
     * @return array{system_errors: SystemError[], file_diffs: FileDiff[]}
     */
    public function process(File $file, Configuration $configuration): array
    {
        $systemErrorsAndFileDiffs = [
            Bridge::SYSTEM_ERRORS => [],
            Bridge::FILE_DIFFS => [],
        ];

        if ($this->neonRectors === []) {
            return $systemErrorsAndFileDiffs;
        }

        $fileContent = $file->getFileContent();

        $neonNode = $this->neonParser->parseString($fileContent);

        $neonNodeTraverser = $this->neonNodeTraverserFactory->create();
        foreach ($this->neonRectors as $neonRector) {
            $neonNodeTraverser->addNeonNodeVisitor($neonRector);
        }

        $originalPrintedContent = $this->formatPreservingNeonPrinter->printNode($neonNode, $fileContent);

        $neonNode = $neonNodeTraverser->traverse($neonNode);
        $changedFileContent = $this->formatPreservingNeonPrinter->printNode($neonNode, $fileContent);

        // has node changed?
        if ($changedFileContent === $originalPrintedContent) {
            return $systemErrorsAndFileDiffs;
        }

        $file->changeFileContent($changedFileContent);

        $fileDiff = $this->fileDiffFactory->createFileDiff($file, $originalPrintedContent, $changedFileContent);
        $systemErrorsAndFileDiffs[Bridge::FILE_DIFFS][] = $fileDiff;

        return $systemErrorsAndFileDiffs;
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
