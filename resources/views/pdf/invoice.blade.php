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
        .company-info {
            float: left;
            width: 50%;
        }
        .invoice-info {
            float: right;
            width: 50%;
            text-align: right;
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
        <div class="company-info">
            <h2>{{ $invoice->company->company_name }}</h2>
            <p>{{ $invoice->company->address }}</p>
            @if($invoice->company->street_number)
                <p>{{ $invoice->company->street_number }}</p>
            @endif
            @if($invoice->company->city)
                <p>{{ $invoice->company->city }}</p>
            @endif
            @if($invoice->company->country)
                <p>{{ $invoice->company->country }}</p>
            @endif
            @if($invoice->company->tax_number)
                <p>Tax Number: {{ $invoice->company->tax_number }}</p>
            @endif
        </div>
        <div class="invoice-info">
            <h1>INVOICE</h1>
            <p>Invoice #: {{ $invoice->invoice_number }}</p>
            <p>Date: {{ $invoice->invoice_date->format('Y-m-d') }}</p>
            <p>Due Date: {{ $invoice->due_date->format('Y-m-d') }}</p>
            <p>Status: {{ ucfirst($invoice->status) }}</p>
        </div>
        <div class="clear"></div>
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