# desafio_revvo

**LEO — Plataforma de Aprendizagem Online**

Solução completa para o Desafio Revvo, desenvolvida com PHP puro, SCSS, JavaScript vanilla e Gulp como automatizador de tarefas.

---

## Sobre mim

**Daniel David**
Desenvolvedor de E-Learning | HTML · CSS ·  PHP · Moodle · Design Instrucional

---

## 🗂 Estrutura do Projeto

```
desafio_revvo/
│
├── public/                    ← Document Root do servidor
│   ├── index.php              ← Homepage principal
│   ├── .htaccess              ← Rewrite rules + segurança
│   ├── assets/
│   │   ├── css/main.css       ← CSS compilado pelo Gulp
│   │   └── js/main.js         ← JS concatenado pelo Gulp
│   └── api/
│       ├── courses.php        ← API REST: CRUD de cursos
│       └── slides.php         ← API REST: CRUD de slides
│
├── src/                       ← Fontes (compilados pelo Gulp)
│   ├── scss/
│   │   ├── main.scss          ← Entry point do SCSS
│   │   ├── _variables.scss    ← Variáveis globais
│   │   ├── _mixins.scss       ← Mixins reutilizáveis
│   │   ├── _base.scss         ← Reset e estilos base
│   │   ├── _components.scss   ← Botões, formulários, toast
│   │   ├── _layout.scss       ← Header, slider, grid, footer
│   │   └── _modals.scss       ← Modais (welcome + CRUD)
│   └── js/
│       └── main.js            ← JS vanilla (slider, CRUD, modais)
│
├── includes/                  ← Backend PHP
│   ├── Database.php           ← Conexão PDO + schema + seeds
│   ├── helpers.php            ← Funções utilitárias
│   ├── CourseModel.php        ← Model: cursos (CRUD)
│   └── SlideModel.php         ← Model: slides (CRUD)
│
├── data/                      ← Banco de dados (auto-criado)
│   └── leo.sqlite             ← SQLite (gerado na 1ª requisição)
│
├── gulpfile.js                ← Tarefas Gulp (sass, js, watch, serve)
├── package.json               ← Dependências Node
└── README.md                  ← Este arquivo
```

---

## Rodando a aplicação

### Pré-requisitos
- **PHP 7.4+** com extensão `pdo_sqlite` habilitada
- **Node.js 14+** e **npm** (para Gulp)
- **Apache** com `mod_rewrite` + `AllowOverride All` (ou Nginx equivalente)

### 1. Clonar o repositório
```bash
git clone https://github.com/seu_usuario/desafio_revvo.git
cd desafio_revvo
```

### 2. Instalar dependências Node
```bash
npm install
```

### 3. Compilar assets com Gulp
```bash
# Build único
npm run build

# Build + watch (recompila ao salvar)
npm run dev

# Build + BrowserSync + watch (requer PHP rodando)
npm run serve
```

### 4. Iniciar servidor PHP (desenvolvimento)
```bash
npm start
# → http://localhost:8000
```

> O banco SQLite é criado automaticamente em `data/leo.sqlite` > na primeira requisição. Não é necessária nenhuma configuração adicional.

### 5. Deploy em Apache
- Aponte o DocumentRoot para a pasta `public/`
- Certifique-se que `AllowOverride All` está ativo
- PHP precisa ter permissão de escrita na pasta `data/`

---

## 🔌 API REST

### Cursos — `public/api/courses.php`

| Método   | URL                       | Ação               | Body JSON                          |
|----------|---------------------------|--------------------|------------------------------------|
| `GET`    | `/api/courses.php`        | Lista todos        | —                                  |
| `GET`    | `/api/courses.php?id=1`   | Busca por ID       | —                                  |
| `POST`   | `/api/courses.php`        | Cria curso         | `title, description, image_url...` |
| `PUT`    | `/api/courses.php?id=1`   | Atualiza           | `title, description, image_url...` |
| `DELETE` | `/api/courses.php?id=1`   | Remove             | —                                  |

**Payload POST/PUT:**
```json
{
  "title":       "Nome do Curso",
  "description": "Descrição do curso",
  "image_url":   "https://...",
  "btn_text":    "Ver Curso",
  "is_new":      1
}
```

### Slides — `public/api/slides.php`

| Método   | URL                      | Ação            | Body JSON                             |
|----------|--------------------------|-----------------|---------------------------------------|
| `GET`    | `/api/slides.php`        | Lista todos     | —                                     |
| `GET`    | `/api/slides.php?id=1`   | Busca por ID    | —                                     |
| `POST`   | `/api/slides.php`        | Cria slide      | `title, description, btn_text...`     |
| `PUT`    | `/api/slides.php?id=1`   | Atualiza        | `title, description, btn_text...`     |
| `DELETE` | `/api/slides.php?id=1`   | Remove          | —                                     |

**Payload POST/PUT:**
```json
{
  "title":       "Título do Slide",
  "description": "Texto descritivo",
  "image_url":   "https://...",
  "btn_text":    "Ver Curso",
  "btn_link":    "#cursos",
  "bg_color":    "#1a1a2e"
}
```

## 🔄 Migração para MySQL

Edite apenas o DSN em `includes/Database.php`:

```php
// Linha atual (SQLite):
$pdo = new PDO('sqlite:' . DB_FILE);

// Substitua por (MySQL):
$pdo = new PDO(
    'mysql:host=localhost;dbname=leo_db;charset=utf8mb4',
    'usuario',
    'senha'
);
```

Crie as tabelas com o schema presente em `_createSchema()` no mesmo arquivo.
Nenhum outro arquivo precisa ser alterado.

---

## 🛠 Tarefas Gulp disponíveis

```bash
gulp          # build completo + watch
gulp build    # build único (sass + js)
gulp sass     # compila apenas SCSS
gulp js       # concatena e minifica apenas JS
gulp watch    # observa mudanças sem build inicial
gulp serve    # build + BrowserSync + watch
```
