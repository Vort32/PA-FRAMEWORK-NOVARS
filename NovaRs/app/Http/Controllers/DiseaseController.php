<?php

namespace App\Http\Controllers;

use App\Models\Disease;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DiseaseController extends Controller
{
    public function index(): View
    {
        Gate::authorize('access-admin');

        $diseases = Disease::orderBy('name')->paginate(12);

        return view('admin.diseases.index', compact('diseases'));
    }

    public function create(): View
    {
        Gate::authorize('access-admin');

        return view('admin.diseases.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('access-admin');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'icd_code' => ['nullable', 'string', 'max:50', 'unique:diseases,icd_code'],
            'description' => ['nullable', 'string'],
        ]);

        Disease::create($data);

        return redirect()->route('admin.diseases.index')->with('status', 'Disease created');
    }

    public function edit(Disease $disease): View
    {
        Gate::authorize('access-admin');

        return view('admin.diseases.edit', compact('disease'));
    }

    public function update(Request $request, Disease $disease): RedirectResponse
    {
        Gate::authorize('access-admin');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'icd_code' => ['nullable', 'string', 'max:50', 'unique:diseases,icd_code,'.$disease->id],
            'description' => ['nullable', 'string'],
        ]);

        $disease->update($data);

        return redirect()->route('admin.diseases.index')->with('status', 'Disease updated');
    }

    public function destroy(Disease $disease): RedirectResponse
    {
        Gate::authorize('access-admin');

        $disease->delete();

        return redirect()->route('admin.diseases.index')->with('status', 'Disease deleted');
    }
}
