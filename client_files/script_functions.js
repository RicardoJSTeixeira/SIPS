/**
 * @author kant
 */

/* GERAL */

function IsNumeric(input){
    var RE = /^-{0,1}\d*\.{0,1}\d+$/;
    return (RE.test(input));
    
}




/* Teia do Mundo Script */


function CalcPercent()
{
	var EmpValor = document.getElementById('tmempresa').value;
	var CasaValor = document.getElementById('tmcasa').value;
	
	
	var ResultEmp = 0;
	var ResultCasa = 0;
	var Total = 0;
	
	if (IsNumeric(EmpValor))
	{
		ResultEmp = EmpValor - (EmpValor * 0.4);
		ResultEmp = Math.round(ResultEmp);
	}
	
	if (IsNumeric(CasaValor))
	{
		ResultCasa = CasaValor - (CasaValor * 0.4);
		ResultCasa = Math.round(ResultCasa);
	}
	document.getElementById('tmcalcone').value = ResultEmp;
	document.getElementById('tmcalctwo').value = ResultCasa;
	Total = parseInt(EmpValor) - parseInt(ResultEmp) + parseInt(CasaValor) - parseInt(ResultCasa);
	document.getElementById('tmcalcedvalues').innerHTML = Total;
	
}
