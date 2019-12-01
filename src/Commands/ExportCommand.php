<?php

namespace Space48\HubSpotBlogExport\Commands;

use Space48\HubSpotBlogExport\BlogPostService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExportCommand extends BaseCommand
{
    protected static $defaultName = 'hubspot:blog:export';

    /**
     * ExportCommand constructor.
     *
     * @param string|null $name
     */
    public function __construct(string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure()
    {
        parent::configure();
        $this->setDescription('Exports all blog posts from HubSpot');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Starting blog export");

        $blogPostService = new BlogPostService($this->getApiKey($input));

        try {
            $pageGenerator = $blogPostService->getPage();
        } catch (\Exception $e) {
            $output->writeln("<error>" . $e->getMessage() . "</error>");
            return 1;
        }

        foreach ($pageGenerator as $page) {
            var_dump($page);
        }

        return 0;
    }
}
