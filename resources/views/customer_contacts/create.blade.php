<h1>Tambah PIC Customer</h1>

<h3>{{ $customer->name }}</h3>

<form
    action="{{ route('customer-contacts.store', $customer) }}"
    method="POST">

    @csrf

    <p>
        Nama
        <br>
        <input type="text" name="name">
    </p>

    <p>
        Jabatan
        <br>
        <input type="text" name="position">
    </p>

    <p>
        Phone
        <br>
        <input type="text" name="phone">
    </p>

    <p>
        Email
        <br>
        <input type="email" name="email">
    </p>

    <button type="submit">
        Simpan
    </button>

</form>