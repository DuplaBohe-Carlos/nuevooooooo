<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Seguimiento de Horas</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background: #f0f2f5;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
            max-width: 450px;
            margin: 50px auto;
        }

        h1, h2 {
            color: #1a202c;
            margin-bottom: 20px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #4a5568;
            font-weight: 500;
        }

        input, select {
            width: 100%;
            padding: 14px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 16px;
            transition: border 0.2s;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #3182ce;
            box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1);
        }

        .btn {
            width: 100%;
            padding: 14px;
            background: #3182ce;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn:hover {
            background: #2b6cb0;
        }

        .alert {
            padding: 14px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .alert-danger {
            background: #fed7d7;
            color: #c53030;
            border: 1px solid #fc8181;
        }

        .alert-success {
            background: #c6f6d5;
            color: #276749;
            border: 1px solid #68d391;
        }

        .link {
            text-align: center;
            margin-top: 20px;
            color: #4a5568;
        }

        .link a {
            color: #3182ce;
            text-decoration: none;
            font-weight: 500;
        }

        .navbar {
            background: #1a202c;
            padding: 16px;
            color: white;
            margin-bottom: 30px;
        }

        .navbar .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
        }

        @media (max-width: 768px) {
            .card {
                margin: 20px auto;
                padding: 20px;
            }
            
            .navbar .container {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>