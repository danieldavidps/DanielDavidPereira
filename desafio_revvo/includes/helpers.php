<?php
/**
 * LEO Platform — Helpers
 */

/**
 * Escapa output HTML para prevenir XSS.
 */
function h(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Retorna resposta JSON e encerra execução.
 */
function jsonResponse(array $data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * Redirect HTTP.
 */
function redirect(string $url): void
{
    header("Location: $url");
    exit;
}

/**
 * Verifica se é o primeiro acesso do usuário (via cookie).
 * Define o cookie na primeira chamada — retorna TRUE apenas na 1ª vez.
 */
function isFirstVisit(): bool
{
    if (isset($_COOKIE['leo_visited'])) {
        return false;
    }
    // Define cookie por 1 ano
    setcookie('leo_visited', '1', [
        'expires'  => time() + 31536000,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    return true;
}

/**
 * Lê e decodifica o corpo JSON da requisição.
 */
function getJsonBody(): array
{
    $raw  = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

/**
 * Sanitiza string vinda de input.
 */
function sanitize(string $val): string
{
    return trim(strip_tags($val));
}
