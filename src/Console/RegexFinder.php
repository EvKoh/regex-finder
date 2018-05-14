<?php

namespace App\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class RegexFinder
 * @package App\Console
 */
final class RegexFinder extends AbstractCommand
{
    /**
     *
     */
    protected function configure()
    {
        $this->setName('app:finder')
            ->setDescription('Finder')
            ->setHelp('Finder...')
            ->addArgument('group', InputArgument::REQUIRED, 'The group for regex, example : "translate".')
            ->addArgument('searchPath', InputArgument::REQUIRED, 'The path where search...');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //$this->clear();
        foreach ($this->regexCreator->getByGroup($input->getArgument('group')) as $regexElements) {
            $this->resetFinder();
            $this->finder->files()
                ->in($input->getArgument('searchPath'))
                ->name($regexElements['regexFileName'])
                ->contains($regexElements['regexContent']);

            foreach ($this->finder as $file) {
                var_dump($file->getRealPath());
            }
        }
    }
}