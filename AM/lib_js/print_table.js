function table2csv(oTable, exportmode, tableElm) {
    var csv = '';
    var headers = [];
    var rows = [];

    // Get header names
    $(tableElm + ' thead').find('th').each(function() {
        var $th = $(this);
        var text = $th.text();
        var header = '"' + text + '"';
        // headers.push(header); // original code
        if (text !== "")
            headers.push(header); // actually datatables seems to copy my original headers so there ist an amount of TH cells which are empty
    });
    csv += headers.join(' \t ') + "\n";

    // get table data
    if (exportmode === "full") { // total data
        var total = oTable.fnSettings().fnRecordsTotal();
        for (i = 0; i < total; i++) {
            var row = oTable.fnGetData(i);
            row = strip_tags(row).replace(/,/g, '\t');
            rows.push(row);
        }
    } else { // visible rows only
        $(tableElm + ' tbody tr:visible').each(function(index) {
            var row = oTable.fnGetData(this);
            row = strip_tags(row).replace(/,/g, '\t');
            rows.push(row);
        });
    }
    csv += rows.join("\n");

    // if a csv div is already open, delete it
    if ($('.csv-data').length)
        $('.csv-data').remove();
    // open a div with a download link 
    window.location.href = 'data:application/vnd.ms-excel;charset=UTF-8,'
            + encodeURIComponent(csv);
}

function strip_tags(html) {
    var tmp = document.createElement("div");
    var array = $.map(html, function(value, index) {
        return [value];
    });
    tmp.innerHTML = array;
    return tmp.textContent || tmp.innerText;
}

