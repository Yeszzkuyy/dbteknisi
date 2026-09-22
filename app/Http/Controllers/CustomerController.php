<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Lead;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Customer::class);

        $customers = Customer::withCount('projects')
            ->with(['contacts' => function ($query) {
                $query->orderByDesc('is_primary')->latest();
            }])
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->whereLike(['name', 'company', 'email'], $request->string('search'));
            })
            ->when($request->filled('pt_group'), fn ($query) => $query->where('pt_group', $request->string('pt_group')))
            ->latest()
            ->get();

        $ptGroups = Lead::PT_GROUPS;

        return $request->ajax()
            ? view('customers._list', compact('customers'))->render()
            : view('customers.index', compact('customers', 'ptGroups'))
                ->with('search', $request->string('search'))
                ->with('ptGroup', $request->string('pt_group'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Customer::class);

        return view('customers.create', ['ptGroups' => Lead::PT_GROUPS]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Customer::class);

        $validated = $request->validate([
            'name' => 'required',
            'contact_person' => 'nullable',
            'company' => 'nullable',
            'pt_group' => 'required|in:'.implode(',', Lead::PT_GROUPS),
            'address' => 'nullable',
            'phone' => 'nullable',
            'whatsapp' => 'nullable',
            'email' => 'nullable|email',
            'notes' => 'nullable',
            'status' => 'nullable|in:lead,deal,instalasi,selesai',
        ]);

        // Form customer hanya punya satu kolom nama; company = name supaya tampilan Lead/Monitoring konsisten
        $validated['company'] ??= $validated['name'];

        Customer::create($validated);

        return redirect()
            ->route('customers.index')
            ->with('success', __('Customer berhasil ditambahkan'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        $this->authorize('view', $customer);

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
        $this->authorize('update', $customer);

        return view('customers.edit', ['customer' => $customer, 'ptGroups' => Lead::PT_GROUPS]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $this->authorize('update', $customer);

        $validated = $request->validate([
            'name' => 'required',
            'contact_person' => 'nullable',
            'company' => 'nullable',
            'pt_group' => 'required|in:'.implode(',', Lead::PT_GROUPS),
            'address' => 'nullable',
            'phone' => 'nullable',
            'whatsapp' => 'nullable',
            'email' => 'nullable|email',
            'notes' => 'nullable',
            'status' => 'nullable|in:lead,deal,instalasi,selesai',
        ]);

        $validated['company'] ??= $validated['name'];

        $customer->update($validated);

        return redirect()
            ->route('customers.index')
            ->with('success', __('Customer berhasil diupdate'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $this->authorize('delete', $customer);

        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', __('Customer berhasil dihapus'));
    }
}