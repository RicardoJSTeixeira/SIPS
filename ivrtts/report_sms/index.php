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
                <input type="text" value="<?= $start_date ?>" id="dpd1"> 
            </div>
            <div class="span2">
                <label for="dpd2">End Date:</label>
                <input type="text" value="<?= $end_date ?>" id="dpd2">
            </div>
            <div class="span3">
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
            <div class="span3">
                <button class="btn btn-large btn-success btn-block pull-left" id="reload" name="reload" value="Reload">Load Report</button>
            </div>
        </div>
    </div>

</div>


<div class="row-fluid">

    <div class="grid span12">
        <div class="grid-title">
            <div class="pull-left">SMS Report</div>
            <div class="pull-right"></div>
            <div class="clear"></div>
        </div>
        <div class="grid-content">
            <table id="smsreport" class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
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

    $("#reload").on('click', function() {
        $("#reload").attr('disabled', true)
        $("#smsreport").dataTable().fnDestroy();
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
                    $("#reload").attr('disabled', false)
                }
            });
        });
    });


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

    });

</script>