<?php

namespace App\Http\Controllers;

use App\Models\CustomerContact;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerContactController extends Controller
{
    /**
     * Show the form for creating a new contact.
     */
    public function create(Customer $customer)
    {
        return view('customer_contacts.create', compact('customer'));
    }

    /**
     * Store a newly created contact.
     */
    public function store(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'is_primary' => 'nullable|boolean',
        ]);

        // Jika di-set sebagai primary, hapus primary lama
        if ($request->has('is_primary') && $request->is_primary) {
            $customer->contacts()->update(['is_primary' => false]);
        }

        $customer->contacts()->create($validated);

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', 'PIC berhasil ditambahkan.');
    }

    /**
     * Show the form for editing a contact.
     */
    public function edit(CustomerContact $customerContact)
    {
        $customer = $customerContact->customer;
        return view('customer_contacts.edit', compact('customerContact', 'customer'));
    }

    /**
     * Update the specified contact.
     */
    public function update(Request $request, CustomerContact $customerContact)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'is_primary' => 'nullable|boolean',
        ]);

        $customer = $customerContact->customer;

        // Jika di-set sebagai primary, hapus primary lama
        if ($request->has('is_primary') && $request->is_primary) {
            $customer->contacts()->where('id', '!=', $customerContact->id)->update(['is_primary' => false]);
        }

        $customerContact->update($validated);

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', 'PIC berhasil diupdate.');
    }

    /**
     * Remove the specified contact.
     */
    public function destroy(CustomerContact $customerContact)
    {
        $customer = $customerContact->customer;
        $customerContact->delete();

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', 'PIC berhasil dihapus.');
    }
}