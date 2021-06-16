<?php

namespace MasterDmx\LaravelRelinking\VO;

use Illuminate\Contracts\Support\Arrayable;
use MasterDmx\LaravelRelinking\DTO\ContextDTO;

class Link implements Arrayable
{
    private string $url;
    private string $title;

    public static function fromContextDTO(ContextDTO $dto): static
    {
        return new static($dto->url, $dto->title);
    }

    public function __construct(string $url, string $title)
    {
        $this->url = $url;
        $this->title = $title;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function toArray()
    {
        return [
            'url' => $this->url,
            'title' => $this->title,
        ];
    }
}
