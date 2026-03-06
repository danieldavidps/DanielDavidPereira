<?php
/**
 * LEO Platform — API: Slides
 *
 * Endpoints:
 *   GET    api/slides.php          → lista todos os slides
 *   GET    api/slides.php?id=N     → retorna um slide
 *   POST   api/slides.php          → cria slide (JSON body)
 *   PUT    api/slides.php?id=N     → atualiza slide (JSON body)
 *   DELETE api/slides.php?id=N     → remove slide
 */

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/includes/helpers.php';
require_once dirname(__DIR__, 2) . '/includes/SlideModel.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$model  = new SlideModel();
$method = strtoupper($_SERVER['REQUEST_METHOD']);
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($method) {

    case 'GET':
        if ($id) {
            $slide = $model->find($id);
            $slide
                ? jsonResponse($slide)
                : jsonResponse(['error' => 'Slide não encontrado.'], 404);
        } else {
            jsonResponse($model->all());
        }
        break;

    case 'POST':
        $body = getJsonBody();
        if (empty($body)) $body = $_POST;

        $title = sanitize($body['title'] ?? '');
        if ($title === '') {
            jsonResponse(['error' => 'O campo "title" é obrigatório.'], 422);
        }

        $data = [
            'title'       => $title,
            'description' => sanitize($body['description'] ?? ''),
            'image_url'   => sanitize($body['image_url']   ?? ''),
            'btn_text'    => sanitize($body['btn_text']    ?? 'Ver Curso'),
            'btn_link'    => sanitize($body['btn_link']    ?? '#'),
            'bg_color'    => sanitize($body['bg_color']    ?? '#1a1a2e'),
        ];

        $newId = $model->create($data);
        jsonResponse(['id' => $newId, 'message' => 'Slide criado com sucesso.'], 201);
        break;

    case 'PUT':
        if (!$id) jsonResponse(['error' => 'ID é obrigatório para atualização.'], 400);
        if (!$model->find($id)) jsonResponse(['error' => 'Slide não encontrado.'], 404);

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
            'btn_link'    => sanitize($body['btn_link']    ?? '#'),
            'bg_color'    => sanitize($body['bg_color']    ?? '#1a1a2e'),
        ];

        $model->update($id, $data);
        jsonResponse(['message' => 'Slide atualizado com sucesso.']);
        break;

    case 'DELETE':
        if (!$id) jsonResponse(['error' => 'ID é obrigatório para exclusão.'], 400);
        if (!$model->find($id)) jsonResponse(['error' => 'Slide não encontrado.'], 404);

        $model->delete($id);
        jsonResponse(['message' => 'Slide removido com sucesso.']);
        break;

    default:
        jsonResponse(['error' => 'Método não permitido.'], 405);
}
