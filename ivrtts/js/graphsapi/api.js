var API = function() {
    var me = this,
            domain = 'http://goviragem.dyndns.org',
            port = ':10000',
            prefix = '/ccstats/v0/',
            count = 'count/',
            by = '?by=',
            data = {'datatype': '', 'id': '', 'timeline': {'start':'0', 'end':'0'}, 'by': {'group': false, 'campaign': false, 'calls': []}};
    this.creat = function(obj) {
        options = obj;
        $.extend(data, options);
        switch (data.datatype) {
            case 'group':
            case 'campaign':
            case 'statuse' :
            case 'agent' :
            case 'database' :
            case 'call' :
                {
                    if (data.id !== '') {
                        return domain + port + prefix + data.datatype + data.id;

                    } else {
                        console.log(domain+ port + prefix +data.datatype +'s');
                        return domain+ port + prefix +data.datatype +'s';
                        
                    }
                    break;
                }
            case 'groups':
            case 'campaigns' :
                {
                    return domain + port + prefix + count + data.datatype;

                    break;
                }
            case 'agents' :
                {
                    if (data.by.group === true) {
                        return domain + port + prefix + count + data.datatype + by + 'group';

                    } else if (data.by.group === false) {
                        return domain + port + prefix + count + data.datatype;

                    } else {
                        return false;
                    }
                    break;
                }
            case 'databases':
                {
                    if (data.by.campaign === true) {
                        return domain + port + prefix + count + data.datatype + by + 'campaign';
                    } else if (data.by.group === false) {
                        return domain + port + prefix + count + data.datatype;

                    } else {
                        return false;
                    }
                    break;
                }
            case 'calls' :
                {
                    if (data.by.calls.length) {
                        return domain + port + prefix + count + data.datatype + by + data.by.calls.join(',');
                    } else {
                        return domain + port + prefix + count + data.datatype;
                    }

                    break;
                }
            case 'calles' :
                {
                    if(data.timeline.start !== 0 && data.timeline.end !==0){
                        console.log('Pesquisa Temporal!!');
                        if(data.timeline.start !== data.timeline.end)
                        {
                            console.log('Range');
                            break;
                        }else{
                            console.log('Dia');
                            break;
                        }
                    }
                    break;
                }
          

        }


    };

    this.get = function(obj, callback) {
        if (me.creat(obj) === false) {
            console.log('API: ERROR');
        } else {
            $.getJSON(me.creat(obj), function(data) {
                callback(data);
            });
        }
    };

};
