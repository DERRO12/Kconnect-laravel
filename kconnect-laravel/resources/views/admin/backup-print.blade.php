    <!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Print Backup Audit Report</title>
    <style>
        body{font-family:Arial;margin:20px;color:#333}
        .print-header{text-align:center;margin-bottom:20px;border-bottom:2px solid #009ada;padding-bottom:15px}
        .print-header h1{color:#009ada;margin:0;font-size:22px}
        .print-header p{color:#666;margin:5px 0 0;font-size:12px}
        .summary-box{display:flex;gap:12px;margin-bottom:20px;justify-content:center;flex-wrap:wrap}
        .summary-item{border:1px solid #ddd;padding:10px 14px;border-radius:8px;text-align:center;min-width:70px}
        .summary-item .val{font-size:20px;font-weight:bold}.summary-item .lbl{font-size:9px;color:#888;text-transform:uppercase}
        .missing-box{background:#fff5f5;border:1px solid #fecaca;border-radius:8px;padding:10px;margin-bottom:15px}
        .missing-box h3{color:#dc2626;margin:0 0 8px;font-size:14px}
        .missing-date{display:inline-block;background:#fee2e2;color:#dc2626;padding:3px 8px;border-radius:12px;font-size:10px;margin:2px;font-weight:600}
        table{width:100%;border-collapse:collapse;font-size:10px}
        th{background:#009ada;color:#fff;padding:8px 6px;text-align:left;font-size:10px}
        td{padding:6px;border:1px solid #ddd}tr:nth-child(even){background:#f9f9f9}
        .badge{padding:2px 8px;border-radius:10px;font-size:9px;font-weight:bold;display:inline-block}
        .badge-green{background:#dcfce7;color:#16a34a}.badge-yellow{background:#fef3c7;color:#d97706}.badge-red{background:#fee2e2;color:#dc2626}
        .footer{text-align:center;margin-top:20px;padding-top:10px;border-top:1px solid #ddd;font-size:10px;color:#888}
        @media print{body{margin:0}@page{margin:1cm}}
    </style></head><body>
    <div class="print-header"><h1>Daily Backup Audit Report</h1><p>Period: {!! ucfirst($period) !!} | Generated: {!! date('F d, Y H:i') !!} | Total: {!! $print_total !!} records</p></div>
    <div class="summary-box">
        <div class="summary-item"><div class="val">{!! $ps['total']??0 !!}</div><div class="lbl">Total</div></div>
        <div class="summary-item"><div class="val" style="color:#16a34a;">{!! $ps['completed']??0 !!}</div><div class="lbl">Completed</div></div>
        <div class="summary-item"><div class="val" style="color:#d97706;">{!! $ps['partial']??0 !!}</div><div class="lbl">Partial</div></div>
        <div class="summary-item"><div class="val" style="color:#dc2626;">{!! $ps['failed']??0 !!}</div><div class="lbl">Failed</div></div>
        <div class="summary-item"><div class="val">{!! $ps['days']??0 !!}</div><div class="lbl">Days Covered</div></div>
        <div class="summary-item"><div class="val" style="color:#dc2626;">{!! count($missing) !!}</div><div class="lbl">Missing Days</div></div>
    </div>
    @if (!empty($missing))<div class="missing-box"><h3>⚠️ Missing Backup Days ({!! date('F Y') !!})</h3>@foreach ($missing as $md)<span class="missing-date">{!! date('M d',strtotime($md)) !!}</span>@endforeach</div>@endif
    <table><tr><th>#</th><th>Date</th><th>Status</th><th>Companies</th><th>Recorded By</th><th>Ticket</th><th>Notes</th></tr>
    @php $i=1; @endphp @foreach ($print_result as $r) @php $comps=json_decode($r['companies']??'[]',true)?:[]; $sc=$r['status']=='Completed'?'badge-green':($r['status']=='Partial'?'badge-yellow':'badge-red'); @endphp
        <tr><td>{!! $i++ !!}</td><td>{!! date('M d, Y',strtotime($r['backup_date'])) !!}</td><td><span class="badge {!! $sc !!}">{!! $r['status'] !!}</span></td><td>{!! implode(', ',$comps) !!}</td><td>{{ $r['full_name'] }}</td><td>{!! $r['ticket_number']??'-' !!}</td><td>{{ substr($r['notes']??'',0,40) }}</td></tr>
    @endforeach</table>
    <div class="footer"><p>K-Connect Intranet Portal | Computer-generated audit report</p><p>Printed by: {{ $admin_name }} | {!! date('F d, Y') !!}</p></div>
    <script>window.onload=function(){window.print();}</script></body></html>