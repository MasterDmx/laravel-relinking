<?php

namespace MasterDmx\LaravelRelinking\DTO;

class ContextDTO
{
    public string $id;
    public string $url;
    public string $title;

    public function __construct(string $id, string $url, string $title)
    {
        $this->id = $id;
        $this->url = $url;
        $this->title = $title;
    }
}
