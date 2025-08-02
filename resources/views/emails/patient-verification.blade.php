@component('mail::message')
# Welcome to MediConnect!

Hello {{ $patient->first_name }},

Thank you for registering with MediConnect! To complete your account setup and start accessing our medical services, please verify your email address.

@component('mail::button', ['url' => $verificationUrl])
Verify Email Address
@endcomponent

**What happens next?**
- Click the button above to verify your email
- Once verified, you can log in to your MediConnect account
- Start booking medical services and connecting with healthcare professionals

**Security Note:**
This verification link will expire in 24 hours for your security. If you didn't create a MediConnect account, please ignore this email.

If you're having trouble clicking the "Verify Email Address" button, copy and paste the URL below into your web browser:

{{ $verificationUrl }}

Thanks,<br>
The {{ config('app.name') }} Team

---
**Need Help?**
If you have any questions, please contact our support team at support@mediconnect.com
@endcomponent
