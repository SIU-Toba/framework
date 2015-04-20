<?php

# Licensed to the Apache Software Foundation (ASF) under one
# or more contributor license agreements.  See the NOTICE file
# distributed with this work for additional information
# regarding copyright ownership.  The ASF licenses this file
# to you under the Apache License, Version 2.0 (the
# "License"); you may not use this file except in compliance
# with the License.  You may obtain a copy of the License at
# 
# http://www.apache.org/licenses/LICENSE-2.0
# 
# Unless required by applicable law or agreed to in writing,
# software distributed under the License is distributed on an
# "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
# KIND, either express or implied.  See the License for the
# specific language governing permissions and limitations
# under the License.
require_once ('cmis_repository_wrapper.php');

// Option Contants for Array Indexing
// -- Generally optional flags that control how much information is returned
// -- Change log token is an anomoly -- but included in URL as parameter
define("OPT_MAX_ITEMS", "maxItems");
define("OPT_SKIP_COUNT", "skipCount");
define("OPT_FILTER", "filter");
define("OPT_INCLUDE_PROPERTY_DEFINITIONS", "includePropertyDefinitions");
define("OPT_INCLUDE_RELATIONSHIPS", "includeRelationships");
define("OPT_INCLUDE_POLICY_IDS", "includePolicyIds");
define("OPT_RENDITION_FILTER", "renditionFilter");
define("OPT_INCLUDE_ACL", "includeACL");
define("OPT_INCLUDE_ALLOWABLE_ACTIONS", "includeAllowableActions");
define("OPT_DEPTH", "depth");
define("OPT_CHANGE_LOG_TOKEN", "changeLogToken");
define("OPT_CHECK_IN_COMMENT", "checkinComment");
define("OPT_CHECK_IN", "checkin");
define("OPT_MAJOR_VERSION", "major");

define("COLLECTION_ROOT_FOLDER","root");
define("COLLECTION_TYPES","types");
define("COLLECTION_CHECKED_OUT","checkedout");
define("COLLECTION_QUERY","query");
define("COLLECTION_UNFILED","unfiled");

define("URI_TEMPLATE_OBJECT_BY_ID","objectbyid");
define("URI_TEMPLATE_OBJECT_BY_PATH","objectbypath");
define("URI_TEMPLATE_TYPE_BY_ID","typebyid");
define("URI_TEMPLATE_QUERY","query");

define("LINK_SELF", "self");
define("LINK_SERVICE","service");
define("LINK_DESCRIBED_BY", "describedby");
define("LINK_VIA","via");
define("LINK_EDIT_MEDIA", "edit-media");
define("LINK_EDIT","edit");
define("LINK_ALTERNATE", "alternate");
define("LINK_FIRST","first");
define("LINK_PREVIOUS", "previous");
define("LINK_NEXT","next");
define("LINK_LAST", "last");
define("LINK_UP","up");
define("LINK_DOWN", "down");
define("LINK_DOWN_TREE","down-tree");
define("LINK_VERSION_HISTORY","version-history");
define("LINK_CURRENT_VERSION", "current-version");


define("LINK_ALLOWABLE_ACTIONS", "http://docs.oasis-open.org/ns/cmis/link/200908/allowableactions");
define("LINK_RELATIONSHIPS","http://docs.oasis-open.org/ns/cmis/link/200908/relationships");
define("LINK_SOURCE","http://docs.oasis-open.org/ns/cmis/link/200908/source");
define("LINK_TARGET","http://docs.oasis-open.org/ns/cmis/link/200908/target");
define("LINK_POLICIES", "http://docs.oasis-open.org/ns/cmis/link/200908/policies");
define("LINK_ACL","http://docs.oasis-open.org/ns/cmis/link/200908/acl");
define("LINK_CHANGES","http://docs.oasis-open.org/ns/cmis/link/200908/changes");
define("LINK_FOLDER_TREE","http://docs.oasis-open.org/ns/cmis/link/200908/foldertree");
define("LINK_ROOT_DESCENDANTS","http://docs.oasis-open.org/ns/cmis/link/200908/rootdescendants");
define("LINK_TYPE_DESCENDANTS","http://docs.oasis-open.org/ns/cmis/link/200908/typedescendants");

define("MIME_ATOM_XML", 'application/atom+xml');
define("MIME_ATOM_XML_ENTRY", 'application/atom+xml;type=entry');
define("MIME_ATOM_XML_FEED", 'application/atom+xml;type=feed');
define("MIME_CMIS_TREE", 'application/cmistree+xml');
define("MIME_CMIS_QUERY", 'application/cmisquery+xml');

// Many Links have a pattern to them based upon objectId -- but can that be depended upon?
/**
 * CMIS Service
 * 
 * @api CMIS
 * @since CMIS-1.0
 */
class CMISService extends CMISRepositoryWrapper {
    
    /**
     * @internal
     */
	var $_link_cache;
    
    /**
     * @internal
     */
	var $_title_cache;
    
    /**
     * @internal
     */
	var $_objTypeId_cache;
    
    /**
     * @internal
     */
	var $_type_cache;
    
    /**
     * @internal
     */
	var $_changeToken_cache;
    

	
	/**
	 * Construct a new CMISService Connector
	 * 
	 * @param String $url Endpoint URL
	 * @param String $username Username
	 * @param String $password Password
	 * @param mixed[] $options Connection Options
	 * @param mixed[] $addlCurlOptions Additional CURL Options
	 * @api CMIS-Service
	 * @since CMIS-1.0
	 */
	 
/* Utility functions */
	
	function GenURLQueryString($options)
	{
		if (count($options) > 0) {
			return '&'.urldecode(http_build_query($options));
		}else{
			return null;
		}
    }
	 
	function chec__construct($url, $username, $password, $options = null, array $addlCurlOptions = array ()) {
		parent :: __construct($url, $username, $password, $options, $addlCurlOptions);
		$this->_link_cache = array ();
		$this->_title_cache = array ();
		$this->_objTypeId_cache = array ();
		$this->_type_cache = array ();
		$this->_changeToken_cache = array ();
	}

	// Utility Methods -- Added Titles

    /**
     * @internal
     */
	function cacheObjectInfo($obj) {
		$this->_link_cache[$obj->id] = $obj->links;
		$this->_title_cache[$obj->id] = $obj->properties["cmis:name"]; // Broad Assumption Here? jajaj, si!!
		$this->_objTypeId_cache[$obj->id] = $obj->properties["cmis:objectTypeId"];
		if (isset($obj->properties["cmis:changeToken"])) {
			$this->_changeToken_cache[$obj->id] = $obj->properties["cmis:changeToken"];
		}
	}

	/**
	 * Get an Object's property and return it as an array 
	 * 
	 * This returns an array even if it is a scalar or null
	 * 
	 * @todo Allow the getProperty method to query the object type information and
	 * return multivalue properties as arrays even if empty or if only a single value
	 * is present.
	 * @param Object $obj Object
	 * @param String $propName Property Name
	 * @returns mixed[]
	 * @api CMIS-Helper
	 * @since CMIS-1.0
	 */
	function getMultiValuedProp($obj,$propName) {
		if (isset($obj->properties[$propName])) {
			return CMISRepositoryWrapper::getAsArray($obj->properties[$propName]);
		}
		return array();
	}

	/**
	 * @internal
	 */
	function cacheFeedInfo($objs) {
		foreach ($objs->objectList as $obj) {
			$this->cacheObjectInfo($obj);
		}
	}

	/**
	 * @internal
	 */
	function cacheTypeFeedInfo($typs) {
		foreach ($typs->objectList as $typ) {
			$this->cacheTypeInfo($typ);
		}
	}

	/**
	 * @internal
	 */
	function cacheTypeInfo($tDef) {
		// TODO: Fix Type Caching with missing properties
		$this->_type_cache[$tDef->id] = $tDef;
	}

	/**
	 * @internal
	 */
	function getPropertyType($typeId, $propertyId) {
		if (isset($this->_type_cache[$typeId])) {
			if ($this->_type_cache[$typeId]->properties) {
				return $this->_type_cache[$typeId]->properties[$propertyId]["cmis:propertyType"];
			}
		}
		$obj = $this->getTypeDefinition($typeId);
		return $obj->properties[$propertyId]["cmis:propertyType"];
	}

	/**
	 * @internal
	 */
	function getObjectType($objectId) {
		if (isset($this->_objTypeId_cache[$objectId]) && $this->_objTypeId_cache[$objectId]) {
			return $this->_objTypeId_cache[$objectId];
		}
		$obj = $this->getObject($objectId);
		return $obj->properties["cmis:objectTypeId"];
	}

	/**
	 * @internal
	 */
	function getTitle($objectId) {
		if ($this->_title_cache[$objectId]) {
			return $this->_title_cache[$objectId];
		}
		$obj = $this->getObject($objectId);
		return $obj->properties["cmis:name"];
	}

	/**
	 * @internal
	 */
	function getTypeLink($typeId, $linkName) {
		if (isset($this->_type_cache[$typeId]->links)) {
			return $this->_type_cache[$typeId]->links[$linkName];
		}
		$typ = $this->getTypeDefinition($typeId);
		return $typ->links[$linkName];
	}

	/**
	 * @internal
	 */
	function getLink($objectId, $linkName) {
		if(isset($this->_link_cache)) {
			if (array_key_exists($objectId, $this->_link_cache)) {
			  return $this->_link_cache[$objectId][$linkName];
			}
		}
		$obj = $this->getObject($objectId);
		return $obj->links[$linkName];
	}

	// Repository Services
	// TODO: Need to fix this for multiple repositories
	/**
	 * Get an Object by Object Id
	 * @api CMIS-RepositoryServices-NotImplemented
	 * @since CMIS-1.0
	 */
	function getRepositories() {
		throw new CmisNotImplementedException("getRepositories");
	}

	/**
	 * Get Repository Information
	 * @returns Object
	 * @api CMIS-RepositoryServices
	 * @since CMIS-1.0
	 */
	function getRepositoryInfo() {
		return $this->workspace;
	}

	/**
	 * Get a set of object-types that are descendants of the specified type
	 * 
	 * If typeId is null, then the repository MUST return all types and ignore the depth parameter.
	 *  
	 * @param String $typeId The typeId of an object-type specified in the repository
	 * @param $depth the number of levels in the hierarchy to return (-1 == all)
	 * @returns Object The set of descendant object-types defined for the given typeId.
	 * @api CMIS-RepositoryServices
	 * @since CMIS-1.0
	 */
	function getTypeDescendants($typeId = null, $depth, $options = array ()) {
		// TODO: Refactor Type Entries Caching
		$varmap = $options;
		if ($typeId) {
			$hash_values = $options;
			$hash_values[OPT_DEPTH] = $depth;
			$myURL = $this->getTypeLink($typeId, LINK_DOWN_TREE);
			$myURL = CMISRepositoryWrapper :: getOpUrl($myURL, $hash_values);
		} else {
			$myURL = $this->processTemplate($this->workspace->links[LINK_TYPE_DESCENDANTS], $varmap);
		}
		$ret = $this->doGet($myURL);
		$typs = $this->extractTypeFeed($ret->body);
		$this->cacheTypeFeedInfo($typs);
		return $typs;
	}

	/**
	 * Get a list of object-types that are children of the specified type
	 * 
	 * If typeId is null, then the repository MUST return all base object-types.
	 *  
	 * @param String $typeId The typeId of an object-type specified in the repository
	 * @returns Object The list of child object-types defined for the given typeId.
	 * @api CMIS-RepositoryServices
	 * @since CMIS-1.0
	 */
	function getTypeChildren($typeId = null, $options = array ()) {
		// TODO: Refactor Type Entries Caching
		$varmap = $options;
		if ($typeId) {
			$myURL = $this->getTypeLink($typeId, "down");
		    $myURL.= $this->GenURLQueryString($options);
		} else {
			//TODO: Need right URL
			$myURL = $this->processTemplate($this->workspace->collections['types'], $varmap);
		}
		$ret = $this->doGet($myURL);
		$typs = $this->extractTypeFeed($ret->body);
		$this->cacheTypeFeedInfo($typs);
		return $typs;
	}

	/**
	 * Gets the definition of the specified object-type.
	 *  
	 * @param String $typeId Object Type Id
	 * @returns Object Type Definition of the Specified Object
	 * @api CMIS-RepositoryServices
	 * @since CMIS-1.0
	 */
	function getTypeDefinition($typeId, $options = array ()) { // Nice to have
		$varmap = $options;
		$varmap["id"] = $typeId;
		$myURL = $this->processTemplate($this->workspace->uritemplates['typebyid'], $varmap);
		$ret = $this->doGet($myURL);
		$obj = $this->extractTypeDef($ret->body);
		$this->cacheTypeInfo($obj);
		return $obj;
	}

	/**
	 * Get an Object's Property Type by Object Id
	 * @param String $objectId Object Id
	 * @returns Object Type Definition of the Specified Object
	 * @api CMIS-Helper
	 * @since CMIS-1.0
	 */
	function getObjectTypeDefinition($objectId) { // Nice to have
		$myURL = $this->getLink($objectId, "describedby");
		$ret = $this->doGet($myURL);
		$obj = $this->extractTypeDef($ret->body);
		$this->cacheTypeInfo($obj);
		return $obj;
	}
	//Repository Services -- New for 1.1
	/**
	 * Creates a new type definition.
	 * 
	 * Creates a new type definition that is a subtype of an existing specified parent type.
	 * Only properties that are new to this type (not inherited) are passed to this service.
	 *
	 * @param String $objectType A type definition object with the property definitions that are to change.
	 * @returns Object Type Definition of the Specified Object
	 * @api CMIS-RepositoryServices-NotImplemented
	 * @since CMIS-1.1
	 */
	function createType($objectType) {
		throw new CmisNotImplementedException("createType");		
	}

	/**
	 * Updates a type definition
	 * 
	 * If you add an optional property to a type in error. There is no way to remove it/correct it - without
	 * deleting the type.
	 * 
	 * @param String $objectType A type definition object with the property definitions that are to change.
	 * @returns Object The updated object-type including all property definitions.
	 * @api CMIS-RepositoryServices-NotImplemented
	 * @since CMIS-1.1
	 */
	function updateType($objectType) {
		throw new CmisNotImplementedException("updateType");		
	}

	/**
	 * Deletes a type definition
	 * 
	 * If there are object instances present of the type being deleted then this operation MUST fail.
	 *
	 * @param String $typeId The typeId of an object-type specified in the repository.
	 * @api CMIS-RepositoryServices-NotImplemented
	 * @since CMIS-1.1
	 */
	function deleteType($typeId) {
		throw new CmisNotImplementedException("deleteType");		
	}
	//Navigation Services
	/**
	 * Get the list of descendant folders contained in the specified folder.
	 * 
	 * @param String $folderId the Object ID of the folder
	 * @param String $depth The number of levels of depth in the folder hierarchy from which to return results (-1 == ALL).
	 * @returns Object[] A tree of the child objects for the specified folder.
	 * @api CMIS-NavigationServices
	 * @since CMIS-1.0
	 */
	function getFolderTree($folderId, $depth, $options = array ()) {
		$hash_values = $options;
		$hash_values[OPT_DEPTH] = $depth;
		$myURL = $this->getLink($folderId, "http://docs.oasis-open.org/ns/cmis/link/200908/foldertree");
		$myURL = CMISRepositoryWrapper :: getOpUrl($myURL, $hash_values);
		$ret = $this->doGet($myURL);
		$objs = $this->extractObjectFeed($ret->body);
		$this->cacheFeedInfo($objs);
		return $objs;
	}

	/**
	 * Get the list of descendant objects contained in the specified folder.
	 * 
	 * @param String $folderId the Object ID of the folder
	 * @param String $depth The number of levels of depth in the folder hierarchy from which to return results (-1 == ALL).
	 * @returns Object[] A tree of the child objects for the specified folder.
	 * @api CMIS-NavigationServices
	 * @since CMIS-1.0
	 */
	function getDescendants($folderId, $depth, $options = array ()) { // Nice to have
		$hash_values = $options;
		$hash_values[OPT_DEPTH] = $depth;
		$myURL = $this->getLink($folderId, LINK_DOWN_TREE);
		$myURL = CMISRepositoryWrapper :: getOpUrl($myURL, $hash_values);
		$ret = $this->doGet($myURL);
		$objs = $this->extractObjectFeed($ret->body);
		$this->cacheFeedInfo($objs);
		return $objs;
	}

	/**
	 * Get the list of child objects contained in the specified folder.
	 * 
	 * @param String $folderId the Object ID of the folder
	 * @returns Object[] A list of the child objects for the specified folder.
	 * @api CMIS-NavigationServices
	 * @since CMIS-1.0
	 */
	function getChildren($folderId, $options = array ()) {
		$myURL = $this->getLink($folderId, LINK_DOWN);
		$myURL.= $this->GenURLQueryString($options);
		$ret = $this->doGet($myURL);
		$objs = $this->extractObjectFeed($ret->body);
		$this->cacheFeedInfo($objs);
		return $objs;
	}

	/**
	 * Get the parent folder of the specified folder.
	 * 
	 * @param String $folderId the Object ID of the folder
	 * @returns Object the parent folder.
	 * @api CMIS-NavigationServices
	 * @since CMIS-1.0
	 */
	function getFolderParent($folderId, $options = array ()) { //yes
		$myURL = $this->getLink($folderId, LINK_UP);
		$myURL.= $this->GenURLQueryString($options);
		$ret = $this->doGet($myURL);
		$obj = CMISRepositoryWrapper::extractObject($ret->body);
		$this->cacheObjectInfo($obj);
		return $obj;
	}

	/**
	 * Get the parent folder(s) for the specified fileable object.
	 * 
	 * @param String $objectId the Object ID of the Object
	 * @returns Object[] list of the parent folder(s) of the specified object.
	 * @api CMIS-NavigationServices
	 * @since CMIS-1.0
	 */
	function getObjectParents($objectId, $options = array ()) { // yes
		$myURL = $this->getLink($objectId, LINK_UP);
		$myURL.= $this->GenURLQueryString($options);
		$ret = $this->doGet($myURL);
		$objs = $this->extractObjectFeed($ret->body);
		$this->cacheFeedInfo($objs);
		return $objs;
	}

	/**
	 * Get the list of documents that are checked out that the user has access to..
	 * 
	 * @returns Object[] list of checked out documents.
	 * @api CMIS-NavigationServices
	 * @since CMIS-1.0
	 */
	function getCheckedOutDocs($options = array ()) {
		$obj_url = $this->workspace->collections[COLLECTION_CHECKED_OUT];
		$ret = $this->doGet($obj_url);
		$objs = $this->extractObjectFeed($ret->body);
		$this->cacheFeedInfo($objs);
		return $objs;
	}

	//Discovery Services
	/**
	 * @internal
	 */
	static function getQueryTemplate() {
		ob_start();
		echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
?>
<cmis:query xmlns:cmis="http://docs.oasis-open.org/ns/cmis/core/200908/"
xmlns:cmism="http://docs.oasis-open.org/ns/cmis/messaging/200908/"
xmlns:atom="http://www.w3.org/2005/Atom"
xmlns:app="http://www.w3.org/2007/app"
xmlns:cmisra="http://docs.oasisopen.org/ns/cmis/restatom/200908/">
<cmis:statement><![CDATA[{q}]]></cmis:statement>
<cmis:searchAllVersions>{searchAllVersions}</cmis:searchAllVersions>
<cmis:includeAllowableActions>{includeAllowableActions}</cmis:includeAllowableActions>
<cmis:includeRelationships>{includeRelationships}</cmis:includeRelationships>
<cmis:renditionFilter>{renditionFilter}</cmis:renditionFilter>
<cmis:maxItems>{maxItems}</cmis:maxItems>
<cmis:skipCount>{skipCount}</cmis:skipCount>
</cmis:query>
<?php


		return ob_get_clean();
	}

	/**
	 * Execute a CMIS Query
	 * @param String $statement Query Statement
	 * @param mixed[] $options Options
	 * @returns Object[] List of object propery values from query
	 * @api CMIS-DiscoveryServices
	 * @since CMIS-1.0
	 */
	function query($q,$options=array()) {
		static $query_template;
		if (!isset($query_template)) {
			$query_template = CMISService::getQueryTemplate();
		}
		$default_hash_values = array(
          "includeAllowableActions" => "false",
          "searchAllVersions" => "false",
          "maxItems" => 0,
          "skipCount" => 0
        );
  		//print_r($default_hash_values);
		//print_r($options);

        
		$hash_values=array_merge($default_hash_values, $options);
		$hash_values['q'] = $q;
		$post_value = CMISRepositoryWrapper::processTemplate($query_template,$hash_values);
		$ret = $this->doPost($this->workspace->collections['query'],$post_value,MIME_CMIS_QUERY);
		$objs = $this->extractObjectFeed($ret->body);
		//$this->cacheFeedInfo($objs);	// No es necesario y tiene asunciones erroneas sobre estructura
 		return $objs;
	}

	/**
	 * @internal
	 */
	function checkURL($url,$functionName=null) {
		if (!$url) {
			throw new CmisNotSupportedException($functionName?$functionName:"UnspecifiedMethod");
		}
	}

	/**
	 * Get Content Changes
	 * @param mixed[] $options Options
	 * @returns Object[] List of Change Events
	 * @api CMIS-DiscoveryServices
	 * @since CMIS-1.0
	 */
	function getContentChanges($options = array()) {
		$myURL =  CMISRepositoryWrapper :: processTemplate($this->workspace->links[LINK_CHANGES],$options);
		$this->checkURL($myURL,"getContentChanges");
		$ret = $this->doGet($myURL);
		$objs = $this->extractObjectFeed($ret->body);
		$this->cacheFeedInfo($objs);
		return $objs;
	}

	//Object Services
	/**
	 * @internal
	 */
	static function getEntryTemplate() {
		ob_start();
		echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
?>
<atom:entry xmlns:cmis="http://docs.oasis-open.org/ns/cmis/core/200908/"
xmlns:cmism="http://docs.oasis-open.org/ns/cmis/messaging/200908/"
xmlns:atom="http://www.w3.org/2005/Atom"
xmlns:app="http://www.w3.org/2007/app"
xmlns:cmisra="http://docs.oasis-open.org/ns/cmis/restatom/200908/">
<atom:title>{title}</atom:title>
{SUMMARY}
{CONTENT}
<cmisra:object><cmis:properties>{PROPERTIES}</cmis:properties></cmisra:object>
</atom:entry>
<?php


		return ob_get_clean();
	}

    
    /**
     * @internal
     */
	static function getPropertyTemplate() {
		ob_start();
?>
		<cmis:property{propertyType} propertyDefinitionId="{propertyId}">
			<cmis:value>{properties}</cmis:value>
		</cmis:property{propertyType}>
<?php


		return ob_get_clean();
	}

    
    /**
     * @internal
     */
	function processPropertyTemplates($objectType, $propMap) {
		static $propTemplate;
		static $propertyTypeMap;
		if (!isset ($propTemplate)) {
			$propTemplate = CMISService :: getPropertyTemplate();
		}
		if (!isset ($propertyTypeMap)) { // Not sure if I need to do this like this
			$propertyTypeMap = array (
				"integer" => "Integer",
				"boolean" => "Boolean",
				"datetime" => "DateTime",
				"decimal" => "Decimal",
				"html" => "Html",
				"id" => "Id",
				"string" => "String",
				"url" => "Url",
				"xml" => "Xml",

				
			);
		}
		$propertyContent = "";
		$hash_values = array ();
		foreach ($propMap as $propId => $propValue) {
			$hash_values['propertyType'] = $propertyTypeMap[$this->getPropertyType($objectType, $propId)];
			$hash_values['propertyId'] = $propId;
			if (is_array($propValue)) {
				$first_one = true;
				$hash_values['properties'] = "";
				foreach ($propValue as $val) {
					//This is a bit of a hack
					if ($first_one) {
						$first_one = false;
					} else {
						$hash_values['properties'] .= "</cmis:value>\n<cmis:value>";
					}
					$hash_values['properties'] .= $val;
				}
			} else {
				$hash_values['properties'] = $propValue;
			}
			//echo "HASH:\n";
			//print_r(array("template" =>$propTemplate, "Hash" => $hash_values));
			$propertyContent .= CMISRepositoryWrapper :: processTemplate($propTemplate, $hash_values);
		}
		return $propertyContent;
	}
	/**
	 * @internal
	 */
	static function getContentEntry($content, $content_type = "application/octet-stream") {
		//static $contentTemplate;
		$contentTemplate = null;
		if (!isset ($contentTemplate)) {
			$contentTemplate = CMISService :: getContentTemplate();
		}
		if ($content) {
			return CMISRepositoryWrapper :: processTemplate($contentTemplate, array (
				"content" => base64_encode($content),
				"content_type" => $content_type
			));
		} else {
			return "";
		}
	}

	/**
	 * @internal
	 */
	static function getSummaryTemplate() {
		ob_start();
?>
		<atom:summary>{summary}</atom:summary>
<?php


		return ob_get_clean();
	}

	/**
	 * @internal
	 */
	static function getContentTemplate() {
		ob_start();
?>
		<cmisra:content>
			<cmisra:mediatype>
				{content_type}
			</cmisra:mediatype>
			<cmisra:base64>
				{content}
			</cmisra:base64>
		</cmisra:content>
<?php


		return ob_get_clean();
	}
	/**
	 * @internal
	 */
	static function createAtomEntry($name, $properties) {

	}

	/**
	 * Get an Object by Object Id
	 * @param String $objectId Object ID
	 * @param mixed[] $options Options
	 * @returns Object
	 * @api CMIS-ObjectServices
	 * @since CMIS-1.0
	 */
	function getObject($objectId, $options = array ()) {
		$varmap = $options;
		$varmap["id"] = $objectId;
		$obj_url = $this->processTemplate($this->workspace->uritemplates['objectbyid'], $varmap);
		$ret = $this->doGet($obj_url);
		$obj = $this->extractObject($ret->body);
		$this->cacheObjectInfo($obj);
		return $obj;
	}

	/**
	 * Get an Object by its Path
	 * @param String $path Path To Object
	 * @param mixed[] $options Options
	 * @returns Object
	 * @api CMIS-ObjectServices
	 * @since CMIS-1.0
	 */
	function getObjectByPath($path, $options = array ()) {
		$varmap = $options;
		$varmap["path"] = $path;
		$obj_url = $this->processTemplate($this->workspace->uritemplates['objectbypath'], $varmap);
		$ret = $this->doGet($obj_url);
		$obj = $this->extractObject($ret->body);
		$this->cacheObjectInfo($obj);
		return $obj;
	}

	/**
	 * Get an Object's Properties by Object Id
	 * @param String $objectId Object Id
	 * @param mixed[] $options Options
	 * @returns Object
	 * @api CMIS-ObjectServices
	 * @since CMIS-1.0
	 */
	function getProperties($objectId, $options = array ()) {
		// May need to set the options array default -- 
		return $this->getObject($objectId, $options);
	}

	/**
	 * Get an Object's Allowable Actions
	 * @param String $objectId Object Id
	 * @param mixed[] $options Options
	 * @returns mixed[]
	 * @api CMIS-ObjectServices
	 * @since CMIS-1.0
	 */
	function getAllowableActions($objectId, $options = array ()) {
		$myURL = $this->getLink($objectId, LINK_ALLOWABLE_ACTIONS);
		$ret = $this->doGet($myURL);
		$result = $this->extractAllowableActions($ret->body);
		return $result;
	}

	/**
	 * Get the list of associated renditions for the specified object
	 * 
	 * Only rendition attributes are returned, not rendition stream.
	 * @param String $objectId Object Id
	 * @param mixed[] $options Options
	 * @returns Object[]
	 * @api CMIS-ObjectServices
	 * @since CMIS-1.0
	 */
	function getRenditions($objectId, $options = array (
		OPT_RENDITION_FILTER => "*"
	)) {
		return getObject($objectId, $options);
	}

	/**
	 * Get an Object's Allowable Actions
	 * @param String $objectId Object Id
	 * @param mixed[] $options Options
	 * @returns String
	 * @api CMIS-ObjectServices
	 * @since CMIS-1.0
	 */
	function getContentStream($objectId, $options = array ()) { // Yes
		$myURL = $this->getLink($objectId, "edit-media");
		$ret = $this->doGet($myURL);
		// doRequest stores the last request information in this object
		return $ret->body;
	}
	
	/**
	 * @internal
	 */
    function legacyPostObject($folderId, $objectName, $objectType, $properties = array (), $content = null, $content_type = "application/octet-stream", $options = array ())
    { // Yes
        $myURL = $this->getLink($folderId, "down");
        // TODO: Need Proper Query String Handling
        // Assumes that the 'down' link does not have a querystring in it
        $myURL = CMISRepositoryWrapper :: getOpUrl($myURL, $options);
        static $entry_template;
        if (!isset ($entry_template))
        {
            $entry_template = CMISService :: getEntryTemplate();
        }
        if (is_array($properties))
        {
            $hash_values = $properties;
        } else
        {
            $hash_values = array ();
        }
        if (!isset ($hash_values["cmis:objectTypeId"]))
        {
            $hash_values["cmis:objectTypeId"] = $objectType;
        }
        $properties_xml = $this->processPropertyTemplates($hash_values["cmis:objectTypeId"], $hash_values);
        if (is_array($options))
        {
            $hash_values = $options;
        } else
        {
            $hash_values = array ();
        }
        $hash_values["PROPERTIES"] = $properties_xml;
        $hash_values["SUMMARY"] = CMISService :: getSummaryTemplate();
        if ($content)
        {
            $hash_values["CONTENT"] = CMISService :: getContentEntry($content, $content_type);
        }
        if (!isset ($hash_values['title']))
        {
            $hash_values['title'] = preg_replace("/[^A-Za-z0-9\s.&; ]/", '', htmlentities($objectName));
        }
        if (!isset ($hash_values['summary']))
        {
            $hash_values['summary'] = preg_replace("/[^A-Za-z0-9\s.&; ]/", '', htmlentities($objectName));
        }
        $post_value = CMISRepositoryWrapper :: processTemplate($entry_template, $hash_values);
        $ret = $this->doPost($myURL, $post_value, MIME_ATOM_XML_ENTRY);
        // print "DO_POST\n";
        // print_r($ret);
        $obj = $this->extractObject($ret->body);
        $this->cacheObjectInfo($obj);
        return $obj;
    }
    
    /**
     * @internal
     */
	function postObject($folderId,$objectName,$objectType,$properties=array(),$content=null,$content_type="application/octet-stream",$options=array()) { // Yes
		$myURL = $this->getLink($folderId,"down");
		// TODO: Need Proper Query String Handling
		// Assumes that the 'down' link does not have a querystring in it
		$myURL = CMISRepositoryWrapper::getOpUrl($myURL,$options);
		static $entry_template;
		if (!isset($entry_template)) {
			$entry_template = CMISService::getEntryTemplate();
		}
		if (is_array($properties)) {
			$hash_values=$properties;
		} else {
			$hash_values=array();
		}
		if (!isset($hash_values["cmis:objectTypeId"])) {
			$hash_values["cmis:objectTypeId"]=$objectType;
		}
		$properties_xml = $this->processPropertyTemplates($objectType,$hash_values);
		if (is_array($options)) {
			$hash_values=$options;
		} else {
			$hash_values=array();
		}
		$hash_values["PROPERTIES"]=$properties_xml;
		$hash_values["SUMMARY"]=CMISService::getSummaryTemplate();
		if ($content) {
			$hash_values["CONTENT"]=CMISService::getContentEntry($content,$content_type);
		}
		
		if (!isset($hash_values['title'])) {
			$hash_values['title'] = preg_replace("/[^A-Za-z0-9\s.&; ]/", '', htmlentities($objectName));
		}
		
		if (!isset($hash_values['summary'])) {
			$hash_values['summary'] = preg_replace("/[^A-Za-z0-9\s.&; ]/", '', htmlentities($objectName));
		}
		$post_value = CMISRepositoryWrapper::processTemplate($entry_template,$hash_values);

		$ret = $this->doPost($myURL,$post_value,MIME_ATOM_XML_ENTRY);
		// print "DO_POST\n";
		// print_r($ret);
		$obj=$this->extractObject($ret->body);
		$this->cacheObjectInfo($obj);
  		return $obj;
	}
    
    /**
     * @internal
     */
	function postEntry($url, $properties = array (), $content = null, $content_type = "application/octet-stream", $options = array ()) {
		// TODO: Fix Hack HERE -- get type if it is there otherwise retrieve it --
		$objType ="";
		if (isset($properties['cmis:objectTypeId'])) {
			$objType = $properties['cmis:objectTypeId'];
		} else if (isset($properties["cmis:objectId"])) {
			$objType=$this->getObjectType($properties["cmis:objectId"]);			
		}
		$myURL = CMISRepositoryWrapper :: getOpUrl($url, $options);
		//DEBUG
		//print("DEBUG: postEntry: myURL = " . $myURL);
		$entry_template = null;
		if (!isset ($entry_template)) {
			$entry_template = CMISService :: getEntryTemplate();
		}
		//print("DEBUG: postEntry: entry_template = " . $entry_template);		
		$properties_xml = $this->processPropertyTemplates($objType, $properties);
		//print("DEBUG: postEntry: properties_xml = " . $properties_xml);		
		if (is_array($options)) {
			$hash_values = $options;
		} else {
			$hash_values = array ();
		}
		$hash_values["PROPERTIES"] = $properties_xml;
		$hash_values["SUMMARY"] = CMISService :: getSummaryTemplate();
		if (isset($content)) {
			$hash_values["CONTENT"] = CMISService :: getContentEntry($content, $content_type);
		}
		//print("DEBUG: postEntry: hash_values = " . print_r($hash_values,true));		
		$post_value = CMISRepositoryWrapper :: processTemplate($entry_template, $hash_values);
		//print("DEBUG: postEntry: post_value = " . $post_value);		
		$ret = $this->doPost($myURL, $post_value, MIME_ATOM_XML_ENTRY);
		$obj = $this->extractObject($ret->body);
		//$this->cacheObjectInfo($obj);
		return $obj;
	}

	function createDocument($folderId, $fileName, $properties = array (), $content = null, $content_type = "application/octet-stream", $options = array ()) { // Yes
		return $this->postObject($folderId, $fileName, "cmis:document", $properties, $content, $content_type, $options);
	}

	function createDocumentWithType($folderId, $fileName, $type = "cmis:document", $properties = array (), $content = null, $content_type = "application/octet-stream", $options = array ()) {
		return $this->postObject($folderId, $fileName, $type, $properties, $content, $content_type, $options);
	}	
	
	function createDocumentFromSource() { //Yes?
		throw new CmisNotSupportedException("createDocumentFromSource is not supported by the AtomPub binding!");
	}

	function createFolder($folderId, $folderName, $properties = array (), $options = array ()) { // Yes
		return $this->legacyPostObject($folderId, $folderName, "cmis:folder", $properties, null, null, $options);
	}

	function createRelationship() { // Not in first Release
		throw new CmisNotImplementedException("createRelationship");
	}

	function createPolicy() { // Not in first Release
		throw new CmisNotImplementedException("createPolicy");
	}
	
	function createItem() {
		throw new CmisNotImplementedException("createItem");
	}

	function updateProperties($objectId, $properties = array (), $options = array ()) { // Yes
		$varmap = $options;
		$varmap["id"] = $objectId;
		$objectName = $this->getTitle($objectId);
		$objectType = $this->getObjectType($objectId);
		$obj_url = $this->getLink($objectId, "edit");
		$obj_url = CMISRepositoryWrapper :: getOpUrl($obj_url, $options);
		static $entry_template;
		if (!isset ($entry_template)) {
			$entry_template = CMISService :: getEntryTemplate();
		}
		if (is_array($properties)) {
			$hash_values = $properties;
		} else {
			$hash_values = array ();
		}
		if (isset($this->_changeToken_cache[$objectId])) {
			$properties['cmis:changeToken'] = $this->_changeToken_cache[$objectId];
		}
		
		$properties_xml = $this->processPropertyTemplates($objectType, $hash_values);
		if (is_array($options)) {
			$hash_values = $options;
		} else {
			$hash_values = array ();
		}
		
		$fixed_hash_values = array(
			"PROPERTIES" => $properties_xml,
			"SUMMARY" => CMISService::getSummaryTemplate(),
		);

		// merge the fixes hash values first so that the processing order is correct
		$hash_values = array_merge($fixed_hash_values, $hash_values);
		
		if (!isset($hash_values['title'])) {
			$hash_values['title'] = $objectName;
		}
		if (!isset($hash_values['summary'])) {
			$hash_values['summary'] = $objectName;
		}
		$put_value = CMISRepositoryWrapper :: processTemplate($entry_template, $hash_values);
		$ret = $this->doPut($obj_url, $put_value, MIME_ATOM_XML_ENTRY);
		
		$obj = $this->extractObject($ret->body);
		$this->cacheObjectInfo($obj);
		return $obj;
	}
	
	// New for 1.1
	function bulkUpdateProperties() {
		throw new CmisNotImplementedException("bulkUpdateProperties");		
	}

	function moveObject($objectId, $targetFolderId, $sourceFolderId, $options = array ()) { //yes
		$options['sourceFolderId'] = $sourceFolderId;
		return $this->postObject($targetFolderId, $this->getTitle($objectId), $this->getObjectType($objectId), array (
			"cmis:objectId" => $objectId
		), null, null, $options);
	}

	/**
	 * Delete an Object
	 * @param String $objectId Object ID
	 * @param mixed[] $options Options
	 * @api CMIS-ObjectServices
	 * @since CMIS-1.0
	 */
	function deleteObject($objectId, $options = array ()) { //Yes
		$varmap = $options;
		$varmap["id"] = $objectId;
		$obj_url = $this->getLink($objectId, "edit");
		$ret = $this->doDelete($obj_url);
		return;
	}

	/**
	 * Delete an Object Tree
	 * @param String $folderId Folder Object ID
	 * @param mixed[] $options Options
	 * @return Object[] Array of problem objects
	 * @api CMIS-ObjectServices
	 * @since CMIS-1.0
	 */
	function deleteTree($folderId, $options = array ()) { // Nice to have
		$hash_values = $options;
		$myURL = $this->getLink($folderId, LINK_DOWN_TREE);
		$myURL = CMISRepositoryWrapper :: getOpUrl($myURL, $hash_values);
		$ret = $this->doDelete($myURL);
		//List of problem objects
		$objs = $this->extractObjectFeed($ret->body);
		$this->cacheFeedInfo($objs);
		return $objs;
	}

	/**
	 * Set an Objects Content Stream
	 * @param String $objectId Object ID
	 * @param String $content Content to be appended
	 * @param String $content_type Content Mime Type
	 * @param mixed[] $options Options
	 * @returns Object
	 * @api CMIS-ObjectServices
	 * @since CMIS-1.0
	 */
	function setContentStream($objectId, $content, $content_type, $options = array ()) { //Yes
		$myURL = $this->getLink($objectId, "edit-media");
		$ret = $this->doPut($myURL, $content, $content_type);
	}

	// New for 1.1
	/**
	 * Append Content to an Objects Content Stream
	 * @param String $objectId Object ID
	 * @param String $content Content to be appended
	 * @param String $content_type Content Mime Type
	 * @param mixed[] $options Options
	 * @returns Object
	 * @api CMIS-ObjectServices-NotImplemented
	 * @since CMIS-1.0
	 */
	function appendContentStream($objectId, $content, $content_type, $options = array ()) { //Yes
		throw new CmisNotImplementedException("appendContentStream");
	}

	/**
	 * Delete an Objects Content Stream
	 * @param String $objectId Object ID
	 * @param mixed[] $options Options
	 * @api CMIS-ObjectServices
	 * @since CMIS-1.0
	 */
	function deleteContentStream($objectId, $options = array ()) { //yes
		$myURL = $this->getLink($objectId, "edit-media");
		$ret = $this->doDelete($myURL);
		return;
	}

	//Versioning Services
	function getPropertiesOfLatestVersion($objectId, $major = false, $options = array ()) {
		return $this->getObjectOfLatestVersion($objectId, $major, $options);
	}

	function getObjectOfLatestVersion($objectId, $major = false, $options = array ()) {
		return $this->getObject($objectId, $options); // Won't be able to handle major/minor distinction
		// Need to add this -- "current-version"
		/*
		 * Headers: CMIS-filter, CMIS-returnVersion (enumReturnVersion) 
		 * HTTP Arguments: filter, returnVersion 
		 * Enum returnVersion: This, Latest, Major
		 */
	}

	function getAllVersions() {
		throw new CmisNotImplementedException("getAllVersions");
	}

	/**
	 * Checkout
	 * @param String $objectId Object ID
	 * @param mixed[] $options Options
	 * @return Object The working copy
	 * @api CMIS-VersionServices
	 * @since CMIS-1.0
	 */
	function checkOut($objectId,$options = array()) {
		$myURL = $this->workspace->collections[COLLECTION_CHECKED_OUT];
		$myURL = CMISRepositoryWrapper :: getOpUrl($myURL, $options);
		$obj = $this->postEntry($myURL,  array ("cmis:objectId" => $objectId));
		return $obj;
	}

	/**
	 * Checkin
	 * @param String $objectId Object ID
	 * @param mixed[] $options Options
	 * @return Object The checked in object
	 * @api CMIS-VersionServices
	 * @since CMIS-1.0
	 */
	function checkIn($objectId, $properties= array()) {
		/*
		 * Tal vez la forma mas prolija de hacer esto es guardar en el checkout
		 * la propiedad $obj->links['self'], y usarla despues aca.
		 * El problema es que se necesita mantener un contexto
		 * entre los 2 usos del api, y ademas se rompe la logica de construccion
		 * de URLs que en ningun caso parte de una URL provista desde afuera...
		 */
		$myURL = $this->url . "/default/entry";
		$qs = array ("id" => $objectId, "major"=> "true", "checkin" => "true");
		$myURL = CMISRepositoryWrapper :: getOpUrl($myURL, $qs);
		
		$objectName = $this->getTitle($objectId);
		$objectType = $this->getObjectType($objectId);
		static $entry_template;
		if (!isset ($entry_template)) {
			$entry_template = CMISService :: getEntryTemplate();
		}
		if (is_array($properties)) {
			$hash_values = $properties;
		} else {
			$hash_values = array ();
		}
		$properties_xml = $this->processPropertyTemplates($objectType, $hash_values);
		$fixed_hash_values = array(
			"PROPERTIES" => $properties_xml,
			"SUMMARY" => CMISService::getSummaryTemplate(),
		);

		// merge the fixes hash values first so that the processing order is correct
		$hash_values = array_merge($fixed_hash_values, $hash_values);
		
		if (!isset($hash_values['title'])) {
			$hash_values['title'] = $objectName;
		}
		if (!isset($hash_values['summary'])) {
			$hash_values['summary'] = $objectName;
		}
		
		$put_value = CMISRepositoryWrapper :: processTemplate($entry_template, $hash_values);
		$ret = $this->doPut($myURL, $put_value, MIME_ATOM_XML_ENTRY);
	}

	/**
	 * Cancel Checkout
	 * @param String $objectId Object ID
	 * @param mixed[] $options Options
	 * @api CMIS-VersionServices
	 * @since CMIS-1.0
	 */
	function cancelCheckOut($objectId,$options = array()) {
		/*
		 * Atencion, este encare tiene problemas porque borra al objeto
		 * si no hay un checkout real hecho previamente
 		 */
		$myURL = $this->url . "/default/entry";
		$qs = array ("id" => $objectId);
		$myURL = CMISRepositoryWrapper :: getOpUrl($myURL, $qs);
		$this->doDelete($myURL);		
		/*
		// TODO: Look at links "via" and "working-copy"
		$varmap = $options;
		$varmap["id"] = $objectId;
		$via = $this->getLink($objectId,"via");
		print("DEBUG: cancelCheckOut VIA="+$via);
		if (!$via) {
			throw new CmisInvalidArgumentException("Not a WORKING COPY!");
		}
		$obj_url = $this->getLink($objectId, "edit");
		$ret = $this->doDelete($obj_url);
		return;
		 */
	}

	function deleteAllVersions() {
		throw new CmisNotImplementedException("deleteAllVersions");
	}

	//Relationship Services
	function getObjectRelationships() {
		// get stripped down version of object (for the links) and then get the relationships?
		// Low priority -- can get all information when getting object
		throw new CmisNotImplementedException("getObjectRelationships");
	}

	//Multi-Filing ServicesRelation
	function addObjectToFolder($objectId, $targetFolderId, $options = array ()) { // Probably
		return $this->postObject($targetFolderId, $this->getTitle($objectId), $this->getObjectType($objectId), array (
			"cmis:objectId" => $objectId
		), null, null, $options);
	}

	function removeObjectFromFolder($objectId, $targetFolderId, $options = array ()) { //Probably
		$hash_values = $options;
		$myURL = $this->workspace->collections['unfiled'];
		$myURL = CMISRepositoryWrapper :: getOpUrl($myURL, $hash_values);
		$ret = $this->postEntry($myURL,  array ("cmis:objectId" => $objectId),null,null,array("removeFrom" => $targetFolderId));
		$obj = $this->extractObject($ret->body);
		$this->cacheObjectInfo($obj);
		return $obj;
	}

	//Policy Services
	function getAppliedPolicies() {
		throw new CmisNotImplementedException("getAppliedPolicies");
	}

	function applyPolicy() {
		throw new CmisNotImplementedException("applyPolicy");
	}

	function removePolicy() {
		throw new CmisNotImplementedException("removePolicy");
	}

	//ACL Services
	function getACL() {
		throw new CmisNotImplementedException("getACL");
	}

	function applyACL() {
		throw new CmisNotImplementedException("applyACL");
	}
}