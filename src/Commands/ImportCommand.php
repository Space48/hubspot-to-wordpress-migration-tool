<?php

namespace Space48\HubSpotWordpressBlogMigration\Commands;

use JsonMachine\JsonMachine;
use Space48\HubSpotWordpressBlogMigration\HubSpotToWordpressService;
use Space48\HubSpotWordpressBlogMigration\WordPressApiClient;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportCommand extends WordpressCommand
{
    const BLOG_POSTS_FILENAME_ARGUMENT = 'BLOG_POSTS_FILENAME';
    const DEFAULT_AUTHOR_NAME = 'DEFAULT_AUTHOR_NAME';

    protected static $defaultName = 'wordpress:blog:import';

    /**
     * ImportCommand constructor.
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

        $this->addArgument(self::BLOG_POSTS_FILENAME_ARGUMENT, InputArgument::REQUIRED, 'Blog posts JSON file');
        $this->addArgument(self::DEFAULT_AUTHOR_NAME, InputArgument::REQUIRED,
            'Default author to use when HubSpot provides author without an email');
        $this->setDescription('Import json export of blog posts into Wordpress');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $blogPosts = JsonMachine::fromFile(getcwd() . "/" . $input->getArgument(self::BLOG_POSTS_FILENAME_ARGUMENT));

        $defaultAuthor = $input->getArgument(self::DEFAULT_AUTHOR_NAME);

        $wordpress = new HubSpotToWordpressService(
            $this->getWordPressAPIURL($input),
            $this->getWordPressAPIUser($input),
            $this->getWordPressAPIPassword($input)
        );

        foreach ($blogPosts as $blogPost) {
            try {
                $wordpress->createBlogPost($blogPost, $defaultAuthor);
            } catch (\Exception $e) {
                $io = new SymfonyStyle($input, $output);
                $io->getErrorStyle()->error("<error>" . $e->getMessage() . "</error>");
                return 1;
            }
        }

        return 0;
    }
}
