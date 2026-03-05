<?php
/**
 * LEO Platform — API: Cursos
 *
 * Endpoints:
 *   GET    api/courses.php          → lista todos os cursos
 *   GET    api/courses.php?id=N     → retorna um curso
 *   POST   api/courses.php          → cria curso (JSON body)
 *   PUT    api/courses.php?id=N     → atualiza curso (JSON body)
 *   DELETE api/courses.php?id=N     → remove curso
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/../../includes/helpers.php';
require_once dirname(__DIR__) . '/../../includes/CourseModel.php';

// CORS para facilitar testes externos
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$model  = new CourseModel();
$method = strtoupper($_SERVER['REQUEST_METHOD']);
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($method) {

    // ── READ ─────────────────────────────────
    case 'GET':
        if ($id) {
            $course = $model->find($id);
            $course
                ? jsonResponse($course)
                : jsonResponse(['error' => 'Curso não encontrado.'], 404);
        } else {
            jsonResponse($model->all());
        }
        break;

    // ── CREATE ───────────────────────────────
    case 'POST':
        $body = getJsonBody();
        if (empty($body)) $body = $_POST; // fallback form-encoded

        $title = sanitize($body['title'] ?? '');
        if ($title === '') {
            jsonResponse(['error' => 'O campo "title" é obrigatório.'], 422);
        }

        $data = [
            'title'       => $title,
            'description' => sanitize($body['description'] ?? ''),
            'image_url'   => sanitize($body['image_url']   ?? ''),
            'btn_text'    => sanitize($body['btn_text']    ?? 'Ver Curso'),
            'is_new'      => $body['is_new'] ?? 0,
        ];

        $newId = $model->create($data);
        jsonResponse(['id' => $newId, 'message' => 'Curso criado com sucesso.'], 201);
        break;

    // ── UPDATE ───────────────────────────────
    case 'PUT':
        if (!$id) jsonResponse(['error' => 'ID é obrigatório para atualização.'], 400);
        if (!$model->find($id)) jsonResponse(['error' => 'Curso não encontrado.'], 404);

        $body  = getJsonBody();
        $title = sanitize($body['title'] ?? '');
        if ($title === '') {
            jsonResponse(['error' => 'O campo "title" é obrigatório.'], 422);
        }

        $data = [
            'title'       => $title,
            'description' => sanitize($body['description'] ?? ''),
            'image_url'   => sanitize($body['image_url']   ?? ''),
            'btn_text'    => sanitize($body['btn_text']    ?? 'Ver Curso'),
            'is_new'      => $body['is_new'] ?? 0,
        ];

        $model->update($id, $data);
        jsonResponse(['message' => 'Curso atualizado com sucesso.']);
        break;

    // ── DELETE ───────────────────────────────
    case 'DELETE':
        if (!$id) jsonResponse(['error' => 'ID é obrigatório para exclusão.'], 400);
        if (!$model->find($id)) jsonResponse(['error' => 'Curso não encontrado.'], 404);

        $model->delete($id);
        jsonResponse(['message' => 'Curso removido com sucesso.']);
        break;

    default:
        jsonResponse(['error' => 'Método não permitido.'], 405);
}
