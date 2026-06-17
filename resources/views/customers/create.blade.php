<h1>Tambah Customer</h1>

<form action="{{ route('customers.store') }}" method="POST">
    @csrf

    <p>
        Nama Perusahaan
        <br>
        <input type="text" name="name">
    </p>

    <p>
        Address
        <br>
        <textarea name="address"></textarea>
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

    <p>
        Notes
        <br>
        <textarea name="notes"></textarea>
    </p>

    <button type="submit">
        Simpan
    </button>
</form>