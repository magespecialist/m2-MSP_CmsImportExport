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
    public function setCmsMode($mode);

    /**
     * Set media mode on import
     * @param $mode
     * @return ContentInterface
     */
    public function setMediaMode($mode);

    /**
     * Set stores mapping on import
     * @param array $storesMap
     * @return ContentInterface
     */
    public function setStoresMap(array $storesMap);

    /**
     * Return CMS block to array
     * @param \Magento\Cms\Api\Data\BlockInterface $blockInterface
     * @return array
     */
    public function blockToArray(\Magento\Cms\Api\Data\BlockInterface $blockInterface);

    /**
     * Return CMS page to array
     * @param \Magento\Cms\Api\Data\PageInterface $pageInterface
     * @return array
     */
    public function pageToArray(\Magento\Cms\Api\Data\PageInterface $pageInterface);

    /**
     * Return CMS blocks as array
     * @param \Magento\Cms\Api\Data\BlockInterface[] $blockInterfaces
     * @return array
     */
    public function blocksToArray(array $blockInterfaces);

    /**
     * Return CMS pages as array
     * @param \Magento\Cms\Api\Data\PageInterface[] $pageInterfaces
     * @return array
     */
    public function pagesToArray(array $pageInterfaces);

    /**
     * Create a zip file and return its name
     * @param \Magento\Cms\Api\Data\PageInterface[] $pageInterfaces
     * @param \Magento\Cms\Api\Data\BlockInterface[] $blockInterfaces
     * @return string
     */
    public function asZipFile(array $pageInterfaces, array $blockInterfaces);

    /**
     * Import a single page from an array and return false on error and true on success
     * @param array $pageData
     * @return bool
     */
    public function importPageFromArray(array $pageData);

    /**
     * Import a single block from an array and return false on error and true on success
     * @param array $blockData
     * @return bool
     */
    public function importBlockFromArray(array $blockData);

    /**
     * Import contents from array and return number of imported records (-1 on error)
     * @param array $payload
     * @param string $archivePath = null
     * @return int
     */
    public function importFromArray(array $payload, $archivePath = null);

    /**
     * Import contents from zip archive and return number of imported records (-1 on error)
     * @param string $fileName
     * @param bool $rm = true
     * @return int
     */
    public function importFromZipFile($fileName, $rm = false);
}
