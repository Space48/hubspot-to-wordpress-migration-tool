<?php

namespace Space48\HubSpotBlogExport;

use SevenShores\Hubspot\Factory;

class BlogPostService
{
    private $hubspotClient;

    public function __construct(string $apiKey)
    {
        $this->hubspotClient = Factory::create($apiKey);
    }

    /**
     * @param int $pageSize
     *
     * @return \Generator
     * @throws \Exception
     */
    public function getPage($pageSize = 20)
    {
        $page = $this->fetchPage($pageSize, 0);

        if (!isset($page->data->objects)) {
            throw new \Exception("Unable to process HubSpot API response");
        }

        $total = $page->data->total;
        for ($offset = 0; $offset + $pageSize < $total; $offset += $pageSize) {
            $page = $this->fetchPage($pageSize, $offset);
            yield $page->data->objects;
        }
    }

    /**
     * @param int $pageSize
     * @param int $offset
     *
     * @return \SevenShores\Hubspot\Http\Response
     */
    private function fetchPage(int $pageSize, int $offset)
    {
        return $this->hubspotClient->blogPosts()->all([
            'count' => $pageSize,
            'offset'
        ]);
    }
}
