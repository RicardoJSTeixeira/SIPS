<?
#HEADER
$self = count(explode('/', $_SERVER['PHP_SELF']));
for ($i = 0; $i < $self - 2; $i++) {
    $header.="../";
}
define("ROOT", $header);
require(ROOT . "ini/header.php");



    if (!isset($_SERVER[PHP_AUTH_USER])) {
           echo "<script> window.location='../index.php'</script>";
    }


    $username = $_SERVER[PHP_AUTH_USER];
    $password = $_SERVER[PHP_AUTH_PW];
    $query = "SELECT user_level FROM vicidial_users where user='$username' and pass='$password'";
    $query = mysql_query($query, $link);
    if (mysql_num_rows($query)) {
        $row = mysql_fetch_row($query);
        if ($row[0] < 6) {
           echo "<script> window.location='../index.php?logout=yes'</script>";
        }
    }else{
           echo "<script> window.location='../index.php?logout=yes'</script>";
    }
?>

<script type="text/javascript" >
    function SetFrameHeight() {
        
        $("#mbody").height($(window).height() - 80);

    }

    window.onresize = SetFrameHeight;

    function ConfirmDelete(Link, Href) {

        var rsp = confirm('De certeza que quer eliminar este item ?');
        if (rsp) {
            Link.href = Href;
        };

    }
        
    function open_page(url){
        $("#mbody").attr('src',url);
    }
     
    $(function() {
        $('#menu-content > .cc-submenu').hide();  
        $('#menu-content > .cc-menu').click(function() {
          
            document.cookie= $("#menu-content > .cc-menu").index(this);
                    
          
            $(this).next('.cc-submenu').slideToggle('fast')
            .siblings('.cc-submenu:visible').slideUp('fast');
        });
      
        var checkCookie = document.cookie;
  
        if (checkCookie != "") {
            $('#menu-content > .cc-menu:eq('+checkCookie+')').next().show();
        }
      
        SetFrameHeight();
    });
           

</script>
</head>
<body id="ib" >

    <div style='padding:0px 10px 0px 10px;'>

        <div id='cc-header'>

            <table border=0 width='100%'>
                <tr>
                    <td width='200px' ><img style='float:left' src='/images/pictures/sipslogo_header.png' id="menu-hide" ></td>
                    <td><a href="../index.php?logout=yes" ><img style='height:60px;float:right;' src='/images/pictures/new_logo.png' /></a></td>
                </tr>
            </table>

        </div>

        <table width='100% ' align=center>
            <tr>
                <td style="width:200px" valign=top id="menu"><!-- MENU -->
                    <div class='cc-menu'  style='margin-top:1px;'>
                        <table>
                            <tr>
                                <td> SIPS.Menu</td><td><img class='cc-menu-img' id='img1'  src='/images/icons/headphone_mic_16.png' /></td>
                            </tr>
                        </table>
                    </div>
                    <div class='cc-submenu' style='display:block'>

                        <table>
                                    
                            <?php
                            $query = "SELECT user_level, user_group FROM vicidial_users where user='$_SERVER[PHP_AUTH_USER]';";
                            $query = mysql_query($query, $link);
                            $row = mysql_fetch_assoc($query);
                            $usrgrp = $row[user_group];

                            if ($usrgrp == 'ADMIN') {
                            
                                $smt = "SELECT * FROM sips_admin_links";
                            } else {
                                $smt = "SELECT * FROM sips_admin_links WHERE grupo = '$usrgrp'";
                            }
                            
                            if ($usrgrp != 'AreaSalesManager') { echo "<tr style='cursor:pointer' onClick=parent.mbody.location='realtime/realtime_report.php' >
                                <td width=32px><img src='/images/icons/color_swatch_32.png' /></td><td>Painel Geral</td></tr>
                            "; }
                            
                            $rslt = mysql_query($smt, $link) or die(mysql_error());

                            for ($i = 0; $i < mysql_num_rows($rslt); $i++) {

                                $curLink = mysql_fetch_assoc($rslt);

                                echo "<tr style='cursor:pointer' onclick=parent.mbody.location='" . $curLink['url'] . "' ><td width=32px><img src=$curLink[imgpath] /></td>
							<td>$curLink[label]</td></tr>";
                            }
                            ?>

                        </table>
                    </div>


                    <DIV id="menu-content">
                        <?php
                        if (!mysql_num_rows(mysql_query("SHOW TABLES LIKE 'sips_admin_menu_groups'", $link))) {

                            $create1 = "CREATE TABLE IF NOT EXISTS `sips_admin_menu_groups` (
                                    `id_menu_group` int(11) NOT NULL AUTO_INCREMENT,
                                    `title` varchar(255) NOT NULL,
                                    `img` varchar(255) NOT NULL,
                                    `order` int(11) NOT NULL,
                                    `allowedgroups` varchar(1000) NOT NULL,
                                    `active` tinyint(1) NOT NULL,
                                    PRIMARY KEY (`id_menu_group`)
                                  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;";


                            $create2 = "INSERT INTO `sips_admin_menu_groups` (`id_menu_group`, `title`, `img`, `order`, `allowedgroups`, `active`) VALUES
                                  (1, 'Operadores', 'headphone_mic_16.png', 1, '--ALL_GROUPS--', 1),
                                  (2, 'Campanhas', 'globe_model_16.png', 2, '--ALL_GROUPS--', 1),
                                  (3, 'Base de Dados', 'database_lightning_16.png', 3, '--ALL_GROUPS--', 1),
                                  (4, 'Inbound', 'comments_16.png', 4, '--ALL_GROUPS--', 1),
                                  (5, 'SMS', 'message_16.png', 5, '--ALL_GROUPS--', 1),
                                  (6, 'Estatísticas', 'chart_pie_16.png', 6, '--ALL_GROUPS--', 1),
                                  (7, 'Administrador', 'role_16.png', 7, '--ALL_GROUPS--', 1),
                                  (8, 'Manuais de Utilização', 'file_extension_pdf_16.png', 8, '--ALL_GROUPS--', 1);";

                            $create3 = "CREATE TABLE IF NOT EXISTS `sips_admin_menu_links` (
                                  `id_menu_links` int(11) NOT NULL AUTO_INCREMENT,
                                  `title` varchar(255) NOT NULL,
                                  `img` varchar(255) NOT NULL,
                                  `id_menu_groups` int(11) NOT NULL,
                                  `url` varchar(1000) NOT NULL,
                                  `order` int(11) NOT NULL,
                                  `active` tinyint(4) NOT NULL,
                                  `allowedgroups` varchar(1000) NOT NULL,
                                  PRIMARY KEY (`id_menu_links`)
                                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=42 ;";



                            $create4 = "INSERT INTO `sips_admin_menu_links` (`id_menu_links`, `title`, `img`, `id_menu_groups`, `url`, `order`, `active`, `allowedgroups`) VALUES
                                (1, 'Listar Operadores', 'user_32.png', 1, 'backoffice/listausers.php', 1, 1, '--ALL_GROUPS--'),
                                (2, 'Criar Novo Operador', 'user_add_32.png', 1, 'backoffice/novouser.php', 2, 1, '--ALL_GROUPS--'),
                                (3, 'Listar Grupos de Operadores', 'group_32.png', 1, 'admin.php?ADD=100000', 3, 1, '--ALL_GROUPS--'),
                                (4, 'Criar Grupo de Operadores', 'group_add_32.png', 1, 'admin.php?ADD=111111', 4, 1, '--ALL_GROUPS--'),
                                (5, 'Ver Faltas e Pausas', 'calendar_32.png', 1, 'backoffice/faltas.php', 5, 1, '--ALL_GROUPS--'),
                                (6, 'Gestor de Campanhas', 'wizard.png', 2, 'campaign_manager/campaign_create.php', 1, 1, '--ALL_GROUPS--'),
                                (7, 'Listar Campanhas', 'events_32.png', 2, 'admin.php?ADD=10', 2, 1, '--ALL_GROUPS--'),
                                (8, 'Listar Feedbacks', 'telephone_go_32.png', 2, 'admin.php?ADD=32', 3, 1, '--ALL_GROUPS--'),
                                (9, 'Listar Códigos de Pausa', 'control_pause_blue_32.png', 2, 'admin.php?ADD=37', 4, 1, '--ALL_GROUPS--'),
                                (10, 'Gerir Reciclagem de contactos', 'database_refresh_32.png', 2, 'admin.php?ADD=35', 5, 1, '--ALL_GROUPS--'),
                                (11, 'Gerir Contactos Alternativos', 'vcard_32.png', 2, 'admin.php?ADD=36', 6, 1, '--ALL_GROUPS--'),
                                (12, 'Gerir Scripts Dinâmicos', 'script_edit_32.png', 2, 'admin_lists_custom.php', 7, 1, '--ALL_GROUPS--'),
                                (13, 'Gerir Calendários', 'calendar_32.png', 2, 'reservas/views/sch_admin.php', 8, 1, '--ALL_GROUPS--'),
                                (14, 'Listar Bases de Dados', 'database_gear_32.png', 3, 'lists/admin_list.php', 1, 1, '--ALL_GROUPS--'),
                                (15, 'Criar Nova Base de Dados', 'database_add_32.png', 3, 'admin.php?ADD=111', 2, 1, '--ALL_GROUPS--'),
                                (16, 'Configurar Campos das DBs', 'database_edit_32.png', 3, 'admin_lists_ref.php', 3, 1, '--ALL_GROUPS--'),
                                (17, 'Carregar Novos Contactos', 'database_save_32.png', 3, 'admin_listloader_third_gen.php', 4, 1, '--ALL_GROUPS--'),
                                (18, 'Pesquisar Contactos', 'application_form_magnify_32.png', 3, 'admin_search_lead.php', 5, 1, '--ALL_GROUPS--'),
                                (19, 'Gerir Números Bloqueados', 'delete_32.png', 3, 'admin.php?ADD=121', 6, 1, '--ALL_GROUPS--'),
                                (20, 'Limpar Nºs não existentes', 'database_delete_32.png', 3, 'clear_bd_noex.php', 7, 1, '--ALL_GROUPS--'),
                                (21, 'Gestão de Leads', 'book_edit_32.png', 3, 'crm/crm_main.php', 7, 1, '--ALL_GROUPS--'),
                                (22, 'Grupos Inbound', 'group_32.png', 4, 'inbound/list_inbound.php', 1, 1, '--ALL_GROUPS--'),
                                (23, 'Criar Grupo Inbound', 'group_add_32.png', 4, 'inbound/new_inbound_group.php', 2, 1, '--ALL_GROUPS--'),
                                (24, 'Lista de DDI''s', 'table_32.png', 4, 'inbound/list_did.php', 3, 1, '--ALL_GROUPS--'),
                                (25, 'Criar DDI', 'table_add_32.png', 4, 'inbound/new_did.php', 4, 1, '--ALL_GROUPS--'),
                                (26, 'Lista de IVR''s', 'computer_32.png', 4, 'inbound/list_ivr.php', 5, 1, '--ALL_GROUPS--'),
                                (27, 'Criar IVR', 'computer_add_32.png', 4, 'admin.php?ADD=1511', 6, 1, '--ALL_GROUPS--'),
                                (28, 'Enviar ou Criar Campanha', 'message_send_32.png', 5, 'sms/sms_form.php', 1, 1, '--ALL_GROUPS--'),
                                (29, 'Ver Campanhas', 'message_list_32.png', 5, 'sms/sms_campaigns_list.php', 2, 1, '--ALL_GROUPS--'),
                                (30, 'Outbound', 'chart_pie_32.png', 6, 'AST_VDADstats.php', 1, 1, '--ALL_GROUPS--'),
                                (31, 'Agentes', 'chart_pie_32.png', 6, 'AST_agent_time_detail.php', 2, 1, '--ALL_GROUPS--'),
                                (32, 'Reports Excel', 'export_excel_32.png', 6, 'reports_menu.php', 3, 1, '--ALL_GROUPS--'),
                                (33, 'Report Script - Outbound', 'report_magnify_32.png', 6, 'statistics/outbound/index_report_script.php', 4, 1, '--ALL_GROUPS--'),
                                (34, 'Report Script - Inbound', 'report_magnify_32.png', 6, 'statistics/inbound/index_report_script.php', 5, 1, '--ALL_GROUPS--'),
                                (35, 'Ver Relatório em Tempo Real', 'report_32.png', 7, '../sips/g_confirmation.php?evnt_cmd=ast_cti', 3, 1, '--ALL_GROUPS--'),
                                (36, 'Ver Consola Asterisk', 'brick_32.png', 7, '../sips/g_ast_cli.php', 2, 1, '--ALL_GROUPS--'),
                                (37, 'Editar Ficheiros Configuração', 'document_editing_32.png', 7, '../sips/g_ast_dir.php', 3, 1, '--ALL_GROUPS--'),
                                (38, 'Administrador', 'file_extension_pdf_32.png', 8, '../manuais/Manual_Admin.pdf', 1, 1, '--ALL_GROUPS--'),
                                (39, 'Operador', 'file_extension_pdf_32.png', 8, '../manuais/Manual_Opera.pdf', 2, 1, '--ALL_GROUPS--'),
                                (40, 'Configuração do Telefone', 'file_extension_pdf_32.png', 8, '../manuais/Manual_Telef.pdf', 3, 1, '--ALL_GROUPS--'),
                                (41, 'Erros de Áudio', 'file_extension_pdf_32.png', 8, '../manuais/Manual_Audio.pdf', 4, 1, '--ALL_GROUPS--');";

                            mysql_query($create1, $link) or die(mysql_error());
                            mysql_query($create2, $link) or die(mysql_error());
                            mysql_query($create3, $link) or die(mysql_error());
                            mysql_query($create4, $link) or die(mysql_error());
                        }


                        $query = "Select `id_menu_group`,`title`,`img`,`allowedgroups` From sips_admin_menu_groups WHERE active=1 order by `order` ASC";
                        $rslt = mysql_query($query, $link) or die(mysql_error());

                        $groups = array();
                        for ($i = 0; $i < mysql_num_rows($rslt); $i++) {
                            $row = mysql_fetch_assoc($rslt);
                            if (preg_match("/\b" . $usrgrp . "\b/", $row[allowedgroups]) OR preg_match("/\bALL_GROUPS\b/", $row[allowedgroups])) {
                                $groups[$i] = $row;
                                $in_groups.="'" . $row[id_menu_group] . "',";
                            }
                        }

                        $groups = array_values($groups);
                        $in_groups = rtrim($in_groups, ",")
                        ;
                        if (strlen($in_groups) > 0) {
                            $query = "Select `title`,`img`,`url`,`allowedgroups`,`id_menu_groups` From sips_admin_menu_links WHERE `id_menu_groups` IN ($in_groups) AND active=1 order by `order` ASC";
                            $rslt = mysql_query($query, $link) or die(mysql_error());

                            $links = array();
                            for ($i = 0; $i < mysql_num_rows($rslt); $i++) {
                                $row = mysql_fetch_assoc($rslt);
                                if (preg_match("/\b" . $usrgrp . "\b/", $row[allowedgroups]) OR preg_match("/\bALL_GROUPS\b/", $row[allowedgroups])) {
                                    $links[$i] = $row;
                                }
                            }

                            $links = array_values($links);
                            for ($i = 0; $i < count($groups); $i++) {

                                echo "<div class='cc-menu' >
                                        <table>
                                            <tr>
                                                <td>" . $groups[$i][title] . "</td><td><img class='cc-menu-img' src='/images/icons/" . $groups[$i][img] . "' /></td>
                                            </tr>
                                        </table>
                                    </div>";

                                echo "<div class='cc-submenu' >
                                <table>";

                                for ($ii = 0; $ii < count($links); $ii++) {
                                    if ($links[$ii][id_menu_groups] == $groups[$i][id_menu_group]) {

                                        echo "<tr onClick=\"open_page('" . $links[$ii][url] . "')\">
                                         <td width=32px><img src='/images/icons/" . $links[$ii][img] . "' /></td><td>" . $links[$ii][title] . "</td>
                                      </tr>";

                                        unset($links[$ii]);
                                        $links = array_values($links);
                                        $ii--;
                                    }
                                }

                                echo "</table>
                                </div>";
                            }
                        }
                        ?>
                    </DIV>
                </td>
                <td valign=top style="float:right;width: 100%;margin-top: -10px;">
                    <iframe name="mbody" id="mbody" src='<?=(($usrgrp == "AreaSalesManager")?"reservas/views/sch_admin.php?user=$username":"realtime/realtime_report.php") ?>' marginheight="0" marginwidth="0" frameborder="0" ></iframe>
                </td>
            </tr>
        </table>

    </div>
    <script>
    $(function(){
        $("#menu-hide").toggle(
                function(){
            $("#menu").stop(true,true).hide('slide',{direction:"left"});
                },
                function(){
            $("#menu").stop(true,true).show('slide',{direction:"left"});
                });
    });
    </script>
</body>
</html>
 