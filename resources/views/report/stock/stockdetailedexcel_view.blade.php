<table border="1" cellspacing="">
    <tr>
        <td bgcolor="#ff6666">Expired</td>
        <td bgcolor="#ffa500">Less than 1 month</td>
        <td bgcolor="#FFFF00">Less than 6 months</td>
        <td bgcolor="#5cd65c">More than 6 months</td>
    </tr>
</table>

<table border="1" borderColor="#dadcdd">
    <thead>
        <tr>
            <th colspan="6">Stock Detailed Report</th>
            <th colspan="9">Generated On {{ date('Y-m-d') }}</th>
        </tr>

        <tr>
            <th style="background-color: #FFaeaaaa">Sl.No</th>
            <th style="background-color: #FFaeaaaa">Location</th>
            <th style="background-color: #FFaeaaaa">Bin</th>
            <th style="background-color: #FFaeaaaa">Serial Number</th>
            <th style="background-color: #FFaeaaaa">Quantity</th>
            <th style="background-color: #FFaeaaaa">UOM</th>
            <th style="background-color: #FFaeaaaa">Item Name</th>
            <th style="background-color: #FFaeaaaa">SPQ Quantity</th>
            <th style="background-color: #FFaeaaaa">Price</th>
            <th style="background-color: #FFaeaaaa">Total Price</th>
            <th style="background-color: #FFaeaaaa">Manufacture Date</th>
            <th style="background-color: #FFaeaaaa">Expiry Date</th>
            <th style="background-color: #FFaeaaaa">No. of Days for Expiry</th>
            <th style="background-color: #FFaeaaaa">Category</th>
            <th style="background-color: #FFaeaaaa">Stock In Time</th>
        </tr>
    </thead>

    <tbody>
        @php
        $i = 0;
        @endphp

        @foreach ($stock as $st)
        @php
        $days = $st['days_before_expiry'] ?? null;
     
        if (is_null($days)) {
        $bgColor = '#e6e6e6'; // grey - no data
        } elseif ($days < 0) {
            $bgColor='#ff6666' ; // red - expired
            } elseif ($days <=30 && $days> 0) {
            $bgColor = '#ffa500'; // orange - less than 1 month
            } elseif ($days <= 180 && $days> 30) {
                $bgColor = '#FFFF00'; // yellow - less than 6 months
                } elseif ($days > 180) {
                $bgColor = '#5cd65c'; // green - more than 6 months
                } elseif ($days == 0) {
                $bgColor = '#ff6666'; // exactly today → expired
                }
                @endphp


                <tr>
                    <td align="center" style="background-color: {{ $bgColor }}">{{ ++$i }}</td>
                    <td align="left" style="background-color: {{ $bgColor }}">{{ $st['location_name'] ?? '-' }}</td>
                    <td align="left" style="background-color: {{ $bgColor }}">{{ $st['bin_name'] ?? '-' }}</td>
                    <td align="left" style="background-color: {{ $bgColor }}">{{ $st['serial'] ?? '-' }}</td>
                    <td align="left" style="background-color: {{ $bgColor }}">{{ $st['net_weight'] ?? 0 }}</td>
                    <td align="left" style="background-color: {{ $bgColor }}">{{ $st['uom_name'] ?? '-' }}</td>
                    <td align="left" style="background-color: {{ $bgColor }}">{{ $st['item_name'] ?? '-' }}</td>
                    <td align="left" style="background-color: {{ $bgColor }}">{{ $st['spq_quantity'] ?? 0 }}</td>
                    <td align="left" style="background-color: {{ $bgColor }}">{{ $st['price'] ?? 0 }}</td>
                    <td align="left" style="background-color: {{ $bgColor }}">{{ $st['total_price'] ?? 0 }}</td>
                    <td align="left" style="background-color: {{ $bgColor }}">{{ $st['date_of_manufacture'] ?? '-' }}</td>
                    <td align="left" style="background-color: {{ $bgColor }}">{{ $st['best_before_date'] ?? '-' }}</td>
                    <td align="left" style="background-color: {{ $bgColor }}">{{ $st['days_before_expiry'] ?? '-' }}</td>
                    <td align="left" style="background-color: {{ $bgColor }}">{{ $st['category_name'] ?? '-' }}</td>
                    <td align="left" style="background-color: {{ $bgColor }}">{{ $st['scan_time'] ?? '-' }}</td>
                </tr>
                @endforeach

                @if($i == 0)
                <tr>
                    <td align="center" colspan="15" style="color: red">No Record Found</td>
                </tr>
                @endif
    </tbody>
</table>