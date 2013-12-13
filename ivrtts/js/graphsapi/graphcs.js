var graph = function() {
    var me = this;

    this.guages = function(selector, data, name) {
        if ($('#' + selector).data().options) {
            $('#' + selector).data().options.refresh(getRandomInt(1, 1550));
        } else {
            $("#" + selector).data().options = new JustGage({id: selector, value: getRandomInt(1, 1550), min: 0, max: 3000, title: name, label: "", showMinMax: false});
        }
    };

    this.performance = function(selector, data) {


        $(selector).val(getRandomInt(10, 90));// mudar

        $(selector).each(function() {
            var max = $(this).data().max;
            var circleColor = $(this).parent().css('color');

            $(this).knob({
                'min': 0,
                'max': max,
                'readOnly': true,
                'width': 120,
                'height': 120,
                'fgColor': circleColor,
                'fontWeight': 10,
                'dynamicDraw': true,
                'thickness': 0.1,
                'tickColorizeValues': true,
                'step': 0.1,
                'skin': 'tron'
            });
        });

    };

    this.bar = function(selector, data) {
        var arr = new Array();
        for (var i = 0; i < 5; i++) // i< data.length
        {
            var obj = {
                "x": getRandomInt(1, 222),
                "y": getRandomInt(1, 222)
            };
            arr.push(obj);
        }
        var data1 = {
            "xScale": 'ordinal',
            "yScale": "linear",
            "main": [
                {
                    "className": ".teste",
                    "data": arr
                }
            ]
        };
        var tt = document.createElement('div'),
                leftOffset, //= -(~~$('html').css('padding-left').replace('px', '') + ~~$('body').css('margin-left').replace('px', '')),
                topOffset; // = -32;
        tt.className = 'ex-tooltip';
        document.body.appendChild(tt);
        tt.style.zIndex = 1000;
        var opts = {"mouseover": function(d, i) {
                var pos = $(this).offset();
                $(tt).text((d.x) + ': ' + d.y)
                        .css({top: currentMousePos.y + 5, left: currentMousePos.x - 2})
                        .show();
            },
            "mouseout": function() {
                tt.style.display = 'none';
            },
            'click': function(d) {
                bar(d);
            }
        };
        new xChart('bar', data1, selector, opts);

    };

    this.dualBar = function(selector, data) {
        var arr = new Array();
        var brr = new Array();
        var numero = getRandomInt(50, 222);
        for (var i = 0; i < 5; i++) // i< data.length
        {
            var obj = {
                "x": numero,
                "y": getRandomInt(50, 522)
            };
            var obj1 = {
                "x": numero,
                "y": getRandomInt(50, 222)
            };
            arr.push(obj);
            brr.push(obj1);
            numero++;
        }
        var data1 = {
            "xScale": 'ordinal',
            "yScale": "linear",
            "main": [
                {
                    "className": ".semana",
                    "data": arr
                },
                {
                    "className": ".hoje",
                    "data": brr
                }
            ]
        };
        var tt = document.createElement('div'),
                leftOffset, //= -(~~$('html').css('padding-left').replace('px', '') + ~~$('body').css('margin-left').replace('px', '')),
                topOffset; // = -32;
        tt.className = 'ex-tooltip';
        document.body.appendChild(tt);
        tt.style.zIndex = 1000;
        var opts = {"mouseover": function(d, i) {
                var pos = $(this).offset();
                $(tt).text((d.x) + ': ' + d.y)
                        .css({top: currentMousePos.y + 5, left: currentMousePos.x - 2})
                        .show();
            },
            "mouseout": function() {
                tt.style.display = 'none';
            },
            'click': function(d) {

            }
        };
        new xChart('bar', data1, selector, opts);

    };


    this.line = function(selector, data) {

        var arr = new Array();
        var numero = getRandomInt(1, 20);
        for (var i = 0; i < 5; i++) // i<data.length
        {

            var obj = {
                "x": '2013-12-' + numero,
                "y": getRandomInt(1, 222)
            };
            arr.push(obj);
            numero++;
        }
        var data1 = {
            "xScale": 'time',
            "yScale": 'linear',
            "main": [{
                    "className": '.chamadas',
                    "data": [
                        {
                            "x": "2012-11-19",
                            "y": getRandomInt(122, 322)
                        },
                        {
                            "x": "2012-11-20",
                            "y": getRandomInt(12, 322)
                        },
                        {
                            "x": "2012-11-21",
                            "y": getRandomInt(1, 322)
                        },
                        {
                            "x": "2012-11-22",
                            "y": getRandomInt(122, 322)
                        },
                        {
                            "x": "2012-11-23",
                            "y": getRandomInt(1, 322)
                        },
                        {
                            "x": "2012-11-24",
                            "y": getRandomInt(2, 322)
                        },
                        {
                            "x": "2012-11-25",
                            "y": getRandomInt(12, 322)
                        }
                    ]
                },
                {
                    "className": '.uteis',
                    "data": [
                        {
                            "x": "2012-11-19",
                            "y": getRandomInt(100, 122)
                        },
                        {
                            "x": "2012-11-20",
                            "y": getRandomInt(100, 122)
                        },
                        {
                            "x": "2012-11-21",
                            "y": getRandomInt(100, 122)
                        },
                        {
                            "x": "2012-11-22",
                            "y": getRandomInt(100, 122)
                        },
                        {
                            "x": "2012-11-23",
                            "y": getRandomInt(1, 122)
                        },
                        {
                            "x": "2012-11-24",
                            "y": getRandomInt(100, 122)
                        },
                        {
                            "x": "2012-11-25",
                            "y": getRandomInt(1, 122)
                        }
                    ]
                },
                {
                    "className": '.uteis1',
                    "data": [
                        {
                            "x": "2012-11-19",
                            "y": getRandomInt(5, 100)
                        },
                        {
                            "x": "2012-11-20",
                            "y": getRandomInt(50, 100)
                        },
                        {
                            "x": "2012-11-21",
                            "y": getRandomInt(50, 100)
                        },
                        {
                            "x": "2012-11-22",
                            "y": getRandomInt(50, 100)
                        },
                        {
                            "x": "2012-11-23",
                            "y": getRandomInt(50, 100)
                        },
                        {
                            "x": "2012-11-24",
                            "y": getRandomInt(50, 100)
                        },
                        {
                            "x": "2012-11-25",
                            "y": getRandomInt(50, 122)
                        }
                    ]
                },
                {
                    "className": '.uteis2',
                    "data": [
                        {
                            "x": "2012-11-19",
                            "y": getRandomInt(1, 50)
                        },
                        {
                            "x": "2012-11-20",
                            "y": getRandomInt(1,50)
                        },
                        {
                            "x": "2012-11-21",
                            "y": getRandomInt(1,50)
                        },
                        {
                            "x": "2012-11-22",
                            "y": getRandomInt(1, 50)
                        },
                        {
                            "x": "2012-11-23",
                            "y": getRandomInt(1, 50)
                        },
                        {
                            "x": "2012-11-24",
                            "y": getRandomInt(1,50)
                        },
                        {
                            "x": "2012-11-25",
                            "y": getRandomInt(1, 50)
                        }
                    ]
                }
            ]
        };


        var tt = document.createElement('div'),
                leftOffset = -(~~$('html').css('padding-left').replace('px', '') + ~~$('body').css('margin-left').replace('px', '')),
                topOffset = -32;
        tt.className = 'ex-tooltip';
        document.body.appendChild(tt);
        var opts = {
            "axisPaddingRight": 15,
            "axisPaddingLeft": 15,
            "tickHintX": 7,
            "dataFormatX": function(x) {
                return d3.time.format('%Y-%m-%d').parse(x);
            },
            "tickFormatX": function(x) {
                return d3.time.format('%A')(x);
            },
            "mouseover": function(d, i) {
                var pos = $(this).offset();
                ($(this));
                $(tt).text(d3.time.format('%A')(d.x) + ': ' + d.y)
                        .css({top: topOffset + pos.top, left: pos.left + leftOffset, zIndex: 1111})
                        .show();
            },
            "mouseout": function(x) {
                $(tt).hide();
            }
        };

        var myChart = new xChart('line-dotted', data1, selector, opts);


    };

    this.pie = function(selector, data1) {
        var data = [
            {label: "Desktop", data: 212},
            {label: "Mobile", data: 27},
            {label: "Mac", data: 100}

        ];

        if ($(selector).length)
        {
            $.plot($(selector), data,
                    {
                        series: {
                            pie: {
                                show: true
                            }
                        },
                        grid: {
                            hoverable: true,
                            clickable: true
                        },
                        legend: {
                            show: false
                        },
                        colors: ["#FA5833", "#2FABE9"]
                    });

        }
        ;
    };
};


currentMousePos = {};
$(document).mousemove(function(event) {
    currentMousePos.x = event.pageX;
    currentMousePos.y = event.pageY;
});
