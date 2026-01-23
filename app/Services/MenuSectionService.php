<?php

namespace App\Services;

use App\Repositories\MenuSectionRepository;

class MenuSectionService
{
    public function __construct(
        protected MenuSectionRepository $menuSectionRepository
    ) {}
}
