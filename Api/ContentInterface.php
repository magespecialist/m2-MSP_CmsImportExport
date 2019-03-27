<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\CmsImportExport\Api;

/**
 * Interface ContentInterface
 * @package MSP\CmsImportExport\Api
 * @api
 */
interface ContentInterface
{
    const CMS_MODE_UPDATE = 'update';
    const CMS_MODE_SKIP = 'skip';

    const MEDIA_MODE_NONE = 'none';
    const MEDIA_MODE_UPDATE = 'update';
    const MEDIA_MODE_SKIP = 'skip';

    /**
     * Set CMS mode on import
     * @param $mode
     * @return ContentInterface
     */
    public function setCmsMode($mode): ContentInterface;

    /**
     * Set media mode on import
     * @param $mode
     * @return ContentInterface
     */
    public function setMediaMode($mode): ContentInterface;

    /**
     * Set stores mapping on import
     * @param array $storesMap
     * @return ContentInterface
     */
    public function setStoresMap(array $storesMap): ContentInterface;

    /**
     * Return CMS block to array
     * @param \Magento\Cms\Api\Data\BlockInterface $blockInterface
     * @return array
     */
    public function blockToArray(\Magento\Cms\Api\Data\BlockInterface $blockInterface): array ;

    /**
     * Return CMS page to array
     * @param \Magento\Cms\Api\Data\PageInterface $pageInterface
     * @return array
     */
    public function pageToArray(\Magento\Cms\Api\Data\PageInterface $pageInterface): array;

    /**
     * Return CMS blocks as array
     * @param \Magento\Cms\Api\Data\BlockInterface[] $blockInterfaces
     * @return array
     */
    public function blocksToArray(array $blockInterfaces): array;

    /**
     * Return CMS pages as array
     * @param \Magento\Cms\Api\Data\PageInterface[] $pageInterfaces
     * @return array
     */
    public function pagesToArray(array $pageInterfaces): array;

    /**
     * Create a zip file and return its name
     * @param \Magento\Cms\Api\Data\PageInterface[] $pageInterfaces
     * @param \Magento\Cms\Api\Data\BlockInterface[] $blockInterfaces
     * @return string
     */
    public function asZipFile(array $pageInterfaces, array $blockInterfaces): string;

    /**
     * Import a single page from an array and return false on error and true on success
     * @param array $pageData
     * @return bool
     */
    public function importPageFromArray(array $pageData): bool;

    /**
     * Import a single block from an array and return false on error and true on success
     * @param array $blockData
     * @return bool
     */
    public function importBlockFromArray(array $blockData): bool;

    /**
     * Import contents from array and return number of imported records (-1 on error)
     * @param array $payload
     * @param string $archivePath = null
     * @return int
     */
    public function importFromArray(array $payload, $archivePath = null): int;

    /**
     * Import contents from zip archive and return number of imported records (-1 on error)
     * @param string $fileName
     * @param bool $rm = true
     * @return int
     */
    public function importFromZipFile($fileName, $rm = false): int;
}
