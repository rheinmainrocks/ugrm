<?php

namespace UGRM\DataBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use UGRM\DataBundle\UsergroupRepository;

class FetchMeetingsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ugrmdata:fetchmeetings')
            ->setDescription('Ruft iCal-Calender ab und speichert diese lokal')
            ->addArgument(
                'datadir',
                InputArgument::REQUIRED,
                'Verzeichnis mit Usergroup-Daten'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ugdir = $input->getArgument('datadir');
        $data = new UsergroupRepository(new \SplFileInfo($ugdir));
        foreach ($data->listGroups() as $group) {
            if (!$group->ical) continue;
            copy($group->ical, sprintf($ugdir . DIRECTORY_SEPARATOR . '%s.ical', $group->id));
            $output->writeln($group->name);
        }
    }
}