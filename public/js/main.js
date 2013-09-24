require.config({
    'baseUrl': App.root + '/public/js'
    , 'urlArgs': "t=" + new Date().getTime()
    , 'paths': {
        'jquery': 'libs/jquery/jquery'
        , 'underscore': 'libs/underscore/underscore'
        , 'backbone': 'libs/backbone/backbone'
    }
    , 'shim': {
        'jquery' : {
            'exports': '$'
        }
        , 'underscore' : {
            'exports': '_'
        }
        , 'backbone': {
            'deps': ['underscore', 'jquery']
            , 'exports': 'Backbone'
        }
    }
});

require(['app'], function(App) {
    App.initialize();
});
