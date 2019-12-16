<?php

namespace Space48\HubSpotWordpressBlogMigration;

use SevenShores\Hubspot\Factory;

class HubSpotBlogPostService
{
    const PAGESIZE = 20;

    private $hubspotClient;

    /**
     * BlogPostService constructor.
     *
     * @param string $apiKey
     */
    public function __construct(string $apiKey)
    {
        $this->hubspotClient = Factory::create($apiKey);
    }

    /**
     * Get all blog posts from HubSpot
     *
     * @return \Generator
     * @throws \Exception
     */
    public function getBlogPosts()
    {
        $page = $this->fetchPage(0);

        if (!isset($page->data->objects)) {
            throw new \Exception("Unable to process HubSpot API response");
        }

        $total = $page->data->total;
        for ($offset = 0; $offset + self::PAGESIZE < $total; $offset += self::PAGESIZE) {
            $page = $this->fetchPage($offset);

            foreach ($page->data->objects as $post) {
                yield $post;
            }
        }
    }

    /**
     * @param int $offset
     * @param int $pageSize
     *
     * @return \SevenShores\Hubspot\Http\Response
     */
    private function fetchPage(int $offset, int $pageSize = self::PAGESIZE)
    {
        return $this->hubspotClient->blogPosts()->all([
            'count' => $pageSize,
            'offset' => $offset
        ]);
    }
}
