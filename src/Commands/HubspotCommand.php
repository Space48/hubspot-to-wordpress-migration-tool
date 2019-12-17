<?php

namespace Space48\HubSpotWordpressBlogMigration\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class HubspotCommand extends BaseCommand
{
    private static $apiKeyOption = 'hubspot-api-key';
    private static $apiKeyEnv = 'HUBSPOT_API_KEY';

    protected function configure()
    {
        $this->addOption(self::$apiKeyOption, null, InputOption::VALUE_REQUIRED, 'Hubspot API Key');
        parent::configure();
    }

    /**
     * Get API Key from input option or environment variable
     *
     * @param InputInterface $input
     *
     * @return array|bool|false|string|string[]|null
     */
    protected function getHubspotApiKey(InputInterface $input)
    {
        if ($option = $input->getOption(self::$apiKeyOption)) {
            return $option;
        }

        if ($env = getenv(self::$apiKeyEnv)) {
            return $env;
        }

        throw new \InvalidArgumentException('HubSpot API Key not provided');
    }
}