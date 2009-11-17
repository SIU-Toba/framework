<?xml version="1.0" encoding="UTF-8"?>
<!--
  Copyright 2008 WSO2, Inc. http://www.wso2.org

  Licensed under the Apache License, Version 2.0 (the "License");
  you may not use this file except in compliance with the License.
  You may obtain a copy of the License at

  http://www.apache.org/licenses/LICENSE-2.0

  Unless required by applicable law or agreed to in writing, software
  distributed under the License is distributed on an "AS IS" BASIS,
  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
  See the License for the specific language governing permissions and
  limitations under the License.

  Created by: Jonathan Marsh <jonathan@wso2.com>

-->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">


    <xsl:output method="text"/>

    <xsl:param name="service" select="services/service[1]/@name"/>
    <xsl:param name="e4x" select="false()"/>
    <xsl:param name="localhost-endpoints" select="false()"/>

    <xsl:template match="/">
        <xsl:apply-templates select="services/service[@name=$service][1]"/>
    </xsl:template>

    <xsl:template match="service">
        <xsl:variable name="service-name">
            <xsl:call-template name="service-name-to-javascript-name">
                <xsl:with-param name="name" select="@name"/>
            </xsl:call-template>
        </xsl:variable>

        <xsl:variable name="original-service-name">
            <xsl:value-of select="@name"/>
        </xsl:variable>
//  Example stubs for <xsl:value-of select="$service-name"/> operations.  This function is not intended
//  to be called, but rather as a source for copy-and-paste development.

//  Note that this stub has been generated for use in <xsl:choose>
        <xsl:when test="$e4x">E4X</xsl:when>
        <xsl:otherwise>DOM</xsl:otherwise>
</xsl:choose> environments.
<xsl:if test="$localhost-endpoints">//  All endpoints have been converted to the "localhost" domain.</xsl:if>

function stubs() {
    <xsl:if test="operations/operation/binding-details[policy/@type = 'UTOverTransport']">
    <xsl:value-of select="$service-name"/>.username = "authorizedUserName";
    <xsl:value-of select="$service-name"/>.password = "authorizedUserPassword";

</xsl:if>
    <xsl:for-each select="operations/operation">
    <xsl:sort select="@name"/>
    <xsl:variable name="original-operation-name" select="@name"/>
    <xsl:variable name="name">
        <xsl:call-template name="operation-name-to-javascript-name">
            <xsl:with-param name="name" select="@name"/>
        </xsl:call-template>
    </xsl:variable>    // <xsl:value-of select="@name"/> operation
    try {
        <xsl:for-each select="signature/returns/param"><xsl:call-template name="return-type"/></xsl:for-each>
        <xsl:text> var </xsl:text>
        <xsl:value-of select="$original-operation-name"/>Return = <xsl:value-of select="$service-name"/>.<xsl:value-of select="$name"/>(<xsl:call-template name="parameters"><xsl:with-param name="prefix" select="'param_'"/></xsl:call-template>);
    } catch (e) {
        // fault handling
    }

</xsl:for-each>}
stubs.visible = false;

var <xsl:value-of select="$service-name"/> = new WebService("<xsl:for-each select="/services/service[@name=$original-service-name]">
            <xsl:sort select="@type = 'SOAP12'" order="descending"/>
            <xsl:sort select="@type = 'SOAP11'" order="descending"/>
            <xsl:sort select="@address" order="ascending"/>
            <xsl:if test="position() = 1"><xsl:value-of select="@endpoint"/></xsl:if>
        </xsl:for-each>");

<xsl:for-each select="operations/operation">
    <xsl:sort select="@name"/>
    <xsl:variable name="name">
    <xsl:call-template name="operation-name-to-javascript-name">
        <xsl:with-param name="name" select="@name"/>
    </xsl:call-template>
</xsl:variable>
<xsl:value-of select="$service-name"/>.<xsl:value-of select="$name"/> =
    function <xsl:value-of select="$name"/>(<xsl:call-template name="parameters"/>)
    {
        var isAsync, request, response, resultValue;
        this._options = new Array();
        isAsync = (this.<xsl:value-of select="$name"/>.callback != null &amp;&amp; typeof(this.<xsl:value-of select="$name"/>.callback) == 'function');
        request = this.<xsl:value-of select="$name"/>_payload(<xsl:call-template name="parameters"/>);

        if (isAsync) {
            try {
                this._call(
                    "<xsl:value-of select="@name"/>",
                    request,
                    function(thisRequest, callbacks) {
                        if (thisRequest.error != null) {
                            callbacks[1](thisRequest.error);
                        } else {
                            <xsl:choose>
                                <xsl:when test="$e4x">response = new XML(thisRequest.responseText);</xsl:when>
                                <xsl:otherwise>response = thisRequest.responseXML;</xsl:otherwise>
                            </xsl:choose>
                            if (response == null) {
                                resultValue = null;
                            } else {
                            <xsl:choose>
                                <xsl:when test="signature/returns/param/@maxOccurs = 'unbounded' or signature/returns/param/@maxOccurs > 1">
                                    <xsl:apply-templates select="signature/returns/param" mode="convert-array"/>
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:apply-templates select="signature/returns/param" mode="convert"/>
                                </xsl:otherwise>
                            </xsl:choose>                            }
                            callbacks[0](resultValue);
                        }
                    },
                    new Array(this.<xsl:value-of select="$name"/>.callback, this.<xsl:value-of select="$name"/>.onError)
                );
            } catch (e) {
                var error;
                if (WebServiceError.prototype.isPrototypeOf(e)) {
                    error = e;
                } else if (e.name != null) {
                    // Mozilla
                    error = new WebServiceError(e.name, e.message + " (" + e.fileName + "#" + e.lineNumber + ")");
                } else if (e.description != null) {
                    // IE
                    error = new WebServiceError(e.description, e.number, e.number);
                } else {
                    error = new WebServiceError(e, "Internal Error");
                }
                this.<xsl:value-of select="$name"/>.onError(error);
            }
        } else {
            try {
                                response = this._call("<xsl:value-of select="@name"/>", request);
                            <xsl:choose>
                                <xsl:when test="signature/returns/param/@maxOccurs = 'unbounded' or signature/returns/param/@maxOccurs > 1">
                                    <xsl:apply-templates select="signature/returns/param" mode="convert-array"/>
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:apply-templates select="signature/returns/param" mode="convert"/>
                                </xsl:otherwise>
                            </xsl:choose>                                return resultValue;
            } catch (e) {
                if (typeof(e) == "string") throw(e);
                if (e.message) throw(e.message);
                throw (e.reason);
            }
        }
        return null; // Suppress warnings when there is no return.
    }
<xsl:value-of select="$service-name"/>.<xsl:value-of select="$name"/>_payload =
    function (<xsl:call-template name="parameters"/>) {
        <xsl:call-template name="build-arrays"/>
        return <xsl:call-template name="payload"/>
    }
<xsl:value-of select="$service-name"/>.<xsl:value-of select="$name"/>_payload.visible = false;
<xsl:value-of select="$service-name"/>.<xsl:value-of select="$name"/>.callback = null;

</xsl:for-each>

// WebService object.
function WebService(endpointName)
{
    this.readyState = 0;
    this.onreadystatechange = null;
    this.scriptInjectionCallback = null;

    //public accessors for manually intervening in setting the address (e.g. supporting tcpmon)
    this.getAddress = function (endpointName)
    {
        return this._endpointDetails[endpointName].address;
    }

    this.setAddress = function (endpointName, address)
    {
        this._endpointDetails[endpointName].address = address;
    }

    // private helper functions
    this._getWSRequest = function()
    {
        var wsrequest;
        try {
            wsrequest = new WSRequest();
            // try to set the proxyAddress based on the context of the stub - browser or Mashup Server
            try {
                wsrequest.proxyEngagedCallback = this.scriptInjectionCallback;
                wsrequest.proxyAddress = document.URL.substring(0,document.URL.indexOf("/services/"));
            } catch (e) {
                try {
                    wsrequest.proxyEngagedCallback = this.scriptInjectionCallback;
                    wsrequest.proxyAddress = system.wwwURL.substring(0,system.wwwURL.indexOf("/services/"));
                } catch (e) { }
            }
        } catch(e) {
            try {
                wsrequest = new ActiveXObject("WSRequest");
            } catch(e) {
                try {
                    wsrequest = new SOAPHttpRequest();
                    netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
                } catch (e) {
                    throw new WebServiceError("WSRequest object not defined.", "WebService._getWSRequest() cannot instantiate WSRequest object.");
                }
            }
        }
        return wsrequest;
    }

    this._endpointDetails =
        {<xsl:for-each select="/services/service[@name=$original-service-name]">
            <xsl:sort select="@type = 'SOAP12'" order="descending"/>
            <xsl:sort select="@type = 'SOAP11'" order="descending"/>
            <xsl:sort select="@address" order="ascending"/>
            "<xsl:value-of select="@endpoint"/>": {
                "type" : "<xsl:value-of select="@type"/>",
                "address" : "<xsl:call-template name="localnameify-url"><xsl:with-param name="url" select="@address"/></xsl:call-template>"<xsl:if test="operations/operation/binding-details/@wsawaction">,
                "action" : {<xsl:for-each select="operations/operation[binding-details/@wsawaction]">
                    "<xsl:value-of select="@name"/>" : "<xsl:value-of select="binding-details/@wsawaction"/>"<xsl:if test="position() &lt; last()">,</xsl:if>
                    </xsl:for-each>
                }</xsl:if>
                <xsl:if test="operations/operation/binding-details/@soapaction">,
                "soapaction" : {<xsl:for-each select="operations/operation">
                    "<xsl:value-of select="@name"/>" : "<xsl:value-of select="binding-details/@soapaction"/>"<xsl:if test="position() &lt; last()">,</xsl:if>
                    </xsl:for-each>
                }</xsl:if><xsl:if test="operations/operation/binding-details/policy">,
                "securityPolicy" : {<xsl:for-each select="operations/operation">
                    "<xsl:value-of select="@name"/>" : "<xsl:value-of select="binding-details/policy/@type"/>"<xsl:if test="position() &lt; last()">,</xsl:if>
                    </xsl:for-each>
                }</xsl:if><xsl:if test="operations/operation/binding-details/@httplocation">,
                "httplocation" : {<xsl:for-each select="operations/operation">
                    "<xsl:value-of select="@name"/>" : "<xsl:value-of select="binding-details/@httplocation"/>"<xsl:if test="position() &lt; last()">,</xsl:if>
                    </xsl:for-each>
                }</xsl:if><xsl:if test="operations/operation/binding-details/@httpignoreUncited">,
                "httpignoreUncited" : {<xsl:for-each select="operations/operation">
                        "<xsl:value-of select="@name"/>" : "<xsl:value-of select="binding-details/@httpignoreUncited"/>"<xsl:if test="position() &lt; last()">,</xsl:if>
                    </xsl:for-each>
                }</xsl:if><xsl:if test="operations/operation/binding-details/@httpmethod">,
                "httpmethod" : {<xsl:for-each select="operations/operation">
                        "<xsl:value-of select="@name"/>" : <xsl:choose>
                            <xsl:when test="binding-details/@httpmethod">"<xsl:value-of select="binding-details/@httpmethod"/>"</xsl:when>
                            <xsl:otherwise>null</xsl:otherwise>
                    </xsl:choose><xsl:if test="position() &lt; last()">,</xsl:if>
                    </xsl:for-each>
                }</xsl:if><xsl:if test="operations/operation/binding-details/@httpqueryParameterSeparator">,
                "httpqueryParameterSeparator" : {<xsl:for-each select="operations/operation">
                        "<xsl:value-of select="@name"/>" : "<xsl:value-of select="binding-details/@httpqueryParameterSeparator"/>"<xsl:if test="position() &lt; last()">,</xsl:if>
                    </xsl:for-each>
                }</xsl:if><xsl:if test="operations/operation/binding-details/@httpinputSerialization">,
                "httpinputSerialization" : {<xsl:for-each select="operations/operation">
                        "<xsl:value-of select="@name"/>" : "<xsl:value-of select="binding-details/@httpinputSerialization"/>"<xsl:if test="position() &lt; last()">,</xsl:if>
                    </xsl:for-each>
                }</xsl:if><xsl:if test="@type = 'HTTP'">,
                "fitsInURLParams" : {<xsl:for-each select="operations/operation">
                        "<xsl:value-of select="@name"/>" : <xsl:value-of select="count(signature/params/param[@simple='no' or ((@type = 'QName' or @type = 'NOTATION' or @type = 'hexBinary' or @type = 'base64Binary') and @type-namespace = 'http://www.w3.org/2001/XMLSchema')]) = 0"/><xsl:if test="position() &lt; last()">,</xsl:if>
                    </xsl:for-each>
                }</xsl:if>
            }<xsl:if test="position() &lt; last()">,</xsl:if></xsl:for-each>
    };
    this.endpoint = endpointName;

    this.username = null;
    this.password = null;

    this._encodeXML = function (value) {
        var str = value.toString();
        str = str.replace(/&amp;/g, "&amp;amp;");
        str = str.replace(/&lt;/g, "&amp;lt;");
        return(str);
    };

    this._setOptions = function (details, opName) {
        var options = new Array();

        if (details.type == 'SOAP12') options.useSOAP = 1.2;
        else if (details.type == 'SOAP11') options.useSOAP = 1.1;
        else if (details.type == 'HTTP') options.useSOAP = false;

        if (options.useSOAP != false) {
            if (details.action != null) {
                options.useWSA = true;
                options.action = details.action[opName];
            } else if (details.soapaction != null) {
                options.useWSA = false;
                options.action = details.soapaction[opName];
            } else {
                options.useWSA = false;
                options.action = undefined;
            }
        }

        if (details["httpmethod"] != null) {
            options.HTTPMethod = details.httpmethod[opName];
        } else {
            options.HTTPMethod = null;
        }

        if (details["httpinputSerialization"] != null) {
            options.HTTPInputSerialization = details.httpinputSerialization[opName];
        } else {
            options.HTTPInputSerialization= null;
        }

        if (details["httplocation"] != null) {
            options.HTTPLocation = details.httplocation[opName];
        } else {
            options.HTTPLocation = null;
        }

        if (details["httpignoreUncited"] != null) {
            options.HTTPLocationIgnoreUncited = details.httpignoreUncited[opName];
        } else {
            options.HTTPLocationIgnoreUncited = null;
        }

        if (details["httpqueryParameterSeparator"] != null) {
            options.HTTPQueryParameterSeparator = details.httpqueryParameterSeparator[opName];
        } else {
            options.HTTPQueryParameterSeparator = null;
        }

        if (details["securityPolicy"]) {
            if (details["securityPolicy"][opName] == "UTOverTransport") {
                options.useWSS = true;
            }
        }

        return options;
    };

    this._call = function (opName, reqContent, callback, userdata)
    {
        var details = this._endpointDetails[this.endpoint];
        this._options = this._setOptions(details, opName);

        var isAsync = (typeof(callback) == 'function');

        var thisRequest = this._getWSRequest();
        if (isAsync) {
            thisRequest._userdata = userdata;
            thisRequest.onreadystatechange =
                function() {
                    if (thisRequest.readyState == 4) {
                        callback(thisRequest, userdata);
                    }
                }
        }

        if (this.username == null)
            thisRequest.open(this._options, details.address, isAsync);
        else
            thisRequest.open(this._options, details.address, isAsync, this.username, this.password);

        thisRequest.send(reqContent);
        if (isAsync) {
            return "";
        } else {
            try {
                var resultContent = thisRequest.responseText;
                if (resultContent == "") {
                    throw new WebServiceError("No response", "WebService._call() did not recieve a response to a synchronous request.");
                }
                <xsl:choose>
                    <xsl:when test="$e4x">var resultXML = new XML(thisRequest.responseText);</xsl:when>
                    <xsl:otherwise>var resultXML = thisRequest.responseXML;</xsl:otherwise>
                </xsl:choose>
            } catch (e) {
                throw new WebServiceError(e);
            }
            return resultXML;
        }
    };
}
WebService.visible = false;

WebService.utils = {
    toXSdate : function (thisDate) {
        var year = thisDate.getUTCFullYear();
        var month = thisDate.getUTCMonth() + 1;
        var day = thisDate.getUTCDate();

        return year + "-" +
            (month &lt; 10 ? "0" : "") + month + "-" +
            (day &lt; 10 ? "0" : "") + day + "Z";
    },

    toXStime : function (thisDate) {
        var hours = thisDate.getUTCHours();
        var minutes = thisDate.getUTCMinutes();
        var seconds = thisDate.getUTCSeconds();
        var milliseconds = thisDate.getUTCMilliseconds();

        return (hours &lt; 10 ? "0" : "") + hours + ":" +
            (minutes &lt; 10 ? "0" : "") + minutes + ":" +
            (seconds &lt; 10 ? "0" : "") + seconds +
            (milliseconds == 0 ? "" : (milliseconds/1000).toString().substring(1)) + "Z";
    },

    toXSdateTime : function (thisDate) {
        var year = thisDate.getUTCFullYear();
        var month = thisDate.getUTCMonth() + 1;
        var day = thisDate.getUTCDate();
        var hours = thisDate.getUTCHours();
        var minutes = thisDate.getUTCMinutes();
        var seconds = thisDate.getUTCSeconds();
        var milliseconds = thisDate.getUTCMilliseconds();

        return year + "-" +
            (month &lt; 10 ? "0" : "") + month + "-" +
            (day &lt; 10 ? "0" : "") + day + "T" +
            (hours &lt; 10 ? "0" : "") + hours + ":" +
            (minutes &lt; 10 ? "0" : "") + minutes + ":" +
            (seconds &lt; 10 ? "0" : "") + seconds +
            (milliseconds == 0 ? "" : (milliseconds/1000).toString().substring(1)) + "Z";
    },

    parseXSdateTime : function (dateTime) {
        var buffer = dateTime.toString();
        var p = 0; // pointer to current parse location in buffer.

        var era, year, month, day, hour, minute, second, millisecond;

        // parse date, if there is one.
        if (buffer.substr(p,1) == '-')
        {
            era = -1;
            p++;
        } else {
            era = 1;
        }

        if (buffer.charAt(p+2) != ':')
        {
            year = era * buffer.substr(p,4);
            p += 5;
            month = buffer.substr(p,2);
            p += 3;
            day = buffer.substr(p,2);
            p += 3;
        } else {
            year = 1970;
            month = 1;
            day = 1;
        }

        // parse time, if there is one
        if (buffer.charAt(p) != '+' &amp;&amp; buffer.charAt(p) != '-')
        {
            hour = buffer.substr(p,2);
            p += 3;
            minute = buffer.substr(p,2);
            p += 3;
            second = buffer.substr(p,2);
            p += 2;
            if (buffer.charAt(p) == '.')
            {
                millisecond = parseFloat(buffer.substring(p))*1000;
                // Note that JS fractional seconds are significant to 3 places - xs:time is significant to more -
                // though implementations are only required to carry 3 places.
                p++;
                while (buffer.charCodeAt(p) >= 48 &amp;&amp; buffer.charCodeAt(p) &lt;= 57) p++;
            } else {
                millisecond = 0;
            }
        } else {
            hour = 0;
            minute = 0;
            second = 0;
            millisecond = 0;
        }

        var tzhour = 0;
        var tzminute = 0;
        // parse time zone
        if (buffer.charAt(p) != 'Z' &amp;&amp; buffer.charAt(p) != '') {
            var sign = (buffer.charAt(p) == '-' ? -1 : +1);
            p++;
            tzhour = sign * buffer.substr(p,2);
            p += 3;
            tzminute = sign * buffer.substr(p,2);
        }

        var thisDate = new Date();
        thisDate.setUTCFullYear(year);
        thisDate.setUTCMonth(month-1);
        thisDate.setUTCDate(day);
        thisDate.setUTCHours(hour);
        thisDate.setUTCMinutes(minute);
        thisDate.setUTCSeconds(second);
        thisDate.setUTCMilliseconds(millisecond);
        thisDate.setUTCHours(thisDate.getUTCHours() - tzhour);
        thisDate.setUTCMinutes(thisDate.getUTCMinutes() - tzminute);
        return thisDate;
    },

    _nextPrefixNumber : 0,

    _QNameNamespaceDecl : function (qn) {
        if (qn.uri == null) return "";
        var prefix = qn.localName.substring(0, qn.localName.indexOf(":"));
        if (prefix == "") {
            prefix = "n" + ++this._nextPrefixNumber;
        }
        return ' xmlns:' + prefix + '="' + qn.uri + '"';
    },

    _QNameValue : function(qn) {
        if (qn.uri == null) return qn.localName;
        var prefix, localName;
        if (qn.localName.indexOf(":") >= 0) {
            prefix = qn.localName.substring(0, qn.localName.indexOf(":"));
            localName = qn.localName.substring(qn.localName.indexOf(":")+1);
        } else {
            prefix = "n" + this._nextPrefixNumber;
            localName = qn.localName;
        }
        return prefix + ":" + localName;
    },

    scheme : function (url) {
        var s = url.substring(0, url.indexOf(':'));
        return s;
    },

    domain : function (url) {
        var d = url.substring(url.indexOf('://') + 3, url.indexOf('/',url.indexOf('://')+3));
        return d;
    },

    domainNoPort : function (url) {
        var d = this.domain(url);
        if (d.indexOf(":") >= 0)
        d = d.substring(0, d.indexOf(':'));
        return d;
    },

    _serializeAnytype : function (name, value, namespace, optional) {
        // dynamically serialize an anyType value in xml, including setting xsi:type.
        if (optional &amp;&amp; value == null) return "";
        var type = "xs:string";
        if (value == null) {
            value = "";
        } else if (typeof(value) == "number") {
            type = "xs:double";
        <xsl:if test="$e4x">} else if (typeof(value) == "xml") {
            type = "xs:anyType";
            value = value.toXMLString();</xsl:if>
        <xsl:if test="not($e4x)">} else if (typeof(value) == "object" &amp;&amp; value.nodeType != undefined) {
            type = "xs:anyType";
            value = WebService.utils._serializeXML(value);</xsl:if>
        } else if (typeof(value) == "boolean") {
            type = "xs:boolean";
        } else if (typeof(value) == "object" &amp;&amp; Date.prototype.isPrototypeOf(value)) {
            type = "xs:dateTime";
            value = WebService.utils.toXSdateTime(value);
        } else if (value.match(/^\s*true\s*$/g) != null) {
            type = "xs:boolean";
        } else if (value.match(/^\s*false\s*$/g) != null) {
            type = "xs:boolean";
        } else if (!isNaN(Date.parse(value))) {
            type = "xs:dateTime";
            value = WebService.utils.toXSdateTime(new Date(Date.parse(value)));
        } else if (value.match(/^\s*\-?\d*\-\d\d\-\d\dZ?\s*$/g) != null) {
            type = "xs:date";
        } else if (value.match(/^\s*\-?\d*\-\d\d\-\d\d[\+\-]\d\d:\d\d\s*$/g) != null) {
            type = "xs:date";
        } else if (value.match(/^\s*\d\d:\d\d:\d\d\.?\d*Z?\s*$/g) != null) {
            type = "xs:time";
        } else if (value.match(/^\s*\d\d:\d\d:\d\d\.?\d*[\+\-]\d\d:\d\d\s*$/g) != null) {
            type = "xs:time";
        } else if (value.match(/^\s*\-?\d*\-\d\d\-\d\dT\d\d:\d\d:\d\d\.?\d*Z?\s*$/g) != null) {
            type = "xs:dateTime";
        } else if (value.match(/^\s*\-?\d*\-\d\d\-\d\dT\d\d:\d\d:\d\d\.?\d*[\+\-]\d\d:\d\d\s*$/g) != null) {
            type = "xs:dateTime";
        } else if (value.match(/^\s*\d\d*\.?\d*\s*$/g) != null) {
            type = "xs:double";
        } else if (value.match(/^\s*\d*\.?\d\d*\s*$/g) != null) {
            type = "xs:double";
        } else if (value.match(/^\s*\&lt;/g) != null) {
    <xsl:choose>
        <xsl:when test="$e4x">
            try {
                value = new XML(value).toXMLString();
                type = "xs:anyType";
            } catch (e) {}
        </xsl:when>
        <xsl:otherwise>
            var browser = WSRequest.util._getBrowser();
            var parseTest;
            if (browser == "ie" || browser == "ie7") {
                parseTest = new ActiveXObject("Microsoft.XMLDOM");
                parseTest.loadXML(value);
                if (parseTest.parseError == 0)
                    type = "xs:anyType";
            } else {
                var parser = new DOMParser();
                parseTest = parser.parseFromString(value,"text/xml");
                if (parseTest.documentElement.nodeName != "parsererror" || parseTest.documentElement.namespaceURI != "http://www.mozilla.org/newlayout/xml/parsererror.xml")
                    type = "xs:anyType";
            }
        </xsl:otherwise>
    </xsl:choose>
        }
        if (type == "xs:string") {
            value = <xsl:value-of select="$service-name"/>._encodeXML(value);
        }
        var starttag =   "&lt;" + name +
                     (namespace == "" ? "" : " xmlns='" + namespace + "'") +
                     " xsi:type='" + type + "'" +
                     " xmlns:xs='http://www.w3.org/2001/XMLSchema' xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance'" +
                     "&gt;";
        var endtag = "&lt;/" + name + "&gt;";
        return starttag + value + endtag;
    },

    <xsl:if test="not($e4x)">_serializeXML : function(payload) {
        var browser = WSRequest.util._getBrowser();
        switch (browser) {
            case "gecko":
            case "safari":
                var serializer = new XMLSerializer();
                return serializer.serializeToString(payload);
                break;
            case "ie":
            case "ie7":
                return payload.xml;
                break;
            case "opera":
                var xmlSerializer = document.implementation.createLSSerializer();
                return xmlSerializer.writeToString(payload);
                break;
            case "undefined":
                throw new WebServiceError("Unknown browser", "WSRequest.util._serializeToString doesn't recognize the browser, to invoke browser-specific serialization code.");
        }
    },
    </xsl:if>
    // library function for dynamically converting an element with js:type annotation to a Javascript type.
    <xsl:choose>
        <xsl:when test="$e4x">_convertJSType : function (element, isWrapped) {
        if (element == null) return "";
        var extractedValue = element.*.toString();
        var resultValue, i;
        var js = new Namespace("http://www.wso2.org/ns/jstype");
        var type = element.@js::type;
        if (type == null) {
            type = "#raw";
        } else {
            type = type.toString();
        }
        switch (type) {
            case "string":
                return extractedValue;
                break;
            case "number":
                return parseFloat(extractedValue);
                break;
            case "boolean":
                return extractedValue == "true" || extractedValue == "1";
                break;
            case "date":
                return WebService.utils.parseXSdateTime(extractedValue);
                break;
            case "array":
                resultValue = new Array();
                for (i=0; i&lt;element.*.length(); i++) {
                    resultValue = resultValue.concat(WebService.utils._convertJSType(element[i]));
                }
                return(resultValue);
                break;
            case "object":
                resultValue = new Object();
                for (i=0; i&lt;element.*.length(); i++) {
                    resultValue[element[i].name()] = WebService.utils._convertJSType(element[i]);
                }
                return(resultValue);
                break;
            case "xmlList":
                return element.*;
                break;
            case "xml":
                return element.*[0];
                break;
            case "#raw":
            default:
                if (isWrapped == true)
                    return element.*;
                else return element;
                break;
        }
    }</xsl:when>
        <xsl:otherwise>_convertJSType : function (element, isWrapped) {
        if (element == null) return "";
        var extractedValue = WSRequest.util._stringValue(element);
        var resultValue, i;
        var type = element.getAttribute("js:type");
        if (type == null) {
            type = "#raw";
        } else {
            type = type.toString();
        }
        switch (type) {
            case "string":
                return extractedValue;
                break;
            case "number":
                return parseFloat(extractedValue);
                break;
            case "boolean":
                return extractedValue == "true" || extractedValue == "1";
                break;
            case "date":
                return WebService.utils.parseXSdateTime(extractedValue);
                break;
            case "array":
                resultValue = new Array();
                for (i=0; i&lt;element.childNodes.length; i++) {
                    resultValue = resultValue.concat(WebService.utils._convertJSType(element.childNodes[i]));
                }
                return(resultValue);
                break;
            case "object":
                resultValue = new Object();
                for (i=0; i&lt;element.childNodes.length; i++) {
                    resultValue[element.childNodes[i].tagName] = WebService.utils._convertJSType(element.childNodes[i]);
                }
                return(resultValue);
                break;
            case "xmlList":
                return element.childNodes;
                break;
            case "xml":
                return element.firstChild;
                break;
            case "#raw":
            default:
                if (isWrapped == true)
                    return element.firstChild;
                else return element;
                break;
        }
    }</xsl:otherwise>
    </xsl:choose>

};

// URL fixup code for a browser.  All variables prefixed with "_fix_" to avoid conflicts with user's local variables.
try {
    var _fix_secureEndpoint = "";
    var _fix_pageUrl = document.URL;
    var _fix_pageScheme = WebService.utils.scheme(_fix_pageUrl);
    // only attempt fixup if we're from an http/https domain ('file:' works fine on IE without fixup)
    if (_fix_pageScheme == "http" || _fix_pageScheme == "https") {
        var _fix_pageDomain = WebService.utils.domain(_fix_pageUrl);
        var _fix_pageDomainNoPort = WebService.utils.domainNoPort(_fix_pageUrl);
        var _fix_endpoints = <xsl:value-of select="$service-name"/>._endpointDetails;
        // loop through each available endpoint
        for (var _fix_i in _fix_endpoints) {
            var _fix_address = _fix_endpoints[_fix_i].address;
            var _fix_address_scheme = WebService.utils.scheme(_fix_address);
            if (_fix_address_scheme == 'http' || _fix_address_scheme == 'https') {
                // if we're in a secure domain, set the endpoint to the first secure endpoint we come across
                if (_fix_secureEndpoint == "" &amp;&amp; _fix_pageScheme == "https" &amp;&amp; _fix_address_scheme == "https") {
                    _fix_secureEndpoint = _fix_i;
                    <xsl:value-of select="$service-name"/>.endpoint = _fix_secureEndpoint;
                }
                // if we're in a known localhost domain, rewrite the endpoint domain so that we won't get
                //  a bogus xss violation
                if (_fix_pageDomainNoPort.indexOf('localhost') == 0 || _fix_pageDomainNoPort.indexOf('127.0.0.1') == 0 ) {
                    _fix_endpoints[_fix_i].address = _fix_address.replace(WebService.utils.domainNoPort(_fix_address), _fix_pageDomainNoPort);
                }
            }
        }
    }
} catch (e) { }

</xsl:template>

<xsl:template match="param[@type-namespace = 'http://www.w3.org/2001/XMLSchema' and
                              ( @type = 'string' or
                                @type = 'normalizedString' or
                                @type = 'token' or
                                @type = 'language' or
                                @type = 'Name' or
                                @type = 'NCName' or
                                @type = 'ID' or
                                @type = 'IDREF' or
                                @type = 'NMTOKEN' or
                                @type = 'ENTITY' or
                                @type = 'anyURI' or
                                @type = 'hexBinary' or
                                @type = 'base64Binary' or
                                @type = 'decimal' or
                                @type = 'NOTATION' or
                                @type = 'duration'
                              )]" mode="convert">
                                <xsl:call-template name="extracted-value"/>
                                resultValue = <xsl:call-template name="return-type"/> extractedValue;
</xsl:template>

<xsl:template match="param[@type-namespace = 'http://www.w3.org/2001/XMLSchema' and
                            ( @type = 'string' or
                              @type = 'normalizedString' or
                              @type = 'token' or
                              @type = 'language' or
                              @type = 'Name' or
                              @type = 'NCName' or
                              @type = 'ID' or
                              @type = 'IDREF' or
                              @type = 'NMTOKEN' or
                              @type = 'ENTITY' or
                              @type = 'anyURI' or
                              @type = 'hexBinary' or
                              @type = 'base64Binary' or
                              @type = 'decimal' or
                              @type = 'NOTATION' or
                              @type = 'duration'
                            )]" mode="convert-array">
                                <xsl:call-template name="extracted-values">
                                    <xsl:with-param name="extraction-code">extractedValue</xsl:with-param>
                                </xsl:call-template>
                                resultValue = <xsl:call-template name="return-type"/> extractedValues;
</xsl:template>

<xsl:template match="param[@type-namespace = 'http://www.w3.org/2001/XMLSchema' and
                              ( @type = 'integer' or
                                @type = 'nonPositiveInteger' or
                                @type = 'negativeInteger' or
                                @type = 'long' or
                                @type = 'int' or
                                @type = 'short' or
                                @type = 'byte' or
                                @type = 'nonNegativeInteger' or
                                @type = 'unsignedLong' or
                                @type = 'unsignedInt' or
                                @type = 'unsignedShort' or
                                @type = 'unsignedByte' or
                                @type = 'positiveInteger' or
                                @type = 'gYear' or
                                @type = 'gMonth' or
                                @type = 'gDay'
                              )]" mode="convert">
                                <xsl:call-template name="extracted-value"/>
                                resultValue = <xsl:call-template name="return-type"/> parseInt(extractedValue);
</xsl:template>

<xsl:template match="param[@type-namespace = 'http://www.w3.org/2001/XMLSchema' and
                            ( @type = 'integer' or
                              @type = 'nonPositiveInteger' or
                              @type = 'negativeInteger' or
                              @type = 'long' or
                              @type = 'int' or
                              @type = 'short' or
                              @type = 'byte' or
                              @type = 'nonNegativeInteger' or
                              @type = 'unsignedLong' or
                              @type = 'unsignedInt' or
                              @type = 'unsignedShort' or
                              @type = 'unsignedByte' or
                              @type = 'positiveInteger' or
                              @type = 'gYear' or
                              @type = 'gMonth' or
                              @type = 'gDay'
                            )]" mode="convert-array">
                                <xsl:call-template name="extracted-values">
                                    <xsl:with-param name="extraction-code">(extractedValue == "INF" || extractedValue == "+INF" ? Infinity : (extractedValue == "-INF" ? -Infinity : parseInt(extractedValue)))</xsl:with-param>
                                </xsl:call-template>
                                resultValue = <xsl:call-template name="return-type"/> extractedValues;
</xsl:template>

<xsl:template match="param[@type-namespace = 'http://www.w3.org/2001/XMLSchema' and
                              ( @type = 'float' or
                                @type = 'double'
                              )]" mode="convert">
                                <xsl:call-template name="extracted-value"/>
                                if (extractedValue == "INF" || extractedValue == "+INF")
                                    resultValue = Infinity;
                                else if (extractedValue == "-INF")
                                    resultValue = -Infinity;
                                else resultValue = <xsl:call-template name="return-type"/> parseFloat(extractedValue);
</xsl:template>

<xsl:template match="param[@type-namespace = 'http://www.w3.org/2001/XMLSchema' and
                            ( @type = 'float' or
                              @type = 'double'
                            )]" mode="convert-array">
                                <xsl:call-template name="extracted-values">
                                    <xsl:with-param name="extraction-code">(extractedValue == "INF" || extractedValue == "+INF" ? Infinity : (extractedValue == "-INF" ? -Infinity : parseFloat(extractedValue)))</xsl:with-param>
                                </xsl:call-template>
                                resultValue = <xsl:call-template name="return-type"/> extractedValues;
</xsl:template>

<xsl:template match="param[@type-namespace = 'http://www.w3.org/2001/XMLSchema' and
                              ( @type = 'boolean' )]" mode="convert">
                                <xsl:call-template name="extracted-value"/>
                                resultValue = /* Boolean */ extractedValue == "true" || extractedValue == "1";
</xsl:template>

<xsl:template match="param[@type-namespace = 'http://www.w3.org/2001/XMLSchema' and
                              ( @type = 'boolean' )]" mode="convert-array">
                                <xsl:call-template name="extracted-values">
                                    <xsl:with-param name="extraction-code">extractedValue == "true" || extractedValue == "1"</xsl:with-param>
                                </xsl:call-template>
                                resultValue = <xsl:call-template name="return-type"/> extractedValues;
</xsl:template>

<!-- todo: Do a better job than String for the following date types
    <simple type="xs:gMonthDay"/>
    <simple type="xs:gYearMonth"/>
-->
<xsl:template match="param[@type-namespace = 'http://www.w3.org/2001/XMLSchema' and
                            ( @type = 'gMonthDay' or
                              @type = 'gYearMonth'
                            )]" mode="convert">
                                <xsl:call-template name="extracted-value"/>
                                resultValue = <xsl:call-template name="return-type"/> extractedValue.toString();
</xsl:template>

<xsl:template match="param[@type-namespace = 'http://www.w3.org/2001/XMLSchema' and
                            ( @type = 'gMonthDay' or
                              @type = 'gYearMonth'
                             )]" mode="convert-array">
                                <xsl:call-template name="extracted-values">
                                    <xsl:with-param name="extraction-code">extractedValue.toString()</xsl:with-param>
                                </xsl:call-template>
                                resultValue = <xsl:call-template name="return-type"/> extractedValues;
</xsl:template>

<xsl:template match="param[@type-namespace = 'http://www.w3.org/2001/XMLSchema' and
                        ( @type = 'date' or
                          @type = 'dateTime' or
                          @type = 'time'
                        )]" mode="convert">
                                <xsl:call-template name="extracted-value"/>
                                resultValue = /* Date */ WebService.utils.parseXSdateTime(extractedValue);
</xsl:template>

<xsl:template match="param[@type-namespace = 'http://www.w3.org/2001/XMLSchema' and
                        ( @type = 'date' or
                          @type = 'dateTime' or
                          @type = 'time'
                        )]" mode="convert-array">
                                <xsl:call-template name="extracted-values">
                                    <xsl:with-param name="extraction-code">WebService.utils.parseXSdateTime(extractedValue)</xsl:with-param>
                                </xsl:call-template>
                                resultValue = <xsl:call-template name="return-type"/> extractedValues;
</xsl:template>

<xsl:template match="param[@type-namespace = 'http://www.w3.org/2001/XMLSchema' and
                            ( @type = 'ENTITIES' )]" mode="convert">
                                <xsl:call-template name="extracted-value"/>
                                resultValue = /* array of xs:ENTITY */ extractedValue.split(' '));
</xsl:template>

<xsl:template match="param[@type-namespace = 'http://www.w3.org/2001/XMLSchema' and
                            ( @type = 'IDREFS' )]" mode="convert">
                                <xsl:call-template name="extracted-value"/>
                                resultValue = /* array of xs:IDREF */ extractedValue.split(' '));
</xsl:template>

<xsl:template match="param[@type-namespace = 'http://www.w3.org/2001/XMLSchema' and
                            ( @type = 'NMTOKENS' )]" mode="convert">
                                <xsl:call-template name="extracted-value"/>
                                resultValue = /* array of xs:NMTOKEN */ extractedValue.split(' '));
</xsl:template>

<xsl:template match="param[@type-namespace = 'http://www.w3.org/2001/XMLSchema' and
                              ( @type = 'QName' )]" mode="convert">
    <xsl:choose>
        <xsl:when test="$e4x and not(@targetNamespace)">    if (response["<xsl:value-of select="@name"/>"] == null) {
                                    resultValue = null;
                                } else {
                                    var extractedValue = (response["<xsl:value-of select="@name"/>"]).toString();
                                    var prefix = extractedValue.substring(0, extractedValue.indexOf(':'));
                                    var extractedNamespace = "";
                                    for each (n in response["<xsl:value-of select="@name"/>"].inScopeNamespaces()) {
                                        if (n.prefix == prefix) extractedNamespace = n.uri;
                                    }
                                    resultValue = /* xs:QName */ new QName(extractedNamespace,  extractedValue);
                                }
</xsl:when>
        <xsl:when test="$e4x">    var ns = new Namespace('<xsl:value-of select="@targetNamespace"/>');
                                if (response.ns::["<xsl:value-of select="@name"/>"] == null) {
                                    resultValue = null;
                                } else {
                                    var extractedValue = (response.ns::["<xsl:value-of select="@name"/>"]).toString();
                                    var prefix = extractedValue.substring(0, extractedValue.indexOf(':'));
                                    var extractedNamespace = "";
                                    for each (n in response.ns::["<xsl:value-of select="@name"/>"].inScopeNamespaces()) {
                                        if (n.prefix == prefix) extractedNamespace = n.uri;
                                    }
                                    resultValue = /* xs:QName */ new QName(extractedNamespace,  extractedValue);
                                }
</xsl:when>
        <xsl:otherwise>    if (response.documentElement.firstChild == null) {
                                    resultValue = null;
                                } else {
                                    var extractedValue = WSRequest.util._stringValue(response.documentElement.firstChild);
                                    var prefix = extractedValue.substring(0, extractedValue.indexOf(':'));

                                    var browser = WSRequest.util._getBrowser();
                                    if (browser == "ie" || browser == "ie7") {
                                        var extractedNamespace = response.documentElement.firstChild.getAttribute("xmlns:" + prefix);
                                    } else {
                                        var extractedNamespace = response.documentElement.firstChild.lookupNamespaceURI(prefix);
                                    }

                                    resultValue = /* xs:QName */ { "uri" : extractedNamespace,  "localName" : extractedValue };
                                }
</xsl:otherwise>
    </xsl:choose>
</xsl:template>

<!-- value extraction for single xs:anyType value when there is an rpc wrappers -->
<xsl:template match="param[@name][@type-namespace = 'http://www.w3.org/2001/XMLSchema' and
                          ( @type = 'anyType' )]" mode="convert">
    <xsl:choose>
        <xsl:when test="$e4x and not(@targetNamespace)">    resultValue = /* xs:anyType */ WebService.utils._convertJSType(response["<xsl:value-of select="@name"/>"], true);
</xsl:when>
        <xsl:when test="$e4x">    var ns = new Namespace('<xsl:value-of select="@targetNamespace"/>');
                                resultValue = /* xs:anyType */ WebService.utils._convertJSType(response.ns::["<xsl:value-of select="@name"/>"], true);
</xsl:when>
        <xsl:otherwise>    resultValue = /* xs:anyType */ WebService.utils._convertJSType(response.documentElement.firstChild, true);
</xsl:otherwise>
    </xsl:choose>
</xsl:template>

<!-- value extraction for single xs:anyType value when there is no rpc wrappers -->
<xsl:template match="param[not(@name)][@type-namespace = 'http://www.w3.org/2001/XMLSchema' and
                           ( @type = 'anyType' )]" mode="convert">
    <xsl:choose>
        <xsl:when test="$e4x">    resultValue = /* xs:anyType */ WebService.utils._convertJSType(response);
</xsl:when>
        <xsl:otherwise>    resultValue = /* xs:anyType */ WebService.utils._convertJSType(response.documentElement);
</xsl:otherwise>
    </xsl:choose>
</xsl:template>

<xsl:template match="param[@type-namespace = 'http://www.w3.org/2001/XMLSchema' and
                           ( @type = 'anyType' )]" mode="convert-array">
    <xsl:choose>
        <xsl:when test="$e4x and not(@targetNamespace)">    resultValue = /* xs:anyType */ WebService.utils._convertJSType(response["<xsl:value-of select="@name"/>"]);
</xsl:when>
        <xsl:when test="$e4x">    var ns = new Namespace('<xsl:value-of select="@targetNamespace"/>');
                                resultValue = /* xs:anyType */ WebService.utils._convertJSType(response.ns::["<xsl:value-of select="@name"/>"]);
</xsl:when>
        <xsl:otherwise>    resultValue = /* xs:anyType */ WebService.utils._convertJSType(response.documentElement.firstChild);
</xsl:otherwise>
    </xsl:choose>
</xsl:template>

<xsl:template match="param" mode="convert">
    <xsl:choose>
        <xsl:when test="$e4x">    resultValue = <xsl:call-template name="return-type"/> response;
</xsl:when>
        <xsl:otherwise>    resultValue = <xsl:call-template name="return-type"/> response.documentElement;
</xsl:otherwise>
    </xsl:choose>
</xsl:template>

<xsl:template match="param" mode="convert-array">
    <xsl:choose>
        <xsl:when test="$e4x">    resultValue = <xsl:call-template name="return-type"/> response;
</xsl:when>
        <xsl:otherwise>    resultValue = <xsl:call-template name="return-type"/> response.documentElement;
</xsl:otherwise>
    </xsl:choose>
</xsl:template>

<!--
<xsl:template match="param" mode="convert-array">
    <xsl:choose>
        <xsl:when test="$e4x">    resultValue = &lt;&gt;{response.*}&lt;/&gt;;
</xsl:when>
        <xsl:otherwise>    var extractedItems = response.documentElement.childNodes;
                                var extractedValues = new Array();
                                for (var i=0; i&lt;extractedItems.length; i++) {
                                    extractedValues = extractedValues.concat(extractedItems.item(i));
                                }
                                resultValue = <xsl:call-template name="return-type"/> extractedValues;
</xsl:otherwise>
    </xsl:choose>
</xsl:template>
-->
<xsl:template name="extracted-value">
    <xsl:choose>
        <xsl:when test="$e4x and not(@targetNamespace)">    var extractedValue = (response["<xsl:value-of select="@name"/>"]).toString();</xsl:when>
        <xsl:when test="$e4x">    var ns = new Namespace('<xsl:value-of select="@targetNamespace"/>');
                                var extractedValue = response.ns::["<xsl:value-of select="@name"/>"].toString();</xsl:when>
        <xsl:otherwise>    var extractedValue = WSRequest.util._stringValue(response.documentElement);</xsl:otherwise>
    </xsl:choose>
</xsl:template>

<xsl:template name="extracted-values">
    <xsl:param name="extraction-code"/>
    <xsl:choose>
        <!-- TODO E4X arrays -->
        <xsl:when test="$e4x">    var extractedValue = (response.ns::["<xsl:value-of select="@name"/>"]).toString();</xsl:when>
        <xsl:otherwise>    var extractedItems = response.documentElement.childNodes;
                                var extractedValues = new Array();
                                for (var i=0; i&lt;extractedItems.length; i++) {
                                    var node = extractedItems.item(i);
                                    if (node.nodeType == 1) {
                                        var extractedValue = WSRequest.util._stringValue(node);
                                        extractedValues = extractedValues.concat(<xsl:value-of select="$extraction-code"/>);
                                    }
                                }</xsl:otherwise>
    </xsl:choose>
</xsl:template>

<xsl:template name="parameters">
    <xsl:param name="prefix" select="'_'"/>
    <xsl:for-each select="signature/params/param">
        <xsl:choose>
            <xsl:when test="@token = '#any'">/* XMLList */ additionalParameters</xsl:when>
            <xsl:otherwise>/* <xsl:if test="@maxOccurs = 'unbounded' or @maxOccurs > 1">array of </xsl:if><xsl:value-of select="@type"/> */ <xsl:value-of select="$prefix"/><xsl:value-of select="@name"/></xsl:otherwise>
        </xsl:choose>
        <xsl:if test="position() != last()">, </xsl:if>
    </xsl:for-each>
</xsl:template>

<xsl:template name="payload">
    <xsl:choose>
        <xsl:when test="signature/params/@wrapper-element">'&lt;p:<xsl:value-of select="signature/params/@wrapper-element"/> xmlns:p="<xsl:value-of select="signature/params/@wrapper-element-ns"/>
            <xsl:text>"&gt;' +</xsl:text>
            <xsl:for-each select="signature/params/param">
            <xsl:choose>
                <xsl:when test="@token = '#any'">
                additionalParameters + '</xsl:when>
                <xsl:when test="@maxOccurs = 'unbounded' or @maxOccurs > 1">
                _array_of_<xsl:value-of select="@name"/> + </xsl:when>
                <xsl:when test="@type = 'date' and @type-namespace='http://www.w3.org/2001/XMLSchema'">
                (_<xsl:value-of select="@name"/> == null ? '' : '&lt;<xsl:value-of select="@name"/><xsl:if test="@targetNamespace"> xmlns="<xsl:value-of select="@targetNamespace"/>"</xsl:if>&gt;' + (typeof(_<xsl:value-of select="@name"/>) == 'object' ? WebService.utils.toXSdate(_<xsl:value-of select="@name"/>) : _<xsl:value-of select="@name"/>) + '&lt;/<xsl:value-of select="@name"/>&gt;') +</xsl:when>
                <xsl:when test="@type = 'time' and @type-namespace='http://www.w3.org/2001/XMLSchema'">
                (_<xsl:value-of select="@name"/> == null ? '' : '&lt;<xsl:value-of select="@name"/><xsl:if test="@targetNamespace"> xmlns="<xsl:value-of select="@targetNamespace"/>"</xsl:if>&gt;' + (typeof(_<xsl:value-of select="@name"/>) == 'object' ? WebService.utils.toXStime(_<xsl:value-of select="@name"/>) : _<xsl:value-of select="@name"/>) + '&lt;/<xsl:value-of select="@name"/>&gt;') +</xsl:when>
                <xsl:when test="@type = 'dateTime' and @type-namespace='http://www.w3.org/2001/XMLSchema'">
                (_<xsl:value-of select="@name"/> == null ? '' : '&lt;<xsl:value-of select="@name"/><xsl:if test="@targetNamespace"> xmlns="<xsl:value-of select="@targetNamespace"/>"</xsl:if>&gt;' + (typeof(_<xsl:value-of select="@name"/>) == 'object' ? WebService.utils.toXSdateTime(_<xsl:value-of select="@name"/>) : _<xsl:value-of select="@name"/>) + '&lt;/<xsl:value-of select="@name"/>&gt;') +</xsl:when>
                <xsl:when test="@type = 'QName' and @type-namespace='http://www.w3.org/2001/XMLSchema'">
                (_<xsl:value-of select="@name"/> == null ? '' : '&lt;<xsl:value-of select="@name"/><xsl:if test="@targetNamespace"> xmlns="<xsl:value-of select="@targetNamespace"/>"</xsl:if>' + WebService.utils._QNameNamespaceDecl(_<xsl:value-of select="@name"/>) + '&gt;' + WebService.utils._QNameValue(_<xsl:value-of select="@name"/>) + '&lt;/<xsl:value-of select="@name"/>&gt;') +</xsl:when>
                <xsl:when test="@type = 'anyType' and @type-namespace='http://www.w3.org/2001/XMLSchema'">
                WebService.utils._serializeAnytype('<xsl:value-of select="@name"/>', _<xsl:value-of select="@name"/>, '<xsl:value-of select="@targetNamespace"/>', <xsl:value-of
                        select="@minOccurs = '0'"/>) +</xsl:when>
                <xsl:when test="@simple = 'yes'">
                (_<xsl:value-of select="@name"/> == null ? '' : '&lt;<xsl:value-of select="@name"/><xsl:if test="@targetNamespace"> xmlns="<xsl:value-of select="@targetNamespace"/>"</xsl:if>&gt;' + this._encodeXML(_<xsl:value-of select="@name"/>) + '&lt;/<xsl:value-of select="@name"/>&gt;') +</xsl:when>
                <xsl:otherwise>
                (_<xsl:value-of select="@name"/> == null ? '' : '&lt;<xsl:value-of select="@name"/><xsl:if test="@targetNamespace"> xmlns="<xsl:value-of select="@targetNamespace"/>"</xsl:if>&gt;' + _<xsl:value-of select="@name"/> + '&lt;/<xsl:value-of select="@name"/>&gt;') +</xsl:otherwise>
                </xsl:choose>
            </xsl:for-each>
            '&lt;/p:<xsl:value-of select="signature/params/@wrapper-element"/>&gt;' ;</xsl:when>
        <xsl:when test="signature/params/param[@type = 'anyType' and @type-namespace='http://www.w3.org/2001/XMLSchema']">_<xsl:value-of select="signature/params/param/@name"/>;</xsl:when>
        <xsl:when test="not(signature/params/param)">null;</xsl:when>
        <xsl:otherwise>"&lt;<xsl:value-of select="@name"/>/&gt;";<!-- @@ bug - should be able to send an empty string... --></xsl:otherwise>
    </xsl:choose>
</xsl:template>

<xsl:template name="build-arrays">
    <xsl:for-each select="signature/params/param[not(@token='#any')][@maxOccurs = 'unbounded' or @maxOccurs > 1]">
        <xsl:variable name="name">
            <xsl:call-template name="xml-name-to-javascript-name">
                <xsl:with-param name="name" select="@name"/>
            </xsl:call-template>
        </xsl:variable>
        <xsl:if test="position() = 1">var i;
        </xsl:if>
        var _array_of_<xsl:value-of select="$name"/> = '';
        for (i=0; i &lt; _<xsl:value-of select="$name"/>.length; i++) {
            <xsl:choose>
                <xsl:when test="@type = 'date' and @type-namespace='http://www.w3.org/2001/XMLSchema'">_array_of_<xsl:value-of select="$name"/> += '&lt;<xsl:value-of select="@name"/><xsl:if test="@targetNamespace"> xmlns="<xsl:value-of select="@targetNamespace"/>"</xsl:if>&gt;' + (typeof(_<xsl:value-of select="@name"/>) == 'object' ? WebService.utils.toXSdate(_<xsl:value-of select="@name"/>) : _<xsl:value-of select="@name"/>) + '&lt;/<xsl:value-of select="@name"/>&gt;';</xsl:when>
                <xsl:when test="@type = 'time' and @type-namespace='http://www.w3.org/2001/XMLSchema'">_array_of_<xsl:value-of select="$name"/> += '&lt;<xsl:value-of select="@name"/><xsl:if test="@targetNamespace"> xmlns="<xsl:value-of select="@targetNamespace"/>"</xsl:if>&gt;' + (typeof(_<xsl:value-of select="@name"/>) == 'object' ? WebService.utils.toXStime(_<xsl:value-of select="@name"/>) : _<xsl:value-of select="@name"/>) + '&lt;/<xsl:value-of select="@name"/>&gt;';</xsl:when>
                <xsl:when test="@type = 'dateTime' and @type-namespace='http://www.w3.org/2001/XMLSchema'">_array_of_<xsl:value-of select="$name"/> += '&lt;<xsl:value-of select="@name"/><xsl:if test="@targetNamespace"> xmlns="<xsl:value-of select="@targetNamespace"/>"</xsl:if>&gt;' + (typeof(_<xsl:value-of select="@name"/>) == 'object' ? WebService.utils.toXSdateTime(_<xsl:value-of select="@name"/>) : _<xsl:value-of select="@name"/>) + '&lt;/<xsl:value-of select="@name"/>&gt;';</xsl:when>
                <xsl:when test="@type='QName' and @type-namespace='http://www.w3.org/2001/XMLSchema'">_array_of_<xsl:value-of select="$name"/> += '&lt;<xsl:value-of select="$name"/><xsl:if test="@targetNamespace"> xmlns="<xsl:value-of select="@targetNamespace"/>"</xsl:if>' + WebService.utils._QNameNamespaceDecl(_<xsl:value-of select="@name"/>[i]) + '&gt;' + WebService.utils._QNameValue(_<xsl:value-of select="@name"/>[i]) + '&lt;/<xsl:value-of select="$name"/>&gt;';</xsl:when>
                <xsl:otherwise>_array_of_<xsl:value-of select="$name"/> += '&lt;<xsl:value-of select="$name"/><xsl:if test="@targetNamespace"> xmlns="<xsl:value-of select="@targetNamespace"/>"</xsl:if>&gt;' + <xsl:if test="(@type = 'string' or @type = 'normalizedString' or @type = 'anyURI') and @type-namespace='http://www.w3.org/2001/XMLSchema'">this._encodeXML</xsl:if>(_<xsl:value-of select="$name"/>[i]) + '&lt;/<xsl:value-of select="$name"/>&gt;';</xsl:otherwise>
            </xsl:choose>
        }
    </xsl:for-each>
</xsl:template>

<xsl:template name="return-type">
    <xsl:text>/* </xsl:text>
    <xsl:if test="@maxOccurs = 'unbounded' or @maxOccurs > 1">array of </xsl:if>
    <xsl:call-template name="xml-name-to-javascript-name">
        <xsl:with-param name="name" select="@type"/>
    </xsl:call-template>
    <xsl:text> */</xsl:text>
</xsl:template>

<xsl:template name="service-name-to-javascript-name">
    <xsl:param name="name"/>
    <xsl:if test="contains(',abstract,APPClient,AtomClient,AtomFeed,boolean,break,byte,case,catch,char,class,const,continue,debugger,default,delete,do,double,else,Email,Entry,enum,export,extends,false,Feed,FeedReader,File,final,finally,float,for,function,goto,if,IM,implements,import,in,instanceof,int,interface,long,native,new,null,package,private,protected,public,return,Scraper,session,short,static,super,switch,synchronized,system,this,throw,throws,transient,true,try,typeof,var,void,volatile,WebServiceError,while,with,WSRequest,',concat(',',$name,','))">_</xsl:if>
    <xsl:value-of select="translate($name,'.-','__')"/>
</xsl:template>

<xsl:template name="operation-name-to-javascript-name">
    <xsl:param name="name"/>
    <xsl:if test="contains(',endpoint,getAddress,onreadystatechange,password,readyState,setAddress,username,',concat(',',$name,','))">_</xsl:if>
    <xsl:value-of select="translate($name,'.-','__')"/>
</xsl:template>

<xsl:template name="xml-name-to-javascript-name">
    <xsl:param name="name"/>
    <xsl:value-of select="translate($name,'.-','__')"/>
</xsl:template>

<xsl:template name="localnameify-url">
    <xsl:param name="url"/>
    <xsl:variable name="scheme" select="substring-before($url, '://')"/>
    <xsl:choose>
        <xsl:when test="$localhost-endpoints and ($scheme='http' or $scheme='https')">
            <xsl:value-of select="$scheme"/>
            <xsl:text>://localhost</xsl:text>
            <xsl:variable name="remainder" select="substring-after($url, concat($scheme,'://'))"/>
            <xsl:variable name="domain" select="substring-before($remainder,'/')"/>
            <xsl:if test="contains($domain,':')">
                <xsl:text>:</xsl:text>
                <xsl:value-of select="substring-after($domain, ':')"/>
            </xsl:if>
            <xsl:text>/</xsl:text>
            <xsl:value-of select="substring-after($remainder, '/')"/>
        </xsl:when>
        <xsl:otherwise>
            <xsl:value-of select="$url"/>
        </xsl:otherwise>
    </xsl:choose>
</xsl:template>

</xsl:stylesheet>
