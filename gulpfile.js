var gulp = require('gulp'),
    sass = require('gulp-sass'),
    rename = require('gulp-rename'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglify'),
    es = require('event-stream'),
    autoprefixer = require('gulp-autoprefixer');

var sassConfig = {
    inputDirectory: 'style/sass/**/*.scss',
    outputDirectory: 'style/css',
    autoprefixer: {
        browsers: ['last 2 versions'],
        cascade: false
    }
};

var jsConfig = {
    inputDirectory: 'js/src/**/*.js',
    outputDirectory: 'js/dist'
};

gulp.task('sass', function() {
    var expanded = gulp
        .src('style/sass/style.scss')
        .pipe(autoprefixer(sassConfig.autoprefixer).on('error', function (error) {
            this.emit('end')
        }))
        .pipe(sass({outputStyle: 'expanded'}).on('error', function (error) {
            this.emit('end')
        }))
        .pipe(rename('style.css'))
        .pipe(gulp.dest(sassConfig.outputDirectory));

    var minified = gulp
        .src('style/sass/style.scss')
        .pipe(autoprefixer(sassConfig.autoprefixer).on('error', sass.logError))
        .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
        .pipe(rename('style.min.css'))
        .pipe(gulp.dest(sassConfig.outputDirectory));

    return es.concat(expanded, minified);
});

gulp.task('js', function(){
    return gulp
        .src(jsConfig.inputDirectory)
        .pipe(concat('main.js'))
        .pipe(gulp.dest(jsConfig.outputDirectory))
        .pipe(rename('main.min.js'))
        .pipe(uglify().on('error', function(uglify) {
            console.error(uglify.message);
            this.emit('end');
        }))
        .pipe(gulp.dest(jsConfig.outputDirectory));
});

gulp.task('watch', function() {
    gulp.watch(sassConfig.inputDirectory, ['sass']);
    gulp.watch(jsConfig.inputDirectory, ['js']);
});

gulp.task('default', function () {
   gulp.start('watch');
});