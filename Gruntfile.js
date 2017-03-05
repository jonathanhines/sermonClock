module.exports = function(grunt) {
  require('jit-grunt')(grunt);

  grunt.loadNpmTasks('grunt-reload');

  grunt.initConfig({
    less: {
      development: {
        options: {
          compress: false,
          yuicompress: false,
          optimization: 2,
          sourceMap: true,
          sourceMapFilename: 'css/style.css.map', // where file is generated and located
          sourceMapURL: '/css/style.css.map', // the complete url and filename put in the compiled css file
          sourceMapRootpath: '/', // adds this path onto the sourcemap filename and less file paths
        },
        files: {
          "css/style.css": "less/style.less" // destination file and source file
        }
      },
      production: {
        options: {
          compress: true,
          yuicompress: true,
          optimization: 2
        },
        files: {
          "css/style.min.css": "less/style.less" // destination file and source file
        }
      }
    },
    reload: {
      port: 6001
    },
    watch: {
      styles: {
        files: ['index.html','less/**/*.less','js/**/*.js'], // which files to watch
        tasks: ['less:development', 'reload'],
        options: {
          nospawn: true
        }
      }
    }
  });

  grunt.registerTask('default', ['less:development', 'reload', 'watch']);
  grunt.registerTask('prod', ['less:production']);
};
