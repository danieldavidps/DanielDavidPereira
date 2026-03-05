/**
 * LEO Platform — Gulpfile
 * Automatizador de tarefas com Gulp 4
 *
 * Instalar dependências:
 *   npm install
 *
 * Comandos disponíveis:
 *   gulp        → build completo + watch
 *   gulp build  → build único (sass + js)
 *   gulp sass   → compila SCSS
 *   gulp js     → concatena e minifica JS
 *   gulp watch  → observa mudanças
 *   gulp serve  → BrowserSync (requer servidor PHP rodando na porta 8000)
 */

'use strict';

const gulp        = require('gulp');
const sass        = require('gulp-sass')(require('sass'));
const cleanCSS    = require('gulp-clean-css');
const sourcemaps  = require('gulp-sourcemaps');
const autoprefixer= require('gulp-autoprefixer');
const uglify      = require('gulp-uglify');
const concat      = require('gulp-concat');
const browserSync = require('browser-sync').create();

// ── Paths ────────────────────────────────────
const paths = {
  scss: {
    src:  'src/scss/main.scss',
    watch:'src/scss/**/*.scss',
    dest: 'public/assets/css'
  },
  js: {
    src:  'src/js/**/*.js',
    dest: 'public/assets/js'
  }
};

// ── SASS → CSS ───────────────────────────────
function taskSass() {
  return gulp.src(paths.scss.src)
    .pipe(sourcemaps.init())
    .pipe(sass({ outputStyle: 'expanded' }).on('error', sass.logError))
    .pipe(autoprefixer({ cascade: false }))
    .pipe(cleanCSS({ level: 2 }))
    .pipe(sourcemaps.write('./maps'))
    .pipe(gulp.dest(paths.scss.dest))
    .pipe(browserSync.stream());
}

// ── JS ───────────────────────────────────────
function taskJs() {
  return gulp.src(paths.js.src)
    .pipe(sourcemaps.init())
    .pipe(concat('main.js'))
    .pipe(uglify())
    .pipe(sourcemaps.write('./maps'))
    .pipe(gulp.dest(paths.js.dest))
    .pipe(browserSync.stream());
}

// ── BrowserSync ──────────────────────────────
function taskServe(done) {
  browserSync.init({
    proxy: 'localhost:8000',  // php -S localhost:8000 -t public/
    notify: false,
    open: true
  });
  done();
}

// ── Watch ────────────────────────────────────
function taskWatch() {
  gulp.watch(paths.scss.watch, taskSass);
  gulp.watch(paths.js.src,     taskJs);
  gulp.watch('public/**/*.php').on('change', browserSync.reload);
}

// ── Exports ──────────────────────────────────
const build = gulp.parallel(taskSass, taskJs);

exports.sass  = taskSass;
exports.js    = taskJs;
exports.build = build;
exports.watch = taskWatch;
exports.serve = gulp.series(build, taskServe, taskWatch);
exports.default = gulp.series(build, taskWatch);
