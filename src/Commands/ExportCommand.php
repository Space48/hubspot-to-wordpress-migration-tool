<?php

namespace Space48\HubSpotBlogExport\Commands;

use Space48\HubSpotBlogExport\BlogPostService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Violet\StreamingJsonEncoder\StreamJsonEncoder;

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
        $this->setDescription('Exports all blog posts from HubSpot as json');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $blogPostService = new BlogPostService($this->getApiKey($input));

        try {
            $pageGenerator = $blogPostService->getPage();
        } catch (\Exception $e) {
            $io = new SymfonyStyle($input, $output);
            $io->getErrorStyle()->error("<error>" . $e->getMessage() . "</error>");
            return 1;
        }

        $jsonEncoder = new StreamJsonEncoder(
            $pageGenerator,
            function($json) use ($output) {
                $output->write($json);
            }
        );
        $jsonEncoder->encode();

        return 0;
    }
}
