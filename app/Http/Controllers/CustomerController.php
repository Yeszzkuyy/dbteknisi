<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $customers = Customer::withCount('projects')
            ->with(['contacts' => function ($query) {
                $query->where('is_primary', true);
            }])
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = '%'.strtolower($request->string('search')).'%';
                $query->where(function ($q) use ($term) {
                    $q->whereRaw('LOWER(name) LIKE ?', [$term])
                      ->orWhereRaw('LOWER(company) LIKE ?', [$term])
                      ->orWhereRaw('LOWER(email) LIKE ?', [$term]);
                });
            })
            ->latest()
            ->get();

        return $request->ajax()
            ? view('customers._list', compact('customers'))->render()
            : view('customers.index', compact('customers'))
                ->with('search', $request->string('search'));
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
            'company' => 'nullable',
            'address' => 'nullable',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'notes' => 'nullable',
            'status' => 'nullable|in:lead,deal,instalasi,selesai',
        ]);

        // Form customer hanya punya satu kolom nama; company = name supaya tampilan Lead/Monitoring konsisten
        $validated['company'] ??= $validated['name'];

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
            'company' => 'nullable',
            'address' => 'nullable',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'notes' => 'nullable',
            'status' => 'nullable|in:lead,deal,instalasi,selesai',
        ]);

        $validated['company'] ??= $validated['name'];

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