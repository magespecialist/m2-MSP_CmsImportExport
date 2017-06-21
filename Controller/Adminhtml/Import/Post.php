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
