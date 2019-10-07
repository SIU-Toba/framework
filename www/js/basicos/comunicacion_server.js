/*
**********  Sistema de comunicaciones asincronicas con el SERVER  *********
*/

/**
 * The Connection Manager singleton provides methods for creating and managing
 * asynchronous transactions.
 *
 * @class Connect
 */


var conexion = 
{
  /**
   * @description Array of MSFT ActiveX ids for XMLHttpRequest.
   * @property _msxml_progid
   * @private
   * @static
   * @type array
   */
	_msxml_progid:[
		'Microsoft.XMLHTTP',
		'MSXML2.XMLHTTP.3.0',
		'MSXML2.XMLHTTP'
		],

  /**
   * @description Object literal of HTTP header(s)
   * @property _http_header
   * @private
   * @static
   * @type object
   */
	_http_headers:{},

  /**
   * @description Determines if HTTP headers are set.
   * @property _has_http_headers
   * @private
   * @static
   * @type boolean
   */
	_has_http_headers:false,

 /**
  * @description Determines if a default header of
  * Content-Type of 'application/x-www-form-urlencoded'
  * will be added to any client HTTP headers sent for POST
  * transactions.
  * @property _use_default_post_header
  * @private
  * @static
  * @type boolean
  */
    _use_default_post_header:true,

 /**
  * @description The default header used for POST transactions.
  * @property _default_post_header
  * @private
  * @static
  * @type boolean
  */
    _default_post_header:'application/x-www-form-urlencoded; charset=UTF-8',

 /**
  * @description The default header used for transactions involving the
  * use of HTML forms.
  * @property _default_form_header
  * @private
  * @static
  * @type boolean
  */
    _default_form_header:'application/x-www-form-urlencoded; charset=UTF-8',

 /**
  * @description Determines if a default header of
  * 'X-Requested-With: XMLHttpRequest'
  * will be added to each transaction.
  * @property _use_default_xhr_header
  * @private
  * @static
  * @type boolean
  */
    _use_default_xhr_header:true,

 /**
  * @description The default header value for the label
  * "X-Requested-With".  This is sent with each
  * transaction, by default, to identify the
  * request as being made by YUI Connection Manager.
  * @property _default_xhr_header
  * @private
  * @static
  * @type boolean
  */
    _default_xhr_header:'XMLHttpRequest',

 /**
  * @description Determines if custom, default headers
  * are set for each transaction.
  * @property _has_default_header
  * @private
  * @static
  * @type boolean
  */
    _has_default_headers:true,

 /**
  * @description Determines if custom, default headers
  * are set for each transaction.
  * @property _has_default_header
  * @private
  * @static
  * @type boolean
  */
    _default_headers:{},

 /**
  * @description Property modified by setForm() to determine if the data
  * should be submitted as an HTML form.
  * @property _isFormSubmit
  * @private
  * @static
  * @type boolean
  */
    _isFormSubmit:false,

 /**
  * @description Property modified by setForm() to determine if a file(s)
  * upload is expected.
  * @property _isFileUpload
  * @private
  * @static
  * @type boolean
  */
    _isFileUpload:false,

 /**
  * @description Property modified by setForm() to set a reference to the HTML
  * form node if the desired action is file upload.
  * @property _formNode
  * @private
  * @static
  * @type object
  */
    _formNode:null,

 /**
  * @description Property modified by setForm() to set the HTML form data
  * for each transaction.
  * @property _sFormData
  * @private
  * @static
  * @type string
  */
    _sFormData:null,

 /**
  * @description Collection of polling references to the polling mechanism in handleReadyState.
  * @property _poll
  * @private
  * @static
  * @type object
  */
    _poll:{},

 /**
  * @description Queue of timeout values for each transaction callback with a defined timeout value.
  * @property _timeOut
  * @private
  * @static
  * @type object
  */
    _timeOut:{},

  /**
   * @description The polling frequency, in milliseconds, for HandleReadyState.
   * when attempting to determine a transaction's XHR readyState.
   * The default is 50 milliseconds.
   * @property _polling_interval
   * @private
   * @static
   * @type int
   */
     _polling_interval:50,

  /**
   * @description A transaction counter that increments the transaction id for each transaction.
   * @property _transaction_id
   * @private
   * @static
   * @type int
   */
     _transaction_id:0,

  /**
   * @description Tracks the name-value pair of the "clicked" submit button if multiple submit
   * buttons are present in an HTML form; and, if YAHOO.util.Event is available.
   * @property _submitElementValue
   * @private
   * @static
   * @type string
   */
	 _submitElementValue:null,

  /**
   * @description Determines whether YAHOO.util.Event is available and returns true or false.
   * If true, an event listener is bound at the document level to trap click events that
   * resolve to a target type of "Submit".  This listener will enable setForm() to determine
   * the clicked "Submit" value in a multi-Submit button, HTML form.
   * @property _hasSubmitListener
   * @private
   * @static
   */
	 _hasSubmitListener:(function()
	 {
	    return false;
	 })(),

  /**
   * @description A reference table that maps callback custom events members to its specific
   * event name.
   * @property _customEvents
   * @private
   * @static
   * @type object
   */
	_customEvents:
	{
		onStart:['startEvent', 'start'],
		onComplete:['completeEvent', 'complete'],
		onSuccess:['successEvent', 'success'],
		onFailure:['failureEvent', 'failure'],
		onUpload:['uploadEvent', 'upload'],
		onAbort:['abortEvent', 'abort']
	},

  /**
   * @description Member to add an ActiveX id to the existing xml_progid array.
   * In the event(unlikely) a new ActiveX id is introduced, it can be added
   * without internal code modifications.
   * @method setProgId
   * @public
   * @static
   * @param {string} id The ActiveX id to be added to initialize the XHR object.
   * @return void
   */
	setProgId:function(id)
	{
		this._msxml_progid.unshift(id);
	},

  /**
   * @description Member to override the default POST header.
   * @method setDefaultPostHeader
   * @public
   * @static
   * @param {boolean} b Set and use default header - true or false .
   * @return void
   */
	setDefaultPostHeader:function(b)
	{
		if(typeof b == 'string'){
			this._default_post_header = b;
		}
		else if(typeof b == 'boolean'){
			this._use_default_post_header = b;
		}
	},

  /**
   * @description Member to override the default transaction header..
   * @method setDefaultXhrHeader
   * @public
   * @static
   * @param {boolean} b Set and use default header - true or false .
   * @return void
   */
	setDefaultXhrHeader:function(b)
	{
		if(typeof b == 'string'){
			this._default_xhr_header = b;
		}
		else{
			this._use_default_xhr_header = b;
		}
	},

  /**
   * @description Member to modify the default polling interval.
   * @method setPollingInterval
   * @public
   * @static
   * @param {int} i The polling interval in milliseconds.
   * @return void
   */
	setPollingInterval:function(i)
	{
		if(typeof i == 'number' && isFinite(i)){
			this._polling_interval = i;
		}
	},

  /**
   * @description Instantiates a XMLHttpRequest object and returns an object with two properties:
   * the XMLHttpRequest instance and the transaction id.
   * @method createXhrObject
   * @private
   * @static
   * @param {int} transactionId Property containing the transaction id for this transaction.
   * @return object
   */
	createXhrObject:function(transactionId)
	{
	},

  /**
   * @description This method is called by asyncRequest to create a
   * valid connection object for the transaction.  It also passes a
   * transaction id and increments the transaction id counter.
   * @method getConnectionObject
   * @private
   * @static
   * @return {object}
   */
	getConnectionObject:function(isFileUpload)
	{
	},


        _getTransactionId:function()
        {
            var tId = this._transaction_id;
            this._transaction_id++;
            return tId;
        },
  /**
   * @description Method for initiating an asynchronous request via the XHR object.
   * @method asyncRequest
   * @public
   * @static
   * @param {string} method HTTP transaction method
   * @param {string} uri Fully qualified path of resource
   * @param {callback} callback User-defined callback function or object
   * @param {string} postData POST body
   * @return {object} Returns the connection object
   */
	asyncRequest:function(method, uri, callback, postData)
	{
            toba.inicio_aguardar();              
            var args = (callback && callback.argument)?callback.argument:null;
            var metodo = method.toUpperCase();
            var conf = {
                tId: this._getTransactionId(),
                method: metodo,
                crossDomain: false,
                isLocal: false, 
                callbackConf: callback
            };
            
            if(callback){
                this.initEvents(conf, callback);
                if (callback.customevents) {
                    this.initCustomEvents(conf, callback);
                }
            }
            
            if(this._isFormSubmit){
                if(this._isFileUpload){
                    conf.processData = false;                    
                    postData = this._uploadFile(conf, callback, uri, postData);
                    //Hay que pegar el upload del archivo y retornar?
                    return;
                }
                
                if(metodo === 'POST'){
                     // If POST data exist in addition to the HTML form data,
                     // it will be concatenated to the form data.
                     postData = postData ? this._sFormData + "&" + postData:this._sFormData;                     
                } else if (metodo === 'GET' && this._sFormData.length > 0) {
                     postData = this._sFormData;
                }
            } else if (this._use_default_post_header && metodo === 'POST'){
                conf.contentType = this._default_post_header;
            }            
                              
            if(metodo === 'GET' && (callback && callback.cache === false)){
                conf.cache = false;
            }
            
            if(this._has_default_headers || this._has_http_headers){
                this.setHeader(conf);
            }
            
            conf.data = (postData || '');   
            //console.log(conf);
            var tjqhr = $.ajax(uri, conf);                  //Inicia la conexion                
            this.handleReadyState(tjqhr, callback);         //Espera y analiza el resultado via polling
            
            if(this._isFormSubmit === true){
                this.resetFormState();
            }
            
	},

  /**
   * @description This method creates and subscribes custom events,
   * specific to each transaction
   * @method initCustomEvents
   * @private
   * @static
   * @param {object} o The connection object
   * @param {callback} callback The user-defined callback object
   * @return {void}
   */
	initCustomEvents:function(o, callback)
	{
		var prop, name;
		// Enumerate through callback.customevents members and bind/subscribe
		// events that match in the _customEvents table.
		for(prop in callback.customevents){                    
			if(this._customEvents[prop][0]){
                            o[this._customEvents[prop][0]] = callback.customevents[prop];
			}
		}
	},
        
        initEvents:function(conf, callback)
	{                
            var Oconn = this;            
            if (callback.success) {
                conf.success = function(data, status, jqXHR) {
                    toba.fin_aguardar();
                    var response = Oconn.handleTransactionResponse(jqXHR, callback);
                    callback.success.call(callback.scope, response);
                    Oconn.releaseObject(response);
                }
            }
            
            if (callback.failure) {
                conf.error = function(data, status, jqXHR) {
                    var response = Oconn.handleTransactionResponse(jqXHR, callback);
                    callback.failure.call(callback.scope, response);
                    Oconn.releaseObject(response);
                }
            }            
	},

  /**
   * @description This method serves as a timer that polls the XHR object's readyState
   * property during a transaction, instead of binding a callback to the
   * onreadystatechange event.  Upon readyState 4, handleTransactionResponse
   * will process the response, and the timer will be cleared.
   * @method handleReadyState
   * @private
   * @static
   * @param {object} o The connection object
   * @param {callback} callback The user-defined callback object
   * @return {void}
   */

    handleReadyState:function(o, callback)
    {
    },

  /**
   * @description This method attempts to interpret the server response and
   * determine whether the transaction was successful, or if an error or
   * exception was encountered.
   * @method handleTransactionResponse
   * @private
   * @static
   * @param {object} o The connection object
   * @param {object} callback The user-defined callback object
   * @param {boolean} isAbort Determines if the transaction was terminated via abort().
   * @return {void}
   */
    handleTransactionResponse:function(o, callback, isAbort)
    {
	var responseObject;

        try
        {
            if(o.status !== undefined && o.status !== 0){
                     responseObject = this.createResponseObject(o, callback);
            } else {
                    responseObject = this.createExceptionObject(callback.tId, callback, (isAbort?isAbort:false));
            }
        }
        catch(e){
                 // custom code to indicate the condition -- in Mozilla/FF --
                 // when the XHR object's status and statusText properties are
                 // unavailable, and a query attempt throws an exception.
                 responseObject = this.createExceptionObject(callback.tId, callback, (isAbort?isAbort:false));
        }
                
        return responseObject;                
    },

  /**
   * @description This method evaluates the server response, creates and returns the results via
   * its properties.  Success and failure cases will differ in the response
   * object's property values.
   * @method createResponseObject
   * @private
   * @static
   * @param {object} o The connection object
   * @param {callback} callback The callback object
   * @return {object}
   */
    createResponseObject:function(o, callback)
    {
		var obj = {};
		var headerObj = {};
                var args = (callback && callback.argument)?callback.argument:null;
                
		try
		{
			var headerStr = o.getAllResponseHeaders();
			var header = headerStr.split('\n');
			for(var i=0; i<header.length; i++){
				var delimitPos = header[i].indexOf(':');
				if(delimitPos !== -1){
					headerObj[header[i].substring(0,delimitPos)] = header[i].substring(delimitPos+2);
				}
			}
		}
		catch(e){}

		obj.tId = callback.tId;
		// Normalize IE's response to HTTP 204 when Win error 1223.
		obj.status = (o.status === 1223)?204:o.status;
		// Normalize IE's statusText to "No Content" instead of "Unknown".
		obj.statusText = (o.status === 1223)?"No Content":o.statusText;
		obj.getResponseHeader = headerObj;
		obj.getAllResponseHeaders = headerStr;
		obj.responseText = o.responseText;
		obj.responseXML = o.responseXML;

		if(args){
			obj.argument = args;
		}

		return obj;
    },

  /**
   * @description If a transaction cannot be completed due to dropped or closed connections,
   * there may be not be enough information to build a full response object.
   * The failure callback will be fired and this specific condition can be identified
   * by a status property value of 0.
   *
   * If an abort was successful, the status property will report a value of -1.
   *
   * @method createExceptionObject
   * @private
   * @static
   * @param {callback} callback The callback object
   * @param {boolean} isAbort Determines if the exception case is caused by a transaction abort
   * @return {object}
   */
    createExceptionObject:function(callback, isAbort)
    {
		var COMM_CODE = 0;
		var COMM_ERROR = 'communication failure';
		var ABORT_CODE = -1;
		var ABORT_ERROR = 'transaction aborted';
                var args = (callback && callback.argument)?callback.argument:null;

		var obj = {};

		obj.tId = callback.tId;
		if(isAbort){
			obj.status = ABORT_CODE;
			obj.statusText = ABORT_ERROR;
		}
		else{
			obj.status = COMM_CODE;
			obj.statusText = COMM_ERROR;
		}

		if(args){
			obj.argument = args;
		}

		return obj;
    },

  /**
   * @description Method that initializes the custom HTTP headers for the each transaction.
   * @method initHeader
   * @public
   * @static
   * @param {string} label The HTTP header label
   * @param {string} value The HTTP header value
   * @param {string} isDefault Determines if the specific header is a default header
   * automatically sent with each transaction.
   * @return {void}
   */
	initHeader:function(label, value, isDefault)
	{
		var headerObj = (isDefault)?this._default_headers:this._http_headers;
		headerObj[label] = value;

		if(isDefault){
			this._has_default_headers = true;
		}
		else{
			this._has_http_headers = true;
		}
	},


  /**
   * @description Accessor that sets the HTTP headers for each transaction.
   * @method setHeader
   * @private
   * @static
   * @param {object} o The connection object for the transaction.
   * @return {void}
   */
	setHeader:function(o)
	{
            var actualHeaders = {};
            if(this._has_default_headers){
                Object.assign(actualHeaders, this._default_headers);
            }

            if(this._has_http_headers){
                Object.assign(actualHeaders, this._http_headers);

                delete this._http_headers;
                this._http_headers = {};
                this._has_http_headers = false;                        
            }
            
            if (Object.getOwnPropertyNames(actualHeaders).length > 0) {
                o.headers = actualHeaders;
            }
	},

  /**
   * @description Resets the default HTTP headers object
   * @method resetDefaultHeaders
   * @public
   * @static
   * @return {void}
   */
	resetDefaultHeaders:function(){
		delete this._default_headers;
		this._default_headers = {};
		this._has_default_headers = false;
	},

  /**
   * @description This method assembles the form label and value pairs and
   * constructs an encoded string.
   * asyncRequest() will automatically initialize the transaction with a
   * a HTTP header Content-Type of application/x-www-form-urlencoded.
   * @method setForm
   * @public
   * @static
   * @param {string || object} form id or name attribute, or form object.
   * @param {boolean} optional enable file upload.
   * @param {boolean} optional enable file upload over SSL in IE only.
   * @return {string} string of the HTML form field name and value pairs..
   */
	setForm:function(formId, isUpload, secureUri)
	{
            
            var oForm;
            this.resetFormState();
            
            if(typeof formId == 'string'){   // Determine if the argument is a form id or a form name.                    
                oForm = (document.getElementById(formId) || document.forms[formId]);
            } else if(typeof formId == 'object'){ // Treat argument as an HTML form object.                    
                oForm = formId;
            } else {
                return;
            }

            this._formData = new FormData(oForm);                
            this._isFormSubmit = true;
            this._isFileUpload = isUpload;
            this._formNode = oForm;

            this.initHeader('Content-Type', this._default_form_header);     //Check header
            return this._sFormData;     
	},

  /**
   * @description Resets HTML form properties when an HTML form or HTML form
   * with file upload transaction is sent.
   * @method resetFormState
   * @private
   * @static
   * @return {void}
   */
	resetFormState:function(){
		this._isFormSubmit = false;
		this._isFileUpload = false;
		this._formNode = null;
		this._sFormData = "";
	},

  /**
   * @description Creates an iframe to be used for form file uploads.  It is remove from the
   * document upon completion of the upload transaction.
   * @method createFrame
   * @private
   * @static
   * @param {string} optional qualified path of iframe resource for SSL in IE.
   * @return {void}
   */
	createFrame:function(secureUri){

	},

  /**
   * @description Parses the POST data and creates hidden form elements
   * for each key-value, and appends them to the HTML form object.
   * @method appendPostData
   * @private
   * @static
   * @param {string} postData The HTTP POST data
   * @return {array} formElements Collection of hidden fields.
   */
	appendPostData:function(postData)
	{
            return postData;
	},

  /**
   * @description Uploads HTML form, inclusive of files/attachments, using the
   * iframe created in createFrame to facilitate the transaction.
   * @method uploadFile
   * @private
   * @static
   * @param {int} id The transaction id.
   * @param {object} callback User-defined callback object.
   * @param {string} uri Fully qualified path of resource.
   * @param {string} postData POST data to be submitted in addition to HTML form.
   * @return {void}
   */
	uploadFile:function(o, callback, uri, postData)
        {
            var uploadEncoding = 'multipart/form-data',
                oConn = this,   
                args = (callback && callback.argument)?callback.argument:null;
        
        
            this._formData = new FormData(this._formNode);
            if(postData){
                    oElements = this.appendPostData(postData);
                    //Hay que agregar al formdata esto!
            }
            // Start file upload.
            this._formNode.submit();
            
            if(callback && callback.timeout){
                this._timeOut[o.tId] = window.setTimeout(function(){ oConn.abort(o, callback, true); }, callback.timeout);
            }
            
            this.resetFormState();
	},

  /**
   * @description Method to terminate a transaction, if it has not reached readyState 4.
   * @method abort
   * @public
   * @static
   * @param {object} o The connection object returned by asyncRequest.
   * @param {object} callback  User-defined callback object.
   * @param {string} isTimeout boolean to indicate if abort resulted from a callback timeout.
   * @return {boolean}
   */
	abort:function(o, callback, isTimeout)
	{
		var abortStatus;
		var args = (callback && callback.argument)?callback.argument:null;


		if(o){
                    if(this.isCallInProgress(o)){
                        // Issue abort request
                        o.abort();

                        window.clearInterval(this._poll[o.tId]);
                        delete this._poll[o.tId];

                        if(isTimeout){
                                window.clearTimeout(this._timeOut[o.tId]);
                                delete this._timeOut[o.tId];
                        }

                        abortStatus = true;
                    }
		} else {
			abortStatus = false;
		}

		if(abortStatus === true){
			this.handleTransactionResponse(o, callback, true);
		}

		return abortStatus;
	},

  /**
   * @description Determines if the transaction is still being processed.
   * @method isCallInProgress
   * @public
   * @static
   * @param {object} o The connection object returned by asyncRequest
   * @return {boolean}
   */
	isCallInProgress:function(o)
	{
            // if the XHR object assigned to the transaction has not been dereferenced,
            // then check its readyState status.  Otherwise, return false.
            if(o){
                return o.readyState !== 4 && o.readyState !== 0;
            }
            return false;	
	},

  /**
   * @description Dereference the XHR instance and the connection object after the transaction is completed.
   * @method releaseObject
   * @private
   * @static
   * @param {object} o The connection object
   * @return {void}
   */
	releaseObject:function(o)
	{
            if(o){
                //dereference the connection object.
                o = null;
            }
	}
};

toba.confirmar_inclusion('basicos/comunicacion_server');