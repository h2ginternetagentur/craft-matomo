const gulp = require('gulp');
const sass = require('gulp-sass')(require('node-sass'));
const minify = require('gulp-minify');
const rename = require('gulp-rename');
let assetPath = '../';

sass.compiler = require('node-sass');

/**
 * js
 */
function js() {
    return gulp.src('scripts/**/*.js')
        .pipe(minify({
            ext: {
                src: '.js',
                min: '.min.js'
            },
            noSource: true
        }))
        .pipe(gulp.dest(assetPath + 'js'))
}

/**
 * scss
 */
function scss() {
    return gulp.src('styles/**/*.scss')
        .pipe(sass({
            outputStyle: 'compressed',
        }).on('error', sass.logError))
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(gulp.dest(assetPath + 'css'))
}

/**
 * fonts
 */
function fonts() {
    return gulp.src('fonts/**')
        .pipe(gulp.dest(assetPath + 'fonts'))
}

/**
 * svgs
 */
function svgs() {
    return gulp.src('svgs/**/*.svg')
        .pipe(gulp.dest(assetPath + 'svgs'))
}

/**
 * libs
 */
function libs() {
    return gulp.src([
        'node_modules/iframe-resizer/js/iframeResizer.min.js'
    ])
        .pipe(gulp.dest(assetPath + 'js'))
}

/**
 * watch
 */
function watch() {
    gulp.watch(['styles/**', 'scripts/**', 'fonts/**', 'svgs/**'], gulp.parallel(js, scss, fonts, svgs));
}

/**
 * exports
 */
exports.js = js;
exports.scss = scss;
exports.fonts = fonts;
exports.svgs = svgs;
exports.libs = libs;
exports.default = gulp.parallel(js, scss, fonts, svgs, libs);
exports.watch = watch;
