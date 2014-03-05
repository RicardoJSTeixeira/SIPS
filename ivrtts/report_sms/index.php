<style src="../css/datepicker.css"></style>
<?php
$start_date = date("o-m-d");
$end_date = date("o-m-d");
?>
<div class="row-fluid">
    <div class="grid span12">
        <div class="grid-title">
            <div class="pull-left">Report Filters</div>
            <div class="pull-right"></div>
            <div class="clear"></div>
        </div>
        <div class="grid-content">
            <div class="span2">
                <label for="dpd1">Start Date:</label>
                <input type="text" style="width: 150px" value="<?= $start_date ?>" id="dpd1"> 
            </div>
            <div class="span2">
                <label for="dpd2">End Date:</label>
                <input type="text" style="width: 150px" value="<?= $end_date ?>" id="dpd2">
            </div>
            <div class="span1">
                &nbsp;
            </div>
            <div class="span4" style="text-align: center">
                <div class="formRow">
                    <div class=" distance">
                        <p>
                            <input type="checkbox" id="delivered" name="delivered" checked>
                            <label for="delivered"><span></span> Delivered</label>
                        </p>
                        <p>
                            <input type="checkbox" id="pending" name="pending" checked>
                            <label for="pending"><span></span> Pending</label>
                        </p>
                        <p>
                            <input type="checkbox" id="failed" name="failed" checked>
                            <label for="failed"><span></span> Failed</label>
                        </p>
                    </div>
                </div>
            </div>
            <div class="span3" style="text-align: center">
                <button class="btn  btn-success" id="reload" name="reload" value="Reload">Load Report</button>
                <button class="btn  btn-success" id="export_all" disabled name="export_all" >Export Report</button>
            </div>    
        </div>
    </div>

</div>


<div class="row-fluid">

    <div class="grid span12">
        <div class="grid-title">
            <div class="pull-left">SMS Report</div>
            <div class="pull-right"><h5>Total Records: <span id="smscount">0</span></h5></div>
            <div class="clear"></div>
        </div>
        <div class="grid-content">
            <table id="smsreport" class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>Sent Date</th>
                        <th>Status Date</th>
                        <th>Destination</th>
                        <th>Content</th>
                        <th># SMS</th>
                        <th>Status</th>
                        <th>Process</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>


<script src="../js/bootstrap-datepicker.js"></script>
<script>
    function getData(callback) {
        var filters = new Array
        if ($("#delivered").is(':checked')) {
            filters.push('0');
        }
        if ($("#pending").is(':checked')) {
            filters.push('1');
        }
        if ($("#failed").is(':checked')) {
            filters.push('2');
        }
        callback(filters);
    }
    var asInitVals = new Array();
    var oTable;
    $("#reload").on('click', function() {
        $("#reload").attr('disabled', true)
        $("#smsreport").dataTable().fnDestroy();
        setTimeout(function() {
            getData(function(filters) {
                $('#smsreport').dataTable({
                    "bProcessing": true,
                    "bDestroy": true,
                    "bAutoWidth": false,
                    "sPaginationType": "full_numbers",
                    "sAjaxSource": '../report_sms/requests.php',
                    "fnServerParams": function(aoData) {
                        aoData.push({"name": "action", "value": "get_sms_report"}, {"name": "start_date", "value": $("#dpd1").val()}, {"name": "end_date", "value": $("#dpd2").val()}, {"name": "filters", value: filters});
                    },
                    "fnDrawCallback": function(oSettings) {
                       
                        
                        $("#reload").attr('disabled', false);
                        $("#export_all").attr('disabled', false);
                        $("#smscount").html($('#smsreport').dataTable().fnSettings().fnRecordsTotal())
                        
                        $("#smsreport_filter input").on('keyup', function(){ $("#smscount").html($("#smsreport").dataTable().fnSettings().fnRecordsDisplay()); }); 
                    }
                });
            });
        }, 2000);
    });




    function table2csv(oTable, exportmode, tableElm, callback) {
        var csv = '';
        var headers = [];
        var rows = [];

        // Get header names
        $(tableElm + ' thead').find('th').each(function() {
            var $th = $(this);
            var text = $th.text();
            var header = '"' + text + '"';
            // headers.push(header); // original code
            if (text != "")
                headers.push(header); // actually datatables seems to copy my original headers so there ist an amount of TH cells which are empty
        });
        csv += headers.join(';') + "\n";

        // get table data
        if (exportmode == "full") { // total data
            var total = oTable.fnSettings().fnRecordsTotal()
            for (i = 0; i < total; i++) {
                var row = oTable.fnGetData(i);
                row = row.join(";");
                rows.push(row);
            }
        } else { // visible rows only
            $(tableElm + ' tbody tr:visible').each(function(index) {
                var row = oTable.fnGetData(this);
                row = row.join(";");
                rows.push(row);
            })
        }
        csv += rows.join("\n");

        // if a csv div is already open, delete it
        if ($('.csv-data').length)
            $('.csv-data').remove();
        // open a div with a download link
        $('body').append('<div class="csv-data hidden"><form id="download" enctype="multipart/form-data" method="post" action="../report_sms/csv.php"><textarea class="form" name="csv">' + csv + '</textarea><input type="submit" class="submit" value="Download as file" /></form></div>');
        if (callback)
            callback();
    }

    function strip_tags(html) {
        var tmp = document.createElement("div");
        tmp.innerHTML = html;
        return tmp.textContent || tmp.innerText;
    }

    // export only what is visible right now (filters & paginationapplied)


    $(function() {
        var nowTemp = new Date();
        var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

        var checkin = $('#dpd1').datepicker({
            format: 'yyyy-mm-dd',
            startDate: "<?= $start_date ?>",
            onRender: function(date) {
                return date.valueOf() < now.valueOf() ? 'disabled' : '';
            }
        }).on('changeDate', function(ev) {
            if (ev.date.valueOf() > checkout.date.valueOf()) {
                var newDate = new Date(ev.date)
                newDate.setDate(newDate.getDate() + 1);
                checkout.setValue(newDate);
            }
            checkin.hide();
            $('#dpd2')[0].focus();
        }).data('datepicker');
        var checkout = $('#dpd2').datepicker({
            format: 'yyyy-mm-dd',
            startDate: "<?= $start_date ?>",
            onRender: function(date) {
                return date.valueOf() <= checkin.date.valueOf() ? 'disabled' : '';
            }
        }).on('changeDate', function(ev) {
            checkout.hide();
        }).data('datepicker');

        $('#export_visible').click(function(event) {
            event.preventDefault();
            table2csv($('#smsreport').dataTable(), 'visible', '#smsreport');
        })

// export all table data
        $('#export_all').click(function(event) {
            event.preventDefault();
            table2csv($('#smsreport').dataTable(), 'full', '#smsreport', function() {
                $("#download").submit();
            });
        })

    });

</script>