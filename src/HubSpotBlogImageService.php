<?php

namespace Space48\HubSpotWordpressBlogMigration;

/**
 * Class HubSpotBlogImageService
 *
 * This service identifies a list of images that occur in a blog post.
 *
 * @package Space48\HubSpotWordpressBlogMigration
 */
class HubSpotBlogImageService
{
    const NEW_IMAGE_PATH = '/wp-content/uploads/migrated/';
    /**
     * @var array - List of blog post fields that might contain images.
     */
    private $imageFields;

    /**
     * @var array - List of blog post author fields that might contain images.
     */
    private $blogAuthorImageFields;

    /** @var ImageSearcher */
    private $imageSearcher;

    /**
     * HubSpotBlogImageService constructor.
     *
     * @param array $imageFields - Array of blog post fields to search for images in.
     */
    public function __construct(
        array $imageFields = null,
        array $blogAuthorImageFields = null,
        ImageSearcher $imageSearcher = null
    ) {
        $this->imageFields = $imageFields ?? ['post_body'];
        $this->blogAuthorImageFields = $blogAuthorImageFields ?? ['avatar'];
        $this->imageSearcher = $imageSearcher ?? new ImageSearcher();
    }

    /**
     * Take a blog post and return all image URLS found.
     *
     * @param array $blogPost - Blog post array as exported from HubSpot
     *
     * @return array - List of image URLs
     * @throws \ErrorException
     */
    public function findImages(array $blogPost)
    {
        $images = [];

        foreach ($this->imageFields as $imageField) {
            $images = array_merge($images, $this->imageSearcher->images($blogPost[$imageField]));
        }

        foreach ($this->blogAuthorImageFields as $blogAuthorImageField) {
            $images = array_merge($images,
                $this->imageSearcher->images($blogPost['blog_author'][$blogAuthorImageField]));
        }

        return $images;
    }

    /**
     * Update image paths in blog post to new format.
     *
     * @param array $blogPost
     *
     * @return array
     */
    public function updateImagePaths(array $blogPost)
    {
        // Loop through blog post fields updating image paths
        foreach ($this->imageFields as $imageField) {
            $blogPost[$imageField] = $this->imageSearcher->updateImagePathInText(
                $blogPost[$imageField],
                self::NEW_IMAGE_PATH
            );
        }

        // Loop through blog post author fields.
        foreach ($this->blogAuthorImageFields as $blogAuthorImageField) {
            $blogPost['blog_author'][$blogAuthorImageField] =
                $this->imageSearcher->updateImagePathInText(
                    $blogPost['blog_author'][$blogAuthorImageField],
                    self::NEW_IMAGE_PATH
                );
        }

        return $blogPost;
    }
}