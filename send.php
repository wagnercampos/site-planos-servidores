<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'msg' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['ok' => false, 'msg' => 'Invalid JSON']);
    exit;
}

$plano          = htmlspecialchars($input['plano']           ?? '');
$nome           = htmlspecialchars($input['nome']            ?? '');
$email          = htmlspecialchars($input['email']           ?? '');
$telefone       = htmlspecialchars($input['telefone']        ?? '');
$cnpj           = htmlspecialchars($input['cnpj']            ?? '');
$formaPagamento = htmlspecialchars($input['formaPagamento']  ?? '');
$valor          = htmlspecialchars($input['valor']           ?? '—');

if (!$nome || !$email || !$telefone) {
    echo json_encode(['ok' => false, 'msg' => 'Campos obrigatórios faltando']);
    exit;
}

require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'mail.agenciamoa.com.br';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'servidores@agenciamoa.com.br';
    $mail->Password   = 'h9K2.&uGc4p_';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom('servidores@agenciamoa.com.br', 'MOA.labs');
    $mail->addAddress('wagnercamposneto@gmail.com', 'Wagner');
    $mail->addReplyTo($email, $nome);

    $mail->Subject = "Nova solicitação — Plano $plano | MOA.labs";
    $mail->Body    =
        "Nova solicitação recebida pelo site MOA.labs\n" .
        str_repeat('=', 50) . "\n\n" .
        "Plano:            $plano\n" .
        "Valor:            $valor\n" .
        "Forma pagamento:  $formaPagamento\n\n" .
        "Nome:      $nome\n" .
        "E-mail:    $email\n" .
        "Telefone:  $telefone\n" .
        "CNPJ:      " . ($cnpj ?: 'Não informado') . "\n\n" .
        str_repeat('=', 50) . "\n" .
        "Enviado em: " . date('d/m/Y H:i:s') . "\n";

    $mail->send();
    echo json_encode(['ok' => true]);

} catch (Exception $e) {
    echo json_encode(['ok' => false, 'msg' => $mail->ErrorInfo]);
}
