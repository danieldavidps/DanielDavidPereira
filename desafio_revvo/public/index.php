<?php
/**
 * LEO Platform — Homepage
 * PHP puro, sem frameworks.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/helpers.php';
require_once dirname(__DIR__) . '/includes/CourseModel.php';
require_once dirname(__DIR__) . '/includes/SlideModel.php';

$courses    = (new CourseModel())->all();
$slides     = (new SlideModel())->all();
$firstVisit = isFirstVisit();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="LEO — Plataforma de Aprendizagem Online">
  <title>LEO — Plataforma de Aprendizagem</title>
  <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>

<!-- =============================================
     ADMIN BAR
============================================= -->
<div class="admin-bar">
  <strong>LEO Admin</strong>
  <span class="admin-bar__sep">|</span>
  <a href="#" onclick="openCourseModal(); return false;">+ Novo Curso</a>
  <span class="admin-bar__sep">|</span>
  <a href="#" onclick="openSlideModal(); return false;">+ Novo Slide</a>
</div>

<!-- =============================================
     HEADER
============================================= -->
<header class="header">
  <a href="/" class="logo">
    L<span class="logo__accent">E</span>O
  </a>

  <div class="header__right">
    <div class="search-bar" role="search">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
           viewBox="0 0 24 24" aria-hidden="true">
        <circle cx="11" cy="11" r="8"/>
        <path d="m21 21-4.35-4.35"/>
      </svg>
      <input type="search" placeholder="Pesquisar cursos..." aria-label="Pesquisar cursos">
    </div>

    <div class="user-info">
      <div class="user-avatar" aria-hidden="true">J</div>
      <div class="user-name">
        <small>Seja bem-vindo</small>
        John Doe
      </div>
      <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"
           style="color:#888;margin-left:2px;">
        <path d="M7 10l5 5 5-5z"/>
      </svg>
    </div>
  </div>
</header>

<!-- =============================================
     SLIDER
============================================= -->
<section class="slider" aria-label="Destaques">
  <div class="slider__track" id="sliderTrack">
    <?php if (empty($slides)): ?>
    <div class="slider__slide" style="background-color:#1a1a2e;">
      <div class="slider__overlay">
        <h2>Nenhum slide cadastrado</h2>
        <p>Adicione slides pelo painel acima.</p>
      </div>
    </div>
    <?php else: ?>
    <?php foreach ($slides as $slide): ?>
    <div class="slider__slide"
         style="background-color:<?= h($slide['bg_color']) ?>;<?= $slide['image_url'] ? 'background-image:url(' . h($slide['image_url']) . ');background-size:cover;background-position:center;' : '' ?>"
         data-id="<?= (int)$slide['id'] ?>">

      <div class="slider__overlay">
        <h2><?= h($slide['title']) ?></h2>
        <p><?= h($slide['description']) ?></p>
        <a href="<?= h($slide['btn_link']) ?>" class="btn btn--outline">
          <?= h($slide['btn_text']) ?>
        </a>
      </div>

      <div class="slider__admin-controls">
        <button class="btn--edit-inline"
                onclick="openSlideModal(<?= (int)$slide['id'] ?>)"
                aria-label="Editar slide">
          ✎ Editar
        </button>
        <button class="btn--delete-inline"
                onclick="deleteSlide(<?= (int)$slide['id'] ?>)"
                aria-label="Excluir slide">
          ✕ Excluir
        </button>
      </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <button class="slider__btn slider__btn--prev"
          onclick="sliderPrev()"
          aria-label="Slide anterior">&#8249;</button>

  <button class="slider__btn slider__btn--next"
          onclick="sliderNext()"
          aria-label="Próximo slide">&#8250;</button>

  <div class="slider__dots" id="sliderDots" role="tablist" aria-label="Navegação do slider"></div>
</section>

<!-- =============================================
     CURSOS
============================================= -->
<main class="container">
  <section class="courses" id="cursos">
    <div class="section-title">
      <span>Meus Cursos</span>
    </div>

    <div class="course-grid">
      <?php foreach ($courses as $course): ?>
      <article class="course-card" data-id="<?= (int)$course['id'] ?>">

        <div class="course-card__thumb">
          <img
            src="<?= h($course['image_url'] ?: 'https://via.placeholder.com/400x150/e8e8e8/aaa?text=Curso') ?>"
            alt="<?= h($course['title']) ?>"
            loading="lazy"
            onerror="this.src='https://via.placeholder.com/400x150/e8e8e8/aaa?text=Curso'"
          >
        </div>

        <?php if ((int)$course['is_new'] === 1): ?>
          <span class="course-card__badge">Novo</span>
        <?php endif; ?>

        <div class="course-card__actions">
          <button class="btn btn--edit-sm"
                  onclick="openCourseModal(<?= (int)$course['id'] ?>)"
                  title="Editar">✎</button>
          <button class="btn btn--danger-sm"
                  onclick="deleteCourse(<?= (int)$course['id'] ?>)"
                  title="Excluir">✕</button>
        </div>

        <div class="course-card__body">
          <h3 class="course-card__title"><?= h($course['title']) ?></h3>
          <p class="course-card__desc"><?= h($course['description']) ?></p>
          <button class="btn btn--green"><?= h($course['btn_text']) ?></button>
        </div>

      </article>
      <?php endforeach; ?>

      <!-- Add Course -->
      <div class="card-add" onclick="openCourseModal()" role="button"
           tabindex="0" aria-label="Adicionar novo curso"
           onkeypress="if(event.key==='Enter')openCourseModal()">
        <svg width="52" height="52" fill="none" stroke="currentColor"
             stroke-width="1.4" viewBox="0 0 24 24" aria-hidden="true">
          <rect x="2" y="3" width="20" height="14" rx="2"/>
          <path d="M8 21h8M12 17v4M9 10h6M12 7v6"/>
        </svg>
        <span>Adicionar Curso</span>
      </div>
    </div>
  </section>
</main>

<!-- =============================================
     FOOTER
============================================= -->
<footer class="footer">
  <div class="footer__inner">
    <div class="footer__brand">
      <div class="logo">L<span class="logo__accent">E</span>O</div>
      <p>Maecenas faucibus mollis interdum. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.</p>
    </div>

    <div class="footer__col">
      <h4>// Contato</h4>
      <p>(21) 98765-3434</p>
      <a href="mailto:contato@leolearning.com">contato@leolearning.com</a>
    </div>

    <div class="footer__col">
      <h4>// Redes Sociais</h4>
      <div class="footer__social">
        <a class="social-btn" href="#" aria-label="Twitter">𝕏</a>
        <a class="social-btn" href="#" aria-label="YouTube">▶</a>
        <a class="social-btn" href="#" aria-label="Pinterest">𝒫</a>
      </div>
    </div>
  </div>

  <p class="footer__copy">Copyright 2017 – All right reserved.</p>
</footer>

<!-- =============================================
     MODAL — PRIMEIRO ACESSO (Welcome)
============================================= -->
<?php if ($firstVisit): ?>
<div class="modal-backdrop is-open" id="modalWelcome" role="dialog"
     aria-modal="true" aria-labelledby="welcomeTitle">
  <div class="modal-welcome">

    <div class="modal-welcome__hero">
      <div class="modal-welcome__icons" aria-hidden="true">
        <div class="modal-welcome__icon">📋</div>
        <div class="modal-welcome__icon">💻</div>
        <div class="modal-welcome__icon">✏️</div>
        <div class="modal-welcome__icon">📐</div>
        <div class="modal-welcome__icon">✂️</div>
        <div class="modal-welcome__icon">🖊️</div>
      </div>
    </div>

    <button class="modal-welcome__close"
            onclick="closeModal('modalWelcome')"
            aria-label="Fechar modal">✕</button>

    <div class="modal-welcome__body">
      <h2 id="welcomeTitle">Egestas Tortor Vulputate</h2>
      <p>
        Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum.<br>
        Donec ullamcorper nulla non metus auctor fringilla. Donec sed odio dui. Cras
      </p>
      <button class="btn btn--blue" onclick="closeModal('modalWelcome')">
        Inscreva-se
      </button>
    </div>

  </div>
</div>
<?php endif; ?>

<!-- =============================================
     MODAL — CRUD CURSO
============================================= -->
<div class="modal-backdrop modal-admin-wrapper" id="modalCourse"
     role="dialog" aria-modal="true" aria-labelledby="courseModalTitle">
  <div class="modal-admin">

    <div class="modal-admin__header">
      <h3 id="courseModalTitle">Novo Curso</h3>
      <button onclick="closeModal('modalCourse')" aria-label="Fechar">✕</button>
    </div>

    <input type="hidden" id="courseId">

    <div class="form-group">
      <label for="c_title">Título *</label>
      <input type="text" id="c_title" placeholder="Nome do curso">
    </div>

    <div class="form-group">
      <label for="c_desc">Descrição</label>
      <textarea id="c_desc" placeholder="Descrição breve do curso..."></textarea>
    </div>

    <div class="form-group">
      <label for="c_img">URL da Imagem (Thumbnail)</label>
      <input type="text" id="c_img" placeholder="https://...">
    </div>

    <div class="form-group">
      <label for="c_btn">Texto do Botão</label>
      <input type="text" id="c_btn" placeholder="Ver Curso" value="Ver Curso">
    </div>

    <div class="form-group form-group--checkbox">
      <input type="checkbox" id="c_is_new">
      <label for="c_is_new">Marcar como Novo</label>
    </div>

    <div class="form-actions">
      <button class="btn btn--cancel" onclick="closeModal('modalCourse')">Cancelar</button>
      <button class="btn btn--save"   onclick="saveCourse()">Salvar Curso</button>
    </div>
  </div>
</div>

<!-- =============================================
     MODAL — CRUD SLIDE
============================================= -->
<div class="modal-backdrop modal-admin-wrapper" id="modalSlide"
     role="dialog" aria-modal="true" aria-labelledby="slideModalTitle">
  <div class="modal-admin">

    <div class="modal-admin__header">
      <h3 id="slideModalTitle">Novo Slide</h3>
      <button onclick="closeModal('modalSlide')" aria-label="Fechar">✕</button>
    </div>

    <input type="hidden" id="slideId">

    <div class="form-group">
      <label for="s_title">Título *</label>
      <input type="text" id="s_title" placeholder="Título do slide">
    </div>

    <div class="form-group">
      <label for="s_desc">Descrição</label>
      <textarea id="s_desc" placeholder="Texto de descrição..."></textarea>
    </div>

    <div class="form-group">
      <label for="s_img">URL da Imagem de Fundo</label>
      <input type="text" id="s_img" placeholder="https://...">
    </div>

    <div class="form-group">
      <label for="s_btn_text">Texto do Botão</label>
      <input type="text" id="s_btn_text" placeholder="Ver Curso" value="Ver Curso">
    </div>

    <div class="form-group">
      <label for="s_btn_link">Link do Botão</label>
      <input type="text" id="s_btn_link" placeholder="#cursos" value="#cursos">
    </div>

    <div class="form-group">
      <label for="s_bg">Cor de Fundo (hex)</label>
      <input type="text" id="s_bg" placeholder="#1a1a2e" value="#1a1a2e">
    </div>

    <div class="form-actions">
      <button class="btn btn--cancel" onclick="closeModal('modalSlide')">Cancelar</button>
      <button class="btn btn--save"   onclick="saveSlide()">Salvar Slide</button>
    </div>
  </div>
</div>

<!-- =============================================
     TOAST
============================================= -->
<div class="toast" id="toast" role="status" aria-live="polite"></div>

<script src="assets/js/main.js"></script>
</body>
</html>
