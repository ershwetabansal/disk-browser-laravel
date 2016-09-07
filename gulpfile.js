var gulp = require('gulp');
var sass = require('gulp-sass');
var browserify = require('browserify');
var source = require('vinyl-source-stream');
var runSequence = require('run-sequence');


gulp.task('heroku:production', function() {
    return gulp.src('node_modules/disk-browser/dist/**/*')
    // Start piping stream to tasks!
        .pipe(gulp.dest('public/app/build/disk-browser'));
});