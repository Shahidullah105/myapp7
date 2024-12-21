@extends('layouts.master')

@section('content')
<div class="container">
    <h2>Invoice List</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Invoice ID</th>
                <th>Customer</th>
                
                <th>VAT</th>
                <th> Payable</th>
                <th>Paid</th>
                <th>Due</th>
               <th>Products</th>
               
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $invoice)
                <tr>
                    <td>{{ $invoice->id }}</td>
                    <td>{{ $invoice->customer->name }}</td>
                    
                    <td>{{ $invoice->vat }}</td>
                    <td>{{ $invoice->payable }}</td>
                    <td>{{ $invoice->paid }}</td>
                    <td>{{ $invoice->due }}</td>

                    <td>

                    <ul>
                        @foreach ($invoice->products as $product)
                        <li>{{ $product->product->name ?? 'Product not found' }} - Qty: {{ $product->qty }} - Price: {{ $product->sale_price }}</li>
                        @endforeach
                    </ul>
                    </td>
                </tr>
            @endforeach
          
        </tbody>
    </table>
</div>
@endsection
