<h1>Edit Customer</h1>

<form action="{{ route('customers.update', $customer) }}" method="POST">
    @csrf
    @method('PUT')

    <p>
        Nama
        <br>
        <input type="text" name="name" value="{{ $customer->name }}">
    </p>

    <p>
        Address
        <br>
        <textarea name="address">{{ $customer->address }}</textarea>
    </p>

    <p>
        Phone
        <br>
        <input type="text" name="phone" value="{{ $customer->phone }}">
    </p>

    <p>
        Email
        <br>
        <input type="email" name="email" value="{{ $customer->email }}">
    </p>

    <p>
        Notes
        <br>
        <textarea name="notes">{{ $customer->notes }}</textarea>
    </p>

    <button type="submit">
        Update
    </button>
</form>