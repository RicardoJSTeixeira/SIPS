function FieldsListBuilder(Flag, ModCampaign)
{
    var newModCampaign;
    var newModTable;

    if (Flag == "ALL")
    {
        FieldsElemInit();
        $(".span-active-campaign-name").html($("#campaign-name").val());
    }


    if ((Flag == "ALL" && $("#tbl-fields tr").length == 0) || Flag == "NEW" || Flag == "DIALOG")
    {

        if (Flag == "DIALOG") {
            newModCampaign = ModCampaign;
            newModTable = "#tbl-fields-copy";
            $("#tbl-fields-copy").empty();
        } else {
            newModCampaign = CampaignID;
            newModTable = "#tbl-fields";
        }

        $.ajax({
            type: "POST",
            url: "_campos_dinamicos-requests.php",
            dataType: "JSON",
            data: {action: "FieldsListBuilder",
                CampaignID: newModCampaign,
                AllFields: AllFields,
                FieldID: AvailFields[0],
                FieldDisplayName: $.trim($("#input-new-field").val()),
                FieldReadOnly: function() {
                    if ($("#input-new-field-readonly").parent().hasClass())
                        return 0;
                    else
                        return 1;
                },
                Flag: Flag
            },
            success: function(json)
            {

                if (typeof json.duplicate == "undefined" && typeof json.full == "undefined")
                {
                    var readonlyChecked;

                    var Editable;
                    var EditableText;
                    var EditableOpacity;

                    var Deletable;
                    var DeleteText;
                    var DeleteOpacity;

                    var ModFields;
                    var DisabledReadOnly;
                    var FieldsCrossCursor;

                    if (Flag == "NEW") {
                        AvailFields.splice(0, 1);
                        $("#input-new-field").val("")
                    }


                    $.each(json.name, function(index, value) {

                        if (Flag == "ALL")
                            $.each(AvailFields, function(index1, value1) {
                                if (value == value1) {
                                    AvailFields.splice(index1, 1)
                                }
                            })


                        if (json.name[index] == "FIRST_NAME" || json.name[index] == "PHONE_NUMBER" || json.name[index] == "ALT_PHONE" || json.name[index] == "ADDRESS3" || json.name[index] == "ADDRESS1" || json.name[index] == "POSTAL_CODE" || json.name[index] == "EMAIL" || json.name[index] == "COMMENTS")
                        {
                            Editable = "no-pointer";
                            EditableText = "";
                            EditableOpacity = "style='opacity:0.3 !important'";
                            Deletable = "no-pointer";
                            DeleteText = "";
                            DeleteOpacity = "style='opacity:0.3 !important'";
                        }
                        else
                        {
                            Editable = "fields-edit";
                            EditableText = "Configurar";
                            EditableOpacity = "";
                            Deletable = "fields-action-remove";
                            DeleteText = "Apagar Campo";
                            DeleteOpacity = "";
                        }



                        if (json.readonly[index] == 0)
                            readonlyChecked = "checked='checked'";
                        else
                            readonlyChecked = "";
                        if (Flag == "NEW") {
                            if ($("#input-new-field-readonly").parent().hasClass("checked")) {
                                readonlyChecked = "checked='checked'"
                            } else {
                                readonlyChecked = "";
                            }
                        }


                        if (Flag == "DIALOG")
                        {
                            ModFields = "";
                            disabledReadOnly = "disabled='disabled'";
                            FieldsCrossCursor = "";
                        }
                        else
                        {
                            ModFields = "<td class='css-td-list-filler'></td>\n\
							<td class='css-td-list-actions'><img class='css-img-list-actions " + Deletable + "' " + DeleteOpacity + " title='" + DeleteText + "' src='icons/mono_trash_16.png'></td>\n\
							<td class='css-td-list-actions'><img class='css-img-list-actions " + Editable + "' " + EditableOpacity + " fields-edit='" + json.name[index] + "' title='" + EditableText + "' src='icons/mono_wrench_16.png'></td>\n\
							";
                            disabledReadOnly = "";
                            FieldsCrossCursor = "fields-cross-cursor";
                        }


                        $("" + newModTable + "").prepend("\n\
						<tr id='" + json.name[index] + "' style='height:22px;'>\n\
							<td class='css-td-list-label " + FieldsCrossCursor + "'><label id='' fields-label='" + json.name[index] + "' class=" + FieldsCrossCursor + ">" + json.displayname[index] + "</label></td>\n\
							<td class='css-td-list-icon'><img class='css-img-list-icon' title='Editável' src='icons/mono_doc_empty_16.png'></td>\n\
							<td class='css-td-list-text'><input " + readonlyChecked + " " + disabledReadOnly + " fields-readonly='" + json.name[index] + "' class='fields-readonly' type='checkbox' id=''></td>\n\
							" + ModFields + "\n\
						</tr>\n\
						")
                    })


                    $(".fields-readonly").uniform();

                    OddEvenRefresh("", "tbl-fields");

                }
                else {
                    if (typeof json.duplicate !== "undefined")
                    {
                        $("#td-new-field-error").html("<b>Já existe um Campo Dinâmico com esse nome. Por favor altere o nome, e tente novamente.<b>");
                        $("#input-new-field").css("border-color", "red");
                    } else if (typeof json.full !== "undefined") {
                        $("#td-new-field-error").html("<b>Esgotou os campos disponiveis.<b>");
                        $("#input-new-field").css("border-color", "red");
                    }
                }




            }
        });
    }


}

function FieldsElemInit()
{

    $("#tbl-fields").sortable({
        stop: function(event, ui) {
            OddEvenRefresh("", "tbl-fields");
            ReOrderFields();
        },
        handle: ".fields-cross-cursor"
    });

    $("#btn-new-field").button()
    $("#btn-fields-apply-to-all-campaigns").button()
    $("#btn-fields-copy-from-campaign").button()
    $("#btn-dialog-fields-copy-from-campaign").button()

    $("#dialog-field-edit").dialog({
        title: "<table><tr><td><img class='dialog-icon-title' src='icons/mono_wrench_16.png'></td><td><span class='dialog-title'>Detalhes do Campo</span></td></tr></table>",
        autoOpen: false,
        height: 280,
        width: 390,
        resizable: false,
        buttons: {"Gravar": DialogFieldsEditOnSave, "Fechar": DialogClose},
        open: DialogFieldsEditOnOpen

    });


    $("#dialog-confirm-fields-apply-to-all-campaigns").dialog({
        title: "<table><tr><td><img class='dialog-icon-title' src='icons/mono_indent_decrease_16.png'></td><td><span class='dialog-title'>Alerta!</span></td></tr></table>",
        autoOpen: false,
        height: 250,
        width: 300,
        resizable: false,
        buttons: {"OK": DialogFieldsApplyToAllCampaignsOnSave,
            "Cancelar": DialogClose
        },
        open: function() {
        }
    });

    $("#dialog-fields-copy-from-campaign").dialog({
        title: "<table><tr><td><img class='dialog-icon-title' src='icons/mono_indent_increase_16.png'></td><td><span class='dialog-title'>Copiar Campos Dinâmicos de outra Campanha</span></td></tr></table>",
        autoOpen: false,
        height: 610,
        width: 390,
        resizable: false,
        buttons: {"Fechar": DialogClose},
        open: DialogFieldsCopyOnOpen

    });




}

function ReOrderFields()
{
    var SortedFields = $("#tbl-fields").sortable("toArray");

    $.ajax({
        type: "POST",
        url: "_campos_dinamicos-requests.php",
        dataType: "JSON",
        data: {action: "ReOrderFields",
            CampaignID: CampaignID,
            SortedFields: SortedFields

        },
        success: function(json)
        {
        }
    });

}

function FieldsReadOnlySwitch()
{

    var ReadOnly;

    if ($(this).parent().hasClass("checked"))
        ReadOnly = 0;
    else
        ReadOnly = 1;



    $.ajax({
        type: "POST",
        url: "_campos_dinamicos-requests.php",
        dataType: "JSON",
        data: {action: "FieldsReadOnlySwitch",
            CampaignID: CampaignID,
            FieldID: $(this).attr("fields-readonly"),
            ReadOnly: ReadOnly
        },
        success: function(json)
        {
        }
    });

}

function NewField()
{
    FieldsListBuilder("NEW");
}

function RemoveField()
{

    closestRow = $(this).closest("tr");

    $.ajax({
        type: "POST",
        url: "_campos_dinamicos-requests.php",
        dataType: "JSON",
        data: {action: "RemoveField",
            CampaignID: CampaignID,
            FieldID: closestRow.attr("id")
        },
        success: function(json)
        {
            console.log($(this).closest("tr").attr("id"));
            closestRow.remove();
            OddEvenRefresh("", "tbl-fields");

        }
    });
}

function DialogFieldsEditOnOpen()
{
    $("#input-field-edit-name").val($("label[fields-label='" + editedField + "']").html());
}

function DialogFieldsEditOnSave()
{
    var that = $(this);
    $.ajax({
        type: "POST",
        url: "_campos_dinamicos-requests.php",
        dataType: "JSON",
        data: {action: "DialogFieldsEditOnSave",
            CampaignID: CampaignID,
            FieldID: editedField,
            FieldName: $("#input-field-edit-name").val()
        },
        success: function(json)
        {
            if (json.flag || $("#input-field-edit-name").val() == $("label[fields-label='" + editedField + "']").html())
            {
                $("label[fields-label='" + editedField + "']").html($("#input-field-edit-name").val())
                that.dialog("close");
            }

        }
    });

}

function DialogFieldsApplyToAllCampaignsOnSave()
{

    $.ajax({
        type: "POST",
        url: "_campos_dinamicos-requests.php",
        dataType: "JSON",
        data: {action: "DialogFieldsApplyToAllCampaignsOnSave",
            CampaignID: CampaignID,
            AllowedCampaigns: AllowedCampaigns
        },
        success: function(json)
        {

        }
    });
    $(this).dialog("close");
}

function DialogFieldsCopyOnOpen()
{
    var ModAllowedCampaigns = AllowedCampaigns;
    $.each(ModAllowedCampaigns, function(index, value) {
        if (value == CampaignID) {
            ModAllowedCampaigns.splice(index, 1);
        }
    })

    $.ajax({
        type: "POST",
        url: "_campos_dinamicos-requests.php",
        dataType: "JSON",
        data: {action: "DialogFieldsCopyOnOpen",
            CampaignID: CampaignID,
            ModAllowedCampaigns: ModAllowedCampaigns
        },
        success: function(json)
        {
            $("#sel-fields-copy-campaign").empty();
            $.each(json.c_id, function(index, value) {

                if (index == 0) {
                    FieldsListBuilder("DIALOG", value);
                }

                $("#sel-fields-copy-campaign").append("<option value='" + json.c_id[index] + "'>" + json.c_name[index] + "</option>")

            })
        }
    });

}

function SelFieldsCopy()
{
    FieldsListBuilder("DIALOG", $("#sel-fields-copy-campaign option:selected").attr("value"))
}

function BtnCopyFields()
{

    $.ajax({
        type: "POST",
        url: "_campos_dinamicos-requests.php",
        dataType: "JSON",
        data: {action: "BtnCopyFields",
            CampaignID: CampaignID,
            CopyCampaignID: $("#sel-fields-copy-campaign option:selected").attr("value")
        },
        success: function(json)
        {
            $("#tbl-fields").empty();
            FieldsListBuilder("ALL");
        }
    });
}

function InputNewField()
{
    if ($("#td-new-field-error").html().length > 0) {
        $("#td-new-field-error").html("");
        $("#input-new-field").css("border-color", "#C0C0C0");
    }
}

$("body")
        .on("click", ".fields-readonly", FieldsReadOnlySwitch)
        .on("click", "#btn-new-field", NewField)
        .on("click", ".fields-action-remove", RemoveField)
        .on("click", ".fields-edit", {dialog: "#dialog-field-edit"}, DialogOpen)
        .on("click", "#btn-fields-apply-to-all-campaigns", {dialog: "#dialog-confirm-fields-apply-to-all-campaigns"}, DialogOpen)
        .on("click", "#btn-fields-copy-from-campaign", {dialog: "#dialog-fields-copy-from-campaign"}, DialogOpen)
        .on("change", "#sel-fields-copy-campaign", SelFieldsCopy)
        .on("click", "#btn-dialog-fields-copy-from-campaign", BtnCopyFields)
        .on("input", "#input-new-field", InputNewField)
















// CAMPOS DINAMICOS ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function submit_Dfields() {

    var sortedIDs = $("#dfields-sortable").sortable("toArray");
    var sortedLabels = new Array();
    var sortedReadOnly = new Array();
    var sortedOrder = new Array();

    $.each(sortedIDs, function(index, value) {
        sortedLabels.push($("label[for=i-" + value + "]").html())
        if ($("#ro-" + value).attr("src") == "icons/mono_document_16.png") {
            sortedReadOnly.push(1)
        } else {
            sortedReadOnly.push(0)
        }
        ;
        sortedOrder.push(index);
    })

    // VER FIELDS IN USE
    var array_in_use = new Array();
    $("#dfields-sortable li").each(function() {
        array_in_use.push($(this).attr("id"))
    })

    //VER OS QUE FALTAM
    var current_array_item = "";
    $.each(array_in_use, function(index, value) {
        current_array_item = value;
        $.each(all_fields, function(index, value) {
            if (current_array_item == value) {
                all_fields.splice(index, 1)
            }
        })
    })




    $.ajax({
        type: "POST",
        url: "_campos_dinamicos-requests.php",
        data: {action: "submit_dfields",
            sent_campaign_id: CampaignID,
            sent_sortedIDs: sortedIDs,
            sent_sortedLabels: sortedLabels,
            sent_sortedReadOnly: sortedReadOnly,
            sent_fillers: all_fields,
            sent_sortedOrder: sortedOrder
        },
        success: function(data) {

        }
    });
}

////////



$(".remove-field").live("click", function() {
    $(this).parent().parent().parent().parent().parent().remove();
})

var edit_id = "";
$(".edit-field").live("click", function() {
    edit_id = $(this).parent().parent().parent().parent().parent().attr("id");
    $("#dialog-edit-field-name").dialog("open");
})

var avail_fields;
var all_fields = new Array('vendor_lead_code', 'phone_number', 'title', 'first_name', 'middle_initial', 'last_name', 'address1', 'address2', 'address3', 'city', 'state', 'province', 'postal_code', 'country_code', 'date_of_birth', 'alt_phone', 'email', 'security_phrase', 'owner', 'comments', 'extra1', 'extra2', 'extra3', 'extra4', 'extra5', 'extra6', 'extra7', 'extra8', 'extra9', 'extra10', 'extra11', 'extra12', 'extra13', 'extra14', 'extra15')


////////

$('#new-field').click(function() {
    var current_li = "";
    avail_fields = new Array('PHONE_NUMBER', 'TITLE', 'FIRST_NAME', 'MIDDLE_INITIAL', 'LAST_NAME', 'ADDRESS1', 'ADDRESS2', 'ADDRESS3', 'CITY', 'STATE', 'PROVINCE', 'POSTAL_CODE', 'COUNTRY_CODE', 'DATE_OF_BIRTH', 'ALT_PHONE', 'EMAIL', 'SECURITY_PHRASE', 'COMMENTS', 'extra1', 'extra2', 'extra3', 'extra4', 'extra5', 'extra6', 'extra7', 'extra8', 'extra9', 'extra10', 'extra11', 'extra12', 'extra13', 'extra14', 'extra15');
    $("#dfields-sortable li").each(function(index1, value1) {
        current_li = $(this).attr("id");

    })
    var new_field_id = avail_fields[0];
    var new_field_value = $('#add-field-name').val();
    var new_field_readonly = "";
    $(".rdonly-chooser div span").each(function() {

        if ($(this).hasClass("checked") && $(this).children().attr("id") == "new-field-rdonly-no") {
            new_field_readonly = "icons/mono_document_empty_16.png";
            return false;
        } else {
            new_field_readonly = "icons/mono_document_16.png";
            return false;
        }

    })
    $('#dfields-sortable').append("<li id='" + new_field_id + "' class='cursor-move'>\n\
                                    <table>\n\
                                    <tr class='height24'><td width='16px'><img class='mono-icon cursor-pointer remove-field' src='icons/mono_cancel_16.png'></td>\n\
                                    <td width='16px'><img id='ro-" + new_field_id + "' class='mono-icon cursor-pointer icon-readonly' src='" + new_field_readonly + "'></td>\n\
                                    <td><label class='cursor-move' style='display:inline; margin-left:3px' for='i-" + new_field_id + "'>" + new_field_value + "</label></td>\n\
                                    <td width='16px'><img class='mono-icon cursor-pointer edit-field' src='icons/mono_document_edit_16.png'></td>\n\
                                    </tr>\n\
                                    </table>\n\
                                    </li>\n\
                                    ")
    $("#add-field-name").val("");
})

$('.icon-readonly').live("click", function() {
    if ($(this).attr("src") == "icons/mono_document_16.png") {
        $(this).attr("src", "icons/mono_document_empty_16.png")
    } else {
        $(this).attr("src", "icons/mono_document_16.png")
    }
})

$("#dialog-edit-field-name").dialog({
    title: ' <span style="font-size:13px; color:black">Alteração do Nome do Campo</span> ',
    autoOpen: false,
    height: 280,
    width: 250,
    resizable: false,
    buttons: {"Gravar": function() {
            $(this).dialog("close");
            $("label[for=i-" + edit_id + "]").html($("#new-field-name").val())
        }, "Fechar": function() {
            $(this).dialog("close");
        }},
    open: function() {
        $("button").blur();
        $("#new-field-name").val($("label[for=i-" + edit_id + "]").html())
    }
});

$("#dfields-sortable").sortable();


$("#campaign-copy-button").click(function() {
    $.ajax({
        type: "POST",
        url: "_campos_dinamicos-requests.php",
        dataType: "JSON",
        data: {action: "copy_dfields",
            sent_campaign_id: CampaignID,
            sent_campaign_id_copy: $("#campaign-copy option:selected").val(),
        },
        success: function(data) {
            $("#dfields-sortable").empty();

            $.each(data.name, function(index, value) {

                if (value == "PHONE_NUMBER" || value == "ALT_PHONE" || value == "ADDRESS3" || value == "FIRST_NAME" || value == "COMMENTS")
                {
                    var cancel = "<td width='19px'></td>";
                }
                else
                {
                    var cancel = "<td width='16px'><img class='mono-icon cursor-pointer remove-field' src='icons/mono_cancel_16.png'></td>";
                }

                $("#dfields-sortable").append("<li id='" + value + "' class='cursor-move'>\n\
                    <table>\n\
                    <tr class='height24'>\n\
                    " + cancel + "\n\
                    <td width='16px'><img id='ro-" + value + "' class='mono-icon cursor-pointer icon-readonly' src='icons/mono_document_empty_16.png'></td>\n\
                    <td><label class='cursor-move' style='display:inline; margin-left:3px' for='i-" + value + "'>" + data.display_name[index] + "</label></td>\n\
                    <td width='16px'><img class='mono-icon cursor-pointer edit-field' src='icons/mono_document_edit_16.png'></td>\n\
                    <tr>\n\
                    </table>\n\
                    </li>\n\
                    ")



            })





        }
    });




})

/*
 
 
 if($default_fields[$i]=="phone_number" || $default_fields[$i]=="alt_phone" || $default_fields[$i]=="address3" || $default_fields[$i]=="first_name" || $default_fields[$i]=="comments") 
 { $td_cancel = "<td width='19px'> </td>"; }
 else 
 { $td_cancel = "<td width='16px'> <img class='mono-icon cursor-pointer remove-field' src='icons/mono_cancel_16.png'> </td> "; }
 $dinamic_fields .= "<li id='$default_fields[$i]' class='cursor-move'>
 <table>
 <tr class='height24'>
 
 $td_cancel 
 <td width='16px'> <img id='ro-$default_fields[$i]' class='mono-icon cursor-pointer icon-readonly' src='icons/mono_document_empty_16.png'> </td>
 <td><label class='cursor-move' style='display:inline; margin-left:3px' for='i-$default_fields[$i]'>$default_labels[$i]</label></td>
 <td width='16px'><img class='mono-icon cursor-pointer edit-field' src='icons/mono_document_edit_16.png'></td>
 </tr>
 </table>
 </li>";
 
 
 
 
 */

