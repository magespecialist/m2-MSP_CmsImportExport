<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MSP\CmsImportExport\Controller\Adminhtml\Import;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\File\UploaderFactory;
use MSP\CmsImportExport\Api\ContentInterface;
use MSP\CmsImportExport\Model\Filesystem;

class Post extends Action
{
    protected $uploaderFactory;
    protected $contentInterface;
    protected $filesystem;
    protected $redirectFactory;

    public function __construct(
        Action\Context $context,
        UploaderFactory $uploaderFactory,
        ContentInterface $contentInterface,
        RedirectFactory $redirectFactory,
        Filesystem $filesystem
    ) {
        $this->uploaderFactory = $uploaderFactory;
        $this->contentInterface = $contentInterface;
        $this->filesystem = $filesystem;
        $this->redirectFactory = $redirectFactory;

        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MSP_CmsImportExport::import');
    }

    public function execute()
    {
        $cmsMode = $this->getRequest()->getParam('cms_mode');
        $mediaMode = $this->getRequest()->getParam('media_mode');
        $storesMap = $this->getRequest()->getParam('store_map');
        
        $destinationPath = $this->filesystem->getUploadPath();

        $uploader = $this->uploaderFactory->create(['fileId' => 'zipfile']);
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(true);
        $uploader->setAllowCreateFolders(true);
        $result = $uploader->save($destinationPath);

        $zipFile = $result['path'].$result['file'];

        $this->contentInterface
            ->setCmsMode($cmsMode)
            ->setMediaMode($mediaMode)
            ->setStoresMap($storesMap);

        $count = $this->contentInterface->importFromZipFile($zipFile, true);

        $this->messageManager->addSuccess(__('A total of %1 item(s) have been imported/updated.', $count));

        $resultRedirect = $this->redirectFactory->create();
        return $resultRedirect->setPath('*/*/index');
    }
}
