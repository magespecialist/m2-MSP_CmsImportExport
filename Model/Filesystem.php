<?php
/**
 * MageSpecialist
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@magespecialist.it so we can send you a copy immediately.
 *
 * @category   MSP
 * @package    MSP_CmsImportExport
 * @copyright  Copyright (c) 2017 Skeeller srl (http://www.magespecialist.it)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
