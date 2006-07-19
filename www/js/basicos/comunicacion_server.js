/*
**********  Sistema de comunicaciones asincronicas con el SERVER  *********
*/
/*
Copyright (c) 2006 Yahoo! Inc. All rights reserved.
version 0.9.0
*/

var conexion =
{
	_msxml_progid:[
		'MSXML2.XMLHTTP.5.0',
		'MSXML2.XMLHTTP.4.0',
		'MSXML2.XMLHTTP.3.0',
		'MSXML2.XMLHTTP',
		'Microsoft.XMLHTTP'
		],

	_http_header:[],

	_isFormPost:false,

	_sFormData:null,

	_polling_interval:300,

	_transaction_id:0,

	setProgId:function(id)
	{
		this.msxml_progid.unshift(id);
	},

	createXhrObject:function(transactionId)
	{
		var obj,http;
		try
		{
			// Instantiates XMLHttpRequest in non-IE browsers and assigns to http.
			http = new XMLHttpRequest();
			//  Object literal with http and id properties
			obj = { conn:http, tId:transactionId };
		}
		catch(e)
		{
			for(var i=0; i<this._msxml_progid.length; ++i){
				try
				{
					// Instantiates XMLHttpRequest for IE and assign to http.
					http = new ActiveXObject(this._msxml_progid[i]);
					//  Object literal with http and id properties
					obj = { conn:http, tId:transactionId };
				}
				catch(e){}
			}
		}
		finally
		{
			return obj;
		}
	},

	getConnectionObject:function()
	{
		var o;
		var tId = this._transaction_id;

		try
		{
			o = this.createXhrObject(tId);
			if(o){
				this._transaction_id++;
			}
		}
		catch(e){}
		finally
		{
			return o;
		}
	},

	asyncRequest:function(method, uri, callback, postData)
	{
		toba.inicio_aguardar();
		var errorObj;
		var o = this.getConnectionObject();

		if(!o){
			return null;
		}
		else{
			var oConn = this;

			o.conn.open(method, uri, true);
		    this.handleReadyState(o, callback);

			if(this._isFormPost){
				postData = this._sFormData;
				this._isFormPost = false;
			}
			else if(postData){
				this.initHeader('Content-Type','application/x-www-form-urlencoded');
			}

			//Verify whether the transaction has any user-defined HTTP headers
			//and set them.
			if(this._http_header.length>0){
				this.setHeader(o);
			}
			if (postData) {
				o.conn.send(postData);
			} else { 
				o.conn.send(null);
			}
			return o;
		}
	},

	handleReadyState:function(o, callback)
	{
		var oConn = this;
		var poll = window.setInterval(
			function(){
				if(o.conn.readyState==4){
					toba.fin_aguardar();
					oConn.handleTransactionResponse(o, callback);
					window.clearInterval(poll);
				}
			}
		,this._polling_interval);
	},

	handleTransactionResponse:function(o, callback)
	{
		var httpStatus;
		var responseObject;

		try{
			httpStatus = o.conn.status;
		}
		catch(e){
			// 13030 is the custom code to indicate the condition -- in Mozilla/FF --
			// when the o object's status and statusText properties are
			// unavailable, and a query attempt throws an exception.
			httpStatus = 13030;
		}

		if(httpStatus == 200){
			responseObject = this.createResponseObject(o, callback.argument);
			if(callback.success){
				if(!callback.scope){
					callback.success(responseObject);
				}
				else{
					callback.success.apply(callback.scope, [responseObject]);
				}
			}
		}
		else{
			switch(httpStatus){
				// The following case labels are wininet.dll error codes that may be encountered.
				// Server timeout
				case 12002:
				// 12029 to 12031 correspond to dropped connections.
				case 12029:
				case 12030:
				case 12031:
				// Connection closed by server.
				case 12152:
				// See above comments for variable status.
				case 13030:
					responseObject = this.createExceptionObject(o, callback.argument);
					if(callback.failure){
						if(!callback.scope){
							callback.failure(responseObject);
						}
						else{
							callback.failure.apply(callback.scope,[responseObject]);
						}
					}
					break;
				default:
					responseObject = this.createResponseObject(o, callback.argument);
					if(callback.failure){
						if(!callback.scope){
							callback.failure(responseObject);
						}
						else{
							callback.failure.apply(callback.scope,[responseObject]);
						}
					}
			}
		}

		this.releaseObject(o);
	},
	createResponseObject:function(o, callbackArg)
	{
		var obj = {};

		obj.tId = o.tId;
		obj.status = o.conn.status;
		obj.statusText = o.conn.statusText;
		obj.allResponseHeaders = o.conn.getAllResponseHeaders();
		obj.responseText = o.conn.responseText;
		obj.responseXML = o.conn.responseXML;
		if(callbackArg){
			obj.argument = callbackArg;
		}

		return obj;
	},

	createExceptionObject:function(tId, callbackArg)
	{
		var COMM_CODE = 0;
		var COMM_ERROR = 'communication failure';

		var obj = {};

		obj.tId = tId;
		obj.status = COMM_CODE;
		obj.statusText = COMM_ERROR;
		if(callbackArg){
			obj.argument = callbackArg;
		}

		return obj;
	},

	initHeader:function(label,value)
	{
		var oHeader = [label,value];
		this._http_header.push(oHeader);
	},

	setHeader:function(o)
	{
		var oHeader = this._http_header;
		for(var i=0;i<oHeader.length;i++){
			o.conn.setRequestHeader(oHeader[i][0],oHeader[i][1]);
		}
		oHeader.splice(0,oHeader.length);
	},

	setForm:function(formName)
	{
		this._sFormData = '';
		var oForm = document.forms[formName];
		var oElement, elName, elValue;
		// iterate over the form elements collection to construct the
		// label-value pairs.
		for (var i=0; i<oForm.elements.length; i++){
			oElement = oForm.elements[i];
			elName = oForm.elements[i].name;
			elValue = oForm.elements[i].value;
			switch (oElement.type)
			{
				case 'select-multiple':
					for(var j=0; j<oElement.options.length; j++){
						if(oElement.options[j].selected){
							this._sFormData += encodeURIComponent(elName) + '=' + encodeURIComponent(oElement.options[j].value) + '&';
						}
					}
					break;
				case 'radio':
				case 'checkbox':
					if(oElement.checked){
						this._sFormData += encodeURIComponent(elName) + '=' + encodeURIComponent(elValue) + '&';
					}
					break;
				case 'file':
				// stub case as XMLHttpRequest will only send the file path as a string.
					break;
				case undefined:
				// stub case for fieldset element which returns undefined.
					break;
				default:
					this._sFormData += encodeURIComponent(elName) + '=' + encodeURIComponent(elValue) + '&';
					break;
			}
		}
		this._sFormData = this._sFormData.substr(0, this._sFormData.length - 1);
		this._isFormPost = true;
		this.initHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
	},

	abort:function(o)
	{
		if(this.isCallInProgress(o)){
			o.conn.abort();
			this.releaseObject(o);
		}
	},

	isCallInProgress:function(o)
	{
		if(o){
			return o.conn.readyState != 4 && o.conn.readyState !== 0;
		}
	},

	releaseObject:function(o)
	{
			//dereference the XHR instance.
			o.conn = null;
			//dereference the connection object.
			o = null;
	}
};


toba.confirmar_inclusion('basicos/comunicacion_server');