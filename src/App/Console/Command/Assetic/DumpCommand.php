<?php

namespace App\Console\Command\Assetic;

use Knp\Command\Command;
use SilexAssetic\Assetic\Dumper;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Output\OutputInterface;

class DumpCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        if (!$this->getSilexApplication()) {
            return false;
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('app:assetic:dump');
        $this->setDescription('Dumps all assets to the filesystem');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(Input $input, OutputInterface $output)
    {
        self::removeDir(ROOT_PATH.'/public/assets');
        $app = $this->getSilexApplication();
        /** @var Dumper $dumper */
        $dumper = $app['assetic.dumper'];
        $output->writeln('start dumping');
        if (isset($app['twig'])) {
            $dumper->addTwigAssets();
            $output->writeln('add TwigAssets');
        }
        $dumper->dumpAssets();
        $output->writeln('<info>Dump finished</info>');
    }

    protected static function removeDir($path) {

        // Normalise $path.
        $path = rtrim($path, '/') . '/';

        // Remove all child files and directories.
        $items = glob($path . '*');

        foreach($items as $item) {
            is_dir($item) ? self::removeDir($item) : unlink($item);
        }

        // Remove directory.
        rmdir($path);
    }

}

