<?php

namespace Space48\HubSpotWordpressBlogMigration;

/**
 * Takes a list of images and downloads them, flattening to a single directory of images.
 *
 * @package Space48\HubSpotWordpressBlogMigration
 */
class ImageDownloader
{
    /** @var bool */
    private $allowOverwrites;

    /**
     * ImageDownloader constructor.
     *
     * @param bool $allowOverwrites - Allow duplicate images to overwrite previous.
     */
    public function __construct(bool $allowOverwrites = true)
    {
        $this->allowOverwrites = $allowOverwrites;
    }

    /**
     * Download images and save to same path as URL.
     *
     * @param array  $imageUrls
     * @param string $downloadPath
     *
     * @throws \ErrorException
     */
    public function download(array $imageUrls, string $downloadPath)
    {
        if (!is_dir($downloadPath)) {
            $success = mkdir($downloadPath, 0777, true);
            if (!$success) {
                throw new \ErrorException('Unable to create download path directory');
            }
        }

        foreach ($imageUrls as $imageUrl) {
            $filePath = $downloadPath . basename($imageUrl);
            if (file_exists($filePath) && !$this->allowOverwrites) {
                throw new \ErrorException('Duplicate image name: ' . $imageUrl);
            }


            $imageData = @file_get_contents($imageUrl);
            if ($imageData === false) {
                // Skip if unable to download image
                continue;
            }

            $success = file_put_contents($filePath, $imageData);
            if (!$success) {
                throw new \ErrorException('Image unable to be saved');
            }
        }
    }
}