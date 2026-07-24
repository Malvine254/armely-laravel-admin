<!doctype html>
<html lang="en">
<body style="font-family:Arial,sans-serif;color:#26364a">
<h2>New event invitation request</h2>
<p>A registration request was submitted for <strong>{{ $event_name ?? 'Sovereign Data Clouds with Snowflake' }}</strong>.</p>
<table cellpadding="8" cellspacing="0" style="border-collapse:collapse">
    <tr><th align="left">Name</th><td>{{ $full_name }}</td></tr>
    <tr><th align="left">Work email</th><td>{{ $work_email }}</td></tr>
    <tr><th align="left">Organization</th><td>{{ $organization }}</td></tr>
    <tr><th align="left">Role</th><td>{{ $job_title }}</td></tr>
    <tr><th align="left">Priority</th><td>{{ $compliance_focus ?: 'Not provided' }}</td></tr>
</table>
</body>
</html>
