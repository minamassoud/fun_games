<?php

namespace App\Domain\TrashGame\Domain\Contracts;

use App\Domain\TrashGame\Domain\Entities\Layout;

interface LayoutFactoryInterface
{
    public function make(array $cards): Layout;
}
