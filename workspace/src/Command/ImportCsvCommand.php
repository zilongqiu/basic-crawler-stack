<?php

namespace App\Command;

use App\Builder\RestaurantBuilderDirector;
use App\Command\Builder\Restaurant\OpenRiceBuilder;
use App\Manager\RestaurantManager;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportCsvCommand extends Command
{
    protected static $defaultName = 'app:import-csv';

    private EntityManagerInterface $em;
    private ContainerInterface $container;

    public function __construct(EntityManagerInterface $em,
                                ContainerInterface $container)
    {
        $this->em = $em;
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        $this->container = $container;
        parent::__construct();
    }

    protected function configure()
    {
        $this
        ->setDescription('Import data from .csv files');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Attempting import of data...');

        $reader = Reader::createFromPath('%kernel.root_dir%/../public/data.csv');
        $reader->setHeaderOffset(0);
        $results = $reader->getIterator();

        $index = 0;
        $rowProcessed = 0;
        $io->progressStart(iterator_count($results));
        $io->writeln('');
        foreach ($results as $data) {

            $io->writeln('');
            $io->writeln('Processing data n° '.$index);
            // $name = $data['en-name'];
            //$referenceUrl = $data['web-scraper-start-url'];

            //$this->em->flush();
            //$this->em->clear();
            $rowProcessed++;
            $index++;
        }
        $io->progressFinish();

        $io->success(sprintf('Successfully import %d rows!', $rowProcessed));
        return 0;
    }
}
