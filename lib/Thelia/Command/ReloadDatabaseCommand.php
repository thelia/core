<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace Thelia\Command;

use \Thelia\Model\Map\ProductTableMap;
use Propel\Runtime\Connection\ConnectionWrapper;
use Propel\Runtime\Propel;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Thelia\Install\Database;

/**
 * Class ReloadDatabasesCommand
 * @package Thelia\Command
 * @author Manuel Raynaud <manu@raynaud.io>
 */
class ReloadDatabaseCommand extends BaseModuleGenerate
{
    public function configure()
    {
        $this
            ->setName("thelia:dev:reloadDB")
            ->setDescription("erase current database and create new one")
/*            ->addOption(
                "load-fixtures",
                null,
                InputOption::VALUE_NONE,
                "load fixtures in databases"
            )*/
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ConnectionWrapper $connection */
        $connection = Propel::getConnection(ProductTableMap::DATABASE_NAME);
        $connection = $connection->getWrappedConnection();

        $database = new Database($connection);
        $output->writeln([
           '',
           '<info>starting reloaded database, please wait</info>'
        ]);
        $database->insertSql();
        $output->writeln([
            '',
            '<info>Database reloaded with success</info>',
            ''
        ]);

        return 0;
    }
}
