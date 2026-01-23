<?php

namespace App\Http\Controllers;

use App\Repositories\MenuSectionRepository;
use App\Services\MenuSectionService;
use Illuminate\Http\Request;

class MenuSectionController extends Controller
{
    public function __construct(
        protected MenuSectionService $menuSectionService,
        protected MenuSectionRepository $menuSectionRepotiroy
    ) {}
}
