<?php

namespace Space48\HubSpotWordpressBlogMigration;

use GuzzleHttp\Client;
use Vnn\WpApiClient\Auth\WpBasicAuth;
use Vnn\WpApiClient\Http\GuzzleAdapter;
use Vnn\WpApiClient\WpClient;

class WordPressApiClient
{
    /**
     * @var WpClient
     */
    private $client;

    /**
     * @var array
     */
    private $defaultParams;

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
        string $apiPassword,
        array $defaultParams = null
    ) {
        $this->client = new WpClient(new GuzzleAdapter(new Client()), $apiUrl);
        $this->client->setCredentials(new WpBasicAuth($apiUser, $apiPassword));
        $this->defaultParams = $defaultParams ?? ['per_page' => 100];
    }

    /**
     * Get blog posts
     *
     * @param $params
     *
     * @return array
     */
    public function getPosts(array $params = [])
    {
        $params = array_merge($this->defaultParams, $params);
        return $this->client->posts()->get(null, $params);
    }

    /**
     * Get authors/users
     *
     * @param array $params
     *
     * @return array
     */
    public function getUsers(array $params = [])
    {
        $params = array_merge($this->defaultParams, $params);
        return $this->client->users()->get(null, $params);
    }

    /**
     * Get Tags
     *
     * @param array $params
     *
     * @return array
     */
    public function getTags(array $params = [])
    {
        $params = array_merge($this->defaultParams, $params);
        return $this->client->tags()->get(null, $params);
    }

    /**
     * Get Categories
     *
     * @param array $params
     *
     * @return array
     */
    public function getCategories(array $params = [])
    {
        $params = array_merge($this->defaultParams, $params);
        return $this->client->categories()->get(null, $params);
    }

    /**
     * Get Media
     *
     * @param array $params
     *
     * @return array
     */
    public function getMedia(array $params = [])
    {
        $params = array_merge($this->defaultParams, $params);
        return $this->client->media()->get(null, $params);
    }

    /**
     * Create a post.
     *
     * @param \DateTimeImmutable $date
     * @param string             $title
     * @param string             $content
     * @param int                $author
     * @param string|null        $slug
     * @param array              $categories
     * @param array              $tags
     * @param int|null           $featuredMedia
     * @param string             $status
     * @param string             $commentStatus
     * @param string             $pingStatus
     * @param string             $format
     *
     * @return array
     */
    public function createPost(
        \DateTimeImmutable $date,
        string $title,
        string $content,
        int $author,
        string $slug = null,
        array $categories = [],
        array $tags = [],
        int $featuredMedia = null,
        string $status = 'publish',
        string $commentStatus = 'closed',
        string $pingStatus = 'closed',
        string $format = 'standard'
    ) {

        return $this->client->posts()->save([
            'date'           => $date->format(DATE_ATOM),
            'slug'           => $slug,
            'status'         => $status,
            'title'          => $title,
            'content'        => $content,
            'author'         => $author,
            'featured_media' => $featuredMedia,
            'comment_status' => $commentStatus,
            'ping_status'    => $pingStatus,
            'format'         => $format,
            'categories'     => $categories,
            'tags'           => $tags
        ]);
    }

    /**
     * Create author/user
     *
     * @param string      $username
     * @param string      $email
     * @param string      $password
     * @param string|null $name
     * @param string|null $firstName
     * @param string|null $lastName
     * @param string|null $description - Bio
     * @param string|null $slug        - URL Slug
     *
     * @return array
     * @throws \Exception
     */
    public function createUser(
        string $username,
        string $email,
        string $password = null,
        string $name = null,
        string $firstName = null,
        string $lastName = null,
        string $description = null,
        string $slug = null
    ) {
        return $this->client->users()->save(
            [
                'username'    => $username,
                'email'       => $email,
                'password'    => $password ?? PasswordGenerator::generate(20),
                'name'        => $name,
                'first_name'  => $firstName,
                'last_name'   => $lastName,
                'description' => $description,
                'slug'        => $slug,
            ]
        );
    }

    /**
     * Create tag
     *
     * @param string $tag
     *
     * @return array
     */
    public function createTag(string $tag)
    {
        return $this->client->tags()->save(['name' => $tag]);
    }

    /**
     * Create category
     *
     * @param string $name
     *
     * @return array
     */
    public function createCategory(string $name)
    {
        return $this->client->categories()->save(['name' => $name]);
    }

    /**
     * Upload file to media
     *
     * @param string $path
     *
     * @return array
     */
    public function createMedia(string $path)
    {
        return $this->client->media()->upload($path);
    }
}
