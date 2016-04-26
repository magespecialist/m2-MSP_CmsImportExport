<?php
/**
 * IDEALIAGroup srl
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@idealiagroup.com so we can send you a copy immediately.
 *
 * @category   MSP
 * @package    MSP_CmsImportExport
 * @copyright  Copyright (c) 2016 IDEALIAGroup srl (http://www.idealiagroup.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace MSP\CmsImportExport\Command;

use MSP\CmsImportExport\Api\ContentInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportPage extends Command
{
    protected $pageInterface;

    public function __construct(
        ContentInterface $contentInterface
    ) {
        $this->contentInterface = $contentInterface;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('cms:import');
        $this->setDescription('Import CMS zip file');
        $this->addArgument('zipfile', InputArgument::REQUIRED, __('Zip file containing CMS information'));

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $zipFile = $input->getArgument('zipfile');
        if ($this->contentInterface->importFromZipFile($zipFile, false) == 0) {
            throw new \Exception(__('Archive is empty'));
        }

        $output->writeln('Done.');
    }
}