<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Tarik data customer, hitung jumlah project, dan ambil kontak yang is_primary saja
        $customers = Customer::withCount('projects')
            ->with(['contacts' => function ($query) {
                $query->where('is_primary', true);
            }])
            ->latest()
            ->get();

        return view('customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'address' => 'nullable',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'notes' => 'nullable',
        ]);

        Customer::create($validated);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        // Load relasi projects dan contacts sekaligus
        // Contacts diurutkan berdasarkan is_primary supaya PIC utama muncul di atas
        $customer->load([
            'projects',
            'contacts' => function ($query) {
                $query->orderBy('is_primary', 'desc')->latest();
            }
        ]);

        return view('customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required',
            'address' => 'nullable',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'notes' => 'nullable',
        ]);

        $customer->update($validated);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer berhasil dihapus');
    }
}