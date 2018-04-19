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
