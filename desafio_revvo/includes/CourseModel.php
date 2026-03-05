<?php
/**
 * LEO Platform — CourseModel
 * Encapsula todo o acesso ao banco para a entidade "curso".
 */

require_once __DIR__ . '/Database.php';

class CourseModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    /** Retorna todos os cursos ordenados. */
    public function all(): array
    {
        return $this->db
            ->query("SELECT * FROM courses ORDER BY sort_order ASC, id ASC")
            ->fetchAll();
    }

    /** Busca um curso pelo ID. */
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM courses WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Cria um novo curso e retorna o ID gerado. */
    public function create(array $data): int
    {
        $maxOrder = (int) $this->db
            ->query("SELECT COALESCE(MAX(sort_order), 0) FROM courses")
            ->fetchColumn();

        $stmt = $this->db->prepare("
            INSERT INTO courses (title, description, image_url, btn_text, is_new, sort_order)
            VALUES (:title, :description, :image_url, :btn_text, :is_new, :sort_order)
        ");

        $stmt->execute([
            ':title'       => $data['title'],
            ':description' => $data['description'] ?? '',
            ':image_url'   => $data['image_url']   ?? '',
            ':btn_text'    => $data['btn_text']     ?? 'Ver Curso',
            ':is_new'      => isset($data['is_new']) ? (int)$data['is_new'] : 0,
            ':sort_order'  => $maxOrder + 1,
        ]);

        return (int) $this->db->lastInsertId();
    }

    /** Atualiza um curso existente. */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE courses
            SET title       = :title,
                description = :description,
                image_url   = :image_url,
                btn_text    = :btn_text,
                is_new      = :is_new
            WHERE id = :id
        ");

        return $stmt->execute([
            ':title'       => $data['title'],
            ':description' => $data['description'] ?? '',
            ':image_url'   => $data['image_url']   ?? '',
            ':btn_text'    => $data['btn_text']     ?? 'Ver Curso',
            ':is_new'      => isset($data['is_new']) ? (int)$data['is_new'] : 0,
            ':id'          => $id,
        ]);
    }

    /** Remove um curso pelo ID. */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM courses WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
