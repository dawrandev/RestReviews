<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Services\MenuItemService;
use App\Services\MenuSectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MenuItemController extends Controller
{
    public function __construct(
        protected MenuItemService $service,
        protected MenuSectionService $sectionService
    ) {}

    public function index(Request $request)
    {
        Gate::authorize('viewAny', MenuItem::class);

        $brandId = auth()->user()->brand_id;

        if (!$brandId) {
            return redirect()->back()->with('error', 'Sizga brand biriktirilmagan! Administrator bilan bog\'laning.');
        }

        $search = $request->input('search');
        $sectionId = $request->input('section_id');

        $menuItems = $this->service->getMenuItems($brandId, $search, $sectionId, 15);
        $sections = $this->sectionService->getAllByBrand($brandId);
        $languages = \App\Models\Language::all();

        return view('pages.menu-items.index', compact('menuItems', 'sections', 'languages'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', MenuItem::class);

        $validated = $request->validate([
            'menu_section_id' => 'required|exists:menu_sections,id',
            'translations' => 'required|array',
            'translations.*.name' => 'required|string|max:255',
            'translations.*.description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'base_price' => 'nullable|numeric|min:0',
            'weight' => 'nullable|integer|min:0',
        ]);

        try {
            $this->service->createMenuItem($validated);
            return redirect()->route('menu-items.index')
                ->with('success', 'Блюдо успешно создано!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Произошла ошибка: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(MenuItem $menuItem)
    {
        Gate::authorize('update', $menuItem);

        $translations = [];
        foreach ($menuItem->translations as $translation) {
            $translations[$translation->lang_code] = [
                'name' => $translation->name,
                'description' => $translation->description,
            ];
        }

        return response()->json([
            'id' => $menuItem->id,
            'menu_section_id' => $menuItem->menu_section_id,
            'translations' => $translations,
            'base_price' => $menuItem->base_price,
            'weight' => $menuItem->weight,
            'image_url' => $menuItem->image_path ? asset('storage/' . $menuItem->image_path) : null,
        ]);
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        Gate::authorize('update', $menuItem);

        $validated = $request->validate([
            'menu_section_id' => 'required|exists:menu_sections,id',
            'translations' => 'required|array',
            'translations.*.name' => 'required|string|max:255',
            'translations.*.description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'base_price' => 'nullable|numeric|min:0',
            'weight' => 'nullable|integer|min:0',
        ]);

        try {
            $this->service->updateMenuItem($menuItem, $validated);
            return redirect()->route('menu-items.index')
                ->with('success', 'Блюдо успешно обновлено!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Произошла ошибка: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(MenuItem $menuItem)
    {
        Gate::authorize('delete', $menuItem);

        try {
            $this->service->deleteMenuItem($menuItem);
            return redirect()->route('menu-items.index')
                ->with('success', 'Taom muvaffaqiyatli o\'chirildi!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }
}
