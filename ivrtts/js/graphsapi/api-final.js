var GetMongoInfo = function() {

   var me = this,
            //domain = window.location.protocol + '//' + window.location.host,
            //gonecomplus.dyndns.org
            domain = 'http://goviragem.dyndns.org',
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

    this.creat = function(obj) {
        var options = obj,
                data = {'datatype': '', 'type': '', 'id': '', 'timeline': {'start': '', 'end': '', 'by': ''}, 'by': {'calls': [], 'filter': []}};
        $.extend(true, data, options);
        switch (data.type) {
            case 'id':
                {
                    return domain + port + prefix + data.datatype + '/' + data.id;
                    break;
                }
            case 'datatype':
                {
                    if (data.by.calls.length && !data.by.filter.length) {
                        return domain + port + prefix + data.datatype + '?' + data.by.calls.join(',');
                    } else if (data.by.calls.length && data.by.filter.length) {
                        return domain + port + prefix + data.datatype + by + data.by.calls.join(',') + and + data.by.filter.join('&');
                    } else {
                        return domain + port + prefix + data.datatype;
                    }
                    break;
                }
            case 'sum':
                {
                    if (data.by.calls.length && !data.by.filter.length) {
                        return domain + port + prefix + sum+ '/' + data.datatype + by + data.by.calls.join(',');
                    } else if (data.by.calls.length && !data.by.filter.length) {
                        return domain + port + prefix + sum + callsSecond + by + data.by.calls.join(',') + and + data.by.filter.join('&');
                    } else {
                        return domain + port + prefix + sum + callsSecond;
                    }
                    break;
                }
            case 'avg':
                {
                    if (data.by.calls.length && !data.by.filter.length) {
                        return domain + port + prefix + avg + callsSecond + by + data.by.calls.join(',');
                    } else if (data.by.calls.length && data.by.filter.length) {
                        return domain + port + prefix + avg + callsSecond + by + data.by.calls.join(',') + and + data.by.filter.join('&');
                    } else {
                        return domain + port + prefix + avg + callsSecond;
                    }
                    break;
                }
            case 'count':
                {
                    if (data.by.calls.length && !data.by.filter.length) {
                        return domain + port + prefix + count + data.datatype + by + data.by.calls.join(',');
                    } else if (data.by.calls.length && data.by.filter.length) {
                        return domain + port + prefix + count + data.datatype + by + data.by.calls.join(',') + and + data.by.filter.join('&');
                    } else {
                        return domain + port + prefix + count + data.datatype;
                    }
                    break;
                }
            case 'min':
            case 'max':
            case 'min,max':
                {

                    if (data.by.calls.length && !data.by.filter.length) {
                        return domain + port + prefix + data.type + startDate + by + data.by.calls.join(',');
                        break;
                    } else if (data.by.calls.length && data.by.filter.length) {
                        return domain + port + prefix + data.type + startDate + by + data.by.calls.join(',') + and + data.by.filter.join('&');
                        break;
                    } else {
                        return domain + port + prefix + data.type + startDate;
                        break;
                    }
                    break;
                }
            case 'timeline':
                {
                    if (data.by.calls.length && !data.by.filter.length) {
                        return domain + port + prefix + data.type + '/' + data.datatype + '/' + data.timeline.by + '/' + data.timeline.start + '/' + data.timeline.end + '?' + data.by.calls.join(',');
                        break;
                    } else if (data.by.calls.length && data.by.filter.length) {
                        return domain + port + prefix + data.type + '/' + data.datatype + '/' + data.timeline.by + '/' + data.timeline.start + '/' + data.timeline.end + '?' + data.by.calls.join(',') + and + data.by.filter.join('&');
                        break;
                    }else{
                        return domain+ port + prefix+ data.type +'/'+ data.datatype+'/'+data.timeline.by+ '/'+ data.timeline.start+'/'+data.timeline.end;
                        break;
                    }
                    break;
                }
            case 'total':
                {
                    if (data.by.calls.length && !data.by.filter.length) {
                        return domain + port + prefix + data.type + '/' + data.datatype + '/' + data.timeline.start + '/' + data.timeline.end + '?' + data.by.calls.join(',');
                        break;
                    } else if (data.by.calls.length && data.by.filter.length) {
                        return domain + port + prefix + data.type + '/' + data.datatype + '/' + data.timeline.start + '/' + data.timeline.end + '?' + data.by.calls.join(',') + and + data.by.filter.join('&');
                        break;
                    } else {
                        return domain + port + prefix + data.type + '/' + data.datatype + '/' + data.timeline.start + '/' + data.timeline.end;
                        break;
                    }
                    break;
                }
        }
    };
    this.get = function(obj, callback) {
        if (me.creat(obj) === false) {
            console.log('Mongo Method: Call error!!');
        } else {
            $.ajaxSetup({cache: true});
            console.log(me.creat(obj));
            $.getJSON(me.creat(obj), function(data) {
                callback(data);
            });
            $.ajaxSetup({cache: false});
        }
    };
};