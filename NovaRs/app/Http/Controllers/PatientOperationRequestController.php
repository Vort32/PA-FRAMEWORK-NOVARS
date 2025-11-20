<?php

namespace App\Http\Controllers;

use App\Enums\OperationRequestStatus;
use App\Enums\UserRole;
use App\Models\Disease;
use App\Models\OperationRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class PatientOperationRequestController extends Controller
{
    public function create(): View
    {
        Gate::authorize('access-patient');

        return view('patient.operation-requests.create', [
            'diseases' => Disease::orderBy('name')->get(),
            'doctors' => User::query()
                ->where('role', UserRole::Doctor->value)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('access-patient');

        $data = $request->validate([
            'doctor_id' => [
                'required',
                Rule::exists('users', 'id')->where('role', UserRole::Doctor->value),
            ],
            'disease_id' => ['nullable', 'exists:diseases,id'],
            'symptoms_description' => ['required', 'string', 'min:10'],
            'preferred_date' => ['nullable', 'date', 'after_or_equal:today'],
            'referral_letter' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        /** @var \App\Models\User $user */
        $user = $request->user();

        $referralLetterPath = null;
        $referralLetterOriginalName = null;

        if ($request->hasFile('referral_letter')) {
            $file = $request->file('referral_letter');
            $referralLetterOriginalName = $file->getClientOriginalName();
            $referralLetterPath = $file->store('referral-letters', 'public');
        }

        OperationRequest::create([
            'patient_id' => $user->id,
            'doctor_id' => $data['doctor_id'],
            'disease_id' => $data['disease_id'] ?? null,
            'symptoms_description' => $data['symptoms_description'],
            'referral_letter_path' => $referralLetterPath,
            'referral_letter_original_name' => $referralLetterOriginalName,
            'preferred_date' => $data['preferred_date'] ?? null,
            'status' => OperationRequestStatus::Pending,
        ]);

        return redirect()->route('patient.dashboard')->with('status', 'Operation request submitted. Please wait for doctor review.');
    }
}
