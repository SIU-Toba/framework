ef_cbu.prototype = new ef_editable();
ef_cbu.prototype.constructor = ef_cbu;


	function ef_cbu(id_form, etiqueta, obligatorio, colapsado, masc, expreg) {
		ef.prototype.constructor.call(this, id_form, etiqueta, obligatorio, colapsado);
		this._forma_mascara = (masc && masc.trim().toLowerCase() != 'no') ? masc : null;
		this._expreg = expreg;
		this._mascara = null;
	}
	
	ef_cbu.prototype.validar = function () {
		if (! ef.prototype.validar.call(this)) {
			return false;
		}
		var estado = this.get_estado();
		if(estado !== '' && ! this.es_cbu_valido(estado)) {
			this._error = 'código CBU inválido';
			return false;
		}
		return true;
	};
	
	
	ef_cbu.prototype.es_cbu_valido = function (cbu) {
		var rta = true;
		//console.debug(cbu.length);
		if (cbu.length != 22) {
			rta = false;
		} else {		
			var v = new Array();
			var i = 0;
			for(i=0; i < cbu.length; i++) {
				v[i] = parseInt(cbu.charAt(i));
			}
			
			//Valido Bloque 2
			var suma1 = v[0]*7 + v[1]*1 + v[2]*3 + v[3]*9 + v[4]*7 + v[5]*1 + v[6]*3;
			
			console.debug(suma1);
			d1 = (10 - (suma1 % 10)) % 10;
			//d1 = 10 - (parseInt(suma1.toString().substr(-1, 1)));
			console.debug(d1);
			console.debug(v[7]);			
			if(d1 !=  v[7]) {
				rta = false;
			}
			
			//Valido Bloque 2
			var suma2 = v[8]*3 + v[9]*9 + v[10]*7 + v[11]*1 + v[12]*3 + v[13]*9 + v[14]*7 + v[15]*1 + v[16]*3 + v[17]*9 + v[18]*7 + v[19]*1 + v[20]*3 ;
			//var d2 =  10 - (parseInt(suma2.toString().substr(-1, 1)));
			d2 = (10 - (suma2 % 10)) % 10;
			/*console.debug(d2);
			console.debug(v[21]);*/
			if(d2 !=  v[21]) {
				rta = false;
			}
		}
		return rta;
	};
