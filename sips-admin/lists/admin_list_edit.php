<?php
for ($i = 0; $i < count(explode('/', $_SERVER['PHP_SELF'])) - 2; $i++) {$far.="../";}
define("ROOT", $far);
require(ROOT . "ini/header.php");
$list_id=$_GET['list_id'];
$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER']; 
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_AUTH_PW = ereg_replace("[^-_0-9a-zA-Z]","",$PHP_AUTH_PW);
$PHP_AUTH_USER = ereg_replace("[^-_0-9a-zA-Z]","",$PHP_AUTH_USER);
if ( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) )
	{
	Header("WWW-Authenticate: Basic realm=\"SIPS Call-Center\"");
	Header("HTTP/1.0 401 Unauthorized");
	echo "Invalid Username/Password: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
	exit;
	}
if(isset($_POST[ADD])){$ADD=$_POST[ADD];}elseif(isset($_GET[ADD])){$ADD=$_GET[ADD];};
if(isset($_POST[list_id])){$list_id=$_POST[list_id];}elseif(isset($_GET[list_id])){$list_id=$_GET[list_id];};
if(isset($_POST[active])){$active=$_POST[active];}elseif(isset($_GET[active])){$active=$_GET[active];};
if(isset($_POST[campaign_id])){$campaign_id=$_POST[campaign_id];}elseif(isset($_GET[campaign_id])){$campaign_id=$_GET[campaign_id];};
if(isset($_POST[list_description])){$list_description=$_POST[list_description];}elseif(isset($_GET[list_description])){$list_description=$_GET[list_description];};
if(isset($_POST[list_id])){$list_id=$_POST[list_id];}elseif(isset($_GET[list_id])){$list_id=$_GET[list_id];};
if(isset($_POST[list_name])){$list_name=$_POST[list_name];}elseif(isset($_GET[list_name])){$list_name=$_GET[list_name];};
if(isset($_POST[old_campaign_id])){$old_campaign_id=$_POST[old_campaign_id];}elseif(isset($_GET[old_campaign_id])){$old_campaign_id=$_GET[old_campaign_id];};
if(isset($_POST[reset_list])){$reset_list=$_POST[reset_list];}elseif(isset($_GET[reset_list])){$reset_list=$_GET[reset_list];};
if(isset($_POST[reset_time])){$reset_time=$_POST[reset_time];}elseif(isset($_GET[reset_time])){$reset_time=$_GET[reset_time];};
if(isset($_POST[web_form_address])){$web_form_address=$_POST[web_form_address];}elseif(isset($_GET[web_form_address])){$web_form_address=$_GET[web_form_address];};
if(isset($_POST[xferconf_a_number])){$xferconf_a_number=$_POST[xferconf_a_number];}elseif(isset($_GET[xferconf_a_number])){$xferconf_a_number=$_GET[xferconf_a_number];};
if(isset($_POST[xferconf_b_number])){$xferconf_b_number=$_POST[xferconf_b_number];}elseif(isset($_GET[xferconf_b_number])){$xferconf_b_number=$_GET[xferconf_b_number];};
if(isset($_POST[xferconf_c_number])){$xferconf_c_number=$_POST[xferconf_c_number];}elseif(isset($_GET[xferconf_c_number])){$xferconf_c_number=$_GET[xferconf_c_number];};
if(isset($_POST[xferconf_d_number])){$xferconf_d_number=$_POST[xferconf_d_number];}elseif(isset($_GET[xferconf_d_number])){$xferconf_d_number=$_GET[xferconf_d_number];};
if(isset($_POST[xferconf_e_number])){$xferconf_e_number=$_POST[xferconf_e_number];}elseif(isset($_GET[xferconf_e_number])){$xferconf_e_number=$_GET[xferconf_e_number];};

        $office_no=strtoupper($PHP_AUTH_USER);
	$password=strtoupper($PHP_AUTH_PW);
	$stmt="SELECT modify_lists from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$LOGmodify_lists=$row[0];
        
if ($ADD==411)
	{
	if ($LOGmodify_lists==1)
		{

		if ( (strlen($list_name) < 2) or (strlen($campaign_id) < 2) )
			{
			echo "<script type=text/javascript> alert('O nome da Base de Dados tem de ter no mínimo 2 caracteres.'); </script>";
			}
		else
			{
			if (strlen($reset_time) < 4) {$reset_time='';}

			echo "<script type=text/javascript> alert('Base de dados \"$list_name\" alterada.'); </script>";
                        

			$stmt="UPDATE vicidial_lists set list_name='$list_name',campaign_id='$campaign_id',active='$active',list_description='$list_description',list_changedate='$SQLdate',reset_time='$reset_time',agent_script_override='$agent_script_override',campaign_cid_override='$campaign_cid_override',am_message_exten_override='$am_message_exten_override',drop_inbound_group_override='$drop_inbound_group_override',xferconf_a_number='$xferconf_a_number',xferconf_b_number='$xferconf_b_number',xferconf_c_number='$xferconf_c_number',xferconf_d_number='$xferconf_d_number',xferconf_e_number='$xferconf_e_number',web_form_address='" . mysql_real_escape_string($web_form_address) . "',web_form_address_two='" . mysql_real_escape_string($web_form_address_two) . "' where list_id='$list_id';";
			$rslt=mysql_query($stmt, $link);

			### LOG INSERTION Admin Log Table ###
			$SQL_log = "$stmt|";
			$SQL_log = ereg_replace(';','',$SQL_log);
			$SQL_log = addslashes($SQL_log);
			$stmt="INSERT INTO vicidial_admin_log set event_date='$SQLdate', user='$PHP_AUTH_USER', ip_address='$ip', event_section='LISTS', event_type='MODIFY', record_id='$list_id', event_code='ADMIN MODIFY LIST', event_sql=\"$SQL_log\", event_notes='';";
			if ($DB) {echo "|$stmt|\n";}
			$rslt=mysql_query($stmt, $link);

			if ($reset_list == 'Y')
				{
				$stmtB="UPDATE vicidial_list set called_since_last_reset='N' where list_id='$list_id';";
				$rslt=mysql_query($stmtB, $link);

				### LOG INSERTION Admin Log Table ###
				$SQL_log = "$stmt|";
				$SQL_log = ereg_replace(';','',$SQL_log);
				$SQL_log = addslashes($SQL_log);
				$stmt="INSERT INTO vicidial_admin_log set event_date='$SQLdate', user='$PHP_AUTH_USER', ip_address='$ip', event_section='LISTS', event_type='RESET', record_id='$list_id', event_code='ADMIN RESET LIST', event_sql=\"$SQL_log\", event_notes='';";
				if ($DB) {echo "|$stmt|\n";}
				$rslt=mysql_query($stmt, $link);
				}
			if ($campaign_id != "$old_campaign_id")
				{
				$stmtC="DELETE from vicidial_hopper where list_id='$list_id' and campaign_id='$old_campaign_id';";
				$rslt=mysql_query($stmtC, $link);
				}
			}
		}
	else
		{
		echo "<script type=text/javascript> alert('Não tem permissão para visualizar esta página.'); </script>";
		exit;
		}
	}


		$stmt="SELECT list_id,list_name,campaign_id,active,list_description,list_changedate,list_lastcalldate,reset_time,agent_script_override,campaign_cid_override,am_message_exten_override,drop_inbound_group_override,xferconf_a_number,xferconf_b_number,xferconf_c_number,xferconf_d_number,xferconf_e_number,web_form_address,web_form_address_two from vicidial_lists where list_id='$list_id';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$list_name =				$row[1];
		$campaign_id =				$row[2];
		$active =					$row[3];
		$list_description =			$row[4];
		$list_changedate =			$row[5];
		$list_lastcalldate =		$row[6];
		$reset_time =				$row[7];
		$agent_script_override =	$row[8];
		$campaign_cid_override =	$row[9];
		$am_message_exten_override =	$row[10];
		$drop_inbound_group_override =	$row[11];
		$xferconf_a_number =		$row[12];
		$xferconf_b_number =		$row[13];
		$xferconf_c_number =		$row[14];
		$xferconf_d_number =		$row[15];
		$xferconf_e_number =		$row[16];
		$web_form_address =			$row[17];
		$web_form_address_two =		$row[18];

		# grab names of global statuses and statuses in the selected campaign
		$stmt="SELECT status,status_name,selectable,human_answered,category,sale,dnc,customer_contact,not_interested,unworkable,scheduled_callback from vicidial_statuses order by status";
		$rslt=mysql_query($stmt, $link);
		$statuses_to_print = mysql_num_rows($rslt);

		$o=0;
		while ($statuses_to_print > $o) 
			{
			$rowx=mysql_fetch_row($rslt);
			$statuses_list["$rowx[0]"] = "$rowx[1]";
			$o++;
			}

		$stmt="SELECT status,status_name,selectable,campaign_id,human_answered,category,sale,dnc,customer_contact,not_interested,unworkable,scheduled_callback from vicidial_campaign_statuses where campaign_id='$campaign_id' order by status";
		$rslt=mysql_query($stmt, $link);
		$Cstatuses_to_print = mysql_num_rows($rslt);

		$o=0;
		while ($Cstatuses_to_print > $o) 
			{
			$rowx=mysql_fetch_row($rslt);
			$statuses_list["$rowx[0]"] = "$rowx[1]";
			$o++;
			}
		# end grab status names

		##### get scripts listings for pulldown
		$Lscripts_list = "<option value=\"\">NONE - INACTIVE</option>\n";
		$stmt="SELECT script_id,script_name from vicidial_scripts order by script_id";
		$rslt=mysql_query($stmt, $link);
		$scripts_to_print = mysql_num_rows($rslt);
		$o=0;
		while ($scripts_to_print > $o)
			{
			$rowx=mysql_fetch_row($rslt);
			$Lscripts_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
			$scriptname_list["$rowx[0]"] = "$rowx[1]";
			$o++;
			}

		##### get in-groups listings for dynamic drop in-group pulldown
		$stmt="SELECT group_id,group_name from vicidial_inbound_groups order by group_id";
		$rslt=mysql_query($stmt, $link);
		$Dgroups_to_print = mysql_num_rows($rslt);
		$Dgroups_menu='';
		$Dgroups_selected=0;
		$o=0;
		while ($Dgroups_to_print > $o) 
			{
			$rowx=mysql_fetch_row($rslt);
			$Dgroups_menu .= "<option ";
			if ($drop_inbound_group_override == "$rowx[0]") 
				{
				$Dgroups_menu .= "SELECTED ";
				$Dgroups_selected++;
				}
			$Dgroups_menu .= "value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
			$o++;
			}
		if ($Dgroups_selected < 1) 
			{$Dgroups_menu .= "<option SELECTED value=\"\">---NONE---</option>\n";}
		else 
			{$Dgroups_menu .= "<option value=\"\">---NONE---</option>\n";}
			
		#############################################
		# SUB MENU ITEMS                            #
		#############################################
		
		$sub_menu_item_1 = "<td id=icon32><a id=menu-link href=\"../list_download.php?list_id=$list_id\"><img src='/images/icons/page_white_put.png' /></a></td><td style='text-align:left'><a href=\"../list_download.php?list_id=$list_id\"> Download da BD </a>  </td>";
		$sub_menu_item_2 = "<td id=icon32><a href=\"../admin.php?ADD=811&list_id=$list_id\"><img src='/images/icons/telephone_go.png' /></a></td><td style=text-align:left><a href=\"$PHP_SELF?ADD=811&list_id=$list_id\"> Callbacks </a>  </td>";
		$sub_menu_item_3 = "";
		$sub_menu_item_4 = "";
		$sub_menu_item_5 = "";
		

			
		if ($LOGuser_level >= 9)
			{
			$sub_menu_item_4 = "<td id=icon32><a href=\"$PHP_SELF?ADD=720000000000000&category=LISTS&stage=$list_id\"><img src='/images/icons/report_key.png' /></a></td><td style=text-align:left><a href=\"$PHP_SELF?ADD=720000000000000&category=LISTS&stage=$list_id\"> Relatório de Admin </a>  </td>";	
			}
			
		
			
		#############################################
		# SUB MENU                                  #
		#############################################		
			
		echo "<div class=cc-mstyle>";
		echo "<table>";
		echo "<tr>";
		echo "<td id='icon32'><img src='/images/icons/livejournal.png' /></td>";
		echo "<td id='submenu-title'> Editar BD: $list_name </td>";
		echo $sub_menu_item_2;
		echo $sub_menu_item_1;
	
		echo $sub_menu_item_5;
		echo "</tr>";
		echo "</table>";
		echo "</div>";	
			

		echo "<div id=work-area>";
		echo "<br><br>";
			
		#############################################
		# TABELAS                                   #
		#############################################		
			

		echo "<form action=$_SERVER[PHP_SELF] method=POST>\n";
		echo "<input type=hidden name=ADD value=411>\n";
		echo "<input type=hidden name=list_id value=\"$list_id\">\n";
		echo "<input type=hidden name=old_campaign_id value=\"$campaign_id\">\n";
		
		
		echo "<div class=cc-mstyle style='border:none; width:70%;'>";
		
		
		echo "<table>";
		
		echo "<tr><td style='width:225px'> <div class=cc-mstyle style='height:28px;'><p> Nome da BD </p></div></td> <td><input type=text name=list_name maxlength=20 value=\"$list_name\"></td></tr>";
		
		echo "<tr ><td style='width:225px'> <div class=cc-mstyle style='height:28px;'><p> Descrição da BD </p></div></td> <td><input type=text name=list_description maxlength=255 value=\"$list_description\"></td></tr>";
		
		
		echo "<tr ><td style='width:225px'> <div class=cc-mstyle style='height:28px;'> <p>Campanha Associada<p></div></td> <td><select style='width:200px' size=1 name=campaign_id>";

        // ALLOWED CAMPAIGNS
        $query1 = mysql_fetch_assoc(mysql_query("SELECT user_group FROM vicidial_users WHERE user='$_SERVER[PHP_AUTH_USER]';", $link)) or die(mysql_error());
        $query2 = mysql_fetch_assoc(mysql_query("SELECT allowed_campaigns FROM vicidial_user_groups WHERE user_group='$query1[user_group]';", $link)) or die(mysql_error());
        $AllowedCampaigns = "'" . preg_replace("/ /","','" , preg_replace("/ -/",'',$query2['allowed_campaigns'])) . "'";
        //
        
        
		$stmt="SELECT campaign_id,campaign_name from vicidial_campaigns WHERE campaign_id IN($AllowedCampaigns) order by campaign_id";
		$rslt=mysql_query($stmt, $link);
		$campaigns_to_print = mysql_num_rows($rslt);
		$campaigns_list='';
		$o=0;
		while ($campaigns_to_print > $o) 
			{
			$rowx=mysql_fetch_row($rslt);
			$campaigns_list .= "<option value=\"$rowx[0]\"".((preg_match("/\b$rowx[0]\b/",$campaign_id))?" SELECTED ":"").">$rowx[1]</option>\n";
			$o++;
			}
		echo "$campaigns_list";
		#echo "<option SELECTED>$campaign_id</option>\n";
		echo "</select></td></tr>\n";
		
		$selected = "";
		$selected1 = "";
		
		if ($active == "Y") { $selected = "selected";} else {$selected1 = "selected";} 
		
		echo "<tr ><td style='width:225px'> <div class=cc-mstyle style='height:28px;'> <p>Activa<p></div></td><td><select size=1 name=active><option $selected value =Y>Sim</option><option $selected1 value=N>Não</option></select></td></tr>\n";
		
		#Reset Lead-Called-Status for this list: 

		
		echo "<tr><td style='width:225px'> <div class=cc-mstyle style='height:28px;'><p> Reset Estados </p></div></td>
		<td><select size=1 name=reset_list><option value=Y>Sim</option><option SELECTED value=N>Não</option></select></td></tr>\n";
		
		echo "<tr ><td style='width:225px'> <div class=cc-mstyle style='height:28px;'><p> Horas para Reset </p></div></td><td align=left><input type=text name=reset_time size=30 maxlength=100 value=\"$reset_time\"></td></tr>\n";
		
		$exploded_alt = explode(" ",$list_changedate);
		
		$alt_new = "<b>Data:</b> $exploded_alt[0] &nbsp; <b>Hora:</b> $exploded_alt[1]";
		
		if ($list_changedate == "" ) { $alt_new = "<b>Data:</b> <font color=grey>Sem Registo</font> &nbsp; <b>Hora:</b> <font color=grey>Sem Registo</font>"; }
		
		echo "<tr ><td style='width:225px'> <div class=cc-mstyle style='height:28px;'><p> Última Alteração </p></div></td><td>$alt_new </td></tr>\n";
		
		$exploded_call = explode(" ",$list_lastcalldate);
		
		$call_new = "<b>Data:</b> $exploded_call[0] &nbsp; <b>Hora:</b> $exploded_call[1]";
		
		if ($list_lastcalldate == "" ) { $call_new = "<b>Data:</b> <font color=grey>Sem Registo</font> &nbsp; <b>Hora:</b> <font color=grey>Sem Registo</font>"; }

		
		echo "<tr ><td style='width:225px'> <div class=cc-mstyle style='height:28px;'><p> Última Chamada </p></div></td><td>$call_new  </td></tr>\n";

	

		echo "<tr ><td style='min-width:225px'> <div class=cc-mstyle style='height:28px;'><p> Formulário Web #1 </p></div></td><td><input type=text name=web_form_address size=70 maxlength=1055 value=\"$web_form_address\"></td></tr>\n";
		if ($SSenable_second_webform > 0)
			{
			echo "<tr ><td style='min-width:225px'> <div class=cc-mstyle style='height:28px;'><p> Formulário Web #2 </p></div></td><td align=left><input type=text name=web_form_address_two size=70 maxlength=1055 value=\"$web_form_address_two\"></td></tr>\n";
			}

		echo "<tr><td style='min-width:225px'> <div class=cc-mstyle style='height:28px;'><p> Numero de Transf. #1 </p></div></td><td><input type=text name=xferconf_a_number size=20 maxlength=50 value=\"$xferconf_a_number\"></td></tr>";
		
		echo "<tr><td style='min-width:225px'> <div class=cc-mstyle style='height:28px;'><p> Numero de Transf. #2 </p></div></td><td><input type=text name=xferconf_b_number size=20 maxlength=50 value=\"$xferconf_b_number\"></td></tr>";
		
		echo "<tr><td style='min-width:225px'> <div class=cc-mstyle style='height:28px;'><p> Numero de Transf. #3 </p></div></td><td><input type=text name=xferconf_c_number size=20 maxlength=50 value=\"$xferconf_c_number\"></td></tr>";
		
		echo "<tr><td style='min-width:225px'> <div class=cc-mstyle style='height:28px;'><p> Numero de Transf. #4 </p></div></td><td><input type=text name=xferconf_d_number size=20 maxlength=50 value=\"$xferconf_d_number\"></td></tr>";
		
		echo "<tr><td style='min-width:225px'> <div class=cc-mstyle style='height:28px;'><p> Numero de Transf. #5 </p></div></td><td><input type=text name=xferconf_e_number size=20 maxlength=50 value=\"$xferconf_e_number\"></td></tr>";

		echo "</table><br><br>";

		echo "<div style='height: 40px;'><span style='float:right;cursor:pointer;'>Gravar<input type=image style='vertical-align:middle' src='/images/icons/shape_square_add.png' alt=Gravar name=SUBMIT></span></div>";


		

		echo "</table>";
		
		echo "</div>";
		echo "</div>";
		
		echo "<br>";
		
		echo "<div class=cc-mstyle>";

		echo "<table>\n";
		echo "<tr><th>Estado</td><th>Nome do Estado</td><th>Chamado</td><th>Não Chamado</td></tr>\n";

		$leads_in_list = 0;
		$leads_in_list_N = 0;
		$leads_in_list_Y = 0;
		$stmt="SELECT status,called_since_last_reset,count(*) from vicidial_list where list_id='$list_id' group by status,called_since_last_reset order by status,called_since_last_reset";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
		$statuses_to_print = mysql_num_rows($rslt);

		$o=0;
		$lead_list['count'] = 0;
		$lead_list['Y_count'] = 0;
		$lead_list['N_count'] = 0;
		while ($statuses_to_print > $o) 
			{
			$rowx=mysql_fetch_row($rslt);
			
			$lead_list['count'] = ($lead_list['count'] + $rowx[2]);
			if ($rowx[1] == 'N') 
				{
				$since_reset = 'N';
				$since_resetX = 'Y';
				}
			else 
				{
				$since_reset = 'Y';
				$since_resetX = 'N';
				} 
			$lead_list[$since_reset][$rowx[0]] = ($lead_list[$since_reset][$rowx[0]] + $rowx[2]);
			$lead_list[$since_reset.'_count'] = ($lead_list[$since_reset.'_count'] + $rowx[2]);
			#If opposite side is not set, it may not in the future so give it a value of zero
			if (!isset($lead_list[$since_resetX][$rowx[0]])) 
				{
				$lead_list[$since_resetX][$rowx[0]]=0;
				}
			$o++;
			}
	 
		$o=0;
		if ($lead_list['count'] > 0)
			{
			while (list($dispo,) = each($lead_list[$since_reset]))
				{
	

				if ($dispo == 'CBHOLD')
					{
					$CLB="<a href=\"$PHP_SELF?ADD=811&list_id=$list_id\">";
					$CLE="</a>";
					}
				else
					{
					$CLB='';
					$CLE='';
					}

				echo "<tr><td>$CLB$dispo$CLE</td><td>$statuses_list[$dispo]</td><td>".$lead_list['Y'][$dispo]."</td><td>".$lead_list['N'][$dispo]." </td></tr>\n";
				$o++;
				}
			}

		echo "<tr><td colspan=2>Sub Totais</td><td>$lead_list[Y_count]</td><td>$lead_list[N_count]</td></tr>\n";
		echo "<tr><td colspan=2>Totais</td><td colspan=2 align=center>$lead_list[count]</td></tr>\n";

		echo "</table><br>\n";
		unset($lead_list);  
?>
