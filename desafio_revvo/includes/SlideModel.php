<?php
/**
 * LEO Platform — SlideModel
 * Encapsula todo o acesso ao banco para a entidade "slide".
 */

require_once __DIR__ . '/Database.php';

class SlideModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    /** Retorna todos os slides ordenados. */
    public function all(): array
    {
        return $this->db
            ->query("SELECT * FROM slides ORDER BY sort_order ASC, id ASC")
            ->fetchAll();
    }

    /** Busca um slide pelo ID. */
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM slides WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Cria um novo slide e retorna o ID gerado. */
    public function create(array $data): int
    {
        $maxOrder = (int) $this->db
            ->query("SELECT COALESCE(MAX(sort_order), 0) FROM slides")
            ->fetchColumn();

        $stmt = $this->db->prepare("
            INSERT INTO slides (title, description, image_url, btn_text, btn_link, bg_color, sort_order)
            VALUES (:title, :description, :image_url, :btn_text, :btn_link, :bg_color, :sort_order)
        ");

        $stmt->execute([
            ':title'       => $data['title'],
            ':description' => $data['description'] ?? '',
            ':image_url'   => $data['image_url']   ?? '',
            ':btn_text'    => $data['btn_text']     ?? 'Ver Curso',
            ':btn_link'    => $data['btn_link']     ?? '#',
            ':bg_color'    => $data['bg_color']     ?? '#1a1a2e',
            ':sort_order'  => $maxOrder + 1,
        ]);

        return (int) $this->db->lastInsertId();
    }

    /** Atualiza um slide existente. */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE slides
            SET title       = :title,
                description = :description,
                image_url   = :image_url,
                btn_text    = :btn_text,
                btn_link    = :btn_link,
                bg_color    = :bg_color
            WHERE id = :id
        ");

        return $stmt->execute([
            ':title'       => $data['title'],
            ':description' => $data['description'] ?? '',
            ':image_url'   => $data['image_url']   ?? '',
            ':btn_text'    => $data['btn_text']     ?? 'Ver Curso',
            ':btn_link'    => $data['btn_link']     ?? '#',
            ':bg_color'    => $data['bg_color']     ?? '#1a1a2e',
            ':id'          => $id,
        ]);
    }

    /** Remove um slide pelo ID. */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM slides WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
