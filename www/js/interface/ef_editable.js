
function ef(id_form)
{
	this.id_form = id_form;	
	this.id_form_orig = id_form;

	this.id = function() { return this.id_form; }
	
	this.set_fila = function (fila)
	{
		this.id_form = this.id_form_orig + fila;
		return this;
	}
	
	this.evento_cambia_valor = function (callback) { return; }

}

//--------------------------------------------------------------------------------

function ef_editable(id_form) //extends ef
{
	this.inheritFrom = ef;
	this.inheritFrom(id_form);
	
	this.valor = function () { return this.input().value; }
	
	this.input = function () { return document.getElementById(this.id_form); }
	
	this.evento_cambia_valor = function (callback) 
	{ 
		if (! this.input().onchange)	//Para no romper scripts hechos ad-hoc
			this.input().onchange = callback;	
	}
}

//--------------------------------------------------------------------------------

function ef_editable_numero(id_form) //extends ef_editable
{
	this.inheritFrom = ef_editable;
	this.inheritFrom(id_form);
	
	this.valor = function () 
	{
		valor = parseFloat(this.input().value);
		if (valor == '' || isNaN(valor))
			return 0;
		else
			return valor;
	}
}