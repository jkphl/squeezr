const gulp = require('gulp');
const uglify = require('gulp-uglify');
const rename = require('gulp-rename');

gulp.task('uglify', () => {
    gulp.src('squeezr/squeezr.js')
        .pipe(uglify())
        .pipe(rename((path) => {
            path.basename += '.min';
        })).pipe(gulp.dest('squeezr'));
});

gulp.task('watch', () => {
    gulp.watch('squeezr/squeezr.js', ['uglify']);
});

gulp.task('default', function () {
    // place code for your default task here
});
