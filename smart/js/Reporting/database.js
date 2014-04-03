function databaseList() {
    $('#campaign-table').dataTable({"sPaginationType": "bootstrap",
        "bProcessing": true,
        "bLengthChange": false,
        "bDestroy": true,
        "aaSorting": [[2, "desc"]],
        "aoColumns": [
            {"sTitle": "ID", "sClass": "", "sWidth": "50px", "sType": "string"},
            {"sTitle": "Campaign Designation", "sClass": "", "sWidth": "150px"},
            {"sTitle": "Total Database", "sClass": "", "sWidth": "50px"},
            {"sTitle": "Total Contact", "sClass": "", "sWidth": "50px"},
            {"sTitle": "Total NEW", "sClass": "", "sWidth": "50px"},
            {"sTitle": "", "sClass": "", "sWidth": "50px"},
            {"sTitle": "", "sClass": "", "sWidth": "50px"},
            {"sTitle": "", "sClass": "", "sWidth": "50px"}],
        "sDom": "<'dt-top-row'Tlf>r<'dt-wrapper't><'dt-row dt-bottom-row'<'row'<'col-sm-6'i><'col-sm-6 text-right'p>>",
        "oTableTools": {
            "aButtons": [{
                    "sExtends": "collection",
                    "sButtonText": 'Save <span class="caret" />',
                    "aButtons": ["csv", "xls", "pdf"]
                }],
            "sSwfPath": "../js/plugin/datatables/media/swf/copy_csv_xls_pdf.swf"
        },
        "fnInitComplete": function(oSettings, json) {
            $(this).closest('#dt_table_tools_wrapper').find('.DTTT.btn-group').addClass('table_tools_group').children('a.btn').each(function() {
                $(this).addClass('btn-sm btn-default');
            });
        }
    });
    $('#campaign-table').dataTable().fnClearTable();




    var win = 3;
    var totalDB = [], totalContact = [], totalNEW = [], callback, date, totalsDB = 0, totalsCONTACTS = 0, totalsNEWS = 0;
    //http://gonecomplus.dyndns.org:10000/ccstats/v0/count/databases?by=campaign
    api.get({datatype: 'databases', type: 'count', by: {calls: ['campaign']}}, function(data) {
        //console.log(data);
        $.each(data, function() {
            totalDB[this._id.campaign.oid] = this.count;
            totalsDB += this.count;
        });
        win--;
        if (!win) {
            list();
        }
    });
    //http://gonecomplus.dyndns.org:10000/ccstats/v0/total/contacts/2000-01-01T00:01/2590-01-01T23:59?by=database,campaign
    api.get({datatype: 'contacts', type: 'total', timeline: {start: '2000-01-01T00:01', end: '2590-01-01T23:59'}, by: {calls: ['by=database,campaign']}}, function(data) {
        //console.log(data);
        $.each(data, function(data) {
            totalsCONTACTS += this.contacts;
            if (totalContact[this.campaign]) {
                totalContact[this.campaign] = totalContact[this.campaign] + this.contacts;
            } else {
                totalContact[this.campaign] = this.contacts;
            }
        });
        win--;
        if (!win) {
            list();
        }
    });
    ///ccstats/v0/count/contacts?by=database,status&status.oid=NEW
    api.get({datatype: 'contacts', type: 'count', by: {calls: ['database,campaign&status.oid=NEW']}}, function(data) {
        //console.log(data);
        $.each(data, function(data) {
            totalsNEWS += this.count;
            if (totalNEW[this._id.database.campaign.oid]) {
                totalNEW[this._id.database.campaign.oid] = this.count;
            } else {
                totalNEW[this._id.database.campaign.oid] = this.count;
            }
        });
        win--;
        if (!win) {
            list();
        }
    });

    function list() {
        // console.log(win);
        var back = [],at=[];
        back.push([0, totalsDB],[1,totalsCONTACTS],[2,totalsNEWS]);
        at.push([0, 'Total Databases'],[1, 'Total Contacts'],[2,' Total News']);
        var dataset = [{label: "", data: back, color: "#57889C"}];
        graficos.floatBar('#databases12', dataset, at, '<span style="display:none;">%x</span> %y ');
        
        api.get({datatype: 'campaigns', type: 'datatype'}, function(data) {
            var ar = [], tamanho = data.length;
            $.each(data, function() {
                var id = '<span class="table-value cursor-pointer" data-oid="' + this.oid + '">' + this.oid + '</span>', totalNews = 0, totalContacts = 0, totalData = 0;

                if (totalNEW[this.oid]) {
                    totalNews = totalNEW[this.oid];
                } else {
                    totalNews = 0;
                }

                if (totalContact[this.oid]) {
                    totalContacts = totalContact[this.oid];
                } else {
                    totalContacts = 0;
                }

                if (totalDB[this.oid]) {
                    totalData = totalDB[this.oid];
                } else {
                    totalData = 0;
                }

                ar.push([id, this.designation, totalData, totalContacts, totalNews, '', '', '']);
                tamanho--;
                if (!tamanho) {
                    $('#campaign-table').dataTable().fnClearTable();
                    $('#campaign-table').dataTable().fnAddData(ar);
                }
            });
        });
    }
}

function database(campaignID) {
    $('#databases .jarviswidget').show();
    $('#databases').show();
    $('#database-table').dataTable({"sPaginationType": "bootstrap",
        "bProcessing": true,
        "bLengthChange": false,
        "bDestroy": true,
        "aaSorting": [[3, "desc"]],
        "aoColumns": [
            {"sTitle": "Lista", "sClass": "", "sWidth": "50px", "sType": "string"},
            {"sTitle": "Nome", "sClass": "", "sWidth": "150px"},
            {"sTitle": "Data Carregamento", "sClass": "", "sWidth": "50px"},
            {"sTitle": "Carregados", "sClass": "", "sWidth": "50px"},
            {"sTitle": "Iniciais", "sClass": "", "sWidth": "50px"},
            {"sTitle": "Agendamentos", "sClass": "", "sWidth": "50px"},
            {"sTitle": "Incidencias", "sClass": "", "sWidth": "50px"},
            {"sTitle": "Max Limits", "sClass": "", "sWidth": "50px"},
            {"sTitle": "Fechados", "sClass": "", "sWidth": "50px"},
            {"sTitle": "VC", "sClass": "", "sWidth": "50px"},
            {"sTitle": "Vendas", "sClass": "", "sWidth": "50px"},
            {"sTitle": "Reach", "sClass": "", "sWidth": "50px"},
            {"sTitle": "Response", "sClass": "", "sWidth": "50px"},
            {"sTitle": "Por Fechar", "sClass": "", "sWidth": "50px"},
            {"sTitle": "Penetração", "sClass": "", "sWidth": "50px"}
        ],
        "sDom": "<'dt-top-row'Tlf>r<'dt-wrapper't><'dt-row dt-bottom-row'<'row'<'col-sm-6'i><'col-sm-6 text-right'p>>",
        "oTableTools": {
            "aButtons": [{
                    "sExtends": "collection",
                    "sButtonText": 'Save <span class="caret" />',
                    "aButtons": ["csv", "xls", "pdf"]
                }],
            "sSwfPath": "../js/plugin/datatables/media/swf/copy_csv_xls_pdf.swf"
        },
        "fnInitComplete": function(oSettings, json) {
            $(this).closest('#dt_table_tools_wrapper').find('.DTTT.btn-group').addClass('table_tools_group').children('a.btn').each(function() {
                $(this).addClass('btn-sm btn-default');
            });
        }
    });


    ///ccstats/v0/total/contacts/2000-01-01T00:01/2020-01-01T00:01?by=database
    var registos = {}, novos = {}, completos = {}, util = {}, sucesso = {}, recy = {}, maximum = {}, win = 4;
    api.get({datatype: 'contacts', type: 'total', timeline: {start: '2000-01-01T00:01', end: '2020-01-01T00:01'}, by: {calls: ['by=database&campaign=' + campaignID]}}, function(data) {

        $.each(data, function(index, value) {
            //registos.push({name: this.database, value: this.contacts});
            registos[this.database] = this.contacts;
        });
        win--;
        if (!win) {
            make();
        }
    });
    ///ccstats/v0/count/contacts?by=database,status&status.oid=NEW
    api.get({datatype: 'contacts', type: 'count', by: {calls: ['database&status.oid=NEW']}}, function(data) {
        $.each(data, function(index, value) {
            //novos.push({name: this._id.database.oid, value: this.count});
            novos[this._id.database.oid] = this.count;
            //console.log(novos);
        });
        win--;
        if (!win) {
            make();
        }
    });

    $.post('../php/reporting.php', {action: 'databaseCallback', id: campaignID}, function(data) {
        callback = data.Callback, date = data.Date, status = data.Status, max = data.Max;
        api.get({datatype: 'contacts', type: 'total', timeline: {start: '2000-01-01T00:01', end: '2020-01-01T00:01'}, by: {calls: ['by=called_since_last_reset,database&status=' + status]}}, function(data) {
            $.each(data, function() {
                var n = +this.called_since_last_reset.slice('-1') || 0;
                //console.log(this.database+':'+this.contacts);
                if (n && n <= max) {
                    if (!recy[this.database]) {
                        recy[this.database] = this.contacts;
                    } else {
                        recy[this.database] = recy[this.database] + this.contacts;
                    }
                } else {
                    if (!maximum[this.database]) {
                        maximum[this.database] = this.contacts;
                    } else {
                        maximum[this.database] = maximum[this.database] + this.contacts;
                    }
                }
            });
            win--;
            if (!win) {
                make();
            }
        });
    }, 'json');

    ///ccstats/v0/total/contacts/2000-01-01T00:01/2020-01-01T00:01?by=database&status=NEW,CALLBK
    $.post('../php/reporting.php', {action: 'getStatus', id: campaignID}, function(data) {
        completosStatus = data.Complete, utilStatus = data.Util, won = 3, sucessoStatus = data.Sucesso;
        api.get({datatype: 'contacts', type: 'total', timeline: {start: '2000-01-01T00:01', end: '2020-01-01T00:01'}, by: {calls: ['by=database&campaign=' + campaignID + '&status=' + completosStatus.join(',')]}}, function(data) {
            $.each(data, function(index, value) {
                //completos.push({name: this.database, value: this.contacts});
                completos[this.database] = this.contacts;
            });
            won--;
            if (!won) {
                volta();
            }
        });

        api.get({datatype: 'contacts', type: 'total', timeline: {start: '2000-01-01T00:01', end: '2020-01-01T00:01'}, by: {calls: ['by=database&campaign=' + campaignID + '&status=' + utilStatus.join(',')]}}, function(data) {
            $.each(data, function(index, value) {
                //util.push({name: this.database, value: this.contacts});
                util[this.database] = this.contacts;
            });
            won--;
            if (!won) {
                volta();
            }
        });

        api.get({datatype: 'contacts', type: 'total', timeline: {start: '2000-01-01T00:01', end: '2020-01-01T00:01'}, by: {calls: ['by=database&campaign=' + campaignID + '&status=' + sucessoStatus.join(',')]}}, function(data) {
            $.each(data, function(index, value) {
                //sucesso.push({name: this.database, value: this.contacts});
                sucesso[this.database] = this.contacts;
            });
            won--;
            if (!won) {
                volta();
            }
        });

        function volta() {
            win--;
            if (!win) {
                make();
            }
        }
    }, 'json');
    function make() {
        var ar = [];
        //http://gonecomplus.dyndns.org:10000/ccstats/v0/databases?campaign.oid=W00011
        api.get({datatype: 'databases', type: 'datatype', by: {calls: ['campaign.oid=' + campaignID]}}, function(data) {
            var n = data.length;
            //console.log(novos);

            $.each(data, function() {
                var data = 0, carregados = 0, iniciais = 0, agendadas = 0, incidencias = 0, max = 0, fechados = 0, vc = 0, vendas = 0, reach = 0, response = 0, fechar = 0, pen = 0;

                if (registos[this.oid]) {
                    carregados = registos[this.oid];
                }
                if (novos[this.oid]) {
                    iniciais = novos[this.oid];
                }
                if (completos[this.oid]) {
                    fechados = completos[this.oid];
                }
                if (util[this.oid]) {
                    vc = util[this.oid];
                }
                if (sucesso[this.oid]) {
                    vendas = sucesso[this.oid];
                }
                if (callback[this.oid]) {
                    agendadas = callback[this.oid];
                }
                if (date[this.oid]) {
                    data = date[this.oid];
                }
                if (vc) {
                    reach = Math.round((vc / fechados) * 100);
                    pen = Math.round((vc / carregados) * 100);
                }
                if (vendas) {
                    response = Math.round((vendas / vc) * 100);
                }
                if (recy[this.oid]) {
                    incidencias = recy[this.oid];
                }
                if (maximum[this.oid]) {
                    max = maximum[this.oid];
                }
                if (carregados) {
                    fechar = (iniciais + agendadas + incidencias - fechados - max) / carregados;
                }

                ar.push([this.oid, this.designation, data, carregados, iniciais, agendadas, incidencias, max, fechados, vc, vendas, reach + '%', response + '%', Math.abs(fechar.toFixed(2)) + '%', pen + '%']);
                n--;
                if (!n) {
                    $('#database-table').dataTable().fnClearTable();
                    $('#database-table').dataTable().fnAddData(ar);
                }
            });
        });
    }





}