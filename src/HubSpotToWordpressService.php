<?php

namespace Space48\HubSpotWordpressBlogMigration;

use JsonMachine\JsonMachine;

class HubSpotToWordpressService
{
    /**
     * @var WordPressApiClient
     */
    private $client;

    /**
     * WordPressApiClient constructor.
     *
     * @param string $apiUrl
     * @param string $apiUser
     * @param string $apiPassword
     */
    public function __construct(
        string $apiUrl,
        string $apiUser,
        string $apiPassword
    ) {
        $this->client = new WordPressApiClient($apiUrl, $apiUser, $apiPassword);
    }

    /**
     * Take hubspot data and create WordPress post
     * Do nothing if already exists.
     *
     * @param array  $blogPost      - HubSpot format blog post
     * @param string $defaultAuthor - Default author name
     *
     * @throws \Exception
     */
    public function createBlogPost(array $blogPost, string $defaultAuthor)
    {
        // Check if post already exists.
        $posts = $this->client->getPosts(['search' => $blogPost['name']]);
        foreach ($posts as $post) {
            if ($post['slug'] == $this->getSlug($blogPost['slug'])) {
                // Skip if post already exists.
                return;
            }
        }

        // Fetch associated wordpress data.
        $authorID = $this->getAuthor($blogPost['blog_author'], $defaultAuthor);
        $tags = $this->getTags($blogPost['tags']);
        $category = $this->getCategory($blogPost['parent_blog']);

        $featuredMedia = null;
        if (!empty($blogPost['featured_image'])) {
            $file = getcwd() . '/downloaded-images/' . basename($blogPost['featured_image']);
            $featuredMedia = $this->getMedia($file);
        }

        // Create blog post.
        $this->client->createPost(
            \DateTimeImmutable::createFromFormat('U', substr($blogPost['publish_date'], 0, -3)),
            $blogPost['name'],
            $blogPost['post_body'],
            $authorID,
            $this->getSlug($blogPost['slug']),
            [$category],
            $tags,
            $featuredMedia
        );
    }

    /**
     * Get Author for blog post, create author if they don't exist
     *
     * @param array  $blogAuthor    - HubSpot Author
     * @param string $defaultAuthor - Default author name
     *
     * @return int
     *
     * @throws \Exception
     */
    private function getAuthor(array $blogAuthor, string $defaultAuthor)
    {
        // Search for author
        $authors = $this->client->getUsers(['search' => $blogAuthor['full_name']]);
        foreach ($authors as $author) {
            if ($author['name'] == $blogAuthor['full_name']) {
                return $author['id'];
            }

            // Override HubSpot user if 'admin' is the user.
            if (empty($blogAuthor['email']) && $author['name'] == $defaultAuthor) {
                return $author['id'];
            }
        }

        // Not found, create author
        $author = $this->client->createUser(
            $blogAuthor['email'],
            $blogAuthor['email'],
            null,
            $blogAuthor['full_name'],
            null,
            null,
            $blogAuthor['bio'],
            $blogAuthor['slug']
        );

        return $author['id'];
    }

    /**
     * Get tag IDs for blog post
     *
     * @param array $tags
     *
     * @return array - Array of Tag Ids
     */
    private function getTags(array $tags)
    {
        $tagIds = [];
        foreach ($tags as $tag) {
            $tagIds[] = $this->getTag($tag);
        }

        return $tagIds;
    }

    /**
     * Get tag ID for blog post, create tag if it doesn't exist.
     *
     * @param string $tag
     *
     * @return int - Tag ID
     */
    private function getTag($tag)
    {
        // Search for tag
        $wordpressTags = $this->client->getTags(['search' => $tag]);
        foreach ($wordpressTags as $wordpressTag) {
            if ($wordpressTag['name'] == $tag) {
                return $wordpressTag['id'];
            }
        }

        // Not found, create tag
        $wordpressTag = $this->client->createTag($tag);

        return $wordpressTag['id'];
    }

    /**
     * Use HubSpot Parent blog as the category. Find category ID or create in WordPress.
     *
     * @param $parentBlog
     *
     * @return int - Category ID
     *
     */
    private function getCategory(array $parentBlog)
    {
        // Search for category
        $categories = $this->client->getCategories(['search' => $parentBlog['name']]);
        foreach ($categories as $category) {
            if ($category['name'] == $parentBlog['name']) {
                return $category['id'];
            }
        }

        // Not found, create tag
        $category = $this->client->createCategory($parentBlog['name']);

        return $category['id'];
    }

    /**
     * Get media ID, create by uploading if not found.
     *
     * @param string $path
     *
     * @return mixed
     */
    private function getMedia(string $path)
    {
        // Search for media
        $media = $this->client->getMedia(['search' => basename('path')]);
        foreach ($media as $medium) {
            // If filenames match, assume the media is the same
            if ($medium['media_details']['sizes']['full']['file'] == basename($path)) {
                return $medium['id'];
            }
        }

        // Not found, create media
        $medium = $this->client->createMedia($path);

        return $medium['id'];

    }

    /**
     * Remove category prefix from HubSpot slug
     *
     * @param $hubSpotSlug
     *
     * @return string
     */
    private function getSlug(string $hubSpotSlug)
    {
        $slugParts = explode('/', $hubSpotSlug);
        return array_pop($slugParts);
    }
}
