<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    /**
     * Summary of update
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'commission' => ['required', 'numeric']
        ]);

        $commission = Commission::find($id);
        $commission->commission = $request->commission;
        $commission->save();

        return redirect()->route('admin.dashboard');
    }
}
