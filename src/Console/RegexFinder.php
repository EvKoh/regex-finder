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
            ->addArgument('searchPath', InputArgument::REQUIRED, 'The path where search...')
            ->addArgument('begin', InputArgument::OPTIONAL, 'Begin with...')
            ->addArgument('end', InputArgument::OPTIONAL, 'End with...');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //$this->clear();
        foreach ($this->regexCreator->getByGroup($input->getArgument('group'), [
            (string)$input->getArgument('begin'),
            (string)$input->getArgument('end'),
        ]) as $regexElements) {
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