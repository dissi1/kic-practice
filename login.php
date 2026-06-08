<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=kic_practice;charset=utf8', 'root', '');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE login = ?");
    $stmt->execute([$_POST['login']]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($_POST['password'], $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['login'] = $user['login'];
        header('Location: index.php');
        exit;
    } else {
        $error = 'Неверный логин или пароль';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - КИЦ пгт. Октябрьское</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', 'Roboto', 'Arial', sans-serif;
            background: linear-gradient(135deg, #eef2f7 0%, #d4dee8 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background: white;
            max-width: 450px;
            width: 100%;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            animation: fadeIn 0.5s ease-out;
        }

        .header-strip {
            background: #1e3a5f;
            height: 8px;
        }

        .logo-area {
            text-align: center;
            padding: 35px 30px 20px;
            border-bottom: 1px solid #eef2f7;
        }

        .logo {
            max-width: 80px;
            margin-bottom: 15px;
        }

        .logo-area h1 {
            color: #1e3a5f;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .logo-area p {
            color: #6c7a89;
            font-size: 0.85rem;
        }

        .form-area {
            padding: 30px;
        }

        .input-group {
            margin-bottom: 25px;
        }

        .input-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #1e3a5f;
            margin-bottom: 8px;
        }

        .input-group input {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #d0d7de;
            border-radius: 12px;
            font-size: 1rem;
            font-family: inherit;
            transition: all 0.2s;
            background: #fefefe;
        }

        .input-group input:focus {
            outline: none;
            border-color: #c9a03d;
            box-shadow: 0 0 0 3px rgba(201, 160, 61, 0.2);
        }

        .login-btn {
            width: 100%;
            background: #1e3a5f;
            color: white;
            border: none;
            padding: 14px;
            border-radius: 40px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 10px;
        }

        .login-btn:hover {
            background: #0f2c48;
        }

        .error-message {
            background: #fdecea;
            color: #b33;
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 0.85rem;
            text-align: center;
        }

        .hint {
            text-align: center;
            margin-top: 20px;
            font-size: 0.75rem;
            color: #8a9bb0;
            background: #f8f9fc;
            padding: 12px;
            border-radius: 12px;
        }

        .hint span {
            background: #eef2f7;
            padding: 3px 10px;
            border-radius: 20px;
            font-family: monospace;
            font-size: 0.8rem;
            color: #1e3a5f;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 500px) {
            .logo-area {
                padding: 25px 20px 15px;
            }
            .form-area {
                padding: 25px 20px;
            }
            .logo-area h1 {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="header-strip"></div>
        
        <div class="logo-area">
            <img src="9855_logo.png" alt="Логотип КИЦ" class="logo">
            <h1>МБУК «Культурно-информационный центр»</h1>
            <p>пгт. Октябрьское</p>
        </div>
        
        <div class="form-area">
            <?php if(isset($error)): ?>
                <div class="error-message">
                    ⚠️ <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <form method="post">
                <div class="input-group">
                    <label>Логин</label>
                    <input type="text" name="login" placeholder="Введите логин" required autofocus>
                </div>
                
                <div class="input-group">
                    <label>Пароль</label>
                    <input type="password" name="password" placeholder="Введите пароль" required>
                </div>
                
                <button type="submit" class="login-btn">Войти в систему</button>
            </form>
            
            <div class="hint">
                🔑 Тестовый доступ: <span>admin</span> / <span>password</span>
            </div>
        </div>
    </div>
</body>
</html>