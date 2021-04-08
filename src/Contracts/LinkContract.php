<?php

namespace MasterDmx\LaravelRelinking\Contracts;

use MasterDmx\LaravelRelinking\Contexts\Context;

interface LinkContract
{
    public function getId(): string;

    public function getContext(): Context;
}
