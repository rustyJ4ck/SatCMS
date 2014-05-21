/**
 * i18n
 * i18n.T(string)
 */

define(['jquery', 'app'],

    function ($, app) {

        'use strict';

        var data;

        if (!data) {

            //app.urls.toRoot
            $.get(('/core/api/editor/i18n/'))
                .success(function(_data){
                    console.log('i18n loaded');
                    data = _data;
                });
        }

        return {

            getData: function(){return data;},

            T: function(v, section, params) {

                var result;

                if (v.indexOf('.') !== -1 /*(v.contains('.')*/) {

                    var phrase = section ? (section + '.' + v) : v;
                    var keys = phrase.split('.');

                    if (keys.count() == 3)
                        result = data[keys[0]][keys[1]][keys[2]];
                    else
                        result = data[keys[0]][keys[1]];
                }
                else {
                    result = data[v];
                }

                result = result ? result : v;

                return result;
            }

        }

    }

);