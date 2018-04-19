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

namespace MSP\CmsImportExport\Block\Adminhtml;

use Magento\Backend\Block\Widget\Form\Container;

class Import extends Container
{
    protected $_mode = 'upload';

    protected function _construct()
    {
        $this->_objectId = 'import';
        $this->_blockGroup = 'MSP_CmsImportExport';
        $this->_controller = 'adminhtml_import';

        parent::_construct();

        $this->buttonList->remove('back');
        $this->buttonList->update('save', 'label', __('Import'));
    }

    public function getHeaderText()
    {
        return __('Import CMS');
    }
}
