# MSP_CmsImportExport

MSP_CmsImportExport is a module for Magento 2 allowing users to **import/export CMS pages** or blocks.

MSP_CmsImportExport supports **multistore** and **media attachments**.

## How to export contents

Select one or more pages you wish to export from CMS > Pages in your backend and select "Export" from the mass action menù.

You will download a **ZIP file** containing pages or blocks information. If your pages or blocks contain one or more images,
they will be added in the ZIP file.

## How to import contents

A new option "Import" should appear in your Magento 2 "content" submenu. Click on it to import a previously exported ZIP file.

While importing you can change the import mode:

### CMS import mode

Overwrite existing: If a page/block with same id and store assignment is found, it will be overwritten by the ZIP file content.
Skip existing: If a page/block with same id and store assignment is found, it will **NOT** be overwritten by the ZIP file content.

### Media import mode

Do not import: Do not import media files in the ZIP file.
Overwrite existing: If an image with same name exists it will be overwritten with version in the ZIP file.
Skip existing: If an image with same name exists it will **NOT** be overwritten with version in the ZIP file.


