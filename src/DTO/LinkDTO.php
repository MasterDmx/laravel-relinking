<?php

namespace MasterDmx\LaravelRelinking\DTO;

use MasterDmx\LaravelRelinking\Link;

class LinkDTO
{
    public $id;

    public Link $link;

    public function __construct($id, Link $link)
    {
        $this->id = $id;
        $this->link = $link;
    }
}
