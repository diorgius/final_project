<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use Illuminate\Http\Request;

/**
 * Summary of CommissionController
 */
class CommissionController extends Controller
{
    /**
     * Обновляем комиссию
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'commission' => ['required', 'numeric']
        ]);

        $commission = Commission::findOrFail($id);
        $commission->commission = $request->commission;
        $commission->save();

        return redirect()->route('admin.main');
    }
}
