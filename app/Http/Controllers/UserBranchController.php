<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class UserBranchController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            $branches = Branch::where('is_active', true)
                ->select('id', 'name', 'type')
                ->get();
        } else {
            $branches = $user->branches()
                ->where('is_active', true)
                ->select('branches.id', 'branches.name', 'branches.type')
                ->get();
        }

        return response()->json($branches);
    }
}
