<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\OperationRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OperationRequestReferralLetterController extends Controller
{
    public function __invoke(OperationRequest $operationRequest): StreamedResponse
    {
        $user = Auth::user();

        abort_unless($user, 401);

        $authorized = $operationRequest->patient_id === $user->id
            || ($operationRequest->doctor_id === $user->id && $user->role === UserRole::Doctor)
            || in_array($user->role, [UserRole::Admin, UserRole::Staff], true);

        abort_unless($authorized, 403);

        abort_unless($operationRequest->referral_letter_path && Storage::disk('public')->exists($operationRequest->referral_letter_path), 404);

        $filename = $operationRequest->referral_letter_original_name ?: basename($operationRequest->referral_letter_path);

        return Storage::disk('public')->response($operationRequest->referral_letter_path, $filename);
    }
}
