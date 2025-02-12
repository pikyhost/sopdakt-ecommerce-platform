<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User PDF</title>
    <style>
        /* Universal box-sizing to ensure consistent box-model behavior */
        * {
            box-sizing: border-box;
        }
        body {
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif; /* A more standard web-safe font */
            -webkit-font-smoothing: antialiased; /* Smooth fonts in Chrome */
            -moz-osx-font-smoothing: grayscale;  /* Smooth fonts in Firefox */
        }
        .container {
            background-color: #ffffff;
            width: 70%;
            margin: 50px auto;
            padding: 40px;
            border-radius: 20px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        h1 {
            color: #333333;
            font-size: 42px;
            font-weight: 600;
            letter-spacing: 1.5px;
            margin: 0;
            padding-bottom: 10px;
            border-bottom: 2px solid rgb(245, 158, 11);
            display: inline-block;
        }
        .info {
            width: 100%;
            background-color: rgb(245, 158, 11);
            color: #ffffff;
            padding: 30px;
            border-radius: 15px;
        }
        .info-row {
            display: block;
            width: 100%;
            padding: 15px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            font-size: 18px;
            display: inline-block;
            width: 45%;
        }
        .value {
            font-size: 18px;
            display: inline-block;
            width: 50%;
        }
        @media (max-width: 768px) {
            .container {
                width: 90%;
                padding: 20px;
            }

            .info {
                padding: 20px;
            }

            h1 {
                font-size: 32px;
            }

            .label, .value {
                font-size: 16px;
                display: block;
                width: 100%;
            }
        }

        @page {
            margin: 10mm;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>User</h1>
    </div>

    <div class="info">
        <div class="info-row">
            <span class="label">ID:</span>
            <span class="value">{{ $record->id }}</span>
        </div>

        <div class="info-row">
            <span class="label">Name:</span>
            <span class="value">{{ $record->name }}</span>
        </div>

        <div class="info-row">
            <span class="label">Email Address:</span>
            <span class="value">{{ $record->email }}</span>
        </div>

        <div class="info-row">
            <span class="label">Phone Number:</span>
            <span class="value">{{ $record->phone ?? ' No Phone Number Saved' }}</span>
        </div>

        <div class="info-row">
            <span class="label">Roles:</span>
            <span class="value">
                @if($record->roles->isNotEmpty())
                    {{ \Illuminate\Support\Str::headline($record->roles->pluck('name')->join(', ')) }}
                @else
                    No Role Assigned
                @endif
            </span>
        </div>

        <div class="info-row">
            <span class="label">Email Verified At:</span>
            <span class="value">{{ $record->email_verified_at ?? 'Not Verified' }}</span>
        </div>

        <div class="info-row">
            <span class="label">Creation Date:</span>
            <span class="value">{{ $record->created_at->format('d-m-Y H:i:s') }}</span>
        </div>

        <div class="info-row">
            <span class="label">Deleted At:</span>
            <span class="value">{{ $record->deleted_at?->format('d-m-Y H:i:s') ?? 'Not Archived (Active)' }}</span>
        </div>
    </div>
</div>

</body>
</html>
