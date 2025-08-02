<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verified - MediConnect</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .content {
            padding: 40px 30px;
            text-align: center;
        }
        .success-icon {
            width: 80px;
            height: 80px;
            background: #28a745;
            border-radius: 50%;
            margin: 0 auto 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            color: white;
        }
        .message {
            font-size: 18px;
            margin-bottom: 30px;
            color: #555;
        }
        .patient-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .patient-info h3 {
            margin: 0 0 10px 0;
            color: #333;
        }
        .patient-info p {
            margin: 5px 0;
            color: #666;
        }
        .actions {
            margin-top: 30px;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 0 10px;
            transition: transform 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
            text-decoration: none;
            color: white;
        }
        .btn-secondary {
            background: #6c757d;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>MediConnect</h1>
            <p>Your Healthcare Connection Platform</p>
        </div>
        
        <div class="content">
            <div class="success-icon">
                ✓
            </div>
            
            <div class="message">
                {{ $message }}
            </div>
            
            @if(isset($patient))
            <div class="patient-info">
                <h3>Account Details</h3>
                <p><strong>Name:</strong> {{ $patient->full_name }}</p>
                <p><strong>Email:</strong> {{ $patient->email }}</p>
                <p><strong>Verified:</strong> {{ $patient->email_verified_at->format('M d, Y \a\t g:i A') }}</p>
            </div>
            @endif
            
            <div class="actions">
                <a href="#" class="btn" onclick="window.close();">Close Window</a>
                <a href="mailto:support@mediconnect.com" class="btn btn-secondary">Contact Support</a>
            </div>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} MediConnect. All rights reserved.</p>
            <p>If you have any questions, please contact us at <a href="mailto:support@mediconnect.com">support@mediconnect.com</a></p>
        </div>
    </div>
    
    <script>
        // Auto-close window after 10 seconds
        setTimeout(function() {
            window.close();
        }, 10000);
    </script>
</body>
</html>
