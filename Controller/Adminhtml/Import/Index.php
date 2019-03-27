<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MSP\CmsImportExport\Controller\Adminhtml\Import;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    protected $pageFactory;

    public function __construct(
        Action\Context $context,
        PageFactory $pageFactory
    ) {
        $this->pageFactory = $pageFactory;

        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MSP_CmsImportExport::import');
    }

    public function execute()
    {
        $resultPage = $this->pageFactory->create();

        $resultPage->setActiveMenu('MSP_CmsImportExport::import')
            ->addBreadcrumb(__('CMS'), __('CMS'));

        $resultPage->addBreadcrumb(__('Import CMS'), __('Import CMS by MageSpecialist'));
        
        $resultPage->getConfig()->getTitle()->prepend(__('CMS'));
        $resultPage->getConfig()->getTitle()->prepend(__('CMS Import by MageSpecialist'));

        return $resultPage;
    }
}
