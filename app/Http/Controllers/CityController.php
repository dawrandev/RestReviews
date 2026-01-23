<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Services\CityService;
use App\Http\Requests\StoreCityRequest;
use App\Http\Requests\UpdateCityRequest;
use App\Permissions\CityPermissions;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function __construct(
        private CityService $cityService
    ) {}

    public function index(Request $request)
    {
        $cities = $this->cityService->getCities($request->get('search'));

        return view('pages.cities.index', compact('cities'));
    }

    public function create()
    {
        return view('pages.cities.create');
    }

    public function store(StoreCityRequest $request)
    {
        try {
            $this->cityService->createCity($request->validated());

            return redirect()->route('cities.index')
                ->with('success', 'Город успешно создан');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Ошибка при создании города: ' . $e->getMessage());
        }
    }

    public function edit(City $city)
    {
        $city = $this->cityService->findCity($city->id);

        $translations = [];
        foreach ($city->translations as $translation) {
            $translations[$translation->code] = $translation->name;
        }

        return response()->json([
            'id' => $city->id,
            'translations' => $translations,
        ]);
    }

    public function update(UpdateCityRequest $request, City $city)
    {
        try {
            $this->cityService->updateCity($city, $request->validated());

            return redirect()->route('cities.index')
                ->with('success', 'Город успешно обновлен');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Ошибка при обновлении города: ' . $e->getMessage());
        }
    }

    public function destroy(City $city)
    {
        try {
            if ($city->restaurants()->count() > 0) {
                return back()->with('error', 'Невозможно удалить город. У него есть рестораны.');
            }

            $this->cityService->deleteCity($city);

            return redirect()->route('cities.index')
                ->with('success', 'Город успешно удален');
        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при удалении города: ' . $e->getMessage());
        }
    }
}
