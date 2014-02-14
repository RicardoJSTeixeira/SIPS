<style>
    .div-wrapper { height:205px; }
    .div-loader { margin-bottom: 8px; }
    .link { font-weight:bold; text-decoration:underline; cursor:pointer; }
</style>
<div class="row-fluid">

    <!-- CAMPAING NAME & DATABASE -->
    <div class="grid span6">
        <div class="grid-title">
            <div class="pull-left">Campaign Name & Database</div>
            <div class="pull-right"></div>
            <div class="clear"></div>
        </div>
        <div class="grid-content">
            <div class="span div-wrapper">
                <div class="span">
                    <label for="new-campaign-name">Campaign Name</label>
                    <input id="new-campaign-name" type="text" class="span" />
                </div>     	
                <form enctype="multipart/form-data" method="POST" action="../intra_newcampaign/upload.php">    
                    <div class="fileupload fileupload-new span" data-provides="fileupload">
                        <label for="new-file-to-upload">Database</label>
                        <div class="input-append input-campaign">
                            <div style="height:30px; width:50%" class="uneditable-input">
                                <i class="icon-file fileupload-exists"></i> 
                                <span class="fileupload-preview"></span>
                            </div>
                            <span class="btn btn-file input-campaign">
                                <span class="fileupload-new">Select File</span>
                                <span class="fileupload-exists">Change</span>
                                <input id="file-to-upload" name="file-to-upload" type="file" />
                            </span>
                            <button id="button-file-upload-remove" class="btn fileupload-exists input-campaign" data-dismiss="fileupload">Remove</button>
                        </div>
                    </div>
                </form>
                <div>
                    <label for="new-voice-selector">Choose the Voice</label>
                    <select id="lang" class="form-control">
                        <option value='pt-male' selected>Português - Masculino</option>
                        <!--<option value='pt-female' selected>Português - Feminino</option>-->
                    </select>
                </div>  
                <div id="div-error-msg" style="border:1px solid white; margin: 16px 0px 16px 0px; max-height:40px; min-height:40px;" class="span"></div>
            </div>
            <button id="button-clear" class="left btn input-campaign">Clear</button>
            <button id="button-preview" class="right btn input-campaign">Preview</button>
            <div class="clear"></div>
        </div>
    </div>

    <!-- PROGRESS -->
    <div class="grid span6">
        <div class="grid-title">
            <div class="pull-left">Progress</div>
            <div class="pull-right"></div>
            <div class="clear"></div>
        </div>
        <div class="grid-content">
            <div class="div-wrapper">
                <div id="loader-content"></div>
                <div id="div-error-msg2" style="border:1px solid white; margin: 16px 0px 16px 0px; max-height:40px; min-height:40px;" class="span"></div>           
            </div>
            <button id="button-cancel" disabled="disabled" class="left btn button-preview">Cancel</button>
            <button id="button-create-campaign" disabled="disabled" class="right btn button-preview">Create Campaign</button>
            <div class="clear"></div>
        </div>
    </div>    

</div> 

<!-- PREVIEW -->
<div id='main-div-preview' style='display:none;' class="grid">
    <div class="grid-title">
        <div class="pull-left">Preview</div>
        <div class="pull-right"></div>
        <div class="clear"></div>
    </div>
    <table class="table table-mod">
        <thead>
            <tr>
                <th>#</th>
                <th>Phone Number</th>
                <th>First Message</th>
                <th>Second Message</th>
                <th>ID</th>
            </tr>
        </thead>
        <tbody>
            <tr id="tr1">
                <td>1</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr id="tr2">
                <td>2</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr id="tr3">
                <td>3</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table> 
</div>   

<!-- ERROR LOG -->
<div id='main-div-error' style='display:none;' class="grid">
    <div class="grid-title">
        <div class="pull-left">Result</div>
        <div class="pull-right"></div>
        <div class="clear"></div>
    </div>
    <div class="grid-content error-log">
    </div>
</div> 

<script>
    var ConvertedFile;
    var CampaignID;
    var ListID;

// BUTTON CLEAR
    $("#button-clear").click(function()
    {
        $("#new-campaign-name").val("");
        $("#button-file-upload-remove").click();
    });

// BUTTON PREVIEW

    $("#button-preview").click(function()
    {
        $("#div-error-msg").empty();
        if ($("#new-campaign-name").val() === "" || $("#new-campaign-name").val() === null)
        {
            makeAlert("#div-error-msg", "Campaign Name is Empty!", "Please choose a Campaign Name.", 2, 1, 0);
        }
        else
        if ($(".fileupload-preview").html() === "" || $(".fileupload-preview").html() === null)
        {
            makeAlert("#div-error-msg", "No File to Upload!", "Please choose a file to Upload.", 2, 1, 0);
        }
        else
        if (!/\.csv$|\.xls$|\.xlsx$|\.ods$|\.sxc$|\.txt$/i.test($(".fileupload-preview").html()))
        {
            makeAlert("#div-error-msg", "Wrong File Type!", "Please choose a file from the correct type.", 2, 1, 0);
        }
        else
        {
            UploadFile();
            $("#loader-content").append("<div class='div-loader'><span>Uploading and converting the file...</span><span id='file-load-icon' class='right'><img src='../images/load/1.gif'></span></div>");
        }

    });

// BUTTON CANCEL
    $("#button-cancel").click(function()
    {
        $.get("../intra_newcampaign/index.php",
                function(data)
                {
                    $(".inner-content").html(data);
                },
                "json");
    });


// BUTTON CREATE CAMPAIGN
    $("#button-create-campaign").click(function()
    {
        var CampaignName = $("#new-campaign-name").val();
        $("#button-cancel").attr("disabled", "disabled");
        $("#button-create-campaign").attr("disabled", "disabled");
        $("#loader-content").append("<div class='div-loader'><span>Creating the Campaign...</span><span id='campaign-load-icon' class='right'><img src='../images/load/1.gif'></span><div>");
        $.post("../intra_newcampaign/requests.php",
                {action: "CreateCampaign", sent_campaign_name: CampaignName, lang: $("#lang").val()},
        function(data)
        {
            CampaignID = data.result[0];
            ListID = data.result[1];
            $("#campaign-load-icon").html("<span class='label label-success'>Done!</span>");
            $("#loader-content").append("<div class='div-loader'><span>Loading new leads...</span><span id='leads-load-icon' class='right'><img src='../images/load/1.gif'></span><div>");
            $.post("../intra_newcampaign/requests.php",
                    {action: "LoadLeads", sent_converted_file: ConvertedFile, sent_list_id: ListID},
            function(data)
            {
                BuildNotifications(User);
                $("#main-div-preview").hide();
                if (data.errors === 0)
                {
                    $("#leads-load-icon").html("<span class='label label-success'>Done!</span>");
                    $(".error-log").append("<div>" + data.leads + " leads loaded sucessfully.<div>");
                    makeAlert("#div-error-msg2", "Campaign sucessfully created!", "", 4, 1, 0);
                    $("#main-div-error").show();
                    CurrentCampaignID = CampaignID;
                    CurrentCampaign = CampaignName;
                }
                else
                {
                    $("#leads-load-icon").html("<span class='label label-warning'>Done, with some errors.</span>");
                    makeAlert("#div-error-msg2", "Campaign created, but some errors were found.", "Click <span class='link link3'>here</span> to cancel this campaign and start over.", 2, 1, 0);
                    $(".error-log").empty();
                    var ok_leads = (data.leads - data.errors);
                    $(".error-log").append("<div><b>Total Leads: " + data.leads + "</b></div>").append("<div><b>Loaded: " + ok_leads + "</b></div>").append("<div style='margin-bottom:16px'><b>With Errors: " + data.errors + "</b></div>");
                    $.each(data.errortext, function(index, value)
                    {
                        $(".error-log").append("<div>" + value + "<div>");
                    });
                    $("#main-div-error").show();
                }
            },
                    'json');
        },
                'json');
    });


// LINKS INSIDE ALERTS
    $(".link1").live("click", function()
    {
        $.get("../intra_newcampaign/index.php",
                function(data)
                {
                    $(".inner-content").html(data);

                },
                "json");
    });

    $(document).on("click", ".link2", function()
    {
        $.get("../intra_realtime/index.php",
                function(data)
                {
                    $(".inner-content").html(data);
                    $(".sidebar-nav").removeClass("active");
                    $("a[menuid=3]").addClass("active");
                },
                "json");
    });

    $(document).on("click", ".link3", function()
    {
        $.post("../intra_newcampaign/requests.php",
                {action: "RollbackEverything", sent_campaign_id: CampaignID, sent_list_id: ListID},
        function(data)
        {
            BuildNotifications(User);
            $.get("../intra_newcampaign/index.php",
                    function(data) {
                        $(".inner-content").html(data);
                    },
                    "json");
        },
                "json");
    });

// LOADER ENGINE
    function UploadFile()
    {
        var FileData = new FormData();
        FileData.append("file-to-upload", document.getElementById('file-to-upload').files[0]);
        var xhr = new XMLHttpRequest();
        xhr.addEventListener("load", uploadComplete, false);
        xhr.addEventListener("error", uploadFailed, false);
        xhr.addEventListener("abort", uploadCanceled, false);
        xhr.open("POST", "../intra_newcampaign/upload.php");
        xhr.send(FileData);
    }


    function uploadFailed(evt)
    {
        alert("There was an error attempting to upload the file.");
    }

    function uploadCanceled(evt)
    {
        alert("The upload has been canceled by the user or the browser dropped the connection.");
    }

    function uploadComplete(evt)
    {
        ConvertedFile = evt.target.responseText;
        $("#file-load-icon").html("<span class='label label-success'>Done!</span>");
        $("#loader-content").append("<div class='div-loader'><span>Generating the preview...</span><span id='preview-load-icon' class='right'><img src='../images/load/1.gif'></span><div>");
        $.post("../intra_newcampaign/requests.php",
                {action: "GetPreview", sent_converted_file: ConvertedFile},
        function(data)
        {
            console.log(data);
            $.each($("#tr1").children(), function(index, value) {
                if (index !== 0) {
                    $(this).html(data[0][index - 1]);
                }
            });
            $.each($("#tr2").children(), function(index, value) {
                if (index !== 0) {
                    $(this).html(data[1][index - 1]);
                }
            });
            $.each($("#tr3").children(), function(index, value) {
                if (index !== 0) {
                    $(this).html(data[2][index - 1]);
                }
            });
            $("#main-div-preview").show();
            $("#preview-load-icon").html("<span class='label label-success'>Done!</span>");
            $("#button-create-campaign").html("<b>Create Campaign</b>");
        },
                "json");
        $(".input-campaign").attr("disabled", "disabled").attr("onclick", "return false;");
        $("#button-create-campaign").removeAttr("disabled");
        $("#button-cancel").removeAttr("disabled");
    }

// TEMPORARY PURGER
    $("#purge").click(function() {
        $.post("../intra_newcampaign/requests.php", {action: "PURGE"});
    });

</script>