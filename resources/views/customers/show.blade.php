<h1>{{ $customer->name }}</h1>

<p>
    Phone: {{ $customer->phone }}
</p>

<p>
    Email: {{ $customer->email }}
</p>

<p>
    Address: {{ $customer->address }}
</p>

<hr>

<h2>PIC Customer</h2>

<a href="{{ route('customer-contacts.create', $customer) }}">
    Tambah PIC
</a>

<table border="1">

<tr>
    <th>Nama</th>
    <th>Jabatan</th>
    <th>Phone</th>
</tr>

@foreach($customer->contacts as $contact)

<tr>
    <td>{{ $contact->name }}</td>
    <td>{{ $contact->position }}</td>
    <td>{{ $contact->phone }}</td>
</tr>

@endforeach

</table>