var gulp = require('gulp');
var debug = require('gulp-debug');
var runSequence = require('run-sequence');
require('gulp-stats')(gulp);

var gutil = require('gulp-util');
var plumber = require("gulp-plumber");
var path = require('path');

/* project paths */
var paths = require("./package.json").paths;

/**
 * Copy images Tasks
 */
gulp.task('copy', function () {
    return gulp.src(paths.assets + 'images/*')
        .pipe(plumber())
        .pipe(require('gulp-copy')(paths.dist + '/images/', {prefix:5}));
});

/**
 * Style Tasks
 */

gulp.task('styles', function () {

    var sass = require('gulp-sass');

    var processors = [
        require('autoprefixer')({browsers: ['> 5%']}), /* auto prefix */
    ];

    return gulp.src(paths.assets + 'styles/app.scss')
        .pipe(plumber())
        .pipe(sass({outputStyle: 'expanded'}).on('error', sass.logError))
        .pipe(require('gulp-strip-css-comments')())
        .pipe(require('gulp-postcss')(processors))
        /* gulp-combine-mq se charge de minifier */
        .pipe(require('gulp-combine-mq')({beautify: false}))
        .pipe(gulp.dest(paths.dist + 'styles'));
});

/**
 * Generate png sprite
 */

gulp.task('sprite', function () {

  return gulp.src(paths.assets + 'images/sprite-icon/*.png')
    .pipe(require('gulp.spritesmith')({
        imgName: 'sprite-icon.png',
        imgPath: '../images/sprite-icon.png',
        cssName: '../styles/_sprite-icon.data.scss',
        retinaSrcFilter : paths.assets + 'images/sprite-icon/*@2x.png',
        retinaImgName: '../images/sprite@2x.png'
      }))
    .pipe(gulp.dest(paths.assets + 'images' ));
});

/**
 * scripts managment
 */

gulp.task('scripts', function() {

  var webpack =  require('webpack');
  var webpackStream =  require('webpack-stream');

  return gulp.src(paths.assets + 'scripts/main.js')
    .pipe(webpackStream({
        resolve: {
            /* la on indique ou allez chercher les futurs appels require() */
            modulesDirectories: ['Resources/assets/scripts/modules','node_modules']
        },
        plugins: [
            new webpack.ProvidePlugin({
                "$": "jquery",
                "jQuery": "jquery",
                "_": "underscore"
            }),
            /* la on ne prend que les data FR du plugin moment.js */
            new webpack.ContextReplacementPlugin(/moment[\/\\]locale$/, /fr/)
            /* a activer pour mminifuier le js */
            // ,new webpack.optimize.UglifyJsPlugin()
        ],
        module : {
            loaders: [
                /* la on expose jQuery et $ à window */
                { test: require.resolve("jquery"), loader: "expose?$!expose?jQuery" }
            ]
        },
        output: {
            filename: 'bundle.js'
        }
    }))
    .pipe(gulp.dest(paths.dist + 'scripts'));
});

gulp.task('fonts', function(){
    return gulp.src(paths.assets +'fonts/**/*')
  .pipe(gulp.dest(paths.dist + 'fonts'))

})

/**
 * watch task
 */
var watchTasks = function() {
    gulp.watch([paths.assets + "styles/**/*.scss"], function(){
        setTimeout(function () {
            gulp.start('styles');
        }, 1000);
    });

    gulp.watch([paths.assets + "scripts/**/*.js"], function(){
        setTimeout(function () {
            gulp.start('scripts');
        }, 1000);
    });
}

gulp.task('default', function(callback){

    runSequence(
        'scripts',
        'sprite',
        'styles',
        'copy',
		'fonts',
        callback
    );

})

gulp.task('w', ['default'], watchTasks);
