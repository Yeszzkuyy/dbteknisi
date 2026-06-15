<h1>Customers</h1>

<a href="/customers/create">
    Tambah Customer
</a>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Nama</th>
        <th>Phone</th>
        <th>Email</th>
    </tr>

    @foreach($customers as $customer)
    <tr>
        <td>{{ $customer->id }}</td>
        <td>{{ $customer->name }}</td>
        <td>{{ $customer->phone }}</td>
        <td>{{ $customer->email }}</td>
    </tr>
    @endforeach
</table>