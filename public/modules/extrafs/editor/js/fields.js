define(['plugins/editor'], function (ed) {

    var extrafs = {

        fields: {

            'numeric': {
                'title': 'Число',
                'params': {
                    'float': {title: 'Дробное', value: '<input type="checkbox" name="value[float]" value="1"/>'},
                    'format': {title: 'Формат', value: '<input type="text"    name="value[format]"   value=""/>'},
                    'precision': {title: 'Точность (знаков)', value: '<input type="text"    name="value[precision]"   value=""/>'},
                    'unit': {title: 'Ед.измер', value: '<input type="text"  name="value[unit]"   value=""/>'}
                }
            },

            'text': {
                'title': 'Строка',
                'params': {
                    'size': {title: 'Ширина', value: '<input type="text"    name="value[size]"   value=""/>'},
                    'rows': {title: 'Высота', value: '<input type="text"    name="value[rows]"   value=""/>'},
                    'wysiwyg': {title: 'wysiwyg', value: '<input type="checkbox"  name="value[wysiwyg]"   value="1"/>'}
                    //       , 'format':{title: 'Формат', value: '<input type="text"    name="value[format]" value=""/>'}
                    , 'allow_html': {title: 'Разрешить HTML', value: '<input type="checkbox"  name="value[allow_html]"   value="1"/>'}
                }

            },

            'link': {
                'title': 'Ссылка',
                'params': {
                    'target': {title: 'Назначение', value: '<input type="text"    name="value[target]"   value=""/>'}
                }

            },

            'datetime': {
                'title': 'Дата',
                'params': {
                    'format': {title: 'Формат', value: '<input type="text"    name="value[format]"   value=""/>'}
                }

            },

            'select': {
                'title': 'Список (select)',
                'params': {
                    'values': {title: 'Значения', value: '<textarea cols="50" rows="5" name="value[options]"></textarea>'}
                }
            },

            'boolean': {
                'title': 'Флажок',
                'params': {}
            },

            'file': {
                'title': 'Файл',
                'params': {
                    'storage': {title: 'Сохранить в', value: '<input type="text"      size="50"  name="value[storage]"   value="extra"/>', description: '/uploads/{path}/'},
                    'spacing': {title: 'Spacing', value: '<input type="text"    name="value[spacing]"   value=""/>'},
                    'title': {title: 'Подсказка', value: '<input type="text"  size="50"  name="value[title]"   value=""/>'}
                }
            },

            'image': {
                'title': 'Изображение',
                'params': {
                    'storage': {title: 'Сохранить в', value: '<input type="text"     size="50" name="value[storage]"   value="extra"/>', description: '/uploads/{path}/'},
                    'spacing': {title: 'Spacing', value: '<input type="text"    name="value[spacing]"   value=""/>'},
                    'title': {title: 'Подсказка', value: '<input type="text"  size="50"  name="value[title]"   value=""/>'}
                }
            },

            'sat_node': {
                'title': 'Страница',
                'params': {
                    'pid': {title: 'Родитель', value: '<input type="text"    name="value[pid]"   value=""/>'}
                }
            }
        },

        fTemplate:  '<div class="form-block" %description%>' +
                    '<label>%title%</label>' +
                    '<div>%content%</div>' +
                    '</div>',

        init: function (root, $scope) {

            $scope.test = function() {
                alert('$scope.test' + JSON.stringify($scope.value));
            }

            console.log('init', $scope.value);

            // type binding
            root.find('select[name=type]').on('change', function () {

                var type = $(this).find('option:selected').data('type');

                $container = root.find('.extrafs-container:eq(0)');
                $container.empty();

                if (extrafs.fields[type].params) {

                    var params = extrafs.fields[type].params;

                    for (var i in params) {
                        var f = params[i];

                        var tpl = extrafs.fTemplate;

                        tpl = tpl.replace('%title%', f.title);
                        tpl = tpl.replace('%content%', f.value);
                        tpl = tpl.replace('%description%', f.description ? 'data-popover data-content="' + f.description + '"' : '');

                        $container.append(tpl);

                    }
                }

                // restore data

                if ($scope.value) {
                    for (var i in $scope.value) {
                        var input = root.find('[name="value[' + i + ']"]:input:eq(0)');
                        var value = $scope.value[i];

                        if (input.size()) {
                            if (input.is(':checkbox'))
                                if (value) input.attr('checked', 'checked'); else input.removeAttr('checked');
                            else
                                input.val(value);
                        }
                    }
                }


                if (!$container.is(':empty')) {
                    $container.find('input,textarea').addClass('form-control');
                    ed.bindUI($container);
                }

            })

            //init
            .trigger('change');

            // sync
            // $scope.$apply();

            console.log('init', $scope.value);

        }
    }

    return extrafs;


});