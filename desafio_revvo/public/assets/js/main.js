/**
 * LEO Platform — main.js
 * Vanilla JS — sem frameworks, sem dependências externas
 */
(function (w, d) {
  'use strict';

  /* ===================================================
     UTILS
  =================================================== */
  const $  = (sel, ctx = d) => ctx.querySelector(sel);
  const $$ = (sel, ctx = d) => [...ctx.querySelectorAll(sel)];

  function showToast(msg, type = 'success') {
    const el = d.getElementById('toast');
    if (!el) return;
    el.textContent = msg;
    el.style.borderLeftColor = type === 'error' ? '#c62828' : '#4caf50';
    el.classList.add('is-visible');
    clearTimeout(el._t);
    el._t = setTimeout(() => el.classList.remove('is-visible'), 3200);
  }

  function openModal(id) {
    const el = d.getElementById(id);
    if (el) el.classList.add('is-open');
  }

  function closeModal(id) {
    const el = d.getElementById(id);
    if (el) el.classList.remove('is-open');
  }

  // Close modals on backdrop click
  $$('.modal-backdrop').forEach(backdrop => {
    backdrop.addEventListener('click', e => {
      if (e.target === backdrop) backdrop.classList.remove('is-open');
    });
  });

  // Expose globally (called from PHP-rendered onclick)
  w.openModal  = openModal;
  w.closeModal = closeModal;
  w.showToast  = showToast;


  /* ===================================================
     SLIDER
  =================================================== */
  const Slider = {
    track:   d.getElementById('sliderTrack'),
    dotsWrap:d.getElementById('sliderDots'),
    current: 0,
    timer:   null,
    autoInterval: 5000,

    init() {
      if (!this.track) return;
      this.slides = $$('.slider__slide', this.track);
      if (!this.slides.length) return;
      this.buildDots();
      this.go(0);
    },

    buildDots() {
      this.dotsWrap.innerHTML = '';
      this.slides.forEach((_, i) => {
        const btn = d.createElement('button');
        btn.className = 'slider__dot' + (i === 0 ? ' is-active' : '');
        btn.setAttribute('aria-label', `Slide ${i + 1}`);
        btn.addEventListener('click', () => this.go(i));
        this.dotsWrap.appendChild(btn);
      });
    },

    go(n) {
      const total = this.slides.length;
      this.current = ((n % total) + total) % total;
      this.track.style.transform = `translateX(-${this.current * 100}%)`;
      $$('.slider__dot', this.dotsWrap).forEach((d, i) =>
        d.classList.toggle('is-active', i === this.current)
      );
      clearTimeout(this.timer);
      this.timer = setTimeout(() => this.go(this.current + 1), this.autoInterval);
    },

    prev() { this.go(this.current - 1); },
    next() { this.go(this.current + 1); }
  };

  w.sliderPrev = () => Slider.prev();
  w.sliderNext = () => Slider.next();


  /* ===================================================
     CRUD — SHARED HELPERS
  =================================================== */
  async function apiRequest(url, method = 'GET', body = null) {
    const opts = {
      method,
      headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    };
    if (body) opts.body = JSON.stringify(body);
    const res = await fetch(url, opts);
    return res.json();
  }

  function setField(id, val) {
    const el = d.getElementById(id);
    if (!el) return;
    if (el.type === 'checkbox') el.checked = !!parseInt(val);
    else el.value = val ?? '';
  }

  function getField(id) {
    const el = d.getElementById(id);
    if (!el) return '';
    if (el.type === 'checkbox') return el.checked ? 1 : 0;
    return el.value.trim();
  }


  /* ===================================================
     CRUD — COURSES
  =================================================== */
  const CourseForm = {
    modalId: 'modalCourse',

    open(id = null) {
      d.getElementById('courseId').value = id || '';
      d.getElementById('courseModalTitle').textContent = id ? 'Editar Curso' : 'Novo Curso';

      if (id) {
        apiRequest(`api/courses.php?id=${id}`)
          .then(c => {
            setField('c_title',   c.title);
            setField('c_desc',    c.description);
            setField('c_img',     c.image_url);
            setField('c_btn',     c.btn_text);
            setField('c_is_new',  c.is_new);
          })
          .catch(() => showToast('Erro ao carregar curso.', 'error'));
      } else {
        ['c_title','c_desc','c_img'].forEach(f => setField(f, ''));
        setField('c_btn', 'Ver Curso');
        setField('c_is_new', 0);
      }
      openModal(this.modalId);
    },

    async save() {
      const id = d.getElementById('courseId').value;
      const payload = {
        title:       getField('c_title'),
        description: getField('c_desc'),
        image_url:   getField('c_img'),
        btn_text:    getField('c_btn') || 'Ver Curso',
        is_new:      getField('c_is_new'),
      };
      if (!payload.title) { showToast('⚠ Informe o título.', 'error'); return; }

      try {
        const url    = id ? `api/courses.php?id=${id}` : 'api/courses.php';
        const method = id ? 'PUT' : 'POST';
        const res    = await apiRequest(url, method, payload);
        showToast(res.message || '✔ Salvo com sucesso!');
        closeModal(this.modalId);
        setTimeout(() => location.reload(), 900);
      } catch {
        showToast('Erro ao salvar.', 'error');
      }
    },

    async delete(id) {
      if (!confirm('Deseja excluir este curso?')) return;
      try {
        const res = await apiRequest(`api/courses.php?id=${id}`, 'DELETE');
        showToast(res.message || '🗑 Removido.');
        setTimeout(() => location.reload(), 900);
      } catch {
        showToast('Erro ao excluir.', 'error');
      }
    }
  };

  w.openCourseModal  = (id) => CourseForm.open(id);
  w.saveCourse       = ()   => CourseForm.save();
  w.deleteCourse     = (id) => CourseForm.delete(id);


  /* ===================================================
     CRUD — SLIDES
  =================================================== */
  const SlideForm = {
    modalId: 'modalSlide',

    open(id = null) {
      d.getElementById('slideId').value = id || '';
      d.getElementById('slideModalTitle').textContent = id ? 'Editar Slide' : 'Novo Slide';

      if (id) {
        apiRequest(`api/slides.php?id=${id}`)
          .then(s => {
            setField('s_title',    s.title);
            setField('s_desc',     s.description);
            setField('s_img',      s.image_url);
            setField('s_btn_text', s.btn_text);
            setField('s_btn_link', s.btn_link);
            setField('s_bg',       s.bg_color);
          })
          .catch(() => showToast('Erro ao carregar slide.', 'error'));
      } else {
        ['s_title','s_desc','s_img'].forEach(f => setField(f, ''));
        setField('s_btn_text', 'Ver Curso');
        setField('s_btn_link', '#cursos');
        setField('s_bg',       '#1a1a2e');
      }
      openModal(this.modalId);
    },

    async save() {
      const id = d.getElementById('slideId').value;
      const payload = {
        title:       getField('s_title'),
        description: getField('s_desc'),
        image_url:   getField('s_img'),
        btn_text:    getField('s_btn_text') || 'Ver Curso',
        btn_link:    getField('s_btn_link') || '#',
        bg_color:    getField('s_bg')       || '#1a1a2e',
      };
      if (!payload.title) { showToast('⚠ Informe o título.', 'error'); return; }

      try {
        const url    = id ? `api/slides.php?id=${id}` : 'api/slides.php';
        const method = id ? 'PUT' : 'POST';
        const res    = await apiRequest(url, method, payload);
        showToast(res.message || '✔ Salvo com sucesso!');
        closeModal(this.modalId);
        setTimeout(() => location.reload(), 900);
      } catch {
        showToast('Erro ao salvar.', 'error');
      }
    },

    async delete(id) {
      if (!confirm('Deseja excluir este slide?')) return;
      try {
        const res = await apiRequest(`api/slides.php?id=${id}`, 'DELETE');
        showToast(res.message || '🗑 Removido.');
        setTimeout(() => location.reload(), 900);
      } catch {
        showToast('Erro ao excluir.', 'error');
      }
    }
  };

  w.openSlideModal = (id) => SlideForm.open(id);
  w.saveSlide      = ()   => SlideForm.save();
  w.deleteSlide    = (id) => SlideForm.delete(id);


  /* ===================================================
     INIT
  =================================================== */
  d.addEventListener('DOMContentLoaded', () => {
    Slider.init();
  });

}(window, document));
