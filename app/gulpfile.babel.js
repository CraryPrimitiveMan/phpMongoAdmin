import gulp from 'gulp';
import react from 'gulp-react';
import htmlreplace from 'gulp-html-replace';
import source from 'vinyl-source-stream';
import browserify from 'browserify';
import watchify from 'watchify';
import reactify from 'reactify';
import streamify from 'gulp-streamify';
import less from 'gulp-less';
import LessPluginCleanCSS from 'less-plugin-clean-css';
import minifyCSS from 'gulp-minify-css';
import sourcemaps from 'gulp-sourcemaps';
import clean from 'gulp-clean';
import connect from 'gulp-connect';

var cleancss = new LessPluginCleanCSS({ advanced: true });

/**
 * Global variables
 */

var path = {
  HTML: 'src/index.html',
  JS: ['src/js/*.js', 'src/js/**/*.js'],
  LESS_FILES: ['src/less/*.less', 'src/less/**/*.less'],
  LESS: 'src/less/app.less',
  ENTRY_POINT: 'src/js/app.js',
  OUT: 'build.js',
  MINIFIED_OUT: 'build.min.js',
  RES_DEST: 'dist',
  INDEX_DEST: './'
};

path.ALL = path.JS.concat(path.HTML);

/**
 * Development tasks
 */

//Copy the index.html to the top folder
gulp.task('copy', () =>
  gulp.src(path.HTML)
    .pipe(gulp.dest(path.INDEX_DEST))
    .pipe(connect.reload())
);

gulp.task('less', () =>
  gulp.src(path.LESS)
    .pipe(less())
    .pipe(gulp.dest(path.RES_DEST))
    .pipe(connect.reload())
);

/*//Transform the JSX to JS
gulp.task('transform', () =>
    gulp.src(path.JS)
        .pipe(react())
        .pipe(gulp.dest(path.RES_DEST))
);

//Watch for the file changed
gulp.task('watch', function(){
  gulp.watch(path.ALL, ['transform', 'copy']);
});
*/

gulp.task('connect', () =>
  connect.server({
    root: './',
    livereload: true,
    port: 8001
  })
);

gulp.task('watch', function() {
  gulp.watch(path.HTML, ['copy']);
  gulp.watch(path.LESS_FILES, ['less']);

  var watcher  = watchify(browserify({
    entries: [path.ENTRY_POINT],
    transform: [reactify],
    debug: true,
    cache: {}, packageCache: {}, fullPaths: true
  }));

  return watcher.on('update', function () {
    watcher.bundle()
      .pipe(source(path.OUT))
      .pipe(gulp.dest(path.RES_DEST))
      .pipe(connect.reload())
      console.log('Updated');
  })
    .bundle()
    .pipe(source(path.OUT))
    .pipe(gulp.dest(path.RES_DEST));
});

gulp.task('default', ['connect', 'watch', 'copy', 'less']);


/**
 * Production tasks
 */

/*//Handle common JS transformations
gulp.task('build', () =>
  gulp.src(path.JS)
    .pipe(react())
    .pipe(concat(path.MINIFIED_OUT))
    .pipe(uglify(path.MINIFIED_OUT))
    .pipe(gulp.dest(path.RES_DEST))
);*/

gulp.task('clean', () =>
  gulp.src(path.RES_DEST, {read: false})
    .pipe(clean())
);

gulp.task('build', () =>
  browserify({
    entries: [path.ENTRY_POINT],
    transform: [reactify]
  })
    .bundle()
    .pipe(source(path.MINIFIED_OUT))
    .pipe(streamify(uglify(path.MINIFIED_OUT)))
    .pipe(gulp.dest(path.RES_DEST))
);

gulp.task('buildStyles', () =>
  gulp.src(path.LESS)
    .pipe(sourcemaps.init())
    .pipe(less({
      plugins: [cleancss]
    }))
    .pipe(minifyCSS())
    .pipe(sourcemaps.write())
    .pipe(gulp.dest(path.RES_DEST))
);

//Replace the multiple reference in html file to one
gulp.task('replaceHTML', () =>
  gulp.src(path.HTML)
    .pipe(htmlreplace({
      'js': 'dist/' + path.MINIFIED_OUT
    }))
    .pipe(gulp.dest(path.DEST))
);

gulp.task('production', ['clean', 'replaceHTML', 'build', 'buildStyles']);