	<!-- Original:  Cyanide_7 (leo7278@hotmail.com) -->
	<!-- Web Site:  http://www7.ewebcity.com/cyanide7 -->
	function formatCurrency(num) {
		num = num.toString().replace(/\$|\,/g,'');
		if(isNaN(num))
			num = "0";
		sign = (num == (num = Math.abs(num)));
		num = Math.floor(num*100+0.50000000001);
		var cents = num%100;
		num = Math.floor(num/100).toString();
		if (10 > cents)
			cents = "0" + cents;
		for (var i = 0; Math.floor((num.length-(1+i))/3) > i; i++)
			num = num.substring(0,num.length-(4*i+3))+'.'+
		num.substring(num.length-(4*i+3));
		return (((sign)?'':'-') + num + ',' + cents);
	}
	
	function redondear(numero, digitos)
	{
		if (!digitos)
			digitos = 2;
	
		return Math.round(numero*Math.pow(10,digitos))/Math.pow(10,digitos);
	}
	
	function es_igual(num1, num2, digitos_precision)
	{
		var entero1 = redondear(num1, digitos_precision);
		var entero2 = redondear(num2, digitos_precision);
		if (entero1 == entero2)
			return true;
		else
			return false;
	}

toba.confirmar_inclusion('financiero');