<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class RegistrationPDFController extends Controller
{
    public function download(Request $request, User $user = null)
    {
        $currentUser = Auth::user();

        // If no user specified, assume current user
        if (!$user) {
            $user = $currentUser;
        }

        // Access control: User can download their own, Admin/System Admin can download anyone's
        if ($currentUser->id !== $user->id && !$currentUser->hasAnyRole(['admin', 'system_admin'])) {
            abort(403);
        }

        $documents = $user->documents;

        $pdf = Pdf::loadView('pdf.registration', compact('user', 'documents'));

        return $pdf->download('registration-details-' . $user->id . '.pdf');
    }
}
