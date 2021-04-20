<?php

namespace MasterDmx\LaravelRelinking;

class DefaultLinkData implements LinkData
{
    private string $id;

    private string $url;

    private string $title;

    /**
     * DefaultLink constructor.
     *
     * @param string $id
     * @param string $url
     * @param string $title
     */
    public function __construct(string $id, string $url, string $title)
    {
        $this->id = $id;
        $this->url = $url;
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'url' => $this->url,
            'title' => $this->title,
        ];
    }
}
