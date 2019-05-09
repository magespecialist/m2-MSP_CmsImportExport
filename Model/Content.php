<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\CmsImportExport\Model;

use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Framework\Json\DecoderInterface;
use Magento\Framework\Json\EncoderInterface;
use Magento\Cms\Api\Data\PageInterface as CmsPageInterface;
use Magento\Cms\Api\Data\BlockInterface as CmsBlockInterface;
use Magento\Cms\Model\BlockFactory as CmsBlockFactory;
use Magento\Cms\Model\PageFactory as CmsPageFactory;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory as CmsPageCollectionFactory;
use Magento\Cms\Model\ResourceModel\Block\CollectionFactory as CmsBlockCollectionFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use MSP\CmsImportExport\Api\ContentInterface;
use Magento\Framework\Filesystem\Io\File;

class Content implements ContentInterface
{
    const JSON_FILENAME = 'cms.json';
    const MEDIA_ARCHIVE_PATH = 'media';

    protected $storeRepositoryInterface;
    protected $encoderInterface;
    protected $decoderInterface;
    protected $pageCollectionFactory;
    protected $blockCollectionFactory;
    protected $blockRepositoryInterface;
    protected $pageFactory;
    protected $blockFactory;
    protected $filesystem;
    protected $file;
    protected $dateTime;

    protected $cmsMode;
    protected $mediaMode;
    protected $storesMap;

    public function __construct(
        StoreRepositoryInterface $storeRepositoryInterface,
        EncoderInterface $encoderInterface,
        DecoderInterface $decoderInterface,
        CmsPageFactory $pageFactory,
        CmsPageCollectionFactory $pageCollectionFactory,
        CmsBlockFactory $blockFactory,
        CmsBlockCollectionFactory $blockCollectionFactory,
        BlockRepositoryInterface $blockRepositoryInterface,
        Filesystem $filesystem,
        File $file,
        DateTime $dateTime
    ) {
        $this->storeRepositoryInterface = $storeRepositoryInterface;
        $this->encoderInterface = $encoderInterface;
        $this->decoderInterface = $decoderInterface;
        $this->pageCollectionFactory = $pageCollectionFactory;
        $this->pageFactory = $pageFactory;
        $this->blockCollectionFactory = $blockCollectionFactory;
        $this->blockFactory = $blockFactory;
        $this->blockRepositoryInterface = $blockRepositoryInterface;
        $this->filesystem = $filesystem;
        $this->file = $file;
        $this->dateTime = $dateTime;

        $this->cmsMode = ContentInterface::CMS_MODE_UPDATE;
        $this->mediaMode = ContentInterface::MEDIA_MODE_UPDATE;

        $this->storesMap = [];
        $stores = $this->storeRepositoryInterface->getList();
        foreach ($stores as $store) {
            $this->storesMap[$store->getCode()] = $store->getCode();
        }
    }

    /**
     * Create a zip file and return its name
     * @param \Magento\Cms\Api\Data\PageInterface[] $pageInterfaces
     * @param \Magento\Cms\Api\Data\BlockInterface[] $blockInterfaces
     * @return string
     */
    public function asZipFile(array $pageInterfaces, array $blockInterfaces): string
    {
        $pagesArray = $this->pagesToArray($pageInterfaces);
        $blocksArray = $this->blocksToArray($blockInterfaces);

        $contentArray = array_merge_recursive($pagesArray, $blocksArray);

        $jsonPayload = $this->encoderInterface->encode($contentArray);

        $exportPath = $this->filesystem->getExportPath();

        $zipFile = $exportPath . '/' . sprintf('cms_%s.zip', $this->dateTime->date('Ymd_His'));
        $relativeZipFile = Filesystem::EXPORT_PATH . '/' . sprintf('cms_%s.zip', $this->dateTime->date('Ymd_His'));

        $zipArchive = new \ZipArchive();
        $zipArchive->open($zipFile, \ZipArchive::CREATE);

        // Add pages json
        $zipArchive->addFromString(self::JSON_FILENAME, $jsonPayload);

        // Add media files
        foreach ($contentArray['media'] as $mediaFile) {
            $absMediaPath = $this->filesystem->getMediaPath($mediaFile);
            if ($this->file->fileExists($absMediaPath, true)) {
                $zipArchive->addFile($absMediaPath, self::MEDIA_ARCHIVE_PATH . '/' . $mediaFile);
            }
        }

        $zipArchive->close();

        // Clear export path
        $this->file->rm($exportPath, true);

        return $relativeZipFile;
    }

    /**
     * Return CMS pages as array
     * @param \Magento\Cms\Api\Data\PageInterface[] $pageInterfaces
     * @return array
     */
    public function pagesToArray(array $pageInterfaces): array
    {
        $pages = [];
        $media = [];

        foreach ($pageInterfaces as $pageInterface) {
            $pageInfo = $this->pageToArray($pageInterface);
            $pages[$this->_getPageKey($pageInterface)] = $pageInfo;
            $media = array_merge($media, $pageInfo['media']);
        }

        return [
            'pages' => $pages,
            'media' => $media,
        ];
    }

    /**
     * Return CMS page to array
     * @param \Magento\Cms\Api\Data\PageInterface $pageInterface
     * @return array
     */
    public function pageToArray(CmsPageInterface $pageInterface): array
    {
        // Extract attachments
        $media = $this->getMediaAttachments($pageInterface->getContent());

        $payload = [
            'cms' => [
                CmsPageInterface::IDENTIFIER => $pageInterface->getIdentifier(),
                CmsPageInterface::TITLE => $pageInterface->getTitle(),
                CmsPageInterface::PAGE_LAYOUT => $pageInterface->getPageLayout(),
                CmsPageInterface::META_KEYWORDS => $pageInterface->getMetaKeywords(),
                CmsPageInterface::META_DESCRIPTION => $pageInterface->getMetaDescription(),
                CmsPageInterface::CONTENT_HEADING => $pageInterface->getContentHeading(),
                CmsPageInterface::CONTENT => $pageInterface->getContent(),
                CmsPageInterface::SORT_ORDER => $pageInterface->getSortOrder(),
                CmsPageInterface::LAYOUT_UPDATE_XML => $pageInterface->getLayoutUpdateXml(),
                CmsPageInterface::CUSTOM_THEME => $pageInterface->getCustomTheme(),
                CmsPageInterface::CUSTOM_ROOT_TEMPLATE => $pageInterface->getCustomRootTemplate(),
                CmsPageInterface::CUSTOM_LAYOUT_UPDATE_XML => $pageInterface->getCustomLayoutUpdateXml(),
                CmsPageInterface::CUSTOM_THEME_FROM => $pageInterface->getCustomThemeFrom(),
                CmsPageInterface::CUSTOM_THEME_TO => $pageInterface->getCustomThemeTo(),
                CmsPageInterface::IS_ACTIVE => $pageInterface->isActive(),
            ],
            'stores' => $this->getStoreCodes($pageInterface->getStoreId()),
            'media' => $media,
        ];

        return $payload;
    }

    /**
     * Get media attachments from content
     * @param $content
     * @return array
     */
    public function getMediaAttachments($content): array
    {
        if (preg_match_all('/\{\{media.+?url\s*=\s*("|&quot;)(.+?)("|&quot;).*?\}\}/', $content, $matches)) {
            return $matches[2];
        }

        return [];
    }

    /**
     * Get store codes
     * @param array $storeIds
     * @return array
     */
    public function getStoreCodes($storeIds): array
    {
        $return = [];

        foreach ($storeIds as $storeId) {
            $return[] = $this->storeRepositoryInterface->getById($storeId)->getCode();
        }

        return $return;
    }

    /**
     * Get page unique key
     * @param CmsPageInterface $pageInterface
     * @return string
     */
    protected function _getPageKey(CmsPageInterface $pageInterface): string
    {
        $keys = $this->getStoreCodes($pageInterface->getStoreId());
        $keys[] = $pageInterface->getIdentifier();

        return implode(':', $keys);
    }

    /**
     * Return CMS blocks as array
     * @param \Magento\Cms\Api\Data\BlockInterface[] $blockInterfaces
     * @return array
     */
    public function blocksToArray(array $blockInterfaces): array
    {
        $blocks = [];
        $media = [];

        foreach ($blockInterfaces as $blockInterface) {
            $blockInfo = $this->blockToArray($blockInterface);
            $blocks[$this->_getBlockKey($blockInterface)] = $blockInfo;
            $media = array_merge($media, $blockInfo['media']);
        }

        return [
            'blocks' => $blocks,
            'media' => $media,
        ];
    }

    /**
     * Return CMS block to array
     * @param \Magento\Cms\Api\Data\BlockInterface $blockInterface
     * @return array
     */
    public function blockToArray(CmsBlockInterface $blockInterface): array
    {
        // Extract attachments
        $media = $this->getMediaAttachments($blockInterface->getContent());

        $payload = [
            'cms' => [
                CmsBlockInterface::IDENTIFIER => $blockInterface->getIdentifier(),
                CmsBlockInterface::TITLE => $blockInterface->getTitle(),
                CmsBlockInterface::CONTENT => $blockInterface->getContent(),
                CmsBlockInterface::IS_ACTIVE => $blockInterface->isActive(),
            ],
            'stores' => $this->getStoreCodes($blockInterface->getStoreId()),
            'media' => $media,
        ];

        return $payload;
    }

    /**
     * Get block unique key
     * @param CmsBlockInterface $blockInterface
     * @return string
     */
    protected function _getBlockKey(CmsBlockInterface $blockInterface): string
    {
        $keys = $this->getStoreCodes($blockInterface->getStoreId());
        $keys[] = $blockInterface->getIdentifier();

        return implode(':', $keys);
    }

    /**
     * Import contents from zip archive and return number of imported records (-1 on error)
     * @param string $fileName
     * @param bool $rm = true
     * @return int
     * @throws \Exception
     */
    public function importFromZipFile($fileName, $rm = false): int
    {
        // Unzip archive
        $zipArchive = new \ZipArchive();
        $res = $zipArchive->open($fileName);
        if ($res !== true) {
            throw new \Exception('Cannot open ZIP archive');
        }

        $subPath = md5(date(DATE_RFC2822));
        $extractPath = $this->filesystem->getExtractPath($subPath);

        $zipArchive->extractTo($extractPath);
        $zipArchive->close();

        // Check if pages.json exists
        $pagesFile = $extractPath . '/' . self::JSON_FILENAME;
        if (!$this->file->fileExists($pagesFile, true)) {
            throw new \Exception(self::JSON_FILENAME . ' is missing');
        }

        // Read and import
        $jsonString = $this->file->read($pagesFile);
        $cmsData = $this->decoderInterface->decode($jsonString);

        $count = $this->importFromArray($cmsData, $extractPath);

        // Remove if necessary
        if ($rm) {
            $this->file->rm($fileName);
        }

        // Clear archive
        $this->file->rmdir($extractPath, true);

        return $count;
    }

    /**
     * Import contents from array and return number of imported records (-1 on error)
     * @param array $payload
     * @param string $archivePath = null
     * @return int
     * @throws \Exception
     */
    public function importFromArray(array $payload, $archivePath = null): int
    {
        if (!isset($payload['pages']) && !isset($payload['blocks'])) {
            throw new \Exception('Invalid json archive');
        }

        $count = 0;

        // Import pages
        foreach ($payload['pages'] as $key => $pageData) {
            if ($this->importPageFromArray($pageData)) {
                $count++;
            }
        }

        // Import blocks
        foreach ($payload['blocks'] as $key => $blockData) {
            if ($this->importBlockFromArray($blockData)) {
                $count++;
            }
        }

        // Import media
        if ($archivePath && ($count > 0) && ($this->mediaMode != ContentInterface::MEDIA_MODE_NONE)) {
            foreach ($payload['media'] as $mediaFile) {
                $sourceFile = $archivePath . '/' . self::MEDIA_ARCHIVE_PATH . '/' . $mediaFile;
                $destFile = $this->filesystem->getMediaPath($mediaFile);

                if ($this->file->fileExists($sourceFile, true)) {
                    if ($this->file->fileExists($destFile, true) &&
                        ($this->mediaMode == ContentInterface::MEDIA_MODE_SKIP)
                    ) {
                        continue;
                    }

                    if (!$this->file->fileExists(dirname($destFile), false)) {
                        if (!$this->file->mkdir(dirname($destFile))) {
                            throw new \Exception('Unable to create folder: ' . dirname($destFile));
                        }
                    }
                    if (!$this->file->cp($sourceFile, $destFile)) {
                        throw new \Exception('Unable to save image: ' . $mediaFile);
                    }
                    $count++;
                }
            }
        }

        return $count;
    }

    /**
     * Import a single page from an array and return false on error and true on success
     * @param array $pageData
     * @return bool
     */
    public function importPageFromArray(array $pageData): bool
    {
        // Will not use repositories to save pages because it does not allow stores selection

        $storeIds = $this->getStoreIdsByCodes($this->_mapStores($pageData['stores']));

        $collection = $this->pageCollectionFactory->create();
        $collection
            ->addFieldToFilter(CmsPageInterface::IDENTIFIER, $pageData['cms'][CmsPageInterface::IDENTIFIER]);

        $pageId = 0;
        foreach ($collection as $item) {
            $storesIntersect = array_intersect($item->getStoreId(), $storeIds);

            // @codingStandardsIgnoreStart
            if (count($storesIntersect)) {
                // @codingStandardsIgnoreEnd
                $pageId = $item->getId();
                break;
            }
        }

        $page = $this->pageFactory->create();
        if ($pageId) {
            $page->load($pageId);

            if ($this->cmsMode == ContentInterface::CMS_MODE_SKIP) {
                return false;
            }
        }

        $cms = $pageData['cms'];

        $page
            ->setIdentifier($cms[CmsPageInterface::IDENTIFIER])
            ->setTitle($cms[CmsPageInterface::TITLE])
            ->setPageLayout($cms[CmsPageInterface::PAGE_LAYOUT])
            ->setMetaKeywords($cms[CmsPageInterface::META_KEYWORDS])
            ->setMetaDescription($cms[CmsPageInterface::META_DESCRIPTION])
            ->setContentHeading($cms[CmsPageInterface::CONTENT_HEADING])
            ->setContent($cms[CmsPageInterface::CONTENT])
            ->setSortOrder($cms[CmsPageInterface::SORT_ORDER])
            ->setLayoutUpdateXml($cms[CmsPageInterface::LAYOUT_UPDATE_XML])
            ->setCustomTheme($cms[CmsPageInterface::CUSTOM_THEME])
            ->setCustomRootTemplate($cms[CmsPageInterface::CUSTOM_ROOT_TEMPLATE])
            ->setCustomLayoutUpdateXml($cms[CmsPageInterface::CUSTOM_LAYOUT_UPDATE_XML])
            ->setCustomThemeFrom($cms[CmsPageInterface::CUSTOM_THEME_FROM])
            ->setCustomThemeTo($cms[CmsPageInterface::CUSTOM_THEME_TO])
            ->setIsActive($cms[CmsPageInterface::IS_ACTIVE]);

        $page->setData('stores', $storeIds);
        $page->save();

        return true;
    }

    /**
     * Get store ids by codes
     * @param array $storeCodes
     * @return array
     */
    public function getStoreIdsByCodes(array $storeCodes): array
    {
        $return = [];
        foreach ($storeCodes as $storeCode) {
            if ($storeCode == 'admin') {
                $return[] = 0;
            } else {
                $store = $this->storeRepositoryInterface->get($storeCode);
                if ($store && $store->getId()) {
                    $return[] = $store->getId();
                }
            }
        }

        return $return;
    }

    /**
     * Map stores
     * @param $storeCodes
     * @return array
     */
    protected function _mapStores($storeCodes): array
    {
        $return = [];
        foreach ($storeCodes as $storeCode) {
            foreach ($this->storesMap as $to => $from) {
                if ($storeCode == $from) {
                    $return[] = $to;
                }
            }
        }

        return $return;
    }

    /**
     * Import a single block from an array and return false on error and true on success
     * @param array $blockData
     * @return bool
     */
    public function importBlockFromArray(array $blockData): bool
    {
        // Will not use repositories to save blocks because it does not allow stores selection

        $storeIds = $this->getStoreIdsByCodes($this->_mapStores($blockData['stores']));

        $collection = $this->blockCollectionFactory->create();
        $collection
            ->addFieldToFilter(CmsBlockInterface::IDENTIFIER, $blockData['cms'][CmsBlockInterface::IDENTIFIER]);

        $blockId = 0;
        foreach ($collection as $item) {
            $storesIntersect = array_intersect($item->getStoreId(), $storeIds);

            // @codingStandardsIgnoreStart
            if (count($storesIntersect)) {
                // @codingStandardsIgnoreEnd
                $blockId = $item->getId();
                break;
            }
        }

        $block = $this->blockFactory->create();
        if ($blockId) {
            $block->load($blockId);

            if ($this->cmsMode == ContentInterface::CMS_MODE_SKIP) {
                return false;
            }
        }

        $cms = $blockData['cms'];

        $block
            ->setIdentifier($cms[CmsBlockInterface::IDENTIFIER])
            ->setTitle($cms[CmsBlockInterface::TITLE])
            ->setContent($cms[CmsBlockInterface::CONTENT])
            ->setIsActive($cms[CmsBlockInterface::IS_ACTIVE]);

        $block->setData('stores', $storeIds);
        $block->save();

        return true;
    }

    /**
     * Set CMS mode
     * @param $mode
     * @return ContentInterface
     */
    public function setCmsMode($mode): ContentInterface
    {
        $this->cmsMode = $mode;
        return $this;
    }

    /**
     * Set media mode
     * @param $mode
     * @return ContentInterface
     */
    public function setMediaMode($mode): ContentInterface
    {
        $this->mediaMode = $mode;
        return $this;
    }

    /**
     * Set stores mapping
     * @param array $storesMap
     * @return ContentInterface
     */
    public function setStoresMap(array $storesMap): ContentInterface
    {
        return $this;
    }
}
