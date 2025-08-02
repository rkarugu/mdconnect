<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification Error - MediConnect</title>
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
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
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
        .error-icon {
            width: 80px;
            height: 80px;
            background: #dc3545;
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
        .help-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: left;
        }
        .help-info h3 {
            margin: 0 0 15px 0;
            color: #333;
        }
        .help-info ul {
            margin: 0;
            padding-left: 20px;
        }
        .help-info li {
            margin: 8px 0;
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
            <p>Email Verification Issue</p>
        </div>
        
        <div class="content">
            <div class="error-icon">
                ✗
            </div>
            
            <div class="message">
                {{ $message }}
            </div>
            
            <div class="help-info">
                <h3>What can you do?</h3>
                <ul>
                    <li>Check your email for a new verification link</li>
                    <li>Request a new verification email from the app</li>
                    <li>Make sure you're clicking the most recent verification link</li>
                    <li>Contact our support team if the problem persists</li>
                </ul>
            </div>
            
            <div class="actions">
                <a href="#" class="btn" onclick="window.close();">Close Window</a>
                <a href="mailto:support@mediconnect.com" class="btn btn-secondary">Contact Support</a>
            </div>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} MediConnect. All rights reserved.</p>
            <p>Need help? Contact us at <a href="mailto:support@mediconnect.com">support@mediconnect.com</a></p>
        </div>
    </div>
    
    <script>
        // Auto-close window after 15 seconds
        setTimeout(function() {
            window.close();
        }, 15000);
    </script>
</body>
</html>
