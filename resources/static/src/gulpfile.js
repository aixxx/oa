var gulp = require('gulp');
var gulpFilter = require('gulp-filter');
var fs = require('fs');
$ = require('gulp-load-plugins')();
var argv = require('yargs').argv;
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');
var minifyCSS = require('gulp-minify-css');
var sass = require('gulp-sass');
var cssnano = require('gulp-cssnano');
var sourcemaps = require('gulp-sourcemaps');
var autoprefixer = require('autoprefixer');
var postcss = require('gulp-postcss');

// COMPILE BOOTSTRAP FILES
gulp.task('bootstrap', function() {
  return gulp.src('sass/bootstrap/bootstrap.scss')
    .pipe(sourcemaps.init())
    .pipe(sourcemaps.write())
    .pipe(sass.sync().on('error', sass.logError))
    .pipe(sass({
      includePaths: ['sass/bootstrap']
    }))
    .pipe(postcss([autoprefixer()]))
    .pipe(gulp.dest('../../../public/static/css/vendor'))
    .pipe(rename('bootstrap.css'))
    .pipe(minifyCSS())
    .pipe(gulp.dest('../../../public/static/css/vendor'));
});
// CREATE SASS MAIN BUNDLE FILES
gulp.task('sass', function() {
  return gulp.src('sass/common/main.scss')
    .pipe(sourcemaps.init())
    .pipe(sourcemaps.write())
    .pipe(sass.sync().on('error', sass.logError))
    .pipe(sass({
      includePaths: ['sass']
    }))
    .pipe(postcss([autoprefixer()]))
    .pipe(gulp.dest('../../../public/static/css/common'))
    .pipe(rename('main.bundle.css'))
    .pipe(minifyCSS())
    .pipe(gulp.dest('../../../public/static/css/common'));
});
// CREATE DEMO VERTICAL BUNDLE FILES
gulp.task('verticalLayout', function() {
  return gulp.src('sass/layouts/vertical/core/*')
    .pipe(sass.sync().on('error', sass.logError))
    .pipe(sass({
      includePaths: ['sass/layouts/vertical/core']
    }))
    .pipe(gulp.dest('../../../public/static/css/layouts/vertical/core'))
    .pipe(minifyCSS())
    .pipe(gulp.dest('../../../public/static/css/layouts/vertical/core'));
});

// CREATE BUNDLES FOR VERTICAL MENU TYPES
gulp.task('verticalLayoutMenu', function() {
  return gulp.src('sass/layouts/vertical/menu-type/*')
    .pipe(sass.sync().on('error', sass.logError))
    .pipe(sass({
      includePaths: ['sass/layouts/vertical/menu-type']
    }))
    .pipe(gulp.dest('../../../public/static/css/layouts/vertical/menu-type'))
    .pipe(minifyCSS())
    .pipe(gulp.dest('../../../public/static/css/layouts/vertical/menu-type'));
});

// CREATE THEME FILES FOR VERTICAL LAYOUT
gulp.task('verticalThemes', function() {
  return gulp.src('sass/layouts/vertical/themes/*')
    .pipe(sass.sync().on('error', sass.logError))
    .pipe(sass({
      includePaths: ['sass/layouts/vertical/themes']
    }))
    .pipe(gulp.dest('../../../public/static/css/layouts/vertical/themes'))
    .pipe(minifyCSS())
    .pipe(gulp.dest('../../../public/static/css/layouts/vertical/themes'));
});
//ADD WATCH
gulp.task('watch', function() {
  gulp.watch('sass/**/*.scss', ['bootstrap','sass', 'verticalLayout','verticalLayoutMenu','verticalThemes']);
});

//ERROR HANDELING
function errorAlert(err) {
  console.log(err.toString());
  this.emit("end");
}

gulp.task('default', ['bootstrap','sass','verticalLayout','verticalLayoutMenu','verticalThemes','watch']);
