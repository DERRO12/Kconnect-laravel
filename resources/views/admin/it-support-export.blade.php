<html>
<head><meta charset="UTF-8"><title>IT Tickets Export</title>
<style>th{background:#009ada;color:white;padding:10px;}td{padding:8px;border:1px solid #ddd;}</style>
</head><body>
<h2>IT Support Tickets Report</h2>
<p>Generated: {{ date('Y-m-d H:i:s') }}</p>
<p>Total Records: {{ count($rows) }}</p>
<table border="1">
<thead><tr>
    <th>Ticket #</th><th>Employee</th><th>Department</th><th>Category</th><th>Priority</th>
    <th>Subject</th><th>Status</th><th>Created</th><th>Last Updated</th><th>Comments</th>
</tr></thead><tbody>
@foreach ($rows as $row)
<tr>
    <td>{{ $row['ticket_number'] }}</td>
    <td>{{ $row['employee_name'] }} ({{ $row['employee_code'] }})</td>
    <td>{{ $row['department'] ?? 'N/A' }}</td>
    <td>{{ $row['category'] }}</td>
    <td>{{ $row['priority'] }}</td>
    <td>{{ $row['subject'] }}</td>
    <td>{{ $row['status'] }}</td>
    <td>{{ date('Y-m-d H:i', strtotime($row['created_at'])) }}</td>
    <td>{{ date('Y-m-d H:i', strtotime($row['updated_at'])) }}</td>
    <td>{{ $row['comment_count'] }}</td>
</tr>
@endforeach
</tbody></table></body></html>
