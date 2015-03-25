<?php


namespace App\Console\Command;


use App\Application;
use Doctrine\ORM\EntityManager;
use Knp\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends Command
{
    protected $commands = array(
        'app:assetic:dump',
        'orm:schema-tool:create',
    );



    protected function configure()
    {
        $this
            ->setName('app:install')
            ->setDescription('installs the app file perm etc..')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach($this->commands as $command) {
           #$this->getApplication()->find($command)->run($input, $output);
        }

        /** @var Application $app */
        $app = $this->getSilexApplication();
        /** @var EntityManager $em */
        $em = $app['orm.em'];

        echo $em->getRepository('App\Model\Entity\Group')->getClassName();
    }
}
