<?php

namespace SUDHAUS7\Shortcutlink\Command;

use Educo\Eddaylight\Utility\FileHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;


class CleanupCommand extends Command {


    /**
     * Configure the command by defining the name, options and arguments
     */
    protected function configure()
    {
        $this->setDescription('deletes all shortlink entries last used/created older than ...');
        $this->addArgument('olderThanSeconds',InputArgument::OPTIONAL,'Delete entries created before or last used since (seconds, default: 1209600 -> 2 weeks)');
        $this->setHelp('A detailed description, if your command was prefixed with "help"');
    }

    /**
     * Executes the current command.
     * Test on CLI in web/typo3/sysext/core call "php bin/typo3 shortcutlink:cleanup"
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|int null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output){
        $olderThan = 1209600;
        if ($input->getArgument('olderThanSeconds') != '') {
            $olderThan = $input->getArgument('olderThanSeconds');
        }
        $olderThanStamp = time()-$olderThan;
        try {
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_shortcutlink_domain_model_shortlink');
            $queryBuilder->delete('tx_shortcutlink_domain_model_shortlink')
                ->where(
                    $queryBuilder->expr()->lt('tstamp', $queryBuilder->createNamedParameter($olderThanStamp))
                )->execute();
        } catch (Exception $e){
            return 1;
        }
        return 0;
    }
}
