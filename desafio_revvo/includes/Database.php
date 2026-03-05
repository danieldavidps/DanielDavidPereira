<?php
/**
 * LEO Platform — Database
 *
 * Usa SQLite por padrão (zero configuração).
 * Para trocar para MySQL, substitua apenas o DSN e credenciais
 * na função getDB() — todo o restante usa PDO puro.
 */

define('DB_FILE', dirname(__DIR__) . '/data/leo.sqlite');

function getDB(): PDO
{
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    $dir = dirname(DB_FILE);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    /* ── Troque aqui para MySQL se necessário ──
       $pdo = new PDO(
           'mysql:host=localhost;dbname=leo_db;charset=utf8mb4',
           'root', 'secret'
       );
    ── */
    $pdo = new PDO('sqlite:' . DB_FILE);
    $pdo->setAttribute(PDO::ATTR_ERRMODE,            PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA journal_mode=WAL; PRAGMA foreign_keys=ON;');

    _createSchema($pdo);

    return $pdo;
}

function _createSchema(PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS slides (
            id          INTEGER PRIMARY KEY AUTOINCREMENT,
            title       TEXT    NOT NULL,
            description TEXT    NOT NULL DEFAULT '',
            image_url   TEXT    NOT NULL DEFAULT '',
            btn_text    TEXT    NOT NULL DEFAULT 'Ver Curso',
            btn_link    TEXT    NOT NULL DEFAULT '#',
            bg_color    TEXT    NOT NULL DEFAULT '#1a1a2e',
            sort_order  INTEGER NOT NULL DEFAULT 0,
            created_at  DATETIME DEFAULT (datetime('now','localtime'))
        );

        CREATE TABLE IF NOT EXISTS courses (
            id          INTEGER PRIMARY KEY AUTOINCREMENT,
            title       TEXT    NOT NULL,
            description TEXT    NOT NULL DEFAULT '',
            image_url   TEXT    NOT NULL DEFAULT '',
            btn_text    TEXT    NOT NULL DEFAULT 'Ver Curso',
            is_new      INTEGER NOT NULL DEFAULT 0,
            sort_order  INTEGER NOT NULL DEFAULT 0,
            created_at  DATETIME DEFAULT (datetime('now','localtime'))
        );
    ");

    _seedSlides($pdo);
    _seedCourses($pdo);
}

function _seedSlides(PDO $pdo): void
{
    if ((int)$pdo->query("SELECT COUNT(*) FROM slides")->fetchColumn() > 0) return;

    $stmt = $pdo->prepare("
        INSERT INTO slides (title, description, btn_text, btn_link, bg_color, sort_order)
        VALUES (:title, :description, :btn_text, :btn_link, :bg_color, :sort_order)
    ");

    $data = [
        [
            'title'       => 'Bem-vindo à LEO',
            'description' => 'Aenean lacinia bibendum nulla sed consectetur. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.',
            'btn_text'    => 'Ver Curso',
            'btn_link'    => '#cursos',
            'bg_color'    => '#1a1a2e',
            'sort_order'  => 1,
        ],
        [
            'title'       => 'Novos Cursos Disponíveis',
            'description' => 'Donec sed odio dui. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper.',
            'btn_text'    => 'Explorar',
            'btn_link'    => '#cursos',
            'bg_color'    => '#0d47a1',
            'sort_order'  => 2,
        ],
        [
            'title'       => 'Aprenda no seu Ritmo',
            'description' => 'Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.',
            'btn_text'    => 'Saiba Mais',
            'btn_link'    => '#cursos',
            'bg_color'    => '#1b5e20',
            'sort_order'  => 3,
        ],
    ];

    foreach ($data as $row) $stmt->execute($row);
}

function _seedCourses(PDO $pdo): void
{
    if ((int)$pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn() > 0) return;

    $stmt = $pdo->prepare("
        INSERT INTO courses (title, description, image_url, btn_text, is_new, sort_order)
        VALUES (:title, :description, :image_url, :btn_text, :is_new, :sort_order)
    ");

    $data = [
        ['Gestão de Projetos',   'Fundamentos de gerenciamento de projetos ágeis e tradicionais.',        'https://images.unsplash.com/photo-1515378791036-0648a3ef77b2?w=400&q=80', 'Ver Curso', 1, 1],
        ['Design Instrucional',  'Princípios de criação de cursos EAD eficientes e engajantes.',          'https://images.unsplash.com/photo-1503676382389-4809596d5290?w=400&q=80', 'Ver Curso', 0, 2],
        ['HTML & CSS Avançado',  'Construção de interfaces modernas e acessíveis na web.',                'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?w=400&q=80', 'Ver Curso', 0, 3],
        ['Moodle para Tutores',  'Configuração e gestão de turmas na plataforma Moodle LMS.',             'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=400&q=80', 'Ver Curso', 0, 4],
        ['JavaScript Essencial', 'Programação web dinâmica com JavaScript moderno (ES6+).',               'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=400&q=80', 'Ver Curso', 0, 5],
        ['UX para E-Learning',   'Experiência do usuário aplicada a ambientes de aprendizagem digital.',  'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=400&q=80', 'Ver Curso', 0, 6],
        ['Avaliação Educacional','Métodos e métricas para mensurar a aprendizagem com eficácia.',         'https://images.unsplash.com/photo-1546410531-bb4caa6b424d?w=400&q=80', 'Ver Curso', 0, 7],
    ];

    foreach ($data as $r) {
        $stmt->execute([
            ':title'       => $r[0],
            ':description' => $r[1],
            ':image_url'   => $r[2],
            ':btn_text'    => $r[3],
            ':is_new'      => $r[4],
            ':sort_order'  => $r[5],
        ]);
    }
}
