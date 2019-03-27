<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MSP\CmsImportExport\Controller\Adminhtml\Page;

use Magento\Backend\App\Response\Http\FileFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\App\Action;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use MSP\CmsImportExport\Api\ContentInterface as ImportExportContentInterface;

class MassExport extends Action
{
    protected $filter;
    protected $collectionFactory;
    protected $importExportContentInterface;
    protected $fileFactory;
    protected $dateTime;

    public function __construct(
        Action\Context $context,
        Filter $filter,
        ImportExportContentInterface $importExportContentInterface,
        CollectionFactory $collectionFactory,
        FileFactory $fileFactory,
        DateTime $dateTime
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->importExportContentInterface = $importExportContentInterface;
        $this->fileFactory = $fileFactory;
        $this->dateTime = $dateTime;

        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MSP_CmsImportExport::export_page');
    }

    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());

        $pages = [];
        foreach ($collection as $page) {
            $pages[] = $page;
        }

        return $this->fileFactory->create(
            sprintf('cms_%s.zip', $this->dateTime->date('Ymd_His')),
            [
                'type' => 'filename',
                'value' => $this->importExportContentInterface->asZipFile($pages, []),
                'rm' => true,
            ],
            DirectoryList::VAR_DIR,
            'application/zip'
        );
    }
}
