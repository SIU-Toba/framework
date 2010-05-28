/**
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements. See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership. The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied. See the License for the
 * specific language governing permissions and limitations under the License.
 */

/**
 * @fileoverview Base OSAPI binding
 */

var osapi = osapi || {};

/**
 * Container-side binding for the gadgetsrpctransport used by osapi. Containers
 * add services to the client-side osapi implementation by defining them in the osapi
 * namespace
 */
if (gadgets && gadgets.rpc) { //Only define if gadgets rpc exists

  /**
   * Dispatch a JSON-RPC batch request to services defined in the osapi namespace
   * @param callbackId
   * @param requests
   */
  osapi._handleGadgetRpcMethod = function(requests) {
    var responses = new Array(requests.length);
    var callCount = 0;
    var callback = this.callback;
    var dummy = function(params, apiCallback) {
      apiCallback({});
    };
    for (var i = 0; i < requests.length; i++) {
      // Don't allow underscores in any part of the method name as a convention
      // for restricted methods
      var current = osapi;
      if (requests[i].method.indexOf("_") == -1) {
        var path = requests[i].method.split(".");
        for (var j = 0; j < path.length; j++) {
          if (current.hasOwnProperty(path[j])) {
            current = current[path[j]];
          } else {
            // No matching api
            current = dummy;
            break;
          }
        }
      } else {
        current = dummy;
      }

      // Execute the call and latch the rpc callback until all
      // complete
      current(requests[i].params, function(i) {
        return function(response) {
          // Put back in json-rpc format
          responses[i] = { id : requests[i].id, data : response};
          callCount++;
          if (callCount == requests.length) {
            callback(responses);
          }
        };
      }(i));
    }
  };

  /**
   * Basic implementation of system.listMethods which can be used to introspect
   * available services
   * @param request
   * @param callback
   */
  osapi.container = {};
  osapi.container["listMethods"] = function(request, callback) {
    var names = [];
    recurseNames(osapi, "", 5, names)
    callback(names);
  };

  /**
   * Recurse the object paths to a limited depth
   */
  function recurseNames(base, path, depth, accumulated) {
    if (depth == 0) return;
    for (var prop in base) if (base.hasOwnProperty(prop)) {
      if (prop.indexOf("_") == -1) {
        var type = typeof(base[prop]);
        if (type == "function") {
          accumulated.push(path + prop);
        } else if (type == "object") {
          recurseNames(base[prop], path + prop + ".", depth - 1, accumulated);
        }
      }
    }
  }

  // Register the osapi RPC dispatcher.
  gadgets.rpc.register("osapi._handleGadgetRpcMethod", osapi._handleGadgetRpcMethod);
}