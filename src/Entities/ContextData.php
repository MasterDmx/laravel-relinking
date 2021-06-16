<?php

namespace MasterDmx\LaravelRelinking\Entities;

class ContextData
{
    public function __construct(
        public $id,
        public $url,
        public $title
    ){}
}
