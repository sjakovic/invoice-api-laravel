<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eee;
        }
        .issuer-info {
            float: left;
            width: 50%;
        }
        .client-info {
            float: right;
            width: 50%;
            text-align: right;
        }
        .invoice-info {
            clear: both;
            text-align: center;
            margin: 20px 0;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .clear {
            clear: both;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f5f5f5;
        }
        .totals {
            float: right;
            width: 300px;
        }
        .totals table {
            width: 100%;
        }
        .totals td {
            border: none;
            padding: 5px 10px;
        }
        .totals tr:last-child td {
            border-top: 2px solid #333;
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #eee;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="issuer-info">
            <h2>From:</h2>
            <h3>{{ $invoice->issuer_company_name }}</h3>
            <p>{{ $invoice->issuer_address }}</p>
            @if($invoice->issuer_street_number)
                <p>{{ $invoice->issuer_street_number }}</p>
            @endif
            @if($invoice->issuer_city)
                <p>{{ $invoice->issuer_city }}</p>
            @endif
            @if($invoice->issuer_country)
                <p>{{ $invoice->issuer_country }}</p>
            @endif
            @if($invoice->issuer_tax_number)
                <p>Tax Number: {{ $invoice->issuer_tax_number }}</p>
            @endif
        </div>
        <div class="client-info">
            <h2>To:</h2>
            <h3>{{ $invoice->client_company_name }}</h3>
            <p>{{ $invoice->client_address }}</p>
            @if($invoice->client_street_number)
                <p>{{ $invoice->client_street_number }}</p>
            @endif
            @if($invoice->client_city)
                <p>{{ $invoice->client_city }}</p>
            @endif
            @if($invoice->client_country)
                <p>{{ $invoice->client_country }}</p>
            @endif
            @if($invoice->client_tax_number)
                <p>Tax Number: {{ $invoice->client_tax_number }}</p>
            @endif
        </div>
        <div class="clear"></div>
    </div>

    <div class="invoice-info">
        <h1>INVOICE</h1>
        <p>Invoice #: {{ $invoice->invoice_number }}</p>
        <p>Date: {{ $invoice->invoice_date->format('Y-m-d') }}</p>
        <p>Due Date: {{ $invoice->due_date->format('Y-m-d') }}</p>
        <p>Status: {{ ucfirst($invoice->status) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->description }}</td>
                <td>{{ number_format($item->quantity, 2) }}</td>
                <td>{{ number_format($item->price, 2) }} {{ $invoice->currency }}</td>
                <td>{{ number_format($item->total, 2) }} {{ $invoice->currency }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td>Subtotal:</td>
                <td>{{ number_format($invoice->amount, 2) }} {{ $invoice->currency }}</td>
            </tr>
            <tr>
                <td>Tax ({{ $invoice->tax }}%):</td>
                <td>{{ number_format($invoice->amount * ($invoice->tax / 100), 2) }} {{ $invoice->currency }}</td>
            </tr>
            @if($invoice->discount > 0)
            <tr>
                <td>Discount:</td>
                <td>-{{ number_format($invoice->discount, 2) }} {{ $invoice->currency }}</td>
            </tr>
            @endif
            <tr>
                <td>Total:</td>
                <td>{{ number_format($invoice->total, 2) }} {{ $invoice->currency }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>This is a computer-generated document. No signature is required.</p>
        <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html> 