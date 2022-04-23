<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Tests\Util;

use Behat\Behat\Context\Context;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpKernel\KernelInterface;

final class FeatureContext implements Context
{
    private KernelInterface $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @BeforeScenario
     */
    public function cleanBeforeScenario(): void
    {
        $this->bootstrapEnvironment();
    }

    /**
     * @AfterScenario
     */
    public function cleanAfterScenario(): void
    {
        $this->bootstrapEnvironment();
    }

    private function bootstrapEnvironment(): void
    {
        $application = $this->getApplication();

        $arg = new ArrayInput(
            [
                'command' => 'database:truncate',
                '--no-interaction' => true,
            ],
        );

        $application->run($arg, new NullOutput());
    }

    private function getApplication(): Application
    {
        $app = new Application($this->kernel);
        $app->setAutoExit(false);

        return $app;
    }
}
