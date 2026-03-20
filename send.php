<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'msg' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['ok' => false, 'msg' => 'Invalid JSON']);
    exit;
}

$plano         = htmlspecialchars($input['plano']          ?? '');
$nome          = htmlspecialchars($input['nome']           ?? '');
$email         = htmlspecialchars($input['email']          ?? '');
$telefone      = htmlspecialchars($input['telefone']       ?? '');
$cnpj          = htmlspecialchars($input['cnpj']           ?? '');
$formaPagamento = htmlspecialchars($input['formaPagamento'] ?? '');

if (!$nome || !$email || !$telefone) {
    echo json_encode(['ok' => false, 'msg' => 'Campos obrigatórios faltando']);
    exit;
}

$para    = 'wagnercamposneto@gmail.com';
$assunto = "Nova solicitação — Plano $plano | MOA.labs";

$corpo  = "Nova solicitação recebida pelo site MOA.labs\n";
$corpo .= str_repeat('=', 50) . "\n\n";
$corpo .= "Plano:           $plano\n";
$corpo .= "Forma pagamento: $formaPagamento\n\n";
$corpo .= "Nome:     $nome\n";
$corpo .= "E-mail:   $email\n";
$corpo .= "Telefone: $telefone\n";
$corpo .= "CNPJ:     " . ($cnpj ?: 'Não informado') . "\n";
$corpo .= "\n" . str_repeat('=', 50) . "\n";
$corpo .= "Enviado em: " . date('d/m/Y H:i:s') . " (horário do servidor)\n";

$headers  = "From: MOA.labs <noreply@agenciamoa.com.br>\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

$enviado = mail($para, $assunto, $corpo, $headers);

if ($enviado) {
    echo json_encode(['ok' => true]);
} else {
    echo json_encode(['ok' => false, 'msg' => 'Falha ao enviar e-mail']);
}
