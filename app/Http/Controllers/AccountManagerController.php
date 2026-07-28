<?php

namespace App\Http\Controllers;

use App\Models\AccountManager;
use Illuminate\Http\Request;

class AccountManagerController extends Controller
{

    public function index()
    {
        $accountManagers = AccountManager::latest()->get();
        return view('settings.account-managers.index', compact('accountManagers'));
    }

    public function create()
    {
        return view('settings.account-managers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        AccountManager::create($validated);

        return redirect()
            ->route('account-managers.index')
            ->with('success', 'Account Manager berhasil ditambahkan.');
    }

    public function edit(AccountManager $accountManager)
    {
        return view('settings.account-managers.edit', compact('accountManager'));
    }

    public function update(Request $request, AccountManager $accountManager)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $accountManager->update($validated);

        return redirect()
            ->route('account-managers.index')
            ->with('success', 'Account Manager berhasil diupdate.');
    }

    public function destroy(AccountManager $accountManager)
    {
        $accountManager->delete();
        return redirect()
            ->route('account-managers.index')
            ->with('success', 'Account Manager berhasil dihapus.');
    }
}