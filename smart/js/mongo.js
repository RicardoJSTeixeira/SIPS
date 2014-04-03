var mongo = function() {

    var me = this,
            domain = window.location.protocol + '//' + window.location.host,
          //domain = 'http://gonecomplus.dyndns.org',
            //domain = 'http://goviragem.dyndns.org',
            port = ':10000',
            prefix = '/ccstats/v0/',
            count = 'count/',
            sum = 'sum',
            calls = '/calls/',
            callsSecond = '/calls/length_in_sec',
            startDate = '/calls/start_date',
            avg = 'avg',
            by = '?by=',
            and = '&';

    function creat(obj) {
        var options = obj,
                data = {'datatype': '', 'type': '', 'id': '', 'timeline': {'start': '', 'end': '', 'by': ''}, 'by': {'calls': [], 'filter': []}};
        $.extend(true, data, options);
        switch (data.type) {
            case 'id':
                {
                    return data.datatype + '/' + data.id;
                    break;
                }
            case 'datatype':
                {
                    if (data.by.calls.length && !data.by.filter.length) {
                        return data.datatype + '?' + data.by.calls.join(',');
                    } else if (data.by.calls.length && data.by.filter.length) {
                        return data.datatype + by + data.by.calls.join(',') + and + data.by.filter.join('&');
                    } else {
                        return  data.datatype;
                    }
                    break;
                }
            case 'sum':
                {
                    if (data.by.calls.length && !data.by.filter.length) {
                        return  sum + callsSecond + by + data.by.calls.join(',');
                    } else if (data.by.calls.length && !data.by.filter.length) {
                        return  callsSecond + by + data.by.calls.join(',') + and + data.by.filter.join('&');
                    } else {
                        return  sum + callsSecond;
                    }
                    break;
                }
            case 'avg':
                {
                    if (data.by.calls.length && !data.by.filter.length) {
                        return avg + callsSecond + by + data.by.calls.join(',');
                    } else if (data.by.calls.length && data.by.filter.length) {
                        return  avg + callsSecond + by + data.by.calls.join(',') + and + data.by.filter.join('&');
                    } else {
                        return  avg + callsSecond;
                    }
                    break;
                }
            case 'count':
                {
                    if (data.by.calls.length && !data.by.filter.length) {
                        return  count + data.datatype + by + data.by.calls.join(',');
                    } else if (data.by.calls.length && data.by.filter.length) {
                        return  count + data.datatype + by + data.by.calls.join(',') + and + data.by.filter.join('&');
                    } else {
                        return  count + data.datatype;
                    }
                    break;
                }
            case 'min':
            case 'max':
            case 'min,max':
                {

                    if (data.by.calls.length && !data.by.filter.length) {
                        return  data.type + startDate + by + data.by.calls.join(',');
                        break;
                    } else if (data.by.calls.length && data.by.filter.length) {
                        return  data.type + startDate + by + data.by.calls.join(',') + and + data.by.filter.join('&');
                        break;
                    } else {
                        return  data.type + startDate;
                        break;
                    }
                    break;
                }
            case 'timeline':
                {
                    if (data.by.calls.length && !data.by.filter.length) {
                        return  data.type + '/' + data.datatype + '/' + data.timeline.by + '/' + data.timeline.start + '/' + data.timeline.end + '?' + data.by.calls.join(',');
                        break;
                    } else if (data.by.calls.length && data.by.filter.length) {
                        return  data.type + '/' + data.datatype + '/' + data.timeline.by + '/' + data.timeline.start + '/' + data.timeline.end + '?' + data.by.calls.join(',') + and + data.by.filter.join('&');
                        break;
                    } else {
                        return  data.type + '/' + data.datatype + '/' + data.timeline.by + '/' + data.timeline.start + '/' + data.timeline.end;
                        break;
                    }
                    break;
                }
            case 'total':
                {
                    if (data.by.calls.length && !data.by.filter.length) {
                        return  data.type + '/' + data.datatype + '/' + data.timeline.start + '/' + data.timeline.end + '?' + data.by.calls.join(',');
                        break;
                    } else if (data.by.calls.length && data.by.filter.length) {
                        return  data.type + '/' + data.datatype + '/' + data.timeline.start + '/' + data.timeline.end + '?' + data.by.calls.join(',') + and + data.by.filter.join('&');
                        break;
                    } else {
                        return  data.type + '/' + data.datatype + '/' + data.timeline.start + '/' + data.timeline.end;
                        break;
                    }
                    break;
                }
        }
    };
    
    this.get = function(obj, callback) {
        if (creat(obj) === false) {
            console.log('Mongo Method: Call error!!');
        } else {
            $.ajaxSetup({cache: true});
            //console.log(creat(obj));
            $.post('../php/Reporting/mongo.php',{q:creat(obj)},
                //console.log(data);
                callback
            ,'json');
            $.ajaxSetup({cache: false});
        }
    };
};