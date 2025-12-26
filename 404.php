<?php
header("HTTP/1.0 404 Not Found");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Страница потерялась 🚀</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .error-container {
            background: white;
            border-radius: 15px;
            padding: 3rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .error-number {
            font-size: 8rem;
            font-weight: 700;
            color: #dc3545;
            text-shadow: 3px 3px 0 #f8f9fa;
        }
        .error-icon {
            font-size: 4rem;
            color: #6c757d;
            margin: 1rem 0;
            animation: float 3s ease-in-out infinite;
        }
        .btn-home {
            padding: 0.8rem 2rem;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }
        .btn-home:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 error-container text-center">
                <h1 class="error-number">404</h1>
                <i class="fa-solid fa-rocket error-icon"></i>
                <h2>Упс! Страница потерялась в космосе 🌌</h2>
                <p class="text-muted mb-4">Кажется, мы заблудились среди звёзд. Давай вернёмся домой?</p>
                <a href="/" class="btn btn-primary btn-home">
                    <i class="fa-solid fa-house me-2"></i>
                    На главную
                </a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>