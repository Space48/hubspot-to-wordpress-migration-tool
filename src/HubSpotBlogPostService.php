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
        $topics = $this->getTopics();
        $page = $this->fetchPageOfBlogPosts(0);

        if (!isset($page->data->objects)) {
            throw new \Exception("Unable to process HubSpot API response");
        }

        $total = $page->data->total;
        for ($offset = 0; $offset + self::PAGESIZE < $total; $offset += self::PAGESIZE) {
            $page = $this->fetchPageOfBlogPosts($offset);

            foreach ($page->data->objects as $post) {
                yield $this->updatePostWithTopics($post, $topics);
            }
        }
    }

    /**
     * Get all topics from HubSpot
     *
     * @return array
     * @throws \Exception
     */
    public function getTopics()
    {
        $page = $this->fetchPageOfTopics(0);

        if (!isset($page->data->objects)) {
            throw new \Exception("Unable to process HubSpot API response");
        }

        $total = $page->data->total;
        $topics = [];
        for ($offset = 0; $offset + self::PAGESIZE < $total; $offset += self::PAGESIZE) {
            $page = $this->fetchPageOfTopics($offset);

            $topics = array_merge($topics, $page->data->objects);
        }

        return $topics;
    }

    /**
     * Make API request to HubSpot for page of blog posts.
     *
     * @param int $offset
     * @param int $pageSize
     *
     * @return \SevenShores\Hubspot\Http\Response
     */
    private function fetchPageOfBlogPosts(int $offset, int $pageSize = self::PAGESIZE)
    {
        return $this->hubspotClient->blogPosts()->all([
            'count'  => $pageSize,
            'offset' => $offset
        ]);
    }

    /**
     * Make API request to HubSpot for page of topics
     *
     * @param int $offset
     * @param int $pageSize
     *
     * @return \SevenShores\Hubspot\Http\Response
     */
    private function fetchPageOfTopics(int $offset, int $pageSize = self::PAGESIZE)
    {
        return $this->hubspotClient->blogTopics()->all([
            'count'  => $pageSize,
            'offset' => $offset
        ]);
    }

    /**
     * Use topic IDs in blog post to look up topics and add as human-readable tags
     *
     * @param object   $post
     * @param object[] $topics
     *
     * @return object
     */
    private function updatePostWithTopics($post, $topics)
    {
        $post->tags = [];
        foreach ($post->topic_ids as $topicId) {
            foreach ($topics as $topic) {
                if ($topic->id == $topicId) {
                    array_push($post->tags, $topic->name);
                }
            }
        }

        return $post;
    }
}
