<?php

namespace App\Http\Controllers;

use App\Enums\RoomStatus;
use App\Models\Equipment;
use App\Models\Room;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RoomController extends Controller
{
    public function index(): View
    {
        Gate::authorize('access-admin');

        $rooms = Room::with('equipments')->paginate(10);

        return view('admin.rooms.index', [
            'rooms' => $rooms,
            'statuses' => RoomStatus::cases(),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('access-admin');

        return view('admin.rooms.create', [
            'equipments' => Equipment::orderBy('name')->get(),
            'statuses' => RoomStatus::cases(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('access-admin');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:rooms,code'],
            'status' => ['required', 'in:'.implode(',', RoomStatus::values())],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
            'equipment_ids' => ['array'],
            'equipment_ids.*' => ['exists:equipments,id'],
            'equipment_quantities' => ['array'],
        ]);

        $room = Room::create([
            'name' => $data['name'],
            'code' => $data['code'],
            'status' => $data['status'],
            'capacity' => $data['capacity'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        $this->syncEquipments($room, $request);

        return redirect()->route('admin.rooms.index')->with('status', 'Room created');
    }

    public function edit(Room $room): View
    {
        Gate::authorize('access-admin');

        return view('admin.rooms.edit', [
            'room' => $room->load('equipments'),
            'equipments' => Equipment::orderBy('name')->get(),
            'statuses' => RoomStatus::cases(),
        ]);
    }

    public function update(Request $request, Room $room): RedirectResponse
    {
        Gate::authorize('access-admin');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:rooms,code,'.$room->id],
            'status' => ['required', 'in:'.implode(',', RoomStatus::values())],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
            'equipment_ids' => ['array'],
            'equipment_ids.*' => ['exists:equipments,id'],
            'equipment_quantities' => ['array'],
        ]);

        $room->update([
            'name' => $data['name'],
            'code' => $data['code'],
            'status' => $data['status'],
            'capacity' => $data['capacity'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        $this->syncEquipments($room, $request);

        return redirect()->route('admin.rooms.index')->with('status', 'Room updated');
    }

    public function destroy(Room $room): RedirectResponse
    {
        Gate::authorize('access-admin');

        $room->equipments()->detach();
        $room->delete();

        return redirect()->route('admin.rooms.index')->with('status', 'Room deleted');
    }

    public function updateStatus(Request $request, Room $room): RedirectResponse|JsonResponse
    {
        Gate::authorize('access-staff');

        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', RoomStatus::values())],
        ]);

        $room->update(['status' => $data['status']]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Room status updated',
                'room' => $room,
            ]);
        }

        return redirect()->back()->with('status', 'Room status updated');
    }

    protected function syncEquipments(Room $room, Request $request): void
    {
        $ids = $request->input('equipment_ids', []);
        $quantities = $request->input('equipment_quantities', []);

        $attachData = [];
        foreach ($ids as $equipmentId) {
            $quantity = (int) ($quantities[$equipmentId] ?? 1);
            $attachData[$equipmentId] = ['quantity' => max($quantity, 1)];
        }

        $room->equipments()->sync($attachData);
    }
}
