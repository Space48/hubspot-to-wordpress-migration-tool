<?php

namespace Space48\HubSpotWordpressBlogMigration\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class WordpressCommand extends BaseCommand
{
    private static $apiUrlOption = 'wordpress-api-url';
    private static $apiUserOption = 'wordpress-api-user';
    private static $apiPasswordOption = 'wordpress-api-password';

    private static $apiUrlEnv = 'WORDPRESS_API_URL';
    private static $apiUserEnv = 'WORDPRESS_API_USER';
    private static $apiPasswordEnv = 'WORDPRESS_API_PASSWORD';

    protected function configure()
    {
        $this->addOption(self::$apiUrlOption, null, InputOption::VALUE_REQUIRED, 'Wordpress API URL');
        $this->addOption(self::$apiUserOption, null, InputOption::VALUE_REQUIRED, 'Wordpress API User');
        $this->addOption(self::$apiPasswordOption, null, InputOption::VALUE_REQUIRED, 'Wordpress API Password');
        parent::configure();
    }

    /**
     * Get WordPress API URL from input option or environment variable
     *
     * @param InputInterface $input
     *
     * @return array|bool|false|string|string[]|null
     */
    protected function getWordPressAPIURL(InputInterface $input)
    {
        if ($option = $input->getOption(self::$apiUrlOption)) {
            return $option;
        }

        if ($env = getenv(self::$apiUrlEnv)) {
            return $env;
        }

        throw new \InvalidArgumentException('WordPress API URL not provided');
    }

    /**
     * Get WordPress API User from input option or environment variable
     *
     * @param InputInterface $input
     *
     * @return array|bool|false|string|string[]|null
     */
    protected function getWordPressAPIUser(InputInterface $input)
    {
        if ($option = $input->getOption(self::$apiUserOption)) {
            return $option;
        }

        if ($env = getenv(self::$apiUserEnv)) {
            return $env;
        }

        throw new \InvalidArgumentException('WordPress API User not provided');
    }

    /**
     * Get WordPress API Password from input option or environment variable
     *
     * @param InputInterface $input
     *
     * @return array|bool|false|string|string[]|null
     */
    protected function getWordPressAPIPassword(InputInterface $input)
    {
        if ($option = $input->getOption(self::$apiPasswordOption)) {
            return $option;
        }

        if ($env = getenv(self::$apiPasswordEnv)) {
            return $env;
        }

        throw new \InvalidArgumentException('WordPress API Password not provided');
    }
}