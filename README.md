# CmsImportExport

MSP_CmsImportExport is a module for Magento 2 allowing users to **import/export CMS pages or blocks**.

MSP_CmsImportExport supports **multistore** and **wysiwyg images**.

## Installation

    composer require msp/cmsimportexport

## How to export contents

Select one or more pages you wish to export from **CMS > Pages** in your Magento Admin and select **Export** from the mass action menù.

You will download a **ZIP file** containing pages or blocks information. If your pages or blocks contain one or more images,
they will be automatically added in the ZIP file.

<img src="https://raw.githubusercontent.com/magespecialist/m2-MSP_CmsImportExport/master/screenshot/export.png" />

## How to import contents

A new option **Import** will appear in your Magento Admin **Content** bmenu. Click on it to import a previously exported ZIP file.

<img src="https://raw.githubusercontent.com/magespecialist/m2-MSP_CmsImportExport/master/screenshot/import.png" />

While importing you can change import modes:

<img src="https://raw.githubusercontent.com/magespecialist/m2-MSP_CmsImportExport/master/screenshot/import2.png" />

### CMS import mode

**Overwrite existing**: If a page/block with same id and store assignment is found, it will be overwritten by the ZIP file content.

**Skip existing**: If a page/block with same id and store assignment is found, it will **NOT** be overwritten by the ZIP file content.

### Media import mode

**Do not import**: Do not import media files in the ZIP file.

**Overwrite existing**: If an image with same name exists it will be overwritten with version in the ZIP file.

**Skip existing**: If an image with same name exists it will **NOT** be overwritten with version in the ZIP file.


