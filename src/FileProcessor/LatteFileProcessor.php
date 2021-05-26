<?php

declare(strict_types=1);

namespace Rector\Nette\FileProcessor;

use Rector\Core\Contract\Processor\FileProcessorInterface;
use Rector\Core\ValueObject\Application\File;
use Rector\Nette\Contract\Rector\LatteRectorInterface;

final class LatteFileProcessor implements FileProcessorInterface
{
    /**
     * @param LatteRectorInterface[] $latteRectors
     */
    public function __construct(
        private array $latteRectors
    ) {
    }

    /**
     * @param File[] $files
     */
    public function process(array $files): void
    {
        foreach ($files as $file) {
            $fileContent = $file->getFileContent();

            foreach ($this->latteRectors as $latteRector) {
                $fileContent = $latteRector->changeContent($fileContent);
            }

            $file->changeFileContent($fileContent);
        }
    }

    public function supports(File $file): bool
    {
        $fileInfo = $file->getSmartFileInfo();
        return $fileInfo->hasSuffixes($this->getSupportedFileExtensions());
    }

    /**
     * @return string[]
     */
    public function getSupportedFileExtensions(): array
    {
        return ['latte'];
    }
}
