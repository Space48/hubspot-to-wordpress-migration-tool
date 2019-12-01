<?php

namespace Space48\HubSpotBlogExport\Commands;

use Dotenv\Dotenv;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class BaseCommand extends Command
{
    private static $apiKeyOption = 'api-key';
    private static $apiKeyEnv = 'HUBSPOT_API_KEY';

    public function __construct(string $name = null)
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        parent::__construct($name);
    }

    protected function configure()
    {
        $this->addOption(self::$apiKeyOption, 'k', InputOption::VALUE_REQUIRED, 'Hubspot API Key');
        parent::configure();
    }

    /**
     * Get API Key from input option or environment variable
     *
     * @param InputInterface $input
     *
     * @return array|bool|false|string|string[]|null
     */
    protected function getApiKey(InputInterface $input)
    {
        if ($apiKey = $input->getOption(self::$apiKeyOption)) {
            return $apiKey;
        }

        if ($apiKey = getenv(self::$apiKeyEnv)) {
            return $apiKey;
        }

        throw new \InvalidArgumentException('HubSpot API Key not provided');
    }
}