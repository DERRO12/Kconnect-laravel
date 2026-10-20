    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Print Backup Report</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; color: #333; }
            .print-header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #009ada; padding-bottom: 15px; }
            .print-header h1 { color: #009ada; margin: 0; font-size: 24px; }
            .print-header p { color: #666; margin: 5px 0 0; }
            .summary-box { display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap; justify-content: center; }
            .summary-item { border: 1px solid #ddd; padding: 10px 15px; border-radius: 8px; text-align: center; min-width: 100px; background: #f8fafc; }
            .summary-item .val { font-size: 22px; font-weight: bold; }
            .summary-item .lbl { font-size: 10px; color: #888; text-transform: uppercase; }
            .filter-info { background: #f0f9ff; padding: 10px 15px; border-radius: 8px; margin-bottom: 15px; border-left: 3px solid #009ada; }
            table { width: 100%; border-collapse: collapse; font-size: 11px; }
            th { background: #009ada; color: white; padding: 8px 6px; text-align: left; font-size: 10px; text-transform: uppercase; }
            td { padding: 6px; border: 1px solid #ddd; }
            tr:nth-child(even) { background: #f9f9f9; }
            .badge { padding: 2px 8px; border-radius: 10px; font-size: 9px; font-weight: bold; display: inline-block; }
            .badge-green { background: #dcfce7; color: #16a34a; }
            .badge-yellow { background: #fef3c7; color: #d97706; }
            .badge-red { background: #fee2e2; color: #dc2626; }
            .footer { margin-top: 20px; padding-top: 10px; border-top: 1px solid #ddd; }
            .signature-section { display: flex; justify-content: space-between; margin-top: 30px; padding-top: 20px; }
            .signature-box { text-align: center; flex: 1; }
            .signature-box hr { width: 80%; margin: 5px auto; }
            .signature-box .label { font-size: 10px; color: #888; }
            @media print { body { margin: 0; } @page { margin: 1cm; } .no-print { display: none; } }
        </style>
    </head>
    <body>
        <div class="print-header">
            <h1>IT Backup Report</h1>
            <p>Period: {!! date('M d, Y', strtotime($date_from)) !!} to {!! date('M d, Y', strtotime($date_to)) !!}</p>
            <p>Generated: {!! date('F d, Y H:i') !!}</p>
        </div>
        
        <div class="filter-info">
            <strong>Date Range:</strong> {!! $date_from !!} to {!! $date_to !!} | 
            <strong>Total Records:</strong> {!! $print_total !!}
        </div>
        
        <div class="summary-box">
            <div class="summary-item"><div class="val">{!! $print_stats['total'] ?? 0 !!}</div><div class="lbl">Total</div></div>
            <div class="summary-item"><div class="val" style="color:#16a34a;">{!! $print_stats['completed'] ?? 0 !!}</div><div class="lbl">Completed</div></div>
            <div class="summary-item"><div class="val" style="color:#d97706;">{!! $print_stats['partial'] ?? 0 !!}</div><div class="lbl">Partial</div></div>
            <div class="summary-item"><div class="val" style="color:#dc2626;">{!! $print_stats['failed'] ?? 0 !!}</div><div class="lbl">Failed</div></div>
            <div class="summary-item"><div class="val">{!! $print_stats['unique_staff'] ?? 0 !!}</div><div class="lbl">Staff</div></div>
        </div>
        
        <table>
            <thead><tr>
                <th>#</th><th>Date</th><th>Staff</th><th>Companies</th><th>Status</th><th>Notes</th>
            </tr></thead>
            <tbody>
                @php $i = 1; @endphp
                @foreach ($print_result as $row)
                    @php $statusColors = ['Completed'=>'badge-green','Partial'=>'badge-yellow','Failed'=>'badge-red']; @endphp
                    <tr>
                        <td>{!! $i++ !!}</td>
                        <td>{!! date('M d, Y', strtotime($row['backup_date'])) !!}</td>
                        <td>{{ $row['employee_name'] ?? 'Unknown' }}</td>
                        <td>{{ $row['companies'] }}</td>
                        <td><span class="badge {!! $statusColors[$row['status']] ?? 'badge-green' !!}">{!! $row['status'] !!}</span></td>
                        <td>{{ substr($row['notes'] ?? '', 0, 50) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="footer">
            <div class="signature-section">
                <div class="signature-box"><hr><p class="label">Checked By</p></div>
                <div class="signature-box"><hr><p class="label">Approved By</p></div>
                <div class="signature-box"><hr><p class="label">Date</p></div>
            </div>
            <p style="text-align:center;font-size:10px;color:#888;margin-top:10px;">This is a computer-generated report. | K-Connect Intranet Portal</p>
        </div>
        
        <div class="no-print" style="text-align:center;margin-top:20px;">
            <button onclick="window.print();" style="padding:10px 30px;background:#009ada;color:white;border:none;border-radius:8px;font-size:14px;cursor:pointer;">
                Print / Save as PDF
            </button>
        </div>
    </body>
    </html>