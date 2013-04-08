<?php

namespace UGRM\DataBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use UGRM\DataBundle\UsergroupRepository;

class GenerateLogosCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ugrmdata:generatelogos')
            ->setDescription('Generiert Logos für Usergroups, die kein eigenes Logo haben')
            ->addArgument(
                'datadir',
                InputArgument::REQUIRED,
                'Verzeichnis mit Usergroup-Daten'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ugdir = $input->getArgument('datadir');
        $svgtemplate = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . 'iloveugs.svg');
        $data = new UsergroupRepository(new \SplFileInfo(realpath($ugdir)));
        foreach ($data->listGroups() as $group) {
            if ($group->logo) continue;
            $logofile = preg_replace('/\.xml$/', '.logo.png', $data->getXmlFile($group)->getPathname());
            $s = array(
                '{tagname}' => strtoupper($group->tags[0]),
                '{usergroupname}' => $group->name,
                '{groupy}' => stristr($group->tags[0], 'j') === false ? 1070 : 1090,
            );
            file_put_contents($logofile . '.svg', str_replace(array_keys($s), array_values($s), $svgtemplate));
            exec(sprintf('/usr/bin/env convert -background transparent -trim +repage %s %s', escapeshellarg($logofile . '.svg'), escapeshellarg($logofile)));
            $output->writeln(sprintf('%s: %s' . PHP_EOL, $group->name, $logofile));
        }

    }
}