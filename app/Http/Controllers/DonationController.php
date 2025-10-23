<?php
namespace App\Http\Controllers;
use App\Models\Cause;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    // store donation for a cause (route model binding)
    public function store(Request $request, Cause $cause)
    {

        // Block donations for completed/canceled
        if (in_array($cause->status, ['completed','canceled'], true)) {
            return back()->with('error', 'Donations are closed for this cause.');
        }

        $data = $request->validate([
            'donor_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'message'    => 'nullable|string|max:1000',

        ]);

        $cause->donations()->create([
            'donor_name' => $data['donor_name'],
            'amount' => $data['amount'],
            'message'    => $data['message'] ?? null,
            'date' => now()->toDateString(),
        ]);

        if (auth()->check()) {
            $data['donor_name'] = auth()->user()->name;
        }

        return redirect()->route('causes.show', $cause)
            ->with('success','Thank you! Your donation was recorded.');
    }
}
