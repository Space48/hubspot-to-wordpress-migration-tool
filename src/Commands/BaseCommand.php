<?php

namespace Space48\HubSpotWordpressBlogMigration\Commands;

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
}