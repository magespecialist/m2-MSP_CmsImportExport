<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MSP\CmsImportExport\Model\Source;

use MSP\CmsImportExport\Api\ContentInterface;

class CmsMode
{
    /**
     * To option array
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['label' => __('Overwrite existing'), 'value' => ContentInterface::CMS_MODE_UPDATE],
            ['label' => __('Skip existing'), 'value' => ContentInterface::CMS_MODE_SKIP],
        ];
    }
}
