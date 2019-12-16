<?php

namespace Space48\HubSpotWordpressBlogMigration;

class ImageSearcher
{
    private $imageRegex;

    /**
     * ImageSearcher constructor.
     *
     * @param string $imageRegex - Regular expression to use to find image URLs.
     */
    public function __construct(string $imageRegex = null)
    {
        $this->imageRegex = $imageRegex ?? '/(https?:\/\/\S+\/(\S+\.(?:jpg|jpeg|png|gif)))/i';
    }

    /**
     * Use regex to find image URLs
     *
     * @param string $text
     *
     * @return array
     * @throws \ErrorException
     */
    public function images(string $text)
    {
        $images = [];

        preg_match_all($this->imageRegex, $text, $images);

        if (!isset($images[0])) {
            throw new \ErrorException('Unexpected empty response from preg_match_all');
        }

        return $images[0];
    }

    /**
     * Update image paths in blog post to new format.
     * e.g
     * https://cdn2.hubspot.net/hubfs/Blog-Images/test1.jpg
     * /wp-content/uploads/migrated/test1.jpg
     *
     * @param string $text    - Blog post field text
     * @param string $newPath - New path prefix to include before filename
     *
     * @return string $text
     */
    public function updateImagePathInText(string $text, string $newPath)
    {
        return preg_replace($this->imageRegex, "$newPath$2", $text);
    }

}