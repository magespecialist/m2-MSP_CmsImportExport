<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MSP\CmsImportExport\Model;

use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\App\Filesystem\DirectoryList;

class Filesystem
{
    const EXPORT_PATH = 'msp_cmsimportexport/export';
    const EXTRACT_PATH = 'msp_cmsimportexport/extract';
    const UPLOAD_PATH = 'msp_cmsimportexport/extract';

    protected $filesystem;
    protected $file;

    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        File $file
    ) {
        $this->filesystem = $filesystem;
        $this->file = $file;
    }

    /**
     * Get upload path
     * @return string
     */
    public function getUploadPath()
    {
        $varDir = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $exportPath = $varDir->getAbsolutePath(self::UPLOAD_PATH);

        $this->file->mkdir($exportPath, DriverInterface::WRITEABLE_DIRECTORY_MODE, true);
        return $exportPath;
    }

    /**
     * Get export path
     * @return string
     */
    public function getExportPath()
    {
        $varDir = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $exportPath = $varDir->getAbsolutePath(self::EXPORT_PATH);

        $this->file->mkdir($exportPath, DriverInterface::WRITEABLE_DIRECTORY_MODE, true);
        return $exportPath;
    }

    /**
     * Get extract path
     * @param string $subPath
     * @return string
     */
    public function getExtractPath($subPath)
    {
        $varDir = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $extractPath = $varDir->getAbsolutePath(self::EXTRACT_PATH.'/'.$subPath);

        $this->file->mkdir($extractPath, DriverInterface::WRITEABLE_DIRECTORY_MODE, true);
        return $extractPath;
    }

    /**
     * Get media file path
     * @param $mediaFile
     * @param bool $write = false
     * @return string
     */
    public function getMediaPath($mediaFile, $write = false)
    {
        if ($write) {
            $mediaDir = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        } else {
            $mediaDir = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        }

        return $mediaDir->getAbsolutePath($mediaFile);
    }
}
