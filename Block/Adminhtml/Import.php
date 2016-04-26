<?php
/**
 * Copyright © 2016 MageSpecialist - IDEALIAGroup srl. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MSP\CmsImportExport\Block\Adminhtml;

use Magento\Backend\Block\Widget\Form\Container;

class Import extends Container {
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