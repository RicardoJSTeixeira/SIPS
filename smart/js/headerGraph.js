var headerGraph = function() {
    var me = this,
            today = moment(),
            info = new mongo();

    this.draw = function() {
            $('#headerGraphics').html('<ul id="sparks"></ul>');
            $('#headerGraphics ul').append('<li class="sparks-info"><h5> Agents <span class="txt-color-blue" id="HG-agents-live">0/0</span></h5></li>')
                    .append('<li class="sparks-info"> <h5> Total Calls <span class="txt-color-purple"><i class="fa fa-arrow-circle-up" data-rel="bootstrap-tooltip" title="Increased"></i>&nbsp;<b id="HG-total-calls">0</b></span></h5><div id="hg" class="sparkline txt-color-purple hidden-mobile" ></div></li>')
                    .append('<li class="sparks-info"><h5> Talk Time <span class="txt-color-greenDark"><i class="fa fa-arrow-circle-down"></i> <b id="HG-talk-time">0h</b></span></h5><div id="hg-talk" class="sparkline txt-color-purple hidden-mobile" ></div></li>');

            $.post('../php/action_user.php', {action: 'agentLive'}, function(data) {
                info.get({datatype: 'agents', type: 'datatype'}, function(datas) {
                    $('#HG-agents-live').html(data.length + '<font size="+2">/</font><b>' + datas.length + '</b> <i class="fa fa-user"></i>');
                });
            }, 'json');

            info.get({datatype: 'calls', type: 'total', timeline: {start: today.format('YYYY-MM-DDT00:01'), end: today.format('YYYY-MM-DDT23:59')}}, function(info) {
                if (info.length) {
                    $('#HG-total-calls').html(info[0].calls);
                    if (info[0].length < 3600) {
                        $('#HG-talk-time').html(Math.round(moment.duration(info[0].length, 's').as('minutes')) + 'm'); //moment.duration(7200, 's').as('hours');
                    } else {
                        $('#HG-talk-time').html(Math.round(moment.duration(info[0].length, 's').as('hours')) + 'h'); //moment.duration(7200, 's').as('hours');
                    }
                } else {
                    $('#HG-total-calls').html('0');
                    $('#HG-talk-time').html('0h'); //moment.duration(7200, 's').as('hours');
                }
            });

            info.get({datatype: 'calls', type: 'total', timeline: {start: today.format('YYYY-MM-DDT00:01'), end: today.format('YYYY-MM-DDT23:59')}, by: {calls: ['by=hour']}}, function(varias) {
                var val = [], valTime = [];
                $.each(varias, function() {
                    val.push(this.calls);
                    valTime.push(Math.round(moment.duration(this.length, 's').as('minutes')));
                });
                $('#hg-talk').sparkline(valTime, {type: 'bar', width: 100, height: 30, barWidth: 5, tooltipFormat: '{{value:levels}} - {{value}}',
                    tooltipFormat: '{{offset:offset}} {{value}} m',
                            tooltipValueLookups: {
                                'offset': {
                                    0: '00:00 -',
                                    1: '01:00 -',
                                    2: '02:00 -',
                                    3: '03:00 -',
                                    4: '04:00 -',
                                    5: '05:00 -',
                                    6: '06:00 -',
                                    7: '07:00 -',
                                    8: '08:00 -',
                                    9: '09:00 -',
                                    10: '10:00 -',
                                    11: '11:00 -',
                                    12: '12:00 -',
                                    13: '13:00 -',
                                    14: '14:00 -',
                                    15: '15:00 -',
                                    16: '16:00 -',
                                    17: '17:00 -',
                                    18: '18:00 -',
                                    19: '19:00 -',
                                    20: '20:00 -',
                                    21: '21:00 -',
                                    22: '22:00 -',
                                    23: '23:00 -'
                                }
                            }
                });
                $('#hg').sparkline(val, {type: 'bar', width: 100, height: 30, barWidth: 5, tooltipFormat: '{{value:levels}} - {{value}}',
                    tooltipFormat: '{{offset:offset}} {{value}} Calls',
                            tooltipValueLookups: {
                                'offset': {
                                    0: '00:00 -',
                                    1: '01:00 -',
                                    2: '02:00 -',
                                    3: '03:00 -',
                                    4: '04:00 -',
                                    5: '05:00 -',
                                    6: '06:00 -',
                                    7: '07:00 -',
                                    8: '08:00 -',
                                    9: '09:00 -',
                                    10: '10:00 -',
                                    11: '11:00 -',
                                    12: '12:00 -',
                                    13: '13:00 -',
                                    14: '14:00 -',
                                    15: '15:00 -',
                                    16: '16:00 -',
                                    17: '17:00 -',
                                    18: '18:00 -',
                                    19: '19:00 -',
                                    20: '20:00 -',
                                    21: '21:00 -',
                                    22: '22:00 -',
                                    23: '23:00 -'
                                }
                            }
                });
            });
        
    };
};
