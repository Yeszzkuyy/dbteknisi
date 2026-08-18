<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\PurchaseOrder;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminService
{
    public function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $last = Invoice::withTrashed()->whereYear('created_at', $year)
            ->orderBy('id', 'desc')->value('invoice_number');
        $seq = $last ? (int) Str::afterLast($last, '-') + 1 : 1;
        return "INV-{$year}-" . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function generatePoNumber(): string
    {
        $year = date('Y');
        $last = PurchaseOrder::withTrashed()->whereYear('created_at', $year)
            ->orderBy('id', 'desc')->value('po_number');
        $seq = $last ? (int) Str::afterLast($last, '-') + 1 : 1;
        return "PO-{$year}-" . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function getInvoices(array $filters = [])
    {
        $query = Invoice::with(['customer', 'project', 'payments']);

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('invoice_number', 'like', '%' . $filters['search'] . '%')
                  ->orWhereHas('customer', fn($c) => $c->where('name', 'like', '%' . $filters['search'] . '%'));
            });
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest('issue_date')->paginate(15);
    }

    public function createInvoice(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            $data['invoice_number'] = $this->generateInvoiceNumber();
            $data['created_by'] = auth()->id();
            return Invoice::create($data);
        });
    }

    public function updateInvoice(Invoice $invoice, array $data): Invoice
    {
        $invoice->update($data);
        return $invoice;
    }

    public function deleteInvoice(Invoice $invoice): void
    {
        $invoice->delete();
    }

    public function getPurchaseOrders(array $filters = [])
    {
        $query = PurchaseOrder::with(['customer', 'project']);

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('po_number', 'like', '%' . $filters['search'] . '%')
                  ->orWhereHas('customer', fn($c) => $c->where('name', 'like', '%' . $filters['search'] . '%'));
            });
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest('issue_date')->paginate(15);
    }

    public function createPurchaseOrder(array $data): PurchaseOrder
    {
        return DB::transaction(function () use ($data) {
            $data['po_number'] = $this->generatePoNumber();
            $data['created_by'] = auth()->id();
            return PurchaseOrder::create($data);
        });
    }

    public function updatePurchaseOrder(PurchaseOrder $po, array $data): PurchaseOrder
    {
        $po->update($data);
        return $po;
    }

    public function deletePurchaseOrder(PurchaseOrder $po): void
    {
        $po->delete();
    }

    public function getPayments(array $filters = [])
    {
        $query = Payment::with(['invoice.customer', 'creator']);

        if (!empty($filters['search'])) {
            $query->whereHas('invoice', function ($q) use ($filters) {
                $q->where('invoice_number', 'like', '%' . $filters['search'] . '%')
                  ->orWhereHas('customer', fn($c) => $c->where('name', 'like', '%' . $filters['search'] . '%'));
            });
        }

        return $query->latest('payment_date')->paginate(15);
    }

    public function createPayment(array $data): Payment
    {
        return DB::transaction(function () use ($data) {
            $data['created_by'] = auth()->id();
            $payment = Payment::create($data);

            // Update invoice status to paid
            $payment->invoice->update(['status' => 'paid']);

            return $payment;
        });
    }

    public function deletePayment(Payment $payment): void
    {
        DB::transaction(function () use ($payment) {
            $invoice = $payment->invoice;
            $payment->delete();

            // Revert invoice to unpaid if no other payments
            if ($invoice->payments()->count() === 0) {
                $invoice->update(['status' => 'unpaid']);
            }
        });
    }
}
