<?php
namespace App\Http\Controllers;
use App\Models\Cause;
use Illuminate\Http\Request;

class CauseController extends Controller
{
    public function index()
    {
        $causes = Cause::withCount('donations')
            ->orderBy('created_at','desc')
            ->paginate(10);

        return view('causes.index', compact('causes'));
    }






    public function show(Cause $cause)
    {
        $cause->load('donations');
        return view('causes.show', compact('cause'));
    }






}
