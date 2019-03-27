<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MSP\CmsImportExport\Command;

use Magento\Framework\ObjectManagerInterface;
use MSP\CmsImportExport\Api\ContentInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportPage extends Command
{

    protected $pageInterface;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * ImportPage constructor.
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('cms:import');
        $this->setDescription('Import CMS zip file');
        $this->addArgument('zipfile', InputArgument::REQUIRED, __('Zip file containing CMS information'));

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $contentInterface = $this->objectManager->get(ContentInterface::class);

        $zipFile = $input->getArgument('zipfile');
        if ($contentInterface->importFromZipFile($zipFile, false) == 0) {
            throw new \Exception(__('Archive is empty'));
        }

        $output->writeln('Done.');
    }
}
