<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reset Password Email</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif">

    <h3>Hey, {{ $mailData['user']->name }}</h3>

    <h2>You have requested to change password</h2>

    <p>Please click on the link give below to reset password</p>

    <a href="{{ route('front.resetPassword', $mailData['token']) }}">Click Here</a>

    <h4>Thanks...</h4>
</body>
</html>