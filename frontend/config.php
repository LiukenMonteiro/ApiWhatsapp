<?php
// ============================================================
// config.php — Configurações do frontend PHP
// ============================================================

// URL base da API Node.js
// Com Docker usa a variável de ambiente API_URL (definida no docker-compose).
// Sem Docker usa localhost:3000 como padrão.
define('API_URL', getenv('API_URL') ?: 'http://localhost:3000');

// Chave de autenticação — deve ser igual à API_KEY do .env
define('API_KEY', getenv('API_KEY') ?: 'troque_por_uma_chave_secreta_aleatoria');

// ============================================================
// Função auxiliar para chamar a API Node.js
// Usa file_get_contents + stream_context (sem extensão cURL)
// ============================================================
function chamarApi(string $metodo, string $endpoint, array $dados = []): array {
    $url = API_URL . $endpoint;

    $opcoes = [
        'http' => [
            'method'        => $metodo,
            'header'        => implode("\r\n", [
                'Content-Type: application/json',
                'x-api-key: ' . API_KEY,
            ]),
            'timeout'       => 10,
            'ignore_errors' => true, // retorna a resposta mesmo em erro HTTP
        ],
    ];

    if (in_array($metodo, ['POST', 'PUT']) && !empty($dados)) {
        $opcoes['http']['content'] = json_encode($dados);
    }

    $contexto  = stream_context_create($opcoes);
    $resposta  = @file_get_contents($url, false, $contexto);

    if ($resposta === false) {
        return ['sucesso' => false, 'mensagem' => 'Erro de conexão com a API. Verifique se o servidor Node.js está rodando.'];
    }

    return json_decode($resposta, true) ?? ['sucesso' => false, 'mensagem' => 'Resposta inválida da API.'];
}
