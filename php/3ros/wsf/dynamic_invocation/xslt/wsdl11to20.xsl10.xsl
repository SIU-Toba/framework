<?xml version="1.0" encoding="utf-8"?>
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
<!-- 
    Converted from http://www.w3.org/2006/02/wsdl11to20.xsl to use XSLT 1.0
    by Jonathan Marsh, jonathan@wso2.com, 10 Sep 2007
-->
<xsl:transform version="1.0" xmlns="http://www.w3.org/ns/wsdl"
    xmlns:w11="http://schemas.xmlsoap.org/wsdl/"
    xmlns:w11soap="http://schemas.xmlsoap.org/wsdl/soap/"
    xmlns:w12soap="http://schemas.xmlsoap.org/wsdl/soap12/"
    xmlns:w11http="http://schemas.xmlsoap.org/wsdl/http/"
    xmlns:w11mime="http://schemas.xmlsoap.org/wsdl/mime/"
    xmlns:soapenc11="http://schemas.xmlsoap.org/soap/encoding/"
    xmlns:soap11="http://schemas.xmlsoap.org/soap/envelope/"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:wsdli="http://www.w3.org/ns/wsdl-instance"
    xmlns:wsdlx="http://www.w3.org/ns/wsdl-extensions" xmlns:wrpc="http://www.w3.org/ns/wsdl/rpc"
    xmlns:wsoap="http://www.w3.org/ns/wsdl/soap" xmlns:whttp="http://www.w3.org/ns/wsdl/http"
    xmlns:wsaw="http://www.w3.org/2006/05/addressing/wsdl"
    exclude-result-prefixes="w11 w11soap w12soap w11http w11mime soap11 soapenc11 xs xsi wsoap whttp wrpc wsdli wsdlx">
    
    <xsl:strip-space elements="*"/>
    
    <xsl:output method="xml" media-type="application/xhtml+xml" encoding="utf-8" indent="yes"/>
        
    <xsl:template match="/">
        <xsl:apply-templates select="w11:definitions"/>
    </xsl:template>
    
    <xsl:template name="converter-doc">
        <xsl:text>
    </xsl:text>
        <xsl:comment> 
        Converted by wsdl11to20.xsl10.xsl.
        See https://wso2.org/repos/wso2/trunk/commons/dynamic-codegen/src/wsdl11to20.xsl10.xsl

        This is an XSLT 1.0-compatible port of Hugo's converter which can be found at:
        http://esw.w3.org/topic/WsdlConverter
    </xsl:comment>
        <xsl:text>
    </xsl:text>
    </xsl:template>
    
    <xsl:variable name="type" select="/w11:definitions/w11:binding[1]"/>
    <xsl:variable name="qname_prefix" select="substring-before($type/@type,':')"/>
    <xsl:variable name="qname_local-name">
        <xsl:choose>
            <xsl:when test="contains($type/@type,':')">
                <xsl:value-of select="substring-after($type/@type,':')"/>
            </xsl:when>
            <xsl:otherwise>
                <xsl:value-of select="$type/@type"/>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:variable>        
    <xsl:variable name="qname_namespace-uri" select="$type/namespace::*[local-name()=$qname_prefix]"/>
    
    <xsl:template match="w11:definitions">
        <description targetNamespace="{@targetNamespace}">
            <!-- copy all the namespaces -->
            <xsl:copy-of select="namespace::*"/>
            <!-- creating namespace declarations -->
            <!--<xsl:copy-of select="namespace::*[local-name() = $qname_prefix]"/>--><!-- no namespace remap -->

            <xsl:call-template name="converter-doc"/>
            
            <xsl:apply-templates select="w11:documentation"/>
            
            <xsl:choose>
                <xsl:when test="not(w11:types)">
                    <types>
                        <xsl:apply-templates select="/w11:definitions/w11:import" mode="types"/>
                        <xsl:apply-templates select="//w11soap:body | //w12soap:body" mode="rpctypes"/>
                        <xsl:call-template name="httpUrlReplacementSchemaDecl"/>
                    </types>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:apply-templates select="w11:types"/>
                </xsl:otherwise>
            </xsl:choose>
            
            <xsl:apply-templates select="*[not( ( local-name()='import'
                                                  or local-name()='documentation'
                                                  or local-name()='types')
                                                and namespace-uri()='http://schemas.xmlsoap.org/wsdl/')
                                          ]"/>
        </description>
    </xsl:template>

    <xsl:template name="resolve-soaprpc-element-localname">
        <xsl:param name="msg"/>
        <xsl:choose>
            <xsl:when test="local-name($msg) = 'input'">
                <xsl:value-of select="$msg/../@name"/>
            </xsl:when>
            <xsl:when test="local-name($msg) = 'output'">
                <!-- @@@ Not 100% sure about this one -->
                <xsl:value-of select="concat(../../@name, 'Response')"/>
            </xsl:when>
            <xsl:otherwise>
                <!-- @@@ I don't think that we can do anything for faults -->
                <xsl:value-of select="'#any'"/>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    
    <xsl:template match="w11soap:body | w12soap:body" mode="rpctypes">
        <xsl:variable name="soapbody" select="."/>
        
        <xsl:if test="$soapbody/../../w11soap:operation/@style = 'rpc' or 
                      $soapbody/../../w12soap:operation/@style = 'rpc' or 
                      $soapbody/../../../w11soap:binding/@style = 'rpc' or
                      $soapbody/../../../w12soap:binding/@style = 'rpc'">
            <xsl:variable name="portType_name" select="substring-after(ancestor::w11:binding/@type,':')"/>
            <xsl:variable name="operation_name" select="ancestor::w11:operation/@name"/>
            <xsl:variable name="bound_operation" select="/w11:definitions/w11:portType[@name = $portType_name]/w11:operation[@name = $operation_name]"/>
            <xsl:variable name="message_name" select="$bound_operation/*[local-name() = local-name(current()/..)]/@message"/>
            <xsl:variable name="message_name_prefix" select="substring-before($message_name,':')"/>
            <xsl:variable name="message_name_local-name">
                <xsl:choose>
                    <xsl:when test="contains($message_name,':')">
                        <xsl:value-of select="substring-after($message_name,':')"/>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:value-of select="$message_name"/>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:variable>        
            <xsl:variable name="message_name_namespace-uri" select="../namespace::*[local-name()=$message_name_prefix]"/>

            <xsl:variable name="message"
                select="/w11:definitions/w11:message[/w11:definitions/@targetNamespace =    $message_name_namespace-uri and @name = $message_name_local-name]"/>
            <xsl:variable name="parts" select="$soapbody/@parts"/>
            <xsl:variable name="usestypes">
                <xsl:call-template name="message-is-using-types">
                    <xsl:with-param name="parts" select="$parts"/>
                    <xsl:with-param name="message" select="$message"/>
                </xsl:call-template>
            </xsl:variable>

            <xsl:variable name="localName">
                <xsl:call-template name="resolve-soaprpc-element-localname">
                    <xsl:with-param name="msg" select=".."/>
                </xsl:call-template>
            </xsl:variable>

            <xsl:if test="$localName != '#any'">
                <xs:schema targetNamespace="{@namespace}">
                    <xs:element name="{$localName}">
                        <xs:complexType>
                            <xs:sequence>
                                <xsl:choose>
                                
                                    <xsl:when test="$usestypes='yes'">
                                        <xsl:for-each select="$message/w11:part[not($parts) or contains($parts,concat(' ',@name,' '))]">
                                            <xsl:variable name="type_prefix" select="substring-before(@type,':')"/>
                                            <xsl:variable name="type_local-name">
                                                <xsl:choose>
                                                    <xsl:when test="contains(@type,':')">
                                                        <xsl:value-of select="substring-after(@type,':')"/>
                                                    </xsl:when>
                                                    <xsl:otherwise>
                                                        <xsl:value-of select="@type"/>
                                                    </xsl:otherwise>
                                                </xsl:choose>
                                            </xsl:variable>        
                                            <xsl:variable name="type_namespace-uri" select="namespace::*[local-name()=$type_prefix]"/>
                                            <xsl:variable name="type_prefix-separator">
                                                <xsl:if test="$type_prefix != ''">:</xsl:if>
                                            </xsl:variable>
                                            
                                            <xs:element name="{@name}" type="{$type_prefix}{$type_prefix-separator}{$type_local-name}">
                                                <xsl:copy-of select="namespace::*[local-name()=$type_prefix]"/>                        </xs:element>
                                        </xsl:for-each>
                                    </xsl:when>
                                    <xsl:otherwise><!--TODO check -->
                                        <xsl:for-each select="$message/w11:part[not($parts) or contains($parts,concat(' ',@name,' '))]">
                                            <xsl:variable name="element_prefix" select="substring-before(@element,':')"/>
                                            <xsl:variable name="element_local-name">
                                                <xsl:choose>
                                                    <xsl:when test="contains(@element,':')">
                                                        <xsl:value-of select="substring-after(@element,':')"/>
                                                    </xsl:when>
                                                    <xsl:otherwise>
                                                        <xsl:value-of select="@element"/>
                                                    </xsl:otherwise>
                                                </xsl:choose>
                                            </xsl:variable>        
                                            <xsl:variable name="element_namespace-uri" select="namespace::*[local-name()=$element_prefix]"/>
                                            <xsl:variable name="element_prefix-separator">
                                                <xsl:if test="$element_prefix != ''">:</xsl:if>
                                            </xsl:variable>
                                            <xs:element ref="{$element_prefix}{$element_prefix-separator}{$element_local-name}">
                                                <xsl:copy-of select="namespace::*[local-name()=$element_prefix]"/><!-- no namespace remap -->
                                            </xs:element>
                                        </xsl:for-each>
                                    </xsl:otherwise>
                                </xsl:choose>
                            </xs:sequence>
                        </xs:complexType>
                    </xs:element>
                </xs:schema>
            </xsl:if>
        </xsl:if>
    </xsl:template>
    
    <xsl:template match="w11:definitions/w11:import">
        <xsl:choose>
            <xsl:when test="@location">
                <xsl:variable name="nodes" select="document(@location)"/>
                <xsl:choose>
                    <xsl:when test="$nodes/w11:definitions">
                        <import namespace="{@namespace}" location="{@location}"/>
                    </xsl:when>
                    <xsl:when test="$nodes/xs:schema">
                        <!-- move to wsdl20:description/wsdl20:types
                             see match=wsdl20:description/wsdl20:types
                             and match=wsdl20:description/wsdl20:portType-->
                    </xsl:when>
                    <xsl:when test="count($nodes) = 0">
                        <import namespace="{@namespace}" location="{@location}"/>
                    </xsl:when>
                    <xsl:otherwise>
                        <!-- No WSDL 2.0 components found at <xsl:value-of
                             select='@location' /> in namespace <xsl:value-of
                             select='@namespace' /> -->
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:when>
            <xsl:otherwise>
                <import namespace="{@namespace}"/>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    
    <xsl:template match="w11:definitions/w11:import" mode="types">
        <!-- check if an WSDL 1.1 import was about XSD -->
        <xsl:if test="@location and document(@location)/xs:schema">
            <!-- move to wsdl20:description/wsdl20:types -->
            <xs:import namespace="{@namespace}" schemaLocation="{@location}"/>
        </xsl:if>
    </xsl:template>
    
    <xsl:template match="w11:types">
        <types>
            <xsl:apply-templates select="/w11:definitions/w11:import" mode="types"/>
            <xsl:apply-templates select="xs:schema"/>
            <xsl:apply-templates select="*[not(local-name()='schema' and namespace-uri()='http://www.w3.org/2001/XMLSchema')]"/>
            <xsl:apply-templates select="//w11soap:body | //w12soap:body" mode="rpctypes"/>
            <xsl:call-template name="httpUrlReplacementSchemaDecl"/>
        </types>
    </xsl:template>
    
    <xsl:template name="httpUrlReplacementSchemaDecl">
        <!-- Convert message parts with using types that are used in an HTTP binding -->
        <!-- FIXME: currently only works with input; how about output? -->
        <xsl:for-each select="//w11:message">
            <xsl:variable name="themessage" select="."/>
            <xsl:variable name="message_name_local-name" select="@name"/>
            <xsl:variable name="message_name_namespace-uri" select="/w11:definitions/@targetNamespace"/>
            
            <xsl:for-each select="//w11:portType/w11:operation">
                <xsl:variable name="operation_prefix" select="substring-before(w11:input/@message,':')"/>
                <xsl:variable name="operation_local-name">
                    <xsl:choose>
                        <xsl:when test="contains(w11:input/@message,':')">
                            <xsl:value-of select="substring-after(w11:input/@message,':')"/>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:value-of select="w11:input/@message"/>
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:variable>        
                <xsl:variable name="operation_namespace-uri" select="namespace::*[local-name()=$operation_prefix]"/>
                <xsl:if test="$operation_namespace-uri = $message_name_namespace-uri and $operation_local-name = $message_name_local-name">
                    <xsl:variable name="operation_name" select="@name"/>
                    <xsl:variable name="porttype_name_local-name" select="../@name"/>
                    <xsl:variable name="porttype_name_namespace-uri" select="/w11:definitions/@targetNamespace"/>
                    <xsl:variable name="bound_to_http">
                        <xsl:for-each select="//w11:binding">
                            <xsl:variable name="binding_prefix" select="substring-before(@type,':')"/>
                            <xsl:variable name="binding_local-name">
                                <xsl:choose>
                                    <xsl:when test="contains(@type,':')">
                                        <xsl:value-of select="substring-after(@type,':')"/>
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <xsl:value-of select="@type"/>
                                    </xsl:otherwise>
                                </xsl:choose>
                            </xsl:variable>        
                            <xsl:variable name="binding_namespace-uri" select="namespace::*[local-name()=$binding_prefix]"/>
                            <xsl:if test="$binding_namespace-uri = $porttype_name_namespace-uri and $binding_local-name = $porttype_name_local-name">
                                <xsl:for-each select="w11:operation[@name = $operation_name]">
                                    <xsl:if test="../w11http:binding">
                                        <xsl:text>y</xsl:text>
                                    </xsl:if>
                                </xsl:for-each>
                            </xsl:if>
                        </xsl:for-each>
                    </xsl:variable>
                    <xsl:if test="contains($bound_to_http, 'y')">
                        <xs:schema targetNamespace="{concat(/w11:definitions/@targetNamespace,
                            'GEN')}">
                            <xs:documentation>
                                The following is made up by the translation script.
                                It's not clear how well this is going to work.                             </xs:documentation>
                            <xs:element name="{$operation_name}">
                                <xs:complexType>
                                    <xsl:for-each select="$themessage/w11:part">
                                        <xs:element name="{@name}" type="{@type}"/>
                                    </xsl:for-each>
                                </xs:complexType>
                            </xs:element>
                        </xs:schema>
                    </xsl:if>
                </xsl:if>
            </xsl:for-each>
        </xsl:for-each>
    </xsl:template>
    
    <xsl:template match="w11:message">
        <!--    <xsl:comment>MESSAGE:<xsl:value-of select='@name' /></xsl:comment> -->
    </xsl:template>
    
    <xsl:template match="w11:portType">
        <interface name="{@name}">
            <xsl:apply-templates select="w11:documentation"/>
            <xsl:apply-templates select="*[not(local-name()='documentation'
                and namespace-uri()='http://schemas.xmlsoap.org/wsdl/')]"/>
        </interface>
    </xsl:template>
    
    <xsl:template match="w11:portType/w11:operation">
        <xsl:apply-templates select="w11:fault"/>
        <xsl:variable name="name" select="@name"/>
        <operation name="{$name}">
            <xsl:variable name="ios" select="*[(local-name()='input' or local-name()='output' or local-name()='fault')  and namespace-uri()='http://schemas.xmlsoap.org/wsdl/']"/>
            <!-- pattern is not optional in WSDL 2.0 -->
            <xsl:variable name="pattern">
                <xsl:choose>
                    <xsl:when test="count($ios) = 1 and $ios[self::w11:input]">
                        <!-- One-Way Operation -->
                        <xsl:text>http://www.w3.org/ns/wsdl/in-only</xsl:text>
                    </xsl:when>
                    <xsl:when test="count($ios) = 2 and $ios[self::w11:input] and $ios[self::w11:fault]">
                        <xsl:text>http://www.w3.org/ns/wsdl/robust-in-only</xsl:text>
                    </xsl:when>
                    <xsl:when test="count($ios) = 1 and $ios[self::w11:output]">
                        <!-- Notification Operation -->
                        <xsl:text>http://www.w3.org/ns/wsdl/out-only</xsl:text>
                    </xsl:when>
                    <xsl:when test="count($ios) = 2 and $ios[self::w11:output] and $ios[self::w11:fault]">
                        <xsl:text>http://www.w3.org/ns/wsdl/robust-out-only</xsl:text>
                    </xsl:when>
                    <xsl:when test="count($ios[self::w11:input]) = 1 and count($ios[self::w11:output]) = 1 and $ios[self::w11:output[preceding-sibling::w11:input]]">
                        <!-- Request-Response Operation -->
                        <xsl:text>http://www.w3.org/ns/wsdl/in-out</xsl:text>
                    </xsl:when>
                    <xsl:when test="count($ios[self::w11:output]) = 1 and count($ios[self::w11:input]) = 1 and $ios[self::w11:input[preceding-sibling::w11:output]]">
                        <!-- Solicit-Response Operation -->
                        <xsl:text>http://www.w3.org/ns/wsdl/out-in</xsl:text>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:text>http://www.w3.org/2006/02/undefined</xsl:text>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:variable>
            <xsl:attribute name="pattern">
                <xsl:value-of select="$pattern"/>
            </xsl:attribute>
            
            <xsl:if test="count(../w11:operation[@name=$name]) > 1">
                <!-- WSDL 1.1 operation/@name are unique according to
                     the WS-I Basic profile but input/output are -->
                <documentation>
                    ERROR: duplicate name for the operation
                </documentation>
            </xsl:if>
            
            <xsl:apply-templates select="w11:documentation"/>
            
            <xsl:for-each select="$ios">
                <xsl:choose>
                    <xsl:when test="self::w11:input or self::w11:output">
                        <xsl:apply-templates select="."/>
                    </xsl:when>
                    <xsl:when test="self::w11:fault">
                        <xsl:choose>
                            <xsl:when test="$pattern = 'http://www.w3.org/ns/wsdl/out-in' or $pattern = 'http://www.w3.org/ns/wsdl/out-opt-in'">
                                <xsl:apply-templates select="." mode="infault"/>
                            </xsl:when>
                            <xsl:otherwise>
                                <xsl:apply-templates select="." mode="outfault"/>
                            </xsl:otherwise>
                        </xsl:choose>
                    </xsl:when>
                </xsl:choose>
            </xsl:for-each>
        </operation>
    </xsl:template>
    
    <xsl:template match="w11:portType/w11:operation/w11:input
                        | w11:portType/w11:operation/w11:output
                        | w11:portType/w11:operation/w11:fault">
        <xsl:element name="{local-name()}" namespace="http://www.w3.org/ns/wsdl">
            <xsl:call-template name="resolve-elementType"/>
        </xsl:element>
    </xsl:template>

    <xsl:template match="w11:portType/w11:operation/w11:input">
        <input>
            <xsl:call-template name="resolve-elementType"/>
        </input>
    </xsl:template>

    <xsl:template match="w11:portType/w11:operation/w11:output">
        <output>
            <xsl:call-template name="resolve-elementType"/>
        </output>
    </xsl:template>

    <xsl:template match="w11:portType/w11:operation/w11:fault">
        <fault>
            <xsl:call-template name="resolve-elementType"/>
        </fault>
    </xsl:template>
    
    <xsl:template match="w11:fault" mode="infault">
        <xsl:variable name="prefix">
            <xsl:value-of select="$qname_prefix"/>
        </xsl:variable>
        <infault ref="{$prefix}:{@name}"/>
    </xsl:template>

    <xsl:template match="w11:fault" mode="outfault">
        <xsl:variable name="prefix">
            <xsl:value-of select="$qname_prefix"/>
        </xsl:variable>
        <outfault ref="{$prefix}:{@name}"/>
    </xsl:template>
    
    <xsl:template match="w11:fault" mode="binding">
        <xsl:variable name="prefix">
            <xsl:value-of select="$qname_prefix"/>
        </xsl:variable>
        <fault ref="{$prefix}:{@name}"/>
    </xsl:template>
    
    <xsl:template name="resolve-elementType-attrs">
        <!-- This template is called by resolve-elementType to set attribute values -->
        <xsl:param name="element"/>
        <xsl:param name="namespace"/>
        <xsl:param name="faultname"/>
        
        <xsl:choose>
            <xsl:when test="self::w11:fault">
                <xsl:attribute name="name">
                    <xsl:value-of select="$faultname"/>
                </xsl:attribute>
                <xsl:attribute name="element">
                    <xsl:value-of select="$element"/>
                </xsl:attribute>
            </xsl:when>
            <xsl:otherwise>
                <xsl:variable name="prefix">
                    <xsl:value-of select="local-name(namespace::*[.=$namespace])"/>
                </xsl:variable>
                <xsl:attribute name="element">
                    <xsl:value-of select="$prefix"/>
                    <xsl:text>:</xsl:text>
                    <xsl:value-of select="$element"/>
                </xsl:attribute>
                <xsl:copy-of select="namespace::*[.=$namespace]"/>
            </xsl:otherwise>
        </xsl:choose>
        <xsl:copy-of select="@wsaw:Action"/>
    </xsl:template>
    
    <xsl:template name="message-is-using-types">
        <xsl:param name="parts"/>
        <xsl:param name="message"/>
        <xsl:param name="first-part" select="substring-before($parts, ' ')"/>
        <xsl:choose>
            <!-- omitted parts attribute -->
            <xsl:when test="not($parts)">
                <xsl:choose>
                    <xsl:when test="$message/w11:part/@type and not($message/w11:part/@element)">yes</xsl:when>
                    <xsl:otherwise>no</xsl:otherwise>
                </xsl:choose>
            </xsl:when>
            <xsl:when test="$first-part != ''">
                <xsl:variable name="remaining-parts-use-types">
                    <xsl:call-template name="message-is-using-types">
                        <xsl:with-param name="parts" select="$parts"/>
                        <xsl:with-param name="message" select="$message"/>
                        <xsl:with-param name="first-part" select="substring-after($parts, ' ')"/>
                    </xsl:call-template>
                </xsl:variable>
                <xsl:choose>
                    <xsl:when test="$remaining-parts-use-types = 'no'">no</xsl:when>
                    <xsl:otherwise>
                        <xsl:variable name="bodypart" select="$message/w11:part[@name =
                            $first-part]"/>
                        <xsl:choose>
                            <xsl:when test="$bodypart/@type and not($bodypart/@element)">yes</xsl:when>
                            <xsl:otherwise>no</xsl:otherwise>
                        </xsl:choose>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:when>
        </xsl:choose>
    </xsl:template>
    
    <xsl:template name="resolve-elementType">
        <xsl:variable name="message_name_prefix" select="substring-before(@message,':')"/>
        <xsl:variable name="message_name_local-name">
            <xsl:choose>
                <xsl:when test="contains(@message,':')">
                    <xsl:value-of select="substring-after(@message,':')"/>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:value-of select="@message"/>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:variable>        
        <xsl:variable name="message_name_namespace-uri" select="string(namespace::*[local-name()=$message_name_prefix])"/>
        <xsl:variable name="message"
            select="/w11:definitions/w11:message[/w11:definitions/@targetNamespace =
            $message_name_namespace-uri and @name = $message_name_local-name]"/>                    
        <xsl:variable name="portType_namespace-uri" select="/w11:definitions/@targetNamespace"/>
        <xsl:variable name="portType_local-name" select="../../@name"/>

        <xsl:variable name="operation_name" select="../@name"/>
        <xsl:variable name="bound_operation" select="//w11:binding[@type = $portType_local-name or contains(concat(':',@type,':'),concat($portType_local-name,':')) ]/w11:operation[@name=$operation_name]"/>
        <!-- FIXME: this has a good chance of breaking if the message is bound more than once -->
        <!-- FIXME: Only running tests on one operation  -->

        <!-- FIXME: currently mime bodies are also taken as regular bodies -->
        <xsl:variable name="soapbody"
            select="$bound_operation/w11:input[not(@message) or @message=$message_name_local-name]/w11soap:body | 
                    $bound_operation/w11:output[not(@message) or @message=$message_name_local-name]/w11soap:body | 
                    $bound_operation/w11:input[not(@message) or @message=$message_name_local-name]/w12soap:body | 
                    $bound_operation/w11:output[not(@message) or @message=$message_name_local-name]/w12soap:body |
                    $bound_operation/w11:input[(not(@message) or @message=$message_name_local-name) and w11mime:multipartRelated]/w11mime:multipartRelated/w11mime:part/w11soap:body |
                    $bound_operation/w11:output[(not(@message) or @message=$message_name_local-name) and w11mime:multipartRelated]/w11mime:multipartRelated/w11mime:part/w11soap:body |
                    $bound_operation/w11:input[(not(@message) or @message=$message_name_local-name) and w11mime:multipartRelated]/w11mime:multipartRelated/w11mime:part/w12soap:body |
                    $bound_operation/w11:output[(not(@message) or @message=$message_name_local-name) and w11mime:multipartRelated]/w11mime:multipartRelated/w11mime:part/w12soap:body"/>
        <xsl:choose>
            <!-- Is this SOAP RPC? -->
            <xsl:when test="$soapbody/../../w11soap:operation/@style = 'rpc' or
                $soapbody/../../w12soap:operation/@style = 'rpc' or
                $soapbody/../../../w11soap:binding/@style = 'rpc' or
                $soapbody/../../../w12soap:binding/@style = 'rpc'">
                <!-- Check that all parts are defined with elements -->
                <xsl:variable name="usestypes">
                    <xsl:call-template name="message-is-using-types">
                        <xsl:with-param name="parts" select="$soapbody/@parts"/>
                        <xsl:with-param name="message" select="$message"/>
                    </xsl:call-template>
                </xsl:variable>
                <xsl:choose>
                    <xsl:when test="$usestypes = 'no'">
                        <!-- This is the case; we can be precise -->
                        <xsl:variable name="localName">
                            <xsl:call-template name="resolve-soaprpc-element-localname">
                                <xsl:with-param name="msg" select="."/>
                            </xsl:call-template>
                        </xsl:variable>
                        <xsl:variable name="elementType_namespace-uri" select="$soapbody/@namespace"/>
                        <xsl:variable name="elementType" select="$localName"/>
                        <xsl:call-template name="resolve-elementType-attrs">
                            <xsl:with-param name="element" select="$elementType"/>
                            <xsl:with-param name="namespace" select="$elementType_namespace-uri"/>
                            <xsl:with-param name="faultname" select="@name"/>
                        </xsl:call-template>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:variable name="new_msg_name">
                            <xsl:choose>
                                <xsl:when test="local-name(.)='input'">
                                    <xsl:value-of select="$operation_name"/>
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:value-of select="concat($operation_name, 'Response')"/>
                                </xsl:otherwise>
                            </xsl:choose>
                        </xsl:variable>
                        <xsl:call-template name="resolve-elementType-attrs">

	                        <xsl:with-param name="element" select="$new_msg_name"/>
                            <xsl:with-param name="namespace" select="$message_name_namespace-uri"/>
                            <xsl:with-param name="faultname" select="@name"/>
                        </xsl:call-template>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:when>
            <!-- Is this a simple case? One part defined as an element -->
            <xsl:when test="count($message/w11:part) = 1 and not($message/w11:part/@type) and $message/w11:part/@element">
                <!-- Simple case when a message has only one part defined as an element -->
                <xsl:variable name="elementType_prefix" select="substring-before($message/w11:part/@element,':')"/>
                <xsl:variable name="elementType_local-name">
                    <xsl:choose>
                        <xsl:when test="contains($message/w11:part/@element,':')">
                            <xsl:value-of select="substring-after($message/w11:part/@element,':')"/>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:value-of select="$message/w11:part/@element"/>
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:variable>        
                <xsl:variable name="elementType_namespace-uri" select="$message/w11:part/namespace::*[local-name()=$elementType_prefix]"/>
                <xsl:call-template name="resolve-elementType-attrs">
                    <xsl:with-param name="element" select="$elementType_local-name"/>
                    <xsl:with-param name="namespace" select="$elementType_namespace-uri"/>
                    <xsl:with-param name="faultname" select="@name"/>
                </xsl:call-template>
            </xsl:when>
            <!-- Is there more than one part? -->
            <xsl:when test="count($message/w11:part) &gt; 1">
                <!-- Case where there's more than one part -->
                <xsl:variable name="httpUrlReplacement">
                    <xsl:for-each select="$bound_operation">
                        <xsl:if test="w11:input[@message =$message_name_local-name]/w11http:urlReplacement or w11:input/w11http:urlReplacement">
                            <xsl:text>y</xsl:text>
                        </xsl:if>
                    </xsl:for-each>
                </xsl:variable>
                <xsl:choose>
                    <!-- Is the message bound to SOAP? -->
                    <xsl:when test="$soapbody">
                        <xsl:choose>
                            <!-- FIXME - BIG FAT WARNING: this is assuming that
                                 there's only one binding of the interface In case
                                 this isn't the case, we have to hope that the same
                                 part is going to be bound to the body, otherwise
                                 the assumptions made here are going to be wrong -->
                            <xsl:when test="not(contains($soapbody/@parts, ' '))">
                                <!-- ... but only one is the body, and is defined as
                                     an element -->
                                <xsl:variable name="bodypart" select="$message/w11:part[contains($soapbody/@parts, @name)]"/>
                                <xsl:if test="not($bodypart/@type) and $bodypart/@element">
                                    <xsl:variable name="elementType_prefix" select="substring-before($bodypart/@element,':')"/>
                                    <xsl:variable name="elementType_local-name">
                                        <xsl:choose>
                                            <xsl:when test="contains($bodypart/@element,':')">
                                                <xsl:value-of select="substring-after($bodypart/@element,':')"/>
                                            </xsl:when>
                                            <xsl:otherwise>
                                                <xsl:value-of select="$bodypart/@element"/>
                                            </xsl:otherwise>
                                        </xsl:choose>
                                    </xsl:variable>        
                                    <xsl:variable name="elementType_namespace-uri" select="$bodypart/namespace::*[local-name()=$elementType_prefix]"/>
                                    <xsl:call-template name="resolve-elementType-attrs">
                                        <xsl:with-param name="element" select="$elementType_local-name"/>
                                        <xsl:with-param name="namespace" select="$bodypart/namespace::*[local-name()=$elementType_prefix]"/>
                                        <xsl:with-param name="faultname" select="@name"/>
                                    </xsl:call-template>
                                </xsl:if>
                            </xsl:when>
                            <xsl:otherwise>
                                <xsl:call-template name="resolve-elementType-attrs">
                                    <xsl:with-param name="element" select="'#any'"/>
                                    <xsl:with-param name="faultname" select="@name"/>
                                </xsl:call-template>
                            </xsl:otherwise>
                        </xsl:choose>
                    </xsl:when>
                    <!-- Is the message bound to HTTP with URL replacement? -->
                    <xsl:when test="contains($httpUrlReplacement, 'y')">
                        <xsl:call-template name="resolve-elementType-attrs">
                            <xsl:with-param name="element" select="concat('convertns:', ../@name)"/>
                            <xsl:with-param name="namespace" select="concat(/w11:definitions/@targetNamespace, 'GEN')"/>
                            <xsl:with-param name="faultname" select="@name"/>
                        </xsl:call-template>
                    </xsl:when>
                    <!-- No, the message is not bound to SOAP nor to HTTP, we don't make any assumptions -->
                    <xsl:otherwise>
                        <xsl:call-template name="resolve-elementType-attrs">
                            <xsl:with-param name="element" select="'#any'"/>
                            <xsl:with-param name="faultname" select="@name"/>
                        </xsl:call-template>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:when>
            <xsl:otherwise>
                <xsl:call-template name="resolve-elementType-attrs">
                    <xsl:with-param name="element" select="'#any'"/>
                    <xsl:with-param name="faultname" select="@name"/>
                </xsl:call-template>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    
    <xsl:template name="http-serialization">
        <xsl:param name="binding-msg-ref"/>
        <xsl:param name="attrib"/>
        <xsl:variable name="mime" select="$binding-msg-ref/w11mime:content"/>
        <xsl:if test="count($mime) = 1 and $mime/@type">
            <xsl:attribute name="{$attrib}">
                <xsl:value-of select="$mime/@type"/>
            </xsl:attribute>
        </xsl:if>
        <xsl:if test="count($mime) &gt; 1">
            <xsl:attribute name="{$attrib}">*/*</xsl:attribute>
        </xsl:if>
    </xsl:template>
    
    <xsl:template match="w11:binding">
        <binding name="{@name}" interface="{@type}">
            <xsl:variable name="qname_prefix" select="substring-before(@type,':')"/>
            <xsl:variable name="qname_local-name">
                <xsl:choose>
                    <xsl:when test="contains(@type,':')">
                        <xsl:value-of select="substring-after(@type,':')"/>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:value-of select="@type"/>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:variable>        
            <xsl:variable name="qname_namespace-uri" select="$type/namespace::*[local-name()=$qname_prefix]"/>
            <xsl:copy-of select="$type/namespace::*[local-name()=$qname_prefix]"/>

            <xsl:if test="w11soap:binding">
                <xsl:attribute name="type">http://www.w3.org/ns/wsdl/soap</xsl:attribute>
                <xsl:attribute name="version" namespace="http://www.w3.org/ns/wsdl/soap">1.1</xsl:attribute>
                <xsl:attribute name="protocol" namespace="http://www.w3.org/ns/wsdl/soap">
                    <xsl:choose>
                        <xsl:when test="w11soap:binding/@transport = 'http://schemas.xmlsoap.org/soap/http'">
                            <xsl:text>http://www.w3.org/2006/01/soap11/bindings/HTTP/</xsl:text>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:value-of select="w11soap:binding/@transport"/>
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:attribute>
            </xsl:if>
            <xsl:if test="w12soap:binding">
                <xsl:attribute name="type">http://www.w3.org/ns/wsdl/soap</xsl:attribute>
                <xsl:attribute name="version" namespace="http://www.w3.org/ns/wsdl/soap">1.2</xsl:attribute>
                <xsl:attribute name="protocol" namespace="http://www.w3.org/ns/wsdl/soap">
                    <xsl:choose>
                        <xsl:when test="w12soap:binding/@transport = 'http://schemas.xmlsoap.org/soap/http'">
                            <xsl:text>http://www.w3.org/2003/05/soap/bindings/HTTP/</xsl:text>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:value-of select="w12soap:binding/@transport"/>
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:attribute>
            </xsl:if>
            <xsl:if test="w11http:binding">
                <xsl:attribute name="type">http://www.w3.org/ns/wsdl/http</xsl:attribute>
                <xsl:attribute name="methodDefault" namespace="http://www.w3.org/ns/wsdl/http">
                    <xsl:value-of select="w11http:binding/@verb"/>
                </xsl:attribute>
            </xsl:if>
            <xsl:apply-templates select="/w11:definitions[@targetNamespace = $qname_namespace-uri]/w11:portType[@name = $qname_local-name]/w11:operation/w11:fault" mode="binding"/>
            <xsl:apply-templates select="*[not(local-name()='binding' and (namespace-uri() = 'http://schemas.xmlsoap.org/wsdl/soap/' or namespace-uri() = 'http://schemas.xmlsoap.org/wsdl/soap12/' or namespace-uri() = 'http://schemas.xmlsoap.org/wsdl/http/'))]"/>
        </binding>
    </xsl:template>
    
    <xsl:template match="w11:binding/w11:operation">
        <xsl:variable name="prefix">
            <xsl:value-of select="$qname_prefix"/>
        </xsl:variable>
        <operation ref="{$prefix}:{@name}">
            
            <!-- SOAP Binding -->
            <xsl:variable name="action" select="w11soap:operation/@soapAction | w12soap:operation/@soapAction"/>
            <xsl:if test="$action != ''">
                <!--
                    @@@ Unsure about this test:
                    http://lists.w3.org/Archives/Public/public-ws-desc-comments/2006Feb/0000.html
                -->
                <xsl:attribute name="action" namespace="http://www.w3.org/ns/wsdl/soap">
                    <xsl:value-of select="$action"/>
                </xsl:attribute>
            </xsl:if>
            <xsl:if test="../w11soap:binding or ../w12soap:binding">
                <xsl:if test="w11:input/w11soap:header or w11:output/w11soap:header or w11:input/w12soap:header or w11:output/w12soap:header">
                       <xsl:apply-templates select="*" mode="binding"/>
                </xsl:if>
            </xsl:if>
            <!-- HTTP Binding -->
            <xsl:if test="../w11http:binding">
                <xsl:call-template name="http-serialization">
                    <xsl:with-param name="binding-msg-ref" select="w11:input"/>
                    <xsl:with-param name="attrib" select="'inputSerialization'"/>
                </xsl:call-template>
                <xsl:call-template name="http-serialization">
                    <xsl:with-param name="binding-msg-ref" select="w11:output"/>
                    <xsl:with-param name="attrib" select="'outputSerialization'"/>
                </xsl:call-template>
            </xsl:if>
            <xsl:if test="w11http:operation/@location">
                <xsl:choose>
                    <xsl:when test="w11:input/w11http:urlReplacement">
                        <xsl:attribute name="location" namespace="http://www.w3.org/ns/wsdl/http">
                            <xsl:value-of select="translate(w11http:operation/@location, '()', '{}')"/>
                        </xsl:attribute>
                        <xsl:attribute name="ignoreUncited"
                            namespace="http://www.w3.org/ns/wsdl/http">
                            <xsl:text>true</xsl:text>
                        </xsl:attribute>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:attribute name="location" namespace="http://www.w3.org/ns/wsdl/http">
                            <xsl:value-of select="w11http:operation/@location"/>
                        </xsl:attribute>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:if>
        </operation>
    </xsl:template>
    
    <xsl:template match="w11:input|w11:output" mode="binding">
        <!-- We're not specifying @messageLabel here because it is not needed for the MEPs 
             that we handle -->
        <xsl:element name="{local-name()}" namespace="http://www.w3.org/ns/wsdl">
            <xsl:for-each select="./w12soap:header | ./w11soap:header">
                <xsl:variable name="header-message_local-name">
                    <xsl:choose>
                        <xsl:when test="contains(@message | @message,':')">
                            <xsl:value-of select="substring-after(@message | @message,':')"/>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:value-of select="@message"/>
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:variable>        
                
                <xsl:variable name="messageName"
                    select="$header-message_local-name"/>
                <xsl:variable name="partName" select="@part"/>
                <xsl:variable name="elementName"
                    select="//w11:message[@name=$messageName]/w11:part[@name=$partName]/@element"/>
                <!-- FIXME: Not handling types -->
                <xsl:if test="$elementName">
                    <wsoap:header required="true">
                        <xsl:variable name="element_prefix" select="substring-before($elementName,':')"/>
                        <xsl:variable name="element_local-name">
                            <xsl:choose>
                                <xsl:when test="contains($elementName,':')">
                                    <xsl:value-of select="substring-after($elementName,':')"/>
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:value-of select="$elementName"/>
                                </xsl:otherwise>
                            </xsl:choose>
                        </xsl:variable>        
                        <xsl:variable name="element_namespace-uri" select="//w11:message[@name=$messageName]/w11:part[@name=$partName]/namespace::*[local-name()=$element_prefix]"/>
                        <xsl:variable name="element_prefix-separator">
                            <xsl:if test="$element_prefix != ''">:</xsl:if>
                        </xsl:variable>        
                        <xsl:copy-of select="//w11:message[@name=$messageName]/w11:part[@name=$partName]/namespace::*[local-name()=$element_prefix]"/><!-- no namespace remap -->
                        <xsl:attribute name="element">
                            <xsl:value-of select="$element_prefix"/>
                            <xsl:value-of select="$element_prefix-separator"/>
                            <xsl:value-of select="$element_local-name"/>
                        </xsl:attribute>
                    </wsoap:header>
                </xsl:if>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="w11:service">
        <service name="{@name}">
            <xsl:variable name="binding_qname_prefix" select="substring-before(w11:port[1]/@binding,':')"/>
            <xsl:variable name="binding_qname_local-name">
                <xsl:choose>
                    <xsl:when test="contains(w11:port[1]/@binding,':')">
                        <xsl:value-of select="substring-after(w11:port[1]/@binding,':')"/>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:value-of select="w11:port[1]/@binding"/>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:variable>        
            <xsl:variable name="binding_qname_namespace-uri" select="w11:port[1]/namespace::*[local-name()=$binding_qname_prefix]"/>
            
            <xsl:variable name="binding" select="/w11:definitions[@targetNamespace = $binding_qname_namespace-uri]/w11:binding[@name = $binding_qname_local-name]"/>
            <!--TODO  <xsl:variable name="interface" select="resolve-QName($binding/@type, $binding)"/>-->
            <xsl:attribute name="interface">
                <xsl:value-of select="$binding/@type"/>
            </xsl:attribute>
            <!--TODO      <xsl:namespace name='{prefix-from-QName($interface)}' select='namespace-uri-from-QName($interface)'/>
-->
            <xsl:apply-templates select="*"/>
        </service>
    </xsl:template>
    
    <xsl:template match="w11:port">
        <endpoint name="{@name}" binding="{@binding}">
            <xsl:variable name="qname_prefix" select="substring-before(@binding,':')"/>
            <xsl:variable name="qname_local-name">
                <xsl:choose>
                    <xsl:when test="contains(@binding,':')">
                        <xsl:value-of select="substring-after(@binding,':')"/>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:value-of select="@binding"/>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:variable>        
            <xsl:variable name="qname_namespace-uri" select="namespace::*[local-name()=$qname_prefix]"/>
            <!--TODO      <xsl:namespace name='{$qname_prefix}' select='$qname_namespace-uri'/>
-->
            <xsl:if test="w11soap:address or w12soap:address">
                <xsl:attribute name="address">
                    <xsl:value-of select="w11soap:address/@location | w12soap:address/@location"/>
                </xsl:attribute>
            </xsl:if>
            <xsl:if test="w11http:address">
                <xsl:attribute name="address">
                    <xsl:value-of select="w11http:address/@location"/>
                </xsl:attribute>
            </xsl:if>
        </endpoint>
    </xsl:template>

    <xsl:template match="w11:documentation">
        <documentation>
            <xsl:apply-templates select="*|@*|text()"/>
        </documentation>
    </xsl:template>

    <xsl:template match="*|@*|text()">
        <xsl:copy>
            <xsl:if test="(local-name()='element' and @type) or (local-name()='extension' and @base) or (local-name()='restriction' and @base)">
                <xsl:variable name="prefix">
                    <xsl:if test="@type and contains(@type,':')">
                        <xsl:value-of select="substring-before(@type, ':')"/>
                    </xsl:if>
                    <xsl:if test="@base and contains(@base,':')">
                        <xsl:value-of select="substring-before(@base, ':')"/>
                    </xsl:if>
                </xsl:variable>
                <xsl:copy-of select="namespace::*[local-name() = $prefix]"/>
            </xsl:if>
            <xsl:if test="@ref or (local-name()='element' or local-name()='attribute' or local-name()='attributeGroup')">
                <xsl:variable name="prefix">
                    <xsl:if test="contains(@ref,':')">
                        <xsl:value-of select="substring-before(@ref, ':')"/>
                    </xsl:if>
                </xsl:variable>
                <xsl:copy-of select="namespace::*[local-name() = $prefix]"/>
            </xsl:if>
            <xsl:apply-templates select="*|@*|text()"/>
        </xsl:copy>
    </xsl:template>
</xsl:transform>
