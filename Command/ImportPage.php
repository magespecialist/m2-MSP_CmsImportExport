<?php
/**
 * Copyright © 2016 MageSpecialist - IDEALIAGroup srl. All rights reserved.
 * See COPYING.txt for license details.
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