<?php

namespace App\Http\Controllers;

use App\Imports\EquipmentImport;
use App\Models\Equipment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;

class EquipmentController extends Controller
{
    public function index(): View
    {
        Gate::authorize('access-admin');

        $equipments = Equipment::orderBy('name')->paginate(12);

        return view('admin.equipments.index', compact('equipments'));
    }

    public function create(): View
    {
        Gate::authorize('access-admin');

        return view('admin.equipments.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('access-admin');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255', 'unique:equipments,serial_number'],
            'quantity_available' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        Equipment::create($data);

        return redirect()->route('admin.equipments.index')->with('status', 'Equipment created');
    }

    public function edit(Equipment $equipment): View
    {
        Gate::authorize('access-admin');

        return view('admin.equipments.edit', compact('equipment'));
    }

    public function update(Request $request, Equipment $equipment): RedirectResponse
    {
        Gate::authorize('access-admin');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255', 'unique:equipments,serial_number,'.$equipment->id],
            'quantity_available' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        $equipment->update($data);

        return redirect()->route('admin.equipments.index')->with('status', 'Equipment updated');
    }

    public function destroy(Equipment $equipment): RedirectResponse
    {
        Gate::authorize('access-admin');

        $equipment->rooms()->detach();
        $equipment->operations()->detach();
        $equipment->delete();

        return redirect()->route('admin.equipments.index')->with('status', 'Equipment deleted');
    }

    public function import(Request $request): RedirectResponse|JsonResponse
    {
        Gate::authorize('access-admin');

        $data = $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,csv', 'max:5120'],
        ]);

        Excel::import(new EquipmentImport(), $data['file']);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Equipments imported']);
        }

        return redirect()->route('admin.equipments.index')->with('status', 'Equipment imported successfully');
    }
}
