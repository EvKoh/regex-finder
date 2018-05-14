<?php

namespace App\Console;

use Symfony\Component\Console\Command\Command;
use App\Service\RegexCreator;
use Symfony\Component\Finder\Finder;

/**
 * Class AbstractCommand
 * @package App\Console
 */
abstract class AbstractCommand extends Command
{
    /**
     * @var RegexCreator
     */
    protected $regexCreator;

    /**
     * @var Finder
     */
    protected $finder;

    /**
     * AbstractCommand constructor.
     * @param RegexCreator $regexCreator
     */
    public function __construct(RegexCreator $regexCreator)
    {
        $this->regexCreator = $regexCreator;
        $this->finder = new Finder();

        parent::__construct();
    }

    /**
     *
     */
    protected function resetFinder()
    {
        $this->finder = new Finder();
    }

    /**
     *
     */
    protected function clear()
    {
        passthru('clear');
    }
}