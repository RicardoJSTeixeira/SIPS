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
            "xScale": 'ordinal',
            "yScale": 'linear',
            "main": [{
                    "className": '.chamadas',
                    "data": [
                        {
                            "x": 1,
                            "y": getRandomInt(122, 322)
                        },
                        {
                            "x": 2,
                            "y": getRandomInt(12, 322)
                        },
                        {
                            "x": 3,
                            "y": getRandomInt(1, 322)
                        },
                        {
                            "x": 4,
                            "y": getRandomInt(122, 322)
                        },
                        {
                            "x": 5,
                            "y": getRandomInt(122, 322)
                        },
                        {
                            "x": 6,
                            "y": getRandomInt(200, 322)
                        },
                        {
                            "x": 7,
                            "y": getRandomInt(102, 322)
                        },
                        {
                            "x": 8,
                            "y": getRandomInt(102, 322)
                        },
                        {
                            "x": 9,
                            "y": getRandomInt(102, 322)
                        },{
                            "x": 10,
                            "y": getRandomInt(102, 322)
                        },
                        {
                            "x": 11,
                            "y": getRandomInt(102, 322)
                        },
                        {
                            "x": 12,
                            "y": getRandomInt(102, 322)
                        },{
                            "x": 13,
                            "y": getRandomInt(122, 322)
                        },
                        {
                            "x": 14,
                            "y": getRandomInt(12, 322)
                        },
                        {
                            "x": 15,
                            "y": getRandomInt(1, 322)
                        },
                        {
                            "x": 16,
                            "y": getRandomInt(122, 322)
                        },
                        {
                            "x": 17,
                            "y": getRandomInt(122, 322)
                        },
                        {
                            "x": 18,
                            "y": getRandomInt(200, 322)
                        },
                        {
                            "x": 19,
                            "y": getRandomInt(102, 322)
                        },
                        {
                            "x": 20,
                            "y": getRandomInt(102, 322)
                        },
                        {
                            "x": 21,
                            "y": getRandomInt(102, 322)
                        },{
                            "x": 22,
                            "y": getRandomInt(102, 322)
                        },
                        {
                            "x": 23,
                            "y": getRandomInt(102, 322)
                        },
                        {
                            "x": 24,
                            "y": getRandomInt(102, 322)
                        },{
                            "x": 25,
                            "y": getRandomInt(122, 322)
                        },
                        {
                            "x": 26,
                            "y": getRandomInt(12, 322)
                        },
                        {
                            "x": 27,
                            "y": getRandomInt(1, 322)
                        },
                        {
                            "x": 28,
                            "y": getRandomInt(122, 322)
                        },
                        {
                            "x": 29,
                            "y": getRandomInt(122, 322)
                        },
                        {
                            "x": 30,
                            "y": getRandomInt(200, 322)
                        },
                        {
                            "x": 31,
                            "y": getRandomInt(102, 322)
                        }
                    ]
                },{
                    "className": '.chamadas1',
                    "data": [
                        {
                            "x": 1,
                            "y": getRandomInt(122, 322)
                        },
                        {
                            "x": 2,
                            "y": getRandomInt(12, 322)
                        },
                        {
                            "x": 3,
                            "y": getRandomInt(1, 322)
                        },
                        {
                            "x": 4,
                            "y": getRandomInt(122, 322)
                        },
                        {
                            "x": 5,
                            "y": getRandomInt(122, 322)
                        },
                        {
                            "x": 6,
                            "y": getRandomInt(200, 322)
                        },
                        {
                            "x": 7,
                            "y": getRandomInt(102, 322)
                        },
                        {
                            "x": 8,
                            "y": getRandomInt(102, 322)
                        },
                        {
                            "x": 9,
                            "y": getRandomInt(102, 322)
                        },{
                            "x": 10,
                            "y": getRandomInt(102, 322)
                        },
                        {
                            "x": 11,
                            "y": getRandomInt(102, 322)
                        },
                        {
                            "x": 12,
                            "y": getRandomInt(102, 322)
                        },{
                            "x": 13,
                            "y": getRandomInt(122, 322)
                        },
                        {
                            "x": 14,
                            "y": getRandomInt(12, 322)
                        },
                        {
                            "x": 15,
                            "y": getRandomInt(1, 322)
                        },
                        {
                            "x": 16,
                            "y": getRandomInt(122, 322)
                        },
                        {
                            "x": 17,
                            "y": getRandomInt(122, 322)
                        },
                        {
                            "x": 18,
                            "y": getRandomInt(200, 322)
                        },
                        {
                            "x": 19,
                            "y": getRandomInt(102, 322)
                        },
                        {
                            "x": 20,
                            "y": getRandomInt(102, 322)
                        },
                        {
                            "x": 21,
                            "y": getRandomInt(102, 322)
                        },{
                            "x": 22,
                            "y": getRandomInt(102, 322)
                        },
                        {
                            "x": 23,
                            "y": getRandomInt(102, 322)
                        },
                        {
                            "x": 24,
                            "y": getRandomInt(102, 322)
                        },{
                            "x": 25,
                            "y": getRandomInt(122, 322)
                        },
                        {
                            "x": 26,
                            "y": getRandomInt(12, 322)
                        },
                        {
                            "x": 27,
                            "y": getRandomInt(1, 322)
                        },
                        {
                            "x": 28,
                            "y": getRandomInt(122, 322)
                        },
                        {
                            "x": 29,
                            "y": getRandomInt(122, 322)
                        },
                        {
                            "x": 30,
                            "y": getRandomInt(200, 322)
                        },
                        {
                            "x": 31,
                            "y": getRandomInt(102, 322)
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
           "tickHintX": 31, /*
            "dataFormatX": function(x) {
                console.log (d3.time.format('%Y-%m-%d').parse(x));
                return d3.time.format('%Y-%m').parse(x);
            },
            "tickFormatX": function(x) {
                return d3.time.format('%m')(x);
            },*/
            "mouseover": function(d, i) {
                var pos = $(this).offset();
                ($(this));
                $(tt).text(d.x+' ' + d.y)
                        .css({top: topOffset + pos.top, left: pos.left + leftOffset, zIndex: 1111})
                        .show();
            },
            "sortX":function(a, b) {return a.x-b.x; },
            "mouseout": function(x) {
                $(tt).hide();
            }
        };

        var myChart = new xChart('line-dotted', data1, selector, opts);


    };

    this.pie = function(selector, data) {
        /* var arr=[];
        for (var i = 0; i < data.length ; i++) // i<data.length
        {

            var obj = {
                          "label": data[i]._id.status.designation,
                         "data": data[i].count
            };
            arr.push(obj);
           
        }
        
        console.log(arr);*/
//        var data = [
//            {label: "Desktop", data: 212},
//            {label: "Mobile", data: 27},
//            {label: "Mac", data: 100}
//
//        ];

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
