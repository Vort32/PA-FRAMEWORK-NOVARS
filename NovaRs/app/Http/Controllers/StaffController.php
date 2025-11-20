<?php

namespace App\Http\Controllers;

use App\Enums\RoomStatus;
use App\Models\Operation;
use App\Models\Room;
use App\Models\Staff;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function dashboard(): View
    {
        Gate::authorize('access-staff');

        $todayOperations = Operation::query()
            ->whereDate('scheduled_at', Carbon::today())
            ->with(['patient', 'doctor', 'room'])
            ->orderBy('scheduled_at')
            ->get();

        $rooms = Room::with('equipments')->orderBy('name')->get();

        return view('staff.dashboard', compact('todayOperations', 'rooms'));
    }

    public function index(): View
    {
        Gate::authorize('access-admin');

        $staff = Staff::with('user')->paginate(10);

        return view('admin.staff.index', compact('staff'));
    }

    public function create(): View
    {
        Gate::authorize('access-admin');

        return view('admin.staff.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('access-admin');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['nullable', 'string', 'min:8'],
            'position' => ['required', 'string', 'max:255'],
            'shift_type' => ['required', 'string', 'max:50'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => 'staff',
            'password' => Hash::make($data['password'] ?? 'password'),
        ]);

        Staff::create([
            'user_id' => $user->id,
            'position' => $data['position'],
            'shift_type' => $data['shift_type'],
        ]);

        return redirect()->route('admin.staff.index')->with('status', 'Staff member created');
    }

    public function edit(Staff $staff): View
    {
        Gate::authorize('access-admin');

        $staff->load('user');

        return view('admin.staff.edit', compact('staff'));
    }

    public function update(Request $request, Staff $staff): RedirectResponse
    {
        Gate::authorize('access-admin');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$staff->user_id],
            'password' => ['nullable', 'string', 'min:8'],
            'position' => ['required', 'string', 'max:255'],
            'shift_type' => ['required', 'string', 'max:50'],
        ]);

        $staff->user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => empty($data['password']) ? $staff->user->password : Hash::make($data['password']),
        ]);

        $staff->update([
            'position' => $data['position'],
            'shift_type' => $data['shift_type'],
        ]);

        return redirect()->route('admin.staff.index')->with('status', 'Staff member updated');
    }

    public function destroy(Staff $staff): RedirectResponse
    {
        Gate::authorize('access-admin');

        $staff->user()->delete();
        $staff->delete();

        return redirect()->route('admin.staff.index')->with('status', 'Staff member deleted');
    }
}
