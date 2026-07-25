<div class="row bg-light text-dark border p-3">
    <div class="col-md-12">
        {{-- <p>
            Name: {{ $customer->name }}<br>
            Email: {{ $customer->email }}<br>
            Ph#: {{ $customer->phone }}<br>
            Address: {{ $customer->address }}<br>
        </p> --}}

        <table class="table table-sm w-100">
            <tbody>
                <tr>
                    <td class="w-100" colspan="2"><b>Customer Info</b></td>
                </tr>
                <tr>
                    <td class="w-50"><b>Name: </b>{{ $customer->name ?? "N/A" }}</td>
                    <td class="w-50"><b>Phone: </b>{{ $customer->phone ?? "N/A" }}</td>
                </tr>
                <tr>
                    <td class="w-50"><b>Email: </b>{{ $customer->email ?? "N/A" }}</td>
                    <td class="w-50"><b>Address: </b>{{ $customer->address ?? "N/A" }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>