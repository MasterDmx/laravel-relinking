<?php

namespace MasterDmx\LaravelRelinking\Links;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use MasterDmx\LaravelRelinking\Contexts\ContextRegistry;

class LinkContextCast implements CastsAttributes
{
    private ContextRegistry $contexts;

    public function __construct()
    {
        $this->contexts = app(ContextRegistry::class);
    }

    public function get($model, $key, $value, $attributes)
    {
        return $this->contexts->get($value);
    }

    public function set($model, $key, $value, $attributes)
    {
        return $value;
    }
}
