<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>SIPS - Contrato Trabalho</title>
		<style type="text/css">
			body td {
				font-family: Verdana, Geneva, sans-serif;
				font-size: 12px;
			}
		</style>
		<?php

		$id = $_GET['id'];
		$con = mysql_connect("localhost", "root", "admin");
		if (!$con) {
			die('Não me consegui ligar' . mysql_error());
		}
		mysql_select_db("sips", $con);
		$colab = mysql_query("SELECT * FROM t_colaborador WHERE uservici = '$id' ") or die(mysql_error());
		$rcolab = mysql_fetch_assoc($colab);
		mysql_close($con);

		$con = mysql_connect("serverintegra.dyndns.org", "admin", "integra");
		if (!$con) {
			die('Não me consegui ligar' . mysql_error());
		}
		mysql_select_db("integra_bd_2", $con);

		$idmor = mysql_query("SELECT ID_Morada FROM t_estrutura INNER JOIN t_utilizador ON t_estrutura.ID_Estrutura = t_utilizador.ID_Colaborador WHERE t_utilizador.Utilizador = '$id'") or die(mysql_error());

		$idmor = mysql_fetch_assoc($idmor);
		$idmor = $idmor['ID_Morada'];

		$morada = mysql_query("SELECT * FROM t_morada WHERE ID_Morada = $idmor") or die(mysql_error());

		mysql_close($con);

		$morada = mysql_fetch_assoc($morada);

		$concMor = $morada['Rua'] . ", " . $morada['Porta'] . ", " . $morada['Andar'] . ", " . $morada['Cod_Postal'] . "-" . $morada['Cod_Rua'] . ", " . $morada['Localidade'];

		$h = explode('-', $rcolab['datainsc']);

		$h[1] = $h[1] + 6;

		if ($h[1] > 12) {
			$h[1] = $h[1] - 12;
			$h[2] = $h[2] + 1;
		}

		$datafim = $h[0] . "-" . $h[1] . "-" . $h[2];
		?>

	</head>

	<body>
		<table width="600" border="0" align="center">
			<tr>
				<td align="center">
				<p align="center">
					<strong>CONTRATO DE TRABALHO A TERMO CERTO</strong>
				</p></td>
			</tr>
			<tr>
				<td align="left">
				<p>
					Entre:
					<br />
					<strong>Primeiro Contraente: Purosinónimo, Lda.</strong>, pessoa colectiva nº 509501320,  Niss 25095013200 matriculada na Conservatória do Registo Comercial de Sintra  sob o n.º 509501320, com o capital social de € 20.000,00 (vinte mil euros) e  sede na Rua da Estação, nº 22, 1º A, Mem Martins, 2725-302 Mem Martins,  exercendo a actividade de Actividades Centros de Chamadas, com o CAE 82200;
				</p>
				<p>
					&nbsp;
				</p></td>
			</tr>
			<tr>
				<td align="left">
				<p>
					<strong>Segundo Contraente: <?php echo utf8_encode($rcolab['nome']); ?>, </strong><?php echo $rcolab['estcivil']; ?>,
					portador(a) do Bilhete de Identidade n.º <?php echo $rcolab['bi']; ?>,
					contribuinte n.º <?php echo $rcolab['nif']; ?>,
					e residente  em <?php echo $concMor; ?>.
				</p>
				<p>
					&nbsp;
				</p></td>
			</tr>
			<tr>
				<td align="left">
				<p>
					Livremente e dentro dos  princípios da boa fé, as partes, nas respectivas qualidades em que intervêm,  para bom e integral cumprimento, celebram entre si um Contrato de Trabalho a Termo  Certo, ao abrigo do Artigo 140.º e seguintes do Decreto-lei n.º 7/2009, de 12  de Fevereiro, a que mútua e reciprocamente se obrigam, com o objecto, termos e  mais condições das cláusulas seguintes por que se regerá.
				</p>
				<p>
					&nbsp;
				</p></td>
			</tr>
			<tr>
				<td align="left">
				<p align="center">
					<strong>PRIMEIRA</strong>
				</p></td>
			</tr>
			<tr>
				<td align="left">
				<p>
					O Segundo Contraente é admitido  ao serviço do primeiro Contraente, na profissão de Técnico de Vendas, podendo  desempenhar outras tarefas, nos termos da lei e do Contrato Colectivo de  Trabalho em vigor.
				</p>
				<p>
					&nbsp;
				</p></td>
			</tr>
			<tr>
				<td align="left">
				<p align="center">
					<strong>SEGUNDA</strong>
				</p></td>
			</tr>
			<tr>
				<td align="left">
				<p>
					O Segundo Contraente deve  comparecer ao serviço com assiduidade, e realizar o trabalho com zelo e  diligência e cumprir as demais obrigações decorrentes do contrato e das normas  que o regem.
				</p>
				<p>
					&nbsp;
				</p></td>
			</tr>
			<tr>
				<td align="center"><strong>TERCEIRA</strong></td>
			</tr>
			<tr>
				<td align="left" valign="top">
				<ol start="1" type="1">
					<li>
						O presente contrato é celebrado pelo prazo de seis       meses, tendo a sua vigência início em <?php echo $rcolab['datainsc']; ?>, e termo em <?php echo $datafim; ?>.
					</li>
					<li>
						Este contrato, é celebrado pelo prazo estabelecido       no número anterior ao abrigo do disposto na alínea a), do n.º 4 do Artigo       140.º do Decreto-lei n.º 7/2009, de 12 de Fevereiro, devido ao início de actividade       da empresa. <u></u>
					</li>
					<li>
						O período experimental será de 30 dias de       calendário, sendo que ambas as partes poderão rescindir este contrato sem       comunicação prévia durante este período experimental.
					</li>
				</ol>
				<p>
					&nbsp;
				</p></td>
			</tr>
			<tr>
				<td align="center"><strong>QUARTA</strong></td>
			</tr>
			<tr>
				<td align="left">
				<p>
					O contrato caducará no prazo  indicado na cláusula terceira desde que o Primeiro Contraente comunique até quinze  dias ou o Segundo Contraente comunique até oito dias, antes do prazo expirar,  por forma escrita, a vontade de não o renovar, nos termos do n.º 1 do artigo 344.º  do Decreto-Lei n.º 7/2009, de 12 de Fevereiro.
				</p>
				<p>
					&nbsp;
				</p></td>
			</tr>
			<tr>
				<td align="center"><strong>QUINTA</strong></td>
			</tr>
			<tr>
				<td align="left">
				<ol start="1" type="1">
					<li>
						O local de trabalho será no estabelecimento da       empresa, situada na Rua da Estação, nº 22, 1º A, em Mem Martins, e em       qualquer local onde o Primeiro Contraente tenha trabalhos a efectuar, para       os quais determine a intervenção do Segundo Contraente, salvaguardando o       disposto no Contrato Colectivo de Trabalho.
					</li>
					<li>
						O Segundo Contraente declara que aceitará, sem       alegar quaisquer prejuízos, a transferência de local de trabalho para       qualquer dos Estabelecimentos existentes ou a abrir pelo Primeiro       Contraente, bem como a mudança de horário daí adveniente, de acordo com o       estipulado na cláusula seguinte.
					</li>
				</ol>
				<p>
					&nbsp;
				</p></td>
			</tr>
			<tr>
				<td align="left">
				<p align="center">
					<strong>SEXTA</strong>
				</p></td>
			</tr>
			<tr>
				<td align="left">
				<p>
					O horário de  trabalho será de 20 horas semanais.
				</p>
				<p>
					&nbsp;
				</p></td>
			</tr>
			<tr>
				<td align="left">
				<p align="center">
					<strong>SÉTIMA</strong>
				</p></td>
			</tr>
			<tr>
				<td align="left">
				<p>
					O Segundo Contraente auferirá um  vencimento mensal ilíquido de € 242,50 (duzentos e quarenta e dois euros e  cinquenta cêntimos), sujeitos aos impostos e demais descontos legais.
				</p>
				<p>
					&nbsp;
				</p></td>
			</tr>
			<tr>
				<td align="left">
				<p align="center">
					<strong>OITAVA</strong>
				</p></td>
			</tr>
			<tr>
				<td align="left">
				<p>
					O Segundo Contraente terá direito  a um período de férias remuneradas e ao correspondente subsídio, calculado nos  termos do Artigo 238.º do Decreto-Lei n.º 7/2009, de 12 de Fevereiro.
				</p>
				<p>
					&nbsp;
				</p></td>
			</tr>
			<tr>
				<td align="left">
				<p align="center">
					<strong>NONA</strong>
				</p></td>
			</tr>
			<tr>
				<td align="left">
				<p>
					O Segundo Contraente obriga-se a  não praticar actos que possam prejudicar interesses do Primeiro Contraente,  nomeadamente, o de não exercer quaisquer actividades concorrenciais com as do  Primeiro Contraente quer o faça por conta própria, quer por conta alheia, mesmo  que seja fora do seu local e horário de trabalho, e o de não angariar serviços  para terceiros.
				</p>
				<p>
					&nbsp;
				</p></td>
			</tr>
			<tr>
				<td align="left">
				<p align="center">
					<strong>DÉCIMA</strong>
				</p></td>
			</tr>
			<tr>
				<td align="left">
				<p>
					Por expressão do seu mútuo  consenso assim o assinam e se obrigam.
					<br />
					Celebrado em duplicado, em Mem  Martins, no dia <?php echo $rcolab['datainsc']; ?>, ficando um exemplar em poder de cada uma  das partes.
				</p>
				<p>
					&nbsp;
				</p></td>
			</tr>
			<tr>
				<td align="left">&nbsp;</td>
			</tr>
			<tr>
				<td align="left">
				<p>
					O primeiro outorgante
				</p>
				<p>
					&nbsp;
				</p></td>
			</tr>
			<tr>
				<td align="left">&nbsp;</td>
			</tr>
			<tr>
				<td align="left">&nbsp;</td>
			</tr>
			<tr>
				<td align="left">
				<p>
					O segundo outorgante
				</p>
				<p>
					&nbsp;
				</p></td>
			</tr>
			<tr>
				<td align="left">&nbsp;</td>
			</tr>
		</table>
	</body>
</html>