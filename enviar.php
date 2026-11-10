<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$config = require __DIR__ . '/config.php';

function registrarLog($destinatario, $codigo, $detalhe) {
    $data = date('Y-m-d H:i:s');

    $destinatario = str_replace(["\r", "\n", "|"], ' ', (string)$destinatario);
    $detalhe = str_replace(["\r", "\n", "|"], ' ', (string)$detalhe);

    $linha = "[$data] | Destinatário: $destinatario | HTTP: $codigo | $detalhe" . PHP_EOL;

    file_put_contents(__DIR__ . '/envios.log', $linha, FILE_APPEND | LOCK_EX);
}

function enviarEmail($dados, $apiKey, $destinatarioLog) {
    $curl = curl_init('https://api.resend.com/emails');

    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json'
        ],
        CURLOPT_POSTFIELDS => json_encode($dados, JSON_UNESCAPED_UNICODE),
        CURLOPT_TIMEOUT => 20
    ]);

    $resposta = curl_exec($curl);
    $erroRede = curl_error($curl);
    $codigo = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    if ($resposta === false) {
        registrarLog($destinatarioLog, 0, 'Falha de conexão: ' . $erroRede);
        return ['sucesso' => false, 'tipo' => 'rede'];
    }

    $json = json_decode($resposta, true);

    if ($codigo >= 200 && $codigo < 300) {
        $id = $json['id'] ?? 'ID não retornado';

        registrarLog($destinatarioLog, $codigo, 'Envio realizado. ID: ' . $id);

        return ['sucesso' => true];
    }

    $erro = $json['message'] ?? $json['error'] ?? 'Erro retornado pela API';

    registrarLog($destinatarioLog, $codigo, 'Erro: ' . $erro);

    return ['sucesso' => false, 'codigo' => $codigo];
}

function mensagemErro($codigo) {
    switch ($codigo) {
        case 401:
            return 'Não foi possível autenticar o envio. Entre em contato com o responsável pelo site.';

        case 403:
            return 'O serviço não autorizou o destinatário. Verifique o endereço de e-mail ou tente novamente mais tarde.';

        case 422:
            return 'Os dados enviados são inválidos. Confira o formulário e tente novamente.';

        case 429:
            return 'O serviço está recebendo muitas solicitações. Aguarde um pouco e tente novamente.';

        default:
            return 'Ocorreu um erro inesperado ao enviar sua mensagem. Tente novamente mais tarde.';
    }
}

function paginaResultado($titulo, $mensagem) {
    $titulo = htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8');
    $mensagem = htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8');

    echo '<!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>' . $titulo . '</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background: #f2f6fc;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                margin: 0;
                padding: 20px;
            }

            .card {
                background: white;
                padding: 32px;
                border-radius: 14px;
                max-width: 520px;
                text-align: center;
            }

            h1 { color: #174ea6; }
            p { line-height: 1.5; color: #475569; }
            a { color: #2157ae; }
        </style>
    </head>
    <body>
        <main class="card">
            <h1>' . $titulo . '</h1>
            <p>' . $mensagem . '</p>
            <a href="index.html">Voltar ao formulário</a>
        </main>
    </body>
    </html>';
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html');
    exit;
}

$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$mensagem = trim($_POST['mensagem'] ?? '');

if (
    $nome === '' ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    $mensagem === ''
) {
    paginaResultado(
        'Dados inválidos',
        'Confira o nome, o e-mail e a mensagem e tente novamente.'
    );
    exit;
}

$nomeSeguro = htmlspecialchars($nome, ENT_QUOTES, 'UTF-8');
$emailSeguro = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
$mensagemSeguro = nl2br(htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8'));

$dados = [
    'from' => $config['remetente'],
    'to' => [$config['destino']],
    'reply_to' => $email,
    'subject' => 'Contato do site — ' . $nome,

    'html' => '
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
        style="max-width:600px;margin:0 auto;border-collapse:collapse;font-family:Arial,sans-serif;color:#263238">

        <tr>
            <td style="background:#2157ae;color:#ffffff;padding:24px;text-align:center">
                <h2 style="margin:0">Nova mensagem de contato</h2>
            </td>
        </tr>

        <tr>
            <td style="padding:24px;background:#f7f9fc">
                <p><strong>Nome:</strong> ' . $nomeSeguro . '</p>
                <p><strong>E-mail:</strong> ' . $emailSeguro . '</p>
                <p><strong>Mensagem:</strong><br>' . $mensagemSeguro . '</p>
            </td>
        </tr>

        <tr>
            <td style="padding:16px;text-align:center;background:#e8f1ff;color:#475569;font-size:12px">
                Mensagem enviada pelo formulário do site.
            </td>
        </tr>
    </table>',

    'text' => "Nova mensagem de contato\nNome: $nome\nE-mail: $email\nMensagem:\n$mensagem"
];

$resultado = enviarEmail($dados, $config['api_key'], $config['destino']);

if (!$resultado['sucesso']) {
    if (($resultado['tipo'] ?? '') === 'rede') {
        paginaResultado(
            'Não foi possível enviar',
            'Não foi possível conectar ao serviço de e-mail. Verifique sua conexão e tente novamente.'
        );
    } else {
        paginaResultado(
            'Não foi possível enviar',
            mensagemErro($resultado['codigo'])
        );
    }

    exit;
}

$confirmacao = [
    'from' => $config['remetente'],
    'to' => [$email],
    'subject' => 'Recebemos sua mensagem',

    'html' => '
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
        style="max-width:600px;margin:0 auto;border-collapse:collapse;font-family:Arial,sans-serif;color:#263238">

        <tr>
            <td style="background:#2157ae;color:#ffffff;padding:24px;text-align:center">
                <h2 style="margin:0">Mensagem recebida!</h2>
            </td>
        </tr>

        <tr>
            <td style="padding:24px;background:#f7f9fc">
                <p>Olá, ' . $nomeSeguro . '!</p>
                <p>Recebemos sua mensagem e agradecemos pelo contato. Em breve, ela será analisada.</p>
            </td>
        </tr>

        <tr>
            <td style="padding:16px;text-align:center;background:#e8f1ff;color:#475569;font-size:12px">
                Esta é uma confirmação automática.
            </td>
        </tr>
    </table>',

    'text' => "Olá, $nome!\nRecebemos sua mensagem e agradecemos pelo contato."
];

enviarEmail($confirmacao, $config['api_key'], $email);

paginaResultado(
    'Mensagem enviada!',
    'Sua mensagem foi recebida. A confirmação pode levar alguns instantes para chegar ao seu e-mail.'
);