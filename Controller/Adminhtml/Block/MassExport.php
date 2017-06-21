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

namespace MSP\CmsImportExport\Controller\Adminhtml\Block;

use Magento\Backend\App\Response\Http\FileFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\App\Action;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Cms\Model\ResourceModel\Block\CollectionFactory;
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
        return $this->_authorization->isAllowed('MSP_CmsImportExport::export_block');
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
                'value' => $this->importExportContentInterface->asZipFile([], $pages),
                'rm' => true,
            ],
            DirectoryList::VAR_DIR,
            'application/zip'
        );
    }
}
