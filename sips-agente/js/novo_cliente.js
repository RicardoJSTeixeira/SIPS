
var nc_live=false,nc_live_id=undefined;
function JanelaNovoCliente()
	{
		var NC_operador = user,
                    NC_campanha = (VDCL_group_id.length)?VDCL_group_id:campaign,
                    NC_list_id = $('#list_id').val(),
                    NC_owner = $('#owner').val(),
                    NC_security_phrase = $('#security_phrase').val(),
                    NC_title = $('#title').val(),
                    NC_first_name = $('#first_name').val(),
                    NC_middle_initial = $('#middle_initial').val(),
                    NC_last_name = $('#last_name').val(),
                    NC_address1 = $('#address1').val(),
                    NC_vendor_lead_code = $('#vendor_lead_code').val(),
                    NC_address2 = $('#address2').val(),
                    NC_address3 = $('#address3').val(),
                    NC_city = $('#city').val(),
                    NC_province = $('#province').val(),
                    NC_state = $('#state').val(),
                    NC_postal_code = $('#postal_code').val(),
                    NC_country_code = $('#country_code').val(),
                    NC_date_of_birth = $('#date_of_birth').val(),
                    NC_phone_number = $('#phone_number').val(),
                    NC_alt_phone = $('#alt_phone').val(),
                    NC_comments = $('#comments').val();
                    NC_lead_id = $('#lead_id').val();
		
		
		
		var GET_STRING = 	'../client_files/acusticamedica/novocliente/novo_cliente.php?operador='
							+ NC_operador +
							'&campanha='
							+ NC_campanha +
							'&list_id='
							+ NC_list_id +   
							'&owner='
							+ NC_owner +
							'&security_phrase='
							+ NC_security_phrase + 
							'&title=' 
							+ NC_title + 
							'&first_name=' 
							+ NC_first_name +
							'&middle_initial='
							+ NC_middle_initial +
							'&last_name='
							+ NC_last_name + 
							'&address1='
							+ NC_address1 + 
							'&vendor_lead_code='
							+ NC_vendor_lead_code +
							'&address2='
							+ NC_address2 +
							'&address3='
							+ NC_address3 +
							'&city='
							+ NC_city + 
							'&province='
							+ NC_province + 
							'&state='
							+ NC_state + 
							'&postal_code='
							+ NC_postal_code + 
							'&country_code='
							+ NC_country_code + 
							'&date_of_birth='
							+ NC_date_of_birth +
							'&phone_number='
							+ NC_phone_number + 
							'&alt_phone='
							+ NC_alt_phone + 
							'&comments='
							+ NC_comments +
                            '&lead_id='
                            + NC_lead_id;
		
		window.open(GET_STRING, 'novapagina');
	}
   