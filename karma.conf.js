module.exports = function(config) {
    config.set({
        frameworks: ['browserify','jasmine'],
        reporters: ['dots'],
        browsers: ['Chrome'],
        files: [
            'node_modules/jquery/dist/jquery.min.js',
            'tests/client/file_browser/**/*Spec.js'
        ],
        preprocessors: {
            'tests/client/file_browser/**/*Spec.js': [ 'browserify' ]
        },
        browserify: {
            debug: true,
            transform: [ 'brfs' ]
        },
        // plugins to load
        plugins: [
          'karma-jasmine',
          'karma-browserify',
          'karma-chrome-launcher'
        ]
    });
};