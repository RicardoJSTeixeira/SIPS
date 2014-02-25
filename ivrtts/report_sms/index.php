
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

<script>
    $(function() {
        $('#smsreport').dataTable({
            "bProcessing": true,
            "bDestroy": true,
            "bAutoWidth": false,
            "sPaginationType": "full_numbers",
            "sAjaxSource": '../report_sms/requests.php',
            "fnServerParams": function(aoData) {
                aoData.push({"name": "action", "value": "get_sms_report"}
                );
            }
        });
    });
</script>