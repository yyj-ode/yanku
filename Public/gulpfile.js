//gulpfile.js
// 载入外挂
var gulp = require('gulp'),
    sass = require('gulp-sass'),
    autoprefixer = require('gulp-autoprefixer'),    //使用gulp-autoprefixer根据设置浏览器版本自动处理浏览器前缀。使用她我们可以很潇洒地写代码，不必考虑各浏览器兼容前缀
    minifycss = require('gulp-minify-css'),         //使用gulp-minify-css是用来css代码压缩
    jshint = require('gulp-jshint'),                //使用gulp-jshint是用来检测javascript的语法错误的
    uglify = require('gulp-uglify'),                //使用gulp-uglify压缩javascript文件，减小文件大小
    imagemin = require('gulp-imagemin'),            //使用gulp-imagemin 压缩图片
    rename = require('gulp-rename'),                //使用gulp-rename重命名
    clean = require('gulp-clean'),                  //前面说过，当css，img，js出现删除操作的时候，虽然watch会监听，但是并不会删除相应文件。现在实现clean任务，执行任务前先删除一次build目录。
    //concat = require('gulp-concat'),              //使用gulp-concat合并javascript文件，减少网络请求。
    notify = require('gulp-notify'),                // 报错处理
    cache = require('gulp-cache'),                  // gulp-cache 监控到图片被改变了，替换了，才去压缩。
    //livereload = require('gulp-livereload'),
    browserSync = require('browser-sync').create(), //browser-sync监听所有文件并且对浏览器进行刷新
    reload       = browserSync.reload;

// 样式
gulp.task('styles', function() {
    return gulp.src('scss/**/*.scss')
        .pipe(sass({ style: 'expanded'}))
        .pipe(autoprefixer('last 2 version', 'safari 5', 'ie 8', 'ie 9', 'opera 12.1', 'ios 6', 'android 4'))
        .pipe(gulp.dest('dist/styles'))
        .pipe(rename({ suffix: '.min' }))
        .pipe(minifycss())
        .pipe(gulp.dest('dist/styles'))
        .pipe(notify({ message: 'Styles task complete' }))
        //.pipe(browserSync.stream());
        // 刷新浏览器, 以下同理
        .pipe(reload({stream: true}));
});

// 脚本
gulp.task('scripts', function() {
    return gulp.src('js/**/*.js')
        .pipe(jshint('.jshintrc'))
        .pipe(jshint.reporter('default'))
        //.pipe(concat('main.js'))
        .pipe(gulp.dest('dist/scripts'))
        .pipe(rename({ suffix: '.min' }))
        .pipe(uglify())
        .pipe(gulp.dest('dist/scripts'))
        .pipe(notify({ message: 'Scripts task complete' }))
        // 刷新浏览器, 以下同理
        .pipe(reload({stream: true}));
});

// 图片
gulp.task('images', function() {
    return gulp.src('src/images/**/*')
        .pipe(cache(imagemin({ optimizationLevel: 3, progressive: true, interlaced: true })))
        .pipe(gulp.dest('dist/images'))
        .pipe(notify({ message: 'Images task complete' }))
        // 刷新浏览器, 以下同理
        .pipe(reload({stream: true}));
});

// 清理
gulp.task('clean', function() {
    return gulp.src(['dist/styles', 'dist/scripts', 'dist/images'], {read: false})
        .pipe(clean());
});

// 看手
gulp.task('watch', function() {
    browserSync.init({
        proxy: '10.10.16.64:8000'
    });
    // 看守所有.scss档
    gulp.watch('scss/**/*.scss', ['styles']);
    // 看守所有.js档
    gulp.watch('js/**/*.js', ['scripts']);
    // 看守所有图片档
    gulp.watch('src/images/**/*', ['images']);
    // 建立即时重整伺服器
    //var server = livereload();

    // 看守所有位在 dist/  目录下的档案，一旦有更动，便进行重整
    //gulp.watch(['dist/**']).on('change', function(file) {
    //    server.changed(file.path);
    //});

});
