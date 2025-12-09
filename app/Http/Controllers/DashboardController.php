<?php

namespace App\Http\Controllers;

use App\Models\ReturnRecord;

class DashboardController extends Controller
{
    public function index()
    {
        $returnedItems = ReturnRecord::with(['lostItem', 'foundItem', 'user'])
            ->latest('return_date')
            ->take(6)
            ->get();

        return view('dashboard', compact('returnedItems'));
    }
}
