
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário de contato</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            padding: 32px 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #e8f0ff, #f5f8ff, #e0eaff);
            color: #1e293b;
            font-family: Arial, sans-serif;
        }

        .container {
            width: 100%;
            max-width: 560px;
            padding: 40px;
            background: #ffffff;
            border-radius: 18px;
            box-shadow: 0 15px 45px rgba(23, 78, 166, 0.12);
            border: 1px solid #e5edfa;
        }

        h1 {
            color: #174ea6;
            font-size: 30px;
            margin-top: 0;
            margin-bottom: 12px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        p {
            color: #64748b;
            line-height: 1.6;
            font-size: 15px;
            margin-bottom: 28px;
        }

        label {
            display: block;
            margin: 20px 0 8px;
            font-weight: bold;
            color: #334155;
            font-size: 14px;
        }

        input, textarea {
            width: 100%;
            padding: 14px 15px;
            border: 1px solid #d8e1ef;
            border-radius: 9px;
            font: inherit;
            font-size: 15px;
            color: #1e293b;
            background: #f8faff;
            outline: none;
            transition: 0.3s ease;
        }

        input:focus, textarea:focus {
            border-color: #2157ae;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(33, 87, 174, 0.10);
        }

        input::placeholder, textarea::placeholder {
            color: #94a3b8;
        }

        textarea {
            min-height: 150px;
            resize: vertical;
        }

        button {
            width: 100%;
            margin-top: 25px;
            padding: 15px;
            border: none;
            border-radius: 9px;
            background: linear-gradient(135deg, #2157ae, #17428c);
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s ease;
        }

        button:hover {
            background: linear-gradient(135deg, #17428c, #10336f);
            transform: translateY(-2px);
            box-shadow: 0 7px 18px rgba(33, 87, 174, 0.22);
        }

        @media (max-width: 600px) {
            .container {
                padding: 28px 22px;
            }

            h1 {
                font-size: 26px;
            }
        }
    </style>
</head>
<body>
    <main class="container">
        <h1>Entre em contato</h1>
        <p>Preencha os campos abaixo para enviar sua mensagem.</p>

        <form action="./enviar.php" method="POST">
            <label for="nome">Nome</label>
            <input type="text" id="nome" name="nome" maxlength="100" required>

            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" maxlength="254" required>

            <label for="mensagem">Mensagem</label>
            <textarea id="mensagem" name="mensagem" maxlength="5000" required></textarea>

            <button type="submit">Enviar mensagem</button>
        </form>
    </main>
</body>
</html>
