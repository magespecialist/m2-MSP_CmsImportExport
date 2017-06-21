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
