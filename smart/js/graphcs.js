var graph = function() {

    var me = this;

    this.guages = function(selector, name, value, max) {
        if ($('#' + selector).data().options) {
            $('#' + selector).data().options.refresh(value);
        } else {
            $("#" + selector).data().options = new JustGage({id: selector, value: value, min: 0, max: max, title: name, showMinMax: true});
        }
    };

    this.performance = function(selector, value) {

        if ($(selector).val()) {
            $(selector)
                    .val(value)
                    .trigger('change');
        } else {
            $(selector).val(value);
            $(selector).knob({
                'min': 0,
                'max': 100,
                'readOnly': true,
                'width': 150,
                'height': 150,
                'fgColor': $(this).data().fgColor,
                'fontWeight': 10,
                'dynamicDraw': true,
                'thickness': 0.1,
                'tickColorizeValues': true,
                'step': 0.1
            });
        }
    };

    this.bar = function(selector, bar, callback) {

        var data = {
            "xScale": "ordinal",
            "yScale": "linear",
            "main": [
                {
                    "className": ".pizza",
                    "data": bar
                }
            ]
        };

        var tt = document.createElement('div'),
                leftOffset, //= -(~~$('html').css('padding-left').replace('px', '') + ~~$('body').css('margin-left').replace('px', '')),
                topOffset; // = -32;
        tt.className = 'ex-tooltip';
        document.body.appendChild(tt);
        tt.style.zIndex = 1000;
        var opts = {
            "tickHintX": 10,
            "mouseover": function(d, i) {
                var pos = $(this).offset();
                $(tt).text(d.x + ' : ' + d.y + d.type)
                        .css({top: currentMousePos.y + 5, left: currentMousePos.x - 2})
                        .show();
            },
            "mouseout": function() {
                tt.style.display = 'none';
            },
            'click': function(data) {
                callback(data);
            }
        };


        var myChart = new xChart('bar', data, selector, opts);

    };

    this.InfBar = function(selector, data) {
        var data1 = {
            "xScale": 'ordinal',
            "yScale": "linear",
            "main": data
        };
        var tt = document.createElement('div'),
                leftOffset, //= -(~~$('html').css('padding-left').replace('px', '') + ~~$('body').css('margin-left').replace('px', '')),
                topOffset; // = -32;
        tt.className = 'ex-tooltip';
        document.body.appendChild(tt);
        tt.style.zIndex = 1000;
        var opts = {
            "tickHintX": 20,
            "mouseover": function(d, i) {
                var pos = $(this).offset();
                $(tt).text('[ Time : ' + d.x + d.type + ' ] [ ' + d.label + ' : ' + d.y + d.unit + ' ]')
                        .css({top: currentMousePos.y + 5, left: currentMousePos.x - 2})
                        .show();
            },
            "mouseout": function() {
                tt.style.display = 'none';
            }
        };
        var myChart = new xChart('bar', data1, selector, opts);
    };


    this.dualBar = function(selector, first, second, third) {

        var data1 = {
            "xScale": 'ordinal',
            "yScale": "linear",
            "main": [
                {
                    "className": ".semana",
                    "data": first
                },
                {
                    "className": ".hoje",
                    "data": second
                },
                {
                    "className": ".agora",
                    "data": third
                }
            ]
        };
        var tt = document.createElement('div'),
                leftOffset, //= -(~~$('html').css('padding-left').replace('px', '') + ~~$('body').css('margin-left').replace('px', '')),
                topOffset; // = -32;
        tt.className = 'ex-tooltip';
        document.body.appendChild(tt);
        tt.style.zIndex = 1000;
        var opts = {
            "tickHintX": 20,
            "mouseover": function(d, i) {
                var pos = $(this).offset();
                $(tt).text('[ Time : ' + d.x + d.type + ' ] [ ' + d.label + ' : ' + d.y + d.unit + ' ]')
                        .css({top: currentMousePos.y + 5, left: currentMousePos.x - 2})
                        .show();
            },
            "mouseout": function() {
                tt.style.display = 'none';
            },
            'click': function(data, i) {

            }
        };
        var myChart = new xChart('bar', data1, selector, opts);

    };


    this.line = function(selector, data) {

        var data1 = {
            "xScale": 'ordinal',
            "yScale": 'linear',
            "main": data
        };


        var tt = document.createElement('div'),
                leftOffset = -(~~$('html').css('padding-left').replace('px', '') + ~~$('body').css('margin-left').replace('px', '')),
                topOffset = -32;
        tt.className = 'ex-tooltip';
        document.body.appendChild(tt);
        var opts = {
            'yMin': 0,
            "axisPaddingRight": 5,
            "axisPaddingLeft": 5,
            "tickHintX": 26, /*
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
                $(tt).text('[ Time : ' + d.x + d.type + '] [' + d.label + ' : ' + d.y + ' ]')
                        .css({top: topOffset + pos.top, left: pos.left + leftOffset, zIndex: 1111})
                        .show();
            },
            /*"sortX": function(a, b) { //sort order menos -> maior
             return a.x - b.x;
             },*/
            "mouseout": function(x) {
                $(tt).hide();
            }
        };

        var myChart = new xChart('line-dotted', data1, selector, opts);


    };

    this.pie = function(selector, data) {

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
                        colors: ["#FA5833", "#2FABE9", "#FABB3D", "#78CD51"]
                    });

        }
        ;
    };

    this.floatBar = function(selector, data, ticks, legenda) {
        if(!legenda){
            legenda="<span style='display:none;'>%x -</span> %y ";
        }
        return $.plot($(selector), data, {
            colors: ['#57889C', '#356e35', '#990329', '#FF6103', '#c79121', '#360068a0', '#d9ce00', '#519c00', '#FF00B3'],
            grid: {
                show: true,
                hoverable: true,
                clickable: "#efefef",
                borderWidth: 0,
                borderColor: '#efefef'
            },
            series: {
                bars: {
                    show: true
                }
            },
            bars: {
                align: "center",
                barWidth: 0.5
            },
            xaxis: {
                ticks: ticks
            },
            legend: true,
            tooltip: true,
            tooltipOpts: {
                content: legenda,
                defaultTheme: false
            }
        });
    };


    this.floatLine = function(selector, ds) {

       return $.plot($(selector), ds, {
            series: {
                lines: {
                    show: true
                },
                points: {
                    show: true
                }
            },
            grid: {
                hoverable: true,
                clickable: true,
                tickColor: "#efefef",
                borderWidth: 0,
                borderColor: "#efefef"
            },
            legend: false,
            tooltip: true,
            tooltipOpts: {
                //content: tooltip,
                defaultTheme: false
            },
            colors: ['#57889C', '#356e35', '#990329', '#FF6103', '#c79121', '#360068a0', '#d9ce00', '#519c00', '#FF00B3'],
        });

    };




};


currentMousePos = {};
$(document).mousemove(function(event) {
    currentMousePos.x = event.pageX;
    currentMousePos.y = event.pageY;
});
