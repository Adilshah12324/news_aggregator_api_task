<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <!-- resources/views/emails/reset_password.blade.php -->

<p>You are receiving this email because we received a password reset request for your account.</p>
<p>
    Click the link below to reset your password:
    <br>
    <a href="{{ $resetLink }}" target="_blank">{{ $resetLink }}</a>
</p>
<p>If you did not request a password reset, no further action is required.</p>

</body>
</html>