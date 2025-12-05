<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Registration Details</title>
    <style>
        body { font-family: sans-serif; line-height: 1.5; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #0f4f4b; padding-bottom: 10px; }
        .header img { height: 50px; }
        .header h1 { color: #0f4f4b; margin: 5px 0; }
        .section { margin-bottom: 25px; }
        .section-title { font-size: 18px; font-weight: bold; color: #0f4f4b; border-bottom: 1px solid #ddd; padding-bottom: 5px; margin-bottom: 10px; }
        .row { margin-bottom: 8px; }
        .label { font-weight: bold; width: 150px; display: inline-block; }
        .value { display: inline-block; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 14px; }
        th { background-color: #f2f2f2; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 12px; color: #777; border-top: 1px solid #ddd; padding-top: 10px; }
        .page-number:after { content: counter(page); }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name', 'Kunjachan Missionary Bhavan') }}</h1>
        <p>Registration Details</p>
    </div>

    <div class="section">
        <div class="section-title">User Information</div>
        <div class="row">
            <span class="label">Name:</span>
            <span class="value">{{ $user->name }}</span>
        </div>
        <div class="row">
            <span class="label">Email:</span>
            <span class="value">{{ $user->email }}</span>
        </div>
        <div class="row">
            <span class="label">Role:</span>
            <span class="value">{{ ucfirst(str_replace('_', ' ', $user->role ?? 'User')) }}</span>
        </div>
        <div class="row">
            <span class="label">Registered At:</span>
            <span class="value">{{ $user->created_at->format('F d, Y H:i') }}</span>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Extra Documents</div>
        @if($documents->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>File Name</th>
                        <th>Type</th>
                        <th>Uploaded At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($documents as $doc)
                        <tr>
                            <td>{{ $doc->file_name }}</td>
                            <td>{{ $doc->file_type }}</td>
                            <td>{{ $doc->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No extra documents uploaded.</p>
        @endif
    </div>

    <div class="footer">
        Generated on {{ now()->format('F d, Y H:i') }} | Page <span class="page-number"></span>
    </div>
</body>
</html>
