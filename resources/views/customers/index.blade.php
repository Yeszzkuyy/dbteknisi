<h1>Customers</h1>

<a href="{{ route('customers.create') }}">
    Tambah Customer
</a>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Nama</th>
        <th>Phone</th>
        <th>Email</th>
        <th>Action</th>
    </tr>

    @foreach($customers as $customer)
    <tr>
        <td>{{ $customer->id }}</td>
        <td>{{ $customer->name }}</td>
        <td>{{ $customer->phone }}</td>
        <td>{{ $customer->email }}</td>
        <td>
            <a href="{{ route('customers.show', $customer) }}"> 
                Detail
            </a>
            <a href="{{ route('customers.edit', $customer) }}">
                Edit
            </a>
            <form action="{{ route('customers.destroy', $customer) }}"
                  method="POST"
                  style="display:inline;">          
                @csrf
                @method('DELETE')           
                <button type="submit">
                    Hapus
                </button>           
            </form>
        </td>
    </tr>
    @endforeach
</table>