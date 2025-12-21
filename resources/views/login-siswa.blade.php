<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Siswa</title>
    <style>
        body {
            background: linear-gradient(135deg, #4a90e2, #9013fe);
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            color: white;
        }
        .login-container {
            width: 360px;
            padding: 25px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            box-shadow: 0 4px 25px rgba(0,0,0,0.3);
            backdrop-filter: blur(7px);
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        input {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: none;
            margin-bottom: 12px;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #ffdd57;
            border: none;
            color: black;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background: #ffe574;
        }
        .error {
            background: rgba(255,0,0,0.6);
            padding: 8px;
            border-radius: 6px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

<div class="login-container">
    <h2>Login Siswa</h2>

    @if($errors->any())
        <div class="error">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('student.login.submit') }}">
        @csrf
        <input type="text" name="nis" placeholder="NIS" required>
        <input type="text" name="name" placeholder="Nama" required>
        <input type="text" name="class" placeholder="Kelas" required>
        <button type="submit">Masuk</button>
    </form>
</div>

</body>
</html>
