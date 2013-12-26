var GetMongoInfo = function() {

    var me = this,
            domain = window.location.protocol + '//' + window.location.host,
            //domain = 'http://goviragem.dyndns.org',
            port = ':10000',
            prefix = '/ccstats/v0/',
            count = 'count/',
            sum = 'sum',
            callsSecond = '/calls/length_in_sec',
            startDate = '/calls/start_date',
            avg = 'avg',
            by = '?by=',
            and = '&',
            data = {'datatype': '', 'type': '', 'id': '', 'timeline': {'start': '', 'end': ''}, 'by': {'calls': [], 'filter': []}};
    this.creat = function(obj) {
        options = obj;
        $.extend(data, options);
        switch (data.type) {
            case 'id':
                {
                    return domain + port + prefix + data.datatype + '/' + data.id;
                    break;
                }
            case 'datatype':
                {
                    if (data.by.calls.length > 0 && data.by.filter.length < 0) {
                        return domain + port + prefix + data.datatype + by + data.by.calls.join(',');
                    } else if (data.by.calls.length > 0 && data.by.filter.length > 0) {
                        return domain + port + prefix + data.datatype + by + data.by.calls.join(',') + and + data.by.filter.join('&');
                    } else {
                        return domain + port + prefix + data.datatype;
                    }
                    break;
                }
            case 'sum':
                {
                    if (data.by.calls.length > 0 && data.by.filter.length < 0) {
                        return domain + port + prefix + sum + callsSecond + by + data.by.calls.join(',');
                    } else if (data.by.calls.length > 0 && data.by.filter.length > 0) {
                        return domain + port + prefix + sum + callsSecond + by + data.by.calls.join(',') + and + data.by.filter.join('&');
                    } else {
                        return domain + port + prefix + sum + callsSecond;
                    }
                    break;
                }
            case 'avg':
                {
                    if (data.by.calls.length > 0 && data.by.filter.length < 0) {
                        return domain + port + prefix + avg + callsSecond + by + data.by.calls.join(',');
                    } else if (data.by.calls.length > 0 && data.by.filter.length > 0) {
                        return domain + port + prefix + avg + callsSecond + by + data.by.calls.join(',') + and + data.by.filter.join('&');
                    } else {
                        return domain + port + prefix + avg + callsSecond;
                    }
                    break;
                }
            case 'count':
                {
                    if (data.by.calls.length > 0 && data.by.filter.length < 0) {
                        return domain + port + prefix + count + data.datatype + by + data.by.calls.join(',');
                    } else if (data.by.calls.length > 0 && data.by.filter.length > 0) {
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

                    if (data.by.calls.length > 0 && data.by.filter.length < 0) {
                        return domain + port + prefix + data.type + startDate + by + data.by.calls.join(',');
                        break;
                    } else if (data.by.calls.length > 0 && data.by.filter.length > 0) {
                        return domain + port + prefix + data.type + startDate + by + data.by.calls.join(',') + and + data.by.filter.join('&');
                        break;
                    } else {
                        return domain + port + prefix + data.type + startDate;
                        break;
                    }
                    break;
                }
        }
    };
    this.get = function(obj, callback) {
        if (me.creat(obj) === false) {
            console.log('Api Error: Call error!!');
        } else {
            $.getJSON(me.creat(obj), function(data) {
                callback(data);
            });
        }
    };
};