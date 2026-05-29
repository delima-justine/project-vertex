<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            line-height: 1.2;
            color: #000;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .receipt {
            background-color: #fff;
            width: 350px;
            margin: 0 auto;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border: 1px solid #eee;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            background-color: #800000; /* PUP Maroon */
            color: #fff;
            padding: 15px;
            border-bottom: 4px double #fff;
        }
        .header h1 {
            font-size: 22px;
            margin: 5px 0;
            text-transform: uppercase;
            color: #fff;
        }
        .header p {
            font-size: 12px;
            margin: 2px 0;
            color: #fff;
        }
        .info {
            font-size: 13px;
            margin-bottom: 15px;
        }
        .info p {
            margin: 3px 0;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .items-table th {
            text-align: left;
            border-bottom: 1px dashed #000;
            font-size: 13px;
            padding: 5px 0;
        }
        .items-table td {
            font-size: 13px;
            padding: 5px 0;
            vertical-align: top;
        }
        .items-table .qty {
            text-align: right;
            width: 50px;
        }
        .divider {
            border-top: 2px dashed #000;
            margin: 15px 0;
        }
        .footer {
            text-align: center;
            font-size: 11px;
            margin-top: 20px;
        }
        .status-badge {
            display: block;
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            margin: 10px 0;
            border: 2px solid;
            padding: 5px;
            text-transform: uppercase;
        }
        
        /* Status Colors */
        .status-pending { border-color: #6c757d; color: #6c757d; }
        .status-approved { border-color: #28a745; color: #28a745; }
        .status-released { border-color: #007bff; color: #007bff; }
        .status-disapproved { border-color: #800000; color: #800000; }

        .reason {
            font-size: 12px;
            font-style: italic;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <h1>{{ config('identities.system_acronym') }}-{{ config('identities.org_acronym') }}</h1>
            <p>{{ config('identities.system_name') }}</p>
            <p>{{ config('identities.org_branch') }}</p>
        </div>

        <div class="status-badge status-{{ strtolower($status) }}">
            {{ strtoupper($status) }}
        </div>

        <div class="info">
            <p><strong>DATE:</strong> {{ date('Y-m-d H:i') }}</p>
            <p><strong>REQUESTOR:</strong> {{ $user->first_name }} {{ $user->last_name }}</p>
            @if($items->first()->batch_id)
                <p><strong>BATCH ID:</strong> {{ $items->first()->batch_id }}</p>
            @endif
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>ITEM DESCRIPTION</th>
                    <th class="qty">QTY</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>{{ $item->supply->item_desc }}</td>
                        <td class="qty">{{ $item->quantity_req }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="divider"></div>

        <div class="info">
            <p><strong>PURPOSE:</strong></p>
            <p>{{ $items->first()->purpose ?: 'N/A' }}</p>
        </div>

        <div class="reason">
            @if($status === 'pending')
                <p>Your request has been submitted and is currently pending review.</p>
            @elseif($status === 'approved')
                <p>Your request has been approved. Please wait for the items to be released. The process for your requested items is 3 working days.</p>
            @elseif($status === 'released')
                <p>Your items have been released. Thank you!</p>
            @else
                <p>Your request has been reviewed and disapproved by the supply office.</p>
            @endif
        </div>

        <div class="divider"></div>

        <div class="footer">
            <p>This is a system-generated notification.</p>
            <p>SMIS &copy; {{ date('Y') }}</p>
        </div>
    </div>
</body>
</html>
