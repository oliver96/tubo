define(
    [
        'jquery'
        , 'underscore'
        , 'backbone'
    ]
    , function($, _, Backbone) {
        var Router = Backbone.Router.extend({
            routes: {
                '': 'showPage'
                , '*actions': 'loadModule'
            }
            , 'showPage': function() {
                console.log("showPage");
            }
            , 'loadModule': function(actions) {
                var tmpAry = actions.split('/');
                console.log(actions);
                var requires = [];
                requires.push('views/' + tmpAry[0] + '/' + tmpAry[1]);
                require(requires);
            }
        });
        
        var initialize = function() {
            var appRouter = new Router();
            Backbone.history.start();
        }
        
        return {'initialize': initialize};
    }
);
