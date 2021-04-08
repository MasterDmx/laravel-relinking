<?php

namespace MasterDmx\LaravelRelinking\Links;

class LinkSaturateDTO
{
    protected string $id;
    protected string $url;
    protected string $title;

    /**
     * LinkSaturateDTO constructor.
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
}
