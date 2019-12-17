<?php

namespace Space48\HubSpotWordpressBlogMigration\Commands;

use JsonMachine\JsonMachine;
use Space48\HubSpotWordpressBlogMigration\HubSpotBlogImageService;
use Space48\HubSpotWordpressBlogMigration\ImageDownloader;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Violet\StreamingJsonEncoder\BufferJsonEncoder;
use Violet\StreamingJsonEncoder\StreamJsonEncoder;

class DownloadMediaCommand extends BaseCommand
{
    const FILENAME_ARGUMENT = 'filename';

    protected static $defaultName = 'hubspot:media:download';

    protected function configure()
    {
        parent::configure();
        $this->addArgument(self::FILENAME_ARGUMENT, InputArgument::REQUIRED, 'JSON file');
        $this->setDescription('Find and download media and update media paths in json');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Find image URLs
        $filename = $input->getArgument(self::FILENAME_ARGUMENT);
        $imageUrls = $this->getImageUrlsFromBlogPosts($filename);

        // Download images
        $path = getcwd() . '/downloaded-images/';
        $this->downloadImages($imageUrls, $path);

        // Update original image URL paths
        $this->updateImagePaths($filename, $output);

        return 0;
    }

    /**
     * Load blog post json file and return an array of all image URLs found within
     *
     * @param string $blogPostsFileName
     *
     * @return array
     * @throws \ErrorException
     */
    private function getImageUrlsFromBlogPosts($blogPostsFileName)
    {
        $hubSpotImageService = new HubSpotBlogImageService();
        $imageUrls = [];

        $blogs = JsonMachine::fromFile(getcwd() . "/$blogPostsFileName");
        foreach ($blogs as $blog) {
            $imageUrls = array_merge($imageUrls, $hubSpotImageService->findImages($blog));
        }

        return array_unique($imageUrls);
    }

    /**
     * Download all images found
     *
     * @param array  $imageUrls - List of image URLs to download
     * @param string $path      - Directory to save images.
     *
     * @throws \ErrorException
     */
    private function downloadImages(array $imageUrls, string $path)
    {
        $imageDownloader = new ImageDownloader();
        $imageDownloader->download($imageUrls, $path);
    }

    /**
     * Update all image URLs in blog posts according to new path.
     *
     * @param string          $blogPostsFileName
     * @param OutputInterface $output
     */
    private function updateImagePaths($blogPostsFileName, OutputInterface $output)
    {
        $blogs = JsonMachine::fromFile(getcwd() . "/$blogPostsFileName");
        $updatedBlogPosts = $this->useHubSpotImageServiceToUpdatePaths($blogs);

        $jsonEncoder = new StreamJsonEncoder(
            $updatedBlogPosts,
            function ($json) use ($output) {
                $output->write($json);
            }
        );
        $jsonEncoder->setOptions(JSON_PRETTY_PRINT);
        $jsonEncoder->encode();
    }

    /**
     * Loop through blog posts and provided updated version
     *
     * @param \IteratorAggregate $blogs
     *
     * @return \Generator
     */
    private function useHubSpotImageServiceToUpdatePaths(\IteratorAggregate $blogs)
    {
        $hubSpotImageService = new HubSpotBlogImageService();

        foreach ($blogs as $blog) {
            yield $hubSpotImageService->updateImagePaths($blog);
        }
    }
}
