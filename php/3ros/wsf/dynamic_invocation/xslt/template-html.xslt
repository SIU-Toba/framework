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

    <xsl:output method="html" indent="yes"/>

    <!-- Toggle between DOM and E4X treatment of XML objects. -->
    <xsl:param name="e4x" select="false()"/>

    <!-- This stylesheet only supports a single service at a time.
         If no service name is specified in this parameter, the first one is used.  -->
    <xsl:param name="service" select="services/service[1]/@name"/>

    <!-- Paths to external resources can be specified here. -->
    <xsl:param name="wsrequest-location" select="'../../../js/wso2/WSRequest.js'"/>
    <xsl:param name="stub-location" />

    <xsl:variable name="service-name">
        <xsl:call-template name="service-name-to-javascript-name">
            <xsl:with-param name="name" select="$service"/>
        </xsl:call-template>
    </xsl:variable>

<xsl:template match="/">
    <xsl:apply-templates select="services/service[@name=$service][1]"/>
</xsl:template>

<xsl:template match="service">
    <xsl:comment>
  ~ Copyright 2005-2008 WSO2, Inc. http://www.wso2.org
  ~
  ~ Licensed under the Apache License, Version 2.0 (the "License");
  ~ you may not use this file except in compliance with the License.
  ~ You may obtain a copy of the License at
  ~
  ~ http://www.apache.org/licenses/LICENSE-2.0
  ~
  ~ Unless required by applicable law or agreed to in writing, software
  ~ distributed under the License is distributed on an "AS IS" BASIS,
  ~ WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
  ~ See the License for the specific language governing permissions and
  ~ limitations under the License.
</xsl:comment>
    <xsl:text>&#10;&#10;</xsl:text>
    <html>
        <!-- Calculate the source of the stub, including whether it's e4x or not -->
        <xsl:variable name="e4x-param">
            <xsl:if test="$e4x">; e4x=1</xsl:if>
        </xsl:variable>
        <xsl:variable name="src">
            <xsl:choose>
                <xsl:when test="$stub-location"><xsl:value-of select="$stub-location"/></xsl:when>
                <xsl:otherwise><xsl:value-of select="$service"/>?stub</xsl:otherwise>
            </xsl:choose>
            <xsl:if test="$e4x">&amp;lang=e4x</xsl:if>
        </xsl:variable>
        <head>
            <title><xsl:value-of select="$service"/> - powered by the WSO2 Mashup Server</title>
<style type="text/css">
    body {font: 75%/1.5 "Lucida Grande","Lucida Sans","Microsoft Sans Serif", "Lucida Sans Unicode",verdana,sans-serif,"trebuchet ms"; color: #111; }
    #result-console {border:1px solid black; padding:1em;}
    #error-console {color:red; font-weight:bold}
    #footer {text-align:center; font-size:75%}
</style>
            <script type="text/javascript" src="{$wsrequest-location}"></script>
            <xsl:text>&#10;</xsl:text>
            <script type="text/javascript{$e4x-param}" src="{$src}"></script>
            <xsl:text>&#10;</xsl:text>
        <xsl:call-template name="localscript"/>
        </head>
        <body onload='init()'>
            <h1><xsl:value-of select="$service"/></h1>
            <div id="result-console">
                <xsl:comment> This div will contain the text returned as response to the service call. </xsl:comment>
            </div>
            <div id="error-console">
                <xsl:comment> This div will contain a description of any errors encountered. </xsl:comment>
            </div>
            <div id="footer">
                <div>Powered by <a href="http://wso2.org/projects/mashup">WSO2 Mashup Server</a>.</div>
                <div>Access all the operations of the <xsl:value-of select="$service"/> service through the <a href="../{substring-before($src, '?')}?tryit">Try-it page</a>.</div>
            </div>
        </body>
    </html>
</xsl:template>

<xsl:template name="localscript">
<script type="text/javascript" language="javascript">
    var browser = WSRequest.util._getBrowser();

    // Demonstrates calling an operation of the '<xsl:value-of select="$service"/>' Mashup
    function init() {
        <xsl:apply-templates select="operations/operation[1]"/>
    }

    // Sample invocations (unused) for other operations.
    function samples() {
        <xsl:apply-templates select="operations/operation[position() > 1]"/>
    }

    // Handles and error by displaying the reason in a dialog
    function showPayload(payload) {
        if (typeof(payload) == "object" &amp;&amp; payload.nodeType != undefined) {
            payload = "<pre>" + WebService.utils._serializeXML(payload) + "</pre>";
        }
        log ("result-console", payload);
    }

    function handleError(error) {
        if (typeof(error.detail) == "object" &amp;&amp; error.detail.nodeType != undefined) {
            error.detail = "<pre>" + WebService.utils._serializeXML(error.detail) + "</pre>";
        }
        log ("error-console", "Fault: " + error.reason + "\n\n" + error.detail);
    }

    function log(consoleName, data) {
        var console = document.getElementById(consoleName);
        <xsl:if test="$e4x">if (typeof(data) == "xml") data = data.toXMLString();
        </xsl:if>if (browser == "ie" || browser == "ie7") console.innerText = data;
        else console.textContent = data;
    }
</script>
</xsl:template>


<xsl:template match="operation">
    <xsl:variable name="original-operation-name" select="@name"/>
    <xsl:variable name="operation-name"><!-- operation name -->
        <xsl:call-template name="operation-name-to-javascript-name">
            <xsl:with-param name="name" select="@name"/>
        </xsl:call-template>
    </xsl:variable>
        // Set up a callback and an error handler for the <xsl:value-of select="$original-operation-name"/> operation.
        <xsl:value-of select="$service-name"/>.<xsl:value-of select="$operation-name"/>.callback = showPayload;
        <xsl:value-of select="$service-name"/>.<xsl:value-of select="$operation-name"/>.onError = handleError;

        // Invoke the operation/method.  Since there is a callback defined, the call is asynchronous.
        // NOTE!  The parameter values below are generated automatically based on the schema types, and are not guaranteed to be
        //        meaningful values when invoking the service.  You should insert meaningful values instead.
        <xsl:value-of select="$service-name"/>.<xsl:value-of select="$operation-name"/>(<xsl:for-each select="signature/params/param">
            <xsl:apply-templates select="." mode="literal"/>
            <xsl:if test="position() != last()">, </xsl:if>
        </xsl:for-each>);
</xsl:template>

<!-- generate a literal value for the parameter type -->
<xsl:template match="param" mode="literal">
    <xsl:text>/* (</xsl:text>
    <xsl:if test="@maxOccurs = 'unbounded' or @maxOccurs > 1">array of </xsl:if>
    <xsl:call-template name="xml-name-to-javascript-name">
        <xsl:with-param name="name" select="@type"/>
    </xsl:call-template>
    <xsl:if test="@minOccurs = 0">?</xsl:if>
    <xsl:text>) </xsl:text>
    <xsl:value-of select="@name"/>
    <xsl:text> */ </xsl:text>
    <xsl:if test="@maxOccurs = 'unbounded' or @maxOccurs > 1">new Array(</xsl:if>
    <xsl:choose>
        <xsl:when test="@minOccurs = 0">null</xsl:when>
        <xsl:when test="@type-namespace != 'http://www.w3.org/2001/XMLSchema' and @simple='yes' and enumeration">"<xsl:value-of select="enumeration[1]/@value"/>"</xsl:when>
        <xsl:when test="@type-namespace != 'http://www.w3.org/2001/XMLSchema' and @simple='yes'">"(not a schema type)"</xsl:when>
        <xsl:when test="@type-namespace != 'http://www.w3.org/2001/XMLSchema' and $e4x">&lt;<xsl:value-of select="@name"/> xmlns='<xsl:value-of
                select="@type-namespace"/>'&gt;(see content model for this type)&lt;/<xsl:value-of select="@name"/>&gt;</xsl:when>
        <xsl:when test="@type-namespace != 'http://www.w3.org/2001/XMLSchema'">"&lt;<xsl:value-of select="@name"/> xmlns='<xsl:value-of
                select="@type-namespace"/>'&gt;(see content model for this type)&lt;/<xsl:value-of select="@name"/>&gt;"</xsl:when>
        <xsl:when test="@type = 'string' or
                        @type = 'normalizedString' or
                        @type = 'token'
                       ">"a<xsl:value-of select="@type"/>Value"</xsl:when>
        <xsl:when test="@type = 'language'">"en-US"</xsl:when>
        <xsl:when test="@type = 'Name' or
                        @type = 'NCName' or
                        @type = 'NMTOKEN'">"my<xsl:value-of select="@type"/>"</xsl:when>
        <xsl:when test="@type = 'NMTOKENS'">new Array("myName")</xsl:when>
        <xsl:when test="@type = 'ID' or
                        @type = 'IDREF'">"my<xsl:value-of select="@type"/>"</xsl:when>
        <xsl:when test="@type = 'IDREFS'">new Array("myId")</xsl:when>
        <xsl:when test="@type = 'ENTITY'">"anENTITY"</xsl:when>
        <xsl:when test="@type = 'ENTITIES'">new Array("anENTITY")</xsl:when>
        <xsl:when test="@type = 'NOTATION'">"aNOTATION"</xsl:when>
        <xsl:when test="@type = 'anyURI'">"http://wso2.org/projects/mashup"</xsl:when>
        <xsl:when test="@type = 'hexBinary'">"57534f32204d617368757020536572766572"</xsl:when>
        <xsl:when test="@type = 'base64Binary'">"V1NPMiBNYXNodXAgU2VydmVy"</xsl:when>
        <xsl:when test="@type = 'decimal'">"1.2345"</xsl:when>
        <xsl:when test="@type = 'float' or
                        @type = 'double'">1.2345</xsl:when>
        <xsl:when test="@type = 'boolean'">true</xsl:when>
        <xsl:when test="@type = 'integer' or
                        @type = 'nonNegativeInteger' or
                        @type = 'positiveInteger'">7</xsl:when>
        <xsl:when test="@type = 'nonPositiveInteger' or
                        @type = 'negativeInteger'">-7</xsl:when>
        <xsl:when test="@type = 'long' or
                        @type = 'unsignedLong'">123456789</xsl:when>
        <xsl:when test="@type = 'int' or
                        @type = 'unsignedInt'">123456</xsl:when>
        <xsl:when test="@type = 'short' or
                        @type = 'unsignedShort'">1234</xsl:when>
        <xsl:when test="@type = 'byte' or
                        @type = 'unsignedByte'">12</xsl:when>
        <xsl:when test="@type = 'duration'">"P1Y2M3DT10H30M"</xsl:when>
        <xsl:when test="@type = 'gYear'">"2008"</xsl:when>
        <xsl:when test="@type = 'gMonth'">"--01--"</xsl:when>
        <xsl:when test="@type = 'gDay'">"---01"</xsl:when>
        <xsl:when test="@type = 'gMonthDay'">"--01-01"</xsl:when>
        <xsl:when test="@type = 'gYearMonth'">"2008-01"</xsl:when>
        <xsl:when test="@type = 'date'">"2008-01-01"</xsl:when>
        <xsl:when test="@type = 'dateTime'">"2008-01-01T06:00:00"</xsl:when>
        <xsl:when test="@type = 'time'">"06:00:00"</xsl:when>
        <xsl:when test="@type = 'QName' and $e4x">new QName("http://www.w2.org/2001/XMLSchema", "QName")</xsl:when>
        <xsl:when test="@type = 'QName'">{ "uri" : "http://www.w2.org/2001/XMLSchema",  "localName" : "QName" }</xsl:when>
        <xsl:when test="@type = 'anyType' and $e4x">&lt;sample&gt;e4x xml content&lt;/sample&gt;</xsl:when>
        <xsl:when test="@type = 'anyType'">"&lt;sample&gt;xml content&lt;/sample&gt;"</xsl:when>
        <xsl:otherwise>"Warning!  Illegal XML Schema data type."</xsl:otherwise>
    </xsl:choose>
    <xsl:if test="@maxOccurs = 'unbounded' or @maxOccurs > 1"> /* add more values here... */)</xsl:if>
</xsl:template>

<!-- do some simple name mapping, replacing '.' and '-' with '_' -->
<xsl:template name="service-name-to-javascript-name">
    <xsl:param name="name"/>
    <xsl:if test="contains(',break,else,new,var,case,finally,return,void,catch,for,switch,while,continue,function,this,with,default,if,throw,delete,in,try,do,instanceof,typeof,abstract,enum,int,short,boolean,export,interface,static,byte,extends,long,super,char,final,native,synchronized,class,float,package,throws,const,goto,private,transient,debugger,implements,protected,volatile,double,import,public,null,true,false,',concat(',',$name,','))">_</xsl:if>
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
    
</xsl:stylesheet>


