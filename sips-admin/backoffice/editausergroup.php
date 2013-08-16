<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
        <title>SIPS</title>


        <script type="text/javascript" src="/jquery/jquery-1.8.3.js"></script>
        <script type="text/javascript" src="/bootstrap/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/bootstrap-colorpicker.js"></script>
        <script type="text/javascript" src="/bootstrap/js/bootstrap-tagmanager.js"></script>
        <script type="text/javascript" src="/bootstrap/js/chosen.jquery.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/warnings-api.js"></script>

        <link type="text/css" rel="stylesheet" href="/bootstrap/css/style.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap-tagmanager.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap-responsive.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/chosen.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/colorpicker.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/icon/font-awesome.css" />

        <script type="text/javascript" src="/jquery/jsdatatable/plugins/plugin.fnAjaxReload.js"></script>
        <script type="text/javascript" src="/jquery/jqueryUI/jquery-ui-1.9.2.custom.min.js"></script>
        <script type="text/javascript" src="/jquery/jqueryUI/plugins/datetimepicker.js"></script>
        <script type="text/javascript" src="/jquery/jqueryUI/language/pt-pt.js"></script>
        <link type="text/css" rel="stylesheet" href="/jquery/themes/flick/bootstrap.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/demo_table.css" />
        <?php
        $self = count(explode('/', $_SERVER['PHP_SELF']));
        for ($i = 0; $i < $self - 2; $i++) {
            $path.="../";
        }
        define("ROOT", $path);
        require(ROOT . "ini/dbconnect.php");
        require "../functions.php";


        foreach ($_POST as $key => $value) {
            ${$key} = $value;
        }
        foreach ($_GET as $key => $value) {
            ${$key} = $value;
        }
//auth
        $PHP_AUTH_USER = $_SERVER['PHP_AUTH_USER'];
        $PHP_AUTH_PW = $_SERVER['PHP_AUTH_PW'];
        $stmt = "SELECT user_group,modify_usergroups from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW';";
        $rslt = mysql_query($stmt, $link) or die(mysql_error());
        $row = mysql_fetch_assoc($rslt);
        $LOGmodify_usergroups = $row['modify_usergroups'];

        if ($LOGmodify_usergroups == 0) {
            echo "Não tem permissões para editar grupos :-(";
            exit;
        }
        
if ($row['user_group'] != "ADMIN") {
$whereLOGallowed_campaignsSQL = "WHERE campaign_id REGEXP '^$allowed_camps_regex$'";}
//auth --

   //data
        $stmt = "SELECT user_group,group_name,allowed_campaigns from vicidial_user_groups where user_group='$user_group';";
        $rslt = mysql_query($stmt, $link) or die(mysql_error());
        $row = mysql_fetch_assoc($rslt);
        $user_group = $row[user_group];
        $group_name = $row[group_name];
        $allowed_campaigns = $row[allowed_campaigns];
        $allowed_camps_regex = str_replace(" ", "|", trim(rtrim($allowed_campaigns, " -")));


        $allowed_campaigns = preg_replace("/ -$/", "", $allowed_campaigns);
        $campaigns = explode(" ", $allowed_campaigns);

        if (isset($campaigns_selected))
            $campaigns = $campaigns_selected;
        if (isset($group_name_new))
            $group_name = $group_name_new;
        if (isset($user_group_new))
            $user_group = $user_group_new;


        $campaigns_value = '';


        $stmt = "SELECT campaign_id,campaign_name from vicidial_campaigns $whereLOGallowed_campaignsSQL order by campaign_name";
        $rslt = mysql_query($stmt, $link) or die(mysql_error());


        if ($user_group == 'ADMIN') {
            $campaigns_all['-ALL-CAMPAIGNS-'] = "ALL-CAMPAIGNS";
        }

        while ($rowx = mysql_fetch_row($rslt)) {
            $campaigns_all[$rowx[0]] = $rowx[1];
        }

        foreach ($campaigns as $value) {
            $campaigns_value .= " $value";
        }

        if (strlen($campaigns_value) > 2) {
            $campaigns_value .= " -";
        }
//data --

  //insert
        if ($ADD == 411111) {
            if ($LOGmodify_usergroups == 1) {

                if ((strlen($user_group) < 2) or (strlen($group_name) < 2)) {
                    ?>
                    <script>
                        $(function() {
                            makeAlert("#wr", "USER GROUP NOT MODIFIED", "Group name and description must be at least 2 characters in length.", 1, true, false);
                        });
                    </script>
                    <?php
                } else {

                    $stmt = "UPDATE vicidial_user_groups set user_group='$user_group', group_name='$group_name',allowed_campaigns='$campaigns_value',agent_xfer_consultative='Y',agent_xfer_dial_override='Y',agent_xfer_vm_transfer='Y',agent_xfer_blind_transfer='Y',agent_xfer_dial_with_customer='Y',agent_xfer_park_customer_dial='Y',agent_fullscreen='N' where user_group='$OLDuser_group';";
                    $rslt = mysql_query($stmt, $link) or die(mysql_error());
                    ?> 
                    <script>
                        $(function() {
                            makeAlert("#wr", "SUCCESS", "USER GROUP MODIFIED :-).", 4, false, false);
                        });
                    </script>
                    <?php
                    ### LOG INSERTION Admin Log Table ###
                    $SQL_log = "$stmt|";
                    $SQL_log = ereg_replace(';', '', $SQL_log);
                    $SQL_log = addslashes($SQL_log);
                    $stmt = "INSERT INTO vicidial_admin_log set event_date='$SQLdate', user='$PHP_AUTH_USER', ip_address='$ip', event_section='USERGROUPS', event_type='MODIFY', record_id='$user_group', event_code='ADMIN MODIFY USER GROUP', event_sql=\"$SQL_log\", event_notes='';";
                    if ($DB) {
                        echo "|$stmt|\n";
                    }
                    $rslt = mysql_query($stmt, $link) or die(mysql_error());
                }
            } else {
                ?>
                <script>
                    $(function() {
                        makeAlert("#wr", "USER GROUP NOT MODIFIED", "Não tem permissão para visualizar esta página.", 1, true, false);
                    });
                </script>
                <?php
                exit;
            }
        }
  //insert --
        ?>


        <style>
            .pickListButtons{
                display:inline-block;
                width: 110px;
                padding: 0px 2px;
                text-align:center;
                margin:0.2em 0;
                vertical-align: middle;
            }

            .pickListContainer {
                display:inline-block;
                margin:0.2em 0;
                padding:0;
                visibility:visible;
            }
            .pickListContainer select {
                display:inline-block;
                height:9em;
            }
            .pickListContainer button {
                margin-bottom: 2px
            }
            .pickListContainer button img {
                margin-bottom:0;
            }
            .pickListContainer div {
                clear:both;
            }
            .pickListFrom lable, .pickListTo lable{
                display:block;
            }
            .pickListFrom, .pickListTo{
                display:inline-block;
            }

            #loader{
                background: #f9f9f9;
                top: 0px;
                left: 0px;
                position: absolute;
                height: 100%;
                width: 100%;
                z-index: 2;
            }
            #loader > img{
                position:absolute;
                left:50%;
                top:50%;
                margin-left: -33px;
                margin-top: -33px;
            }
            .view-button{
                margin-left: 0.5em;
            }option:disabled, select[disabled] > option {
                background: #c0c0c0;
                display:none;
            }
        </style>
    </head>

    <body>
        <div id="loader"><img src="/images/icons/big-loader.gif"/></div>
        <div class=content>
            <div class="grid">
                <div class="grid-title">
                    <div class="pull-left">Editar Grupo de Utilizadores</div>
                    <div class="pull-right"></div>
                    <div class="clear"></div>
                </div>
                <div class="grid-content">
                    <div id="wr"></div>
                    <form method="post" target="_self" >
                        <input type=hidden name=ADD value=411111 >
                        <input type=hidden name=OLDuser_group value="<?= $user_group ?>">
                        <div class="formRow op fix">
                            <label>Nome do Grupo:</label>
                            <div class="formRight">
                                <?= $user_group ?>
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Descrição do Grupo:</label>
                            <div class="formRight">
                                <input type=text name=group_name_new class="span" maxlength=40 value="<?= $group_name ?>">
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Campanhas Autorizadas:</label>
                            <div class="formRight">
                                <select name="campaigns_selected[]" multiple="" class='chzn-select'>
                                    <?= populate_multi_options($campaigns_all, $campaigns) ?>
                                </select>

                            </div>
                        </div> 


                        <div class="clear"></div>
                        <div class="seperator_dashed"></div>
                        <p class="text-right">
                            <button class="btn btn-success">Gravar</button>
                        </p>
                </div>
            </div>


            <?php
### list of users in this user group

            $stmt = "SELECT user_id,user,full_name,user_level,active from vicidial_users where user_group='$user_group'";
            $rsltx = mysql_query($stmt, $link) or die(mysql_error());
            $users_to_print = mysql_num_rows($rsltx);
            ?>
            <div class="grid">
                <div class="grid-title">
                    <div class="pull-left">Utilizadores neste Grupo: <?= $users_to_print ?></div>
                    <div class="pull-right"></div>
                    <div class="clear"></div>
                </div>
                <div class="grid-content">
                    <table id='lists' class="table table-mod-2 table-striped" >
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Username</th>
                                <th>Nível</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($rcolab = mysql_fetch_assoc($rsltx)) { ?>
                                <tr>
                                    <td><?= $rcolab[full_name] ?></td>
                                    <td><?= $rcolab[user] ?></td>
                                    <td><?= $rcolab[user_level] ?>
                                        <div class="view-button"><a href='editauser.php?user=<?= $rcolab['user'] ?>' target='_self' class="btn  btn-mini"><i class="icon-pencil"></i>Editar</a></div>
                                        <div class="view-button"><a href='presencas.php?user=<?= $rcolab['user'] ?>' target='_self' class="btn  btn-mini" ><i class="icon-calendar"></i> Faltas</a></div>
                                        <div class="view-button"><a href='../user_stats.php?user=<?= $rcolab['user'] ?>' target='_self'  class="btn  btn-mini"><i class="icon-bar-chart"></i> Estatística</a></div>
                                        <div class="view-button"><a href='gravacoes.php?user=<?= $rcolab['user'] ?>' target='_self'  class="btn  btn-mini"><i class="icon-headphones"></i> Gravações</a></div>
                                        <div class="view-button"><a href="#" data-userid='<?= $rcolab['user_id'] ?>' data-active="<?= $rcolab['active'] ?>" class="btn  btn-mini activator"> <i class="icon-check<?= ($rcolab['active'] == "Y") ? "" : "-empty" ?>" ></i><span><?= ($rcolab['active'] == "Y") ? "Activo" : "Inactivo" ?></span></a></div>
                                    </td>
                                </tr>
                            <?php } ?>

                        </tbody>
                    </table>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
        <script>
            /**
             * Picklists - jQuery plugin for converting a multiple <select> into two, allowing users to easily select multiple items
             * Based on code from Multiple Selects Plugin: http://code.google.com/p/jqpickLists/
             *
             * Copyright (c) 2007 George Smith
             * Dual licensed under the MIT and GPL licenses:
             * http://www.opensource.org/licenses/mit-license.php
             * http://www.gnu.org/licenses/gpl.html
             *
             * Version: 0.1
             */

            /**
             * Adds multiple select behaviour to a <select> element.
             * This allows options to be transferred to a different select using mouse double-clicks, or multiple options at a time via a button element.
             *
             * @usage
             * $('#simple').pickList(options);
             */
            jQuery.fn.pickList = function(settings)
            {
                // set some sensible defaults
                settings = jQuery.extend({
                    buttons: true,
                    removeText: 'remove',
                    removeIcon: '',
                    addText: 'add',
                    addIcon: '',
                    beforeFrom: '',
                    beforeTo: '',
                    testMode: false
                }, settings);
                return this.each(function() {
                    if (this.multiple == false) {
                        return;
                    }
                    var name = this.name;
                    if (!this.id) {
                        // we really need an id for this to work properly, so let's create one 
                        // (needs error checking to see if id already exists)
                        this.id = this.name.match(/[a-zA-Z0-9]+/);
                    }
                    var id = this.id;

                    var select = jQuery('#' + id);

                    // add onsubmit stuff to the form so all the selected elements get passed through correctly
                    jQuery(this.form)
                            .submit(function(e) {
                        if (settings.testMode)
                            e.preventDefault();
                        for (var item = 0; item < this.pickLists.length; item++)
                        {
                            selectAll(this.pickLists[item]);
                        }
                    })
                            .each(function() {
                        if (this.pickLists == undefined)
                            this.pickLists = new Array();
                        // myAlert('id:' + id);
                        this.pickLists.push(id);
                    });

                    var container = jQuery(this).parent().addClass('pickListContainer');

                    //if (settings.beforeFrom) {
                    select.before($('<div class="pickListFrom">').prepend($("<lable>").text(settings.beforeFrom)));
                    //}

                    select.parent().find(".pickListFrom").append($('<select id="from_' + id + '" multiple="multiple">'));
                    if (settings.buttons)
                    {
                        select.before($('<div class="pickListButtons">')
                                .append($('<button id="b_to_' + id + '" class="btn">')
                                .html(button(settings.addText, settings.addIcon)))
                                .append($('<button id="b_from_' + id + '" class="btn">')
                                .html(button(settings.removeText, settings.removeIcon)))
                                );
                    }
                    moveAllOptions(id, 'from_' + id);
                    //RHELL-I- adicionado para seleccionar os seleccionados...
                    addTo('from_' + id, id);
                    //RHELL-E- adicionado para seleccionar os seleccionados...
                    //if (settings.beforeTo)
                    select.wrap($('<div class="pickListTo">')).before($("<lable>").text(settings.beforeTo));
                    console.log(select);

                    jQuery('#from_' + id).dblclick(function() {
                        addTo('from_' + id, id);
                    });
                    jQuery('#' + id).dblclick(function() {
                        moveFrom(id, 'from_' + id);
                    });

                    if (settings.buttons)
                    {
                        jQuery("#b_to_" + id).click(function(e) {
                            e.preventDefault();
                            addTo('from_' + id, id);
                        });
                        jQuery("#b_from_" + id).click(function(e) {
                            e.preventDefault();
                            moveFrom(id, 'from_' + id);
                        });
                    }

                });
                function button(text, Icon)
                {
                    icon = (Icon != '') ? '<i class="' + Icon + '"></i>' : '';
                    return (icon + text);
                }
                function selectAll(me) {
                    $('#' + me + ' option').attr('selected', true);
                    $('#from_' + me + ' option').attr('selected', false);
                }
                function addTo(from, to)
                {
                    var dest = jQuery("#" + to)[0];

                    jQuery("#" + from + " option:selected").clone().each(function() {
                        if (this.disabled == true)
                            return
                        jQuery(this)
                                .appendTo(dest)
                                .attr("selected", false);
                    });
                    jQuery("#" + to + " option").sortOption();
                    jQuery("#" + from + " option:selected")
                            .attr("selected", false)
                            .attr("disabled", true);
                    jQuery("#" + from + " option").toggleOption();
                }
                function moveFrom(from, to)
                {
                    jQuery("#" + from + " option:selected").each(function()
                    {
                        select = jQuery(this);
                        val = select
                                .attr("selected", false)
                                .val();
                        select.remove();
                        jQuery('option:disabled', jQuery("#" + to)).each(function()
                        {
                            if (this.value == val)
                            {
                                jQuery(this).attr("disabled", false);
                            }
                        });
                    });
                    jQuery("#" + from + " option").sortOption();

                    jQuery("#" + to + " option:not(:disabled)").toggleOption();


                }
                function moveAllOptions(from, to) {
                    jQuery("#" + to).html(jQuery("#" + from).html())
                            .find('option:selected');
                    //RHELL -- removido para ficar com os seleccionados....  .attr("selected", false);
                    jQuery("#" + from).html('');
                }


            };

            jQuery.fn.toggleOption = function(  ) {
                jQuery(this).each(function() {

                    show = !$(this).attr("disabled");
                    jQuery(this).toggle(show);
                    if (show) {
                        if (jQuery(this).parent('span.toggleOption').length)
                            jQuery(this).unwrap( );
                    } else if (!jQuery(this).parent('span.toggleOption').length) {
                        jQuery(this).wrap('<span class="toggleOption" style="display: none;" />');
                    }
                });

            };

            jQuery.fn.sortOption = function() {
                var arr = this.map(function(_, o) {
                    return {
                        t: $(o).text(),
                        v: o.value
                    };
                }).get();
                arr.sort(function(o1, o2) {
                    return o1.t > o2.t ? 1 : o1.t < o2.t ? -1 : 0;
                });
                this.each(function(i, o) {
                    o.value = arr[i].v;
                    $(o).text(arr[i].t);
                });
            };


        </script>

        <script>
            var otable;
            $(function() {
                otable = $('#lists').dataTable({
                    "sPaginationType": "full_numbers",
                    "oLanguage": {
                        "sUrl": "/jquery/jsdatatable/language/pt-pt.txt"
                    }
                });
                //$(".chzn-select").chosen({no_results_text: "Não foi encontrado."});
                $(".chzn-select").pickList({
                    "removeIcon": "icon-arrow-left",
                    "removeText": "Remover",
                    "addIcon": "icon-arrow-right",
                    "addText": "Adicionar",
                    "beforeFrom": 'Campanhas',
                    "beforeTo": 'Selecionadas'
                });
                $(".activator").on("click", function() {
                    var that = $(this);
                    eu = that;
                    var active = (that.data("active") == "Y") ? "N" : "Y";
                    var user_id = that.data("userid");
                    $.post("_requests.php", {action: "user_change_status", user: user_id, active: active}, function(data) {
                        that.data("active", active);
                        that.find("i").attr("class", "icon-check" + ((active == "N") ? "-empty" : ""))
                                .parent().find("span").text((active == "Y") ? "Activo" : "Inactivo");
                    }, "json");
                    return false;
                });


                $("#loader").fadeOut("slow");
            });

        </script>
    </body>
</html>

