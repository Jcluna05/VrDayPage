/*!
 * gulp
 * $ npm install gulp-sass gulp-autoprefixer gulp-cssnano gulp-jshint gulp-concat gulp-uglify gulp-imagemin gulp-notify gulp-rename gulp-livereload gulp-cache del --save-dev
 */

// Load plugins
var gulp = require('gulp'),
    sass = require('gulp-sass'),
    autoprefixer = require('gulp-autoprefixer'),
    cssnano = require('gulp-cssnano'),
    //jshint = require('gulp-jshint'),
    uglify = require('gulp-uglify'),
    rename = require('gulp-rename'),
    concat = require('gulp-concat'),
    notify = require('gulp-notify'),
    livereload = require('gulp-livereload'),
    del = require('del'),
    rtlcss = require('gulp-rtlcss');

sass.compiler = require('node-sass');

// Styles
gulp.task('styles', function() {
  return gulp.src('assets/scss/*.scss')
  	.pipe(sass().on('error', sass.logError))
    .pipe(autoprefixer({browsers: 'last 3 versions', remove: false}))
    .pipe(gulp.dest('assets/css'))
    .pipe(rename({ suffix: '.min' }))
    .pipe(cssnano({safe: true, zindex: false }))
    .pipe(gulp.dest('assets/css'))
    .pipe(notify({ message: 'Styles task complete' }));
});

gulp.task('rtl-styles', function() {
    return gulp.src(['assets/scss/*.scss', '!assets/scss/slickmenu.scss'])
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer({browsers: 'last 3 versions', remove: false}))
        .pipe(rename({ suffix: '-rtl' }))
        .pipe(rtlcss())
        .pipe(gulp.dest('assets/css'))
        .pipe(rename({ suffix: '.min' }))
        .pipe(cssnano({safe: true, zindex: false }))
        .pipe(gulp.dest('assets/css'))
        .pipe(notify({ message: 'RTL Styles task complete' }));
});

// Welcome Styles
gulp.task('welcome-styles', function() {
    return gulp.src('admin/welcome/assets/scss/*.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer({browsers: 'last 3 versions', remove: false}))
        .pipe(gulp.dest('admin/welcome/assets/css'))
        .pipe(cssnano({safe: true, zindex: false }))
        .pipe(gulp.dest('admin/welcome/assets/css'))
        .pipe(notify({ message: 'Welcome Styles task complete' }));
});

// License Styles
gulp.task('license-styles', function() {
    return gulp.src('includes/license/assets/scss/*.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer({browsers: 'last 3 versions', remove: false}))
        .pipe(gulp.dest('includes/license/assets/css'))
        .pipe(cssnano({safe: true, zindex: false }))
        .pipe(gulp.dest('includes/license/assets/css'))
        .pipe(notify({ message: 'Welcome Styles task complete' }));
});

// Theme Fixes Styles
gulp.task('theme-fix-styles', function() {
  return gulp.src('assets/theme-fix/scss/*.scss')
  	.pipe(sass().on('error', sass.logError))
    .pipe(autoprefixer({browsers: 'last 3 versions', remove: false}))
    .pipe(gulp.dest('assets/theme-fix/css'))
    .pipe(rename({ suffix: '.min' }))
    .pipe(cssnano({safe: true, zindex: false }))
    .pipe(gulp.dest('assets/theme-fix/css'))
    .pipe(notify({ message: 'Theme Fix Styles task complete' }));
});

gulp.task('theme-fix-rtl-styles', function() {
    return gulp.src('assets/theme-fix/scss/*.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer({browsers: 'last 3 versions', remove: false}))
        .pipe(rename({ suffix: '-rtl' }))
        .pipe(rtlcss())
        .pipe(gulp.dest('assets/theme-fix/css'))
        .pipe(rename({ suffix: '.min' }))
        .pipe(cssnano({safe: true, zindex: false }))
        .pipe(gulp.dest('assets/theme-fix/css'))
        .pipe(notify({ message: 'Theme Fix Styles task complete' }));
});

// MbNavStyles
gulp.task('mbnav-styles', function() {
  return gulp.src('includes/modules/sm-mb-nav-menu/scss/*.scss')
  	.pipe(sass().on('error', sass.logError))
    .pipe(autoprefixer({browsers: 'last 3 versions', remove: false}))
    .pipe(gulp.dest('includes/modules/sm-mb-nav-menu/css'))
    .pipe(rename({ suffix: '.min' }))
    .pipe(cssnano({safe: true, zindex: false }))
    .pipe(gulp.dest('includes/modules/sm-mb-nav-menu/css'))

    .pipe(notify({ message: 'MB Nav Styles task complete' }));
});

gulp.task('mbnav-rtl-styles', function() {
    return gulp.src('includes/modules/sm-mb-nav-menu/scss/*.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer({browsers: 'last 3 versions', remove: false}))
        .pipe(rename({ suffix: '-rtl' }))
        .pipe(rtlcss())
        .pipe(gulp.dest('includes/modules/sm-mb-nav-menu/css'))
        .pipe(rename({ suffix: '.min' }))
        .pipe(cssnano({safe: true, zindex: false }))
        .pipe(gulp.dest('includes/modules/sm-mb-nav-menu/css'))
        .pipe(notify({ message: 'MB Nav RTL Styles task complete' }));
});

// Vendors Scripts
gulp.task('utils', function() {
  return gulp.src(["./assets/utils/*.js"])
  	.pipe(concat('utils.js'))
    .pipe(gulp.dest('assets/js'))
    .pipe(notify({ message: 'Utils task complete' }));
});

// Scripts
gulp.task('scripts', function() {
  return gulp.src(["./assets/js/*", "!./assets/js/*.min.js"])
    .pipe(gulp.dest('assets/js'))
    .pipe(rename({ suffix: '.min' }))
    .pipe(uglify())
    .pipe(gulp.dest('assets/js'))
    .pipe(notify({ message: 'Scripts task complete' }));
});

// Welcome Scripts
gulp.task('welcome-scripts', function() {
    return gulp.src(["./admin/welcome/assets/js/*", "!./admin/welcome/assets/js/*-min.js"])
        .pipe(gulp.dest('admin/welcome/assets/js'))
        .pipe(rename({ suffix: '-min' }))
        .pipe(uglify())
        .pipe(gulp.dest('admin/welcome/assets/js'))
        .pipe(notify({ message: 'Welcome Scripts task complete' }));
});

// License Scripts
gulp.task('license-scripts', function() {
    return gulp.src(["./includes/license/assets/js/*", "!./includes/license/assets/js/*-min.js"])
        .pipe(gulp.dest('includes/license/assets/js'))
        .pipe(rename({ suffix: '-min' }))
        .pipe(uglify())
        .pipe(gulp.dest('includes/license/assets/js'))
        .pipe(notify({ message: 'Scripts task complete' }));
});

// Theme Fixes Scripts
gulp.task('theme-fix-scripts', function() {
  return gulp.src(["./assets/theme-fix/js/*", "!./assets/theme-fix/js/*.min.*"])
    .pipe(gulp.dest('assets/theme-fix/js'))
    .pipe(rename({ suffix: '.min' }))
    .pipe(uglify())
    .pipe(gulp.dest('assets/theme-fix/js'))
    .pipe(notify({ message: 'Theme Fixes Scripts task complete' }));
});


// MbNavScripts
gulp.task('mbnav-scripts', function() {
  return gulp.src(["./includes/modules/sm-mb-nav-menu/js/*", "!./includes/modules/sm-mb-nav-menu/js/*.min.js"])
    .pipe(gulp.dest('includes/modules/sm-mb-nav-menu/js'))
    .pipe(rename({ suffix: '.min' }))
    .pipe(uglify())
    .pipe(gulp.dest('includes/modules/sm-mb-nav-menu/js'))
    .pipe(notify({ message: 'MB Nav Scripts task complete' }));
});


// Clean
gulp.task('clean', function() {
  return del([
  	'assets/css/*.css', 
  	'includes/modules/sm-mb-nav-menu/css/*.css', 
  	'assets/js/*.min.js',
  	'includes/modules/sm-mb-nav-menu/js/*.min.js'
  ]);
});

// Default task
gulp.task('default', gulp.series('clean', 'styles', 'rtl-styles', 'welcome-styles', 'license-styles', 'theme-fix-styles', 'theme-fix-rtl-styles', 'mbnav-styles', 'mbnav-rtl-styles', 'utils', 'scripts', 'welcome-scripts', 'license-scripts', 'mbnav-scripts'));

// Watch
gulp.task('watch', function() {

  // Watch .scss files
  gulp.watch('assets/scss/**/*.scss', gulp.series('styles', 'rtl-styles', 'theme-fix-styles'));
  gulp.watch('admin/welcome/assets/scss/**/*.scss', gulp.series('welcome-styles'));
  gulp.watch('includes/license/assets/scss/**/*.scss', gulp.series('license-styles'));
  gulp.watch('assets/theme-fix/scss/*.scss', gulp.series('theme-fix-styles', 'theme-fix-rtl-styles'));
  
  gulp.watch('includes/modules/sm-mb-nav-menu/scss/*.scss', gulp.series('mbnav-styles', 'mbnav-rtl-styles'));

  gulp.watch(["./assets/utils/*.js"], gulp.series('utils'));

  gulp.watch(["assets/js/*.js", "!assets/js/*.min.js"], gulp.series('scripts'));
  gulp.watch(["admin/welcome/assets/js/*.js", "!admin/welcome/assets/js/*-min.js"], gulp.series('welcome-scripts'));
  gulp.watch(["includes/license/assets/js/*.js", "!includes/license/assets/js/*-min.js"], gulp.series('license-scripts'));

  gulp.watch(["./assets/theme-fix/js/*.js", "!./assets/theme-fix/js/*.min.js"], gulp.series('theme-fix-scripts'));

  gulp.watch(["./includes/modules/sm-mb-nav-menu/js/*", "!./includes/modules/sm-mb-nav-menu/js/*.min.js"], gulp.series('mbnav-scripts'));
  
  // Create LiveReload server
  livereload.listen();

  // Watch any files in dist/, reload on change
  gulp.watch(['css/**']).on('change', livereload.changed);

});


gulp.on('uncaughtException', function(err) {
 	console.log(err);
 	server.kill();
 	process.kill();
});