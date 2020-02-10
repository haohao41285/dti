webpackJsonp([0],{

/***/ "./node_modules/axios/index.js":
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__("./node_modules/axios/lib/axios.js");

/***/ }),

/***/ "./node_modules/axios/lib/adapters/xhr.js":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__("./node_modules/axios/lib/utils.js");
var settle = __webpack_require__("./node_modules/axios/lib/core/settle.js");
var buildURL = __webpack_require__("./node_modules/axios/lib/helpers/buildURL.js");
var buildFullPath = __webpack_require__("./node_modules/axios/lib/core/buildFullPath.js");
var parseHeaders = __webpack_require__("./node_modules/axios/lib/helpers/parseHeaders.js");
var isURLSameOrigin = __webpack_require__("./node_modules/axios/lib/helpers/isURLSameOrigin.js");
var createError = __webpack_require__("./node_modules/axios/lib/core/createError.js");

module.exports = function xhrAdapter(config) {
  return new Promise(function dispatchXhrRequest(resolve, reject) {
    var requestData = config.data;
    var requestHeaders = config.headers;

    if (utils.isFormData(requestData)) {
      delete requestHeaders['Content-Type']; // Let the browser set it
    }

    var request = new XMLHttpRequest();

    // HTTP basic authentication
    if (config.auth) {
      var username = config.auth.username || '';
      var password = config.auth.password || '';
      requestHeaders.Authorization = 'Basic ' + btoa(username + ':' + password);
    }

    var fullPath = buildFullPath(config.baseURL, config.url);
    request.open(config.method.toUpperCase(), buildURL(fullPath, config.params, config.paramsSerializer), true);

    // Set the request timeout in MS
    request.timeout = config.timeout;

    // Listen for ready state
    request.onreadystatechange = function handleLoad() {
      if (!request || request.readyState !== 4) {
        return;
      }

      // The request errored out and we didn't get a response, this will be
      // handled by onerror instead
      // With one exception: request that using file: protocol, most browsers
      // will return status as 0 even though it's a successful request
      if (request.status === 0 && !(request.responseURL && request.responseURL.indexOf('file:') === 0)) {
        return;
      }

      // Prepare the response
      var responseHeaders = 'getAllResponseHeaders' in request ? parseHeaders(request.getAllResponseHeaders()) : null;
      var responseData = !config.responseType || config.responseType === 'text' ? request.responseText : request.response;
      var response = {
        data: responseData,
        status: request.status,
        statusText: request.statusText,
        headers: responseHeaders,
        config: config,
        request: request
      };

      settle(resolve, reject, response);

      // Clean up request
      request = null;
    };

    // Handle browser request cancellation (as opposed to a manual cancellation)
    request.onabort = function handleAbort() {
      if (!request) {
        return;
      }

      reject(createError('Request aborted', config, 'ECONNABORTED', request));

      // Clean up request
      request = null;
    };

    // Handle low level network errors
    request.onerror = function handleError() {
      // Real errors are hidden from us by the browser
      // onerror should only fire if it's a network error
      reject(createError('Network Error', config, null, request));

      // Clean up request
      request = null;
    };

    // Handle timeout
    request.ontimeout = function handleTimeout() {
      var timeoutErrorMessage = 'timeout of ' + config.timeout + 'ms exceeded';
      if (config.timeoutErrorMessage) {
        timeoutErrorMessage = config.timeoutErrorMessage;
      }
      reject(createError(timeoutErrorMessage, config, 'ECONNABORTED',
        request));

      // Clean up request
      request = null;
    };

    // Add xsrf header
    // This is only done if running in a standard browser environment.
    // Specifically not if we're in a web worker, or react-native.
    if (utils.isStandardBrowserEnv()) {
      var cookies = __webpack_require__("./node_modules/axios/lib/helpers/cookies.js");

      // Add xsrf header
      var xsrfValue = (config.withCredentials || isURLSameOrigin(fullPath)) && config.xsrfCookieName ?
        cookies.read(config.xsrfCookieName) :
        undefined;

      if (xsrfValue) {
        requestHeaders[config.xsrfHeaderName] = xsrfValue;
      }
    }

    // Add headers to the request
    if ('setRequestHeader' in request) {
      utils.forEach(requestHeaders, function setRequestHeader(val, key) {
        if (typeof requestData === 'undefined' && key.toLowerCase() === 'content-type') {
          // Remove Content-Type if data is undefined
          delete requestHeaders[key];
        } else {
          // Otherwise add header to the request
          request.setRequestHeader(key, val);
        }
      });
    }

    // Add withCredentials to request if needed
    if (!utils.isUndefined(config.withCredentials)) {
      request.withCredentials = !!config.withCredentials;
    }

    // Add responseType to request if needed
    if (config.responseType) {
      try {
        request.responseType = config.responseType;
      } catch (e) {
        // Expected DOMException thrown by browsers not compatible XMLHttpRequest Level 2.
        // But, this can be suppressed for 'json' type as it can be parsed by default 'transformResponse' function.
        if (config.responseType !== 'json') {
          throw e;
        }
      }
    }

    // Handle progress if needed
    if (typeof config.onDownloadProgress === 'function') {
      request.addEventListener('progress', config.onDownloadProgress);
    }

    // Not all browsers support upload events
    if (typeof config.onUploadProgress === 'function' && request.upload) {
      request.upload.addEventListener('progress', config.onUploadProgress);
    }

    if (config.cancelToken) {
      // Handle cancellation
      config.cancelToken.promise.then(function onCanceled(cancel) {
        if (!request) {
          return;
        }

        request.abort();
        reject(cancel);
        // Clean up request
        request = null;
      });
    }

    if (requestData === undefined) {
      requestData = null;
    }

    // Send the request
    request.send(requestData);
  });
};


/***/ }),

/***/ "./node_modules/axios/lib/axios.js":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__("./node_modules/axios/lib/utils.js");
var bind = __webpack_require__("./node_modules/axios/lib/helpers/bind.js");
var Axios = __webpack_require__("./node_modules/axios/lib/core/Axios.js");
var mergeConfig = __webpack_require__("./node_modules/axios/lib/core/mergeConfig.js");
var defaults = __webpack_require__("./node_modules/axios/lib/defaults.js");

/**
 * Create an instance of Axios
 *
 * @param {Object} defaultConfig The default config for the instance
 * @return {Axios} A new instance of Axios
 */
function createInstance(defaultConfig) {
  var context = new Axios(defaultConfig);
  var instance = bind(Axios.prototype.request, context);

  // Copy axios.prototype to instance
  utils.extend(instance, Axios.prototype, context);

  // Copy context to instance
  utils.extend(instance, context);

  return instance;
}

// Create the default instance to be exported
var axios = createInstance(defaults);

// Expose Axios class to allow class inheritance
axios.Axios = Axios;

// Factory for creating new instances
axios.create = function create(instanceConfig) {
  return createInstance(mergeConfig(axios.defaults, instanceConfig));
};

// Expose Cancel & CancelToken
axios.Cancel = __webpack_require__("./node_modules/axios/lib/cancel/Cancel.js");
axios.CancelToken = __webpack_require__("./node_modules/axios/lib/cancel/CancelToken.js");
axios.isCancel = __webpack_require__("./node_modules/axios/lib/cancel/isCancel.js");

// Expose all/spread
axios.all = function all(promises) {
  return Promise.all(promises);
};
axios.spread = __webpack_require__("./node_modules/axios/lib/helpers/spread.js");

module.exports = axios;

// Allow use of default import syntax in TypeScript
module.exports.default = axios;


/***/ }),

/***/ "./node_modules/axios/lib/cancel/Cancel.js":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


/**
 * A `Cancel` is an object that is thrown when an operation is canceled.
 *
 * @class
 * @param {string=} message The message.
 */
function Cancel(message) {
  this.message = message;
}

Cancel.prototype.toString = function toString() {
  return 'Cancel' + (this.message ? ': ' + this.message : '');
};

Cancel.prototype.__CANCEL__ = true;

module.exports = Cancel;


/***/ }),

/***/ "./node_modules/axios/lib/cancel/CancelToken.js":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var Cancel = __webpack_require__("./node_modules/axios/lib/cancel/Cancel.js");

/**
 * A `CancelToken` is an object that can be used to request cancellation of an operation.
 *
 * @class
 * @param {Function} executor The executor function.
 */
function CancelToken(executor) {
  if (typeof executor !== 'function') {
    throw new TypeError('executor must be a function.');
  }

  var resolvePromise;
  this.promise = new Promise(function promiseExecutor(resolve) {
    resolvePromise = resolve;
  });

  var token = this;
  executor(function cancel(message) {
    if (token.reason) {
      // Cancellation has already been requested
      return;
    }

    token.reason = new Cancel(message);
    resolvePromise(token.reason);
  });
}

/**
 * Throws a `Cancel` if cancellation has been requested.
 */
CancelToken.prototype.throwIfRequested = function throwIfRequested() {
  if (this.reason) {
    throw this.reason;
  }
};

/**
 * Returns an object that contains a new `CancelToken` and a function that, when called,
 * cancels the `CancelToken`.
 */
CancelToken.source = function source() {
  var cancel;
  var token = new CancelToken(function executor(c) {
    cancel = c;
  });
  return {
    token: token,
    cancel: cancel
  };
};

module.exports = CancelToken;


/***/ }),

/***/ "./node_modules/axios/lib/cancel/isCancel.js":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


module.exports = function isCancel(value) {
  return !!(value && value.__CANCEL__);
};


/***/ }),

/***/ "./node_modules/axios/lib/core/Axios.js":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__("./node_modules/axios/lib/utils.js");
var buildURL = __webpack_require__("./node_modules/axios/lib/helpers/buildURL.js");
var InterceptorManager = __webpack_require__("./node_modules/axios/lib/core/InterceptorManager.js");
var dispatchRequest = __webpack_require__("./node_modules/axios/lib/core/dispatchRequest.js");
var mergeConfig = __webpack_require__("./node_modules/axios/lib/core/mergeConfig.js");

/**
 * Create a new instance of Axios
 *
 * @param {Object} instanceConfig The default config for the instance
 */
function Axios(instanceConfig) {
  this.defaults = instanceConfig;
  this.interceptors = {
    request: new InterceptorManager(),
    response: new InterceptorManager()
  };
}

/**
 * Dispatch a request
 *
 * @param {Object} config The config specific for this request (merged with this.defaults)
 */
Axios.prototype.request = function request(config) {
  /*eslint no-param-reassign:0*/
  // Allow for axios('example/url'[, config]) a la fetch API
  if (typeof config === 'string') {
    config = arguments[1] || {};
    config.url = arguments[0];
  } else {
    config = config || {};
  }

  config = mergeConfig(this.defaults, config);

  // Set config.method
  if (config.method) {
    config.method = config.method.toLowerCase();
  } else if (this.defaults.method) {
    config.method = this.defaults.method.toLowerCase();
  } else {
    config.method = 'get';
  }

  // Hook up interceptors middleware
  var chain = [dispatchRequest, undefined];
  var promise = Promise.resolve(config);

  this.interceptors.request.forEach(function unshiftRequestInterceptors(interceptor) {
    chain.unshift(interceptor.fulfilled, interceptor.rejected);
  });

  this.interceptors.response.forEach(function pushResponseInterceptors(interceptor) {
    chain.push(interceptor.fulfilled, interceptor.rejected);
  });

  while (chain.length) {
    promise = promise.then(chain.shift(), chain.shift());
  }

  return promise;
};

Axios.prototype.getUri = function getUri(config) {
  config = mergeConfig(this.defaults, config);
  return buildURL(config.url, config.params, config.paramsSerializer).replace(/^\?/, '');
};

// Provide aliases for supported request methods
utils.forEach(['delete', 'get', 'head', 'options'], function forEachMethodNoData(method) {
  /*eslint func-names:0*/
  Axios.prototype[method] = function(url, config) {
    return this.request(utils.merge(config || {}, {
      method: method,
      url: url
    }));
  };
});

utils.forEach(['post', 'put', 'patch'], function forEachMethodWithData(method) {
  /*eslint func-names:0*/
  Axios.prototype[method] = function(url, data, config) {
    return this.request(utils.merge(config || {}, {
      method: method,
      url: url,
      data: data
    }));
  };
});

module.exports = Axios;


/***/ }),

/***/ "./node_modules/axios/lib/core/InterceptorManager.js":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__("./node_modules/axios/lib/utils.js");

function InterceptorManager() {
  this.handlers = [];
}

/**
 * Add a new interceptor to the stack
 *
 * @param {Function} fulfilled The function to handle `then` for a `Promise`
 * @param {Function} rejected The function to handle `reject` for a `Promise`
 *
 * @return {Number} An ID used to remove interceptor later
 */
InterceptorManager.prototype.use = function use(fulfilled, rejected) {
  this.handlers.push({
    fulfilled: fulfilled,
    rejected: rejected
  });
  return this.handlers.length - 1;
};

/**
 * Remove an interceptor from the stack
 *
 * @param {Number} id The ID that was returned by `use`
 */
InterceptorManager.prototype.eject = function eject(id) {
  if (this.handlers[id]) {
    this.handlers[id] = null;
  }
};

/**
 * Iterate over all the registered interceptors
 *
 * This method is particularly useful for skipping over any
 * interceptors that may have become `null` calling `eject`.
 *
 * @param {Function} fn The function to call for each interceptor
 */
InterceptorManager.prototype.forEach = function forEach(fn) {
  utils.forEach(this.handlers, function forEachHandler(h) {
    if (h !== null) {
      fn(h);
    }
  });
};

module.exports = InterceptorManager;


/***/ }),

/***/ "./node_modules/axios/lib/core/buildFullPath.js":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var isAbsoluteURL = __webpack_require__("./node_modules/axios/lib/helpers/isAbsoluteURL.js");
var combineURLs = __webpack_require__("./node_modules/axios/lib/helpers/combineURLs.js");

/**
 * Creates a new URL by combining the baseURL with the requestedURL,
 * only when the requestedURL is not already an absolute URL.
 * If the requestURL is absolute, this function returns the requestedURL untouched.
 *
 * @param {string} baseURL The base URL
 * @param {string} requestedURL Absolute or relative URL to combine
 * @returns {string} The combined full path
 */
module.exports = function buildFullPath(baseURL, requestedURL) {
  if (baseURL && !isAbsoluteURL(requestedURL)) {
    return combineURLs(baseURL, requestedURL);
  }
  return requestedURL;
};


/***/ }),

/***/ "./node_modules/axios/lib/core/createError.js":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var enhanceError = __webpack_require__("./node_modules/axios/lib/core/enhanceError.js");

/**
 * Create an Error with the specified message, config, error code, request and response.
 *
 * @param {string} message The error message.
 * @param {Object} config The config.
 * @param {string} [code] The error code (for example, 'ECONNABORTED').
 * @param {Object} [request] The request.
 * @param {Object} [response] The response.
 * @returns {Error} The created error.
 */
module.exports = function createError(message, config, code, request, response) {
  var error = new Error(message);
  return enhanceError(error, config, code, request, response);
};


/***/ }),

/***/ "./node_modules/axios/lib/core/dispatchRequest.js":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__("./node_modules/axios/lib/utils.js");
var transformData = __webpack_require__("./node_modules/axios/lib/core/transformData.js");
var isCancel = __webpack_require__("./node_modules/axios/lib/cancel/isCancel.js");
var defaults = __webpack_require__("./node_modules/axios/lib/defaults.js");

/**
 * Throws a `Cancel` if cancellation has been requested.
 */
function throwIfCancellationRequested(config) {
  if (config.cancelToken) {
    config.cancelToken.throwIfRequested();
  }
}

/**
 * Dispatch a request to the server using the configured adapter.
 *
 * @param {object} config The config that is to be used for the request
 * @returns {Promise} The Promise to be fulfilled
 */
module.exports = function dispatchRequest(config) {
  throwIfCancellationRequested(config);

  // Ensure headers exist
  config.headers = config.headers || {};

  // Transform request data
  config.data = transformData(
    config.data,
    config.headers,
    config.transformRequest
  );

  // Flatten headers
  config.headers = utils.merge(
    config.headers.common || {},
    config.headers[config.method] || {},
    config.headers
  );

  utils.forEach(
    ['delete', 'get', 'head', 'post', 'put', 'patch', 'common'],
    function cleanHeaderConfig(method) {
      delete config.headers[method];
    }
  );

  var adapter = config.adapter || defaults.adapter;

  return adapter(config).then(function onAdapterResolution(response) {
    throwIfCancellationRequested(config);

    // Transform response data
    response.data = transformData(
      response.data,
      response.headers,
      config.transformResponse
    );

    return response;
  }, function onAdapterRejection(reason) {
    if (!isCancel(reason)) {
      throwIfCancellationRequested(config);

      // Transform response data
      if (reason && reason.response) {
        reason.response.data = transformData(
          reason.response.data,
          reason.response.headers,
          config.transformResponse
        );
      }
    }

    return Promise.reject(reason);
  });
};


/***/ }),

/***/ "./node_modules/axios/lib/core/enhanceError.js":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


/**
 * Update an Error with the specified config, error code, and response.
 *
 * @param {Error} error The error to update.
 * @param {Object} config The config.
 * @param {string} [code] The error code (for example, 'ECONNABORTED').
 * @param {Object} [request] The request.
 * @param {Object} [response] The response.
 * @returns {Error} The error.
 */
module.exports = function enhanceError(error, config, code, request, response) {
  error.config = config;
  if (code) {
    error.code = code;
  }

  error.request = request;
  error.response = response;
  error.isAxiosError = true;

  error.toJSON = function() {
    return {
      // Standard
      message: this.message,
      name: this.name,
      // Microsoft
      description: this.description,
      number: this.number,
      // Mozilla
      fileName: this.fileName,
      lineNumber: this.lineNumber,
      columnNumber: this.columnNumber,
      stack: this.stack,
      // Axios
      config: this.config,
      code: this.code
    };
  };
  return error;
};


/***/ }),

/***/ "./node_modules/axios/lib/core/mergeConfig.js":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__("./node_modules/axios/lib/utils.js");

/**
 * Config-specific merge-function which creates a new config-object
 * by merging two configuration objects together.
 *
 * @param {Object} config1
 * @param {Object} config2
 * @returns {Object} New object resulting from merging config2 to config1
 */
module.exports = function mergeConfig(config1, config2) {
  // eslint-disable-next-line no-param-reassign
  config2 = config2 || {};
  var config = {};

  var valueFromConfig2Keys = ['url', 'method', 'params', 'data'];
  var mergeDeepPropertiesKeys = ['headers', 'auth', 'proxy'];
  var defaultToConfig2Keys = [
    'baseURL', 'url', 'transformRequest', 'transformResponse', 'paramsSerializer',
    'timeout', 'withCredentials', 'adapter', 'responseType', 'xsrfCookieName',
    'xsrfHeaderName', 'onUploadProgress', 'onDownloadProgress',
    'maxContentLength', 'validateStatus', 'maxRedirects', 'httpAgent',
    'httpsAgent', 'cancelToken', 'socketPath'
  ];

  utils.forEach(valueFromConfig2Keys, function valueFromConfig2(prop) {
    if (typeof config2[prop] !== 'undefined') {
      config[prop] = config2[prop];
    }
  });

  utils.forEach(mergeDeepPropertiesKeys, function mergeDeepProperties(prop) {
    if (utils.isObject(config2[prop])) {
      config[prop] = utils.deepMerge(config1[prop], config2[prop]);
    } else if (typeof config2[prop] !== 'undefined') {
      config[prop] = config2[prop];
    } else if (utils.isObject(config1[prop])) {
      config[prop] = utils.deepMerge(config1[prop]);
    } else if (typeof config1[prop] !== 'undefined') {
      config[prop] = config1[prop];
    }
  });

  utils.forEach(defaultToConfig2Keys, function defaultToConfig2(prop) {
    if (typeof config2[prop] !== 'undefined') {
      config[prop] = config2[prop];
    } else if (typeof config1[prop] !== 'undefined') {
      config[prop] = config1[prop];
    }
  });

  var axiosKeys = valueFromConfig2Keys
    .concat(mergeDeepPropertiesKeys)
    .concat(defaultToConfig2Keys);

  var otherKeys = Object
    .keys(config2)
    .filter(function filterAxiosKeys(key) {
      return axiosKeys.indexOf(key) === -1;
    });

  utils.forEach(otherKeys, function otherKeysDefaultToConfig2(prop) {
    if (typeof config2[prop] !== 'undefined') {
      config[prop] = config2[prop];
    } else if (typeof config1[prop] !== 'undefined') {
      config[prop] = config1[prop];
    }
  });

  return config;
};


/***/ }),

/***/ "./node_modules/axios/lib/core/settle.js":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var createError = __webpack_require__("./node_modules/axios/lib/core/createError.js");

/**
 * Resolve or reject a Promise based on response status.
 *
 * @param {Function} resolve A function that resolves the promise.
 * @param {Function} reject A function that rejects the promise.
 * @param {object} response The response.
 */
module.exports = function settle(resolve, reject, response) {
  var validateStatus = response.config.validateStatus;
  if (!validateStatus || validateStatus(response.status)) {
    resolve(response);
  } else {
    reject(createError(
      'Request failed with status code ' + response.status,
      response.config,
      null,
      response.request,
      response
    ));
  }
};


/***/ }),

/***/ "./node_modules/axios/lib/core/transformData.js":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__("./node_modules/axios/lib/utils.js");

/**
 * Transform the data for a request or a response
 *
 * @param {Object|String} data The data to be transformed
 * @param {Array} headers The headers for the request or response
 * @param {Array|Function} fns A single function or Array of functions
 * @returns {*} The resulting transformed data
 */
module.exports = function transformData(data, headers, fns) {
  /*eslint no-param-reassign:0*/
  utils.forEach(fns, function transform(fn) {
    data = fn(data, headers);
  });

  return data;
};


/***/ }),

/***/ "./node_modules/axios/lib/defaults.js":
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(process) {

var utils = __webpack_require__("./node_modules/axios/lib/utils.js");
var normalizeHeaderName = __webpack_require__("./node_modules/axios/lib/helpers/normalizeHeaderName.js");

var DEFAULT_CONTENT_TYPE = {
  'Content-Type': 'application/x-www-form-urlencoded'
};

function setContentTypeIfUnset(headers, value) {
  if (!utils.isUndefined(headers) && utils.isUndefined(headers['Content-Type'])) {
    headers['Content-Type'] = value;
  }
}

function getDefaultAdapter() {
  var adapter;
  if (typeof XMLHttpRequest !== 'undefined') {
    // For browsers use XHR adapter
    adapter = __webpack_require__("./node_modules/axios/lib/adapters/xhr.js");
  } else if (typeof process !== 'undefined' && Object.prototype.toString.call(process) === '[object process]') {
    // For node use HTTP adapter
    adapter = __webpack_require__("./node_modules/axios/lib/adapters/xhr.js");
  }
  return adapter;
}

var defaults = {
  adapter: getDefaultAdapter(),

  transformRequest: [function transformRequest(data, headers) {
    normalizeHeaderName(headers, 'Accept');
    normalizeHeaderName(headers, 'Content-Type');
    if (utils.isFormData(data) ||
      utils.isArrayBuffer(data) ||
      utils.isBuffer(data) ||
      utils.isStream(data) ||
      utils.isFile(data) ||
      utils.isBlob(data)
    ) {
      return data;
    }
    if (utils.isArrayBufferView(data)) {
      return data.buffer;
    }
    if (utils.isURLSearchParams(data)) {
      setContentTypeIfUnset(headers, 'application/x-www-form-urlencoded;charset=utf-8');
      return data.toString();
    }
    if (utils.isObject(data)) {
      setContentTypeIfUnset(headers, 'application/json;charset=utf-8');
      return JSON.stringify(data);
    }
    return data;
  }],

  transformResponse: [function transformResponse(data) {
    /*eslint no-param-reassign:0*/
    if (typeof data === 'string') {
      try {
        data = JSON.parse(data);
      } catch (e) { /* Ignore */ }
    }
    return data;
  }],

  /**
   * A timeout in milliseconds to abort a request. If set to 0 (default) a
   * timeout is not created.
   */
  timeout: 0,

  xsrfCookieName: 'XSRF-TOKEN',
  xsrfHeaderName: 'X-XSRF-TOKEN',

  maxContentLength: -1,

  validateStatus: function validateStatus(status) {
    return status >= 200 && status < 300;
  }
};

defaults.headers = {
  common: {
    'Accept': 'application/json, text/plain, */*'
  }
};

utils.forEach(['delete', 'get', 'head'], function forEachMethodNoData(method) {
  defaults.headers[method] = {};
});

utils.forEach(['post', 'put', 'patch'], function forEachMethodWithData(method) {
  defaults.headers[method] = utils.merge(DEFAULT_CONTENT_TYPE);
});

module.exports = defaults;

/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__("./node_modules/process/browser.js")))

/***/ }),

/***/ "./node_modules/axios/lib/helpers/bind.js":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


module.exports = function bind(fn, thisArg) {
  return function wrap() {
    var args = new Array(arguments.length);
    for (var i = 0; i < args.length; i++) {
      args[i] = arguments[i];
    }
    return fn.apply(thisArg, args);
  };
};


/***/ }),

/***/ "./node_modules/axios/lib/helpers/buildURL.js":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__("./node_modules/axios/lib/utils.js");

function encode(val) {
  return encodeURIComponent(val).
    replace(/%40/gi, '@').
    replace(/%3A/gi, ':').
    replace(/%24/g, '$').
    replace(/%2C/gi, ',').
    replace(/%20/g, '+').
    replace(/%5B/gi, '[').
    replace(/%5D/gi, ']');
}

/**
 * Build a URL by appending params to the end
 *
 * @param {string} url The base of the url (e.g., http://www.google.com)
 * @param {object} [params] The params to be appended
 * @returns {string} The formatted url
 */
module.exports = function buildURL(url, params, paramsSerializer) {
  /*eslint no-param-reassign:0*/
  if (!params) {
    return url;
  }

  var serializedParams;
  if (paramsSerializer) {
    serializedParams = paramsSerializer(params);
  } else if (utils.isURLSearchParams(params)) {
    serializedParams = params.toString();
  } else {
    var parts = [];

    utils.forEach(params, function serialize(val, key) {
      if (val === null || typeof val === 'undefined') {
        return;
      }

      if (utils.isArray(val)) {
        key = key + '[]';
      } else {
        val = [val];
      }

      utils.forEach(val, function parseValue(v) {
        if (utils.isDate(v)) {
          v = v.toISOString();
        } else if (utils.isObject(v)) {
          v = JSON.stringify(v);
        }
        parts.push(encode(key) + '=' + encode(v));
      });
    });

    serializedParams = parts.join('&');
  }

  if (serializedParams) {
    var hashmarkIndex = url.indexOf('#');
    if (hashmarkIndex !== -1) {
      url = url.slice(0, hashmarkIndex);
    }

    url += (url.indexOf('?') === -1 ? '?' : '&') + serializedParams;
  }

  return url;
};


/***/ }),

/***/ "./node_modules/axios/lib/helpers/combineURLs.js":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


/**
 * Creates a new URL by combining the specified URLs
 *
 * @param {string} baseURL The base URL
 * @param {string} relativeURL The relative URL
 * @returns {string} The combined URL
 */
module.exports = function combineURLs(baseURL, relativeURL) {
  return relativeURL
    ? baseURL.replace(/\/+$/, '') + '/' + relativeURL.replace(/^\/+/, '')
    : baseURL;
};


/***/ }),

/***/ "./node_modules/axios/lib/helpers/cookies.js":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__("./node_modules/axios/lib/utils.js");

module.exports = (
  utils.isStandardBrowserEnv() ?

  // Standard browser envs support document.cookie
    (function standardBrowserEnv() {
      return {
        write: function write(name, value, expires, path, domain, secure) {
          var cookie = [];
          cookie.push(name + '=' + encodeURIComponent(value));

          if (utils.isNumber(expires)) {
            cookie.push('expires=' + new Date(expires).toGMTString());
          }

          if (utils.isString(path)) {
            cookie.push('path=' + path);
          }

          if (utils.isString(domain)) {
            cookie.push('domain=' + domain);
          }

          if (secure === true) {
            cookie.push('secure');
          }

          document.cookie = cookie.join('; ');
        },

        read: function read(name) {
          var match = document.cookie.match(new RegExp('(^|;\\s*)(' + name + ')=([^;]*)'));
          return (match ? decodeURIComponent(match[3]) : null);
        },

        remove: function remove(name) {
          this.write(name, '', Date.now() - 86400000);
        }
      };
    })() :

  // Non standard browser env (web workers, react-native) lack needed support.
    (function nonStandardBrowserEnv() {
      return {
        write: function write() {},
        read: function read() { return null; },
        remove: function remove() {}
      };
    })()
);


/***/ }),

/***/ "./node_modules/axios/lib/helpers/isAbsoluteURL.js":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


/**
 * Determines whether the specified URL is absolute
 *
 * @param {string} url The URL to test
 * @returns {boolean} True if the specified URL is absolute, otherwise false
 */
module.exports = function isAbsoluteURL(url) {
  // A URL is considered absolute if it begins with "<scheme>://" or "//" (protocol-relative URL).
  // RFC 3986 defines scheme name as a sequence of characters beginning with a letter and followed
  // by any combination of letters, digits, plus, period, or hyphen.
  return /^([a-z][a-z\d\+\-\.]*:)?\/\//i.test(url);
};


/***/ }),

/***/ "./node_modules/axios/lib/helpers/isURLSameOrigin.js":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__("./node_modules/axios/lib/utils.js");

module.exports = (
  utils.isStandardBrowserEnv() ?

  // Standard browser envs have full support of the APIs needed to test
  // whether the request URL is of the same origin as current location.
    (function standardBrowserEnv() {
      var msie = /(msie|trident)/i.test(navigator.userAgent);
      var urlParsingNode = document.createElement('a');
      var originURL;

      /**
    * Parse a URL to discover it's components
    *
    * @param {String} url The URL to be parsed
    * @returns {Object}
    */
      function resolveURL(url) {
        var href = url;

        if (msie) {
        // IE needs attribute set twice to normalize properties
          urlParsingNode.setAttribute('href', href);
          href = urlParsingNode.href;
        }

        urlParsingNode.setAttribute('href', href);

        // urlParsingNode provides the UrlUtils interface - http://url.spec.whatwg.org/#urlutils
        return {
          href: urlParsingNode.href,
          protocol: urlParsingNode.protocol ? urlParsingNode.protocol.replace(/:$/, '') : '',
          host: urlParsingNode.host,
          search: urlParsingNode.search ? urlParsingNode.search.replace(/^\?/, '') : '',
          hash: urlParsingNode.hash ? urlParsingNode.hash.replace(/^#/, '') : '',
          hostname: urlParsingNode.hostname,
          port: urlParsingNode.port,
          pathname: (urlParsingNode.pathname.charAt(0) === '/') ?
            urlParsingNode.pathname :
            '/' + urlParsingNode.pathname
        };
      }

      originURL = resolveURL(window.location.href);

      /**
    * Determine if a URL shares the same origin as the current location
    *
    * @param {String} requestURL The URL to test
    * @returns {boolean} True if URL shares the same origin, otherwise false
    */
      return function isURLSameOrigin(requestURL) {
        var parsed = (utils.isString(requestURL)) ? resolveURL(requestURL) : requestURL;
        return (parsed.protocol === originURL.protocol &&
            parsed.host === originURL.host);
      };
    })() :

  // Non standard browser envs (web workers, react-native) lack needed support.
    (function nonStandardBrowserEnv() {
      return function isURLSameOrigin() {
        return true;
      };
    })()
);


/***/ }),

/***/ "./node_modules/axios/lib/helpers/normalizeHeaderName.js":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__("./node_modules/axios/lib/utils.js");

module.exports = function normalizeHeaderName(headers, normalizedName) {
  utils.forEach(headers, function processHeader(value, name) {
    if (name !== normalizedName && name.toUpperCase() === normalizedName.toUpperCase()) {
      headers[normalizedName] = value;
      delete headers[name];
    }
  });
};


/***/ }),

/***/ "./node_modules/axios/lib/helpers/parseHeaders.js":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__("./node_modules/axios/lib/utils.js");

// Headers whose duplicates are ignored by node
// c.f. https://nodejs.org/api/http.html#http_message_headers
var ignoreDuplicateOf = [
  'age', 'authorization', 'content-length', 'content-type', 'etag',
  'expires', 'from', 'host', 'if-modified-since', 'if-unmodified-since',
  'last-modified', 'location', 'max-forwards', 'proxy-authorization',
  'referer', 'retry-after', 'user-agent'
];

/**
 * Parse headers into an object
 *
 * ```
 * Date: Wed, 27 Aug 2014 08:58:49 GMT
 * Content-Type: application/json
 * Connection: keep-alive
 * Transfer-Encoding: chunked
 * ```
 *
 * @param {String} headers Headers needing to be parsed
 * @returns {Object} Headers parsed into an object
 */
module.exports = function parseHeaders(headers) {
  var parsed = {};
  var key;
  var val;
  var i;

  if (!headers) { return parsed; }

  utils.forEach(headers.split('\n'), function parser(line) {
    i = line.indexOf(':');
    key = utils.trim(line.substr(0, i)).toLowerCase();
    val = utils.trim(line.substr(i + 1));

    if (key) {
      if (parsed[key] && ignoreDuplicateOf.indexOf(key) >= 0) {
        return;
      }
      if (key === 'set-cookie') {
        parsed[key] = (parsed[key] ? parsed[key] : []).concat([val]);
      } else {
        parsed[key] = parsed[key] ? parsed[key] + ', ' + val : val;
      }
    }
  });

  return parsed;
};


/***/ }),

/***/ "./node_modules/axios/lib/helpers/spread.js":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


/**
 * Syntactic sugar for invoking a function and expanding an array for arguments.
 *
 * Common use case would be to use `Function.prototype.apply`.
 *
 *  ```js
 *  function f(x, y, z) {}
 *  var args = [1, 2, 3];
 *  f.apply(null, args);
 *  ```
 *
 * With `spread` this example can be re-written.
 *
 *  ```js
 *  spread(function(x, y, z) {})([1, 2, 3]);
 *  ```
 *
 * @param {Function} callback
 * @returns {Function}
 */
module.exports = function spread(callback) {
  return function wrap(arr) {
    return callback.apply(null, arr);
  };
};


/***/ }),

/***/ "./node_modules/axios/lib/utils.js":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var bind = __webpack_require__("./node_modules/axios/lib/helpers/bind.js");

/*global toString:true*/

// utils is a library of generic helper functions non-specific to axios

var toString = Object.prototype.toString;

/**
 * Determine if a value is an Array
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is an Array, otherwise false
 */
function isArray(val) {
  return toString.call(val) === '[object Array]';
}

/**
 * Determine if a value is undefined
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if the value is undefined, otherwise false
 */
function isUndefined(val) {
  return typeof val === 'undefined';
}

/**
 * Determine if a value is a Buffer
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Buffer, otherwise false
 */
function isBuffer(val) {
  return val !== null && !isUndefined(val) && val.constructor !== null && !isUndefined(val.constructor)
    && typeof val.constructor.isBuffer === 'function' && val.constructor.isBuffer(val);
}

/**
 * Determine if a value is an ArrayBuffer
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is an ArrayBuffer, otherwise false
 */
function isArrayBuffer(val) {
  return toString.call(val) === '[object ArrayBuffer]';
}

/**
 * Determine if a value is a FormData
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is an FormData, otherwise false
 */
function isFormData(val) {
  return (typeof FormData !== 'undefined') && (val instanceof FormData);
}

/**
 * Determine if a value is a view on an ArrayBuffer
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a view on an ArrayBuffer, otherwise false
 */
function isArrayBufferView(val) {
  var result;
  if ((typeof ArrayBuffer !== 'undefined') && (ArrayBuffer.isView)) {
    result = ArrayBuffer.isView(val);
  } else {
    result = (val) && (val.buffer) && (val.buffer instanceof ArrayBuffer);
  }
  return result;
}

/**
 * Determine if a value is a String
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a String, otherwise false
 */
function isString(val) {
  return typeof val === 'string';
}

/**
 * Determine if a value is a Number
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Number, otherwise false
 */
function isNumber(val) {
  return typeof val === 'number';
}

/**
 * Determine if a value is an Object
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is an Object, otherwise false
 */
function isObject(val) {
  return val !== null && typeof val === 'object';
}

/**
 * Determine if a value is a Date
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Date, otherwise false
 */
function isDate(val) {
  return toString.call(val) === '[object Date]';
}

/**
 * Determine if a value is a File
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a File, otherwise false
 */
function isFile(val) {
  return toString.call(val) === '[object File]';
}

/**
 * Determine if a value is a Blob
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Blob, otherwise false
 */
function isBlob(val) {
  return toString.call(val) === '[object Blob]';
}

/**
 * Determine if a value is a Function
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Function, otherwise false
 */
function isFunction(val) {
  return toString.call(val) === '[object Function]';
}

/**
 * Determine if a value is a Stream
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Stream, otherwise false
 */
function isStream(val) {
  return isObject(val) && isFunction(val.pipe);
}

/**
 * Determine if a value is a URLSearchParams object
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a URLSearchParams object, otherwise false
 */
function isURLSearchParams(val) {
  return typeof URLSearchParams !== 'undefined' && val instanceof URLSearchParams;
}

/**
 * Trim excess whitespace off the beginning and end of a string
 *
 * @param {String} str The String to trim
 * @returns {String} The String freed of excess whitespace
 */
function trim(str) {
  return str.replace(/^\s*/, '').replace(/\s*$/, '');
}

/**
 * Determine if we're running in a standard browser environment
 *
 * This allows axios to run in a web worker, and react-native.
 * Both environments support XMLHttpRequest, but not fully standard globals.
 *
 * web workers:
 *  typeof window -> undefined
 *  typeof document -> undefined
 *
 * react-native:
 *  navigator.product -> 'ReactNative'
 * nativescript
 *  navigator.product -> 'NativeScript' or 'NS'
 */
function isStandardBrowserEnv() {
  if (typeof navigator !== 'undefined' && (navigator.product === 'ReactNative' ||
                                           navigator.product === 'NativeScript' ||
                                           navigator.product === 'NS')) {
    return false;
  }
  return (
    typeof window !== 'undefined' &&
    typeof document !== 'undefined'
  );
}

/**
 * Iterate over an Array or an Object invoking a function for each item.
 *
 * If `obj` is an Array callback will be called passing
 * the value, index, and complete array for each item.
 *
 * If 'obj' is an Object callback will be called passing
 * the value, key, and complete object for each property.
 *
 * @param {Object|Array} obj The object to iterate
 * @param {Function} fn The callback to invoke for each item
 */
function forEach(obj, fn) {
  // Don't bother if no value provided
  if (obj === null || typeof obj === 'undefined') {
    return;
  }

  // Force an array if not already something iterable
  if (typeof obj !== 'object') {
    /*eslint no-param-reassign:0*/
    obj = [obj];
  }

  if (isArray(obj)) {
    // Iterate over array values
    for (var i = 0, l = obj.length; i < l; i++) {
      fn.call(null, obj[i], i, obj);
    }
  } else {
    // Iterate over object keys
    for (var key in obj) {
      if (Object.prototype.hasOwnProperty.call(obj, key)) {
        fn.call(null, obj[key], key, obj);
      }
    }
  }
}

/**
 * Accepts varargs expecting each argument to be an object, then
 * immutably merges the properties of each object and returns result.
 *
 * When multiple objects contain the same key the later object in
 * the arguments list will take precedence.
 *
 * Example:
 *
 * ```js
 * var result = merge({foo: 123}, {foo: 456});
 * console.log(result.foo); // outputs 456
 * ```
 *
 * @param {Object} obj1 Object to merge
 * @returns {Object} Result of all merge properties
 */
function merge(/* obj1, obj2, obj3, ... */) {
  var result = {};
  function assignValue(val, key) {
    if (typeof result[key] === 'object' && typeof val === 'object') {
      result[key] = merge(result[key], val);
    } else {
      result[key] = val;
    }
  }

  for (var i = 0, l = arguments.length; i < l; i++) {
    forEach(arguments[i], assignValue);
  }
  return result;
}

/**
 * Function equal to merge with the difference being that no reference
 * to original objects is kept.
 *
 * @see merge
 * @param {Object} obj1 Object to merge
 * @returns {Object} Result of all merge properties
 */
function deepMerge(/* obj1, obj2, obj3, ... */) {
  var result = {};
  function assignValue(val, key) {
    if (typeof result[key] === 'object' && typeof val === 'object') {
      result[key] = deepMerge(result[key], val);
    } else if (typeof val === 'object') {
      result[key] = deepMerge({}, val);
    } else {
      result[key] = val;
    }
  }

  for (var i = 0, l = arguments.length; i < l; i++) {
    forEach(arguments[i], assignValue);
  }
  return result;
}

/**
 * Extends object a by mutably adding to it the properties of object b.
 *
 * @param {Object} a The object to be extended
 * @param {Object} b The object to copy properties from
 * @param {Object} thisArg The object to bind function to
 * @return {Object} The resulting value of object a
 */
function extend(a, b, thisArg) {
  forEach(b, function assignValue(val, key) {
    if (thisArg && typeof val === 'function') {
      a[key] = bind(val, thisArg);
    } else {
      a[key] = val;
    }
  });
  return a;
}

module.exports = {
  isArray: isArray,
  isArrayBuffer: isArrayBuffer,
  isBuffer: isBuffer,
  isFormData: isFormData,
  isArrayBufferView: isArrayBufferView,
  isString: isString,
  isNumber: isNumber,
  isObject: isObject,
  isUndefined: isUndefined,
  isDate: isDate,
  isFile: isFile,
  isBlob: isBlob,
  isFunction: isFunction,
  isStream: isStream,
  isURLSearchParams: isURLSearchParams,
  isStandardBrowserEnv: isStandardBrowserEnv,
  forEach: forEach,
  merge: merge,
  deepMerge: deepMerge,
  extend: extend,
  trim: trim
};


/***/ }),

/***/ "./node_modules/bootstrap-multiselect/dist/js/bootstrap-multiselect.js":
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(__webpack_provided_window_dot_jQuery) {/**
 * Bootstrap Multiselect (https://github.com/davidstutz/bootstrap-multiselect)
 * 
 * Apache License, Version 2.0:
 * Copyright (c) 2012 - 2015 David Stutz
 * 
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a
 * copy of the License at http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 * 
 * BSD 3-Clause License:
 * Copyright (c) 2012 - 2015 David Stutz
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *    - Redistributions of source code must retain the above copyright notice,
 *      this list of conditions and the following disclaimer.
 *    - Redistributions in binary form must reproduce the above copyright notice,
 *      this list of conditions and the following disclaimer in the documentation
 *      and/or other materials provided with the distribution.
 *    - Neither the name of David Stutz nor the names of its contributors may be
 *      used to endorse or promote products derived from this software without
 *      specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS;
 * OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR
 * OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF
 * ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
!function ($) {
    "use strict";// jshint ;_;

    if (typeof ko !== 'undefined' && ko.bindingHandlers && !ko.bindingHandlers.multiselect) {
        ko.bindingHandlers.multiselect = {
            after: ['options', 'value', 'selectedOptions'],

            init: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
                var $element = $(element);
                var config = ko.toJS(valueAccessor());

                $element.multiselect(config);

                if (allBindings.has('options')) {
                    var options = allBindings.get('options');
                    if (ko.isObservable(options)) {
                        ko.computed({
                            read: function() {
                                options();
                                setTimeout(function() {
                                    var ms = $element.data('multiselect');
                                    if (ms)
                                        ms.updateOriginalOptions();//Not sure how beneficial this is.
                                    $element.multiselect('rebuild');
                                }, 1);
                            },
                            disposeWhenNodeIsRemoved: element
                        });
                    }
                }

                //value and selectedOptions are two-way, so these will be triggered even by our own actions.
                //It needs some way to tell if they are triggered because of us or because of outside change.
                //It doesn't loop but it's a waste of processing.
                if (allBindings.has('value')) {
                    var value = allBindings.get('value');
                    if (ko.isObservable(value)) {
                        ko.computed({
                            read: function() {
                                value();
                                setTimeout(function() {
                                    $element.multiselect('refresh');
                                }, 1);
                            },
                            disposeWhenNodeIsRemoved: element
                        }).extend({ rateLimit: 100, notifyWhenChangesStop: true });
                    }
                }

                //Switched from arrayChange subscription to general subscription using 'refresh'.
                //Not sure performance is any better using 'select' and 'deselect'.
                if (allBindings.has('selectedOptions')) {
                    var selectedOptions = allBindings.get('selectedOptions');
                    if (ko.isObservable(selectedOptions)) {
                        ko.computed({
                            read: function() {
                                selectedOptions();
                                setTimeout(function() {
                                    $element.multiselect('refresh');
                                }, 1);
                            },
                            disposeWhenNodeIsRemoved: element
                        }).extend({ rateLimit: 100, notifyWhenChangesStop: true });
                    }
                }

                ko.utils.domNodeDisposal.addDisposeCallback(element, function() {
                    $element.multiselect('destroy');
                });
            },

            update: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
                var $element = $(element);
                var config = ko.toJS(valueAccessor());

                $element.multiselect('setOptions', config);
                $element.multiselect('rebuild');
            }
        };
    }

    function forEach(array, callback) {
        for (var index = 0; index < array.length; ++index) {
            callback(array[index], index);
        }
    }

    /**
     * Constructor to create a new multiselect using the given select.
     *
     * @param {jQuery} select
     * @param {Object} options
     * @returns {Multiselect}
     */
    function Multiselect(select, options) {

        this.$select = $(select);
        
        // Placeholder via data attributes
        if (this.$select.attr("data-placeholder")) {
            options.nonSelectedText = this.$select.data("placeholder");
        }
        
        this.options = this.mergeOptions($.extend({}, options, this.$select.data()));

        // Initialization.
        // We have to clone to create a new reference.
        this.originalOptions = this.$select.clone()[0].options;
        this.query = '';
        this.searchTimeout = null;
        this.lastToggledInput = null

        this.options.multiple = this.$select.attr('multiple') === "multiple";
        this.options.onChange = $.proxy(this.options.onChange, this);
        this.options.onDropdownShow = $.proxy(this.options.onDropdownShow, this);
        this.options.onDropdownHide = $.proxy(this.options.onDropdownHide, this);
        this.options.onDropdownShown = $.proxy(this.options.onDropdownShown, this);
        this.options.onDropdownHidden = $.proxy(this.options.onDropdownHidden, this);
        
        // Build select all if enabled.
        this.buildContainer();
        this.buildButton();
        this.buildDropdown();
        this.buildSelectAll();
        this.buildDropdownOptions();
        this.buildFilter();

        this.updateButtonText();
        this.updateSelectAll();

        if (this.options.disableIfEmpty && $('option', this.$select).length <= 0) {
            this.disable();
        }
        
        this.$select.hide().after(this.$container);
    };

    Multiselect.prototype = {

        defaults: {
            /**
             * Default text function will either print 'None selected' in case no
             * option is selected or a list of the selected options up to a length
             * of 3 selected options.
             * 
             * @param {jQuery} options
             * @param {jQuery} select
             * @returns {String}
             */
            buttonText: function(options, select) {
                if (options.length === 0) {
                    return this.nonSelectedText;
                }
                else if (this.allSelectedText 
                            && options.length === $('option', $(select)).length 
                            && $('option', $(select)).length !== 1 
                            && this.multiple) {

                    if (this.selectAllNumber) {
                        return this.allSelectedText + ' (' + options.length + ')';
                    }
                    else {
                        return this.allSelectedText;
                    }
                }
                else if (options.length > this.numberDisplayed) {
                    return options.length + ' ' + this.nSelectedText;
                }
                else {
                    var selected = '';
                    var delimiter = this.delimiterText;
                    
                    options.each(function() {
                        var label = ($(this).attr('label') !== undefined) ? $(this).attr('label') : $(this).text();
                        selected += label + delimiter;
                    });
                    
                    return selected.substr(0, selected.length - 2);
                }
            },
            /**
             * Updates the title of the button similar to the buttonText function.
             * 
             * @param {jQuery} options
             * @param {jQuery} select
             * @returns {@exp;selected@call;substr}
             */
            buttonTitle: function(options, select) {
                if (options.length === 0) {
                    return this.nonSelectedText;
                }
                else {
                    var selected = '';
                    var delimiter = this.delimiterText;
                    
                    options.each(function () {
                        var label = ($(this).attr('label') !== undefined) ? $(this).attr('label') : $(this).text();
                        selected += label + delimiter;
                    });
                    return selected.substr(0, selected.length - 2);
                }
            },
            /**
             * Create a label.
             *
             * @param {jQuery} element
             * @returns {String}
             */
            optionLabel: function(element){
                return $(element).attr('label') || $(element).text();
            },
            /**
             * Triggered on change of the multiselect.
             * 
             * Not triggered when selecting/deselecting options manually.
             * 
             * @param {jQuery} option
             * @param {Boolean} checked
             */
            onChange : function(option, checked) {

            },
            /**
             * Triggered when the dropdown is shown.
             *
             * @param {jQuery} event
             */
            onDropdownShow: function(event) {

            },
            /**
             * Triggered when the dropdown is hidden.
             *
             * @param {jQuery} event
             */
            onDropdownHide: function(event) {

            },
            /**
             * Triggered after the dropdown is shown.
             * 
             * @param {jQuery} event
             */
            onDropdownShown: function(event) {
                
            },
            /**
             * Triggered after the dropdown is hidden.
             * 
             * @param {jQuery} event
             */
            onDropdownHidden: function(event) {
                
            },
            /**
             * Triggered on select all.
             */
            onSelectAll: function() {
                
            },
            enableHTML: false,
            buttonClass: 'btn btn-default',
            inheritClass: false,
            buttonWidth: 'auto',
            buttonContainer: '<div class="btn-group" />',
            dropRight: false,
            selectedClass: 'active',
            // Maximum height of the dropdown menu.
            // If maximum height is exceeded a scrollbar will be displayed.
            maxHeight: false,
            checkboxName: false,
            includeSelectAllOption: false,
            includeSelectAllIfMoreThan: 0,
            selectAllText: ' Select all',
            selectAllValue: 'multiselect-all',
            selectAllName: false,
            selectAllNumber: true,
            enableFiltering: false,
            enableCaseInsensitiveFiltering: false,
            enableClickableOptGroups: false,
            filterPlaceholder: 'Search',
            // possible options: 'text', 'value', 'both'
            filterBehavior: 'text',
            includeFilterClearBtn: true,
            preventInputChangeEvent: false,
            nonSelectedText: 'None selected',
            nSelectedText: 'selected',
            allSelectedText: 'All selected',
            numberDisplayed: 3,
            disableIfEmpty: false,
            delimiterText: ', ',
            templates: {
                button: '<button type="button" class="multiselect dropdown-toggle" data-toggle="dropdown"><span class="multiselect-selected-text"></span> <b class="caret"></b></button>',
                ul: '<ul class="multiselect-container dropdown-menu"></ul>',
                filter: '<li class="multiselect-item filter"><div class="input-group"><span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span><input class="form-control multiselect-search" type="text"></div></li>',
                filterClearBtn: '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="glyphicon glyphicon-remove-circle"></i></button></span>',
                li: '<li><a tabindex="0"><label></label></a></li>',
                divider: '<li class="multiselect-item divider"></li>',
                liGroup: '<li class="multiselect-item multiselect-group"><label></label></li>'
            }
        },

        constructor: Multiselect,

        /**
         * Builds the container of the multiselect.
         */
        buildContainer: function() {
            this.$container = $(this.options.buttonContainer);
            this.$container.on('show.bs.dropdown', this.options.onDropdownShow);
            this.$container.on('hide.bs.dropdown', this.options.onDropdownHide);
            this.$container.on('shown.bs.dropdown', this.options.onDropdownShown);
            this.$container.on('hidden.bs.dropdown', this.options.onDropdownHidden);
        },

        /**
         * Builds the button of the multiselect.
         */
        buildButton: function() {
            this.$button = $(this.options.templates.button).addClass(this.options.buttonClass);
            if (this.$select.attr('class') && this.options.inheritClass) {
                this.$button.addClass(this.$select.attr('class'));
            }
            // Adopt active state.
            if (this.$select.prop('disabled')) {
                this.disable();
            }
            else {
                this.enable();
            }

            // Manually add button width if set.
            if (this.options.buttonWidth && this.options.buttonWidth !== 'auto') {
                this.$button.css({
                    'width' : this.options.buttonWidth,
                    'overflow' : 'hidden',
                    'text-overflow' : 'ellipsis'
                });
                this.$container.css({
                    'width': this.options.buttonWidth
                });
            }

            // Keep the tab index from the select.
            var tabindex = this.$select.attr('tabindex');
            if (tabindex) {
                this.$button.attr('tabindex', tabindex);
            }

            this.$container.prepend(this.$button);
        },

        /**
         * Builds the ul representing the dropdown menu.
         */
        buildDropdown: function() {

            // Build ul.
            this.$ul = $(this.options.templates.ul);

            if (this.options.dropRight) {
                this.$ul.addClass('pull-right');
            }

            // Set max height of dropdown menu to activate auto scrollbar.
            if (this.options.maxHeight) {
                // TODO: Add a class for this option to move the css declarations.
                this.$ul.css({
                    'max-height': this.options.maxHeight + 'px',
                    'overflow-y': 'auto',
                    'overflow-x': 'hidden'
                });
            }

            this.$container.append(this.$ul);
        },

        /**
         * Build the dropdown options and binds all nessecary events.
         * 
         * Uses createDivider and createOptionValue to create the necessary options.
         */
        buildDropdownOptions: function() {

            this.$select.children().each($.proxy(function(index, element) {

                var $element = $(element);
                // Support optgroups and options without a group simultaneously.
                var tag = $element.prop('tagName')
                    .toLowerCase();
            
                if ($element.prop('value') === this.options.selectAllValue) {
                    return;
                }

                if (tag === 'optgroup') {
                    this.createOptgroup(element);
                }
                else if (tag === 'option') {

                    if ($element.data('role') === 'divider') {
                        this.createDivider();
                    }
                    else {
                        this.createOptionValue(element);
                    }

                }

                // Other illegal tags will be ignored.
            }, this));

            // Bind the change event on the dropdown elements.
            $('li input', this.$ul).on('change', $.proxy(function(event) {
                var $target = $(event.target);

                var checked = $target.prop('checked') || false;
                var isSelectAllOption = $target.val() === this.options.selectAllValue;

                // Apply or unapply the configured selected class.
                if (this.options.selectedClass) {
                    if (checked) {
                        $target.closest('li')
                            .addClass(this.options.selectedClass);
                    }
                    else {
                        $target.closest('li')
                            .removeClass(this.options.selectedClass);
                    }
                }

                // Get the corresponding option.
                var value = $target.val();
                var $option = this.getOptionByValue(value);

                var $optionsNotThis = $('option', this.$select).not($option);
                var $checkboxesNotThis = $('input', this.$container).not($target);

                if (isSelectAllOption) {
                    if (checked) {
                        this.selectAll();
                    }
                    else {
                        this.deselectAll();
                    }
                }

                if(!isSelectAllOption){
                    if (checked) {
                        $option.prop('selected', true);

                        if (this.options.multiple) {
                            // Simply select additional option.
                            $option.prop('selected', true);
                        }
                        else {
                            // Unselect all other options and corresponding checkboxes.
                            if (this.options.selectedClass) {
                                $($checkboxesNotThis).closest('li').removeClass(this.options.selectedClass);
                            }

                            $($checkboxesNotThis).prop('checked', false);
                            $optionsNotThis.prop('selected', false);

                            // It's a single selection, so close.
                            this.$button.click();
                        }

                        if (this.options.selectedClass === "active") {
                            $optionsNotThis.closest("a").css("outline", "");
                        }
                    }
                    else {
                        // Unselect option.
                        $option.prop('selected', false);
                    }
                }

                this.$select.change();

                this.updateButtonText();
                this.updateSelectAll();

                this.options.onChange($option, checked);

                if(this.options.preventInputChangeEvent) {
                    return false;
                }
            }, this));

            $('li a', this.$ul).on('mousedown', function(e) {
                if (e.shiftKey) {
                    // Prevent selecting text by Shift+click
                    return false;
                }
            });
        
            $('li a', this.$ul).on('touchstart click', $.proxy(function(event) {
                event.stopPropagation();

                var $target = $(event.target);
                
                if (event.shiftKey && this.options.multiple) {
                    if($target.is("label")){ // Handles checkbox selection manually (see https://github.com/davidstutz/bootstrap-multiselect/issues/431)
                        event.preventDefault();
                        $target = $target.find("input");
                        $target.prop("checked", !$target.prop("checked"));
                    }
                    var checked = $target.prop('checked') || false;

                    if (this.lastToggledInput !== null && this.lastToggledInput !== $target) { // Make sure we actually have a range
                        var from = $target.closest("li").index();
                        var to = this.lastToggledInput.closest("li").index();
                        
                        if (from > to) { // Swap the indices
                            var tmp = to;
                            to = from;
                            from = tmp;
                        }
                        
                        // Make sure we grab all elements since slice excludes the last index
                        ++to;
                        
                        // Change the checkboxes and underlying options
                        var range = this.$ul.find("li").slice(from, to).find("input");
                        
                        range.prop('checked', checked);
                        
                        if (this.options.selectedClass) {
                            range.closest('li')
                                .toggleClass(this.options.selectedClass, checked);
                        }
                        
                        for (var i = 0, j = range.length; i < j; i++) {
                            var $checkbox = $(range[i]);

                            var $option = this.getOptionByValue($checkbox.val());

                            $option.prop('selected', checked);
                        }                   
                    }
                    
                    // Trigger the select "change" event
                    $target.trigger("change");
                }
                
                // Remembers last clicked option
                if($target.is("input") && !$target.closest("li").is(".multiselect-item")){
                    this.lastToggledInput = $target;
                }

                $target.blur();
            }, this));

            // Keyboard support.
            this.$container.off('keydown.multiselect').on('keydown.multiselect', $.proxy(function(event) {
                if ($('input[type="text"]', this.$container).is(':focus')) {
                    return;
                }

                if (event.keyCode === 9 && this.$container.hasClass('open')) {
                    this.$button.click();
                }
                else {
                    var $items = $(this.$container).find("li:not(.divider):not(.disabled) a").filter(":visible");

                    if (!$items.length) {
                        return;
                    }

                    var index = $items.index($items.filter(':focus'));

                    // Navigation up.
                    if (event.keyCode === 38 && index > 0) {
                        index--;
                    }
                    // Navigate down.
                    else if (event.keyCode === 40 && index < $items.length - 1) {
                        index++;
                    }
                    else if (!~index) {
                        index = 0;
                    }

                    var $current = $items.eq(index);
                    $current.focus();

                    if (event.keyCode === 32 || event.keyCode === 13) {
                        var $checkbox = $current.find('input');

                        $checkbox.prop("checked", !$checkbox.prop("checked"));
                        $checkbox.change();
                    }

                    event.stopPropagation();
                    event.preventDefault();
                }
            }, this));

            if(this.options.enableClickableOptGroups && this.options.multiple) {
                $('li.multiselect-group', this.$ul).on('click', $.proxy(function(event) {
                    event.stopPropagation();

                    var group = $(event.target).parent();

                    // Search all option in optgroup
                    var $options = group.nextUntil('li.multiselect-group');
                    var $visibleOptions = $options.filter(":visible:not(.disabled)");

                    // check or uncheck items
                    var allChecked = true;
                    var optionInputs = $visibleOptions.find('input');
                    optionInputs.each(function() {
                        allChecked = allChecked && $(this).prop('checked');
                    });

                    optionInputs.prop('checked', !allChecked).trigger('change');
               }, this));
            }
        },

        /**
         * Create an option using the given select option.
         *
         * @param {jQuery} element
         */
        createOptionValue: function(element) {
            var $element = $(element);
            if ($element.is(':selected')) {
                $element.prop('selected', true);
            }

            // Support the label attribute on options.
            var label = this.options.optionLabel(element);
            var value = $element.val();
            var inputType = this.options.multiple ? "checkbox" : "radio";

            var $li = $(this.options.templates.li);
            var $label = $('label', $li);
            $label.addClass(inputType);

            if (this.options.enableHTML) {
                $label.html(" " + label);
            }
            else {
                $label.text(" " + label);
            }
        
            var $checkbox = $('<input/>').attr('type', inputType);

            if (this.options.checkboxName) {
                $checkbox.attr('name', this.options.checkboxName);
            }
            $label.prepend($checkbox);

            var selected = $element.prop('selected') || false;
            $checkbox.val(value);

            if (value === this.options.selectAllValue) {
                $li.addClass("multiselect-item multiselect-all");
                $checkbox.parent().parent()
                    .addClass('multiselect-all');
            }

            $label.attr('title', $element.attr('title'));

            this.$ul.append($li);

            if ($element.is(':disabled')) {
                $checkbox.attr('disabled', 'disabled')
                    .prop('disabled', true)
                    .closest('a')
                    .attr("tabindex", "-1")
                    .closest('li')
                    .addClass('disabled');
            }

            $checkbox.prop('checked', selected);

            if (selected && this.options.selectedClass) {
                $checkbox.closest('li')
                    .addClass(this.options.selectedClass);
            }
        },

        /**
         * Creates a divider using the given select option.
         *
         * @param {jQuery} element
         */
        createDivider: function(element) {
            var $divider = $(this.options.templates.divider);
            this.$ul.append($divider);
        },

        /**
         * Creates an optgroup.
         *
         * @param {jQuery} group
         */
        createOptgroup: function(group) {
            var groupName = $(group).prop('label');

            // Add a header for the group.
            var $li = $(this.options.templates.liGroup);
            
            if (this.options.enableHTML) {
                $('label', $li).html(groupName);
            }
            else {
                $('label', $li).text(groupName);
            }
            
            if (this.options.enableClickableOptGroups) {
                $li.addClass('multiselect-group-clickable');
            }

            this.$ul.append($li);

            if ($(group).is(':disabled')) {
                $li.addClass('disabled');
            }

            // Add the options of the group.
            $('option', group).each($.proxy(function(index, element) {
                this.createOptionValue(element);
            }, this));
        },

        /**
         * Build the selct all.
         * 
         * Checks if a select all has already been created.
         */
        buildSelectAll: function() {
            if (typeof this.options.selectAllValue === 'number') {
                this.options.selectAllValue = this.options.selectAllValue.toString();
            }
            
            var alreadyHasSelectAll = this.hasSelectAll();

            if (!alreadyHasSelectAll && this.options.includeSelectAllOption && this.options.multiple
                    && $('option', this.$select).length > this.options.includeSelectAllIfMoreThan) {

                // Check whether to add a divider after the select all.
                if (this.options.includeSelectAllDivider) {
                    this.$ul.prepend($(this.options.templates.divider));
                }

                var $li = $(this.options.templates.li);
                $('label', $li).addClass("checkbox");
                
                if (this.options.enableHTML) {
                    $('label', $li).html(" " + this.options.selectAllText);
                }
                else {
                    $('label', $li).text(" " + this.options.selectAllText);
                }
                
                if (this.options.selectAllName) {
                    $('label', $li).prepend('<input type="checkbox" name="' + this.options.selectAllName + '" />');
                }
                else {
                    $('label', $li).prepend('<input type="checkbox" />');
                }
                
                var $checkbox = $('input', $li);
                $checkbox.val(this.options.selectAllValue);

                $li.addClass("multiselect-item multiselect-all");
                $checkbox.parent().parent()
                    .addClass('multiselect-all');

                this.$ul.prepend($li);

                $checkbox.prop('checked', false);
            }
        },

        /**
         * Builds the filter.
         */
        buildFilter: function() {

            // Build filter if filtering OR case insensitive filtering is enabled and the number of options exceeds (or equals) enableFilterLength.
            if (this.options.enableFiltering || this.options.enableCaseInsensitiveFiltering) {
                var enableFilterLength = Math.max(this.options.enableFiltering, this.options.enableCaseInsensitiveFiltering);

                if (this.$select.find('option').length >= enableFilterLength) {

                    this.$filter = $(this.options.templates.filter);
                    $('input', this.$filter).attr('placeholder', this.options.filterPlaceholder);
                    
                    // Adds optional filter clear button
                    if(this.options.includeFilterClearBtn){
                        var clearBtn = $(this.options.templates.filterClearBtn);
                        clearBtn.on('click', $.proxy(function(event){
                            clearTimeout(this.searchTimeout);
                            this.$filter.find('.multiselect-search').val('');
                            $('li', this.$ul).show().removeClass("filter-hidden");
                            this.updateSelectAll();
                        }, this));
                        this.$filter.find('.input-group').append(clearBtn);
                    }
                    
                    this.$ul.prepend(this.$filter);

                    this.$filter.val(this.query).on('click', function(event) {
                        event.stopPropagation();
                    }).on('input keydown', $.proxy(function(event) {
                        // Cancel enter key default behaviour
                        if (event.which === 13) {
                          event.preventDefault();
                        }
                        
                        // This is useful to catch "keydown" events after the browser has updated the control.
                        clearTimeout(this.searchTimeout);

                        this.searchTimeout = this.asyncFunction($.proxy(function() {

                            if (this.query !== event.target.value) {
                                this.query = event.target.value;

                                var currentGroup, currentGroupVisible;
                                $.each($('li', this.$ul), $.proxy(function(index, element) {
                                    var value = $('input', element).length > 0 ? $('input', element).val() : "";
                                    var text = $('label', element).text();

                                    var filterCandidate = '';
                                    if ((this.options.filterBehavior === 'text')) {
                                        filterCandidate = text;
                                    }
                                    else if ((this.options.filterBehavior === 'value')) {
                                        filterCandidate = value;
                                    }
                                    else if (this.options.filterBehavior === 'both') {
                                        filterCandidate = text + '\n' + value;
                                    }

                                    if (value !== this.options.selectAllValue && text) {
                                        // By default lets assume that element is not
                                        // interesting for this search.
                                        var showElement = false;

                                        if (this.options.enableCaseInsensitiveFiltering && filterCandidate.toLowerCase().indexOf(this.query.toLowerCase()) > -1) {
                                            showElement = true;
                                        }
                                        else if (filterCandidate.indexOf(this.query) > -1) {
                                            showElement = true;
                                        }

                                        // Toggle current element (group or group item) according to showElement boolean.
                                        $(element).toggle(showElement).toggleClass('filter-hidden', !showElement);
                                        
                                        // Differentiate groups and group items.
                                        if ($(element).hasClass('multiselect-group')) {
                                            // Remember group status.
                                            currentGroup = element;
                                            currentGroupVisible = showElement;
                                        }
                                        else {
                                            // Show group name when at least one of its items is visible.
                                            if (showElement) {
                                                $(currentGroup).show().removeClass('filter-hidden');
                                            }
                                            
                                            // Show all group items when group name satisfies filter.
                                            if (!showElement && currentGroupVisible) {
                                                $(element).show().removeClass('filter-hidden');
                                            }
                                        }
                                    }
                                }, this));
                            }

                            this.updateSelectAll();
                        }, this), 300, this);
                    }, this));
                }
            }
        },

        /**
         * Unbinds the whole plugin.
         */
        destroy: function() {
            this.$container.remove();
            this.$select.show();
            this.$select.data('multiselect', null);
        },

        /**
         * Refreshs the multiselect based on the selected options of the select.
         */
        refresh: function() {
            $('option', this.$select).each($.proxy(function(index, element) {
                var $input = $('li input', this.$ul).filter(function() {
                    return $(this).val() === $(element).val();
                });

                if ($(element).is(':selected')) {
                    $input.prop('checked', true);

                    if (this.options.selectedClass) {
                        $input.closest('li')
                            .addClass(this.options.selectedClass);
                    }
                }
                else {
                    $input.prop('checked', false);

                    if (this.options.selectedClass) {
                        $input.closest('li')
                            .removeClass(this.options.selectedClass);
                    }
                }

                if ($(element).is(":disabled")) {
                    $input.attr('disabled', 'disabled')
                        .prop('disabled', true)
                        .closest('li')
                        .addClass('disabled');
                }
                else {
                    $input.prop('disabled', false)
                        .closest('li')
                        .removeClass('disabled');
                }
            }, this));

            this.updateButtonText();
            this.updateSelectAll();
        },

        /**
         * Select all options of the given values.
         * 
         * If triggerOnChange is set to true, the on change event is triggered if
         * and only if one value is passed.
         * 
         * @param {Array} selectValues
         * @param {Boolean} triggerOnChange
         */
        select: function(selectValues, triggerOnChange) {
            if(!$.isArray(selectValues)) {
                selectValues = [selectValues];
            }

            for (var i = 0; i < selectValues.length; i++) {
                var value = selectValues[i];

                if (value === null || value === undefined) {
                    continue;
                }

                var $option = this.getOptionByValue(value);
                var $checkbox = this.getInputByValue(value);

                if($option === undefined || $checkbox === undefined) {
                    continue;
                }
                
                if (!this.options.multiple) {
                    this.deselectAll(false);
                }
                
                if (this.options.selectedClass) {
                    $checkbox.closest('li')
                        .addClass(this.options.selectedClass);
                }

                $checkbox.prop('checked', true);
                $option.prop('selected', true);
                
                if (triggerOnChange) {
                    this.options.onChange($option, true);
                }
            }

            this.updateButtonText();
            this.updateSelectAll();
        },

        /**
         * Clears all selected items.
         */
        clearSelection: function () {
            this.deselectAll(false);
            this.updateButtonText();
            this.updateSelectAll();
        },

        /**
         * Deselects all options of the given values.
         * 
         * If triggerOnChange is set to true, the on change event is triggered, if
         * and only if one value is passed.
         * 
         * @param {Array} deselectValues
         * @param {Boolean} triggerOnChange
         */
        deselect: function(deselectValues, triggerOnChange) {
            if(!$.isArray(deselectValues)) {
                deselectValues = [deselectValues];
            }

            for (var i = 0; i < deselectValues.length; i++) {
                var value = deselectValues[i];

                if (value === null || value === undefined) {
                    continue;
                }

                var $option = this.getOptionByValue(value);
                var $checkbox = this.getInputByValue(value);

                if($option === undefined || $checkbox === undefined) {
                    continue;
                }

                if (this.options.selectedClass) {
                    $checkbox.closest('li')
                        .removeClass(this.options.selectedClass);
                }

                $checkbox.prop('checked', false);
                $option.prop('selected', false);
                
                if (triggerOnChange) {
                    this.options.onChange($option, false);
                }
            }

            this.updateButtonText();
            this.updateSelectAll();
        },
        
        /**
         * Selects all enabled & visible options.
         *
         * If justVisible is true or not specified, only visible options are selected.
         *
         * @param {Boolean} justVisible
         * @param {Boolean} triggerOnSelectAll
         */
        selectAll: function (justVisible, triggerOnSelectAll) {
            var justVisible = typeof justVisible === 'undefined' ? true : justVisible;
            var allCheckboxes = $("li input[type='checkbox']:enabled", this.$ul);
            var visibleCheckboxes = allCheckboxes.filter(":visible");
            var allCheckboxesCount = allCheckboxes.length;
            var visibleCheckboxesCount = visibleCheckboxes.length;
            
            if(justVisible) {
                visibleCheckboxes.prop('checked', true);
                $("li:not(.divider):not(.disabled)", this.$ul).filter(":visible").addClass(this.options.selectedClass);
            }
            else {
                allCheckboxes.prop('checked', true);
                $("li:not(.divider):not(.disabled)", this.$ul).addClass(this.options.selectedClass);
            }
                
            if (allCheckboxesCount === visibleCheckboxesCount || justVisible === false) {
                $("option:enabled", this.$select).prop('selected', true);
            }
            else {
                var values = visibleCheckboxes.map(function() {
                    return $(this).val();
                }).get();
                
                $("option:enabled", this.$select).filter(function(index) {
                    return $.inArray($(this).val(), values) !== -1;
                }).prop('selected', true);
            }
            
            if (triggerOnSelectAll) {
                this.options.onSelectAll();
            }
        },

        /**
         * Deselects all options.
         * 
         * If justVisible is true or not specified, only visible options are deselected.
         * 
         * @param {Boolean} justVisible
         */
        deselectAll: function (justVisible) {
            var justVisible = typeof justVisible === 'undefined' ? true : justVisible;
            
            if(justVisible) {              
                var visibleCheckboxes = $("li input[type='checkbox']:not(:disabled)", this.$ul).filter(":visible");
                visibleCheckboxes.prop('checked', false);
                
                var values = visibleCheckboxes.map(function() {
                    return $(this).val();
                }).get();
                
                $("option:enabled", this.$select).filter(function(index) {
                    return $.inArray($(this).val(), values) !== -1;
                }).prop('selected', false);
                
                if (this.options.selectedClass) {
                    $("li:not(.divider):not(.disabled)", this.$ul).filter(":visible").removeClass(this.options.selectedClass);
                }
            }
            else {
                $("li input[type='checkbox']:enabled", this.$ul).prop('checked', false);
                $("option:enabled", this.$select).prop('selected', false);
                
                if (this.options.selectedClass) {
                    $("li:not(.divider):not(.disabled)", this.$ul).removeClass(this.options.selectedClass);
                }
            }
        },

        /**
         * Rebuild the plugin.
         * 
         * Rebuilds the dropdown, the filter and the select all option.
         */
        rebuild: function() {
            this.$ul.html('');

            // Important to distinguish between radios and checkboxes.
            this.options.multiple = this.$select.attr('multiple') === "multiple";

            this.buildSelectAll();
            this.buildDropdownOptions();
            this.buildFilter();

            this.updateButtonText();
            this.updateSelectAll();
            
            if (this.options.disableIfEmpty && $('option', this.$select).length <= 0) {
                this.disable();
            }
            else {
                this.enable();
            }
            
            if (this.options.dropRight) {
                this.$ul.addClass('pull-right');
            }
        },

        /**
         * The provided data will be used to build the dropdown.
         */
        dataprovider: function(dataprovider) {
            
            var groupCounter = 0;
            var $select = this.$select.empty();
            
            $.each(dataprovider, function (index, option) {
                var $tag;
                
                if ($.isArray(option.children)) { // create optiongroup tag
                    groupCounter++;
                    
                    $tag = $('<optgroup/>').attr({
                        label: option.label || 'Group ' + groupCounter,
                        disabled: !!option.disabled
                    });
                    
                    forEach(option.children, function(subOption) { // add children option tags
                        $tag.append($('<option/>').attr({
                            value: subOption.value,
                            label: subOption.label || subOption.value,
                            title: subOption.title,
                            selected: !!subOption.selected,
                            disabled: !!subOption.disabled
                        }));
                    });
                }
                else {
                    $tag = $('<option/>').attr({
                        value: option.value,
                        label: option.label || option.value,
                        title: option.title,
                        selected: !!option.selected,
                        disabled: !!option.disabled
                    });
                }
                
                $select.append($tag);
            });
            
            this.rebuild();
        },

        /**
         * Enable the multiselect.
         */
        enable: function() {
            this.$select.prop('disabled', false);
            this.$button.prop('disabled', false)
                .removeClass('disabled');
        },

        /**
         * Disable the multiselect.
         */
        disable: function() {
            this.$select.prop('disabled', true);
            this.$button.prop('disabled', true)
                .addClass('disabled');
        },

        /**
         * Set the options.
         *
         * @param {Array} options
         */
        setOptions: function(options) {
            this.options = this.mergeOptions(options);
        },

        /**
         * Merges the given options with the default options.
         *
         * @param {Array} options
         * @returns {Array}
         */
        mergeOptions: function(options) {
            return $.extend(true, {}, this.defaults, this.options, options);
        },

        /**
         * Checks whether a select all checkbox is present.
         *
         * @returns {Boolean}
         */
        hasSelectAll: function() {
            return $('li.multiselect-all', this.$ul).length > 0;
        },

        /**
         * Updates the select all checkbox based on the currently displayed and selected checkboxes.
         */
        updateSelectAll: function() {
            if (this.hasSelectAll()) {
                var allBoxes = $("li:not(.multiselect-item):not(.filter-hidden) input:enabled", this.$ul);
                var allBoxesLength = allBoxes.length;
                var checkedBoxesLength = allBoxes.filter(":checked").length;
                var selectAllLi  = $("li.multiselect-all", this.$ul);
                var selectAllInput = selectAllLi.find("input");
                
                if (checkedBoxesLength > 0 && checkedBoxesLength === allBoxesLength) {
                    selectAllInput.prop("checked", true);
                    selectAllLi.addClass(this.options.selectedClass);
                    this.options.onSelectAll();
                }
                else {
                    selectAllInput.prop("checked", false);
                    selectAllLi.removeClass(this.options.selectedClass);
                }
            }
        },

        /**
         * Update the button text and its title based on the currently selected options.
         */
        updateButtonText: function() {
            var options = this.getSelected();
            
            // First update the displayed button text.
            if (this.options.enableHTML) {
                $('.multiselect .multiselect-selected-text', this.$container).html(this.options.buttonText(options, this.$select));
            }
            else {
                $('.multiselect .multiselect-selected-text', this.$container).text(this.options.buttonText(options, this.$select));
            }
            
            // Now update the title attribute of the button.
            $('.multiselect', this.$container).attr('title', this.options.buttonTitle(options, this.$select));
        },

        /**
         * Get all selected options.
         *
         * @returns {jQUery}
         */
        getSelected: function() {
            return $('option', this.$select).filter(":selected");
        },

        /**
         * Gets a select option by its value.
         *
         * @param {String} value
         * @returns {jQuery}
         */
        getOptionByValue: function (value) {

            var options = $('option', this.$select);
            var valueToCompare = value.toString();

            for (var i = 0; i < options.length; i = i + 1) {
                var option = options[i];
                if (option.value === valueToCompare) {
                    return $(option);
                }
            }
        },

        /**
         * Get the input (radio/checkbox) by its value.
         *
         * @param {String} value
         * @returns {jQuery}
         */
        getInputByValue: function (value) {

            var checkboxes = $('li input', this.$ul);
            var valueToCompare = value.toString();

            for (var i = 0; i < checkboxes.length; i = i + 1) {
                var checkbox = checkboxes[i];
                if (checkbox.value === valueToCompare) {
                    return $(checkbox);
                }
            }
        },

        /**
         * Used for knockout integration.
         */
        updateOriginalOptions: function() {
            this.originalOptions = this.$select.clone()[0].options;
        },

        asyncFunction: function(callback, timeout, self) {
            var args = Array.prototype.slice.call(arguments, 3);
            return setTimeout(function() {
                callback.apply(self || window, args);
            }, timeout);
        },

        setAllSelectedText: function(allSelectedText) {
            this.options.allSelectedText = allSelectedText;
            this.updateButtonText();
        }
    };

    $.fn.multiselect = function(option, parameter, extraOptions) {
        return this.each(function() {
            var data = $(this).data('multiselect');
            var options = typeof option === 'object' && option;

            // Initialize the multiselect.
            if (!data) {
                data = new Multiselect(this, options);
                $(this).data('multiselect', data);
            }

            // Call multiselect method.
            if (typeof option === 'string') {
                data[option](parameter, extraOptions);
                
                if (option === 'destroy') {
                    $(this).data('multiselect', false);
                }
            }
        });
    };

    $.fn.multiselect.Constructor = Multiselect;

    $(function() {
        $("select[data-role=multiselect]").multiselect();
    });

}(__webpack_provided_window_dot_jQuery);

/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__("./node_modules/jquery/dist/jquery.js")))

/***/ }),

/***/ "./node_modules/bootstrap-toggle/js/bootstrap-toggle.js":
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(jQuery) {/*! ========================================================================
 * Bootstrap Toggle: bootstrap-toggle.js v2.2.0
 * http://www.bootstraptoggle.com
 * ========================================================================
 * Copyright 2014 Min Hur, The New York Times Company
 * Licensed under MIT
 * ======================================================================== */


 +function ($) {
 	'use strict';

	// TOGGLE PUBLIC CLASS DEFINITION
	// ==============================

	var Toggle = function (element, options) {
		this.$element  = $(element)
		this.options   = $.extend({}, this.defaults(), options)
		this.render()
	}

	Toggle.VERSION  = '2.2.0'

	Toggle.DEFAULTS = {
		on: 'On',
		off: 'Off',
		onstyle: 'primary',
		offstyle: 'default',
		size: 'normal',
		style: '',
		width: null,
		height: null
	}

	Toggle.prototype.defaults = function() {
		return {
			on: this.$element.attr('data-on') || Toggle.DEFAULTS.on,
			off: this.$element.attr('data-off') || Toggle.DEFAULTS.off,
			onstyle: this.$element.attr('data-onstyle') || Toggle.DEFAULTS.onstyle,
			offstyle: this.$element.attr('data-offstyle') || Toggle.DEFAULTS.offstyle,
			size: this.$element.attr('data-size') || Toggle.DEFAULTS.size,
			style: this.$element.attr('data-style') || Toggle.DEFAULTS.style,
			width: this.$element.attr('data-width') || Toggle.DEFAULTS.width,
			height: this.$element.attr('data-height') || Toggle.DEFAULTS.height
		}
	}

	Toggle.prototype.render = function () {
		this._onstyle = 'btn-' + this.options.onstyle
		this._offstyle = 'btn-' + this.options.offstyle
		var size = this.options.size === 'large' ? 'btn-lg'
			: this.options.size === 'small' ? 'btn-sm'
			: this.options.size === 'mini' ? 'btn-xs'
			: ''
		var $toggleOn = $('<label class="btn">').html(this.options.on)
			.addClass(this._onstyle + ' ' + size)
		var $toggleOff = $('<label class="btn">').html(this.options.off)
			.addClass(this._offstyle + ' ' + size + ' active')
		var $toggleHandle = $('<span class="toggle-handle btn btn-default">')
			.addClass(size)
		var $toggleGroup = $('<div class="toggle-group">')
			.append($toggleOn, $toggleOff, $toggleHandle)
		var $toggle = $('<div class="toggle btn" data-toggle="toggle">')
			.addClass( this.$element.prop('checked') ? this._onstyle : this._offstyle+' off' )
			.addClass(size).addClass(this.options.style)

		this.$element.wrap($toggle)
		$.extend(this, {
			$toggle: this.$element.parent(),
			$toggleOn: $toggleOn,
			$toggleOff: $toggleOff,
			$toggleGroup: $toggleGroup
		})
		this.$toggle.append($toggleGroup)

		var width = this.options.width || Math.max($toggleOn.outerWidth(), $toggleOff.outerWidth())+($toggleHandle.outerWidth()/2)
		var height = this.options.height || Math.max($toggleOn.outerHeight(), $toggleOff.outerHeight())
		$toggleOn.addClass('toggle-on')
		$toggleOff.addClass('toggle-off')
		this.$toggle.css({ width: width, height: height })
		if (this.options.height) {
			$toggleOn.css('line-height', $toggleOn.height() + 'px')
			$toggleOff.css('line-height', $toggleOff.height() + 'px')
		}
		this.update(true)
		this.trigger(true)
	}

	Toggle.prototype.toggle = function () {
		if (this.$element.prop('checked')) this.off()
		else this.on()
	}

	Toggle.prototype.on = function (silent) {
		if (this.$element.prop('disabled')) return false
		this.$toggle.removeClass(this._offstyle + ' off').addClass(this._onstyle)
		this.$element.prop('checked', true)
		if (!silent) this.trigger()
	}

	Toggle.prototype.off = function (silent) {
		if (this.$element.prop('disabled')) return false
		this.$toggle.removeClass(this._onstyle).addClass(this._offstyle + ' off')
		this.$element.prop('checked', false)
		if (!silent) this.trigger()
	}

	Toggle.prototype.enable = function () {
		this.$toggle.removeAttr('disabled')
		this.$element.prop('disabled', false)
	}

	Toggle.prototype.disable = function () {
		this.$toggle.attr('disabled', 'disabled')
		this.$element.prop('disabled', true)
	}

	Toggle.prototype.update = function (silent) {
		if (this.$element.prop('disabled')) this.disable()
		else this.enable()
		if (this.$element.prop('checked')) this.on(silent)
		else this.off(silent)
	}

	Toggle.prototype.trigger = function (silent) {
		this.$element.off('change.bs.toggle')
		if (!silent) this.$element.change()
		this.$element.on('change.bs.toggle', $.proxy(function() {
			this.update()
		}, this))
	}

	Toggle.prototype.destroy = function() {
		this.$element.off('change.bs.toggle')
		this.$toggleGroup.remove()
		this.$element.removeData('bs.toggle')
		this.$element.unwrap()
	}

	// TOGGLE PLUGIN DEFINITION
	// ========================

	function Plugin(option) {
		return this.each(function () {
			var $this   = $(this)
			var data    = $this.data('bs.toggle')
			var options = typeof option == 'object' && option

			if (!data) $this.data('bs.toggle', (data = new Toggle(this, options)))
			if (typeof option == 'string' && data[option]) data[option]()
		})
	}

	var old = $.fn.bootstrapToggle

	$.fn.bootstrapToggle             = Plugin
	$.fn.bootstrapToggle.Constructor = Toggle

	// TOGGLE NO CONFLICT
	// ==================

	$.fn.toggle.noConflict = function () {
		$.fn.bootstrapToggle = old
		return this
	}

	// TOGGLE DATA-API
	// ===============

	$(function() {
		$('input[type=checkbox][data-toggle^=toggle]').bootstrapToggle()
	})

	$(document).on('click.bs.toggle', 'div[data-toggle^=toggle]', function(e) {
		var $checkbox = $(this).find('input[type=checkbox]')
		$checkbox.bootstrapToggle('toggle')
		e.preventDefault()
	})

}(jQuery);

/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__("./node_modules/jquery/dist/jquery.js")))

/***/ }),

/***/ "./node_modules/canvasjs/dist/canvasjs.min.js":
/***/ (function(module, exports, __webpack_require__) {

var require;var require;!function(e){if(true)module.exports=e();else if("function"==typeof define&&define.amd)define([],e);else{var t;t="undefined"!=typeof window?window:"undefined"!=typeof global?global:"undefined"!=typeof self?self:this,t.CanvasJS=e()}}(function(){return function(){function e(t,i,a){function n(r,o){if(!i[r]){if(!t[r]){var l="function"==typeof require&&require;if(!o&&l)return require(r,!0);if(s)return s(r,!0);var h=new Error("Cannot find module '"+r+"'");throw h.code="MODULE_NOT_FOUND",h}var d=i[r]={exports:{}};t[r][0].call(d.exports,function(e){return n(t[r][1][e]||e)},d,d.exports,e,t,i,a)}return i[r].exports}for(var s="function"==typeof require&&require,r=0;r<a.length;r++)n(a[r]);return n}return e}()({1:[function(e,t,i){"use strict";function a(e){return e&&e.__esModule?e:{default:e}}Object.defineProperty(i,"__esModule",{value:!0}),i.default=function(e){function t(){T&&(x.lineThickness>0&&i.stroke(),e.axisY.viewportMinimum<=0&&e.axisY.viewportMaximum>=0?b=S:e.axisY.viewportMaximum<0?b=r.y1:e.axisY.viewportMinimum>0&&(b=n.y2),i.lineTo(g,b),i.lineTo(T.x,b),i.closePath(),i.globalAlpha=x.fillOpacity,i.fill(),i.globalAlpha=1,l.isCanvasSupported&&(a.lineTo(g,b),a.lineTo(T.x,b),a.closePath(),a.fill()),i.beginPath(),i.moveTo(g,y),a.beginPath(),a.moveTo(g,y),T={x:g,y:y})}var i=e.targetCanvasCtx||this.plotArea.ctx;if(!(e.dataSeriesIndexes.length<=0)){var a=this._eventManager.ghostCtx,n=e.axisX.lineCoordinates,r=e.axisY.lineCoordinates,h=[],d=this.plotArea;i.save(),l.isCanvasSupported&&a.save(),i.beginPath(),i.rect(d.x1,d.y1,d.width,d.height),i.clip(),l.isCanvasSupported&&(a.beginPath(),a.rect(d.x1,d.y1,d.width,d.height),a.clip());for(var c=0;c<e.dataSeriesIndexes.length;c++){var p=e.dataSeriesIndexes[c],x=this.data[p],u=x.dataPoints,m=x.id;this._eventManager.objectMap[m]={objectType:"dataSeries",dataSeriesIndex:p};var v=(0,l.intToHexColorString)(m);a.fillStyle=v,h=[];var g,y,f,b,M=!0,P=0,S=e.axisY.conversionParameters.reference+e.axisY.conversionParameters.pixelPerUnit*(0-e.axisY.conversionParameters.minimum)+.5<<0,T=null;if(u.length>0){var C=x._colorSet[P%x._colorSet.length];i.fillStyle=C,i.strokeStyle=C,i.lineWidth=x.lineThickness,i.setLineDash&&i.setLineDash((0,l.getLineDashArray)(x.lineDashType,x.lineThickness));for(var k=!0;P<u.length;P++)if(!((f=u[P].x.getTime?u[P].x.getTime():u[P].x)<e.axisX.dataInfo.viewPortMin||f>e.axisX.dataInfo.viewPortMax))if("number"==typeof u[P].y){g=e.axisX.conversionParameters.reference+e.axisX.conversionParameters.pixelPerUnit*(f-e.axisX.conversionParameters.minimum)+.5<<0,y=e.axisY.conversionParameters.reference+e.axisY.conversionParameters.pixelPerUnit*(u[P].y-e.axisY.conversionParameters.minimum)+.5<<0,M||k?(i.beginPath(),i.moveTo(g,y),T={x:g,y:y},l.isCanvasSupported&&(a.beginPath(),a.moveTo(g,y)),M=!1,k=!1):(i.lineTo(g,y),l.isCanvasSupported&&a.lineTo(g,y),P%250==0&&t());var w=x.dataPointIds[P];if(this._eventManager.objectMap[w]={id:w,objectType:"dataPoint",dataSeriesIndex:p,dataPointIndex:P,x1:g,y1:y},0!==u[P].markerSize&&(u[P].markerSize>0||x.markerSize>0)){var _=x.getMarkerProperties(P,g,y,i);h.push(_);var A=(0,l.intToHexColorString)(w);l.isCanvasSupported&&h.push({x:g,y:y,ctx:a,type:_.type,size:_.size,color:A,borderColor:A,borderThickness:_.borderThickness})}(u[P].indexLabel||x.indexLabel||u[P].indexLabelFormatter||x.indexLabelFormatter)&&this._indexLabels.push({chartType:"area",dataPoint:u[P],dataSeries:x,point:{x:g,y:y},direction:u[P].y>=0?1:-1,color:C})}else t(),k=!0;t(),s.default.drawMarkers(h)}}return i.restore(),l.isCanvasSupported&&this._eventManager.ghostCtx.restore(),{source:i,dest:this.plotArea.ctx,animationCallback:o.default.xClipAnimation,easingFunction:o.default.easing.linear,animationBase:0}}};var n=e("../helpers/render"),s=a(n),r=e("../helpers/animator"),o=a(r),l=e("../helpers/utils")},{"../helpers/animator":37,"../helpers/render":38,"../helpers/utils":39}],2:[function(e,t,i){"use strict";Object.defineProperty(i,"__esModule",{value:!0}),i.default=function(e){var t=e.targetCanvasCtx||this.plotArea.ctx;if(!(e.dataSeriesIndexes.length<=0)){var i,a,r,o=null,l=this.plotArea,h=0,d=e.axisY.conversionParameters.reference+e.axisY.conversionParameters.pixelPerUnit*(0-e.axisY.conversionParameters.minimum)<<0,c=this.dataPointMaxWidth?this.dataPointMaxWidth:Math.min(.15*this.height,this.plotArea.height/e.plotType.totalDataSeries*.9)<<0,p=e.axisX.dataInfo.minDiff,x=l.height/Math.abs(e.axisX.viewportMaximum-e.axisX.viewportMinimum)*Math.abs(p)/e.plotType.totalDataSeries*.9<<0;x>c?x=c:p===1/0?x=c/e.plotType.totalDataSeries*.9:x<1&&(x=1),t.save(),s.isCanvasSupported&&this._eventManager.ghostCtx.save(),t.beginPath(),t.rect(l.x1,l.y1,l.width,l.height),t.clip(),s.isCanvasSupported&&(this._eventManager.ghostCtx.rect(l.x1,l.y1,l.width,l.height),this._eventManager.ghostCtx.clip());for(var u=0;u<e.dataSeriesIndexes.length;u++){var m=e.dataSeriesIndexes[u],v=this.data[m],g=v.dataPoints;if(g.length>0){var y=!!(x>5&&v.bevelEnabled);for(t.strokeStyle="#4572A7 ",h=0;h<g.length;h++)if(!((r=g[h].getTime?g[h].x.getTime():g[h].x)<e.axisX.dataInfo.viewPortMin||r>e.axisX.dataInfo.viewPortMax)&&"number"==typeof g[h].y){a=e.axisX.conversionParameters.reference+e.axisX.conversionParameters.pixelPerUnit*(r-e.axisX.conversionParameters.minimum)+.5<<0,i=e.axisY.conversionParameters.reference+e.axisY.conversionParameters.pixelPerUnit*(g[h].y-e.axisY.conversionParameters.minimum)+.5<<0;var f,b,M=a-e.plotType.totalDataSeries*x/2+(e.previousDataSeriesCount+u)*x<<0,P=M+x<<0;g[h].y>=0?(f=d,b=i):(f=i,b=d),o=g[h].color?g[h].color:v._colorSet[h%v._colorSet.length],(0,s.drawRect)(t,f,M,b,P,o,0,null,y,!1,!1,!1,v.fillOpacity);var S=v.dataPointIds[h];this._eventManager.objectMap[S]={id:S,objectType:"dataPoint",dataSeriesIndex:m,dataPointIndex:h,x1:f,y1:M,x2:b,y2:P},o=(0,s.intToHexColorString)(S),s.isCanvasSupported&&(0,s.drawRect)(this._eventManager.ghostCtx,f,M,b,P,o,0,null,!1,!1,!1,!1),(g[h].indexLabel||v.indexLabel||g[h].indexLabelFormatter||v.indexLabelFormatter)&&this._indexLabels.push({chartType:"bar",dataPoint:g[h],dataSeries:v,point:{x:g[h].y>=0?b:f,y:M+(P-M)/2},direction:g[h].y>=0?1:-1,bounds:{x1:Math.min(f,b),y1:M,x2:Math.max(f,b),y2:P},color:o})}}}t.restore(),s.isCanvasSupported&&this._eventManager.ghostCtx.restore();var T=Math.max(d,e.axisX.boundingRect.x2);return{source:t,dest:this.plotArea.ctx,animationCallback:n.default.xScaleAnimation,easingFunction:n.default.easing.easeOutQuart,animationBase:T}}};var a=e("../helpers/animator"),n=function(e){return e&&e.__esModule?e:{default:e}}(a),s=e("../helpers/utils")},{"../helpers/animator":37,"../helpers/utils":39}],3:[function(e,t,i){"use strict";function a(e){return e&&e.__esModule?e:{default:e}}Object.defineProperty(i,"__esModule",{value:!0}),i.default=function(e){var t=e.targetCanvasCtx||this.plotArea.ctx,i=e.dataSeriesIndexes.length;if(!(i<=0)){var a,n,r,h=this.plotArea,d=0,c=(e.axisY.conversionParameters.reference,e.axisY.conversionParameters.pixelPerUnit,e.axisY.conversionParameters.minimum,this.dataPointMaxWidth?this.dataPointMaxWidth:.15*this.width<<0),p=e.axisX.dataInfo.minDiff,x=h.width/Math.abs(e.axisX.viewportMaximum-e.axisX.viewportMinimum)*Math.abs(p)/i*.9<<0;t.save(),l.isCanvasSupported&&this._eventManager.ghostCtx.save(),t.beginPath(),t.rect(h.x1,h.y1,h.width,h.height),t.clip(),l.isCanvasSupported&&(this._eventManager.ghostCtx.rect(h.x1,h.y1,h.width,h.height),this._eventManager.ghostCtx.clip());for(var u=-1/0,m=1/0,v=0;v<e.dataSeriesIndexes.length;v++)for(var g=e.dataSeriesIndexes[v],y=this.data[g],f=y.dataPoints,b=0,d=0;d<f.length;d++)(r=r=f[d].getTime?f[d].x.getTime():f[d].x)<e.axisX.dataInfo.viewPortMin||r>e.axisX.dataInfo.viewPortMax||void 0!==f[d].z&&(b=f[d].z,b>u&&(u=b),b<m&&(m=b));for(var M=5*Math.PI*5,P=Math.max(Math.pow(.25*Math.min(h.height,h.width)/2,2)*Math.PI,M),v=0;v<e.dataSeriesIndexes.length;v++){var g=e.dataSeriesIndexes[v],y=this.data[g],f=y.dataPoints;if(1==f.length&&(x=c),x<1?x=1:x>c&&(x=c),f.length>0){t.strokeStyle="#4572A7 ";for(var d=0;d<f.length;d++)if(!((r=r=f[d].getTime?f[d].x.getTime():f[d].x)<e.axisX.dataInfo.viewPortMin||r>e.axisX.dataInfo.viewPortMax)&&"number"==typeof f[d].y){a=e.axisX.conversionParameters.reference+e.axisX.conversionParameters.pixelPerUnit*(r-e.axisX.conversionParameters.minimum)+.5<<0,n=e.axisY.conversionParameters.reference+e.axisY.conversionParameters.pixelPerUnit*(f[d].y-e.axisY.conversionParameters.minimum)+.5<<0;var b=f[d].z,S=u===m?P/2:M+(P-M)/(u-m)*(b-m),T=Math.max(Math.sqrt(S/Math.PI)<<0,1),C=2*T,k=y.getMarkerProperties(d,t);k.size=C,t.globalAlpha=y.fillOpacity,s.default.drawMarker(a,n,t,k.type,k.size,k.color,k.borderColor,k.borderThickness),t.globalAlpha=1;var w=y.dataPointIds[d];this._eventManager.objectMap[w]={id:w,objectType:"dataPoint",dataSeriesIndex:g,dataPointIndex:d,x1:a,y1:n,size:C};var _=(0,l.intToHexColorString)(w);l.isCanvasSupported&&s.default.drawMarker(a,n,this._eventManager.ghostCtx,k.type,k.size,_,_,k.borderThickness),(f[d].indexLabel||y.indexLabel||f[d].indexLabelFormatter||y.indexLabelFormatter)&&this._indexLabels.push({chartType:"bubble",dataPoint:f[d],dataSeries:y,point:{x:a,y:n},direction:1,bounds:{x1:a-k.size/2,y1:n-k.size/2,x2:a+k.size/2,y2:n+k.size/2},color:null})}}}t.restore(),l.isCanvasSupported&&this._eventManager.ghostCtx.restore();return{source:t,dest:this.plotArea.ctx,animationCallback:o.default.fadeInAnimation,easingFunction:o.default.easing.easeInQuad,animationBase:0}}};var n=e("../helpers/render"),s=a(n),r=e("../helpers/animator"),o=a(r),l=e("../helpers/utils")},{"../helpers/animator":37,"../helpers/render":38,"../helpers/utils":39}],4:[function(e,t,i){"use strict";Object.defineProperty(i,"__esModule",{value:!0}),i.default=function(e){var t=e.targetCanvasCtx||this.plotArea.ctx,i=this._eventManager.ghostCtx;if(!(e.dataSeriesIndexes.length<=0)){var n,s,r,o,l,h,d=null,c=this.plotArea,p=0,x=(e.axisY.conversionParameters.reference,e.axisY.conversionParameters.pixelPerUnit,e.axisY.conversionParameters.minimum,this.dataPointMaxWidth?this.dataPointMaxWidth:.015*this.width),u=e.axisX.dataInfo.minDiff,m=c.width/Math.abs(e.axisX.viewportMaximum-e.axisX.viewportMinimum)*Math.abs(u)*.7<<0;m>x?m=x:u===1/0?m=x:m<1&&(m=1),t.save(),a.isCanvasSupported&&i.save(),t.beginPath(),t.rect(c.x1,c.y1,c.width,c.height),t.clip(),a.isCanvasSupported&&(i.rect(c.x1,c.y1,c.width,c.height),i.clip());for(var v=0;v<e.dataSeriesIndexes.length;v++){var g=e.dataSeriesIndexes[v],y=this.data[g],f=y.dataPoints;if(f.length>0){var b=!!(m>5&&y.bevelEnabled);for(p=0;p<f.length;p++)if(!((h=f[p].getTime?f[p].x.getTime():f[p].x)<e.axisX.dataInfo.viewPortMin||h>e.axisX.dataInfo.viewPortMax)&&null!==f[p].y&&f[p].y.length&&"number"==typeof f[p].y[0]&&"number"==typeof f[p].y[1]&&"number"==typeof f[p].y[2]&&"number"==typeof f[p].y[3]){n=e.axisX.conversionParameters.reference+e.axisX.conversionParameters.pixelPerUnit*(h-e.axisX.conversionParameters.minimum)+.5<<0,s=e.axisY.conversionParameters.reference+e.axisY.conversionParameters.pixelPerUnit*(f[p].y[0]-e.axisY.conversionParameters.minimum)+.5<<0,r=e.axisY.conversionParameters.reference+e.axisY.conversionParameters.pixelPerUnit*(f[p].y[1]-e.axisY.conversionParameters.minimum)+.5<<0,o=e.axisY.conversionParameters.reference+e.axisY.conversionParameters.pixelPerUnit*(f[p].y[2]-e.axisY.conversionParameters.minimum)+.5<<0,l=e.axisY.conversionParameters.reference+e.axisY.conversionParameters.pixelPerUnit*(f[p].y[3]-e.axisY.conversionParameters.minimum)+.5<<0;var M=n-m/2<<0,P=M+m<<0;d=f[p].color?f[p].color:y._colorSet[0];var S=Math.round(Math.max(1,.15*m)),T=S%2==0?0:.5,C=y.dataPointIds[p];this._eventManager.objectMap[C]={id:C,objectType:"dataPoint",dataSeriesIndex:g,dataPointIndex:p,x1:M,y1:s,x2:P,y2:r,x3:n,y3:o,x4:n,y4:l,borderThickness:S,color:d},t.strokeStyle=d,t.beginPath(),t.lineWidth=S,i.lineWidth=Math.max(S,4),"candlestick"===y.type?(t.moveTo(n-T,r),t.lineTo(n-T,Math.min(s,l)),t.stroke(),t.moveTo(n-T,Math.max(s,l)),t.lineTo(n-T,o),t.stroke(),drawRect(t,M,Math.min(s,l),P,Math.max(s,l),f[p].y[0]<=f[p].y[3]?y.risingColor:d,S,d,b,b,!1,!1,y.fillOpacity),a.isCanvasSupported&&(d=intToHexColorString(C),i.strokeStyle=d,i.moveTo(n-T,r),i.lineTo(n-T,Math.min(s,l)),i.stroke(),i.moveTo(n-T,Math.max(s,l)),i.lineTo(n-T,o),i.stroke(),drawRect(i,M,Math.min(s,l),P,Math.max(s,l),d,0,null,!1,!1,!1,!1))):"ohlc"===y.type&&(t.moveTo(n-T,r),t.lineTo(n-T,o),t.stroke(),t.beginPath(),t.moveTo(n,s),t.lineTo(M,s),t.stroke(),t.beginPath(),t.moveTo(n,l),t.lineTo(P,l),t.stroke(),a.isCanvasSupported&&(d=intToHexColorString(C),i.strokeStyle=d,i.moveTo(n-T,r),i.lineTo(n-T,o),i.stroke(),i.beginPath(),i.moveTo(n,s),i.lineTo(M,s),i.stroke(),i.beginPath(),i.moveTo(n,l),i.lineTo(P,l),i.stroke())),(f[p].indexLabel||y.indexLabel||f[p].indexLabelFormatter||y.indexLabelFormatter)&&this._indexLabels.push({chartType:y.type,dataPoint:f[p],dataSeries:y,point:{x:M+(P-M)/2,y:r},direction:1,bounds:{x1:M,y1:Math.min(r,o),x2:P,y2:Math.max(r,o)},color:d})}}}return t.restore(),a.isCanvasSupported&&i.restore(),{source:t,dest:this.plotArea.ctx,animationCallback:AnimationHelper.fadeInAnimation,easingFunction:AnimationHelper.easing.easeInQuad,animationBase:0}}};var a=e("../helpers/utils")},{"../helpers/utils":39}],5:[function(e,t,i){"use strict";Object.defineProperty(i,"__esModule",{value:!0}),i.default=function(e){var t=e.targetCanvasCtx||this.plotArea.ctx;if(!(e.dataSeriesIndexes.length<=0)){var i,a,r,o=null,l=this.plotArea,h=0,d=e.axisY.conversionParameters.reference+e.axisY.conversionParameters.pixelPerUnit*(0-e.axisY.conversionParameters.minimum)<<0,c=this.dataPointMaxWidth?this.dataPointMaxWidth:Math.min(.15*this.width,this.plotArea.width/e.plotType.totalDataSeries*.9)<<0,p=e.axisX.dataInfo.minDiff,x=l.width/Math.abs(e.axisX.viewportMaximum-e.axisX.viewportMinimum)*Math.abs(p)/e.plotType.totalDataSeries*.9<<0;x>c?x=c:p===1/0?x=c/e.plotType.totalDataSeries*.9:x<1&&(x=1),t.save(),s.isCanvasSupported&&this._eventManager.ghostCtx.save(),t.beginPath(),t.rect(l.x1,l.y1,l.width,l.height),t.clip(),s.isCanvasSupported&&(this._eventManager.ghostCtx.rect(l.x1,l.y1,l.width,l.height),this._eventManager.ghostCtx.clip());for(var u=0;u<e.dataSeriesIndexes.length;u++){var m=e.dataSeriesIndexes[u],v=this.data[m],g=v.dataPoints;if(g.length>0){var y=!!(x>5&&v.bevelEnabled);for(h=0;h<g.length;h++)if(!((r=g[h].getTime?g[h].x.getTime():g[h].x)<e.axisX.dataInfo.viewPortMin||r>e.axisX.dataInfo.viewPortMax)&&"number"==typeof g[h].y){i=e.axisX.conversionParameters.reference+e.axisX.conversionParameters.pixelPerUnit*(r-e.axisX.conversionParameters.minimum)+.5<<0,a=e.axisY.conversionParameters.reference+e.axisY.conversionParameters.pixelPerUnit*(g[h].y-e.axisY.conversionParameters.minimum)+.5<<0;var f,b,M=i-e.plotType.totalDataSeries*x/2+(e.previousDataSeriesCount+u)*x<<0,P=M+x<<0;g[h].y>=0?(f=a,b=d,f>b&&(f=b,b=f)):(b=a,(f=d)>b&&(f=b,b=f)),o=g[h].color?g[h].color:v._colorSet[h%v._colorSet.length],(0,s.drawRect)(t,M,f,P,b,o,0,null,y&&g[h].y>=0,g[h].y<0&&y,!1,!1,v.fillOpacity);var S=v.dataPointIds[h];this._eventManager.objectMap[S]={id:S,objectType:"dataPoint",dataSeriesIndex:m,dataPointIndex:h,x1:M,y1:f,x2:P,y2:b},o=(0,s.intToHexColorString)(S),s.isCanvasSupported&&(0,s.drawRect)(this._eventManager.ghostCtx,M,f,P,b,o,0,null,!1,!1,!1,!1),(g[h].indexLabel||v.indexLabel||g[h].indexLabelFormatter||v.indexLabelFormatter)&&this._indexLabels.push({chartType:"column",dataPoint:g[h],dataSeries:v,point:{x:M+(P-M)/2,y:g[h].y>=0?f:b},direction:g[h].y>=0?1:-1,bounds:{x1:M,y1:Math.min(f,b),x2:P,y2:Math.max(f,b)},color:o})}}}t.restore(),s.isCanvasSupported&&this._eventManager.ghostCtx.restore();var T=Math.min(d,e.axisY.boundingRect.y2);return{source:t,dest:this.plotArea.ctx,animationCallback:n.default.yScaleAnimation,easingFunction:n.default.easing.easeOutQuart,animationBase:T}}};var a=e("../helpers/animator"),n=function(e){return e&&e.__esModule?e:{default:e}}(a),s=e("../helpers/utils")},{"../helpers/animator":37,"../helpers/utils":39}],6:[function(e,t,i){"use strict";function a(e){return e&&e.__esModule?e:{default:e}}Object.defineProperty(i,"__esModule",{value:!0}),i.PieChart=i.RangeSplineAreaChart=i.RangeAreaChart=i.RangeBarChart=i.RangeColumnChart=i.CandlestickChart=i.ScatterChart=i.BubbleChart=i.StackedArea100Chart=i.StackedAreaChart=i.StepAreaChart=i.SplineAreaChart=i.AreaChart=i.StackedBar100Chart=i.StackedBarChart=i.BarChart=i.StackedColumn100Chart=i.StackedColumnChart=i.ColumnChart=i.SplineChart=void 0;var n=e("../charts/spline"),s=a(n),r=e("../charts/column"),o=a(r),l=e("../charts/stacked_column"),h=a(l),d=e("../charts/stacked_column_100"),c=a(d),p=e("../charts/bar"),x=a(p),u=e("../charts/stacked_bar"),m=a(u),v=e("../charts/stacked_bar_100"),g=a(v),y=e("../charts/area"),f=a(y),b=e("../charts/spline_area"),M=a(b),P=e("../charts/step_area"),S=a(P),T=e("../charts/stacked_area"),C=a(T),k=e("../charts/stacked_area_100"),w=a(k),_=e("../charts/bubble"),A=a(_),L=e("../charts/scatter"),I=a(L),B=e("../charts/candlestick"),F=a(B),z=e("../charts/range_column"),D=a(z),X=e("../charts/range_bar"),Y=a(X),O=e("../charts/range_area"),E=a(O),W=e("../charts/range_spline_area"),R=a(W),H=e("../charts/pie"),V=a(H);i.SplineChart=s.default,i.ColumnChart=o.default,i.StackedColumnChart=h.default,i.StackedColumn100Chart=c.default,i.BarChart=x.default,i.StackedBarChart=m.default,i.StackedBar100Chart=g.default,i.AreaChart=f.default,i.SplineAreaChart=M.default,i.StepAreaChart=S.default,i.StackedAreaChart=C.default,i.StackedArea100Chart=w.default,i.BubbleChart=A.default,i.ScatterChart=I.default,i.CandlestickChart=F.default,i.RangeColumnChart=D.default,i.RangeBarChart=Y.default,i.RangeAreaChart=E.default,i.RangeSplineAreaChart=R.default,i.PieChart=V.default},{"../charts/area":1,"../charts/bar":2,"../charts/bubble":3,"../charts/candlestick":4,"../charts/column":5,"../charts/pie":7,"../charts/range_area":8,"../charts/range_bar":9,"../charts/range_column":10,"../charts/range_spline_area":11,"../charts/scatter":12,"../charts/spline":13,"../charts/spline_area":14,"../charts/stacked_area":15,"../charts/stacked_area_100":16,"../charts/stacked_bar":17,"../charts/stacked_bar_100":18,"../charts/stacked_column":19,"../charts/stacked_column_100":20,"../charts/step_area":21}],7:[function(e,t,i){"use strict";Object.defineProperty(i,"__esModule",{value:!0}),i.default=function(e){function t(){var e=x.plotArea.ctx;e.fillStyle="black",e.strokeStyle="grey";e.textBaseline="middle",e.lineJoin="round";var t=0;for(t=0;t<v.length;t++){var i=f[t];if(i.indexLabelText){i.indexLabelTextBlock.y-=i.indexLabelTextBlock.height/2;var a=0;if("left"===i.hemisphere)var a="inside"!==m.indexLabelPlacement?-(i.indexLabelTextBlock.width+g):-i.indexLabelTextBlock.width/2;else var a="inside"!==m.indexLabelPlacement?g:-i.indexLabelTextBlock.width/2;if(i.indexLabelTextBlock.x+=a,i.indexLabelTextBlock.render(!0),i.indexLabelTextBlock.x-=a,i.indexLabelTextBlock.y+=i.indexLabelTextBlock.height/2,"inside"!==i.indexLabelPlacement){var n=i.center.x+_*Math.cos(i.midAngle),r=i.center.y+_*Math.sin(i.midAngle);e.strokeStyle=i.indexLabelLineColor,e.lineWidth=i.indexLabelLineThickness,e.setLineDash&&e.setLineDash((0,s.getLineDashArray)(i.indexLabelLineDashType,i.indexLabelLineThickness)),e.beginPath(),e.moveTo(n,r),e.lineTo(i.indexLabelTextBlock.x,i.indexLabelTextBlock.y),e.lineTo(i.indexLabelTextBlock.x+("left"===i.hemisphere?-g:g),i.indexLabelTextBlock.y),e.stroke()}e.lineJoin="miter"}}}function i(e){var t=x.plotArea.ctx;t.clearRect(y.x1,y.y1,y.width,y.height),t.fillStyle=x.backgroundColor,t.fillRect(y.x1,y.y1,y.width,y.height);for(var i=f[0].startAngle+2*Math.PI*e,a=0;a<v.length;a++){var n=0===a?f[a].startAngle:r,r=n+(f[a].endAngle-f[a].startAngle),o=!1;r>i&&(r=i,o=!0);var l=v[a].color?v[a].color:m._colorSet[a%m._colorSet.length];if(r>n&&(0,s.drawSegment)(x.plotArea.ctx,f[a].center,f[a].radius,l,m.type,n,r,m.fillOpacity,f[a].percentInnerRadius),o)break}}function a(e){var i=x.plotArea.ctx;i.clearRect(y.x1,y.y1,y.width,y.height),i.fillStyle=x.backgroundColor,i.fillRect(y.x1,y.y1,y.width,y.height);for(var a=0;a<v.length;a++){var n=f[a].startAngle,r=f[a].endAngle;if(r>n){var o=.07*_*Math.cos(f[a].midAngle),l=.07*_*Math.sin(f[a].midAngle),h=!1;if(v[a].exploded?(Math.abs(f[a].center.x-(T.x+o))>1e-9||Math.abs(f[a].center.y-(T.y+l))>1e-9)&&(f[a].center.x=T.x+o*e,f[a].center.y=T.y+l*e,h=!0):(Math.abs(f[a].center.x-T.x)>0||Math.abs(f[a].center.y-T.y)>0)&&(f[a].center.x=T.x+o*(1-e),f[a].center.y=T.y+l*(1-e),h=!0),h){var d={};d.dataSeries=m,d.dataPoint=m.dataPoints[a],d.index=a,x._toolTip.highlightObjects([d])}var c=v[a].color?v[a].color:m._colorSet[a%m._colorSet.length];(0,s.drawSegment)(x.plotArea.ctx,f[a].center,f[a].radius,c,m.type,n,r,m.fillOpacity,f[a].percentInnerRadius)}}t()}function r(e,t){var i={x1:e.indexLabelTextBlock.x,y1:e.indexLabelTextBlock.y-e.indexLabelTextBlock.height/2,x2:e.indexLabelTextBlock.x+e.indexLabelTextBlock.width,y2:e.indexLabelTextBlock.y+e.indexLabelTextBlock.height/2},a={x1:t.indexLabelTextBlock.x,y1:t.indexLabelTextBlock.y-t.indexLabelTextBlock.height/2,x2:t.indexLabelTextBlock.x+t.indexLabelTextBlock.width,y2:t.indexLabelTextBlock.y+t.indexLabelTextBlock.height/2};return!(i.x2<a.x1-g||i.x1>a.x2+g||i.y1>a.y2+g||i.y2<a.y1-g)}function o(e,t){var i={y:e.indexLabelTextBlock.y,y1:e.indexLabelTextBlock.y-e.indexLabelTextBlock.height/2,y2:e.indexLabelTextBlock.y+e.indexLabelTextBlock.height/2},a={y:t.indexLabelTextBlock.y,y1:t.indexLabelTextBlock.y-t.indexLabelTextBlock.height/2,y2:t.indexLabelTextBlock.y+t.indexLabelTextBlock.height/2};return a.y>i.y?a.y1-i.y2:i.y1-a.y2}function l(e){for(var t=null,i=1;i<v.length;i++){if(t=(e+i+f.length)%f.length,f[t].hemisphere!==f[e].hemisphere){t=null;break}if(f[t].indexLabelText&&t!==e&&(o(f[t],f[e])<0||("right"===f[e].hemisphere?f[t].indexLabelTextBlock.y>=f[e].indexLabelTextBlock.y:f[t].indexLabelTextBlock.y<=f[e].indexLabelTextBlock.y)))break;t=null}return t}function h(e){for(var t=null,i=1;i<v.length;i++){if(t=(e-i+f.length)%f.length,f[t].hemisphere!==f[e].hemisphere){t=null;break}if(f[t].indexLabelText&&f[t].hemisphere===f[e].hemisphere&&t!==e&&(o(f[t],f[e])<0||("right"===f[e].hemisphere?f[t].indexLabelTextBlock.y<=f[e].indexLabelTextBlock.y:f[t].indexLabelTextBlock.y>=f[e].indexLabelTextBlock.y)))break;t=null}return t}function d(e,t){t=t||0;var i=0,a=T.y-1*indexLabelRadius,n=T.y+1*indexLabelRadius;if(e>=0&&e<v.length){var s=f[e];if(t<0&&s.indexLabelTextBlock.y<a||t>0&&s.indexLabelTextBlock.y>n)return 0;var r=t,o=0,c=0,p=0,x=0,u=0;r<0?s.indexLabelTextBlock.y-s.indexLabelTextBlock.height/2>a&&s.indexLabelTextBlock.y-s.indexLabelTextBlock.height/2+r<a&&(r=-(a-(s.indexLabelTextBlock.y-s.indexLabelTextBlock.height/2+r))):s.indexLabelTextBlock.y+s.indexLabelTextBlock.height/2<a&&s.indexLabelTextBlock.y+s.indexLabelTextBlock.height/2+r>n&&(r=s.indexLabelTextBlock.y+s.indexLabelTextBlock.height/2+r-n);var m=s.indexLabelTextBlock.y+r,g=0;g="right"===s.hemisphere?T.x+Math.sqrt(Math.pow(indexLabelRadius,2)-Math.pow(m-T.y,2)):T.x-Math.sqrt(Math.pow(indexLabelRadius,2)-Math.pow(m-T.y,2)),c=T.x+_*Math.cos(s.midAngle),p=T.y+_*Math.sin(s.midAngle),o=Math.sqrt(Math.pow(g-c,2)+Math.pow(m-p,2)),u=Math.acos(_/indexLabelRadius),x=Math.acos((indexLabelRadius*indexLabelRadius+_*_-o*o)/(2*_*indexLabelRadius)),r=x<u?m-s.indexLabelTextBlock.y:0;var y,M,C=h(e),k=l(e),w=0,A=0;if(r<0){if(y="right"===s.hemisphere?C:k,i=r,null!==y){var L=-r,M=s.indexLabelTextBlock.y-s.indexLabelTextBlock.height/2-(f[y].indexLabelTextBlock.y+f[y].indexLabelTextBlock.height/2);M-L<b&&(w=-L,A=d(y,w,recursionCount+1),+A.toFixed(S)>+w.toFixed(S)&&(i=M>b?-(M-b):-(L-(A-w))))}}else if(r>0&&(y="right"===s.hemisphere?k:C,i=r,null!==y)){var L=r,M=f[y].indexLabelTextBlock.y-f[y].indexLabelTextBlock.height/2-(s.indexLabelTextBlock.y+s.indexLabelTextBlock.height/2);M-L<b&&(w=L,A=d(y,w,recursionCount+1),+A.toFixed(S)<+w.toFixed(S)&&(i=M>b?M-b:L-(w-A)))}if(i){var I=s.indexLabelTextBlock.y+i,B=0;if(B="right"===s.hemisphere?T.x+Math.sqrt(Math.pow(indexLabelRadius,2)-Math.pow(I-T.y,2)):T.x-Math.sqrt(Math.pow(indexLabelRadius,2)-Math.pow(I-T.y,2)),s.midAngle>Math.PI/2-P&&s.midAngle<Math.PI/2+P){var F=(e-1+f.length)%f.length,z=f[F],D=f[(e+1+f.length)%f.length];"left"===s.hemisphere&&"right"===z.hemisphere&&B>z.indexLabelTextBlock.x?B=z.indexLabelTextBlock.x-15:"right"===s.hemisphere&&"left"===D.hemisphere&&B<D.indexLabelTextBlock.x&&(B=D.indexLabelTextBlock.x+15)}else if(s.midAngle>3*Math.PI/2-P&&s.midAngle<3*Math.PI/2+P){var F=(e-1+f.length)%f.length,z=f[F],D=f[(e+1+f.length)%f.length];"right"===s.hemisphere&&"left"===z.hemisphere&&B<z.indexLabelTextBlock.x?B=z.indexLabelTextBlock.x+15:"left"===s.hemisphere&&"right"===D.hemisphere&&B>D.indexLabelTextBlock.x&&(B=D.indexLabelTextBlock.x-15)}s.indexLabelTextBlock.y=I,s.indexLabelTextBlock.x=B,s.indexLabelAngle=Math.atan2(s.indexLabelTextBlock.y-T.y,s.indexLabelTextBlock.x-T.x)}}return i}function c(){var e=x.plotArea.ctx;e.fillStyle="grey",e.strokeStyle="grey";e.font="16px Arial",e.textBaseline="middle";var t=0,i=0,a=0,n=!0;for(i=0;i<10&&(i<1||a>0);i++){var s,h,c,p,h,u,P,C,k,w,I,B,F,z,h,D,X,Y,O,E,W,R,H,V;!function(){function e(e,t,i){for(var a=[],n=0,s=t;!0&&(a.push(f[s]),s!==i);s=(s+1+v.length)%v.length);for(a.sort(function(e,t){return e.y-t.y}),s=0;s<a.length;s++){var r=a[s];if(!(n<.7*e))break;n+=r.indexLabelTextBlock.height,r.indexLabelTextBlock.text="",r.indexLabelText="",r.indexLabelTextBlock.measureText()}}if((m.radius||!m.radius&&void 0!==m.innerRadius&&null!==m.innerRadius&&_-a<=A)&&(n=!1),n&&(_-=a),a=0,"inside"!==m.indexLabelPlacement){for(s=_*M,t=0;t<v.length;t++)h=f[t],h.indexLabelTextBlock.x=T.x+s*Math.cos(h.midAngle),h.indexLabelTextBlock.y=T.y+s*Math.sin(h.midAngle),h.indexLabelAngle=h.midAngle,h.radius=_,h.percentInnerRadius=L;for(t=0;t<v.length;t++)if(h=f[t],null!==(u=l(t))&&(c=f[t],p=f[u],P=0,(P=o(c,p)-b)<0)){for(C=0,k=0,w=0;w<v.length;w++)w!==t&&f[w].hemisphere===h.hemisphere&&(f[w].indexLabelTextBlock.y<h.indexLabelTextBlock.y?C++:k++);I=P/(C+k||1)*k,B=-1*(P-I),F=0,z=0,"right"===h.hemisphere?(F=d(t,I),B=-1*(P-F),z=d(u,B),+z.toFixed(S)<+B.toFixed(S)&&+F.toFixed(S)<=+I.toFixed(S)&&d(t,-(B-z))):(F=d(u,I),B=-1*(P-F),z=d(t,B),+z.toFixed(S)<+B.toFixed(S)&&+F.toFixed(S)<=+I.toFixed(S)&&d(u,-(B-z)))}}else for(t=0;t<v.length;t++)h=f[t],s="pie"===m.type?.7*_:.8*_,D=T.x+s*Math.cos(h.midAngle),X=T.y+s*Math.sin(h.midAngle),h.indexLabelTextBlock.x=D,h.indexLabelTextBlock.y=X;for(t=0;t<v.length;t++)h=f[t],Y=h.indexLabelTextBlock.measureText(),0!==Y.height&&0!==Y.width&&(O=0,E=0,"right"===h.hemisphere?(O=y.x2-(h.indexLabelTextBlock.x+h.indexLabelTextBlock.width+g),O*=-1):O=y.x1-(h.indexLabelTextBlock.x-h.indexLabelTextBlock.width-g),O>0&&(!n&&h.indexLabelText&&(W="right"===h.hemisphere?y.x2-h.indexLabelTextBlock.x:h.indexLabelTextBlock.x-y.x1,.3*h.indexLabelTextBlock.maxWidth>W?h.indexLabelText="":h.indexLabelTextBlock.maxWidth=.85*W,.3*h.indexLabelTextBlock.maxWidth<W&&(h.indexLabelTextBlock.x-="right"===h.hemisphere?2:-2)),(Math.abs(h.indexLabelTextBlock.y-h.indexLabelTextBlock.height/2-T.y)<_||Math.abs(h.indexLabelTextBlock.y+h.indexLabelTextBlock.height/2-T.y)<_)&&(E=O/Math.abs(Math.cos(h.indexLabelAngle)),E>9&&(E*=.3),E>a&&(a=E))),R=0,H=0,h.indexLabelAngle>0&&h.indexLabelAngle<Math.PI?(R=y.y2-(h.indexLabelTextBlock.y+h.indexLabelTextBlock.height/2+5),R*=-1):R=y.y1-(h.indexLabelTextBlock.y-h.indexLabelTextBlock.height/2-5),R>0&&(!n&&h.indexLabelText&&(V=h.indexLabelAngle>0&&h.indexLabelAngle<Math.PI?-1:1,0===d(t,R*V)&&d(t,2*V)),Math.abs(h.indexLabelTextBlock.x-T.x)<_&&(H=R/Math.abs(Math.sin(h.indexLabelAngle)),H>9&&(H*=.3),H>a&&(a=H))));!function(){for(var t=-1,i=-1,a=0,n=!1,s=0;s<v.length;s++)if(n=!1,c=f[s],c.indexLabelText){var h=l(s);if(null!==h){var d=f[h];P=0,P=o(c,d),P<0&&r(c,d)?(t<0&&(t=s),h!==t&&(i=h,a+=-P),s%Math.max(v.length/10,3)==0&&(n=!0)):n=!0,n&&a>0&&t>=0&&i>=0&&(e(a,t,i),t=-1,i=-1,a=0)}}a>0&&e(a,t,i)}()}()}}function p(){if(x.plotArea.layoutManager.reset(),x._title&&(x._title.dockInsidePlotArea||"center"===x._title.horizontalAlign&&"center"===x._title.verticalAlign)&&x._title.render(),x.subtitles)for(var e=0;e<x.subtitles.length;e++){var t=x.subtitles[e];(t.dockInsidePlotArea||"center"===t.horizontalAlign&&"center"===t.verticalAlign)&&t.render()}x.legend&&(x.legend.dockInsidePlotArea||"center"===x.legend.horizontalAlign&&"center"===x.legend.verticalAlign)&&x.legend.render()}var x=this;if(!(e.dataSeriesIndexes.length<=0)){for(var u=e.dataSeriesIndexes[0],m=this.data[u],v=m.dataPoints,g=10,y=this.plotArea,f=[],b=2,M=1.3,P=20/180*Math.PI,S=6,T={x:(y.x2+y.x1)/2,y:(y.y2+y.y1)/2},C=0,k=!1,w=0;w<v.length;w++)C+=Math.abs(v[w].y),!k&&void 0!==v[w].indexLabel&&null!==v[w].indexLabel&&v[w].indexLabel.toString().length>0&&(k=!0),!k&&void 0!==v[w].label&&null!==v[w].label&&v[w].label.toString().length>0&&(k=!0);if(0!==C){k=k||void 0!==m.indexLabel&&null!==m.indexLabel&&m.indexLabel.toString().length>0;var _="inside"!==m.indexLabelPlacement&&k?.75*Math.min(y.width,y.height)/2:.92*Math.min(y.width,y.height)/2;m.radius&&(_=(0,s.convertPercentToValue)(m.radius,_));var A=void 0!==m.innerRadius&&null!==m.innerRadius?(0,s.convertPercentToValue)(m.innerRadius,_):.7*_,L=Math.min(A/_,(_-1)/_);this.pieDoughnutClickHandler=function(e){if(!x.isAnimating){var t=e.dataPointIndex,i=e.dataPoint,n=this;n.dataPointIds[t],i.exploded?i.exploded=!1:i.exploded=!0,n.dataPoints.length>1&&x._animator.animate(0,500,function(e){a(e),p()})}},function(){if(m&&v){var e=0,t=0,i=0,a=0;for(w=0;w<v.length;w++){var s=v[w],r=m.dataPointIds[w],o={id:r,objectType:"dataPoint",dataPointIndex:w,dataSeriesIndex:0};f.push(o);var l={percent:null,total:null},h=null;l=x.getPercentAndTotal(m,s),(m.indexLabelFormatter||s.indexLabelFormatter)&&(h={chart:x._options,dataSeries:m,dataPoint:s,total:l.total,percent:l.percent});var d=s.indexLabelFormatter?s.indexLabelFormatter(h):s.indexLabel?x.replaceKeywordsWithValue(s.indexLabel,s,m,w):m.indexLabelFormatter?m.indexLabelFormatter(h):m.indexLabel?x.replaceKeywordsWithValue(m.indexLabel,s,m,w):s.label?s.label:"";x._eventManager.objectMap[r]=o,o.center={x:T.x,y:T.y},o.y=s.y,o.radius=_,o.percentInnerRadius=L,o.indexLabelText=d,o.indexLabelPlacement=m.indexLabelPlacement,o.indexLabelLineColor=s.indexLabelLineColor?s.indexLabelLineColor:m.indexLabelLineColor?m.indexLabelLineColor:s.color?s.color:m._colorSet[w%m._colorSet.length],o.indexLabelLineThickness=s.indexLabelLineThickness?s.indexLabelLineThickness:m.indexLabelLineThickness,o.indexLabelLineDashType=s.indexLabelLineDashType?s.indexLabelLineDashType:m.indexLabelLineDashType,o.indexLabelFontColor=s.indexLabelFontColor?s.indexLabelFontColor:m.indexLabelFontColor,o.indexLabelFontStyle=s.indexLabelFontStyle?s.indexLabelFontStyle:m.indexLabelFontStyle,o.indexLabelFontWeight=s.indexLabelFontWeight?s.indexLabelFontWeight:m.indexLabelFontWeight,o.indexLabelFontSize=s.indexLabelFontSize?s.indexLabelFontSize:m.indexLabelFontSize,o.indexLabelFontFamily=s.indexLabelFontFamily?s.indexLabelFontFamily:m.indexLabelFontFamily,o.indexLabelBackgroundColor=s.indexLabelBackgroundColor?s.indexLabelBackgroundColor:m.indexLabelBackgroundColor?m.indexLabelBackgroundColor:null,o.indexLabelMaxWidth=s.indexLabelMaxWidth?s.indexLabelMaxWidth:m.indexLabelMaxWidth?m.indexLabelMaxWidth:.33*y.width,o.indexLabelWrap=void 0!==s.indexLabelWrap?s.indexLabelWrap:m.indexLabelWrap,o.startAngle=0===w?m.startAngle?m.startAngle/180*Math.PI:0:f[w-1].endAngle,o.startAngle=(o.startAngle+2*Math.PI)%(2*Math.PI),o.endAngle=o.startAngle+2*Math.PI/C*Math.abs(s.y);var c=(o.endAngle+o.startAngle)/2;c=(c+2*Math.PI)%(2*Math.PI),o.midAngle=c,o.midAngle>Math.PI/2-P&&o.midAngle<Math.PI/2+P?((0===e||f[i].midAngle>o.midAngle)&&(i=w),e++):o.midAngle>3*Math.PI/2-P&&o.midAngle<3*Math.PI/2+P&&((0===t||f[a].midAngle>o.midAngle)&&(a=w),t++),c>Math.PI/2&&c<=3*Math.PI/2?o.hemisphere="left":o.hemisphere="right",o.indexLabelTextBlock=new n.default(x.plotArea.ctx,{fontSize:o.indexLabelFontSize,fontFamily:o.indexLabelFontFamily,fontColor:o.indexLabelFontColor,fontStyle:o.indexLabelFontStyle,fontWeight:o.indexLabelFontWeight,
horizontalAlign:"left",backgroundColor:o.indexLabelBackgroundColor,maxWidth:o.indexLabelMaxWidth,maxHeight:o.indexLabelWrap?5*o.indexLabelFontSize:1.5*o.indexLabelFontSize,text:o.indexLabelText,padding:0,textBaseline:"top"}),o.indexLabelTextBlock.measureText()}var p=0,u=0,g=!1;for(w=0;w<v.length;w++){var o=f[(i+w)%v.length];e>1&&o.midAngle>Math.PI/2-P&&o.midAngle<Math.PI/2+P&&(p<=e/2&&!g?(o.hemisphere="right",p++):(o.hemisphere="left",g=!0))}for(g=!1,w=0;w<v.length;w++){var o=f[(a+w)%v.length];t>1&&o.midAngle>3*Math.PI/2-P&&o.midAngle<3*Math.PI/2+P&&(u<=t/2&&!g?(o.hemisphere="left",u++):(o.hemisphere="right",g=!0))}}}(),c(),c(),c(),c(),this.disableToolTip=!0,this._animator.animate(0,this.animatedRender?this.animationDuration:0,function(e){i(e),p()},function(){x.disableToolTip=!1,x._animator.animate(0,x.animatedRender?500:0,function(e){a(e),p()})})}}};var a=e("../core/text_block"),n=function(e){return e&&e.__esModule?e:{default:e}}(a),s=e("../helpers/utils")},{"../core/text_block":34,"../helpers/utils":39}],8:[function(e,t,i){"use strict";Object.defineProperty(i,"__esModule",{value:!0}),i.default=function(e){function t(){if(b){var e=null;d.lineThickness>0&&i.stroke();for(var t=l.length-1;t>=0;t--)e=l[t],i.lineTo(e.x,e.y),n.lineTo(e.x,e.y);if(i.closePath(),i.globalAlpha=d.fillOpacity,i.fill(),i.globalAlpha=1,n.fill(),d.lineThickness>0){i.beginPath(),i.moveTo(e.x,e.y);for(var t=0;t<l.length;t++)e=l[t],i.lineTo(e.x,e.y);i.stroke()}i.beginPath(),i.moveTo(u,m),n.beginPath(),n.moveTo(u,m),b={x:u,y:m},l=[],l.push({x:u,y:v})}}var i=e.targetCanvasCtx||this.plotArea.ctx;if(!(e.dataSeriesIndexes.length<=0)){var n=this._eventManager.ghostCtx,s=(e.axisX.lineCoordinates,e.axisY.lineCoordinates,[]),r=this.plotArea;i.save(),a.isCanvasSupported&&n.save(),i.beginPath(),i.rect(r.x1,r.y1,r.width,r.height),i.clip(),a.isCanvasSupported&&(n.beginPath(),n.rect(r.x1,r.y1,r.width,r.height),n.clip());for(var o=0;o<e.dataSeriesIndexes.length;o++){var l=[],h=e.dataSeriesIndexes[o],d=this.data[h],c=d.dataPoints,p=d.id;this._eventManager.objectMap[p]={objectType:"dataSeries",dataSeriesIndex:h};var x=intToHexColorString(p);n.fillStyle=x,s=[];var u,m,v,g,y=!0,f=0,b=(e.axisY.conversionParameters.reference,e.axisY.conversionParameters.pixelPerUnit,e.axisY.conversionParameters.minimum,null);if(c.length>0){var M=d._colorSet[f%d._colorSet.length];i.fillStyle=M,i.strokeStyle=M,i.lineWidth=d.lineThickness,i.setLineDash&&i.setLineDash(getLineDashArray(d.lineDashType,d.lineThickness));for(var P=!0;f<c.length;f++)if(!((g=c[f].x.getTime?c[f].x.getTime():c[f].x)<e.axisX.dataInfo.viewPortMin||g>e.axisX.dataInfo.viewPortMax))if(null!==c[f].y&&c[f].y.length&&"number"==typeof c[f].y[0]&&"number"==typeof c[f].y[1]){u=e.axisX.conversionParameters.reference+e.axisX.conversionParameters.pixelPerUnit*(g-e.axisX.conversionParameters.minimum)+.5<<0,m=e.axisY.conversionParameters.reference+e.axisY.conversionParameters.pixelPerUnit*(c[f].y[0]-e.axisY.conversionParameters.minimum)+.5<<0,v=e.axisY.conversionParameters.reference+e.axisY.conversionParameters.pixelPerUnit*(c[f].y[1]-e.axisY.conversionParameters.minimum)+.5<<0,y||P?(i.beginPath(),i.moveTo(u,m),b={x:u,y:m},l=[],l.push({x:u,y:v}),a.isCanvasSupported&&(n.beginPath(),n.moveTo(u,m)),y=!1,P=!1):(i.lineTo(u,m),l.push({x:u,y:v}),a.isCanvasSupported&&n.lineTo(u,m),f%250==0&&t());var S=d.dataPointIds[f];if(this._eventManager.objectMap[S]={id:S,objectType:"dataPoint",dataSeriesIndex:h,dataPointIndex:f,x1:u,y1:m,y2:v},0!==c[f].markerSize&&(c[f].markerSize>0||d.markerSize>0)){var T=d.getMarkerProperties(f,u,v,i);s.push(T);var C=intToHexColorString(S);a.isCanvasSupported&&s.push({x:u,y:v,ctx:n,type:T.type,size:T.size,color:C,borderColor:C,borderThickness:T.borderThickness}),T=d.getMarkerProperties(f,u,m,i),s.push(T);var C=intToHexColorString(S);a.isCanvasSupported&&s.push({x:u,y:m,ctx:n,type:T.type,size:T.size,color:C,borderColor:C,borderThickness:T.borderThickness})}(c[f].indexLabel||d.indexLabel||c[f].indexLabelFormatter||d.indexLabelFormatter)&&(this._indexLabels.push({chartType:"rangeArea",dataPoint:c[f],dataSeries:d,indexKeyword:0,point:{x:u,y:m},direction:c[f].y[0]<=c[f].y[1]?-1:1,color:M}),this._indexLabels.push({chartType:"rangeArea",dataPoint:c[f],dataSeries:d,indexKeyword:1,point:{x:u,y:v},direction:c[f].y[0]<=c[f].y[1]?1:-1,color:M}))}else t(),P=!0;t(),RenderHelper.drawMarkers(s)}}return i.restore(),a.isCanvasSupported&&this._eventManager.ghostCtx.restore(),{source:i,dest:this.plotArea.ctx,animationCallback:AnimationHelper.xClipAnimation,easingFunction:AnimationHelper.easing.linear,animationBase:0}}};var a=e("../helpers/utils")},{"../helpers/utils":39}],9:[function(e,t,i){"use strict";Object.defineProperty(i,"__esModule",{value:!0}),i.default=function(e){var t=e.targetCanvasCtx||this.plotArea.ctx;if(!(e.dataSeriesIndexes.length<=0)){var i,n,s,r,o=null,l=this.plotArea,h=0,d=(e.axisY.conversionParameters.reference,e.axisY.conversionParameters.pixelPerUnit,e.axisY.conversionParameters.minimum,this.dataPointMaxWidth?this.dataPointMaxWidth:Math.min(.15*this.height,this.plotArea.height/e.plotType.totalDataSeries*.9)<<0),c=e.axisX.dataInfo.minDiff,p=l.height/Math.abs(e.axisX.viewportMaximum-e.axisX.viewportMinimum)*Math.abs(c)/e.plotType.totalDataSeries*.9<<0;p>d?p=d:c===1/0?p=d/e.plotType.totalDataSeries*.9:p<1&&(p=1),t.save(),a.isCanvasSupported&&this._eventManager.ghostCtx.save(),t.beginPath(),t.rect(l.x1,l.y1,l.width,l.height),t.clip(),a.isCanvasSupported&&(this._eventManager.ghostCtx.rect(l.x1,l.y1,l.width,l.height),this._eventManager.ghostCtx.clip());for(var x=0;x<e.dataSeriesIndexes.length;x++){var u=e.dataSeriesIndexes[x],m=this.data[u],v=m.dataPoints;if(v.length>0){var g=!!(p>5&&m.bevelEnabled);for(t.strokeStyle="#4572A7 ",h=0;h<v.length;h++)if(!((r=v[h].getTime?v[h].x.getTime():v[h].x)<e.axisX.dataInfo.viewPortMin||r>e.axisX.dataInfo.viewPortMax)&&null!==v[h].y&&v[h].y.length&&"number"==typeof v[h].y[0]&&"number"==typeof v[h].y[1]){i=e.axisY.conversionParameters.reference+e.axisY.conversionParameters.pixelPerUnit*(v[h].y[0]-e.axisY.conversionParameters.minimum)+.5<<0,n=e.axisY.conversionParameters.reference+e.axisY.conversionParameters.pixelPerUnit*(v[h].y[1]-e.axisY.conversionParameters.minimum)+.5<<0,s=e.axisX.conversionParameters.reference+e.axisX.conversionParameters.pixelPerUnit*(r-e.axisX.conversionParameters.minimum)+.5<<0;var y=s-e.plotType.totalDataSeries*p/2+(e.previousDataSeriesCount+x)*p<<0,f=y+p<<0;if(i>n){var b=i;i=n,n=b}o=v[h].color?v[h].color:m._colorSet[h%m._colorSet.length],drawRect(t,i,y,n,f,o,0,null,g,!1,!1,!1,m.fillOpacity);var M=m.dataPointIds[h];this._eventManager.objectMap[M]={id:M,objectType:"dataPoint",dataSeriesIndex:u,dataPointIndex:h,x1:i,y1:y,x2:n,y2:f},o=intToHexColorString(M),a.isCanvasSupported&&drawRect(this._eventManager.ghostCtx,i,y,n,f,o,0,null,!1,!1,!1,!1),(v[h].indexLabel||m.indexLabel||v[h].indexLabelFormatter||m.indexLabelFormatter)&&(this._indexLabels.push({chartType:"rangeBar",dataPoint:v[h],dataSeries:m,indexKeyword:0,point:{x:v[h].y[1]>=v[h].y[0]?i:n,y:y+(f-y)/2},direction:v[h].y[1]>=v[h].y[0]?-1:1,bounds:{x1:Math.min(i,n),y1:y,x2:Math.max(i,n),y2:f},color:o}),this._indexLabels.push({chartType:"rangeBar",dataPoint:v[h],dataSeries:m,indexKeyword:1,point:{x:v[h].y[1]>=v[h].y[0]?n:i,y:y+(f-y)/2},direction:v[h].y[1]>=v[h].y[0]?1:-1,bounds:{x1:Math.min(i,n),y1:y,x2:Math.max(i,n),y2:f},color:o}))}}}return t.restore(),a.isCanvasSupported&&this._eventManager.ghostCtx.restore(),{source:t,dest:this.plotArea.ctx,animationCallback:AnimationHelper.fadeInAnimation,easingFunction:AnimationHelper.easing.easeInQuad,animationBase:0}}};var a=e("../helpers/utils")},{"../helpers/utils":39}],10:[function(e,t,i){"use strict";Object.defineProperty(i,"__esModule",{value:!0}),i.default=function(e){var t=e.targetCanvasCtx||this.plotArea.ctx;if(!(e.dataSeriesIndexes.length<=0)){var i,n,s,r,o=null,l=this.plotArea,h=0,d=(e.axisY.conversionParameters.reference,e.axisY.conversionParameters.pixelPerUnit,e.axisY.conversionParameters.minimum,this.dataPointMaxWidth?this.dataPointMaxWidth:.03*this.width),c=e.axisX.dataInfo.minDiff,p=l.width/Math.abs(e.axisX.viewportMaximum-e.axisX.viewportMinimum)*Math.abs(c)/e.plotType.totalDataSeries*.9<<0;p>d?p=d:c===1/0?p=d/e.plotType.totalDataSeries*.9:p<1&&(p=1),t.save(),a.isCanvasSupported&&this._eventManager.ghostCtx.save(),t.beginPath(),t.rect(l.x1,l.y1,l.width,l.height),t.clip(),a.isCanvasSupported&&(this._eventManager.ghostCtx.rect(l.x1,l.y1,l.width,l.height),this._eventManager.ghostCtx.clip());for(var x=0;x<e.dataSeriesIndexes.length;x++){var u=e.dataSeriesIndexes[x],m=this.data[u],v=m.dataPoints;if(v.length>0){var g=!!(p>5&&m.bevelEnabled);for(h=0;h<v.length;h++)if(!((r=v[h].getTime?v[h].x.getTime():v[h].x)<e.axisX.dataInfo.viewPortMin||r>e.axisX.dataInfo.viewPortMax)&&null!==v[h].y&&v[h].y.length&&"number"==typeof v[h].y[0]&&"number"==typeof v[h].y[1]){i=e.axisX.conversionParameters.reference+e.axisX.conversionParameters.pixelPerUnit*(r-e.axisX.conversionParameters.minimum)+.5<<0,n=e.axisY.conversionParameters.reference+e.axisY.conversionParameters.pixelPerUnit*(v[h].y[0]-e.axisY.conversionParameters.minimum)+.5<<0,s=e.axisY.conversionParameters.reference+e.axisY.conversionParameters.pixelPerUnit*(v[h].y[1]-e.axisY.conversionParameters.minimum)+.5<<0;var n,s,y=i-e.plotType.totalDataSeries*p/2+(e.previousDataSeriesCount+x)*p<<0,f=y+p<<0;if(o=v[h].color?v[h].color:m._colorSet[h%m._colorSet.length],n>s){var b=n;n=s,s=b}var M=m.dataPointIds[h];this._eventManager.objectMap[M]={id:M,objectType:"dataPoint",dataSeriesIndex:u,dataPointIndex:h,x1:y,y1:n,x2:f,y2:s},drawRect(t,y,n,f,s,o,0,o,g,g,!1,!1,m.fillOpacity),o=intToHexColorString(M),a.isCanvasSupported&&drawRect(this._eventManager.ghostCtx,y,n,f,s,o,0,null,!1,!1,!1,!1),(v[h].indexLabel||m.indexLabel||v[h].indexLabelFormatter||m.indexLabelFormatter)&&(this._indexLabels.push({chartType:"rangeColumn",dataPoint:v[h],dataSeries:m,indexKeyword:0,point:{x:y+(f-y)/2,y:v[h].y[1]>=v[h].y[0]?s:n},direction:v[h].y[1]>=v[h].y[0]?-1:1,bounds:{x1:y,y1:Math.min(n,s),x2:f,y2:Math.max(n,s)},color:o}),this._indexLabels.push({chartType:"rangeColumn",dataPoint:v[h],dataSeries:m,indexKeyword:1,point:{x:y+(f-y)/2,y:v[h].y[1]>=v[h].y[0]?n:s},direction:v[h].y[1]>=v[h].y[0]?1:-1,bounds:{x1:y,y1:Math.min(n,s),x2:f,y2:Math.max(n,s)},color:o}))}}}return t.restore(),a.isCanvasSupported&&this._eventManager.ghostCtx.restore(),{source:t,dest:this.plotArea.ctx,animationCallback:AnimationHelper.fadeInAnimation,easingFunction:AnimationHelper.easing.easeInQuad,animationBase:0}}};var a=e("../helpers/utils")},{"../helpers/utils":39}],11:[function(e,t,i){"use strict";Object.defineProperty(i,"__esModule",{value:!0}),i.default=function(e){function t(){var e=getBezierPoints(y,2);if(e.length>0){i.beginPath(),i.moveTo(e[0].x,e[0].y),a.isCanvasSupported&&(n.beginPath(),n.moveTo(e[0].x,e[0].y));for(var t=0;t<e.length-3;t+=3)i.bezierCurveTo(e[t+1].x,e[t+1].y,e[t+2].x,e[t+2].y,e[t+3].x,e[t+3].y),a.isCanvasSupported&&n.bezierCurveTo(e[t+1].x,e[t+1].y,e[t+2].x,e[t+2].y,e[t+3].x,e[t+3].y);h.lineThickness>0&&i.stroke(),e=getBezierPoints(f,2),i.lineTo(f[f.length-1].x,f[f.length-1].y);for(var t=e.length-1;t>2;t-=3)i.bezierCurveTo(e[t-1].x,e[t-1].y,e[t-2].x,e[t-2].y,e[t-3].x,e[t-3].y),a.isCanvasSupported&&n.bezierCurveTo(e[t-1].x,e[t-1].y,e[t-2].x,e[t-2].y,e[t-3].x,e[t-3].y);if(i.closePath(),i.globalAlpha=h.fillOpacity,i.fill(),i.globalAlpha=1,h.lineThickness>0){i.beginPath(),i.moveTo(f[f.length-1].x,f[f.length-1].y);for(var t=e.length-1;t>2;t-=3)i.bezierCurveTo(e[t-1].x,e[t-1].y,e[t-2].x,e[t-2].y,e[t-3].x,e[t-3].y),a.isCanvasSupported&&n.bezierCurveTo(e[t-1].x,e[t-1].y,e[t-2].x,e[t-2].y,e[t-3].x,e[t-3].y);i.stroke()}i.beginPath(),a.isCanvasSupported&&(n.closePath(),n.fill())}}var i=e.targetCanvasCtx||this.plotArea.ctx;if(!(e.dataSeriesIndexes.length<=0)){var n=this._eventManager.ghostCtx,s=(e.axisX.lineCoordinates,e.axisY.lineCoordinates,[]),r=this.plotArea;i.save(),a.isCanvasSupported&&n.save(),i.beginPath(),i.rect(r.x1,r.y1,r.width,r.height),i.clip(),a.isCanvasSupported&&(n.beginPath(),n.rect(r.x1,r.y1,r.width,r.height),n.clip());for(var o=0;o<e.dataSeriesIndexes.length;o++){var l=e.dataSeriesIndexes[o],h=this.data[l],d=h.dataPoints,c=h.id;this._eventManager.objectMap[c]={objectType:"dataSeries",dataSeriesIndex:l};var p=intToHexColorString(c);n.fillStyle=p,s=[];var x,u,m,v,g=0,y=(e.axisY.conversionParameters.reference,e.axisY.conversionParameters.pixelPerUnit,e.axisY.conversionParameters.minimum,[]),f=[];if(d.length>0){for(color=h._colorSet[g%h._colorSet.length],i.fillStyle=color,i.strokeStyle=color,i.lineWidth=h.lineThickness,i.setLineDash&&i.setLineDash(getLineDashArray(h.lineDashType,h.lineThickness));g<d.length;g++)if(!((v=d[g].x.getTime?d[g].x.getTime():d[g].x)<e.axisX.dataInfo.viewPortMin||v>e.axisX.dataInfo.viewPortMax))if(null!==d[g].y&&d[g].y.length&&"number"==typeof d[g].y[0]&&"number"==typeof d[g].y[1]){x=e.axisX.conversionParameters.reference+e.axisX.conversionParameters.pixelPerUnit*(v-e.axisX.conversionParameters.minimum)+.5<<0,u=e.axisY.conversionParameters.reference+e.axisY.conversionParameters.pixelPerUnit*(d[g].y[0]-e.axisY.conversionParameters.minimum)+.5<<0,m=e.axisY.conversionParameters.reference+e.axisY.conversionParameters.pixelPerUnit*(d[g].y[1]-e.axisY.conversionParameters.minimum)+.5<<0;var b=h.dataPointIds[g];if(this._eventManager.objectMap[b]={id:b,objectType:"dataPoint",dataSeriesIndex:l,dataPointIndex:g,x1:x,y1:u,y2:m},y[y.length]={x:x,y:u},f[f.length]={x:x,y:m},0!==d[g].markerSize&&(d[g].markerSize>0||h.markerSize>0)){var M=h.getMarkerProperties(g,x,u,i);s.push(M);var P=intToHexColorString(b);a.isCanvasSupported&&s.push({x:x,y:u,ctx:n,type:M.type,size:M.size,color:P,borderColor:P,borderThickness:M.borderThickness});var M=h.getMarkerProperties(g,x,m,i);s.push(M);var P=intToHexColorString(b);a.isCanvasSupported&&s.push({x:x,y:m,ctx:n,type:M.type,size:M.size,color:P,borderColor:P,borderThickness:M.borderThickness})}(d[g].indexLabel||h.indexLabel||d[g].indexLabelFormatter||h.indexLabelFormatter)&&(this._indexLabels.push({chartType:"splineArea",dataPoint:d[g],dataSeries:h,indexKeyword:0,point:{x:x,y:u},direction:d[g].y[0]<=d[g].y[1]?-1:1,color:color}),this._indexLabels.push({chartType:"splineArea",dataPoint:d[g],dataSeries:h,indexKeyword:1,point:{x:x,y:m},direction:d[g].y[0]<=d[g].y[1]?1:-1,color:color}))}else g>0&&(t(),y=[],f=[]);t(),RenderHelper.drawMarkers(s)}}return i.restore(),a.isCanvasSupported&&this._eventManager.ghostCtx.restore(),{source:i,dest:this.plotArea.ctx,animationCallback:AnimationHelper.xClipAnimation,easingFunction:AnimationHelper.easing.linear,animationBase:0}}};var a=e("../helpers/utils")},{"../helpers/utils":39}],12:[function(e,t,i){"use strict";function a(e){return e&&e.__esModule?e:{default:e}}Object.defineProperty(i,"__esModule",{value:!0}),i.default=function(e){var t=e.targetCanvasCtx||this.plotArea.ctx,i=e.dataSeriesIndexes.length;if(!(i<=0)){var a,n,r,h=this.plotArea,d=0,c=(e.axisY.conversionParameters.reference,e.axisY.conversionParameters.pixelPerUnit,e.axisY.conversionParameters.minimum,this.dataPointMaxWidth?this.dataPointMaxWidth:.15*this.width<<0),p=e.axisX.dataInfo.minDiff,x=h.width/Math.abs(e.axisX.viewportMaximum-e.axisX.viewportMinimum)*Math.abs(p)/i*.9<<0;t.save(),l.isCanvasSupported&&this._eventManager.ghostCtx.save(),t.beginPath(),t.rect(h.x1,h.y1,h.width,h.height),t.clip(),l.isCanvasSupported&&(this._eventManager.ghostCtx.rect(h.x1,h.y1,h.width,h.height),this._eventManager.ghostCtx.clip());for(var u=0;u<e.dataSeriesIndexes.length;u++){var m=e.dataSeriesIndexes[u],v=this.data[m],g=v.dataPoints;if(1==g.length&&(x=c),x<1?x=1:x>c&&(x=c),g.length>0){t.strokeStyle="#4572A7 ";for(var y=(Math.pow(.3*Math.min(h.height,h.width)/2,2),Math.PI,0),f=0,d=0;d<g.length;d++)if(!((r=r=g[d].getTime?g[d].x.getTime():g[d].x)<e.axisX.dataInfo.viewPortMin||r>e.axisX.dataInfo.viewPortMax)&&"number"==typeof g[d].y){a=e.axisX.conversionParameters.reference+e.axisX.conversionParameters.pixelPerUnit*(r-e.axisX.conversionParameters.minimum)+.5<<0,n=e.axisY.conversionParameters.reference+e.axisY.conversionParameters.pixelPerUnit*(g[d].y-e.axisY.conversionParameters.minimum)+.5<<0;var b=v.getMarkerProperties(d,a,n,t);if(t.globalAlpha=v.fillOpacity,s.default.drawMarker(b.x,b.y,b.ctx,b.type,b.size,b.color,b.borderColor,b.borderThickness),t.globalAlpha=1,!(Math.sqrt((y-a)*(y-a)+(f-n)*(f-n))<Math.min(b.size,5)&&g.length>Math.min(this.plotArea.width,this.plotArea.height))){var M=v.dataPointIds[d];this._eventManager.objectMap[M]={id:M,objectType:"dataPoint",dataSeriesIndex:m,dataPointIndex:d,x1:a,y1:n};var P=(0,l.intToHexColorString)(M);l.isCanvasSupported&&s.default.drawMarker(b.x,b.y,this._eventManager.ghostCtx,b.type,b.size,P,P,b.borderThickness),(g[d].indexLabel||v.indexLabel||g[d].indexLabelFormatter||v.indexLabelFormatter)&&this._indexLabels.push({chartType:"scatter",dataPoint:g[d],dataSeries:v,point:{x:a,y:n},direction:1,bounds:{x1:a-b.size/2,y1:n-b.size/2,x2:a+b.size/2,y2:n+b.size/2},color:null}),y=a,f=n}}}}t.restore(),l.isCanvasSupported&&this._eventManager.ghostCtx.restore();return{source:t,dest:this.plotArea.ctx,animationCallback:o.default.fadeInAnimation,easingFunction:o.default.easing.easeInQuad,animationBase:0}}};var n=e("../helpers/render"),s=a(n),r=e("../helpers/animator"),o=a(r),l=e("../helpers/utils")},{"../helpers/animator":37,"../helpers/render":38,"../helpers/utils":39}],13:[function(e,t,i){"use strict";Object.defineProperty(i,"__esModule",{value:!0}),i.default=function(e){function t(e){var t=getBezierPoints(e,2);if(t.length>0){i.beginPath(),a.isCanvasSupported&&n.beginPath(),i.moveTo(t[0].x,t[0].y),a.isCanvasSupported&&n.moveTo(t[0].x,t[0].y);for(var s=0;s<t.length-3;s+=3)i.bezierCurveTo(t[s+1].x,t[s+1].y,t[s+2].x,t[s+2].y,t[s+3].x,t[s+3].y),a.isCanvasSupported&&n.bezierCurveTo(t[s+1].x,t[s+1].y,t[s+2].x,t[s+2].y,t[s+3].x,t[s+3].y),s>0&&s%3e3==0&&(i.stroke(),i.beginPath(),i.moveTo(t[s+3].x,t[s+3].y),a.isCanvasSupported&&(n.stroke(),n.beginPath(),n.moveTo(t[s+3].x,t[s+3].y)));i.stroke(),a.isCanvasSupported&&n.stroke()}}var i=e.targetCanvasCtx||this.plotArea.ctx;if(!(e.dataSeriesIndexes.length<=0)){var n=this._eventManager.ghostCtx;i.save();var s=this.plotArea;i.beginPath(),i.rect(s.x1,s.y1,s.width,s.height),i.clip();for(var r=[],o=0;o<e.dataSeriesIndexes.length;o++){var l=e.dataSeriesIndexes[o],h=this.data[l];i.lineWidth=h.lineThickness;var d=h.dataPoints;i.setLineDash&&i.setLineDash(getLineDashArray(h.lineDashType,h.lineThickness));var c=h.id;this._eventManager.objectMap[c]={objectType:"dataSeries",dataSeriesIndex:l};var p=intToHexColorString(c);n.strokeStyle=p,n.lineWidth=h.lineThickness>0?Math.max(h.lineThickness,4):0;var x=h._colorSet,u=x[0];i.strokeStyle=u;var m,v,g,y=0,f=[];if(i.beginPath(),d.length>0)for(y=0;y<d.length;y++)if(!((g=d[y].getTime?d[y].x.getTime():d[y].x)<e.axisX.dataInfo.viewPortMin||g>e.axisX.dataInfo.viewPortMax))if("number"==typeof d[y].y){m=e.axisX.conversionParameters.reference+e.axisX.conversionParameters.pixelPerUnit*(g-e.axisX.conversionParameters.minimum)+.5<<0,v=e.axisY.conversionParameters.reference+e.axisY.conversionParameters.pixelPerUnit*(d[y].y-e.axisY.conversionParameters.minimum)+.5<<0;var b=h.dataPointIds[y];if(this._eventManager.objectMap[b]={id:b,objectType:"dataPoint",dataSeriesIndex:l,dataPointIndex:y,x1:m,y1:v},f[f.length]={x:m,y:v},d[y].markerSize>0||h.markerSize>0){var M=h.getMarkerProperties(y,m,v,i);r.push(M);var P=intToHexColorString(b);a.isCanvasSupported&&r.push({x:m,y:v,ctx:n,type:M.type,size:M.size,color:P,borderColor:P,borderThickness:M.borderThickness})}(d[y].indexLabel||h.indexLabel||d[y].indexLabelFormatter||h.indexLabelFormatter)&&this._indexLabels.push({chartType:"spline",dataPoint:d[y],dataSeries:h,point:{x:m,y:v},direction:d[y].y>=0?1:-1,color:u})}else y>0&&(t(f),f=[]);t(f)}return RenderHelper.drawMarkers(r),i.restore(),i.beginPath(),a.isCanvasSupported&&n.beginPath(),{source:i,dest:this.plotArea.ctx,animationCallback:AnimationHelper.xClipAnimation,easingFunction:AnimationHelper.easing.linear,animationBase:0}}};var a=e("../helpers/utils")},{"../helpers/utils":39}],14:[function(e,t,i){"use strict";Object.defineProperty(i,"__esModule",{value:!0}),i.default=function(e){function t(){var t=getBezierPoints(P,2);if(t.length>0){i.beginPath(),i.moveTo(t[0].x,t[0].y),a.isCanvasSupported&&(n.beginPath(),n.moveTo(t[0].x,t[0].y));for(var o=0;o<t.length-3;o+=3)i.bezierCurveTo(t[o+1].x,t[o+1].y,t[o+2].x,t[o+2].y,t[o+3].x,t[o+3].y),a.isCanvasSupported&&n.bezierCurveTo(t[o+1].x,t[o+1].y,t[o+2].x,t[o+2].y,t[o+3].x,t[o+3].y);c.lineThickness>0&&i.stroke(),e.axisY.viewportMinimum<=0&&e.axisY.viewportMaximum>=0?y=b:e.axisY.viewportMaximum<0?y=r.y1:e.axisY.viewportMinimum>0&&(y=s.y2),M={x:t[0].x,y:t[0].y},i.lineTo(t[t.length-1].x,y),i.lineTo(M.x,y),i.closePath(),i.globalAlpha=c.fillOpacity,i.fill(),i.globalAlpha=1,a.isCanvasSupported&&(n.lineTo(t[t.length-1].x,y),n.lineTo(M.x,y),n.closePath(),n.fill())}}var i=e.targetCanvasCtx||this.plotArea.ctx;if(!(e.dataSeriesIndexes.length<=0)){var n=this._eventManager.ghostCtx,s=e.axisX.lineCoordinates,r=e.axisY.lineCoordinates,o=[],l=this.plotArea;i.save(),a.isCanvasSupported&&n.save(),i.beginPath(),i.rect(l.x1,l.y1,l.width,l.height),i.clip(),a.isCanvasSupported&&(n.beginPath(),n.rect(l.x1,l.y1,l.width,l.height),n.clip());for(var h=0;h<e.dataSeriesIndexes.length;h++){var d=e.dataSeriesIndexes[h],c=this.data[d],p=c.dataPoints,x=c.id;this._eventManager.objectMap[x]={objectType:"dataSeries",dataSeriesIndex:d};var u=intToHexColorString(x);n.fillStyle=u,o=[];var m,v,g,y,f=0,b=e.axisY.conversionParameters.reference+e.axisY.conversionParameters.pixelPerUnit*(0-e.axisY.conversionParameters.minimum)+.5<<0,M=null,P=[];if(p.length>0){for(color=c._colorSet[f%c._colorSet.length],i.fillStyle=color,i.strokeStyle=color,i.lineWidth=c.lineThickness,i.setLineDash&&i.setLineDash(getLineDashArray(c.lineDashType,c.lineThickness));f<p.length;f++)if(!((g=p[f].x.getTime?p[f].x.getTime():p[f].x)<e.axisX.dataInfo.viewPortMin||g>e.axisX.dataInfo.viewPortMax))if("number"==typeof p[f].y){m=e.axisX.conversionParameters.reference+e.axisX.conversionParameters.pixelPerUnit*(g-e.axisX.conversionParameters.minimum)+.5<<0,v=e.axisY.conversionParameters.reference+e.axisY.conversionParameters.pixelPerUnit*(p[f].y-e.axisY.conversionParameters.minimum)+.5<<0;var S=c.dataPointIds[f];if(this._eventManager.objectMap[S]={id:S,objectType:"dataPoint",dataSeriesIndex:d,dataPointIndex:f,x1:m,y1:v},P[P.length]={x:m,y:v},0!==p[f].markerSize&&(p[f].markerSize>0||c.markerSize>0)){var T=c.getMarkerProperties(f,m,v,i);o.push(T);var C=intToHexColorString(S);a.isCanvasSupported&&o.push({x:m,y:v,ctx:n,type:T.type,size:T.size,color:C,borderColor:C,borderThickness:T.borderThickness})}(p[f].indexLabel||c.indexLabel||p[f].indexLabelFormatter||c.indexLabelFormatter)&&this._indexLabels.push({chartType:"splineArea",dataPoint:p[f],dataSeries:c,point:{x:m,y:v},direction:p[f].y>=0?1:-1,color:color})}else f>0&&(t(),P=[]);t(),RenderHelper.drawMarkers(o)}}return i.restore(),a.isCanvasSupported&&this._eventManager.ghostCtx.restore(),{source:i,dest:this.plotArea.ctx,animationCallback:AnimationHelper.xClipAnimation,easingFunction:AnimationHelper.easing.linear,animationBase:0}}};var a=e("../helpers/utils")},{"../helpers/utils":39}],15:[function(e,t,i){"use strict";function a(e){return e&&e.__esModule?e:{default:e}}Object.defineProperty(i,"__esModule",{value:!0}),i.default=function(e){var t=e.targetCanvasCtx||this.plotArea.ctx;if(!(e.dataSeriesIndexes.length<=0)){var i,a,n,r=null,h=[],d=this.plotArea,c=[],p=[],x=0,u=e.axisY.conversionParameters.reference+e.axisY.conversionParameters.pixelPerUnit*(0-e.axisY.conversionParameters.minimum)<<0,m=(e.axisX.dataInfo.minDiff,this._eventManager.ghostCtx);l.isCanvasSupported&&m.beginPath(),t.save(),l.isCanvasSupported&&m.save(),t.beginPath(),t.rect(d.x1,d.y1,d.width,d.height),t.clip(),l.isCanvasSupported&&(m.beginPath(),m.rect(d.x1,d.y1,d.width,d.height),m.clip());for(var v=[],g=0;g<e.dataSeriesIndexes.length;g++){var y,f=e.dataSeriesIndexes[g],b=this.data[f],M=b.dataPoints;for(b.dataPointIndexes=[],x=0;x<M.length;x++)y=M[x].x.getTime?M[x].x.getTime():M[x].x,b.dataPointIndexes[y]=x,v[y]||(p.push(y),v[y]=!0);p.sort(l.compareNumbers)}for(var g=0;g<e.dataSeriesIndexes.length;g++){var f=e.dataSeriesIndexes[g],b=this.data[f],M=b.dataPoints,P=!0,S=[],T=b.id;this._eventManager.objectMap[T]={objectType:"dataSeries",dataSeriesIndex:f};var C=(0,l.intToHexColorString)(T);if(m.fillStyle=C,p.length>0){for(r=b._colorSet[0],t.fillStyle=r,t.strokeStyle=r,t.lineWidth=b.lineThickness,t.setLineDash&&t.setLineDash((0,l.getLineDashArray)(b.lineDashType,b.lineThickness)),x=0;x<p.length;x++){n=p[x];var k=null;if(k=b.dataPointIndexes[n]>=0?M[b.dataPointIndexes[n]]:{x:n,y:0},!(n<e.axisX.dataInfo.viewPortMin||n>e.axisX.dataInfo.viewPortMax)&&"number"==typeof k.y){var i=e.axisX.conversionParameters.reference+e.axisX.conversionParameters.pixelPerUnit*(n-e.axisX.conversionParameters.minimum)+.5<<0,a=e.axisY.conversionParameters.reference+e.axisY.conversionParameters.pixelPerUnit*(k.y-e.axisY.conversionParameters.minimum),w=c[n]?c[n]:0;if(a-=w,S.push({x:i,y:u-w}),c[n]=u-a,P)t.beginPath(),t.moveTo(i,a),l.isCanvasSupported&&(m.beginPath(),m.moveTo(i,a)),P=!1;else if(t.lineTo(i,a),l.isCanvasSupported&&m.lineTo(i,a),x%250==0){for(b.lineThickness>0&&t.stroke();S.length>0;){var _=S.pop();t.lineTo(_.x,_.y),l.isCanvasSupported&&m.lineTo(_.x,_.y)}t.closePath(),t.globalAlpha=b.fillOpacity,t.fill(),t.globalAlpha=1,t.beginPath(),t.moveTo(i,a),l.isCanvasSupported&&(m.closePath(),m.fill(),m.beginPath(),m.moveTo(i,a)),S.push({x:i,y:u-w})}if(b.dataPointIndexes[n]>=0){var A=b.dataPointIds[b.dataPointIndexes[n]];this._eventManager.objectMap[A]={id:A,objectType:"dataPoint",dataSeriesIndex:f,dataPointIndex:b.dataPointIndexes[n],x1:i,y1:a}}if(b.dataPointIndexes[n]>=0&&0!==k.markerSize&&(k.markerSize>0||b.markerSize>0)){var L=b.getMarkerProperties(x,i,a,t);h.push(L),markerColor=(0,l.intToHexColorString)(A),l.isCanvasSupported&&h.push({x:i,y:a,ctx:m,type:L.type,size:L.size,color:markerColor,borderColor:markerColor,borderThickness:L.borderThickness})}(k.indexLabel||b.indexLabel||k.indexLabelFormatter||b.indexLabelFormatter)&&this._indexLabels.push({chartType:"stackedArea",dataPoint:k,dataSeries:b,point:{x:i,y:a},direction:M[x].y>=0?1:-1,color:r})}}for(b.lineThickness>0&&t.stroke();S.length>0;){var _=S.pop();t.lineTo(_.x,_.y),l.isCanvasSupported&&m.lineTo(_.x,_.y)}t.closePath(),t.globalAlpha=b.fillOpacity,t.fill(),t.globalAlpha=1,t.beginPath(),t.moveTo(i,a),l.isCanvasSupported&&(m.closePath(),m.fill(),m.beginPath(),m.moveTo(i,a))}delete b.dataPointIndexes}return s.default.drawMarkers(h),t.restore(),l.isCanvasSupported&&m.restore(),{source:t,dest:this.plotArea.ctx,animationCallback:o.default.xClipAnimation,easingFunction:o.default.easing.linear,animationBase:0}}};var n=e("../helpers/render"),s=a(n),r=e("../helpers/animator"),o=a(r),l=e("../helpers/utils")},{"../helpers/animator":37,"../helpers/render":38,"../helpers/utils":39}],16:[function(e,t,i){"use strict";function a(e){return e&&e.__esModule?e:{default:e}}Object.defineProperty(i,"__esModule",{value:!0}),i.default=function(e){var t=e.targetCanvasCtx||this.plotArea.ctx;if(!(e.dataSeriesIndexes.length<=0)){var i,a,n,r=null,h=this.plotArea,d=[],c=[],p=[],x=0,u=e.axisY.conversionParameters.reference+e.axisY.conversionParameters.pixelPerUnit*(0-e.axisY.conversionParameters.minimum)<<0,m=this.dataPointMaxWidth?this.dataPointMaxWidth:.15*this.width<<0,v=e.axisX.dataInfo.minDiff,g=h.width/Math.abs(e.axisX.viewportMaximum-e.axisX.viewportMinimum)*Math.abs(v)*.9<<0,y=this._eventManager.ghostCtx;t.save(),l.isCanvasSupported&&y.save(),t.beginPath(),t.rect(h.x1,h.y1,h.width,h.height),t.clip(),l.isCanvasSupported&&(y.beginPath(),y.rect(h.x1,h.y1,h.width,h.height),y.clip());for(var f=[],b=0;b<e.dataSeriesIndexes.length;b++){var M,P=e.dataSeriesIndexes[b],S=this.data[P],T=S.dataPoints;for(S.dataPointIndexes=[],x=0;x<T.length;x++)M=T[x].x.getTime?T[x].x.getTime():T[x].x,S.dataPointIndexes[M]=x,f[M]||(p.push(M),f[M]=!0);p.sort(l.compareNumbers)}for(var b=0;b<e.dataSeriesIndexes.length;b++){var P=e.dataSeriesIndexes[b],S=this.data[P],T=S.dataPoints,C=!0,k=S.id;this._eventManager.objectMap[k]={objectType:"dataSeries",dataSeriesIndex:P};var w=(0,l.intToHexColorString)(k);y.fillStyle=w,1==T.length&&(g=m),g<1?g=1:g>m&&(g=m);var _=[];if(p.length>0){for(r=S._colorSet[x%S._colorSet.length],t.fillStyle=r,t.strokeStyle=r,t.lineWidth=S.lineThickness,t.setLineDash&&t.setLineDash((0,l.getLineDashArray)(S.lineDashType,S.lineThickness)),x=0;x<p.length;x++){n=p[x];var A=null;if(A=S.dataPointIndexes[n]>=0?T[S.dataPointIndexes[n]]:{x:n,y:0},!(n<e.axisX.dataInfo.viewPortMin||n>e.axisX.dataInfo.viewPortMax)&&"number"==typeof A.y){var L;L=0!==e.dataPointYSums[n]?A.y/e.dataPointYSums[n]*100:0;var i=e.axisX.conversionParameters.reference+e.axisX.conversionParameters.pixelPerUnit*(n-e.axisX.conversionParameters.minimum)+.5<<0,a=e.axisY.conversionParameters.reference+e.axisY.conversionParameters.pixelPerUnit*(L-e.axisY.conversionParameters.minimum),I=c[n]?c[n]:0;if(a-=I,_.push({x:i,y:u-I}),c[n]=u-a,C)t.beginPath(),t.moveTo(i,a),l.isCanvasSupported&&(y.beginPath(),y.moveTo(i,a)),C=!1;else if(t.lineTo(i,a),l.isCanvasSupported&&y.lineTo(i,a),x%250==0){for(S.lineThickness>0&&t.stroke();_.length>0;){var B=_.pop();t.lineTo(B.x,B.y),l.isCanvasSupported&&y.lineTo(B.x,B.y)}t.closePath(),t.globalAlpha=S.fillOpacity,t.fill(),t.globalAlpha=1,t.beginPath(),t.moveTo(i,a),l.isCanvasSupported&&(y.closePath(),y.fill(),y.beginPath(),y.moveTo(i,a)),_.push({x:i,y:u-I})}if(S.dataPointIndexes[n]>=0){var F=S.dataPointIds[S.dataPointIndexes[n]];this._eventManager.objectMap[F]={id:F,objectType:"dataPoint",dataSeriesIndex:P,dataPointIndex:S.dataPointIndexes[n],x1:i,y1:a}}if(S.dataPointIndexes[n]>=0&&0!==A.markerSize&&(A.markerSize>0||S.markerSize>0)){var z=S.getMarkerProperties(x,i,a,t);d.push(z),markerColor=(0,l.intToHexColorString)(F),l.isCanvasSupported&&d.push({x:i,y:a,ctx:y,type:z.type,size:z.size,color:markerColor,borderColor:markerColor,borderThickness:z.borderThickness})}(A.indexLabel||S.indexLabel||A.indexLabelFormatter||S.indexLabelFormatter)&&this._indexLabels.push({chartType:"stackedArea100",dataPoint:A,dataSeries:S,point:{x:i,y:a},direction:T[x].y>=0?1:-1,color:r})}}for(S.lineThickness>0&&t.stroke();_.length>0;){var B=_.pop();t.lineTo(B.x,B.y),l.isCanvasSupported&&y.lineTo(B.x,B.y)}t.closePath(),t.globalAlpha=S.fillOpacity,t.fill(),t.globalAlpha=1,t.beginPath(),t.moveTo(i,a),l.isCanvasSupported&&(y.closePath(),y.fill(),y.beginPath(),y.moveTo(i,a))}delete S.dataPointIndexes}return s.default.drawMarkers(d),t.restore(),l.isCanvasSupported&&y.restore(),{source:t,dest:this.plotArea.ctx,animationCallback:o.default.xClipAnimation,easingFunction:o.default.easing.linear,animationBase:0}}};var n=e("../helpers/render"),s=a(n),r=e("../helpers/animator"),o=a(r),l=e("../helpers/utils")},{"../helpers/animator":37,"../helpers/render":38,"../helpers/utils":39}],17:[function(e,t,i){"use strict";Object.defineProperty(i,"__esModule",{value:!0}),i.default=function(e){var t=e.targetCanvasCtx||this.plotArea.ctx;if(!(e.dataSeriesIndexes.length<=0)){var i,n,s,r=null,o=this.plotArea,l=[],h=[],d=0,c=e.axisY.conversionParameters.reference+e.axisY.conversionParameters.pixelPerUnit*(0-e.axisY.conversionParameters.minimum)<<0,p=this.dataPointMaxWidth?this.dataPointMaxWidth:.15*this.height<<0,x=e.axisX.dataInfo.minDiff,u=o.height/Math.abs(e.axisX.viewportMaximum-e.axisX.viewportMinimum)*Math.abs(x)/e.plotType.plotUnits.length*.9<<0;u>p?u=p:x===1/0?u=p:u<1&&(u=1),t.save(),a.isCanvasSupported&&this._eventManager.ghostCtx.save(),t.beginPath(),t.rect(o.x1,o.y1,o.width,o.height),t.clip(),a.isCanvasSupported&&(this._eventManager.ghostCtx.rect(o.x1,o.y1,o.width,o.height),this._eventManager.ghostCtx.clip());for(var m=0;m<e.dataSeriesIndexes.length;m++){var v=e.dataSeriesIndexes[m],g=this.data[v],y=g.dataPoints;if(y.length>0){
var f=!!(u>5&&g.bevelEnabled);for(t.strokeStyle="#4572A7 ",d=0;d<y.length;d++)if(!((s=y[d].x.getTime?y[d].x.getTime():y[d].x)<e.axisX.dataInfo.viewPortMin||s>e.axisX.dataInfo.viewPortMax)&&"number"==typeof y[d].y){n=e.axisX.conversionParameters.reference+e.axisX.conversionParameters.pixelPerUnit*(s-e.axisX.conversionParameters.minimum)+.5<<0,i=e.axisY.conversionParameters.reference+e.axisY.conversionParameters.pixelPerUnit*(y[d].y-e.axisY.conversionParameters.minimum);var b,M,P=n-e.plotType.plotUnits.length*u/2+e.index*u<<0,S=P+u<<0;if(y[d].y>=0){var T=l[s]?l[s]:0;b=c+T,M=i+T,l[s]=T+(M-b)}else{var T=h[s]?h[s]:0;b=i-T,M=c-T,h[s]=T+(M-b)}r=y[d].color?y[d].color:g._colorSet[d%g._colorSet.length],drawRect(t,b,P,M,S,r,0,null,f,!1,!1,!1,g.fillOpacity);var C=g.dataPointIds[d];this._eventManager.objectMap[C]={id:C,objectType:"dataPoint",dataSeriesIndex:v,dataPointIndex:d,x1:b,y1:P,x2:M,y2:S},r=intToHexColorString(C),a.isCanvasSupported&&drawRect(this._eventManager.ghostCtx,b,P,M,S,r,0,null,!1,!1,!1,!1),(y[d].indexLabel||g.indexLabel||y[d].indexLabelFormatter||g.indexLabelFormatter)&&this._indexLabels.push({chartType:"stackedBar",dataPoint:y[d],dataSeries:g,point:{x:y[d].y>=0?M:b,y:n},direction:y[d].y>=0?1:-1,bounds:{x1:Math.min(b,M),y1:P,x2:Math.max(b,M),y2:S},color:r})}}}t.restore(),a.isCanvasSupported&&this._eventManager.ghostCtx.restore();var k=Math.max(c,e.axisX.boundingRect.x2);return{source:t,dest:this.plotArea.ctx,animationCallback:AnimationHelper.xScaleAnimation,easingFunction:AnimationHelper.easing.easeOutQuart,animationBase:k}}};var a=e("../helpers/utils")},{"../helpers/utils":39}],18:[function(e,t,i){"use strict";Object.defineProperty(i,"__esModule",{value:!0}),i.default=function(e){var t=e.targetCanvasCtx||this.plotArea.ctx;if(!(e.dataSeriesIndexes.length<=0)){var i,n,s,r=null,o=this.plotArea,l=[],h=[],d=0,c=e.axisY.conversionParameters.reference+e.axisY.conversionParameters.pixelPerUnit*(0-e.axisY.conversionParameters.minimum)<<0,p=this.dataPointMaxWidth?this.dataPointMaxWidth:.15*this.height<<0,x=e.axisX.dataInfo.minDiff,u=o.height/Math.abs(e.axisX.viewportMaximum-e.axisX.viewportMinimum)*Math.abs(x)/e.plotType.plotUnits.length*.9<<0;u>p?u=p:x===1/0?u=p:u<1&&(u=1),t.save(),a.isCanvasSupported&&this._eventManager.ghostCtx.save(),t.beginPath(),t.rect(o.x1,o.y1,o.width,o.height),t.clip(),a.isCanvasSupported&&(this._eventManager.ghostCtx.rect(o.x1,o.y1,o.width,o.height),this._eventManager.ghostCtx.clip());for(var m=0;m<e.dataSeriesIndexes.length;m++){var v=e.dataSeriesIndexes[m],g=this.data[v],y=g.dataPoints;if(y.length>0){var f=!!(u>5&&g.bevelEnabled);for(t.strokeStyle="#4572A7 ",d=0;d<y.length;d++)if(!((s=y[d].x.getTime?y[d].x.getTime():y[d].x)<e.axisX.dataInfo.viewPortMin||s>e.axisX.dataInfo.viewPortMax)&&"number"==typeof y[d].y){n=e.axisX.conversionParameters.reference+e.axisX.conversionParameters.pixelPerUnit*(s-e.axisX.conversionParameters.minimum)+.5<<0;var b;b=0!==e.dataPointYSums[s]?y[d].y/e.dataPointYSums[s]*100:0,i=e.axisY.conversionParameters.reference+e.axisY.conversionParameters.pixelPerUnit*(b-e.axisY.conversionParameters.minimum);var M,P,S=n-e.plotType.plotUnits.length*u/2+e.index*u<<0,T=S+u<<0;if(y[d].y>=0){var C=l[s]?l[s]:0;M=c+C,P=i+C,l[s]=C+(P-M)}else{var C=h[s]?h[s]:0;M=i-C,P=c-C,h[s]=C+(P-M)}r=y[d].color?y[d].color:g._colorSet[d%g._colorSet.length],drawRect(t,M,S,P,T,r,0,null,f,!1,!1,!1,g.fillOpacity);var k=g.dataPointIds[d];this._eventManager.objectMap[k]={id:k,objectType:"dataPoint",dataSeriesIndex:v,dataPointIndex:d,x1:M,y1:S,x2:P,y2:T},r=intToHexColorString(k),a.isCanvasSupported&&drawRect(this._eventManager.ghostCtx,M,S,P,T,r,0,null,!1,!1,!1,!1),(y[d].indexLabel||g.indexLabel||y[d].indexLabelFormatter||g.indexLabelFormatter)&&this._indexLabels.push({chartType:"stackedBar100",dataPoint:y[d],dataSeries:g,point:{x:y[d].y>=0?P:M,y:n},direction:y[d].y>=0?1:-1,bounds:{x1:Math.min(M,P),y1:S,x2:Math.max(M,P),y2:T},color:r})}}}t.restore(),a.isCanvasSupported&&this._eventManager.ghostCtx.restore();var w=Math.max(c,e.axisX.boundingRect.x2);return{source:t,dest:this.plotArea.ctx,animationCallback:AnimationHelper.xScaleAnimation,easingFunction:AnimationHelper.easing.easeOutQuart,animationBase:w}}};var a=e("../helpers/utils")},{"../helpers/utils":39}],19:[function(e,t,i){"use strict";Object.defineProperty(i,"__esModule",{value:!0}),i.default=function(e){var t=e.targetCanvasCtx||this.plotArea.ctx;if(!(e.dataSeriesIndexes.length<=0)){var i,n,s,r=null,o=this.plotArea,l=[],h=[],d=0,c=e.axisY.conversionParameters.reference+e.axisY.conversionParameters.pixelPerUnit*(0-e.axisY.conversionParameters.minimum)<<0,p=this.dataPointMaxWidth?this.dataPointMaxWidth:.15*this.width<<0,x=e.axisX.dataInfo.minDiff,u=o.width/Math.abs(e.axisX.viewportMaximum-e.axisX.viewportMinimum)*Math.abs(x)/e.plotType.plotUnits.length*.9<<0;u>p?u=p:x===1/0?u=p:u<1&&(u=1),t.save(),a.isCanvasSupported&&this._eventManager.ghostCtx.save(),t.beginPath(),t.rect(o.x1,o.y1,o.width,o.height),t.clip(),a.isCanvasSupported&&(this._eventManager.ghostCtx.rect(o.x1,o.y1,o.width,o.height),this._eventManager.ghostCtx.clip());for(var m=0;m<e.dataSeriesIndexes.length;m++){var v=e.dataSeriesIndexes[m],g=this.data[v],y=g.dataPoints;if(y.length>0){var f=!!(u>5&&g.bevelEnabled);for(t.strokeStyle="#4572A7 ",d=0;d<y.length;d++)if(!((s=y[d].x.getTime?y[d].x.getTime():y[d].x)<e.axisX.dataInfo.viewPortMin||s>e.axisX.dataInfo.viewPortMax)&&"number"==typeof y[d].y){i=e.axisX.conversionParameters.reference+e.axisX.conversionParameters.pixelPerUnit*(s-e.axisX.conversionParameters.minimum)+.5<<0,n=e.axisY.conversionParameters.reference+e.axisY.conversionParameters.pixelPerUnit*(y[d].y-e.axisY.conversionParameters.minimum);var b,M,P=i-e.plotType.plotUnits.length*u/2+e.index*u<<0,S=P+u<<0;if(y[d].y>=0){var T=l[s]?l[s]:0;b=n-T,M=c-T,l[s]=T+(M-b)}else{var T=h[s]?h[s]:0;M=n+T,b=c+T,h[s]=T+(M-b)}r=y[d].color?y[d].color:g._colorSet[d%g._colorSet.length],drawRect(t,P,b,S,M,r,0,null,f&&y[d].y>=0,y[d].y<0&&f,!1,!1,g.fillOpacity);var C=g.dataPointIds[d];this._eventManager.objectMap[C]={id:C,objectType:"dataPoint",dataSeriesIndex:v,dataPointIndex:d,x1:P,y1:b,x2:S,y2:M},r=intToHexColorString(C),a.isCanvasSupported&&drawRect(this._eventManager.ghostCtx,P,b,S,M,r,0,null,!1,!1,!1,!1),(y[d].indexLabel||g.indexLabel||y[d].indexLabelFormatter||g.indexLabelFormatter)&&this._indexLabels.push({chartType:"stackedColumn",dataPoint:y[d],dataSeries:g,point:{x:i,y:y[d].y>=0?b:M},direction:y[d].y>=0?1:-1,bounds:{x1:P,y1:Math.min(b,M),x2:S,y2:Math.max(b,M)},color:r})}}}t.restore(),a.isCanvasSupported&&this._eventManager.ghostCtx.restore();var k=Math.min(c,e.axisY.boundingRect.y2);return{source:t,dest:this.plotArea.ctx,animationCallback:AnimationHelper.yScaleAnimation,easingFunction:AnimationHelper.easing.easeOutQuart,animationBase:k}}};var a=e("../helpers/utils")},{"../helpers/utils":39}],20:[function(e,t,i){"use strict";Object.defineProperty(i,"__esModule",{value:!0}),i.default=function(e){var t=e.targetCanvasCtx||this.plotArea.ctx;if(!(e.dataSeriesIndexes.length<=0)){var i,n,s,r=null,o=this.plotArea,l=[],h=[],d=0,c=e.axisY.conversionParameters.reference+e.axisY.conversionParameters.pixelPerUnit*(0-e.axisY.conversionParameters.minimum)<<0,p=this.dataPointMaxWidth?this.dataPointMaxWidth:.15*this.width<<0,x=e.axisX.dataInfo.minDiff,u=o.width/Math.abs(e.axisX.viewportMaximum-e.axisX.viewportMinimum)*Math.abs(x)/e.plotType.plotUnits.length*.9<<0;u>p?u=p:x===1/0?u=p:u<1&&(u=1),t.save(),a.isCanvasSupported&&this._eventManager.ghostCtx.save(),t.beginPath(),t.rect(o.x1,o.y1,o.width,o.height),t.clip(),a.isCanvasSupported&&(this._eventManager.ghostCtx.rect(o.x1,o.y1,o.width,o.height),this._eventManager.ghostCtx.clip());for(var m=0;m<e.dataSeriesIndexes.length;m++){var v=e.dataSeriesIndexes[m],g=this.data[v],y=g.dataPoints;if(y.length>0){var f=!!(u>5&&g.bevelEnabled);for(d=0;d<y.length;d++)if(!((s=y[d].x.getTime?y[d].x.getTime():y[d].x)<e.axisX.dataInfo.viewPortMin||s>e.axisX.dataInfo.viewPortMax)&&"number"==typeof y[d].y){i=e.axisX.conversionParameters.reference+e.axisX.conversionParameters.pixelPerUnit*(s-e.axisX.conversionParameters.minimum)+.5<<0;var b;b=0!==e.dataPointYSums[s]?y[d].y/e.dataPointYSums[s]*100:0,n=e.axisY.conversionParameters.reference+e.axisY.conversionParameters.pixelPerUnit*(b-e.axisY.conversionParameters.minimum);var M,P,S=i-e.plotType.plotUnits.length*u/2+e.index*u<<0,T=S+u<<0;if(y[d].y>=0){var C=l[s]?l[s]:0;M=n-C,P=c-C,l[s]=C+(P-M)}else{var C=h[s]?h[s]:0;P=n+C,M=c+C,h[s]=C+(P-M)}r=y[d].color?y[d].color:g._colorSet[d%g._colorSet.length],drawRect(t,S,M,T,P,r,0,null,f&&y[d].y>=0,y[d].y<0&&f,!1,!1,g.fillOpacity);var k=g.dataPointIds[d];this._eventManager.objectMap[k]={id:k,objectType:"dataPoint",dataSeriesIndex:v,dataPointIndex:d,x1:S,y1:M,x2:T,y2:P},r=intToHexColorString(k),a.isCanvasSupported&&drawRect(this._eventManager.ghostCtx,S,M,T,P,r,0,null,!1,!1,!1,!1),(y[d].indexLabel||g.indexLabel||y[d].indexLabelFormatter||g.indexLabelFormatter)&&this._indexLabels.push({chartType:"stackedColumn100",dataPoint:y[d],dataSeries:g,point:{x:i,y:y[d].y>=0?M:P},direction:y[d].y>=0?1:-1,bounds:{x1:S,y1:Math.min(M,P),x2:T,y2:Math.max(M,P)},color:r})}}}t.restore(),a.isCanvasSupported&&this._eventManager.ghostCtx.restore();var w=Math.min(c,e.axisY.boundingRect.y2);return{source:t,dest:this.plotArea.ctx,animationCallback:AnimationHelper.yScaleAnimation,easingFunction:AnimationHelper.easing.easeOutQuart,animationBase:w}}};var a=e("../helpers/utils")},{"../helpers/utils":39}],21:[function(e,t,i){"use strict";Object.defineProperty(i,"__esModule",{value:!0}),i.default=function(e){function t(){P&&(c.lineThickness>0&&i.stroke(),e.axisY.viewportMinimum<=0&&e.axisY.viewportMaximum>=0?y=M:e.axisY.viewportMaximum<0?y=r.y1:e.axisY.viewportMinimum>0&&(y=s.y2),i.lineTo(m,y),i.lineTo(P.x,y),i.closePath(),i.globalAlpha=c.fillOpacity,i.fill(),i.globalAlpha=1,a.isCanvasSupported&&(n.lineTo(m,y),n.lineTo(P.x,y),n.closePath(),n.fill()),i.beginPath(),i.moveTo(m,v),n.beginPath(),n.moveTo(m,v),P={x:m,y:v})}var i=e.targetCanvasCtx||this.plotArea.ctx;if(!(e.dataSeriesIndexes.length<=0)){var n=this._eventManager.ghostCtx,s=e.axisX.lineCoordinates,r=e.axisY.lineCoordinates,o=[],l=this.plotArea;i.save(),a.isCanvasSupported&&n.save(),i.beginPath(),i.rect(l.x1,l.y1,l.width,l.height),i.clip(),a.isCanvasSupported&&(n.beginPath(),n.rect(l.x1,l.y1,l.width,l.height),n.clip());for(var h=0;h<e.dataSeriesIndexes.length;h++){var d=e.dataSeriesIndexes[h],c=this.data[d],p=c.dataPoints,x=c.id;this._eventManager.objectMap[x]={objectType:"dataSeries",dataSeriesIndex:d};var u=intToHexColorString(x);n.fillStyle=u,o=[];var m,v,g,y,f=!0,b=0,M=e.axisY.conversionParameters.reference+e.axisY.conversionParameters.pixelPerUnit*(0-e.axisY.conversionParameters.minimum)+.5<<0,P=null,S=!1;if(p.length>0){var T=c._colorSet[b%c._colorSet.length];for(i.fillStyle=T,i.strokeStyle=T,i.lineWidth=c.lineThickness,i.setLineDash&&i.setLineDash(getLineDashArray(c.lineDashType,c.lineThickness));b<p.length;b++)if(!((g=p[b].x.getTime?p[b].x.getTime():p[b].x)<e.axisX.dataInfo.viewPortMin||g>e.axisX.dataInfo.viewPortMax)){var C=v;if("number"==typeof p[b].y){m=e.axisX.conversionParameters.reference+e.axisX.conversionParameters.pixelPerUnit*(g-e.axisX.conversionParameters.minimum)+.5<<0,v=e.axisY.conversionParameters.reference+e.axisY.conversionParameters.pixelPerUnit*(p[b].y-e.axisY.conversionParameters.minimum)+.5<<0,f||S?(i.beginPath(),i.moveTo(m,v),P={x:m,y:v},a.isCanvasSupported&&(n.beginPath(),n.moveTo(m,v)),f=!1,S=!1):(i.lineTo(m,C),a.isCanvasSupported&&n.lineTo(m,C),i.lineTo(m,v),a.isCanvasSupported&&n.lineTo(m,v),b%250==0&&t());var k=c.dataPointIds[b];if(this._eventManager.objectMap[k]={id:k,objectType:"dataPoint",dataSeriesIndex:d,dataPointIndex:b,x1:m,y1:v},0!==p[b].markerSize&&(p[b].markerSize>0||c.markerSize>0)){var w=c.getMarkerProperties(b,m,v,i);o.push(w);var _=intToHexColorString(k);a.isCanvasSupported&&o.push({x:m,y:v,ctx:n,type:w.type,size:w.size,color:_,borderColor:_,borderThickness:w.borderThickness})}(p[b].indexLabel||c.indexLabel||p[b].indexLabelFormatter||c.indexLabelFormatter)&&this._indexLabels.push({chartType:"stepArea",dataPoint:p[b],dataSeries:c,point:{x:m,y:v},direction:p[b].y>=0?1:-1,color:T})}else t(),S=!0}t(),RenderHelper.drawMarkers(o)}}return i.restore(),a.isCanvasSupported&&this._eventManager.ghostCtx.restore(),{source:i,dest:this.plotArea.ctx,animationCallback:AnimationHelper.xClipAnimation,easingFunction:AnimationHelper.easing.linear,animationBase:0}}};var a=e("../helpers/utils")},{"../helpers/utils":39}],22:[function(e,t,i){"use strict";Object.defineProperty(i,"__esModule",{value:!0});i.cultures={en:{}},i.constants={numberDuration:1,yearDuration:314496e5,monthDuration:2592e6,weekDuration:6048e5,dayDuration:864e5,hourDuration:36e5,minuteDuration:6e4,secondDuration:1e3,millisecondDuration:1,dayOfWeekFromInt:["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"]}},{}],23:[function(e,t,i){"use strict";Object.defineProperty(i,"__esModule",{value:!0});i.isDebugMode=!1,i.isCanvasSupported=!!document.createElement("canvas").getContext,i.defaultOptions={Chart:{width:500,height:400,zoomEnabled:!1,zoomType:"x",backgroundColor:"white",theme:"theme1",animationEnabled:!1,animationDuration:1200,dataPointMaxWidth:null,colorSet:"colorSet1",culture:"en",creditText:"CanvasJS.com",interactivityEnabled:!0,exportEnabled:!1,exportFileName:"Chart",rangeChanging:null,rangeChanged:null},Title:{padding:0,text:null,verticalAlign:"top",horizontalAlign:"center",fontSize:20,fontFamily:"Calibri",fontWeight:"normal",fontColor:"black",fontStyle:"normal",borderThickness:0,borderColor:"black",cornerRadius:0,backgroundColor:null,margin:5,wrap:!0,maxWidth:null,dockInsidePlotArea:!1},Subtitle:{padding:0,text:null,verticalAlign:"top",horizontalAlign:"center",fontSize:14,fontFamily:"Calibri",fontWeight:"normal",fontColor:"black",fontStyle:"normal",borderThickness:0,borderColor:"black",cornerRadius:0,backgroundColor:null,margin:2,wrap:!0,maxWidth:null,dockInsidePlotArea:!1},Legend:{name:null,verticalAlign:"center",horizontalAlign:"right",fontSize:14,fontFamily:"calibri",fontWeight:"normal",fontColor:"black",fontStyle:"normal",cursor:null,itemmouseover:null,itemmouseout:null,itemmousemove:null,itemclick:null,dockInsidePlotArea:!1,reversed:!1,maxWidth:null,maxHeight:null,itemMaxWidth:null,itemWidth:null,itemWrap:!0,itemTextFormatter:null},ToolTip:{enabled:!0,shared:!1,animationEnabled:!0,content:null,contentFormatter:null,reversed:!1,backgroundColor:null,borderColor:null,borderThickness:2,cornerRadius:5,fontSize:14,fontColor:"#000000",fontFamily:"Calibri, Arial, Georgia, serif;",fontWeight:"normal",fontStyle:"italic"},Axis:{minimum:null,maximum:null,viewportMinimum:null,viewportMaximum:null,interval:null,intervalType:null,title:null,titleFontColor:"black",titleFontSize:20,titleFontFamily:"arial",titleFontWeight:"normal",titleFontStyle:"normal",labelAngle:0,labelFontFamily:"arial",labelFontColor:"black",labelFontSize:12,labelFontWeight:"normal",labelFontStyle:"normal",labelAutoFit:!1,labelWrap:!0,labelMaxWidth:null,labelFormatter:null,prefix:"",suffix:"",includeZero:!0,tickLength:5,tickColor:"black",tickThickness:1,lineColor:"black",lineThickness:1,lineDashType:"solid",gridColor:"A0A0A0",gridThickness:0,gridDashType:"solid",interlacedColor:null,valueFormatString:null,margin:2,stripLines:[]},StripLine:{value:null,startValue:null,endValue:null,color:"orange",opacity:null,thickness:2,lineDashType:"solid",label:"",labelBackgroundColor:"#EEEEEE",labelFontFamily:"arial",labelFontColor:"orange",labelFontSize:12,labelFontWeight:"normal",labelFontStyle:"normal",labelFormatter:null,showOnTop:!1},DataSeries:{name:null,dataPoints:null,label:"",bevelEnabled:!1,highlightEnabled:!0,cursor:null,indexLabel:"",indexLabelPlacement:"auto",indexLabelOrientation:"horizontal",indexLabelFontColor:"black",indexLabelFontSize:12,indexLabelFontStyle:"normal",indexLabelFontFamily:"Arial",indexLabelFontWeight:"normal",indexLabelBackgroundColor:null,indexLabelLineColor:null,indexLabelLineThickness:1,indexLabelLineDashType:"solid",indexLabelMaxWidth:null,indexLabelWrap:!0,indexLabelFormatter:null,lineThickness:2,lineDashType:"solid",color:null,risingColor:"white",fillOpacity:null,startAngle:0,radius:null,innerRadius:null,type:"column",xValueType:"number",axisYType:"primary",xValueFormatString:null,yValueFormatString:null,zValueFormatString:null,percentFormatString:null,showInLegend:null,legendMarkerType:null,legendMarkerColor:null,legendText:null,legendMarkerBorderColor:null,legendMarkerBorderThickness:null,markerType:"circle",markerColor:null,markerSize:null,markerBorderColor:null,markerBorderThickness:null,mouseover:null,mouseout:null,mousemove:null,click:null,toolTipContent:null,visible:!0},TextBlock:{x:0,y:0,width:null,height:null,maxWidth:null,maxHeight:null,padding:0,angle:0,text:"",horizontalAlign:"center",fontSize:12,fontFamily:"calibri",fontWeight:"normal",fontColor:"black",fontStyle:"normal",borderThickness:0,borderColor:"black",cornerRadius:0,backgroundColor:null,textBaseline:"top"},CultureInfo:{decimalSeparator:".",digitGroupSeparator:",",zoomText:"Zoom",panText:"Pan",resetText:"Reset",menuText:"More Options",saveJPGText:"Save as JPG",savePNGText:"Save as PNG",days:["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"],shortDays:["Sun","Mon","Tue","Wed","Thu","Fri","Sat"],months:["January","February","March","April","May","June","July","August","September","October","November","December"],shortMonths:["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"]}}},{}],24:[function(e,t,i){"use strict";Object.defineProperty(i,"__esModule",{value:!0}),i.themes=i.colorSets=void 0;var a=e("../helpers/utils");i.colorSets={colorSet1:["#369EAD","#C24642","#7F6084","#86B402","#A2D1CF","#C8B631","#6DBCEB","#52514E","#4F81BC","#A064A1","#F79647"],colorSet2:["#4F81BC","#C0504E","#9BBB58","#23BFAA","#8064A1","#4AACC5","#F79647","#33558B"],colorSet3:["#8CA1BC","#36845C","#017E82","#8CB9D0","#708C98","#94838D","#F08891","#0366A7","#008276","#EE7757","#E5BA3A","#F2990B","#03557B","#782970"]},i.themes={theme1:{Chart:{colorSet:"colorSet1"},Title:{fontFamily:a.isCanvasSupported?"Calibri, Optima, Candara, Verdana, Geneva, sans-serif":"calibri",fontSize:33,fontColor:"#3A3A3A",fontWeight:"bold",verticalAlign:"top",margin:5},Subtitle:{fontFamily:a.isCanvasSupported?"Calibri, Optima, Candara, Verdana, Geneva, sans-serif":"calibri",fontSize:16,fontColor:"#3A3A3A",fontWeight:"bold",verticalAlign:"top",margin:5},Axis:{titleFontSize:26,titleFontColor:"#666666",titleFontFamily:a.isCanvasSupported?"Calibri, Optima, Candara, Verdana, Geneva, sans-serif":"calibri",labelFontFamily:a.isCanvasSupported?"Calibri, Optima, Candara, Verdana, Geneva, sans-serif":"calibri",labelFontSize:18,labelFontColor:"grey",tickColor:"#BBBBBB",tickThickness:2,gridThickness:2,gridColor:"#BBBBBB",lineThickness:2,lineColor:"#BBBBBB"},Legend:{verticalAlign:"bottom",horizontalAlign:"center",fontFamily:a.isCanvasSupported?"monospace, sans-serif,arial black":"calibri"},DataSeries:{indexLabelFontColor:"grey",indexLabelFontFamily:a.isCanvasSupported?"Calibri, Optima, Candara, Verdana, Geneva, sans-serif":"calibri",indexLabelFontSize:18,indexLabelLineThickness:1}},theme2:{Chart:{colorSet:"colorSet2"},Title:{fontFamily:"impact, charcoal, arial black, sans-serif",fontSize:32,fontColor:"#333333",verticalAlign:"top",margin:5},Subtitle:{fontFamily:"impact, charcoal, arial black, sans-serif",fontSize:14,fontColor:"#333333",verticalAlign:"top",margin:5},Axis:{titleFontSize:22,titleFontColor:"rgb(98,98,98)",titleFontFamily:a.isCanvasSupported?"monospace, sans-serif,arial black":"arial",titleFontWeight:"bold",labelFontFamily:a.isCanvasSupported?"monospace, Courier New, Courier":"arial",labelFontSize:16,labelFontColor:"grey",labelFontWeight:"bold",tickColor:"grey",tickThickness:2,gridThickness:2,gridColor:"grey",lineColor:"grey",lineThickness:0},Legend:{verticalAlign:"bottom",horizontalAlign:"center",fontFamily:a.isCanvasSupported?"monospace, sans-serif,arial black":"arial"},DataSeries:{indexLabelFontColor:"grey",indexLabelFontFamily:a.isCanvasSupported?"Courier New, Courier, monospace":"arial",indexLabelFontWeight:"bold",indexLabelFontSize:18,indexLabelLineThickness:1}},theme3:{Chart:{colorSet:"colorSet1"},Title:{fontFamily:a.isCanvasSupported?"Candara, Optima, Trebuchet MS, Helvetica Neue, Helvetica, Trebuchet MS, serif":"calibri",fontSize:32,fontColor:"#3A3A3A",fontWeight:"bold",verticalAlign:"top",margin:5},Subtitle:{fontFamily:a.isCanvasSupported?"Candara, Optima, Trebuchet MS, Helvetica Neue, Helvetica, Trebuchet MS, serif":"calibri",fontSize:16,fontColor:"#3A3A3A",fontWeight:"bold",verticalAlign:"top",margin:5},Axis:{titleFontSize:22,titleFontColor:"rgb(98,98,98)",titleFontFamily:a.isCanvasSupported?"Verdana, Geneva, Calibri, sans-serif":"calibri",labelFontFamily:a.isCanvasSupported?"Calibri, Optima, Candara, Verdana, Geneva, sans-serif":"calibri",labelFontSize:18,labelFontColor:"grey",tickColor:"grey",tickThickness:2,gridThickness:2,gridColor:"grey",lineThickness:2,lineColor:"grey"},Legend:{verticalAlign:"bottom",horizontalAlign:"center",fontFamily:a.isCanvasSupported?"monospace, sans-serif,arial black":"calibri"},DataSeries:{bevelEnabled:!0,indexLabelFontColor:"grey",indexLabelFontFamily:a.isCanvasSupported?"Candara, Optima, Calibri, Verdana, Geneva, sans-serif":"calibri",indexLabelFontSize:18,indexLabelLineColor:"lightgrey",indexLabelLineThickness:2}}}},{"../helpers/utils":39}],25:[function(e,t,i){"use strict";function a(e){this.chart=e,this.ctx=this.chart.plotArea.ctx,this.animations=[],this.animationRequestId=null}Object.defineProperty(i,"__esModule",{value:!0});var n=e("../helpers/animator"),s=function(e){return e&&e.__esModule?e:{default:e}}(n);a.prototype.animate=function(e,t,i,a,n){var r=this;this.chart.isAnimating=!0,n=n||s.default.easing.linear,i&&this.animations.push({startTime:(new Date).getTime()+(e||0),duration:t,animationCallback:i,onComplete:a});for(var o=[];this.animations.length>0;){var l=this.animations.shift(),h=(new Date).getTime(),d=0;l.startTime<=h&&(d=n(Math.min(h-l.startTime,l.duration),0,1,l.duration),d=Math.min(d,1),!isNaN(d)&&isFinite(d)||(d=1)),d<1&&o.push(l),l.animationCallback(d),d>=1&&l.onComplete&&l.onComplete()}this.animations=o,this.animations.length>0?this.animationRequestId=this.chart.requestAnimFrame.call(window,function(){r.animate.call(r)}):this.chart.isAnimating=!1},a.prototype.cancelAllAnimations=function(){this.animations=[],this.animationRequestId&&this.chart.cancelRequestAnimFrame.call(window,this.animationRequestId),this.animationRequestId=null,this.chart.isAnimating=!1},i.default=a},{"../helpers/animator":37}],26:[function(e,t,i){"use strict";function a(e){return e&&e.__esModule?e:{default:e}}function n(e,t,i,a){if(n.base.constructor.call(this,"Axis",t,e.theme),this.chart=e,this.canvas=e.canvas,this.ctx=e.ctx,this.maxWidth=0,this.maxHeight=0,this.intervalStartPosition=0,this.labels=[],this._labels=null,this.dataInfo={min:1/0,max:-1/0,viewPortMin:1/0,viewPortMax:-1/0,minDiff:1/0},"axisX"===i?(this.sessionVariables=this.chart.sessionVariables[i],this._options.interval||(this.intervalType=null)):this.sessionVariables="left"===a||"top"===a?this.chart.sessionVariables.axisY:this.chart.sessionVariables.axisY2,void 0===this._options.titleFontSize&&(this.titleFontSize=this.chart.getAutoFontSize(this.titleFontSize)),void 0===this._options.labelFontSize&&(this.labelFontSize=this.chart.getAutoFontSize(this.labelFontSize)),this.type=i,"axisX"!==i||t&&void 0!==t.gridThickness||(this.gridThickness=0),this._position=a,this.lineCoordinates={x1:null,y1:null,x2:null,y2:null,width:null},this.labelAngle=(this.labelAngle%360+360)%360,this.labelAngle>90&&this.labelAngle<=270?this.labelAngle-=180:this.labelAngle>180&&this.labelAngle<=270?this.labelAngle-=180:this.labelAngle>270&&this.labelAngle<=360&&(this.labelAngle-=360),this._options.stripLines&&this._options.stripLines.length>0){this.stripLines=[];for(var s=0;s<this._options.stripLines.length;s++)this.stripLines.push(new StripLine(this.chart,this._options.stripLines[s],e.theme,++this.chart._eventManager.lastObjectId,this))}this._titleTextBlock=null,this.hasOptionChanged("viewportMinimum")||isNaN(this.sessionVariables.newViewportMinimum)||null===this.sessionVariables.newViewportMinimum?this.sessionVariables.newViewportMinimum=null:this.viewportMinimum=this.sessionVariables.newViewportMinimum,this.hasOptionChanged("viewportMaximum")||isNaN(this.sessionVariables.newViewportMaximum)||null===this.sessionVariables.newViewportMaximum?this.sessionVariables.newViewportMaximum=null:this.viewportMaximum=this.sessionVariables.newViewportMaximum,null!==this.minimum&&null!==this.viewportMinimum&&(this.viewportMinimum=Math.max(this.viewportMinimum,this.minimum)),null!==this.maximum&&null!==this.viewportMaximum&&(this.viewportMaximum=Math.min(this.viewportMaximum,this.maximum)),this.trackChanges("viewportMinimum"),this.trackChanges("viewportMaximum")}Object.defineProperty(i,"__esModule",{value:!0});var s=e("./canvasjs"),r=a(s),o=e("./text_block"),l=a(o),h=e("../helpers/utils");(0,h.extend)(n,r.default),n.prototype.createLabels=function(){var e,t,i=0,a=0,n=0,s=0;if("bottom"===this._position||"top"===this._position?(s=this.lineCoordinates.width/Math.abs(this.viewportMaximum-this.viewportMinimum)*this.interval,a=this.labelAutoFit?void 0===this._options.labelMaxWidth?.9*s>>0:this.labelMaxWidth:void 0===this._options.labelMaxWidth?.7*this.chart.width>>0:this.labelMaxWidth,n=void 0===this._options.labelWrap||this.labelWrap?.5*this.chart.height>>0:1.5*this.labelFontSize):"left"!==this._position&&"right"!==this._position||(s=this.lineCoordinates.height/Math.abs(this.viewportMaximum-this.viewportMinimum)*this.interval,a=this.labelAutoFit?void 0===this._options.labelMaxWidth?.3*this.chart.width>>0:this.labelMaxWidth:void 0===this._options.labelMaxWidth?.5*this.chart.width>>0:this.labelMaxWidth,n=void 0===this._options.labelWrap||this.labelWrap?2*s>>0:1.5*this.labelFontSize),"axisX"===this.type&&"dateTime"===this.chart.plotInfo.axisXValueType)for(t=addToDateTime(new Date(this.viewportMaximum),this.interval,this.intervalType),i=this.intervalStartPosition;i<t;addToDateTime(i,this.interval,this.intervalType)){var r=i.getTime(),o=this.labelFormatter?this.labelFormatter({chart:this.chart,axis:this._options,value:i,label:this.labels[i]?this.labels[i]:null}):"axisX"===this.type&&this.labels[r]?this.labels[r]:dateFormat(i,this.valueFormatString,this.chart._cultureInfo);e=new l.default(this.ctx,{x:0,y:0,maxWidth:a,maxHeight:n,angle:this.labelAngle,text:this.prefix+o+this.suffix,horizontalAlign:"left",fontSize:this.labelFontSize,fontFamily:this.labelFontFamily,fontWeight:this.labelFontWeight,fontColor:this.labelFontColor,fontStyle:this.labelFontStyle,textBaseline:"middle"}),this._labels.push({position:i.getTime(),textBlock:e,effectiveHeight:null})}else{if(t=this.viewportMaximum,this.labels&&this.labels.length){var d=Math.ceil(this.interval),c=Math.ceil(this.intervalStartPosition),p=!1;for(i=c;i<this.viewportMaximum;i+=d){if(!this.labels[i]){p=!1;break}p=!0}p&&(this.interval=d,this.intervalStartPosition=c)}for(i=this.intervalStartPosition;i<=t;i=parseFloat((i+this.interval).toFixed(14))){var o=this.labelFormatter?this.labelFormatter({chart:this.chart,axis:this._options,value:i,label:this.labels[i]?this.labels[i]:null}):"axisX"===this.type&&this.labels[i]?this.labels[i]:(0,h.numberFormat)(i,this.valueFormatString,this.chart._cultureInfo);e=new l.default(this.ctx,{x:0,y:0,maxWidth:a,maxHeight:n,angle:this.labelAngle,text:this.prefix+o+this.suffix,horizontalAlign:"left",fontSize:this.labelFontSize,fontFamily:this.labelFontFamily,fontWeight:this.labelFontWeight,fontColor:this.labelFontColor,fontStyle:this.labelFontStyle,textBaseline:"middle",borderThickness:0}),this._labels.push({position:i,textBlock:e,effectiveHeight:null})}}for(var i=0;i<this.stripLines.length;i++){var x=this.stripLines[i];e=new l.default(this.ctx,{x:0,y:0,backgroundColor:x.labelBackgroundColor,maxWidth:a,maxHeight:n,angle:this.labelAngle,text:x.labelFormatter?x.labelFormatter({chart:this.chart,axis:this,stripLine:x}):x.label,horizontalAlign:"left",fontSize:x.labelFontSize,fontFamily:x.labelFontFamily,fontWeight:x.labelFontWeight,fontColor:x._options.labelFontColor||x.color,fontStyle:x.labelFontStyle,textBaseline:"middle",borderThickness:0}),this._labels.push({position:x.value,textBlock:e,effectiveHeight:null,stripLine:x})}},n.prototype.createLabelsAndCalculateWidth=function(){var e=0;if(this._labels=[],"left"===this._position||"right"===this._position){this.createLabels();for(var t=0;t<this._labels.length;t++){var i=this._labels[t].textBlock,a=i.measureText(),n=0;n=0===this.labelAngle?a.width:a.width*Math.cos(Math.PI/180*Math.abs(this.labelAngle))+a.height/2*Math.sin(Math.PI/180*Math.abs(this.labelAngle)),e<n&&(e=n),this._labels[t].effectiveWidth=n}}return(this.title?getFontHeightInPixels(this.titleFontFamily,this.titleFontSize,this.titleFontWeight)+2:0)+e+this.tickLength+5},n.prototype.createLabelsAndCalculateHeight=function(){var e=0;this._labels=[];var t,i=0;if(this.createLabels(),"bottom"===this._position||"top"===this._position)for(i=0;i<this._labels.length;i++){t=this._labels[i].textBlock;var a=t.measureText(),n=0;n=0===this.labelAngle?a.height:a.width*Math.sin(Math.PI/180*Math.abs(this.labelAngle))+a.height/2*Math.cos(Math.PI/180*Math.abs(this.labelAngle)),e<n&&(e=n),this._labels[i].effectiveHeight=n}return(this.title?getFontHeightInPixels(this.titleFontFamily,this.titleFontSize,this.titleFontWeight)+2:0)+e+this.tickLength+5},n.setLayoutAndRender=function(e,t,i,a,n){var s,r,o,l,h=e.chart,d=h.ctx;e.calculateAxisParameters(),t&&t.calculateAxisParameters(),i&&i.calculateAxisParameters();var c=(t&&t.lineThickness&&t.lineThickness,i&&i.lineThickness&&i.lineThickness,t&&t.gridThickness&&t.gridThickness,i&&i.gridThickness&&i.gridThickness,t?t.margin:0);t&&t.margin;if("normal"===a){e.lineCoordinates={};var p=Math.ceil(t?t.createLabelsAndCalculateWidth():0);s=Math.round(n.x1+p+c),e.lineCoordinates.x1=s;var x=Math.ceil(i?i.createLabelsAndCalculateWidth():0);o=Math.round(n.x2-x>e.chart.width-10?e.chart.width-10:n.x2-x),e.lineCoordinates.x2=o,e.lineCoordinates.width=Math.abs(o-s);var u=Math.ceil(e.createLabelsAndCalculateHeight());r=Math.round(n.y2-u-e.margin),l=Math.round(n.y2-e.margin),e.lineCoordinates.y1=r,e.lineCoordinates.y2=r,e.boundingRect={x1:s,y1:r,x2:o,y2:l,width:o-s,height:l-r},t&&(s=Math.round(n.x1+t.margin),r=Math.round(n.y1<10?10:n.y1),o=Math.round(n.x1+p+t.margin),l=Math.round(n.y2-u-e.margin),t.lineCoordinates={x1:o,y1:r,x2:o,y2:l,height:Math.abs(l-r)},t.boundingRect={x1:s,y1:r,x2:o,y2:l,width:o-s,height:l-r}),i&&(s=Math.round(e.lineCoordinates.x2),r=Math.round(n.y1<10?10:n.y1),o=Math.round(s+x+i.margin),l=Math.round(n.y2-u-e.margin),i.lineCoordinates={x1:s,y1:r,x2:s,y2:l,height:Math.abs(l-r)},i.boundingRect={x1:s,y1:r,x2:o,y2:l,width:o-s,height:l-r}),e.calculateValueToPixelConversionParameters(),t&&t.calculateValueToPixelConversionParameters(),i&&i.calculateValueToPixelConversionParameters(),d.save(),d.rect(5,e.boundingRect.y1,e.chart.width-10,e.boundingRect.height),d.clip(),e.renderLabelsTicksAndTitle(),d.restore(),t&&t.renderLabelsTicksAndTitle(),i&&i.renderLabelsTicksAndTitle(),h.preparePlotArea();var m=e.chart.plotArea;d.save(),d.rect(m.x1,m.y1,Math.abs(m.x2-m.x1),Math.abs(m.y2-m.y1)),d.clip(),e.renderStripLinesOfThicknessType("value"),t&&t.renderStripLinesOfThicknessType("value"),i&&i.renderStripLinesOfThicknessType("value"),e.renderInterlacedColors(),t&&t.renderInterlacedColors(),i&&i.renderInterlacedColors(),d.restore(),e.renderGrid(),t&&t.renderGrid(),i&&i.renderGrid(),e.renderAxisLine(),t&&t.renderAxisLine(),i&&i.renderAxisLine(),e.renderStripLinesOfThicknessType("pixel"),t&&t.renderStripLinesOfThicknessType("pixel"),i&&i.renderStripLinesOfThicknessType("pixel")}else{var v=Math.ceil(e.createLabelsAndCalculateWidth());t&&(t.lineCoordinates={},
s=Math.round(n.x1+v+e.margin),o=Math.round(n.x2>t.chart.width-10?t.chart.width-10:n.x2),t.lineCoordinates.x1=s,t.lineCoordinates.x2=o,t.lineCoordinates.width=Math.abs(o-s)),i&&(i.lineCoordinates={},s=Math.round(n.x1+v+e.margin),o=Math.round(n.x2>i.chart.width-10?i.chart.width-10:n.x2),i.lineCoordinates.x1=s,i.lineCoordinates.x2=o,i.lineCoordinates.width=Math.abs(o-s));var g=Math.ceil(t?t.createLabelsAndCalculateHeight():0),y=Math.ceil(i?i.createLabelsAndCalculateHeight():0);t&&(r=Math.round(n.y2-g-t.margin),l=Math.round(n.y2-c>t.chart.height-10?t.chart.height-10:n.y2-c),t.lineCoordinates.y1=r,t.lineCoordinates.y2=r,t.boundingRect={x1:s,y1:r,x2:o,y2:l,width:o-s,height:g}),i&&(r=Math.round(n.y1+i.margin),l=n.y1+i.margin+y,i.lineCoordinates.y1=l,i.lineCoordinates.y2=l,i.boundingRect={x1:s,y1:r,x2:o,y2:l,width:o-s,height:y}),s=Math.round(n.x1+e.margin),r=Math.round(i?i.lineCoordinates.y2:n.y1<10?10:n.y1),o=Math.round(n.x1+v+e.margin),l=Math.round(t?t.lineCoordinates.y1:n.y2-c>e.chart.height-10?e.chart.height-10:n.y2-c),e.lineCoordinates={x1:o,y1:r,x2:o,y2:l,height:Math.abs(l-r)},e.boundingRect={x1:s,y1:r,x2:o,y2:l,width:o-s,height:l-r},e.calculateValueToPixelConversionParameters(),t&&t.calculateValueToPixelConversionParameters(),i&&i.calculateValueToPixelConversionParameters(),t&&t.renderLabelsTicksAndTitle(),i&&i.renderLabelsTicksAndTitle(),e.renderLabelsTicksAndTitle(),h.preparePlotArea();var m=e.chart.plotArea;d.save(),d.rect(m.x1,m.y1,Math.abs(m.x2-m.x1),Math.abs(m.y2-m.y1)),d.clip(),e.renderStripLinesOfThicknessType("value"),t&&t.renderStripLinesOfThicknessType("value"),i&&i.renderStripLinesOfThicknessType("value"),e.renderInterlacedColors(),t&&t.renderInterlacedColors(),i&&i.renderInterlacedColors(),d.restore(),e.renderGrid(),t&&t.renderGrid(),i&&i.renderGrid(),e.renderAxisLine(),t&&t.renderAxisLine(),i&&i.renderAxisLine(),e.renderStripLinesOfThicknessType("pixel"),t&&t.renderStripLinesOfThicknessType("pixel"),i&&i.renderStripLinesOfThicknessType("pixel")}},n.prototype.renderLabelsTicksAndTitle=function(){var e=!1,t=0,i=1,a=0;this.conversionParameters.pixelPerUnit,this.interval;if(0!==this.labelAngle&&360!==this.labelAngle&&(i=1.2),void 0===this._options.interval){if("bottom"===this._position||"top"===this._position){for(o=0;o<this._labels.length;o++)if(s=this._labels[o],!(s.position<this.viewportMinimum||s.stripLine)){var n=s.textBlock.width*Math.cos(Math.PI/180*this.labelAngle)+s.textBlock.height*Math.sin(Math.PI/180*this.labelAngle);t+=n}t>this.lineCoordinates.width*i&&(e=!0)}if("left"===this._position||"right"===this._position){for(o=0;o<this._labels.length;o++)if(s=this._labels[o],!(s.position<this.viewportMinimum||s.stripLine)){var n=s.textBlock.height*Math.cos(Math.PI/180*this.labelAngle)+s.textBlock.width*Math.sin(Math.PI/180*this.labelAngle);t+=n}t>this.lineCoordinates.height*i&&(e=!0)}}if("bottom"===this._position){var s,r,o=0;for(o=0;o<this._labels.length;o++)if(s=this._labels[o],!(s.position<this.viewportMinimum||s.position>this.viewportMaximum)){if(r=this.getPixelCoordinatesOnAxis(s.position),this.tickThickness&&!this._labels[o].stripLine||this._labels[o].stripLine&&"pixel"===this._labels[o].stripLine._thicknessType){this._labels[o].stripLine?(d=this._labels[o].stripLine,this.ctx.lineWidth=d.thickness,this.ctx.strokeStyle=d.color):(this.ctx.lineWidth=this.tickThickness,this.ctx.strokeStyle=this.tickColor);var h=this.ctx.lineWidth%2==1?.5+(r.x<<0):r.x<<0;this.ctx.beginPath(),this.ctx.moveTo(h,r.y<<0),this.ctx.lineTo(h,r.y+this.tickLength<<0),this.ctx.stroke()}e&&a++%2!=0&&!this._labels[o].stripLine||(0===s.textBlock.angle?(r.x-=s.textBlock.width/2,r.y+=this.tickLength+s.textBlock.fontSize/2):(r.x-=this.labelAngle<0?s.textBlock.width*Math.cos(Math.PI/180*this.labelAngle):0,r.y+=this.tickLength+Math.abs(this.labelAngle<0?s.textBlock.width*Math.sin(Math.PI/180*this.labelAngle)-5:5)),s.textBlock.x=r.x,s.textBlock.y=r.y,s.textBlock.render(!0))}this.title&&(this._titleTextBlock=new l.default(this.ctx,{x:this.lineCoordinates.x1,y:this.boundingRect.y2-this.titleFontSize-5,maxWidth:this.lineCoordinates.width,maxHeight:1.5*this.titleFontSize,angle:0,text:this.title,horizontalAlign:"center",fontSize:this.titleFontSize,fontFamily:this.titleFontFamily,fontWeight:this.titleFontWeight,fontColor:this.titleFontColor,fontStyle:this.titleFontStyle,textBaseline:"top"}),this._titleTextBlock.measureText(),this._titleTextBlock.x=this.lineCoordinates.x1+this.lineCoordinates.width/2-this._titleTextBlock.width/2,this._titleTextBlock.y=this.boundingRect.y2-this._titleTextBlock.height-3,this._titleTextBlock.render(!0))}else if("top"===this._position){var s,r,d,o=0;for(o=0;o<this._labels.length;o++)if(s=this._labels[o],!(s.position<this.viewportMinimum||s.position>this.viewportMaximum)){if(r=this.getPixelCoordinatesOnAxis(s.position),this.tickThickness&&!this._labels[o].stripLine||this._labels[o].stripLine&&"pixel"===this._labels[o].stripLine._thicknessType){this._labels[o].stripLine?(d=this._labels[o].stripLine,this.ctx.lineWidth=d.thickness,this.ctx.strokeStyle=d.color):(this.ctx.lineWidth=this.tickThickness,this.ctx.strokeStyle=this.tickColor);var h=this.ctx.lineWidth%2==1?.5+(r.x<<0):r.x<<0;this.ctx.beginPath(),this.ctx.moveTo(h,r.y<<0),this.ctx.lineTo(h,r.y-this.tickLength<<0),this.ctx.stroke()}e&&a++%2!=0&&!this._labels[o].stripLine||(0===s.textBlock.angle?(r.x-=s.textBlock.width/2,r.y-=this.tickLength+s.textBlock.height/2):(r.x-=this.labelAngle>0?s.textBlock.width*Math.cos(Math.PI/180*this.labelAngle):0,r.y-=this.tickLength+Math.abs(this.labelAngle>0?s.textBlock.width*Math.sin(Math.PI/180*this.labelAngle)+5:5)),s.textBlock.x=r.x,s.textBlock.y=r.y,s.textBlock.render(!0))}this.title&&(this._titleTextBlock=new l.default(this.ctx,{x:this.lineCoordinates.x1,y:this.boundingRect.y1+1,maxWidth:this.lineCoordinates.width,maxHeight:1.5*this.titleFontSize,angle:0,text:this.title,horizontalAlign:"center",fontSize:this.titleFontSize,fontFamily:this.titleFontFamily,fontWeight:this.titleFontWeight,fontColor:this.titleFontColor,fontStyle:this.titleFontStyle,textBaseline:"top"}),this._titleTextBlock.measureText(),this._titleTextBlock.x=this.lineCoordinates.x1+this.lineCoordinates.width/2-this._titleTextBlock.width/2,this._titleTextBlock.render(!0))}else if("left"===this._position){for(var s,r,o=0;o<this._labels.length;o++)if(s=this._labels[o],!(s.position<this.viewportMinimum||s.position>this.viewportMaximum)){if(r=this.getPixelCoordinatesOnAxis(s.position),this.tickThickness&&!this._labels[o].stripLine||this._labels[o].stripLine&&"pixel"===this._labels[o].stripLine._thicknessType){this._labels[o].stripLine?(d=this._labels[o].stripLine,this.ctx.lineWidth=d.thickness,this.ctx.strokeStyle=d.color):(this.ctx.lineWidth=this.tickThickness,this.ctx.strokeStyle=this.tickColor);var c=this.ctx.lineWidth%2==1?.5+(r.y<<0):r.y<<0;this.ctx.beginPath(),this.ctx.moveTo(r.x<<0,c),this.ctx.lineTo(r.x-this.tickLength<<0,c),this.ctx.stroke()}e&&a++%2!=0&&!this._labels[o].stripLine||(s.textBlock.x=r.x-s.textBlock.width*Math.cos(Math.PI/180*this.labelAngle)-this.tickLength-5,0===this.labelAngle?s.textBlock.y=r.y:s.textBlock.y=r.y-s.textBlock.width*Math.sin(Math.PI/180*this.labelAngle),s.textBlock.render(!0))}if(this.title){this._titleTextBlock=new l.default(this.ctx,{x:this.boundingRect.x1+1,y:this.lineCoordinates.y2,maxWidth:this.lineCoordinates.height,maxHeight:1.5*this.titleFontSize,angle:-90,text:this.title,horizontalAlign:"center",fontSize:this.titleFontSize,fontFamily:this.titleFontFamily,fontWeight:this.titleFontWeight,fontColor:this.titleFontColor,fontStyle:this.titleFontStyle,textBaseline:"top"});this._titleTextBlock.measureText();this._titleTextBlock.y=this.lineCoordinates.height/2+this._titleTextBlock.width/2+this.lineCoordinates.y1,this._titleTextBlock.render(!0)}}else if("right"===this._position){for(var s,r,o=0;o<this._labels.length;o++)if(s=this._labels[o],!(s.position<this.viewportMinimum||s.position>this.viewportMaximum)){if(r=this.getPixelCoordinatesOnAxis(s.position),this.tickThickness&&!this._labels[o].stripLine||this._labels[o].stripLine&&"pixel"===this._labels[o].stripLine._thicknessType){this._labels[o].stripLine?(d=this._labels[o].stripLine,this.ctx.lineWidth=d.thickness,this.ctx.strokeStyle=d.color):(this.ctx.lineWidth=this.tickThickness,this.ctx.strokeStyle=this.tickColor);var c=this.ctx.lineWidth%2==1?.5+(r.y<<0):r.y<<0;this.ctx.beginPath(),this.ctx.moveTo(r.x<<0,c),this.ctx.lineTo(r.x+this.tickLength<<0,c),this.ctx.stroke()}e&&a++%2!=0&&!this._labels[o].stripLine||(s.textBlock.x=r.x+this.tickLength+5,this.labelAngle,s.textBlock.y=r.y,s.textBlock.render(!0))}this.title&&(this._titleTextBlock=new l.default(this.ctx,{x:this.boundingRect.x2-1,y:this.lineCoordinates.y2,maxWidth:this.lineCoordinates.height,maxHeight:1.5*this.titleFontSize,angle:90,text:this.title,horizontalAlign:"center",fontSize:this.titleFontSize,fontFamily:this.titleFontFamily,fontWeight:this.titleFontWeight,fontColor:this.titleFontColor,fontStyle:this.titleFontStyle,textBaseline:"top"}),this._titleTextBlock.measureText(),this._titleTextBlock.y=this.lineCoordinates.height/2-this._titleTextBlock.width/2+this.lineCoordinates.y1,this._titleTextBlock.render(!0))}},n.prototype.renderInterlacedColors=function(){var e,t,i=this.chart.plotArea.ctx,a=this.chart.plotArea,n=0,s=!0;if("bottom"!==this._position&&"top"!==this._position||!this.interlacedColor){if(("left"===this._position||"right"===this._position)&&this.interlacedColor)for(i.fillStyle=this.interlacedColor,n=0;n<this._labels.length;n++)this._labels[n].stripLine||(s?(t=this.getPixelCoordinatesOnAxis(this._labels[n].position),e=n+1>=this._labels.length-1?this.getPixelCoordinatesOnAxis(this.viewportMaximum):this.getPixelCoordinatesOnAxis(this._labels[n+1].position),i.fillRect(a.x1,e.y,Math.abs(a.x1-a.x2),Math.abs(e.y-t.y)),s=!1):s=!0)}else for(i.fillStyle=this.interlacedColor,n=0;n<this._labels.length;n++)this._labels[n].stripLine||(s?(e=this.getPixelCoordinatesOnAxis(this._labels[n].position),t=n+1>=this._labels.length-1?this.getPixelCoordinatesOnAxis(this.viewportMaximum):this.getPixelCoordinatesOnAxis(this._labels[n+1].position),i.fillRect(e.x,a.y1,Math.abs(t.x-e.x),Math.abs(a.y1-a.y2)),s=!1):s=!0);i.beginPath()},n.prototype.renderStripLinesOfThicknessType=function(e){if(this.stripLines&&this.stripLines.length>0&&e){var t=0;for(t=0;t<this.stripLines.length;t++){var i=this.stripLines[t];i._thicknessType===e&&("pixel"===e&&(i.value<this.viewportMinimum||i.value>this.viewportMaximum)||(i.showOnTop?this.chart.addEventListener("dataAnimationIterationEnd",i.render,i):i.render()))}}},n.prototype.renderGrid=function(){if(this.gridThickness&&this.gridThickness>0){var e,t=this.chart.ctx,i=this.chart.plotArea;if(t.lineWidth=this.gridThickness,t.strokeStyle=this.gridColor,t.setLineDash&&t.setLineDash((0,h.getLineDashArray)(this.gridDashType,this.gridThickness)),"bottom"===this._position||"top"===this._position){for(n=0;n<this._labels.length&&!this._labels[n].stripLine;n++)if(!(this._labels[n].position<this.viewportMinimum||this._labels[n].position>this.viewportMaximum)){t.beginPath(),e=this.getPixelCoordinatesOnAxis(this._labels[n].position);var a=t.lineWidth%2==1?.5+(e.x<<0):e.x<<0;t.moveTo(a,i.y1<<0),t.lineTo(a,i.y2<<0),t.stroke()}}else if("left"===this._position||"right"===this._position)for(var n=0;n<this._labels.length&&!this._labels[n].stripLine;n++)if(!(0===n&&"axisY"===this.type&&this.chart.axisX&&this.chart.axisX.lineThickness||this._labels[n].position<this.viewportMinimum||this._labels[n].position>this.viewportMaximum)){t.beginPath(),e=this.getPixelCoordinatesOnAxis(this._labels[n].position);var s=t.lineWidth%2==1?.5+(e.y<<0):e.y<<0;t.moveTo(i.x1<<0,s),t.lineTo(i.x2<<0,s),t.stroke()}}},n.prototype.renderAxisLine=function(){var e=this.chart.ctx;if("bottom"===this._position||"top"===this._position){if(this.lineThickness){e.lineWidth=this.lineThickness,e.strokeStyle=this.lineColor?this.lineColor:"black",e.setLineDash&&e.setLineDash((0,h.getLineDashArray)(this.lineDashType,this.lineThickness));var t=this.lineThickness%2==1?.5+(this.lineCoordinates.y1<<0):this.lineCoordinates.y1<<0;e.beginPath(),e.moveTo(this.lineCoordinates.x1,t),e.lineTo(this.lineCoordinates.x2,t),e.stroke()}}else if(("left"===this._position||"right"===this._position)&&this.lineThickness){e.lineWidth=this.lineThickness,e.strokeStyle=this.lineColor,e.setLineDash&&e.setLineDash((0,h.getLineDashArray)(this.lineDashType,this.lineThickness));var i=this.lineThickness%2==1?.5+(this.lineCoordinates.x1<<0):this.lineCoordinates.x1<<0;e.beginPath(),e.moveTo(i,this.lineCoordinates.y1),e.lineTo(i,this.lineCoordinates.y2),e.stroke()}},n.prototype.getPixelCoordinatesOnAxis=function(e){var t={};this.lineCoordinates.width,this.lineCoordinates.height;if("bottom"===this._position||"top"===this._position){var i=this.conversionParameters.pixelPerUnit;t.x=this.conversionParameters.reference+i*(e-this.viewportMinimum),t.y=this.lineCoordinates.y1}if("left"===this._position||"right"===this._position){var i=-this.conversionParameters.pixelPerUnit;t.y=this.conversionParameters.reference-i*(e-this.viewportMinimum),t.x=this.lineCoordinates.x2}return t},n.prototype.convertPixelToValue=function(e){if(!e)return null;var t="left"===this._position||"right"===this._position?e.y:e.x;return this.conversionParameters.minimum+(t-this.conversionParameters.reference)/this.conversionParameters.pixelPerUnit},n.prototype.setViewPortRange=function(e,t){this.sessionVariables.newViewportMinimum=this.viewportMinimum=Math.min(e,t),this.sessionVariables.newViewportMaximum=this.viewportMaximum=Math.max(e,t)},n.prototype.getXValueAt=function(e){if(!e)return null;var t=null;return"left"===this._position?t=(this.chart.axisX.viewportMaximum-this.chart.axisX.viewportMinimum)/this.chart.axisX.lineCoordinates.height*(this.chart.axisX.lineCoordinates.y2-e.y)+this.chart.axisX.viewportMinimum:"bottom"===this._position&&(t=(this.chart.axisX.viewportMaximum-this.chart.axisX.viewportMinimum)/this.chart.axisX.lineCoordinates.width*(e.x-this.chart.axisX.lineCoordinates.x1)+this.chart.axisX.viewportMinimum),t},n.prototype.calculateValueToPixelConversionParameters=function(e){this.reversed=!1;var t={pixelPerUnit:null,minimum:null,reference:null},i=this.lineCoordinates.width,a=this.lineCoordinates.height;t.minimum=this.viewportMinimum,"bottom"!==this._position&&"top"!==this._position||(t.pixelPerUnit=(this.reversed?-1:1)*i/Math.abs(this.viewportMaximum-this.viewportMinimum),t.reference=this.reversed?this.lineCoordinates.x2:this.lineCoordinates.x1),"left"!==this._position&&"right"!==this._position||(t.pixelPerUnit=(this.reversed?1:-1)*a/Math.abs(this.viewportMaximum-this.viewportMinimum),t.reference=this.reversed?this.lineCoordinates.y1:this.lineCoordinates.y2),this.conversionParameters=t},n.prototype.calculateAxisParameters=function(){var e=this.chart.layoutManager.getFreeSpace(),t=!1;"bottom"===this._position||"top"===this._position?(this.maxWidth=e.width,this.maxHeight=e.height):(this.maxWidth=e.height,this.maxHeight=e.width);var i,a,s,r,o="axisX"===this.type?this.maxWidth<500?8:Math.max(6,Math.floor(this.maxWidth/62)):Math.max(Math.floor(this.maxWidth/40),2),l=0;if((null===this.viewportMinimum||isNaN(this.viewportMinimum))&&(this.viewportMinimum=this.minimum),(null===this.viewportMaximum||isNaN(this.viewportMaximum))&&(this.viewportMaximum=this.maximum),"axisX"===this.type?(i=null!==this.viewportMinimum?this.viewportMinimum:this.dataInfo.viewPortMin,a=null!==this.viewportMaximum?this.viewportMaximum:this.dataInfo.viewPortMax,a-i==0&&(l=void 0===this._options.interval?.4:this._options.interval,a+=l,i-=l),this.dataInfo.minDiff!==1/0?s=this.dataInfo.minDiff:a-i>1?s=.5*Math.abs(a-i):(s=1,"dateTime"===this.chart.plotInfo.axisXValueType&&(t=!0))):"axisY"===this.type&&(i=null!==this.viewportMinimum?this.viewportMinimum:this.dataInfo.viewPortMin,a=null!==this.viewportMaximum?this.viewportMaximum:this.dataInfo.viewPortMax,isFinite(i)||isFinite(a)?isFinite(i)?isFinite(a)||(a=i):i=a:(a=void 0===this._options.interval?-1/0:this._options.interval,i=0),0===i&&0===a?(a+=9,i=0):a-i==0?(l=Math.min(Math.abs(.01*Math.abs(a)),5),a+=l,i-=l):i>a?(l=Math.min(Math.abs(.01*Math.abs(a-i)),5),a>=0?i=a-l:a=i+l):(l=Math.min(Math.abs(.01*Math.abs(a-i)),.05),0!==a&&(a+=l),0!==i&&(i-=l)),s=this.dataInfo.minDiff!==1/0?this.dataInfo.minDiff:a-i>1?.5*Math.abs(a-i):1,this.includeZero&&(null===this.viewportMinimum||isNaN(this.viewportMinimum))&&i>0&&(i=0),this.includeZero&&(null===this.viewportMaximum||isNaN(this.viewportMaximum))&&a<0&&(a=0)),r=(isNaN(this.viewportMaximum)||null===this.viewportMaximum?a:this.viewportMaximum)-(isNaN(this.viewportMinimum)||null===this.viewportMinimum?i:this.viewportMinimum),"axisX"===this.type&&"dateTime"===this.chart.plotInfo.axisXValueType?(this.intervalType||(r/1<=o?(this.interval=1,this.intervalType="millisecond"):r/2<=o?(this.interval=2,this.intervalType="millisecond"):r/5<=o?(this.interval=5,this.intervalType="millisecond"):r/10<=o?(this.interval=10,this.intervalType="millisecond"):r/20<=o?(this.interval=20,this.intervalType="millisecond"):r/50<=o?(this.interval=50,this.intervalType="millisecond"):r/100<=o?(this.interval=100,this.intervalType="millisecond"):r/200<=o?(this.interval=200,this.intervalType="millisecond"):r/250<=o?(this.interval=250,this.intervalType="millisecond"):r/300<=o?(this.interval=300,this.intervalType="millisecond"):r/400<=o?(this.interval=400,this.intervalType="millisecond"):r/500<=o?(this.interval=500,this.intervalType="millisecond"):r/(1*constants.secondDuration)<=o?(this.interval=1,this.intervalType="second"):r/(2*constants.secondDuration)<=o?(this.interval=2,this.intervalType="second"):r/(5*constants.secondDuration)<=o?(this.interval=5,this.intervalType="second"):r/(10*constants.secondDuration)<=o?(this.interval=10,this.intervalType="second"):r/(15*constants.secondDuration)<=o?(this.interval=15,this.intervalType="second"):r/(20*constants.secondDuration)<=o?(this.interval=20,this.intervalType="second"):r/(30*constants.secondDuration)<=o?(this.interval=30,this.intervalType="second"):r/(1*constants.minuteDuration)<=o?(this.interval=1,this.intervalType="minute"):r/(2*constants.minuteDuration)<=o?(this.interval=2,this.intervalType="minute"):r/(5*constants.minuteDuration)<=o?(this.interval=5,this.intervalType="minute"):r/(10*constants.minuteDuration)<=o?(this.interval=10,this.intervalType="minute"):r/(15*constants.minuteDuration)<=o?(this.interval=15,this.intervalType="minute"):r/(20*constants.minuteDuration)<=o?(this.interval=20,this.intervalType="minute"):r/(30*constants.minuteDuration)<=o?(this.interval=30,this.intervalType="minute"):r/(1*constants.hourDuration)<=o?(this.interval=1,this.intervalType="hour"):r/(2*constants.hourDuration)<=o?(this.interval=2,this.intervalType="hour"):r/(3*constants.hourDuration)<=o?(this.interval=3,this.intervalType="hour"):r/(6*constants.hourDuration)<=o?(this.interval=6,this.intervalType="hour"):r/(1*constants.dayDuration)<=o?(this.interval=1,this.intervalType="day"):r/(2*constants.dayDuration)<=o?(this.interval=2,this.intervalType="day"):r/(4*constants.dayDuration)<=o?(this.interval=4,this.intervalType="day"):r/(1*constants.weekDuration)<=o?(this.interval=1,this.intervalType="week"):r/(2*constants.weekDuration)<=o?(this.interval=2,this.intervalType="week"):r/(3*constants.weekDuration)<=o?(this.interval=3,this.intervalType="week"):r/(1*constants.monthDuration)<=o?(this.interval=1,this.intervalType="month"):r/(2*constants.monthDuration)<=o?(this.interval=2,this.intervalType="month"):r/(3*constants.monthDuration)<=o?(this.interval=3,this.intervalType="month"):r/(6*constants.monthDuration)<=o?(this.interval=6,this.intervalType="month"):r/(1*constants.yearDuration)<=o?(this.interval=1,this.intervalType="year"):r/(2*constants.yearDuration)<=o?(this.interval=2,this.intervalType="year"):r/(4*constants.yearDuration)<=o?(this.interval=4,this.intervalType="year"):(this.interval=Math.floor(n.getNiceNumber(r/(o-1),!0)/constants.yearDuration),this.intervalType="year")),(null===this.viewportMinimum||isNaN(this.viewportMinimum))&&(this.viewportMinimum=i-s/2),(null===this.viewportMaximum||isNaN(this.viewportMaximum))&&(this.viewportMaximum=a+s/2),this.valueFormatString||(t?this.valueFormatString="MMM DD YYYY HH:mm":"year"===this.intervalType?this.valueFormatString="YYYY":"month"===this.intervalType?this.valueFormatString="MMM YYYY":"week"===this.intervalType?this.valueFormatString="MMM DD YYYY":"day"===this.intervalType?this.valueFormatString="MMM DD YYYY":"hour"===this.intervalType?this.valueFormatString="hh:mm TT":"minute"===this.intervalType?this.valueFormatString="hh:mm TT":"second"===this.intervalType?this.valueFormatString="hh:mm:ss TT":"millisecond"===this.intervalType&&(this.valueFormatString="fff'ms'"))):(this.intervalType="number",r=n.getNiceNumber(r,!1),this._options&&this._options.interval?this.interval=this._options.interval:this.interval=n.getNiceNumber(r/(o-1),!0),(null===this.viewportMinimum||isNaN(this.viewportMinimum))&&("axisX"===this.type?this.viewportMinimum=i-s/2:this.viewportMinimum=Math.floor(i/this.interval)*this.interval),(null===this.viewportMaximum||isNaN(this.viewportMaximum))&&("axisX"===this.type?this.viewportMaximum=a+s/2:this.viewportMaximum=Math.ceil(a/this.interval)*this.interval),0===this.viewportMaximum&&0===this.viewportMinimum&&(0===this._options.viewportMinimum?this.viewportMaximum+=10:0===this._options.viewportMaximum&&(this.viewportMinimum-=10),this._options&&void 0===this._options.interval&&(this.interval=n.getNiceNumber((this.viewportMaximum-this.viewportMinimum)/(o-1),!0)))),null!==this.minimum&&null!==this.maximum||("axisX"===this.type?(i=null!==this.minimum?this.minimum:this.dataInfo.min,a=null!==this.maximum?this.maximum:this.dataInfo.max,a-i==0&&(l=void 0===this._options.interval?.4:this._options.interval,a+=l,i-=l),s=this.dataInfo.minDiff!==1/0?this.dataInfo.minDiff:a-i>1?.5*Math.abs(a-i):1):"axisY"===this.type&&(i=null!==this.minimum?this.minimum:this.dataInfo.min,a=null!==this.maximum?this.maximum:this.dataInfo.max,isFinite(i)||isFinite(a)?0===i&&0===a?(a+=9,i=0):a-i==0?(l=Math.min(Math.abs(.01*Math.abs(a)),5),a+=l,i-=l):i>a?(l=Math.min(Math.abs(.01*Math.abs(a-i)),5),a>=0?i=a-l:a=i+l):(l=Math.min(Math.abs(.01*Math.abs(a-i)),.05),0!==a&&(a+=l),0!==i&&(i-=l)):(a=void 0===this._options.interval?-1/0:this._options.interval,i=0),s=this.dataInfo.minDiff!==1/0?this.dataInfo.minDiff:a-i>1?.5*Math.abs(a-i):1,this.includeZero&&(null===this.minimum||isNaN(this.minimum))&&i>0&&(i=0),this.includeZero&&(null===this.maximum||isNaN(this.maximum))&&a<0&&(a=0)),r=a-i,"axisX"===this.type&&"dateTime"===this.chart.plotInfo.axisXValueType?((null===this.minimum||isNaN(this.minimum))&&(this.minimum=i-s/2),(null===this.maximum||isNaN(this.maximum))&&(this.maximum=a+s/2)):(this.intervalType="number",null===this.minimum&&("axisX"===this.type?this.minimum=i-s/2:this.minimum=Math.floor(i/this.interval)*this.interval,this.minimum=Math.min(this.minimum,null===this.sessionVariables.viewportMinimum||isNaN(this.sessionVariables.viewportMinimum)?1/0:this.sessionVariables.viewportMinimum)),null===this.maximum&&("axisX"===this.type?this.maximum=a+s/2:this.maximum=Math.ceil(a/this.interval)*this.interval,this.maximum=Math.max(this.maximum,null===this.sessionVariables.viewportMaximum||isNaN(this.sessionVariables.viewportMaximum)?-1/0:this.sessionVariables.viewportMaximum)),0===this.maximum&&0===this.minimum&&(0===this._options.minimum?this.maximum+=10:0===this._options.maximum&&(this.minimum-=10)))),this.viewportMinimum=Math.max(this.viewportMinimum,this.minimum),this.viewportMaximum=Math.min(this.viewportMaximum,this.maximum),"axisX"===this.type&&"dateTime"===this.chart.plotInfo.axisXValueType?this.intervalStartPosition=this.getLabelStartPoint(new Date(this.viewportMinimum),this.intervalType,this.interval):this.intervalStartPosition=Math.floor((this.viewportMinimum+.2*this.interval)/this.interval)*this.interval,!this.valueFormatString&&(this.valueFormatString="#,##0.##",(r=Math.abs(this.viewportMaximum-this.viewportMinimum))<1)){var h=Math.floor(Math.abs(Math.log(r)/Math.LN10))+2;if(!isNaN(h)&&isFinite(h)||(h=2),h>2)for(var d=0;d<h-2;d++)this.valueFormatString+="#"}},n.getNiceNumber=function(e,t){var i,a=Math.floor(Math.log(e)/Math.LN10),n=e/Math.pow(10,a);return i=t?n<1.5?1:n<3?2:n<7?5:10:n<=1?1:n<=2?2:n<=5?5:10,Number((i*Math.pow(10,a)).toFixed(20))},n.prototype.getLabelStartPoint=function(){var e=convertToNumber(this.interval,this.intervalType),t=Math.floor(this.viewportMinimum/e)*e,i=new Date(t);return"millisecond"===this.intervalType||("second"===this.intervalType?i.getMilliseconds()>0&&(i.setSeconds(i.getSeconds()+1),i.setMilliseconds(0)):"minute"===this.intervalType?(i.getSeconds()>0||i.getMilliseconds()>0)&&(i.setMinutes(i.getMinutes()+1),i.setSeconds(0),i.setMilliseconds(0)):"hour"===this.intervalType?(i.getMinutes()>0||i.getSeconds()>0||i.getMilliseconds()>0)&&(i.setHours(i.getHours()+1),i.setMinutes(0),i.setSeconds(0),i.setMilliseconds(0)):"day"===this.intervalType?(i.getHours()>0||i.getMinutes()>0||i.getSeconds()>0||i.getMilliseconds()>0)&&(i.setDate(i.getDate()+1),i.setHours(0),i.setMinutes(0),i.setSeconds(0),i.setMilliseconds(0)):"week"===this.intervalType?(i.getDay()>0||i.getHours()>0||i.getMinutes()>0||i.getSeconds()>0||i.getMilliseconds()>0)&&(i.setDate(i.getDate()+(7-i.getDay())),i.setHours(0),i.setMinutes(0),i.setSeconds(0),i.setMilliseconds(0)):"month"===this.intervalType?(i.getDate()>1||i.getHours()>0||i.getMinutes()>0||i.getSeconds()>0||i.getMilliseconds()>0)&&(i.setMonth(i.getMonth()+1),i.setDate(1),i.setHours(0),i.setMinutes(0),i.setSeconds(0),i.setMilliseconds(0)):"year"===this.intervalType&&(i.getMonth()>0||i.getDate()>1||i.getHours()>0||i.getMinutes()>0||i.getSeconds()>0||i.getMilliseconds()>0)&&(i.setFullYear(i.getFullYear()+1),i.setMonth(0),i.setDate(1),i.setHours(0),i.setMinutes(0),i.setSeconds(0),i.setMilliseconds(0))),i},i.default=n},{"../helpers/utils":39,"./canvasjs":27,"./text_block":34}],27:[function(e,t,i){"use strict";function a(e,t,i,a){this._defaultsKey=e,this.parent=a,this._eventListeners=[];var s={};i&&n.themes[i]&&n.themes[i][e]&&(s=n.themes[i][e]),this._options=t||{},this.setOptions(this._options,s)}Object.defineProperty(i,"__esModule",{value:!0});var n=e("../constants/themes"),s=e("../constants/options");a.prototype.setOptions=function(e,t){if(s.defaultOptions[this._defaultsKey]){var i=s.defaultOptions[this._defaultsKey];for(var a in i)i.hasOwnProperty(a)&&(this[a]=e&&a in e?e[a]:t&&a in t?t[a]:i[a])}else s.isDebugMode&&window.console&&console.log("defaults not set")},a.prototype.updateOption=function(e){!s.defaultOptions[this._defaultsKey]&&s.isDebugMode&&window.console&&console.log("defaults not set");var t=s.defaultOptions[this._defaultsKey],i=this._options.theme?this._options.theme:this.chart&&this.chart._options.theme?this.chart._options.theme:"theme1",a={},r=this[e];return i&&n.themes[i]&&n.themes[i][this._defaultsKey]&&(a=n.themes[i][this._defaultsKey]),e in t&&(r=e in this._options?this._options[e]:a&&e in a?a[e]:t[e]),r!==this[e]&&(this[e]=r,!0)},a.prototype.trackChanges=function(e){if(!this.sessionVariables)throw"Session Variable Store not set";this.sessionVariables[e]=this._options[e]},a.prototype.isBeingTracked=function(e){return this._options._oldOptions||(this._options._oldOptions={}),!!this._options._oldOptions[e]},a.prototype.hasOptionChanged=function(e){if(!this.sessionVariables)throw"Session Variable Store not set";return!(this.sessionVariables[e]===this._options[e])},a.prototype.addEventListener=function(e,t,i){e&&t&&(i=i||this,this._eventListeners[e]=this._eventListeners[e]||[],this._eventListeners[e].push({context:i,eventHandler:t}))},a.prototype.removeEventListener=function(e,t){if(e&&t&&this._eventListeners[e])for(var i=this._eventListeners[e],a=0;a<i.length;a++)if(i[a].eventHandler===t){i[a].splice(a,1);break}},a.prototype.removeAllEventListeners=function(){this._eventListeners=[]},a.prototype.dispatchEvent=function(e,t,i){if(e&&this._eventListeners[e]){t=t||{};for(var a=this._eventListeners[e],n=0;n<a.length;n++)a[n].eventHandler.call(a[n].context,t)}"function"==typeof this[e]&&this[e].call(i||this.chart._publicChartReference,t)},i.default=a},{"../constants/options":23,"../constants/themes":24}],28:[function(e,t,i){"use strict";function a(e){return e&&e.__esModule?e:{default:e}}function n(e,t,i){return t in e?Object.defineProperty(e,t,{value:i,enumerable:!0,configurable:!0,writable:!0}):e[t]=i,e}function s(e,t,i){this._publicChartReference=i,t=t||{},s.base.constructor.call(this,"Chart",t,t.theme?t.theme:"theme1");var a=this;if(this._containerId=e,this._objectsInitialized=!1,this.ctx=null,this.overlaidCanvasCtx=null,this._indexLabels=[],this._panTimerId=0,this._lastTouchEventType="",this._lastTouchData=null,this.isAnimating=!1,this.renderCount=0,this.animatedRender=!1,this.disableToolTip=!1,this.panEnabled=!1,this._defaultCursor="default",this.plotArea={canvas:null,ctx:null,x1:0,y1:0,x2:0,y2:0,width:0,height:0},this._dataInRenderedOrder=[],this._container="string"==typeof this._containerId?document.getElementById(this._containerId):this._containerId,!this._container)return void(window.console&&window.console.log('CanvasJS Error: Chart Container with id "'+this._containerId+'" was not found'));this._container.innerHTML="";var n=0,r=0;n=this._options.width?this.width:this._container.clientWidth>0?this._container.clientWidth:this.width,r=this._options.height?this.height:this._container.clientHeight>0?this._container.clientHeight:this.height,this.width=n,this.height=r,this.x1=this.y1=0,this.x2=this.width,this.y2=this.height,this._selectedColorSet=void 0!==B.colorSets[this.colorSet]?B.colorSets[this.colorSet]:B.colorSets.colorSet1,this._canvasJSContainer=document.createElement("div"),this._canvasJSContainer.setAttribute("class","canvasjs-chart-container"),this._canvasJSContainer.style.position="relative",this._canvasJSContainer.style.textAlign="left",this._canvasJSContainer.style.cursor="auto",z.isCanvasSupported||(this._canvasJSContainer.style.height="0px"),this._container.appendChild(this._canvasJSContainer),this.canvas=(0,z.createCanvas)(n,r),this.canvas.style.position="absolute",this.canvas.getContext&&(this._canvasJSContainer.appendChild(this.canvas),this.ctx=this.canvas.getContext("2d"),this.ctx.textBaseline="top",(0,z.extendCtx)(this.ctx),z.isCanvasSupported?this.plotArea.ctx=this.ctx:(this.plotArea.canvas=(0,z.createCanvas)(n,r),this.plotArea.canvas.style.position="absolute",this.plotArea.canvas.setAttribute("class","plotAreaCanvas"),this._canvasJSContainer.appendChild(this.plotArea.canvas),this.plotArea.ctx=this.plotArea.canvas.getContext("2d")),this.overlaidCanvas=(0,z.createCanvas)(n,r),this.overlaidCanvas.style.position="absolute",this._canvasJSContainer.appendChild(this.overlaidCanvas),this.overlaidCanvasCtx=this.overlaidCanvas.getContext("2d"),this.overlaidCanvasCtx.textBaseline="top",this._eventManager=new f.default(this),(0,z.addEvent)(window,"resize",function(){a._updateSize()&&a.render()}),this._toolBar=document.createElement("div"),this._toolBar.setAttribute("class","canvasjs-chart-toolbar"),this._toolBar.style.cssText="position: absolute; right: 1px; top: 1px;",this._canvasJSContainer.appendChild(this._toolBar),this.bounds={x1:0,y1:0,x2:this.width,y2:this.height},(0,z.addEvent)(this.overlaidCanvas,"click",function(e){a._mouseEventHandler(e)}),(0,z.addEvent)(this.overlaidCanvas,"mousemove",function(e){a._mouseEventHandler(e)}),(0,z.addEvent)(this.overlaidCanvas,"mouseup",function(e){a._mouseEventHandler(e)}),(0,z.addEvent)(this.overlaidCanvas,"mousedown",function(e){a._mouseEventHandler(e),(0,z.hide)(a._dropdownMenu)}),(0,z.addEvent)(this.overlaidCanvas,"mouseout",function(e){a._mouseEventHandler(e)}),(0,z.addEvent)(this.overlaidCanvas,window.navigator.msPointerEnabled?"MSPointerDown":"touchstart",function(e){a._touchEventHandler(e)}),(0,z.addEvent)(this.overlaidCanvas,window.navigator.msPointerEnabled?"MSPointerMove":"touchmove",function(e){
a._touchEventHandler(e)}),(0,z.addEvent)(this.overlaidCanvas,window.navigator.msPointerEnabled?"MSPointerUp":"touchend",function(e){a._touchEventHandler(e)}),(0,z.addEvent)(this.overlaidCanvas,window.navigator.msPointerEnabled?"MSPointerCancel":"touchcancel",function(e){a._touchEventHandler(e)}),this._creditLink||(this._creditLink=document.createElement("a"),this._creditLink.setAttribute("class","canvasjs-chart-credit"),this._creditLink.setAttribute("style","outline:none;margin:0px;position:absolute;right:3px;top:"+(this.height-14)+"px;color:dimgrey;text-decoration:none;font-size:10px;font-family:Lucida Grande, Lucida Sans Unicode, Arial, sans-serif"),this._creditLink.setAttribute("tabIndex",-1),this._creditLink.setAttribute("target","_blank")),this._toolTip=new M.default(this,this._options.toolTip,this.theme),this.data=null,this.axisX=null,this.axisY=null,this.axisY2=null,this.sessionVariables={axisX:{},axisY:{},axisY2:{}})}Object.defineProperty(i,"__esModule",{value:!0});var r=e("./canvasjs"),o=a(r),l=e("./animator"),h=a(l),d=e("./data_series"),c=a(d),p=e("./text_block"),x=a(p),u=e("../helpers/render"),m=a(u),v=e("./layout_manager"),g=a(v),y=e("./event_manager"),f=a(y),b=e("./tooltip"),M=a(b),P=e("../core/culture_info"),S=a(P),T=e("../core/axis"),C=a(T),k=e("../core/title"),w=a(k),_=e("../core/legend"),A=a(_),L=e("../helpers/animator"),I=a(L),B=e("../constants/themes"),F=e("../constants/options"),z=e("../helpers/utils"),D=e("../charts/index"),X=(0,z.getDevicePixelBackingStoreRatio)();(0,z.extend)(s,o.default),s.prototype._updateOptions=function(){var e=this;if(this.updateOption("width"),this.updateOption("height"),this.updateOption("dataPointMaxWidth"),this.updateOption("interactivityEnabled"),this.updateOption("theme"),this.updateOption("colorSet")&&(this._selectedColorSet=void 0!==B.colorSets[this.colorSet]?B.colorSets[this.colorSet]:B.colorSets.colorSet1),this.updateOption("backgroundColor"),this.backgroundColor||(this.backgroundColor="rgba(0,0,0,0)"),this.updateOption("culture"),this._cultureInfo=new S.default(this._options.culture),this.updateOption("animationEnabled"),this.animationEnabled=this.animationEnabled&&z.isCanvasSupported,this.updateOption("animationDuration"),this.updateOption("rangeChanging"),this.updateOption("rangeChanged"),this._options.zoomEnabled?(this._zoomButton||((0,z.hide)(this._zoomButton=document.createElement("button")),setButtonState(this,this._zoomButton,"pan"),this._toolBar.appendChild(this._zoomButton),(0,z.addEvent)(this._zoomButton,"click",function(){e.zoomEnabled?(e.zoomEnabled=!1,e.panEnabled=!0,setButtonState(e,e._zoomButton,"zoom")):(e.zoomEnabled=!0,e.panEnabled=!1,setButtonState(e,e._zoomButton,"pan")),e.render()})),this._resetButton||((0,z.hide)(this._resetButton=document.createElement("button")),setButtonState(this,this._resetButton,"reset"),this._toolBar.appendChild(this._resetButton),(0,z.addEvent)(this._resetButton,"click",function(){e._toolTip.hide(),e.zoomEnabled||e.panEnabled?(e.zoomEnabled=!0,e.panEnabled=!1,setButtonState(e,e._zoomButton,"pan"),e._defaultCursor="default",e.overlaidCanvas.style.cursor=e._defaultCursor):(e.zoomEnabled=!1,e.panEnabled=!1),e.sessionVariables.axisX&&(e.sessionVariables.axisX.newViewportMinimum=null,e.sessionVariables.axisX.newViewportMaximum=null),e.sessionVariables.axisY&&(e.sessionVariables.axisY.newViewportMinimum=null,e.sessionVariables.axisY.newViewportMaximum=null),e.sessionVariables.axisY2&&(e.sessionVariables.axisY2.newViewportMinimum=null,e.sessionVariables.axisY2.newViewportMaximum=null),e.resetOverlayedCanvas(),(0,z.hide)(e._zoomButton,e._resetButton),e._dispatchRangeEvent("rangeChanging","reset"),e.render(),e._dispatchRangeEvent("rangeChanged","reset")}),this.overlaidCanvas.style.cursor=e._defaultCursor),this.zoomEnabled||this.panEnabled||(this._zoomButton?(e._zoomButton.getAttribute("state")===e._cultureInfo.zoomText?(this.panEnabled=!0,this.zoomEnabled=!1):(this.zoomEnabled=!0,this.panEnabled=!1),(0,z.show)(e._zoomButton,e._resetButton)):(this.zoomEnabled=!0,this.panEnabled=!1))):(this.zoomEnabled=!1,this.panEnabled=!1),this._menuButton?this.exportEnabled?(0,z.show)(this._menuButton):(0,z.hide)(this._menuButton):this.exportEnabled&&z.isCanvasSupported&&(this._menuButton=document.createElement("button"),setButtonState(this,this._menuButton,"menu"),this._toolBar.appendChild(this._menuButton),(0,z.addEvent)(this._menuButton,"click",function(){if("none"===e._dropdownMenu.style.display){if(e._dropDownCloseTime&&(new Date).getTime()-e._dropDownCloseTime.getTime()<=500)return;e._dropdownMenu.style.display="block",e._menuButton.blur(),e._dropdownMenu.focus()}},!0)),!this._dropdownMenu&&this.exportEnabled&&z.isCanvasSupported){this._dropdownMenu=document.createElement("div"),this._dropdownMenu.setAttribute("tabindex",-1),this._dropdownMenu.style.cssText="position: absolute; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none; cursor: pointer;right: 1px;top: 25px;min-width: 120px;outline: 0;border: 1px solid silver;font-size: 14px;font-family: Calibri, Verdana, sans-serif;padding: 5px 0px 5px 0px;text-align: left;background-color: #fff;line-height: 20px;box-shadow: 2px 2px 10px #888888;",e._dropdownMenu.style.display="none",this._toolBar.appendChild(this._dropdownMenu),(0,z.addEvent)(this._dropdownMenu,"blur",function(){(0,z.hide)(e._dropdownMenu),e._dropDownCloseTime=new Date},!0);var t=document.createElement("div");t.style.cssText="padding: 2px 15px 2px 10px",t.innerHTML=this._cultureInfo.saveJPGText,this._dropdownMenu.appendChild(t),(0,z.addEvent)(t,"mouseover",function(){this.style.backgroundColor="#EEEEEE"},!0),(0,z.addEvent)(t,"mouseout",function(){this.style.backgroundColor="transparent"},!0),(0,z.addEvent)(t,"click",function(){exportCanvas(e.canvas,"jpg",e.exportFileName),(0,z.hide)(e._dropdownMenu)},!0);var t=document.createElement("div");t.style.cssText="padding: 2px 15px 2px 10px",t.innerHTML=this._cultureInfo.savePNGText,this._dropdownMenu.appendChild(t),(0,z.addEvent)(t,"mouseover",function(){this.style.backgroundColor="#EEEEEE"},!0),(0,z.addEvent)(t,"mouseout",function(){this.style.backgroundColor="transparent"},!0),(0,z.addEvent)(t,"click",function(){exportCanvas(e.canvas,"png",e.exportFileName),(0,z.hide)(e._dropdownMenu)},!0)}if("none"!==this._toolBar.style.display&&this._zoomButton&&(this.panEnabled?setButtonState(e,e._zoomButton,"zoom"):setButtonState(e,e._zoomButton,"pan"),e._resetButton.getAttribute("state")!==e._cultureInfo.resetText&&setButtonState(e,e._resetButton,"reset")),void 0===F.defaultOptions.Chart.creditHref)this.creditHref="http://canvasjs.com/",this.creditText="CanvasJS.com";else var i=this.updateOption("creditText"),a=this.updateOption("creditHref");(0===this.renderCount||i||a)&&(this._creditLink.setAttribute("href",this.creditHref),this._creditLink.innerHTML=this.creditText),this.creditHref&&this.creditText?this._creditLink.parentElement||this._canvasJSContainer.appendChild(this._creditLink):this._creditLink.parentElement&&this._canvasJSContainer.removeChild(this._creditLink),this._options.toolTip&&this._toolTip._options!==this._options.toolTip&&(this._toolTip._options=this._options.toolTip);for(var n in this._toolTip._options)this._toolTip._options.hasOwnProperty(n)&&this._toolTip.updateOption(n)},s.prototype._updateSize=function(){var e=0,t=0;return this._options.width?e=this.width:this.width=e=this._container.clientWidth>0?this._container.clientWidth:this.width,this._options.height?t=this.height:this.height=t=this._container.clientHeight>0?this._container.clientHeight:this.height,(this.canvas.width!==e*X||this.canvas.height!==t*X)&&((0,z.setCanvasSize)(this.canvas,e,t),(0,z.setCanvasSize)(this.overlaidCanvas,e,t),(0,z.setCanvasSize)(this._eventManager.ghostCanvas,e,t),!0)},s.prototype._initialize=function(){this._animator?this._animator.cancelAllAnimations():this._animator=new h.default(this),this.removeAllEventListeners(),this.disableToolTip=!1,this._axes=[],this.pieDoughnutClickHandler=null,this.animationRequestId&&this.cancelRequestAnimFrame.call(window,this.animationRequestId),this._updateOptions(),this.animatedRender=z.isCanvasSupported&&this.animationEnabled&&0===this.renderCount,this._updateSize(),this.clearCanvas(),this.ctx.beginPath(),this.axisX=null,this.axisY=null,this.axisY2=null,this._indexLabels=[],this._dataInRenderedOrder=[],this._events=[],this._eventManager&&this._eventManager.reset(),this.plotInfo={axisPlacement:null,axisXValueType:null,plotTypes:[]},this.layoutManager=new g.default(0,0,this.width,this.height,2),this.plotArea.layoutManager&&this.plotArea.layoutManager.reset(),this.data=[];for(var e=0,t=0;t<this._options.data.length;t++)if(e++,!this._options.data[t].type||s._supportedChartTypes.indexOf(this._options.data[t].type)>=0){var i=new c.default(this,this._options.data[t],this.theme,e-1,++this._eventManager.lastObjectId);null===i.name&&(i.name="DataSeries "+e),null===i.color?this._options.data.length>1?(i._colorSet=[this._selectedColorSet[i.index%this._selectedColorSet.length]],i.color=this._selectedColorSet[i.index%this._selectedColorSet.length]):"line"===i.type||"stepLine"===i.type||"spline"===i.type||"area"===i.type||"stepArea"===i.type||"splineArea"===i.type||"stackedArea"===i.type||"stackedArea100"===i.type||"rangeArea"===i.type||"rangeSplineArea"===i.type||"candlestick"===i.type||"ohlc"===i.type?i._colorSet=[this._selectedColorSet[0]]:i._colorSet=this._selectedColorSet:i._colorSet=[i.color],null===i.markerSize&&(("line"===i.type||"stepLine"===i.type||"spline"===i.type)&&i.dataPoints&&i.dataPoints.length<this.width/16||"scatter"===i.type)&&(i.markerSize=8),"bubble"!==i.type&&"scatter"!==i.type||!i.dataPoints||i.dataPoints.sort(z.compareDataPointX),this.data.push(i);var a,n=i.axisPlacement;if("normal"===n?"xySwapped"===this.plotInfo.axisPlacement?a='You cannot combine "'+i.type+'" with bar chart':"none"===this.plotInfo.axisPlacement?a='You cannot combine "'+i.type+'" with pie chart':null===this.plotInfo.axisPlacement&&(this.plotInfo.axisPlacement="normal"):"xySwapped"===n?"normal"===this.plotInfo.axisPlacement?a='You cannot combine "'+i.type+'" with line, area, column or pie chart':"none"===this.plotInfo.axisPlacement?a='You cannot combine "'+i.type+'" with pie chart':null===this.plotInfo.axisPlacement&&(this.plotInfo.axisPlacement="xySwapped"):"none"==n&&("normal"===this.plotInfo.axisPlacement?a='You cannot combine "'+i.type+'" with line, area, column or bar chart':"xySwapped"===this.plotInfo.axisPlacement?a='You cannot combine "'+i.type+'" with bar chart':null===this.plotInfo.axisPlacement&&(this.plotInfo.axisPlacement="none")),a&&window.console)return void window.console.log(a)}this._objectsInitialized=!0},s._supportedChartTypes=(0,z.addArrayIndexOf)(["line","stepLine","spline","column","area","stepArea","splineArea","bar","bubble","scatter","stackedColumn","stackedColumn100","stackedBar","stackedBar100","stackedArea","stackedArea100","candlestick","ohlc","rangeColumn","rangeBar","rangeArea","rangeSplineArea","pie","doughnut","funnel"]),s.prototype.render=function(e){e&&(this._options=e),this._initialize();for(var t=[],i=0;i<this.data.length;i++)"normal"!==this.plotInfo.axisPlacement&&"xySwapped"!==this.plotInfo.axisPlacement||(this.data[i].axisYType&&"primary"!==this.data[i].axisYType?"secondary"===this.data[i].axisYType&&(this.axisY2||("normal"===this.plotInfo.axisPlacement?this._axes.push(this.axisY2=new C.default(this,this._options.axisY2,"axisY","right")):"xySwapped"===this.plotInfo.axisPlacement&&this._axes.push(this.axisY2=new C.default(this,this._options.axisY2,"axisY","top"))),this.data[i].axisY=this.axisY2):(this.axisY||("normal"===this.plotInfo.axisPlacement?this._axes.push(this.axisY=new C.default(this,this._options.axisY,"axisY","left")):"xySwapped"===this.plotInfo.axisPlacement&&this._axes.push(this.axisY=new C.default(this,this._options.axisY,"axisY","bottom"))),this.data[i].axisY=this.axisY),this.axisX||("normal"===this.plotInfo.axisPlacement?this._axes.push(this.axisX=new C.default(this,this._options.axisX,"axisX","bottom")):"xySwapped"===this.plotInfo.axisPlacement&&this._axes.push(this.axisX=new C.default(this,this._options.axisX,"axisX","left"))),this.data[i].axisX=this.axisX);this.axisY&&this.axisY2&&(this.axisY.gridThickness>0&&void 0===this.axisY2._options.gridThickness?this.axisY2.gridThickness=0:this.axisY2.gridThickness>0&&void 0===this.axisY._options.gridThickness&&(this.axisY.gridThickness=0));var a=!1;if(this._axes.length>0&&(this.zoomEnabled||this.panEnabled))for(var i=0;i<this._axes.length;i++)if(null!==this._axes[i].viewportMinimum||null!==this._axes[i].viewportMaximum){a=!0;break}if(a?(0,z.show)(this._zoomButton,this._resetButton):(0,z.hide)(this._zoomButton,this._resetButton),this._processData(),this._options.title&&(this._title=new w.default(this,this._options.title),this._title.dockInsidePlotArea?t.push(this._title):this._title.render()),this._options.subtitles)for(var i=0;i<this._options.subtitles.length;i++){this.subtitles=[];var n=new Subtitle(this,this._options.subtitles[i]);this.subtitles.push(n),n.dockInsidePlotArea?t.push(n):n.render()}this.legend=new A.default(this,this._options.legend,this.theme);for(var i=0;i<this.data.length;i++)(this.data[i].showInLegend||"pie"===this.data[i].type||"doughnut"===this.data[i].type)&&this.legend.dataSeries.push(this.data[i]);if(this.legend.dockInsidePlotArea?t.push(this.legend):this.legend.render(),"normal"===this.plotInfo.axisPlacement||"xySwapped"===this.plotInfo.axisPlacement)C.default.setLayoutAndRender(this.axisX,this.axisY,this.axisY2,this.plotInfo.axisPlacement,this.layoutManager.getFreeSpace());else{if("none"!==this.plotInfo.axisPlacement)return;this.preparePlotArea()}var s=0;for(s in t)t.hasOwnProperty(s)&&t[s].render();var r=[];if(this.animatedRender){var o=(0,z.createCanvas)(this.width,this.height);o.getContext("2d").drawImage(this.canvas,0,0,this.width,this.height)}for(var i=0;i<this.plotInfo.plotTypes.length;i++)for(var l=this.plotInfo.plotTypes[i],h=0;h<l.plotUnits.length;h++){var d=l.plotUnits[h],c=null;d.targetCanvas=null,this.animatedRender&&(d.targetCanvas=(0,z.createCanvas)(this.width,this.height),d.targetCanvasCtx=d.targetCanvas.getContext("2d")),"line"===d.type?c=this.renderLine(d):"stepLine"===d.type?c=this.renderStepLine(d):"spline"===d.type?c=this.renderSpline(d):"column"===d.type?c=this.renderColumn(d):"bar"===d.type?c=this.renderBar(d):"area"===d.type?c=this.renderArea(d):"stepArea"===d.type?c=this.renderStepArea(d):"splineArea"===d.type?c=this.renderSplineArea(d):"stackedColumn"===d.type?c=this.renderStackedColumn(d):"stackedColumn100"===d.type?c=this.renderStackedColumn100(d):"stackedBar"===d.type?c=this.renderStackedBar(d):"stackedBar100"===d.type?c=this.renderStackedBar100(d):"stackedArea"===d.type?c=this.renderStackedArea(d):"stackedArea100"===d.type?c=this.renderStackedArea100(d):"bubble"===d.type?c=c=this.renderBubble(d):"scatter"===d.type?c=this.renderScatter(d):"pie"===d.type?this.renderPie(d):"doughnut"===d.type?this.renderPie(d):"candlestick"===d.type?c=this.renderCandlestick(d):"ohlc"===d.type?c=this.renderCandlestick(d):"rangeColumn"===d.type?c=this.renderRangeColumn(d):"rangeBar"===d.type?c=this.renderRangeBar(d):"rangeArea"===d.type?c=this.renderRangeArea(d):"rangeSplineArea"===d.type&&(c=this.renderRangeSplineArea(d));for(var p=0;p<d.dataSeriesIndexes.length;p++)this._dataInRenderedOrder.push(this.data[d.dataSeriesIndexes[p]]);this.animatedRender&&c&&r.push(c)}if(this.animatedRender&&this._indexLabels.length>0){var x=(0,z.createCanvas)(this.width,this.height),u=x.getContext("2d");r.push(this.renderIndexLabels(u))}var m=this;if(r.length>0?(m.disableToolTip=!0,m._animator.animate(200,m.animationDuration,function(e){m.ctx.clearRect(0,0,m.width,m.height),m.ctx.drawImage(o,0,0,Math.floor(m.width*X),Math.floor(m.height*X),0,0,m.width,m.height);for(var t=0;t<r.length;t++)c=r[t],e<1&&void 0!==c.startTimePercent?e>=c.startTimePercent&&c.animationCallback(c.easingFunction(e-c.startTimePercent,0,1,1-c.startTimePercent),c):c.animationCallback(c.easingFunction(e,0,1,1),c);m.dispatchEvent("dataAnimationIterationEnd",{chart:m})},function(){r=[];for(var e=0;e<m.plotInfo.plotTypes.length;e++)for(var t=m.plotInfo.plotTypes[e],i=0;i<t.plotUnits.length;i++){var a=t.plotUnits[i];a.targetCanvas=null}o=null,m.disableToolTip=!1})):(m._indexLabels.length>0&&m.renderIndexLabels(),m.dispatchEvent("dataAnimationIterationEnd",{chart:m})),this.attachPlotAreaEventHandlers(),this.zoomEnabled||this.panEnabled||!this._zoomButton||"none"===this._zoomButton.style.display||(0,z.hide)(this._zoomButton,this._resetButton),this._toolTip._updateToolTip(),this.renderCount++,F.isDebugMode){var m=this;setTimeout(function(){var e=document.getElementById("ghostCanvasCopy");if(e){(0,z.setCanvasSize)(e,m.width,m.height);e.getContext("2d").drawImage(m._eventManager.ghostCanvas,0,0)}},2e3)}},s.prototype.attachPlotAreaEventHandlers=function(){var e;this.attachEvent((e={context:this,chart:this,mousedown:this._plotAreaMouseDown,mouseup:this._plotAreaMouseUp,mousemove:this._plotAreaMouseMove,cursor:this.zoomEnabled?"col-resize":"move"},n(e,"cursor",this.panEnabled?"move":"default"),n(e,"capture",!0),n(e,"bounds",this.plotArea),e))},s.prototype.categoriseDataSeries=function(){for(var e="",t=0;t<this.data.length;t++)if(e=this.data[t],e.dataPoints&&0!==e.dataPoints.length&&e.visible&&s._supportedChartTypes.indexOf(e.type)>=0){for(var i=null,a=!1,n=null,r=!1,o=0;o<this.plotInfo.plotTypes.length;o++)if(this.plotInfo.plotTypes[o].type===e.type){a=!0;var i=this.plotInfo.plotTypes[o];break}a||(i={type:e.type,totalDataSeries:0,plotUnits:[]},this.plotInfo.plotTypes.push(i));for(var o=0;o<i.plotUnits.length;o++)if(i.plotUnits[o].axisYType===e.axisYType){r=!0;var n=i.plotUnits[o];break}r||(n={type:e.type,previousDataSeriesCount:0,index:i.plotUnits.length,plotType:i,axisYType:e.axisYType,axisY:"primary"===e.axisYType?this.axisY:this.axisY2,axisX:this.axisX,dataSeriesIndexes:[],yTotals:[]},i.plotUnits.push(n)),i.totalDataSeries++,n.dataSeriesIndexes.push(t),e.plotUnit=n}for(var t=0;t<this.plotInfo.plotTypes.length;t++)for(var i=this.plotInfo.plotTypes[t],l=0,o=0;o<i.plotUnits.length;o++)i.plotUnits[o].previousDataSeriesCount=l,l+=i.plotUnits[o].dataSeriesIndexes.length},s.prototype.assignIdToDataPoints=function(){for(var e=0;e<this.data.length;e++){var t=this.data[e];if(t.dataPoints)for(var i=t.dataPoints.length,a=0;a<i;a++)t.dataPointIds[a]=++this._eventManager.lastObjectId}},s.prototype._processData=function(){this.assignIdToDataPoints(),this.categoriseDataSeries();for(var e=0;e<this.plotInfo.plotTypes.length;e++)for(var t=this.plotInfo.plotTypes[e],i=0;i<t.plotUnits.length;i++){var a=t.plotUnits[i];"line"===a.type||"stepLine"===a.type||"spline"===a.type||"column"===a.type||"area"===a.type||"stepArea"===a.type||"splineArea"===a.type||"bar"===a.type||"bubble"===a.type||"scatter"===a.type?this._processMultiseriesPlotUnit(a):"stackedColumn"===a.type||"stackedBar"===a.type||"stackedArea"===a.type?this._processStackedPlotUnit(a):"stackedColumn100"===a.type||"stackedBar100"===a.type||"stackedArea100"===a.type?this._processStacked100PlotUnit(a):"candlestick"!==a.type&&"ohlc"!==a.type&&"rangeColumn"!==a.type&&"rangeBar"!==a.type&&"rangeArea"!==a.type&&"rangeSplineArea"!==a.type||this._processMultiYPlotUnit(a)}},s.prototype._processMultiseriesPlotUnit=function(e){if(e.dataSeriesIndexes&&!(e.dataSeriesIndexes.length<1))for(var t,i,a=e.axisY.dataInfo,n=e.axisX.dataInfo,s=!1,r=0;r<e.dataSeriesIndexes.length;r++){var o=this.data[e.dataSeriesIndexes[r]],l=0,h=!1,d=!1;if("normal"===o.axisPlacement||"xySwapped"===o.axisPlacement)var c=this.sessionVariables.axisX.newViewportMinimum?this.sessionVariables.axisX.newViewportMinimum:this._options.axisX&&this._options.axisX.viewportMinimum?this._options.axisX.viewportMinimum:this._options.axisX&&this._options.axisX.minimum?this._options.axisX.minimum:-1/0,p=this.sessionVariables.axisX.newViewportMaximum?this.sessionVariables.axisX.newViewportMaximum:this._options.axisX&&this._options.axisX.viewportMaximum?this._options.axisX.viewportMaximum:this._options.axisX&&this._options.axisX.maximum?this._options.axisX.maximum:1/0;for((o.dataPoints[l].x&&o.dataPoints[l].x.getTime||"dateTime"===o.xValueType)&&(s=!0),l=0;l<o.dataPoints.length;l++){if(void 0===o.dataPoints[l].x&&(o.dataPoints[l].x=l),o.dataPoints[l].x.getTime?(s=!0,t=o.dataPoints[l].x.getTime()):t=o.dataPoints[l].x,i=o.dataPoints[l].y,t<n.min&&(n.min=t),t>n.max&&(n.max=t),i<a.min&&(a.min=i),i>a.max&&(a.max=i),l>0){var x=t-o.dataPoints[l-1].x;if(x<0&&(x*=-1),n.minDiff>x&&0!==x&&(n.minDiff=x),null!==i&&null!==o.dataPoints[l-1].y){var u=i-o.dataPoints[l-1].y;u<0&&(u*=-1),a.minDiff>u&&0!==u&&(a.minDiff=u)}}if(!(t<c)||h)if(!h&&(h=!0,l>0))l-=2;else{if(t>p&&!d)d=!0;else if(t>p&&d)continue;o.dataPoints[l].label&&(e.axisX.labels[t]=o.dataPoints[l].label),t<n.viewPortMin&&(n.viewPortMin=t),t>n.viewPortMax&&(n.viewPortMax=t),null!==i&&(i<a.viewPortMin&&(a.viewPortMin=i),i>a.viewPortMax&&(a.viewPortMax=i))}}this.plotInfo.axisXValueType=o.xValueType=s?"dateTime":"number"}},s.prototype._processStackedPlotUnit=function(e){if(e.dataSeriesIndexes&&!(e.dataSeriesIndexes.length<1)){for(var t,i,a=e.axisY.dataInfo,n=e.axisX.dataInfo,s=!1,r=[],o=[],l=0;l<e.dataSeriesIndexes.length;l++){var h=this.data[e.dataSeriesIndexes[l]],d=0,c=!1,p=!1;if("normal"===h.axisPlacement||"xySwapped"===h.axisPlacement)var x=this.sessionVariables.axisX.newViewportMinimum?this.sessionVariables.axisX.newViewportMinimum:this._options.axisX&&this._options.axisX.viewportMinimum?this._options.axisX.viewportMinimum:this._options.axisX&&this._options.axisX.minimum?this._options.axisX.minimum:-1/0,u=this.sessionVariables.axisX.newViewportMaximum?this.sessionVariables.axisX.newViewportMaximum:this._options.axisX&&this._options.axisX.viewportMaximum?this._options.axisX.viewportMaximum:this._options.axisX&&this._options.axisX.maximum?this._options.axisX.maximum:1/0;for((h.dataPoints[d].x&&h.dataPoints[d].x.getTime||"dateTime"===h.xValueType)&&(s=!0),d=0;d<h.dataPoints.length;d++){if(void 0===h.dataPoints[d].x&&(h.dataPoints[d].x=d),h.dataPoints[d].x.getTime?(s=!0,t=h.dataPoints[d].x.getTime()):t=h.dataPoints[d].x,i=h.dataPoints[d].y,t<n.min&&(n.min=t),t>n.max&&(n.max=t),d>0){var m=t-h.dataPoints[d-1].x;if(m<0&&(m*=-1),n.minDiff>m&&0!==m&&(n.minDiff=m),null!==i&&null!==h.dataPoints[d-1].y){var v=i-h.dataPoints[d-1].y;v<0&&(v*=-1),a.minDiff>v&&0!==v&&(a.minDiff=v)}}if(!(t<x)||c)if(!c&&(c=!0,d>0))d-=2;else{if(t>u&&!p)p=!0;else if(t>u&&p)continue;h.dataPoints[d].label&&(e.axisX.labels[t]=h.dataPoints[d].label),t<n.viewPortMin&&(n.viewPortMin=t),t>n.viewPortMax&&(n.viewPortMax=t),null!==i&&(e.yTotals[t]=(e.yTotals[t]?e.yTotals[t]:0)+Math.abs(i),i>=0?r[t]?r[t]+=i:r[t]=i:o[t]?o[t]+=i:o[t]=i)}}this.plotInfo.axisXValueType=h.xValueType=s?"dateTime":"number"}for(d in r)if(r.hasOwnProperty(d)){if(isNaN(d))continue;var g=r[d];if(g<a.min&&(a.min=g),g>a.max&&(a.max=g),d<n.viewPortMin||d>n.viewPortMax)continue;g<a.viewPortMin&&(a.viewPortMin=g),g>a.viewPortMax&&(a.viewPortMax=g)}for(d in o)if(o.hasOwnProperty(d)){if(isNaN(d))continue;var g=o[d];if(g<a.min&&(a.min=g),g>a.max&&(a.max=g),d<n.viewPortMin||d>n.viewPortMax)continue;g<a.viewPortMin&&(a.viewPortMin=g),g>a.viewPortMax&&(a.viewPortMax=g)}}},s.prototype._processStacked100PlotUnit=function(e){if(e.dataSeriesIndexes&&!(e.dataSeriesIndexes.length<1)){for(var t,i,a=e.axisY.dataInfo,n=e.axisX.dataInfo,s=!1,r=!1,o=!1,l=[],h=0;h<e.dataSeriesIndexes.length;h++){var d=this.data[e.dataSeriesIndexes[h]],c=0,p=!1,x=!1;if("normal"===d.axisPlacement||"xySwapped"===d.axisPlacement)var u=this.sessionVariables.axisX.newViewportMinimum?this.sessionVariables.axisX.newViewportMinimum:this._options.axisX&&this._options.axisX.viewportMinimum?this._options.axisX.viewportMinimum:this._options.axisX&&this._options.axisX.minimum?this._options.axisX.minimum:-1/0,m=this.sessionVariables.axisX.newViewportMaximum?this.sessionVariables.axisX.newViewportMaximum:this._options.axisX&&this._options.axisX.viewportMaximum?this._options.axisX.viewportMaximum:this._options.axisX&&this._options.axisX.maximum?this._options.axisX.maximum:1/0;for((d.dataPoints[c].x&&d.dataPoints[c].x.getTime||"dateTime"===d.xValueType)&&(s=!0),c=0;c<d.dataPoints.length;c++){if(void 0===d.dataPoints[c].x&&(d.dataPoints[c].x=c),d.dataPoints[c].x.getTime?(s=!0,t=d.dataPoints[c].x.getTime()):t=d.dataPoints[c].x,i=d.dataPoints[c].y,t<n.min&&(n.min=t),t>n.max&&(n.max=t),c>0){var v=t-d.dataPoints[c-1].x;if(v<0&&(v*=-1),n.minDiff>v&&0!==v&&(n.minDiff=v),null!==i&&null!==d.dataPoints[c-1].y){var g=i-d.dataPoints[c-1].y;g<0&&(g*=-1),a.minDiff>g&&0!==g&&(a.minDiff=g)}}if(!(t<u)||p)if(!p&&(p=!0,c>0))c-=2;else{if(t>m&&!x)x=!0;else if(t>m&&x)continue;d.dataPoints[c].label&&(e.axisX.labels[t]=d.dataPoints[c].label),t<n.viewPortMin&&(n.viewPortMin=t),t>n.viewPortMax&&(n.viewPortMax=t),null!==i&&(e.yTotals[t]=(e.yTotals[t]?e.yTotals[t]:0)+Math.abs(i),i>=0?r=!0:o=!0,l[t]?l[t]+=Math.abs(i):l[t]=Math.abs(i))}}this.plotInfo.axisXValueType=d.xValueType=s?"dateTime":"number"}r&&!o?(a.max=99,a.min=1):r&&o?(a.max=99,a.min=-99):!r&&o&&(a.max=-1,a.min=-99),a.viewPortMin=a.min,a.viewPortMax=a.max,e.dataPointYSums=l}},s.prototype._processMultiYPlotUnit=function(e){if(e.dataSeriesIndexes&&!(e.dataSeriesIndexes.length<1))for(var t,i,a,n,s=e.axisY.dataInfo,r=e.axisX.dataInfo,o=!1,l=0;l<e.dataSeriesIndexes.length;l++){var h=this.data[e.dataSeriesIndexes[l]],d=0,c=!1,p=!1;if("normal"===h.axisPlacement||"xySwapped"===h.axisPlacement)var x=this.sessionVariables.axisX.newViewportMinimum?this.sessionVariables.axisX.newViewportMinimum:this._options.axisX&&this._options.axisX.viewportMinimum?this._options.axisX.viewportMinimum:this._options.axisX&&this._options.axisX.minimum?this._options.axisX.minimum:-1/0,u=this.sessionVariables.axisX.newViewportMaximum?this.sessionVariables.axisX.newViewportMaximum:this._options.axisX&&this._options.axisX.viewportMaximum?this._options.axisX.viewportMaximum:this._options.axisX&&this._options.axisX.maximum?this._options.axisX.maximum:1/0;for((h.dataPoints[d].x&&h.dataPoints[d].x.getTime||"dateTime"===h.xValueType)&&(o=!0),d=0;d<h.dataPoints.length;d++){if(void 0===h.dataPoints[d].x&&(h.dataPoints[d].x=d),h.dataPoints[d].x.getTime?(o=!0,t=h.dataPoints[d].x.getTime()):t=h.dataPoints[d].x,i=h.dataPoints[d].y,i&&i.length&&(a=Math.min.apply(null,i),n=Math.max.apply(null,i)),t<r.min&&(r.min=t),t>r.max&&(r.max=t),a<s.min&&(s.min=a),n>s.max&&(s.max=n),d>0){var m=t-h.dataPoints[d-1].x;if(m<0&&(m*=-1),r.minDiff>m&&0!==m&&(r.minDiff=m),null!==i[0]&&null!==h.dataPoints[d-1].y[0]){var v=i[0]-h.dataPoints[d-1].y[0];v<0&&(v*=-1),s.minDiff>v&&0!==v&&(s.minDiff=v)}}if(!(t<x)||c)if(!c&&(c=!0,d>0))d-=2;else{if(t>u&&!p)p=!0;else if(t>u&&p)continue;h.dataPoints[d].label&&(e.axisX.labels[t]=h.dataPoints[d].label),t<r.viewPortMin&&(r.viewPortMin=t),t>r.viewPortMax&&(r.viewPortMax=t),null!==i&&(a<s.viewPortMin&&(s.viewPortMin=a),n>s.viewPortMax&&(s.viewPortMax=n))}}this.plotInfo.axisXValueType=h.xValueType=o?"dateTime":"number"}},s.prototype.getDataPointAtXY=function(e,t,i){i=i||!1;for(var a=[],n=this._dataInRenderedOrder.length-1;n>=0;n--){var s=this._dataInRenderedOrder[n],r=null;r=s.getDataPointAtXY(e,t,i),r&&a.push(r)}for(var o=null,l=!1,h=0;h<a.length;h++)if("line"===a[h].dataSeries.type||"stepLine"===a[h].dataSeries.type||"area"===a[h].dataSeries.type||"stepArea"===a[h].dataSeries.type){var d=(0,z.getProperty)("markerSize",a[h].dataPoint,a[h].dataSeries)||8;if(a[h].distance<=d/2){l=!0;break}}for(h=0;h<a.length;h++)l&&"line"!==a[h].dataSeries.type&&"stepLine"!==a[h].dataSeries.type&&"area"!==a[h].dataSeries.type&&"stepArea"!==a[h].dataSeries.type||(o?a[h].distance<=o.distance&&(o=a[h]):o=a[h]);return o},s.prototype.getObjectAtXY=function(e,t,i){i=i||!1;var a=null,n=this.getDataPointAtXY(e,t,i);if(n)a=n.dataSeries.dataPointIds[n.dataPointIndex];else if(z.isCanvasSupported)a=(0,z.getObjectId)(e,t,this._eventManager.ghostCtx);else for(var s=0;s<this.legend.items.length;s++){var r=this.legend.items[s];e>=r.x1&&e<=r.x2&&t>=r.y1&&t<=r.y2&&(a=r.id)}return a},s.prototype.getAutoFontSize=function(e,t,i){t=t||this.width,i=i||this.height;var a=e/400;return Math.round(Math.min(this.width,this.height)*a)},s.prototype.resetOverlayedCanvas=function(){this.overlaidCanvasCtx.clearRect(0,0,this.width,this.height)},s.prototype.clearCanvas=function(){this.ctx.clearRect(0,0,this.width,this.height),this.backgroundColor&&(this.ctx.fillStyle=this.backgroundColor,this.ctx.fillRect(0,0,this.width,this.height))},s.prototype.attachEvent=function(e){this._events.push(e)},s.prototype._touchEventHandler=function(e){if(e.changedTouches&&this.interactivityEnabled){var t=[],i=e.changedTouches,a=i?i[0]:e,n=null;switch(e.type){case"touchstart":case"MSPointerDown":t=["mousemove","mousedown"],this._lastTouchData=(0,z.getMouseCoordinates)(a),this._lastTouchData.time=new Date;break;case"touchmove":case"MSPointerMove":t=["mousemove"];break;case"touchend":case"MSPointerUp":t="touchstart"===this._lastTouchEventType||"MSPointerDown"===this._lastTouchEventType?["mouseup","click"]:["mouseup"];break;default:return}if(!(i&&i.length>1)){n=(0,z.getMouseCoordinates)(a),n.time=new Date;try{var s=n.y-this._lastTouchData.y,r=(n.x,this._lastTouchData.x,n.time-this._lastTouchData.time);if(Math.abs(s)>15&&(this._lastTouchData.scroll||r<200)){this._lastTouchData.scroll=!0;var o=window.parent||window;o&&o.scrollBy&&o.scrollBy(0,-s)}}catch(e){}if(this._lastTouchEventType=e.type,this._lastTouchData.scroll&&this.zoomEnabled)return this.isDrag&&this.resetOverlayedCanvas(),void(this.isDrag=!1);for(var l=0;l<t.length;l++){var h=t[l],d=document.createEvent("MouseEvent");d.initMouseEvent(h,!0,!0,window,1,a.screenX,a.screenY,a.clientX,a.clientY,!1,!1,!1,!1,0,null),a.target.dispatchEvent(d),e.preventManipulation&&e.preventManipulation(),e.preventDefault&&e.preventDefault()}}}},s.prototype._dispatchRangeEvent=function(e,t){var i={};i.chart=this._publicChartReference,i.type=e,i.trigger=t;var a=[];this.axisX&&a.push("axisX"),this.axisY&&a.push("axisY"),this.axisY2&&a.push("axisY2");for(var n=0;n<a.length;n++)i[a[n]]={viewportMinimum:this[a[n]].sessionVariables.newViewportMinimum,viewportMaximum:this[a[n]].sessionVariables.newViewportMaximum};this.dispatchEvent(e,i,this._publicChartReference)},s.prototype._mouseEventHandler=function(e){if(this.interactivityEnabled){if(this._ignoreNextEvent)return void(this._ignoreNextEvent=!1);e.preventManipulation&&e.preventManipulation(),e.preventDefault&&e.preventDefault(),void 0===e.target&&e.srcElement&&(e.target=e.srcElement);var t,i,a=(0,z.getMouseCoordinates)(e),n=e.type;if(!e){window.event}if(e.which?i=3==e.which:e.button&&(i=2==e.button),F.isDebugMode&&window.console&&(window.console.log(n+" --\x3e x: "+a.x+"; y:"+a.y),i&&window.console.log(e.which),"mouseup"===n&&window.console.log("mouseup")),!i){if(s.capturedEventParam)t=s.capturedEventParam,"mouseup"===n&&(s.capturedEventParam=null,t.chart.overlaidCanvas.releaseCapture?t.chart.overlaidCanvas.releaseCapture():document.body.removeEventListener("mouseup",t.chart._mouseEventHandler,!1)),t.hasOwnProperty(n)&&t[n].call(t.context,a.x,a.y);else if(this._events){for(var r=0;r<this._events.length;r++)if(this._events[r].hasOwnProperty(n)){t=this._events[r];var o=t.bounds;if(a.x>=o.x1&&a.x<=o.x2&&a.y>=o.y1&&a.y<=o.y2){t[n].call(t.context,a.x,a.y),"mousedown"===n&&!0===t.capture?(s.capturedEventParam=t,this.overlaidCanvas.setCapture?this.overlaidCanvas.setCapture():document.body.addEventListener("mouseup",this._mouseEventHandler,!1)):"mouseup"===n&&(t.chart.overlaidCanvas.releaseCapture?t.chart.overlaidCanvas.releaseCapture():document.body.removeEventListener("mouseup",this._mouseEventHandler,!1));break}t=null}t&&t.cursor?e.target.style.cursor=t.cursor:e.target.style.cursor=this._defaultCursor}if(this._toolTip&&this._toolTip.enabled){var l=this.plotArea;(a.x<l.x1||a.x>l.x2||a.y<l.y1||a.y>l.y2)&&this._toolTip.hide()}
this.isDrag&&this.zoomEnabled||!this._eventManager||this._eventManager.mouseEventHandler(e)}}},s.prototype._plotAreaMouseDown=function(e,t){this.isDrag=!0,this.plotInfo.axisPlacement,this.dragStartPoint={x:e,y:t}},s.prototype._plotAreaMouseUp=function(e,t){if(("normal"===this.plotInfo.axisPlacement||"xySwapped"===this.plotInfo.axisPlacement)&&this.isDrag){var i=t-this.dragStartPoint.y,a=e-this.dragStartPoint.x,n=this.zoomType.indexOf("x")>=0,s=this.zoomType.indexOf("y")>=0,r=!1;if(this.resetOverlayedCanvas(),"xySwapped"===this.plotInfo.axisPlacement){var o=s;s=n,n=o}if(this.panEnabled||this.zoomEnabled){if(this.panEnabled)for(var l=0,h=0;h<this._axes.length;h++){var d=this._axes[h];d.viewportMinimum<d.minimum?(l=d.minimum-d.viewportMinimum,d.sessionVariables.newViewportMinimum=d.viewportMinimum+l,d.sessionVariables.newViewportMaximum=d.viewportMaximum+l,r=!0):d.viewportMaximum>d.maximum&&(l=d.viewportMaximum-d.maximum,d.sessionVariables.newViewportMinimum=d.viewportMinimum-l,d.sessionVariables.newViewportMaximum=d.viewportMaximum-l,r=!0)}else if((!n||Math.abs(a)>2)&&(!s||Math.abs(i)>2)&&this.zoomEnabled){if(!this.dragStartPoint)return;var c={x1:n?this.dragStartPoint.x:this.plotArea.x1,y1:s?this.dragStartPoint.y:this.plotArea.y1,x2:n?e:this.plotArea.x2,y2:s?t:this.plotArea.y2};Math.abs(c.x1-c.x2)>2&&Math.abs(c.y1-c.y2)>2&&this._zoomPanToSelectedRegion(c.x1,c.y1,c.x2,c.y2)&&(r=!0)}r&&(this._ignoreNextEvent=!0,this._dispatchRangeEvent("rangeChanging","zoom"),this.render(),this._dispatchRangeEvent("rangeChanged","zoom"),r&&this.zoomEnabled&&"none"===this._zoomButton.style.display&&((0,z.show)(this._zoomButton,this._resetButton),setButtonState(this,this._zoomButton,"pan"),setButtonState(this,this._resetButton,"reset")))}}this.isDrag=!1},s.prototype._plotAreaMouseMove=function(e,t){if(this.isDrag&&"none"!==this.plotInfo.axisPlacement){var i=0,a=0,n=null,s=null,r=this.zoomType.indexOf("x")>=0,o=this.zoomType.indexOf("y")>=0;if("xySwapped"===this.plotInfo.axisPlacement){var l=o;o=r,r=l}if(i=this.dragStartPoint.x-e,a=this.dragStartPoint.y-t,Math.abs(i)>2&&Math.abs(i)<8&&(this.panEnabled||this.zoomEnabled)?this._toolTip.hide():this.panEnabled||this.zoomEnabled||this._toolTip.mouseMoveHandler(e,t),(!r||Math.abs(i)>2||!o||Math.abs(a)>2)&&(this.panEnabled||this.zoomEnabled))if(this.panEnabled)s={x1:r?this.plotArea.x1+i:this.plotArea.x1,y1:o?this.plotArea.y1+a:this.plotArea.y1,x2:r?this.plotArea.x2+i:this.plotArea.x2,y2:o?this.plotArea.y2+a:this.plotArea.y2},this._zoomPanToSelectedRegion(s.x1,s.y1,s.x2,s.y2,!0)&&(this._dispatchRangeEvent("rangeChanging","pan"),this.render(),this._dispatchRangeEvent("rangeChanged","pan"),this.dragStartPoint.x=e,this.dragStartPoint.y=t);else if(this.zoomEnabled){this.resetOverlayedCanvas(),n=this.overlaidCanvasCtx.globalAlpha,this.overlaidCanvasCtx.globalAlpha=.7,this.overlaidCanvasCtx.fillStyle="#A0ABB8";var h={x1:r?this.dragStartPoint.x:this.plotArea.x1,y1:o?this.dragStartPoint.y:this.plotArea.y1,x2:r?e-this.dragStartPoint.x:this.plotArea.x2-this.plotArea.x1,y2:o?t-this.dragStartPoint.y:this.plotArea.y2-this.plotArea.y1};this.overlaidCanvasCtx.fillRect(h.x1,h.y1,h.x2,h.y2),this.overlaidCanvasCtx.globalAlpha=n}}else this._toolTip.mouseMoveHandler(e,t)},s.prototype._zoomPanToSelectedRegion=function(e,t,i,a,n){n=n||!1;var s=this.zoomType.indexOf("x")>=0,r=this.zoomType.indexOf("y")>=0,o=!1,l=[],h=[];this.axisX&&s&&l.push(this.axisX),this.axisY&&r&&l.push(this.axisY),this.axisY2&&r&&l.push(this.axisY2);for(var d=[],c=0;c<l.length;c++){var p=l[c],x=p.convertPixelToValue({x:e,y:t}),u=p.convertPixelToValue({x:i,y:a});if(x>u){var m=u;u=x,x=m}if(isFinite(p.dataInfo.minDiff))if(Math.abs(u-x)<3*Math.abs(p.dataInfo.minDiff)||x<p.minimum||u>p.maximum){if(!n){o=!1;break}}else h.push(p),d.push({val1:x,val2:u}),o=!0}if(o)for(var c=0;c<h.length;c++){var p=h[c],v=d[c];p.setViewPortRange(v.val1,v.val2)}return o},s.prototype.preparePlotArea=function(){var e=this.plotArea,t=this.axisY?this.axisY:this.axisY2;if(!z.isCanvasSupported&&(e.x1>0||e.y1>0)&&e.ctx.translate(e.x1,e.y1),this.axisX&&t)e.x1=this.axisX.lineCoordinates.x1<this.axisX.lineCoordinates.x2?this.axisX.lineCoordinates.x1:t.lineCoordinates.x1,e.y1=this.axisX.lineCoordinates.y1<t.lineCoordinates.y1?this.axisX.lineCoordinates.y1:t.lineCoordinates.y1,e.x2=this.axisX.lineCoordinates.x2>t.lineCoordinates.x2?this.axisX.lineCoordinates.x2:t.lineCoordinates.x2,e.y2=this.axisX.lineCoordinates.y2>this.axisX.lineCoordinates.y1?this.axisX.lineCoordinates.y2:t.lineCoordinates.y2,e.width=e.x2-e.x1,e.height=e.y2-e.y1;else{var i=this.layoutManager.getFreeSpace();e.x1=i.x1,e.x2=i.x2,e.y1=i.y1,e.y2=i.y2,e.width=i.width,e.height=i.height}z.isCanvasSupported||(e.canvas.width=e.width,e.canvas.height=e.height,e.canvas.style.left=e.x1+"px",e.canvas.style.top=e.y1+"px",(e.x1>0||e.y1>0)&&e.ctx.translate(-e.x1,-e.y1)),e.layoutManager=new g.default(e.x1,e.y1,e.x2,e.y2,2)},s.prototype.getPixelCoordinatesOnPlotArea=function(e,t){return{x:this.axisX.getPixelCoordinatesOnAxis(e).x,y:this.axisY.getPixelCoordinatesOnAxis(t).y}},s.prototype.renderIndexLabels=function(e){for(var t=e||this.plotArea.ctx,i=this.plotArea,a=0,n=0,s=0,r=0,o=0,l=0,h=0,d=0,c=0,p=0;p<this._indexLabels.length;p++){var u,m,v=this._indexLabels[p],g=v.chartType.toLowerCase(),y=(0,z.getProperty)("indexLabelFontColor",v.dataPoint,v.dataSeries),f=(0,z.getProperty)("indexLabelFontSize",v.dataPoint,v.dataSeries),b=(0,z.getProperty)("indexLabelFontFamily",v.dataPoint,v.dataSeries),M=(0,z.getProperty)("indexLabelFontStyle",v.dataPoint,v.dataSeries),P=(0,z.getProperty)("indexLabelFontWeight",v.dataPoint,v.dataSeries),S=(0,z.getProperty)("indexLabelBackgroundColor",v.dataPoint,v.dataSeries),T=(0,z.getProperty)("indexLabelMaxWidth",v.dataPoint,v.dataSeries),C=(0,z.getProperty)("indexLabelWrap",v.dataPoint,v.dataSeries),k={percent:null,total:null},w=null;(v.dataSeries.type.indexOf("stacked")>=0||"pie"===v.dataSeries.type||"doughnut"===v.dataSeries.type)&&(k=this.getPercentAndTotal(v.dataSeries,v.dataPoint)),(v.dataSeries.indexLabelFormatter||v.dataPoint.indexLabelFormatter)&&(w={chart:this._options,dataSeries:v.dataSeries,dataPoint:v.dataPoint,index:v.indexKeyword,total:k.total,percent:k.percent});var _=v.dataPoint.indexLabelFormatter?v.dataPoint.indexLabelFormatter(w):v.dataPoint.indexLabel?this.replaceKeywordsWithValue(v.dataPoint.indexLabel,v.dataPoint,v.dataSeries,null,v.indexKeyword):v.dataSeries.indexLabelFormatter?v.dataSeries.indexLabelFormatter(w):v.dataSeries.indexLabel?this.replaceKeywordsWithValue(v.dataSeries.indexLabel,v.dataPoint,v.dataSeries,null,v.indexKeyword):null;if(null!==_&&""!==_){var A=(0,z.getProperty)("indexLabelPlacement",v.dataPoint,v.dataSeries),L=(0,z.getProperty)("indexLabelOrientation",v.dataPoint,v.dataSeries),B=v.direction,F=v.dataSeries.axisX,D=v.dataSeries.axisY,X=new x.default(t,{x:0,y:0,maxWidth:T||.5*this.width,maxHeight:C?5*f:1.5*f,angle:"horizontal"===L?0:-90,text:_,padding:0,backgroundColor:S,horizontalAlign:"left",fontSize:f,fontFamily:b,fontWeight:P,fontColor:y,fontStyle:M,textBaseline:"top"});X.measureText();if(g.indexOf("line")>=0||g.indexOf("area")>=0||g.indexOf("bubble")>=0||g.indexOf("scatter")>=0){if(v.dataPoint.x<F.viewportMinimum||v.dataPoint.x>F.viewportMaximum||v.dataPoint.y<D.viewportMinimum||v.dataPoint.y>D.viewportMaximum)continue}else if(v.dataPoint.x<F.viewportMinimum||v.dataPoint.x>F.viewportMaximum)continue;if(h=2,l=2,"horizontal"===L?(d=X.width,c=X.height):(c=X.width,d=X.height),"normal"===this.plotInfo.axisPlacement)g.indexOf("line")>=0||g.indexOf("area")>=0?(A="auto",h=4):g.indexOf("stacked")>=0?"auto"===A&&(A="inside"):"bubble"!==g&&"scatter"!==g||(A="inside"),u=v.point.x-d/2,"inside"!==A?(n=i.y1,s=i.y2,B>0?(m=v.point.y-c-h)<n&&(m="auto"===A?Math.max(v.point.y,n)+h:n+h):(m=v.point.y+h)>s-c-h&&(m="auto"===A?Math.min(v.point.y,s)-c-h:s-c-h)):(n=Math.max(v.bounds.y1,i.y1),s=Math.min(v.bounds.y2,i.y2),a=g.indexOf("range")>=0?B>0?Math.max(v.bounds.y1,i.y1)+c/2+h:Math.min(v.bounds.y2,i.y2)-c/2-h:(Math.max(v.bounds.y1,i.y1)+Math.min(v.bounds.y2,i.y2))/2,B>0?(m=Math.max(v.point.y,a)-c/2)<n&&("bubble"===g||"scatter"===g)&&(m=Math.max(v.point.y-c-h,i.y1+h)):(m=Math.min(v.point.y,a)-c/2)>s-c-h&&("bubble"===g||"scatter"===g)&&(m=Math.min(v.point.y+h,i.y2-c-h)),m=Math.min(m,s-c));else if(g.indexOf("line")>=0||g.indexOf("area")>=0||g.indexOf("scatter")>=0?(A="auto",l=4):g.indexOf("stacked")>=0?"auto"===A&&(A="inside"):"bubble"===g&&(A="inside"),m=v.point.y-c/2,"inside"!==A)r=i.x1,o=i.x2,B<0?(u=v.point.x-d-l)<r&&(u="auto"===A?Math.max(v.point.x,r)+l:r+l):(u=v.point.x+l)>o-d-l&&(u="auto"===A?Math.min(v.point.x,o)-d-l:o-d-l);else{if(r=Math.max(v.bounds.x1,i.x1),o=Math.min(v.bounds.x2,i.x2),g.indexOf("range")>=0)a=B<0?Math.max(v.bounds.x1,i.x1)+d/2+l:Math.min(v.bounds.x2,i.x2)-d/2-l;else var a=(Math.max(v.bounds.x1,i.x1)+Math.min(v.bounds.x2,i.x2))/2;u=B<0?Math.max(v.point.x,a)-d/2:Math.min(v.point.x,a)-d/2,u=Math.max(u,r)}"vertical"===L&&(m+=c),X.x=u,X.y=m,X.render(!0)}}return{source:t,dest:this.plotArea.ctx,animationCallback:I.default.fadeInAnimation,easingFunction:I.default.easing.easeInQuad,animationBase:0,startTimePercent:.7}},s.prototype.renderLine=function(e){var t=e.targetCanvasCtx||this.plotArea.ctx;if(!(e.dataSeriesIndexes.length<=0)){var i=this._eventManager.ghostCtx;t.save();var a=this.plotArea;t.beginPath(),t.rect(a.x1,a.y1,a.width,a.height),t.clip();for(var n=[],s=0;s<e.dataSeriesIndexes.length;s++){var r=e.dataSeriesIndexes[s],o=this.data[r];t.lineWidth=o.lineThickness;var l=o.dataPoints;t.setLineDash&&t.setLineDash((0,z.getLineDashArray)(o.lineDashType,o.lineThickness));var h=o.id;this._eventManager.objectMap[h]={objectType:"dataSeries",dataSeriesIndex:r};var d=(0,z.intToHexColorString)(h);i.strokeStyle=d,i.lineWidth=o.lineThickness>0?Math.max(o.lineThickness,4):0;var c=o._colorSet,p=c[0];t.strokeStyle=p;var x,u,v,g=!0,y=0;if(t.beginPath(),l.length>0){var f=!1;for(y=0;y<l.length;y++)if(!((v=l[y].x.getTime?l[y].x.getTime():l[y].x)<e.axisX.dataInfo.viewPortMin||v>e.axisX.dataInfo.viewPortMax))if("number"==typeof l[y].y){x=e.axisX.conversionParameters.reference+e.axisX.conversionParameters.pixelPerUnit*(v-e.axisX.conversionParameters.minimum)+.5<<0,u=e.axisY.conversionParameters.reference+e.axisY.conversionParameters.pixelPerUnit*(l[y].y-e.axisY.conversionParameters.minimum)+.5<<0;var b=o.dataPointIds[y];if(this._eventManager.objectMap[b]={id:b,objectType:"dataPoint",dataSeriesIndex:r,dataPointIndex:y,x1:x,y1:u},g||f?(t.beginPath(),t.moveTo(x,u),z.isCanvasSupported&&(i.beginPath(),i.moveTo(x,u)),g=!1,f=!1):(t.lineTo(x,u),z.isCanvasSupported&&i.lineTo(x,u),y%500==0&&(t.stroke(),t.beginPath(),t.moveTo(x,u),z.isCanvasSupported&&(i.stroke(),i.beginPath(),i.moveTo(x,u)))),l[y].markerSize>0||o.markerSize>0){var M=o.getMarkerProperties(y,x,u,t);n.push(M);var P=(0,z.intToHexColorString)(b);z.isCanvasSupported&&n.push({x:x,y:u,ctx:i,type:M.type,size:M.size,color:P,borderColor:P,borderThickness:M.borderThickness})}(l[y].indexLabel||o.indexLabel||l[y].indexLabelFormatter||o.indexLabelFormatter)&&this._indexLabels.push({chartType:"line",dataPoint:l[y],dataSeries:o,point:{x:x,y:u},direction:l[y].y>=0?1:-1,color:p})}else y>0&&(t.stroke(),z.isCanvasSupported&&i.stroke()),f=!0;t.stroke(),z.isCanvasSupported&&i.stroke()}}return m.default.drawMarkers(n),t.restore(),t.beginPath(),z.isCanvasSupported&&i.beginPath(),{source:t,dest:this.plotArea.ctx,animationCallback:I.default.xClipAnimation,easingFunction:I.default.easing.linear,animationBase:0}}},s.prototype.renderStepLine=function(e){var t=e.targetCanvasCtx||this.plotArea.ctx;if(!(e.dataSeriesIndexes.length<=0)){var i=this._eventManager.ghostCtx;t.save();var a=this.plotArea;t.beginPath(),t.rect(a.x1,a.y1,a.width,a.height),t.clip();for(var n=[],s=0;s<e.dataSeriesIndexes.length;s++){var r=e.dataSeriesIndexes[s],o=this.data[r];t.lineWidth=o.lineThickness;var l=o.dataPoints;t.setLineDash&&t.setLineDash((0,z.getLineDashArray)(o.lineDashType,o.lineThickness));var h=o.id;this._eventManager.objectMap[h]={objectType:"dataSeries",dataSeriesIndex:r};var d=(0,z.intToHexColorString)(h);i.strokeStyle=d,i.lineWidth=o.lineThickness>0?Math.max(o.lineThickness,4):0;var c=o._colorSet,p=c[0];t.strokeStyle=p;var x,u,v,g=!0,y=0;if(t.beginPath(),l.length>0){var f=!1;for(y=0;y<l.length;y++)if(!((v=l[y].getTime?l[y].x.getTime():l[y].x)<e.axisX.dataInfo.viewPortMin||v>e.axisX.dataInfo.viewPortMax))if("number"==typeof l[y].y){var b=u;x=e.axisX.conversionParameters.reference+e.axisX.conversionParameters.pixelPerUnit*(v-e.axisX.conversionParameters.minimum)+.5<<0,u=e.axisY.conversionParameters.reference+e.axisY.conversionParameters.pixelPerUnit*(l[y].y-e.axisY.conversionParameters.minimum)+.5<<0;var M=o.dataPointIds[y];if(this._eventManager.objectMap[M]={id:M,objectType:"dataPoint",dataSeriesIndex:r,dataPointIndex:y,x1:x,y1:u},g||f?(t.beginPath(),t.moveTo(x,u),z.isCanvasSupported&&(i.beginPath(),i.moveTo(x,u)),g=!1,f=!1):(t.lineTo(x,b),z.isCanvasSupported&&i.lineTo(x,b),t.lineTo(x,u),z.isCanvasSupported&&i.lineTo(x,u),y%500==0&&(t.stroke(),t.beginPath(),t.moveTo(x,u),z.isCanvasSupported&&(i.stroke(),i.beginPath(),i.moveTo(x,u)))),l[y].markerSize>0||o.markerSize>0){var P=o.getMarkerProperties(y,x,u,t);n.push(P);var S=(0,z.intToHexColorString)(M);z.isCanvasSupported&&n.push({x:x,y:u,ctx:i,type:P.type,size:P.size,color:S,borderColor:S,borderThickness:P.borderThickness})}(l[y].indexLabel||o.indexLabel||l[y].indexLabelFormatter||o.indexLabelFormatter)&&this._indexLabels.push({chartType:"stepLine",dataPoint:l[y],dataSeries:o,point:{x:x,y:u},direction:l[y].y>=0?1:-1,color:p})}else y>0&&(t.stroke(),z.isCanvasSupported&&i.stroke()),f=!0;t.stroke(),z.isCanvasSupported&&i.stroke()}}return m.default.drawMarkers(n),t.restore(),t.beginPath(),z.isCanvasSupported&&i.beginPath(),{source:t,dest:this.plotArea.ctx,animationCallback:I.default.xClipAnimation,easingFunction:I.default.easing.linear,animationBase:0}}},s.prototype.animationRequestId=null,s.prototype.requestAnimFrame=function(){return window.requestAnimationFrame||window.webkitRequestAnimationFrame||window.mozRequestAnimationFrame||window.oRequestAnimationFrame||window.msRequestAnimationFrame||function(e){window.setTimeout(e,1e3/60)}}(),s.prototype.cancelRequestAnimFrame=function(){return window.cancelAnimationFrame||window.webkitCancelRequestAnimationFrame||window.mozCancelRequestAnimationFrame||window.oCancelRequestAnimationFrame||window.msCancelRequestAnimationFrame||clearTimeout}(),s.prototype.getPercentAndTotal=function(e,t){var i=null,a=null,n=null;if(e.type.indexOf("stacked")>=0)a=0,(i=t.x.getTime?t.x.getTime():t.x)in e.plotUnit.yTotals&&(a=e.plotUnit.yTotals[i],n=isNaN(t.y)?0:0===a?0:t.y/a*100);else if("pie"===e.type||"doughnut"===e.type){a=0;for(var s=0;s<e.dataPoints.length;s++)isNaN(e.dataPoints[s].y)||(a+=e.dataPoints[s].y);n=isNaN(t.y)?0:t.y/a*100}return{percent:n,total:a}},s.prototype.replaceKeywordsWithValue=function(e,t,i,a,n){var s=/\{.*?\}|"[^"]*"|'[^']*'/g,r=this;if(n=void 0===n?0:n,(i.type.indexOf("stacked")>=0||"pie"===i.type||"doughnut"===i.type)&&(e.indexOf("#percent")>=0||e.indexOf("#total")>=0)){var o="#percent",l="#total",h=this.getPercentAndTotal(i,t);l=isNaN(h.total)?l:h.total,o=isNaN(h.percent)?o:h.percent;do{var d="";if(i.percentFormatString)d=i.percentFormatString;else{d="#,##0.";var c=Math.max(Math.ceil(Math.log(1/Math.abs(o))/Math.LN10),2);!isNaN(c)&&isFinite(c)||(c=2);for(var p=0;p<c;p++)d+="#"}e=e.replace("#percent",(0,z.numberFormat)(o,d,r._cultureInfo)),e=e.replace("#total",(0,z.numberFormat)(l,i.yValueFormatString?i.yValueFormatString:"#,##0.########"))}while(e.indexOf("#percent")>=0||e.indexOf("#total")>=0)}var x=function(e){if('"'===e[0]&&'"'===e[e.length-1]||"'"===e[0]&&"'"===e[e.length-1])return e.slice(1,e.length-1);var s=(0,z.trimString)(e.slice(1,e.length-1));s=s.replace("#index",n);var o=null;try{var l=s.match(/(.*?)\s*\[\s*(.*?)\s*\]/);l&&l.length>0&&(o=(0,z.trimString)(l[2]),s=(0,z.trimString)(l[1]))}catch(e){}var h=null;if("color"===s)return t.color?t.color:i.color?i.color:i._colorSet[a%i._colorSet.length];if(t.hasOwnProperty(s))h=t;else{if(!i.hasOwnProperty(s))return"";h=i}var d=h[s];return null!==o&&(d=d[o]),"x"===s?r.axisX&&"dateTime"===r.plotInfo.axisXValueType?dateFormat(d,t.xValueFormatString?t.xValueFormatString:i.xValueFormatString?i.xValueFormatString:r.axisX&&r.axisX.valueFormatString?r.axisX.valueFormatString:"DD MMM YY",r._cultureInfo):(0,z.numberFormat)(d,t.xValueFormatString?t.xValueFormatString:i.xValueFormatString?i.xValueFormatString:"#,##0.########",r._cultureInfo):"y"===s?(0,z.numberFormat)(d,t.yValueFormatString?t.yValueFormatString:i.yValueFormatString?i.yValueFormatString:"#,##0.########",r._cultureInfo):"z"===s?(0,z.numberFormat)(d,t.zValueFormatString?t.zValueFormatString:i.zValueFormatString?i.zValueFormatString:"#,##0.########",r._cultureInfo):d};return e.replace(s,x)},s.prototype.renderSpline=D.SplineChart,s.prototype.renderColumn=D.ColumnChart,s.prototype.renderStackedColumn=D.StackedColumnChart,s.prototype.renderStackedColumn100=D.StackedColumn100Chart,s.prototype.renderBar=D.BarChart,s.prototype.renderStackedBar=D.StackedBarChart,s.prototype.renderStackedBar100=D.StackedBar100Chart,s.prototype.renderArea=D.AreaChart,s.prototype.renderSplineArea=D.SplineAreaChart,s.prototype.renderStepArea=D.StepAreaChart,s.prototype.renderStackedArea=D.StackedAreaChart,s.prototype.renderStackedArea100=D.StackedArea100Chart,s.prototype.renderBubble=D.BubbleChart,s.prototype.renderScatter=D.ScatterChart,s.prototype.renderCandlestick=D.CandlestickChart,s.prototype.renderRangeColumn=D.RangeColumnChart,s.prototype.renderRangeBar=D.RangeBarChart,s.prototype.renderRangeArea=D.RangeAreaChart,s.prototype.renderRangeSplineArea=D.RangeSplineAreaChart,s.prototype.renderPie=D.PieChart,i.default=s},{"../charts/index":6,"../constants/options":23,"../constants/themes":24,"../core/axis":26,"../core/culture_info":29,"../core/legend":33,"../core/title":35,"../helpers/animator":37,"../helpers/render":38,"../helpers/utils":39,"./animator":25,"./canvasjs":27,"./data_series":30,"./event_manager":31,"./layout_manager":32,"./text_block":34,"./tooltip":36}],29:[function(e,t,i){"use strict";function a(e){var t;e&&o.cultures[e]&&(t=o.cultures[e]),a.base.constructor.call(this,"CultureInfo",t)}Object.defineProperty(i,"__esModule",{value:!0});var n=e("./canvasjs"),s=function(e){return e&&e.__esModule?e:{default:e}}(n),r=e("../helpers/utils"),o=e("../constants/culture");(0,r.extend)(a,s.default),i.default=a},{"../constants/culture":22,"../helpers/utils":39,"./canvasjs":27}],30:[function(e,t,i){"use strict";function a(e,t,i,n,s){a.base.constructor.call(this,"DataSeries",t,i),this.chart=e,this.canvas=e.canvas,this._ctx=e.canvas.ctx,this.index=n,this.noDataPointsInPlotArea=0,this.id=s,this.chart._eventManager.objectMap[s]={id:s,objectType:"dataSeries",dataSeriesIndex:n},this.dataPointIds=[],this.plotUnit=[],this.axisX=null,this.axisY=null,null===this.fillOpacity&&(this.type.match(/area/i)?this.fillOpacity=.7:this.fillOpacity=1),this.axisPlacement=this.getDefaultAxisPlacement(),void 0===this._options.indexLabelFontSize&&(this.indexLabelFontSize=this.chart.getAutoFontSize(this.indexLabelFontSize))}Object.defineProperty(i,"__esModule",{value:!0});var n=e("./canvasjs"),s=function(e){return e&&e.__esModule?e:{default:e}}(n),r=e("../helpers/utils");(0,r.extend)(a,s.default),a.prototype.getDefaultAxisPlacement=function(){var e=this.type;return"column"===e||"line"===e||"stepLine"===e||"spline"===e||"area"===e||"stepArea"===e||"splineArea"===e||"stackedColumn"===e||"stackedLine"===e||"bubble"===e||"scatter"===e||"stackedArea"===e||"stackedColumn100"===e||"stackedLine100"===e||"stackedArea100"===e||"candlestick"===e||"ohlc"===e||"rangeColumn"===e||"rangeArea"===e||"rangeSplineArea"===e?"normal":"bar"===e||"stackedBar"===e||"stackedBar100"===e||"rangeBar"===e?"xySwapped":"pie"===e||"doughnut"===e||"funnel"===e?"none":(window.console.log("Unknown Chart Type: "+e),null)},a.getDefaultLegendMarker=function(e){return"column"===e||"stackedColumn"===e||"stackedLine"===e||"bar"===e||"stackedBar"===e||"stackedBar100"===e||"bubble"===e||"scatter"===e||"stackedColumn100"===e||"stackedLine100"===e||"stepArea"===e||"candlestick"===e||"ohlc"===e||"rangeColumn"===e||"rangeBar"===e||"rangeArea"===e||"rangeSplineArea"===e?"square":"line"===e||"stepLine"===e||"spline"===e||"pie"===e||"doughnut"===e||"funnel"===e?"circle":"area"===e||"splineArea"===e||"stackedArea"===e||"stackedArea100"===e?"triangle":(window.console.log("Unknown Chart Type: "+e),null)},a.prototype.getDataPointAtX=function(e,t){if(!this.dataPoints||0===this.dataPoints.length)return null;var i={dataPoint:null,distance:1/0,index:NaN},a=null,n=0,s=0,r=1,o=1/0,l=0,h=0,d=0;if("none"!==this.chart.plotInfo.axisPlacement){var c=this.dataPoints[this.dataPoints.length-1].x-this.dataPoints[0].x;d=c>0?Math.min(Math.max((this.dataPoints.length-1)/c*(e-this.dataPoints[0].x)>>0,0),this.dataPoints.length):0}for(;;){if((s=r>0?d+n:d-n)>=0&&s<this.dataPoints.length){a=this.dataPoints[s];var p=Math.abs(a.x-e);p<i.distance&&(i.dataPoint=a,i.distance=p,i.index=s);var x=Math.abs(a.x-e);if(x<=o?o=x:r>0?l++:h++,l>1e3&&h>1e3)break}else if(d-n<0&&d+n>=this.dataPoints.length)break;-1===r?(n++,r=1):r=-1}return t||i.dataPoint.x!==e?t&&null!==i.dataPoint?i:null:i},a.prototype.getDataPointAtXY=function(e,t,i){if(!this.dataPoints||0===this.dataPoints.length)return null;i=i||!1;var a=[],n=0,s=0,o=1,l=!1,h=1/0,d=0,c=0,p=0;if("none"!==this.chart.plotInfo.axisPlacement){var x=this.chart.axisX.getXValueAt({x:e,y:t}),u=this.dataPoints[this.dataPoints.length-1].x-this.dataPoints[0].x;p=u>0?Math.min(Math.max((this.dataPoints.length-1)/u*(x-this.dataPoints[0].x)>>0,0),this.dataPoints.length):0}for(;;){if((s=o>0?p+n:p-n)>=0&&s<this.dataPoints.length){var m=this.dataPointIds[s],v=this.chart._eventManager.objectMap[m],g=this.dataPoints[s],y=null;if(v){switch(this.type){case"column":case"stackedColumn":case"stackedColumn100":case"bar":case"stackedBar":case"stackedBar100":case"rangeColumn":case"rangeBar":e>=v.x1&&e<=v.x2&&t>=v.y1&&t<=v.y2&&(a.push({dataPoint:g,dataPointIndex:s,dataSeries:this,distance:Math.min(Math.abs(v.x1-e),Math.abs(v.x2-e),Math.abs(v.y1-t),Math.abs(v.y2-t))}),l=!0);break;case"line":case"stepLine":case"spline":case"area":case"stepArea":case"stackedArea":case"stackedArea100":case"splineArea":case"scatter":var f=(0,r.getProperty)("markerSize",g,this)||4,b=i?20:f;y=Math.sqrt(Math.pow(v.x1-e,2)+Math.pow(v.y1-t,2)),y<=b&&a.push({dataPoint:g,dataPointIndex:s,dataSeries:this,distance:y});var M=Math.abs(v.x1-e);M<=h?h=M:o>0?d++:c++,y<=f/2&&(l=!0);break;case"rangeArea":case"rangeSplineArea":var f=(0,r.getProperty)("markerSize",g,this)||4,b=i?20:f;y=Math.min(Math.sqrt(Math.pow(v.x1-e,2)+Math.pow(v.y1-t,2)),Math.sqrt(Math.pow(v.x1-e,2)+Math.pow(v.y2-t,2))),y<=b&&a.push({dataPoint:g,dataPointIndex:s,dataSeries:this,distance:y});var M=Math.abs(v.x1-e);M<=h?h=M:o>0?d++:c++,y<=f/2&&(l=!0);break;case"bubble":var f=v.size;y=Math.sqrt(Math.pow(v.x1-e,2)+Math.pow(v.y1-t,2)),y<=f/2&&(a.push({dataPoint:g,dataPointIndex:s,dataSeries:this,distance:y}),l=!0);break;case"pie":case"doughnut":var P=v.center,S="doughnut"===this.type?v.percentInnerRadius*v.radius:0;if((y=Math.sqrt(Math.pow(P.x-e,2)+Math.pow(P.y-t,2)))<v.radius&&y>S){var T=t-P.y,C=e-P.x,k=Math.atan2(T,C);k<0&&(k+=2*Math.PI),k=Number(((k/Math.PI*180%360+360)%360).toFixed(12));var w=Number(((v.startAngle/Math.PI*180%360+360)%360).toFixed(12)),_=Number(((v.endAngle/Math.PI*180%360+360)%360).toFixed(12));0===_&&v.endAngle>1&&(_=360),w>=_&&0!==g.y&&(_+=360,k<w&&(k+=360)),k>w&&k<_&&(a.push({dataPoint:g,dataPointIndex:s,dataSeries:this,distance:0}),l=!0)}break;case"candlestick":(e>=v.x1-v.borderThickness/2&&e<=v.x2+v.borderThickness/2&&t>=v.y2-v.borderThickness/2&&t<=v.y3+v.borderThickness/2||Math.abs(v.x2-e+v.x1-e)<v.borderThickness&&t>=v.y1&&t<=v.y4)&&(a.push({dataPoint:g,dataPointIndex:s,dataSeries:this,distance:Math.min(Math.abs(v.x1-e),Math.abs(v.x2-e),Math.abs(v.y2-t),Math.abs(v.y3-t))}),l=!0);break;case"ohlc":(Math.abs(v.x2-e+v.x1-e)<v.borderThickness&&t>=v.y2&&t<=v.y3||e>=v.x1&&e<=(v.x2+v.x1)/2&&t>=v.y1-v.borderThickness/2&&t<=v.y1+v.borderThickness/2||e>=(v.x1+v.x2)/2&&e<=v.x2&&t>=v.y4-v.borderThickness/2&&t<=v.y4+v.borderThickness/2)&&(a.push({dataPoint:g,dataPointIndex:s,dataSeries:this,distance:Math.min(Math.abs(v.x1-e),Math.abs(v.x2-e),Math.abs(v.y2-t),Math.abs(v.y3-t))}),l=!0)}if(l||d>1e3&&c>1e3)break}}else if(p-n<0&&p+n>=this.dataPoints.length)break;-1===o?(n++,o=1):o=-1}for(var A=null,L=0;L<a.length;L++)A?a[L].distance<=A.distance&&(A=a[L]):A=a[L];return A},a.prototype.getMarkerProperties=function(e,t,i,a){var n=this.dataPoints,s=this,r=n[e].markerColor?n[e].markerColor:s.markerColor?s.markerColor:n[e].color?n[e].color:s.color?s.color:s._colorSet[e%s._colorSet.length],o=n[e].markerBorderColor?n[e].markerBorderColor:s.markerBorderColor?s.markerBorderColor:null,l=n[e].markerBorderThickness?n[e].markerBorderThickness:s.markerBorderThickness?s.markerBorderThickness:null;return{x:t,y:i,ctx:a,type:n[e].markerType?n[e].markerType:s.markerType,size:n[e].markerSize?n[e].markerSize:s.markerSize,color:r,borderColor:o,borderThickness:l}},i.default=a},{"../helpers/utils":39,"./canvasjs":27}],31:[function(e,t,i){"use strict";function a(e){this.chart=e,this.lastObjectId=0;this.objectMap=[],this.rectangularRegionEventSubscriptions=[],this.previousDataPointEventObject=null,this.ghostCanvas=(0,n.createCanvas)(this.chart.width,this.chart.height),this.ghostCtx=this.ghostCanvas.getContext("2d");this.mouseoveredObjectMaps=[]}Object.defineProperty(i,"__esModule",{value:!0});var n=e("../helpers/utils");a.prototype.reset=function(){this.lastObjectId=0,this.objectMap=[],this.rectangularRegionEventSubscriptions=[],this.previousDataPointEventObject=null,this.eventObjects=[],n.isCanvasSupported&&(this.ghostCtx.clearRect(0,0,this.chart.width,this.chart.height),this.ghostCtx.beginPath())},a.prototype.getNewObjectTrackingId=function(){return++this.lastObjectId},a.prototype.mouseEventHandler=function(e){if("mousemove"===e.type||"click"===e.type){var t=[],i=(0,n.getMouseCoordinates)(e),a=null;if((a=this.chart.getObjectAtXY(i.x,i.y,!1))&&void 0!==this.objectMap[a]){var s=this.objectMap[a];if("dataPoint"===s.objectType){var r=this.chart.data[s.dataSeriesIndex],o=r.dataPoints[s.dataPointIndex],l=s.dataPointIndex;s.eventParameter={x:i.x,y:i.y,dataPoint:o,dataSeries:r._options,dataPointIndex:l,dataSeriesIndex:r.index,chart:this.chart._publicChartReference},s.eventContext={context:o,userContext:o,mouseover:"mouseover",mousemove:"mousemove",mouseout:"mouseout",click:"click"},t.push(s),s=this.objectMap[r.id],s.eventParameter={x:i.x,y:i.y,dataPoint:o,dataSeries:r._options,dataPointIndex:l,dataSeriesIndex:r.index,chart:this.chart._publicChartReference},s.eventContext={context:r,userContext:r._options,mouseover:"mouseover",mousemove:"mousemove",mouseout:"mouseout",click:"click"},t.push(this.objectMap[r.id])}else if("legendItem"===s.objectType){var r=this.chart.data[s.dataSeriesIndex],o=null!==s.dataPointIndex?r.dataPoints[s.dataPointIndex]:null;s.eventParameter={x:i.x,y:i.y,dataSeries:r._options,dataPoint:o,dataPointIndex:s.dataPointIndex,dataSeriesIndex:s.dataSeriesIndex,chart:this.chart._publicChartReference},s.eventContext={context:this.chart.legend,userContext:this.chart.legend._options,mouseover:"itemmouseover",mousemove:"itemmousemove",mouseout:"itemmouseout",click:"itemclick"},t.push(s)}}for(var h=[],d=0;d<this.mouseoveredObjectMaps.length;d++){for(var c=!0,p=0;p<t.length;p++)if(t[p].id===this.mouseoveredObjectMaps[d].id){c=!1;break}c?this.fireEvent(this.mouseoveredObjectMaps[d],"mouseout",e):h.push(this.mouseoveredObjectMaps[d])}this.mouseoveredObjectMaps=h;for(var d=0;d<t.length;d++){for(var x=!1,p=0;p<this.mouseoveredObjectMaps.length;p++)if(t[d].id===this.mouseoveredObjectMaps[p].id){x=!0;break}x||(this.fireEvent(t[d],"mouseover",e),this.mouseoveredObjectMaps.push(t[d])),"click"===e.type?this.fireEvent(t[d],"click",e):"mousemove"===e.type&&this.fireEvent(t[d],"mousemove",e)}}},a.prototype.fireEvent=function(e,t,i){if(e&&t){var a=e.eventParameter,n=e.eventContext,s=e.eventContext.userContext;s&&n&&s[n[t]]&&s[n[t]].call(s,a),"mouseout"!==t?s.cursor&&s.cursor!==i.target.style.cursor&&(i.target.style.cursor=s.cursor):(i.target.style.cursor=this.chart._defaultCursor,delete e.eventParameter,delete e.eventContext),"click"===t&&"dataPoint"===e.objectType&&this.chart.pieDoughnutClickHandler&&this.chart.pieDoughnutClickHandler.call(this.chart.data[e.dataSeriesIndex],a)}},i.default=a},{"../helpers/utils":39}],32:[function(e,t,i){"use strict";function a(e,t,i,a,n){void 0===n&&(n=0),this._padding=n,this._x1=e,this._y1=t,this._x2=i,this._y2=a,this._topOccupied=this._padding,this._bottomOccupied=this._padding,this._leftOccupied=this._padding,this._rightOccupied=this._padding}Object.defineProperty(i,"__esModule",{value:!0}),a.prototype.registerSpace=function(e,t){"top"===e?this._topOccupied+=t.height:"bottom"===e?this._bottomOccupied+=t.height:"left"===e?this._leftOccupied+=t.width:"right"===e&&(this._rightOccupied+=t.width)},a.prototype.unRegisterSpace=function(e,t){"top"===e?this._topOccupied-=t.height:"bottom"===e?this._bottomOccupied-=t.height:"left"===e?this._leftOccupied-=t.width:"right"===e&&(this._rightOccupied-=t.width)},a.prototype.getFreeSpace=function(){return{x1:this._x1+this._leftOccupied,y1:this._y1+this._topOccupied,x2:this._x2-this._rightOccupied,y2:this._y2-this._bottomOccupied,width:this._x2-this._x1-this._rightOccupied-this._leftOccupied,height:this._y2-this._y1-this._bottomOccupied-this._topOccupied}},a.prototype.reset=function(){this._topOccupied=this._padding,this._bottomOccupied=this._padding,this._leftOccupied=this._padding,this._rightOccupied=this._padding},i.default=a},{}],33:[function(e,t,i){"use strict";function a(e){return e&&e.__esModule?e:{default:e}}function n(e,t,i){n.base.constructor.call(this,"Legend",t,i),this.chart=e,this.canvas=e.canvas,this.ctx=this.chart.ctx,this.ghostCtx=this.chart._eventManager.ghostCtx,this.items=[],this.width=0,this.height=0,this.orientation=null,this.dataSeries=[],this.bounds={x1:null,y1:null,x2:null,y2:null},void 0===this._options.fontSize&&(this.fontSize=this.chart.getAutoFontSize(this.fontSize)),this.lineHeight=(0,x.getFontHeightInPixels)(this.fontFamily,this.fontSize,this.fontWeight),this.horizontalSpacing=this.fontSize}Object.defineProperty(i,"__esModule",{value:!0});var s=e("./data_series"),r=a(s),o=e("./canvasjs"),l=a(o),h=e("./text_block"),d=a(h),c=e("../helpers/render"),p=a(c),x=e("../helpers/utils");(0,x.extend)(n,l.default),n.prototype.render=function(){var e=this.dockInsidePlotArea?this.chart.plotArea:this.chart,t=e.layoutManager.getFreeSpace(),i=null,a=0,n=0,s=0,o=0,l=[],h=[];"top"===this.verticalAlign||"bottom"===this.verticalAlign?(this.orientation="horizontal",i=this.verticalAlign,s=null!==this.maxWidth?this.maxWidth:.7*t.width,o=null!==this.maxHeight?this.maxHeight:.5*t.height):"center"===this.verticalAlign&&(this.orientation="vertical",i=this.horizontalAlign,s=null!==this.maxWidth?this.maxWidth:.5*t.width,o=null!==this.maxHeight?this.maxHeight:.7*t.height);for(var c=0;c<this.dataSeries.length;c++){var x=this.dataSeries[c];if("pie"!==x.type&&"doughnut"!==x.type&&"funnel"!==x.type){var u=x.legendMarkerType?x.legendMarkerType:"line"!==x.type&&"stepLine"!==x.type&&"spline"!==x.type&&"scatter"!==x.type&&"bubble"!==x.type||!x.markerType?r.default.getDefaultLegendMarker(x.type):x.markerType,m=x.legendText?x.legendText:this.itemTextFormatter?this.itemTextFormatter({chart:this.chart,legend:this._options,dataSeries:x,dataPoint:null
}):x.name,v=x.legendMarkerColor?x.legendMarkerColor:x.markerColor?x.markerColor:x._colorSet[0],g=x.markerSize||"line"!==x.type&&"stepLine"!==x.type&&"spline"!==x.type?.6*this.lineHeight:0,y=x.legendMarkerBorderColor?x.legendMarkerBorderColor:x.markerBorderColor,f=x.legendMarkerBorderThickness?x.legendMarkerBorderThickness:x.markerBorderThickness?Math.max(1,Math.round(.2*g)):0;x._colorSet[0];m=this.chart.replaceKeywordsWithValue(m,x.dataPoints[0],x,c);var b={markerType:u,markerColor:v,text:m,textBlock:null,chartType:x.type,markerSize:g,lineColor:x._colorSet[0],dataSeriesIndex:x.index,dataPointIndex:null,markerBorderColor:y,markerBorderThickness:f};l.push(b)}else for(var M=0;M<x.dataPoints.length;M++){var P=x.dataPoints[M],u=P.legendMarkerType?P.legendMarkerType:x.legendMarkerType?x.legendMarkerType:r.default.getDefaultLegendMarker(x.type),m=P.legendText?P.legendText:x.legendText?x.legendText:this.itemTextFormatter?this.itemTextFormatter({chart:this.chart,legend:this._options,dataSeries:x,dataPoint:P}):P.name?P.name:"DataPoint: "+(M+1),v=P.legendMarkerColor?P.legendMarkerColor:x.legendMarkerColor?x.legendMarkerColor:P.color?P.color:x.color?x.color:x._colorSet[M%x._colorSet.length],g=.6*this.lineHeight,y=P.legendMarkerBorderColor?P.legendMarkerBorderColor:x.legendMarkerBorderColor?x.legendMarkerBorderColor:P.markerBorderColor?P.markerBorderColor:x.markerBorderColor,f=P.legendMarkerBorderThickness?P.legendMarkerBorderThickness:x.legendMarkerBorderThickness?x.legendMarkerBorderThickness:P.markerBorderThickness||x.markerBorderThickness?Math.max(1,Math.round(.2*g)):0;m=this.chart.replaceKeywordsWithValue(m,P,x,M);var b={markerType:u,markerColor:v,text:m,textBlock:null,chartType:x.type,markerSize:g,dataSeriesIndex:c,dataPointIndex:M,markerBorderColor:y,markerBorderThickness:f};(P.showInLegend||x.showInLegend&&!1!==P.showInLegend)&&l.push(b)}b=null}if(!0===this.reversed&&l.reverse(),l.length>0){var S=null,T=0,C=0,k=0;C=null!==this.itemWidth?null!==this.itemMaxWidth?Math.min(this.itemWidth,this.itemMaxWidth,s):Math.min(this.itemWidth,s):null!==this.itemMaxWidth?Math.min(this.itemMaxWidth,s):s,g=0===g?.6*this.lineHeight:g,C-=g+.1*this.horizontalSpacing;for(var c=0;c<l.length;c++){var b=l[c];"line"!==b.chartType&&"spline"!==b.chartType&&"stepLine"!==b.chartType||(C-=.1*this.lineHeight*2),o<=0||void 0===o||C<=0||void 0===C||("horizontal"===this.orientation?(b.textBlock=new d.default(this.ctx,{x:0,y:0,maxWidth:C,maxHeight:this.itemWrap?o:this.lineHeight,angle:0,text:b.text,horizontalAlign:"left",fontSize:this.fontSize,fontFamily:this.fontFamily,fontWeight:this.fontWeight,fontColor:this.fontColor,fontStyle:this.fontStyle,textBaseline:"top"}),b.textBlock.measureText(),null!==this.itemWidth&&(b.textBlock.width=this.itemWidth-(g+.1*this.horizontalSpacing+("line"===b.chartType||"spline"===b.chartType||"stepLine"===b.chartType?.1*this.lineHeight*2:0))),(!S||S.width+Math.round(b.textBlock.width+.1*this.horizontalSpacing+g+(0===S.width?0:this.horizontalSpacing)+("line"===b.chartType||"spline"===b.chartType||"stepLine"===b.chartType?.1*this.lineHeight*2:0))>s)&&(S={items:[],width:0},h.push(S),this.height+=k,k=0),k=Math.max(k,b.textBlock.height),b.textBlock.x=S.width,b.textBlock.y=0,S.width+=Math.round(b.textBlock.width+.1*this.horizontalSpacing+g+(0===S.width?0:this.horizontalSpacing)+("line"===b.chartType||"spline"===b.chartType||"stepLine"===b.chartType?.1*this.lineHeight*2:0)),S.items.push(b),this.width=Math.max(S.width,this.width)):(b.textBlock=new d.default(this.ctx,{x:0,y:0,maxWidth:C,maxHeight:!0===this.itemWrap?o:1.5*this.fontSize,angle:0,text:b.text,horizontalAlign:"left",fontSize:this.fontSize,fontFamily:this.fontFamily,fontWeight:this.fontWeight,fontColor:this.fontColor,fontStyle:this.fontStyle,textBaseline:"top"}),b.textBlock.measureText(),null!==this.itemWidth&&(b.textBlock.width=this.itemWidth-(g+.1*this.horizontalSpacing+("line"===b.chartType||"spline"===b.chartType||"stepLine"===b.chartType?.1*this.lineHeight*2:0))),this.height<=o?(S={items:[],width:0},h.push(S)):(S=h[T],T=(T+1)%h.length),this.height+=b.textBlock.height,b.textBlock.x=S.width,b.textBlock.y=0,S.width+=Math.round(b.textBlock.width+.1*this.horizontalSpacing+g+(0===S.width?0:this.horizontalSpacing)+("line"===b.chartType||"spline"===b.chartType||"stepLine"===b.chartType?.1*this.lineHeight*2:0)),S.items.push(b),this.width=Math.max(S.width,this.width)))}!1===this.itemWrap?this.height=h.length*this.lineHeight:this.height+=k,this.height=Math.min(o,this.height),this.width=Math.min(s,this.width)}"top"===this.verticalAlign?(n="left"===this.horizontalAlign?t.x1:"right"===this.horizontalAlign?t.x2-this.width:t.x1+t.width/2-this.width/2,a=t.y1):"center"===this.verticalAlign?(n="left"===this.horizontalAlign?t.x1:"right"===this.horizontalAlign?t.x2-this.width:t.x1+t.width/2-this.width/2,a=t.y1+t.height/2-this.height/2):"bottom"===this.verticalAlign&&(n="left"===this.horizontalAlign?t.x1:"right"===this.horizontalAlign?t.x2-this.width:t.x1+t.width/2-this.width/2,a=t.y2-this.height),this.items=l;for(var c=0;c<this.items.length;c++){var b=l[c];b.id=++this.chart._eventManager.lastObjectId,this.chart._eventManager.objectMap[b.id]={id:b.id,objectType:"legendItem",legendItemIndex:c,dataSeriesIndex:b.dataSeriesIndex,dataPointIndex:b.dataPointIndex}}for(var w=0,c=0;c<h.length;c++){for(var S=h[c],k=0,_=0;_<S.items.length;_++){var b=S.items[_],A=b.textBlock.x+n+(0===_?.2*g:this.horizontalSpacing),L=a+w,I=A;this.chart.data[b.dataSeriesIndex].visible||(this.ctx.globalAlpha=.5),this.ctx.save(),this.ctx.rect(n,a,s,o),this.ctx.clip(),"line"!==b.chartType&&"stepLine"!==b.chartType&&"spline"!==b.chartType||(this.ctx.strokeStyle=b.lineColor,this.ctx.lineWidth=Math.ceil(this.lineHeight/8),this.ctx.beginPath(),this.ctx.moveTo(A-.1*this.lineHeight,L+this.lineHeight/2),this.ctx.lineTo(A+.7*this.lineHeight,L+this.lineHeight/2),this.ctx.stroke(),I-=.1*this.lineHeight),p.default.drawMarker(A+g/2,L+this.lineHeight/2,this.ctx,b.markerType,b.markerSize,b.markerColor,b.markerBorderColor,b.markerBorderThickness),b.textBlock.x=A+.1*this.horizontalSpacing+g,"line"!==b.chartType&&"stepLine"!==b.chartType&&"spline"!==b.chartType||(b.textBlock.x=b.textBlock.x+.1*this.lineHeight),b.textBlock.y=L,b.textBlock.render(!0),this.ctx.restore(),k=_>0?Math.max(k,b.textBlock.height):b.textBlock.height,this.chart.data[b.dataSeriesIndex].visible||(this.ctx.globalAlpha=1);var B=intToHexColorString(b.id);this.ghostCtx.fillStyle=B,this.ghostCtx.beginPath(),this.ghostCtx.fillRect(I,b.textBlock.y,b.textBlock.x+b.textBlock.width-I,b.textBlock.height),b.x1=this.chart._eventManager.objectMap[b.id].x1=I,b.y1=this.chart._eventManager.objectMap[b.id].y1=b.textBlock.y,b.x2=this.chart._eventManager.objectMap[b.id].x2=b.textBlock.x+b.textBlock.width,b.y2=this.chart._eventManager.objectMap[b.id].y2=b.textBlock.y+b.textBlock.height}w+=k}e.layoutManager.registerSpace(i,{width:this.width+2+2,height:this.height+5+5}),this.bounds={x1:n,y1:a,x2:n+this.width,y2:a+this.height}},i.default=n},{"../helpers/render":38,"../helpers/utils":39,"./canvasjs":27,"./data_series":30,"./text_block":34}],34:[function(e,t,i){"use strict";function a(e,t){a.base.constructor.call(this,"TextBlock",t),this.ctx=e,this._isDirty=!0,this._wrappedText=null,this._lineHeight=(0,r.getFontHeightInPixels)(this.fontFamily,this.fontSize,this.fontWeight)}Object.defineProperty(i,"__esModule",{value:!0});var n=e("./canvasjs"),s=function(e){return e&&e.__esModule?e:{default:e}}(n),r=e("../helpers/utils");(0,r.extend)(a,s.default),a.prototype.render=function(e){e&&this.ctx.save();var t=this.ctx.font;this.ctx.textBaseline=this.textBaseline;var i=0;this._isDirty&&this.measureText(this.ctx),this.ctx.translate(this.x,this.y+i),"middle"===this.textBaseline&&(i=-this._lineHeight/2),this.ctx.font=this._getFontString(),this.ctx.rotate(Math.PI/180*this.angle);var a=0,n=this.padding,s=null;(this.borderThickness>0&&this.borderColor||this.backgroundColor)&&this.ctx.roundRect(0,i,this.width,this.height,this.cornerRadius,this.borderThickness,this.backgroundColor,this.borderColor),this.ctx.fillStyle=this.fontColor;for(var r=0;r<this._wrappedText.lines.length;r++)s=this._wrappedText.lines[r],"right"===this.horizontalAlign?a=this.width-s.width-this.padding:"left"===this.horizontalAlign?a=this.padding:"center"===this.horizontalAlign&&(a=(this.width-2*this.padding)/2-s.width/2+this.padding),this.ctx.fillText(s.text,a,n),n+=s.height;this.ctx.font=t,e&&this.ctx.restore()},a.prototype.setText=function(e){this.text=e,this._isDirty=!0,this._wrappedText=null},a.prototype.measureText=function(){if(null===this.maxWidth)throw"Please set maxWidth and height for TextBlock";return this._wrapText(this.ctx),this._isDirty=!1,{width:this.width,height:this.height}},a.prototype._getLineWithWidth=function(e,t,i){if(e=String(e),i=i||!1,!e)return{text:"",width:0};var a=0,n=0,s=e.length-1,r=1/0;for(this.ctx.font=this._getFontString();n<=s;){r=Math.floor((n+s)/2);var o=e.substr(0,r+1);if((a=this.ctx.measureText(o).width)<t)n=r+1;else{if(!(a>t))break;s=r-1}}a>t&&o.length>1&&(o=o.substr(0,o.length-1),a=this.ctx.measureText(o).width);var l=!0;if(o.length!==e.length&&" "!==e[o.length]||(l=!1),l){var h=o.split(" ");h.length>1&&h.pop(),o=h.join(" "),a=this.ctx.measureText(o).width}return{text:o,width:a}},a.prototype._wrapText=function(){var e=new String((0,r.trimString)(String(this.text))),t=[],i=this.ctx.font,a=0,n=0;for(this.ctx.font=this._getFontString();e.length>0;){var s=this.maxWidth-2*this.padding,o=this.maxHeight-2*this.padding,l=this._getLineWithWidth(e,s,!1);if(l.height=this._lineHeight,t.push(l),n=Math.max(n,l.width),a+=l.height,e=(0,r.trimString)(e.slice(l.text.length,e.length)),o&&a>o){var l=t.pop();a-=l.height}}this._wrappedText={lines:t,width:n,height:a},this.width=n+2*this.padding,this.height=a+2*this.padding,this.ctx.font=i},a.prototype._getFontString=function(){return(0,r.getFontString)("",this,null)},i.default=a},{"../helpers/utils":39,"./canvasjs":27}],35:[function(e,t,i){"use strict";function a(e){return e&&e.__esModule?e:{default:e}}function n(e,t){n.base.constructor.call(this,"Title",t,e.theme),this.chart=e,this.canvas=e.canvas,this.ctx=this.chart.ctx,void 0===this._options.fontSize&&(this.fontSize=this.chart.getAutoFontSize(this.fontSize)),this.width=null,this.height=null,this.bounds={x1:null,y1:null,x2:null,y2:null}}Object.defineProperty(i,"__esModule",{value:!0});var s=e("./canvasjs"),r=a(s),o=e("./text_block"),l=a(o);(0,e("../helpers/utils").extend)(n,r.default),n.prototype.render=function(){if(this.text){var e,t,i=this.dockInsidePlotArea?this.chart.plotArea:this.chart,a=i.layoutManager.getFreeSpace(),n=a.x1,s=a.y1,r=0,o=0,h=this.chart._menuButton&&this.chart.exportEnabled&&"top"===this.verticalAlign?22:0;"top"===this.verticalAlign||"bottom"===this.verticalAlign?(null===this.maxWidth&&(this.maxWidth=a.width-4-h*("center"===this.horizontalAlign?2:1)),o=.5*a.height-this.margin-2,r=0):"center"===this.verticalAlign&&("left"===this.horizontalAlign||"right"===this.horizontalAlign?(null===this.maxWidth&&(this.maxWidth=a.height-4),o=.5*a.width-this.margin-2):"center"===this.horizontalAlign&&(null===this.maxWidth&&(this.maxWidth=a.width-4),o=.5*a.height-4)),this.wrap||(o=Math.min(o,Math.max(1.5*this.fontSize,this.fontSize+2.5*this.padding)));var d=new l.default(this.ctx,{fontSize:this.fontSize,fontFamily:this.fontFamily,fontColor:this.fontColor,fontStyle:this.fontStyle,fontWeight:this.fontWeight,horizontalAlign:this.horizontalAlign,verticalAlign:this.verticalAlign,borderColor:this.borderColor,borderThickness:this.borderThickness,backgroundColor:this.backgroundColor,maxWidth:this.maxWidth,maxHeight:o,cornerRadius:this.cornerRadius,text:this.text,padding:this.padding,textBaseline:"top"}),c=d.measureText();"top"===this.verticalAlign||"bottom"===this.verticalAlign?("top"===this.verticalAlign?(s=a.y1+2,t="top"):"bottom"===this.verticalAlign&&(s=a.y2-2-c.height,t="bottom"),"left"===this.horizontalAlign?n=a.x1+2:"center"===this.horizontalAlign?n=a.x1+a.width/2-c.width/2:"right"===this.horizontalAlign&&(n=a.x2-2-c.width-h),e=this.horizontalAlign,this.width=c.width,this.height=c.height):"center"===this.verticalAlign&&("left"===this.horizontalAlign?(n=a.x1+2,s=a.y2-2-(this.maxWidth/2-c.width/2),r=-90,t="left",this.width=c.height,this.height=c.width):"right"===this.horizontalAlign?(n=a.x2-2,s=a.y1+2+(this.maxWidth/2-c.width/2),r=90,t="right",this.width=c.height,this.height=c.width):"center"===this.horizontalAlign&&(s=i.y1+(i.height/2-c.height/2),n=i.x1+(i.width/2-c.width/2),t="center",this.width=c.width,this.height=c.height),e="center"),d.x=n,d.y=s,d.angle=r,d.horizontalAlign=e,d.render(!0),i.layoutManager.registerSpace(t,{width:this.width+("left"===t||"right"===t?this.margin+2:0),height:this.height+("top"===t||"bottom"===t?this.margin+2:0)}),this.bounds={x1:n,y1:s,x2:n+this.width,y2:s+this.height},this.ctx.textBaseline="top"}},i.default=n},{"../helpers/utils":39,"./canvasjs":27,"./text_block":34}],36:[function(e,t,i){"use strict";function a(e){return e&&e.__esModule?e:{default:e}}function n(e,t,i){n.base.constructor.call(this,"ToolTip",t,i),this.chart=e,this.canvas=e.canvas,this.ctx=this.chart.ctx,this.currentSeriesIndex=-1,this.currentDataPointIndex=-1,this._timerId=0,this._prevX=NaN,this._prevY=NaN,this._initialize()}Object.defineProperty(i,"__esModule",{value:!0});var s=e("../helpers/animator"),r=(a(s),e("../helpers/render")),o=a(r),l=e("./canvasjs"),h=a(l),d=e("../helpers/utils");(0,d.extend)(n,h.default),n.prototype._initialize=function(){if(this.enabled){this.container=document.createElement("div"),this.container.setAttribute("class","canvasjs-chart-tooltip"),this.container.style.position="absolute",this.container.style.height="auto",this.container.style.boxShadow="1px 1px 2px 2px rgba(0,0,0,0.1)",this.container.style.zIndex="1000",this.container.style.display="none";var e='<div style=" width: auto;';e+="height: auto;",e+="min-width: 50px;",e+="line-height: auto;",e+="margin: 0px 0px 0px 0px;",e+="padding: 5px;",e+="font-family: Calibri, Arial, Georgia, serif;",e+="font-weight: normal;",e+="font-style: "+(d.isCanvasSupported?"italic;":"normal;"),e+="font-size: 14px;",e+="color: #000000;",e+="text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.1);",e+="text-align: left;",e+="border: 2px solid gray;",e+=d.isCanvasSupported?"background: rgba(255,255,255,.9);":"background: rgb(255,255,255);",e+="text-indent: 0px;",e+="white-space: nowrap;",e+="border-radius: 5px;",e+="-moz-user-select:none;",e+="-khtml-user-select: none;",e+="-webkit-user-select: none;",e+="-ms-user-select: none;",e+="user-select: none;",d.isCanvasSupported||(e+="filter: alpha(opacity = 90);",e+="filter: progid:DXImageTransform.Microsoft.Shadow(Strength=3, Direction=135, Color='#666666');"),e+='} "> Sample Tooltip</div>',this.container.innerHTML=e,this.contentDiv=this.container.firstChild,this.container.style.borderRadius=this.contentDiv.style.borderRadius,this.chart._canvasJSContainer.appendChild(this.container)}},n.prototype.mouseMoveHandler=function(e,t){this._lastUpdated&&(new Date).getTime()-this._lastUpdated<40||(this._lastUpdated=(new Date).getTime(),this._updateToolTip(e,t))},n.prototype._updateToolTip=function(e,t){if(!this.chart.disableToolTip){if(void 0===e||void 0===t){if(isNaN(this._prevX)||isNaN(this._prevY))return;e=this._prevX,t=this._prevY}else this._prevX=e,this._prevY=t;var i,a=null,n=null,s=[],r=0;if(this.shared&&this.enabled&&"none"!==this.chart.plotInfo.axisPlacement){r="xySwapped"===this.chart.plotInfo.axisPlacement?(this.chart.axisX.viewportMaximum-this.chart.axisX.viewportMinimum)/this.chart.axisX.lineCoordinates.height*(this.chart.axisX.lineCoordinates.y2-t)+this.chart.axisX.viewportMinimum:(this.chart.axisX.viewportMaximum-this.chart.axisX.viewportMinimum)/this.chart.axisX.lineCoordinates.width*(e-this.chart.axisX.lineCoordinates.x1)+this.chart.axisX.viewportMinimum;for(var o=[],l=0;l<this.chart.data.length;l++){var h=this.chart.data[l].getDataPointAtX(r,!0);h&&h.index>=0&&(h.dataSeries=this.chart.data[l],null!==h.dataPoint.y&&o.push(h))}if(0===o.length)return;o.sort(function(e,t){return e.distance-t.distance});var c=o[0];for(l=0;l<o.length;l++)o[l].dataPoint.x.valueOf()===c.dataPoint.x.valueOf()&&s.push(o[l]);o=null}else{var p=this.chart.getDataPointAtXY(e,t,!0);if(p)this.currentDataPointIndex=p.dataPointIndex,this.currentSeriesIndex=p.dataSeries.index;else if(d.isCanvasSupported){var x=(0,d.getObjectId)(e,t,this.chart._eventManager.ghostCtx);if(x>0&&void 0!==this.chart._eventManager.objectMap[x]){var u=this.chart._eventManager.objectMap[x];if("legendItem"===u.objectType)return;this.currentSeriesIndex=u.dataSeriesIndex,this.currentDataPointIndex=u.dataPointIndex>=0?u.dataPointIndex:-1}else this.currentDataPointIndex=-1}else this.currentDataPointIndex=-1;if(this.currentSeriesIndex>=0){n=this.chart.data[this.currentSeriesIndex];var h={};if(this.currentDataPointIndex>=0)a=n.dataPoints[this.currentDataPointIndex],h.dataSeries=n,h.dataPoint=a,h.index=this.currentDataPointIndex,h.distance=Math.abs(a.x-r);else{if(!this.enabled||"line"!==n.type&&"stepLine"!==n.type&&"spline"!==n.type&&"area"!==n.type&&"stepArea"!==n.type&&"splineArea"!==n.type&&"stackedArea"!==n.type&&"stackedArea100"!==n.type&&"rangeArea"!==n.type&&"rangeSplineArea"!==n.type&&"candlestick"!==n.type&&"ohlc"!==n.type)return;var r=n.axisX.conversionParameters.minimum+(e-n.axisX.conversionParameters.reference)/n.axisX.conversionParameters.pixelPerUnit;h=n.getDataPointAtX(r,!0),h.dataSeries=n,this.currentDataPointIndex=h.index,a=h.dataPoint}if(null!==h.dataPoint.y)if(h.dataSeries.axisY)if(h.dataPoint.y.length>0){for(var m=0,l=0;l<h.dataPoint.y.length;l++)h.dataPoint.y[l]<h.dataSeries.axisY.viewportMinimum?m--:h.dataPoint.y[l]>h.dataSeries.axisY.viewportMaximum&&m++;m<h.dataPoint.y.length&&m>-h.dataPoint.y.length&&s.push(h)}else h.dataPoint.y>=h.dataSeries.axisY.viewportMinimum&&h.dataPoint.y<=h.dataSeries.axisY.viewportMaximum&&s.push(h);else s.push(h)}}if(s.length>0&&(this.highlightObjects(s),this.enabled)){var v="";if(null!==(v=this.getToolTipInnerHTML({entries:s}))){this.contentDiv.innerHTML=v,this.contentDiv.innerHTML=v;var g=!1;"none"===this.container.style.display&&(g=!0,this.container.style.display="block");try{this.contentDiv.style.background=this.backgroundColor?this.backgroundColor:d.isCanvasSupported?"rgba(255,255,255,.9)":"rgb(255,255,255)",this.contentDiv.style.borderRightColor=this.contentDiv.style.borderLeftColor=this.contentDiv.style.borderColor=this.borderColor?this.borderColor:s[0].dataPoint.color?s[0].dataPoint.color:s[0].dataSeries.color?s[0].dataSeries.color:s[0].dataSeries._colorSet[s[0].index%s[0].dataSeries._colorSet.length],this.contentDiv.style.borderWidth=this.borderThickness||0===this.borderThickness?this.borderThickness+"px":"2px",this.contentDiv.style.borderRadius=this.cornerRadius||0===this.cornerRadius?this.cornerRadius+"px":"5px",this.container.style.borderRadius=this.contentDiv.style.borderRadius,this.contentDiv.style.fontSize=this.fontSize||0===this.fontSize?this.fontSize+"px":"14px",this.contentDiv.style.color=this.fontColor?this.fontColor:"#000000",this.contentDiv.style.fontFamily=this.fontFamily?this.fontFamily:"Calibri, Arial, Georgia, serif;",this.contentDiv.style.fontWeight=this.fontWeight?this.fontWeight:"normal",this.contentDiv.style.fontStyle=this.fontStyle?this.fontStyle:d.isCanvasSupported?"italic":"normal"}catch(e){}var y;"pie"===s[0].dataSeries.type||"doughnut"===s[0].dataSeries.type||"funnel"===s[0].dataSeries.type||"bar"===s[0].dataSeries.type||"rangeBar"===s[0].dataSeries.type||"stackedBar"===s[0].dataSeries.type||"stackedBar100"===s[0].dataSeries.type?y=e-10-this.container.clientWidth:(y=s[0].dataSeries.axisX.conversionParameters.reference+s[0].dataSeries.axisX.conversionParameters.pixelPerUnit*(s[0].dataPoint.x-s[0].dataSeries.axisX.conversionParameters.minimum)-this.container.clientWidth<<0,y-=10),y<0&&(y+=this.container.clientWidth+20),y+this.container.clientWidth>this.chart._container.clientWidth&&(y=Math.max(0,this.chart._container.clientWidth-this.container.clientWidth)),y+="px",i=1!==s.length||this.shared||"line"!==s[0].dataSeries.type&&"stepLine"!==s[0].dataSeries.type&&"spline"!==s[0].dataSeries.type&&"area"!==s[0].dataSeries.type&&"stepArea"!==s[0].dataSeries.type&&"splineArea"!==s[0].dataSeries.type&&"stackedArea"!==s[0].dataSeries.type&&"stackedArea100"!==s[0].dataSeries.type?"bar"===s[0].dataSeries.type||"rangeBar"===s[0].dataSeries.type||"stackedBar"===s[0].dataSeries.type||"stackedBar100"===s[0].dataSeries.type?s[0].dataSeries.axisX.conversionParameters.reference+s[0].dataSeries.axisX.conversionParameters.pixelPerUnit*(s[0].dataPoint.x-s[0].dataSeries.axisX.viewportMinimum)+.5<<0:t:s[0].dataSeries.axisY.conversionParameters.reference+s[0].dataSeries.axisY.conversionParameters.pixelPerUnit*(s[0].dataPoint.y-s[0].dataSeries.axisY.viewportMinimum)+.5<<0,i=10-i,i+this.container.clientHeight+5>0&&(i-=i+this.container.clientHeight+5-0),i+="px",this.container.style.left=y,this.container.style.bottom=i,!this.animationEnabled||g?this.disableAnimation():this.enableAnimation()}else this.hide(!1)}}},n.prototype.highlightObjects=function(e){var t=this.chart.overlaidCanvasCtx;this.chart.resetOverlayedCanvas(),t.clearRect(0,0,this.chart.width,this.chart.height),t.save();var i=this.chart.plotArea,a=0;t.rect(i.x1,i.y1,i.x2-i.x1,i.y2-i.y1),t.clip();for(var n=0;n<e.length;n++){var s=e[n],r=this.chart._eventManager.objectMap[s.dataSeries.dataPointIds[s.index]];if(r&&r.objectType&&"dataPoint"===r.objectType){var l=this.chart.data[r.dataSeriesIndex],h=l.dataPoints[r.dataPointIndex],c=r.dataPointIndex;if(!1!==h.highlightEnabled&&(!0===l.highlightEnabled||!0===h.highlightEnabled))if("line"===l.type||"stepLine"===l.type||"spline"===l.type||"scatter"===l.type||"area"===l.type||"stepArea"===l.type||"splineArea"===l.type||"stackedArea"===l.type||"stackedArea100"===l.type||"rangeArea"===l.type||"rangeSplineArea"===l.type){var p=l.getMarkerProperties(c,r.x1,r.y1,this.chart.overlaidCanvasCtx);if(p.size=Math.max(1.5*p.size<<0,10),p.borderColor=p.borderColor||"#FFFFFF",p.borderThickness=p.borderThickness||Math.ceil(.1*p.size),o.default.drawMarkers([p]),void 0!==r.y2){var p=l.getMarkerProperties(c,r.x1,r.y2,this.chart.overlaidCanvasCtx);p.size=Math.max(1.5*p.size<<0,10),p.borderColor=p.borderColor||"#FFFFFF",p.borderThickness=p.borderThickness||Math.ceil(.1*p.size),o.default.drawMarkers([p])}}else if("bubble"===l.type){var p=l.getMarkerProperties(c,r.x1,r.y1,this.chart.overlaidCanvasCtx);p.size=r.size,p.color="white",p.borderColor="white",t.globalAlpha=.3,o.default.drawMarkers([p]),t.globalAlpha=1}else"column"===l.type||"stackedColumn"===l.type||"stackedColumn100"===l.type||"bar"===l.type||"rangeBar"===l.type||"stackedBar"===l.type||"stackedBar100"===l.type||"rangeColumn"===l.type?(0,d.drawRect)(t,r.x1,r.y1,r.x2,r.y2,"white",0,null,!1,!1,!1,!1,.3):"pie"===l.type||"doughnut"===l.type?(0,d.drawSegment)(t,r.center,r.radius,"white",l.type,r.startAngle,r.endAngle,.3,r.percentInnerRadius):"candlestick"===l.type?(t.globalAlpha=1,t.strokeStyle=r.color,t.lineWidth=2*r.borderThickness,a=t.lineWidth%2==0?0:.5,t.beginPath(),t.moveTo(r.x3-a,r.y2),t.lineTo(r.x3-a,Math.min(r.y1,r.y4)),t.stroke(),t.beginPath(),t.moveTo(r.x3-a,Math.max(r.y1,r.y4)),t.lineTo(r.x3-a,r.y3),t.stroke(),(0,d.drawRect)(t,r.x1,Math.min(r.y1,r.y4),r.x2,Math.max(r.y1,r.y4),"transparent",2*r.borderThickness,r.color,!1,!1,!1,!1),t.globalAlpha=1):"ohlc"===l.type&&(t.globalAlpha=1,t.strokeStyle=r.color,t.lineWidth=2*r.borderThickness,a=t.lineWidth%2==0?0:.5,t.beginPath(),t.moveTo(r.x3-a,r.y2),t.lineTo(r.x3-a,r.y3),t.stroke(),t.beginPath(),t.moveTo(r.x3,r.y1),t.lineTo(r.x1,r.y1),t.stroke(),t.beginPath(),t.moveTo(r.x3,r.y4),t.lineTo(r.x2,r.y4),t.stroke(),t.globalAlpha=1)}}t.restore(),t.globalAlpha=1,t.beginPath()},n.prototype.getToolTipInnerHTML=function(e){for(var t=e.entries,i=null,a=null,n=null,s=0,r="",o=!0,l=0;l<t.length;l++)if(t[l].dataSeries.toolTipContent||t[l].dataPoint.toolTipContent){o=!1;break}if(o&&(this.content&&"function"==typeof this.content||this.contentFormatter)){var h={chart:this.chart,toolTip:this._options,entries:t};i=this.contentFormatter?this.contentFormatter(h):this.content(h)}else if(this.shared&&"none"!==this.chart.plotInfo.axisPlacement){for(var d="",l=0;l<t.length;l++)a=t[l].dataSeries,n=t[l].dataPoint,s=t[l].index,r="",0===l&&o&&!this.content&&(d+=void 0!==this.chart.axisX.labels[n.x]?this.chart.axisX.labels[n.x]:"{x}",d+="</br>",d=this.chart.replaceKeywordsWithValue(d,n,a,s)),null===n.toolTipContent||void 0===n.toolTipContent&&null===a._options.toolTipContent||("line"===a.type||"stepLine"===a.type||"spline"===a.type||"area"===a.type||"stepArea"===a.type||"splineArea"===a.type||"column"===a.type||"bar"===a.type||"scatter"===a.type||"stackedColumn"===a.type||"stackedColumn100"===a.type||"stackedBar"===a.type||"stackedBar100"===a.type||"stackedArea"===a.type||"stackedArea100"===a.type?r+=n.toolTipContent?n.toolTipContent:a.toolTipContent?a.toolTipContent:this.content&&"function"!=typeof this.content?this.content:"<span style='\"'color:{color};'\"'>{name}:</span>&nbsp;&nbsp;{y}":"bubble"===a.type?r+=n.toolTipContent?n.toolTipContent:a.toolTipContent?a.toolTipContent:this.content&&"function"!=typeof this.content?this.content:"<span style='\"'color:{color};'\"'>{name}:</span>&nbsp;&nbsp;{y}, &nbsp;&nbsp;{z}":"pie"===a.type||"doughnut"===a.type||"funnel"===a.type?r+=n.toolTipContent?n.toolTipContent:a.toolTipContent?a.toolTipContent:this.content&&"function"!=typeof this.content?this.content:"&nbsp;&nbsp;{y}":"rangeColumn"===a.type||"rangeBar"===a.type||"rangeArea"===a.type||"rangeSplineArea"===a.type?r+=n.toolTipContent?n.toolTipContent:a.toolTipContent?a.toolTipContent:this.content&&"function"!=typeof this.content?this.content:"<span style='\"'color:{color};'\"'>{name}:</span>&nbsp;&nbsp;{y[0]},&nbsp;{y[1]}":"candlestick"!==a.type&&"ohlc"!==a.type||(r+=n.toolTipContent?n.toolTipContent:a.toolTipContent?a.toolTipContent:this.content&&"function"!=typeof this.content?this.content:"<span style='\"'color:{color};'\"'>{name}:</span><br/>Open: &nbsp;&nbsp;{y[0]}<br/>High: &nbsp;&nbsp;&nbsp;{y[1]}<br/>Low:&nbsp;&nbsp;&nbsp;{y[2]}<br/>Close: &nbsp;&nbsp;{y[3]}"),null===i&&(i=""),!0===this.reversed?(i=this.chart.replaceKeywordsWithValue(r,n,a,s)+i,l<t.length-1&&(i="</br>"+i)):(i+=this.chart.replaceKeywordsWithValue(r,n,a,s),l<t.length-1&&(i+="</br>")));null!==i&&(i=d+i)}else{if(a=t[0].dataSeries,n=t[0].dataPoint,s=t[0].index,null===n.toolTipContent||void 0===n.toolTipContent&&null===a._options.toolTipContent)return null;"line"===a.type||"stepLine"===a.type||"spline"===a.type||"area"===a.type||"stepArea"===a.type||"splineArea"===a.type||"column"===a.type||"bar"===a.type||"scatter"===a.type||"stackedColumn"===a.type||"stackedColumn100"===a.type||"stackedBar"===a.type||"stackedBar100"===a.type||"stackedArea"===a.type||"stackedArea100"===a.type?r=n.toolTipContent?n.toolTipContent:a.toolTipContent?a.toolTipContent:this.content&&"function"!=typeof this.content?this.content:"<span style='\"'color:{color};'\"'>"+(n.label?"{label}":"{x}")+" :</span>&nbsp;&nbsp;{y}":"bubble"===a.type?r=n.toolTipContent?n.toolTipContent:a.toolTipContent?a.toolTipContent:this.content&&"function"!=typeof this.content?this.content:"<span style='\"'color:{color};'\"'>"+(n.label?"{label}":"{x}")+":</span>&nbsp;&nbsp;{y}, &nbsp;&nbsp;{z}":"pie"===a.type||"doughnut"===a.type||"funnel"===a.type?r=n.toolTipContent?n.toolTipContent:a.toolTipContent?a.toolTipContent:this.content&&"function"!=typeof this.content?this.content:(n.name?"{name}:&nbsp;&nbsp;":n.label?"{label}:&nbsp;&nbsp;":"")+"{y}":"rangeColumn"===a.type||"rangeBar"===a.type||"rangeArea"===a.type||"rangeSplineArea"===a.type?r=n.toolTipContent?n.toolTipContent:a.toolTipContent?a.toolTipContent:this.content&&"function"!=typeof this.content?this.content:"<span style='\"'color:{color};'\"'>"+(n.label?"{label}":"{x}")+" :</span>&nbsp;&nbsp;{y[0]}, &nbsp;{y[1]}":"candlestick"!==a.type&&"ohlc"!==a.type||(r=n.toolTipContent?n.toolTipContent:a.toolTipContent?a.toolTipContent:this.content&&"function"!=typeof this.content?this.content:"<span style='\"'color:{color};'\"'>"+(n.label?"{label}":"{x}")+"</span><br/>Open: &nbsp;&nbsp;{y[0]}<br/>High: &nbsp;&nbsp;&nbsp;{y[1]}<br/>Low: &nbsp;&nbsp;&nbsp;&nbsp;{y[2]}<br/>Close: &nbsp;&nbsp;{y[3]}"),null===i&&(i=""),i+=this.chart.replaceKeywordsWithValue(r,n,a,s)}return i},n.prototype.enableAnimation=function(){this.container.style.WebkitTransition||(this.container.style.WebkitTransition="left .2s ease-out, bottom .2s ease-out",this.container.style.MozTransition="left .2s ease-out, bottom .2s ease-out",this.container.style.MsTransition="left .2s ease-out, bottom .2s ease-out",this.container.style.transition="left .2s ease-out, bottom .2s ease-out")},n.prototype.disableAnimation=function(){this.container.style.WebkitTransition&&(this.container.style.WebkitTransition="",this.container.style.MozTransition="",this.container.style.MsTransition="",this.container.style.transition="")},n.prototype.hide=function(e){this.enabled&&(e=void 0===e||e,this.container.style.display="none",this.currentSeriesIndex=-1,this._prevX=NaN,this._prevY=NaN,e&&this.chart.resetOverlayedCanvas())},i.default=n},{"../helpers/animator":37,"../helpers/render":38,"../helpers/utils":39,"./canvasjs":27}],37:[function(e,t,i){"use strict";function a(e,t){if(0!==e){var i=t.dest,a=t.source.canvas,n=t.animationBase,s=n-n*e;i.drawImage(a,0,0,a.width,a.height,0,s,i.canvas.width/devicePixelBackingStoreRatio,e*i.canvas.height/devicePixelBackingStoreRatio)}}function n(e,t){if(0!==e){var i=t.dest,a=t.source.canvas,n=t.animationBase,s=n-n*e;i.drawImage(a,0,0,a.width,a.height,s,0,e*i.canvas.width/devicePixelBackingStoreRatio,i.canvas.height/devicePixelBackingStoreRatio)}}function s(e,t){if(0!==e){var i=t.dest,a=t.source.canvas;i.save(),e>0&&i.drawImage(a,0,0,a.width*e,a.height,0,0,a.width*e/devicePixelBackingStoreRatio,a.height/devicePixelBackingStoreRatio),i.restore()}}function r(e,t){if(0!==e){var i=t.dest,a=t.source.canvas;i.save(),i.globalAlpha=e,i.drawImage(a,0,0,a.width,a.height,0,0,i.canvas.width/devicePixelBackingStoreRatio,i.canvas.height/devicePixelBackingStoreRatio),i.restore()}}Object.defineProperty(i,"__esModule",{value:!0});var o={linear:function(e,t,i,a){return i*e/a+t},easeOutQuad:function(e,t,i,a){return-i*(e/=a)*(e-2)+t},easeOutQuart:function(e,t,i,a){return-i*((e=e/a-1)*e*e*e-1)+t},easeInQuad:function(e,t,i,a){return i*(e/=a)*e+t},easeInQuart:function(e,t,i,a){return i*(e/=a)*e*e*e+t}};i.default={yScaleAnimation:a,xScaleAnimation:n,xClipAnimation:s,fadeInAnimation:r,easing:o}},{}],38:[function(e,t,i){"use strict";Object.defineProperty(i,"__esModule",{value:!0});var a={drawMarker:function(e,t,i,a,n,s,r,o){if(i){var l=1;i.fillStyle=s||"#000000",i.strokeStyle=r||"#000000",i.lineWidth=o||0,"circle"===a?(i.moveTo(e,t),i.beginPath(),i.arc(e,t,n/2,0,2*Math.PI,!1),s&&i.fill(),o&&(r?i.stroke():(l=i.globalAlpha,i.globalAlpha=.15,i.strokeStyle="black",i.stroke(),i.globalAlpha=l))):"square"===a?(i.beginPath(),i.rect(e-n/2,t-n/2,n,n),s&&i.fill(),o&&(r?i.stroke():(l=i.globalAlpha,i.globalAlpha=.15,i.strokeStyle="black",i.stroke(),i.globalAlpha=l))):"triangle"===a?(i.beginPath(),i.moveTo(e-n/2,t+n/2),i.lineTo(e+n/2,t+n/2),i.lineTo(e,t-n/2),i.closePath(),s&&i.fill(),o&&(r?i.stroke():(l=i.globalAlpha,i.globalAlpha=.15,i.strokeStyle="black",i.stroke(),i.globalAlpha=l)),i.beginPath()):"cross"===a&&(i.strokeStyle=s,o=n/4,i.lineWidth=o,i.beginPath(),i.moveTo(e-n/2,t-n/2),i.lineTo(e+n/2,t+n/2),i.stroke(),i.moveTo(e+n/2,t-n/2),i.lineTo(e-n/2,t+n/2),i.stroke())}},drawMarkers:function(e){for(var t=0;t<e.length;t++){var i=e[t];a.drawMarker(i.x,i.y,i.ctx,i.type,i.size,i.color,i.borderColor,i.borderThickness)}}};i.default=a},{}],39:[function(e,t,i){"use strict";function a(e,t){e.prototype=n(t.prototype),e.prototype.constructor=e,e.base=t.prototype}function n(e){function t(){}return t.prototype=e,new t}function s(e,t,i){
return"millisecond"===i?e.setMilliseconds(e.getMilliseconds()+1*t):"second"===i?e.setSeconds(e.getSeconds()+1*t):"minute"===i?e.setMinutes(e.getMinutes()+1*t):"hour"===i?e.setHours(e.getHours()+1*t):"day"===i?e.setDate(e.getDate()+1*t):"week"===i?e.setDate(e.getDate()+7*t):"month"===i?e.setMonth(e.getMonth()+1*t):"year"===i&&e.setFullYear(e.getFullYear()+1*t),e}function r(e,t){return constants[t+"Duration"]*e}function o(e,t){var i=!1;for(e<0&&(i=!0,e*=-1),e=""+e,t=t||1;e.length<t;)e="0"+e;return i?"-"+e:e}function l(e){if(!e)return e;e=e.replace(/^\s\s*/,"");for(var t=/\s/,i=e.length;t.test(e.charAt(--i)););return e.slice(0,i+1)}function h(e){e.roundRect=function(e,t,i,a,n,s,r,o){r&&(this.fillStyle=r),o&&(this.strokeStyle=o),void 0===n&&(n=5),this.lineWidth=s,this.beginPath(),this.moveTo(e+n,t),this.lineTo(e+i-n,t),this.quadraticCurveTo(e+i,t,e+i,t+n),this.lineTo(e+i,t+a-n),this.quadraticCurveTo(e+i,t+a,e+i-n,t+a),this.lineTo(e+n,t+a),this.quadraticCurveTo(e,t+a,e,t+a-n),this.lineTo(e,t+n),this.quadraticCurveTo(e,t,e+n,t),this.closePath(),r&&this.fill(),o&&s>0&&this.stroke()}}function d(e,t){return e-t}function c(e,t){return e.x-t.x}function p(e){var t=((16711680&e)>>16).toString(16),i=((65280&e)>>8).toString(16),a=((255&e)>>0).toString(16);return t=t.length<2?"0"+t:t,i=i.length<2?"0"+i:i,a=a.length<2?"0"+a:a,"#"+t+i+a}function x(e,t,i){return e<<16|t<<8|i}function u(e){var t=[],i=(16711680&e)>>16,a=(65280&e)>>8,n=(255&e)>>0;return t[0]=i,t[1]=a,t[2]=n,t}function m(e){var t=this.length>>>0,i=Number(arguments[1])||0;for(i=i<0?Math.ceil(i):Math.floor(i),i<0&&(i+=t);i<t;i++)if(i in this&&this[i]===e)return i;return-1}function v(e){return e.indexOf||(e.indexOf=m),e}function g(e,t,i){i=i||"normal";var a=e+"_"+t+"_"+i,n=Y[a];if(isNaN(n)){try{var s="position:absolute; left:0px; top:-20000px; padding:0px;margin:0px;border:none;white-space:pre;line-height:normal;font-family:"+e+"; font-size:"+t+"px; font-weight:"+i+";";if(!O){var r=document.body;O=document.createElement("span"),O.innerHTML="";var o=document.createTextNode("Mpgyi");O.appendChild(o),r.appendChild(O)}O.style.display="",O.setAttribute("style",s),n=Math.round(O.offsetHeight),O.style.display="none"}catch(e){n=Math.ceil(1.1*t)}n=Math.max(n,t),Y[a]=n}return n}function y(e,t){e=e||"solid";var i=[];if(i={solid:[],shortDash:[3,1],shortDot:[1,1],shortDashDot:[3,1,1,1],shortDashDotDot:[3,1,1,1,1,1],dot:[1,2],dash:[4,2],dashDot:[4,2,1,2],longDash:[8,2],longDashDot:[8,2,1,2],longDashDotDot:[8,2,1,2,1,2]}[e])for(var a=0;a<i.length;a++)i[a]*=t;else i=[];return i}function f(e,t,i,a){if(e.addEventListener)e.addEventListener(t,i,a||!1);else{if(!e.attachEvent)return!1;e.attachEvent("on"+t,function(t){t=t||window.event,t.preventDefault=t.preventDefault||function(){t.returnValue=!1},t.stopPropagation=t.stopPropagation||function(){t.cancelBubble=!0},i.call(e,t)})}}function b(){var e=/D{1,4}|M{1,4}|Y{1,4}|h{1,2}|H{1,2}|m{1,2}|s{1,2}|f{1,3}|t{1,2}|T{1,2}|K|z{1,3}|"[^"]*"|'[^']*'/g,t=["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"],i=["Sun","Mon","Tue","Wed","Thu","Fri","Sat"],a=["January","February","March","April","May","June","July","August","September","October","November","December"],n=["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],s=/\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[-+]\d{4})?)\b/g,r=/[^-+\dA-Z]/g;return function(l,h,d){var c=d?d.days:t,p=d?d.months:a,x=d?d.shortDays:i,u=d?d.shortMonths:n,m=!1;if(l=l&&l.getTime?l:l?new Date(l):new Date,isNaN(l))throw SyntaxError("invalid date");"UTC:"===h.slice(0,4)&&(h=h.slice(4),m=!0);var v=m?"getUTC":"get",g=l[v+"Date"](),y=l[v+"Day"](),f=l[v+"Month"](),b=l[v+"FullYear"](),M=l[v+"Hours"](),P=l[v+"Minutes"](),S=l[v+"Seconds"](),T=l[v+"Milliseconds"](),C=m?0:l.getTimezoneOffset();return h.replace(e,function(e){switch(e){case"D":return g;case"DD":return o(g,2);case"DDD":return x[y];case"DDDD":return c[y];case"M":return f+1;case"MM":return o(f+1,2);case"MMM":return u[f];case"MMMM":return p[f];case"Y":return parseInt(String(b).slice(-2));case"YY":return o(String(b).slice(-2),2);case"YYY":return o(String(b).slice(-3),3);case"YYYY":return o(b,4);case"h":return M%12||12;case"hh":return o(M%12||12,2);case"H":return M;case"HH":return o(M,2);case"m":return P;case"mm":return o(P,2);case"s":return S;case"ss":return o(S,2);case"f":return String(T).slice(0,1);case"ff":return o(String(T).slice(0,2),2);case"fff":return o(String(T).slice(0,3),3);case"t":return M<12?"a":"p";case"tt":return M<12?"am":"pm";case"T":return M<12?"A":"P";case"TT":return M<12?"AM":"PM";case"K":return m?"UTC":(String(l).match(s)||[""]).pop().replace(r,"");case"z":return(C>0?"-":"+")+Math.floor(Math.abs(C)/60);case"zz":return(C>0?"-":"+")+o(Math.floor(Math.abs(C)/60),2);case"zzz":return(C>0?"-":"+")+o(Math.floor(Math.abs(C)/60),2)+o(Math.abs(C)%60,2);default:return e.slice(1,e.length-1)}})}}function M(e,t,i){if(null===e)return"";e=Number(e);var a=e<0;a&&(e*=-1);var n=i?i.decimalSeparator:".",s=i?i.digitGroupSeparator:",",r="";t=String(t);var l=1,h="",d="",c=-1,p=[],x=[],u=0,m=0,v=0,g=!1,y=0;d=t.match(/"[^"]*"|'[^']*'|[eE][+-]*[0]+|[,]+[.]|‰|./g);for(var f=null,b=0;d&&b<d.length;b++)if("."===(f=d[b])&&c<0)c=b;else{if("%"===f)l*=100;else{if("‰"===f){l*=1e3;continue}if(","===f[0]&&"."===f[f.length-1]){l/=Math.pow(1e3,f.length-1),c=b+f.length-1;continue}"E"!==f[0]&&"e"!==f[0]||"0"!==f[f.length-1]||(g=!0)}c<0?(p.push(f),"#"===f||"0"===f?u++:","===f&&v++):(x.push(f),"#"!==f&&"0"!==f||m++)}if(g){var M=Math.floor(e);y=(0===M?"":String(M)).length-u,l/=Math.pow(10,y)}e*=l,c<0&&(c=b),r=e.toFixed(m);var P=r.split("."),S=(P[0]+"").split(""),T=(P[1]+"").split("");S&&"0"===S[0]&&S.shift();for(var C=0,k=0,w=0,_=0,A=0;p.length>0;)if("#"===(f=p.pop())||"0"===f)if(++C===u){var L=S;if(S=[],"0"===f)for(var I=u-k-(L?L.length:0);I>0;)L.unshift("0"),I--;for(;L.length>0;)h=L.pop()+h,++A%_==0&&w===v&&L.length>0&&(h=s+h);a&&(h="-"+h)}else S.length>0?(h=S.pop()+h,k++,A++):"0"===f&&(h="0"+h,k++,A++),A%_==0&&w===v&&S.length>0&&(h=s+h);else"E"!==f[0]&&"e"!==f[0]||"0"!==f[f.length-1]||!/[eE][+-]*[0]+/.test(f)?","===f?(w++,_=A,A=0,S.length>0&&(h=s+h)):h=f.length>1&&('"'===f[0]&&'"'===f[f.length-1]||"'"===f[0]&&"'"===f[f.length-1])?f.slice(1,f.length-1)+h:f+h:(f=y<0?f.replace("+","").replace("-",""):f.replace("-",""),h+=f.replace(/[0]+/,function(e){return o(y,e.length)}));for(var B="",F=!1;x.length>0;)f=x.shift(),"#"===f||"0"===f?T.length>0&&0!==Number(T.join(""))?(B+=T.shift(),F=!0):"0"===f&&(B+="0",F=!0):f.length>1&&('"'===f[0]&&'"'===f[f.length-1]||"'"===f[0]&&"'"===f[f.length-1])?B+=f.slice(1,f.length-1):"E"!==f[0]&&"e"!==f[0]||"0"!==f[f.length-1]||!/[eE][+-]*[0]+/.test(f)?B+=f:(f=y<0?f.replace("+","").replace("-",""):f.replace("-",""),B+=f.replace(/[0]+/,function(e){return o(y,e.length)}));return h+=(F?n:"")+B}function P(e,t,i){e*=H,t*=H;for(var a=i.getImageData(e,t,2,2).data,n=!0,s=0;s<4;s++)if(a[s]!==a[s+4]|a[s]!==a[s+8]|a[s]!==a[s+12]){n=!1;break}return n?x(a[0],a[1],a[2]):0}function S(e){var t=0,i=0;return e=e||window.event,e.offsetX||0===e.offsetX?(t=e.offsetX,i=e.offsetY):e.layerX||0==e.layerX?(t=e.layerX,i=e.layerY):(t=e.pageX-e.target.offsetLeft,i=e.pageY-e.target.offsetTop),{x:t,y:i}}function T(e,t,i){var a="",n=e?e+"FontStyle":"fontStyle",s=e?e+"FontWeight":"fontWeight",r=e?e+"FontSize":"fontSize",o=e?e+"FontFamily":"fontFamily";a+=t[n]?t[n]+" ":i&&i[n]?i[n]+" ":"",a+=t[s]?t[s]+" ":i&&i[s]?i[s]+" ":"",a+=t[r]?t[r]+"px ":i&&i[r]?i[r]+"px ":"";var l=t[o]?t[o]+"":i&&i[o]?i[o]+"":"";if(!j&&l){var h=l.split(",")[0];"'"!==h[0]&&'"'!==h[0]&&(h="'"+h+"'"),a+=h}else a+=l;return a}function C(e,t,i){return e in t?t[e]:i[e]}function k(){return E?W/R:1}function w(e,t,i){if(j&&E){var a=e.getContext("2d");R=a.webkitBackingStorePixelRatio||a.mozBackingStorePixelRatio||a.msBackingStorePixelRatio||a.oBackingStorePixelRatio||a.backingStorePixelRatio||1,H=k(),e.width=t*H,e.height=i*H,W!==R&&(e.style.width=t+"px",e.style.height=i+"px",a.scale(H,H))}else e.width=t,e.height=i}function _(e,t){var i=document.createElement("canvas");return i.setAttribute("class","canvasjs-chart-canvas"),w(i,e,t),j||"undefined"==typeof G_vmlCanvasManager||G_vmlCanvasManager.initElement(i),i}function A(e,t,i){if(e&&t&&i){var a=i+"."+("jpeg"===t?"jpg":t),n="image/"+t,s=e.toDataURL(n),r=!1,o=document.createElement("a");o.download=a,o.href=s,o.target="_blank";if("undefined"!=typeof Blob&&new Blob){for(var l=s.replace(/^data:[a-z/]*;base64,/,""),h=atob(l),d=new ArrayBuffer(h.length),c=new Uint8Array(d),p=0;p<h.length;p++)c[p]=h.charCodeAt(p);var x=new Blob([d],{type:"image/"+t});try{window.navigator.msSaveBlob(x,a),r=!0}catch(e){o.dataset.downloadurl=[n,o.download,o.href].join(":"),o.href=window.URL.createObjectURL(x)}}if(!r)try{event=document.createEvent("MouseEvents"),event.initMouseEvent("click",!0,!1,window,0,0,0,0,0,!1,!1,!1,!1,0,null),o.dispatchEvent?o.dispatchEvent(event):o.fireEvent&&o.fireEvent("onclick")}catch(e){var u=window.open();u.document.write("<img src='"+s+"'></img><div>Please right click on the image and save it to your device</div>"),u.document.close()}}}function L(e,t,i){t.getAttribute("state")!==i&&(t.setAttribute("state",i),t.setAttribute("type","button"),t.style.position="relative",t.style.margin="0px 0px 0px 0px",t.style.padding="3px 4px 0px 4px",t.style.cssFloat="left",t.setAttribute("title",e._cultureInfo[i+"Text"]),t.innerHTML="<img style='height:16px;' src='"+V[i].image+"' alt='"+e._cultureInfo[i+"Text"]+"' />")}function I(){for(var e=null,t=0;t<arguments.length;t++)e=arguments[t],e.style&&(e.style.display="inline")}function B(){for(var e=null,t=0;t<arguments.length;t++)(e=arguments[t])&&e.style&&(e.style.display="none")}function F(e,t){for(var i=[],a=0;a<e.length;a++)if(0!=a){var n,s,r;r=a-1,n=0===r?0:r-1,s=r===e.length-1?r:r+1;var o={x:(e[s].x-e[n].x)/t,y:(e[s].y-e[n].y)/t},l={x:e[r].x+o.x/3,y:e[r].y+o.y/3};i[i.length]=l,r=a,n=0===r?0:r-1,s=r===e.length-1?r:r+1;var h={x:(e[s].x-e[n].x)/t,y:(e[s].y-e[n].y)/t},d={x:e[r].x-h.x/3,y:e[r].y-h.y/3};i[i.length]=d,i[i.length]=e[a]}else i.push(e[0]);return i}function z(e,t){if(null===e||void 0===e)return t;var i=parseFloat(e.toString())*(e.toString().indexOf("%")>=0?t/100:1);return!isNaN(i)&&i<=t&&i>=0?i:t}function D(e,t,i,a,n,s,r,o,l,h,d,c,p){void 0===p&&(p=1),r=r||0,o=o||"black";var x=t,u=a,m=i,v=n;if(a-t>15&&n-i>15)var g=8;else var g=.35*Math.min(a-t,n-i);var y="rgba(255, 255, 255, .4)",f="rgba(255, 255, 255, 0.1)",b=s;if(e.beginPath(),e.moveTo(t,i),e.save(),e.fillStyle=b,e.globalAlpha=p,e.fillRect(t,i,a-t,n-i),e.globalAlpha=1,r>0){var M=r%2==0?0:.5;e.beginPath(),e.lineWidth=r,e.strokeStyle=o,e.moveTo(t,i),e.rect(t-M,i-M,a-t+2*M,n-i+2*M),e.stroke()}if(e.restore(),!0===l){e.save(),e.beginPath(),e.moveTo(t,i),e.lineTo(t+g,i+g),e.lineTo(a-g,i+g),e.lineTo(a,i),e.closePath();var P=e.createLinearGradient((a+t)/2,m+g,(a+t)/2,m);P.addColorStop(0,b),P.addColorStop(1,y),e.fillStyle=P,e.fill(),e.restore()}if(!0===h){e.save(),e.beginPath(),e.moveTo(t,n),e.lineTo(t+g,n-g),e.lineTo(a-g,n-g),e.lineTo(a,n),e.closePath();var P=e.createLinearGradient((a+t)/2,v-g,(a+t)/2,v);P.addColorStop(0,b),P.addColorStop(1,y),e.fillStyle=P,e.fill(),e.restore()}if(!0===d){e.save(),e.beginPath(),e.moveTo(t,i),e.lineTo(t+g,i+g),e.lineTo(t+g,n-g),e.lineTo(t,n),e.closePath();var P=e.createLinearGradient(x+g,(n+i)/2,x,(n+i)/2);P.addColorStop(0,b),P.addColorStop(1,f),e.fillStyle=P,e.fill(),e.restore()}if(!0===c){e.save(),e.beginPath(),e.moveTo(a,i),e.lineTo(a-g,i+g),e.lineTo(a-g,n-g),e.lineTo(a,n);var P=e.createLinearGradient(u-g,(n+i)/2,u,(n+i)/2);P.addColorStop(0,b),P.addColorStop(1,f),e.fillStyle=P,P.addColorStop(0,b),P.addColorStop(1,f),e.fillStyle=P,e.fill(),e.closePath(),e.restore()}}function X(e,t,i,a,n,s,r,o,l){if(void 0===o&&(o=1),!j){var h=Number((r%(2*Math.PI)).toFixed(8));Number((s%(2*Math.PI)).toFixed(8))===h&&(r-=1e-4)}e.save(),e.globalAlpha=o,"pie"===n?(e.beginPath(),e.moveTo(t.x,t.y),e.arc(t.x,t.y,i,s,r,!1),e.fillStyle=a,e.strokeStyle="white",e.lineWidth=2,e.closePath(),e.fill()):"doughnut"===n&&(e.beginPath(),e.arc(t.x,t.y,i,s,r,!1),e.arc(t.x,t.y,l*i,r,s,!0),e.closePath(),e.fillStyle=a,e.strokeStyle="white",e.lineWidth=2,e.fill()),e.globalAlpha=1,e.restore()}Object.defineProperty(i,"__esModule",{value:!0}),i.extend=a,i.inherit=n,i.addToDateTime=s,i.convertToNumber=r,i.pad=o,i.trimString=l,i.extendCtx=h,i.compareNumbers=d,i.compareDataPointX=c,i.intToHexColorString=p,i.RGBToInt=x,i.intToRGB=u,i.arrayIndexOf=m,i.addArrayIndexOf=v,i.getFontHeightInPixels=g,i.getLineDashArray=y,i.addEvent=f,i.dateFormat=b,i.numberFormat=M,i.getObjectId=P,i.getMouseCoordinates=S,i.getFontString=T,i.getProperty=C,i.getDevicePixelBackingStoreRatio=k,i.setCanvasSize=w,i.createCanvas=_,i.exportCanvas=A,i.setButtonState=L,i.show=I,i.hide=B,i.getBezierPoints=F,i.convertPercentToValue=z,i.drawRect=D,i.drawSegment=X;var Y={},O=null,E=!0,W=window.devicePixelRatio||1,R=1,H=k(),V={reset:{image:"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAcCAYAAAAAwr0iAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAALEgAACxIB0t1+/AAAABx0RVh0U29mdHdhcmUAQWRvYmUgRmlyZXdvcmtzIENTNui8sowAAAKRSURBVEiJrdY/iF1FFMfxzwnZrGISUSR/JLGIhoh/QiRNBLWxMLIWEkwbgiAoFgoW2mhlY6dgpY2IlRBRxBSKhSAKIklWJRYuMZKAhiyopAiaTY7FvRtmZ+/ed9/zHRjezLw5v/O9d86cuZGZpmURAfdn5o9DfdZNLXpjz+LziPgyIl6MiG0jPTJzZBuyDrP4BVm0P/AKbljTb4ToY/gGewYA7KyCl+1b3DUYANvwbiHw0gCAGRzBOzjTAXEOu0cC4Ch+r5x/HrpdrcZmvIDFSucMtnYCYC++6HmNDw8FKDT34ETrf639/azOr5vwRk/g5fbeuABtgC04XWk9VQLciMP4EH/3AFzErRNC7MXlQmsesSoHsGPE23hmEoBW+61K66HMXFmIMvN8myilXS36R01ub+KfYvw43ZXwYDX+AHP4BAci4pFJomfmr/ihmNofESsBImJGk7mlncrM45n5JPbhz0kAWpsv+juxaX21YIPmVJS2uNzJMS6ZNexC0d+I7fUWXLFyz2kSZlpWPvASlmqAf/FXNXf3FAF2F/1LuFifAlionB6dRuSI2IwHi6lzmXmp6xR8XY0fiIh7psAwh+3FuDkRHQVjl+a8lkXjo0kLUKH7XaV5oO86PmZ1FTzyP4K/XGl9v/zwfbW7BriiuETGCP5ch9bc9f97HF/vcFzCa5gdEPgWq+t/4v0V63oE1uF4h0DiFJ7HnSWMppDdh1dxtsPvJ2wcBNAKbsJXa0Ck5opdaBPsRNu/usba09i1KsaAVzmLt3sghrRjuK1Tf4xkegInxwy8gKf7dKMVH2QRsV5zXR/Cftyu+aKaKbbkQrsdH+PTzLzcqzkOQAVzM+7FHdiqqe2/YT4zF/t8S/sPmawyvC974vcAAAAASUVORK5CYII="},pan:{image:"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAALEgAACxIB0t1+/AAAABx0RVh0U29mdHdhcmUAQWRvYmUgRmlyZXdvcmtzIENTNui8sowAAAJVSURBVFiFvZe7a1RBGMV/x2hWI4JpfKCIiSBKOoOCkID/wP4BFqIIFkE02ChIiC8QDKlSiI3YqRBsBVGwUNAUdiIEUgjiAzQIIsuKJsfizsXr5t7d+8jmwLDfzHz3nLOzc7+ZxTZlGyDgZiWOCuJ9wH2gCUyuqQFgF/AGcKJNrYkBYBj40CIet+muGQi/96kM4WS7C/Tm5VUg7whJg8BkEGkCR4BDYfodsADUgP6wErO5iCtswsuJb32hdbXy8qzL5TIdmzJinHdZoZIBZcSFkGlAKs1Z3YCketZcBtouuaQNkrblMiBpBrhme7mAgU4wMCvpcFsDkq4C54DFVRTH9h+i6vlE0r5UA5ImgCuh28jB28iIs7BIVCOeStoZD64P4uPAjUTygKSx2FsK2TIwkugfk9Qkfd/E+yMWHQCeSRqx/R3gOp3LazfaS2C4B5gHDgD7U9x3E3uAH7KNpC3AHHAwTL4FHgM9GQ8vAaPA0dB/Abxqk2/gBLA9MXba9r1k/d4LfA3JtwueBeM58ucS+edXnAW23wP10N3advEi9CXizTnyN4bPS7Zn4sH/dq3t18AY4e1YLYSy3g/csj2VnFshZPuOpOeSKHCodUINuGj7YetE6je1PV9QoNPJ9StNHKodx7nRbiWrGHBGXAi5DUiqtQwtpcWK0Jubt8CltA5MEV1IfwO7+VffPwGfia5m34CT4bXujIIX0Qna1/cGMNqV/wUJE2czxD8CQ4X5Sl7Jz7SILwCDpbjKPBRMHAd+EtX4HWV5Spdc2w8kDQGPbH8py/MXMygM69/FKz4AAAAASUVORK5CYII="},zoom:{image:"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAK6wAACusBgosNWgAAABx0RVh0U29mdHdhcmUAQWRvYmUgRmlyZXdvcmtzIENTNui8sowAAAMqSURBVFiFvdfbj91TFMDxz57U6GUEMS1aYzyMtCSSDhWjCZMInpAI3khE/QHtgzdRkXgSCS8SES9epKLi0oRKNETjRahREq2KS1stdRujtDPtbA97n5zdn9+5zJxTK9k5v3POXmt991p7r71+IcaoGwkhTOIebMRqzOBTvIG3Y4zTXRmqSoyx5cAKbMJOHMFJnMZ8/jyFaXyMR7G6nb1aH22cP4BvcBxziG3GKfyTIR9D6BYg1KUghPBCDveFlb/24Av8iuUYw41YVsz5G7uxKcZ4aMEpwGt5NY3V/YbHsQ6rcAHOw/kYxigewr5CZw4fYGxBKcCLOFEYehXrMdRhr5yLETxVScsOLOkKAPfn1TYMPIvLFrShUlS2FDZm8XRHACzFAWl3R2xbqPMCYhmeLCAOYEMngAczbcTvuHYxzguIy/FesR9e6gSwU/OoPYHBHgHgviIKX2Flq7k34KhmcVnbi/PC8JX4MgMcxb118wZwdz5aISscqx7VRcox7MrPQ7i+btIAJrAkf9+bI9EPmZY2IAxiTSuAldLq4Y9+AcSUh78KP0tbAcwU35cXMD1JCIFUoGiehlqAz6TNB1f1C0DK+0h+nsNPrQC2a4bqGmlD9kOGcWt+Po6pVgDvSxfJaSkFd4UQBvoAsBYbCoB3a2flM7slA0R8iyt6rAFDeDPbm8eOTpVwGD9qVq7nLbIaZnmksPU1JtsCZMXNmpdRxFasWITzh6Xj3LCzra1OxcD2QjHiGVzdpfORnMqZio2PcF23ABdJF1Np4BPptlyPi6WzPYBzpJZtHe7A6xW9cnyP8TqA//SEIYRL8Bxul7rihvwgtVn78WcGGZXa9HGd5TDujDHuOePXNiHdKjWgZX/YbsxLx/ktqbjVzTlcjUSnvI5JrdlUVp6WesZZ6R1hRrpq9+EVTGS9jTjYAuKIouGpbcurEkIYxC051KNSamazsc+xK8b4S0VnEi/j0hqTP+M27O258egQwZuzs7pI7Mf4WQXIEDc5s9sux+5+1Py2EmP8UOq6GvWhIScxfdYjUERiAt9Jd84J6a16zf8JEKT3yCm8g1UxRv8CC4pyRhzR1uUAAAAASUVORK5CYII="},menu:{image:"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAgCAYAAAAbifjMAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAK6wAACusBgosNWgAAABx0RVh0U29mdHdhcmUAQWRvYmUgRmlyZXdvcmtzIENTNui8sowAAAAWdEVYdENyZWF0aW9uIFRpbWUAMDcvMTUvMTTPsvU0AAAAP0lEQVRIie2SMQoAIBDDUvH/X667g8sJJ9KOhYYOkW0qGaU1MPdC0vGSbV19EACo3YMPAFH5BUBUjsqfAPpVXtNgGDfxEDCtAAAAAElFTkSuQmCC"}},j=i.isCanvasSupported=!!document.createElement("canvas").getContext},{}],40:[function(e,t,i){"use strict";function a(e){return e&&e.__esModule?e:{default:e}}function n(e,t){var i=this,a=new r.default(e,t,this);this.render=function(){return a.render(i.options)},this.options=a._options}Object.defineProperty(i,"__esModule",{value:!0}),i.formatDate=i.formatNumber=i.addCultureInfo=i.addColorSet=void 0,i.Chart=n;var s=e("../core/charts"),r=a(s),o=e("../core/culture_info"),l=a(o),h=e("../constants/themes"),d=e("../constants/culture"),c=e("../helpers/utils");i.addColorSet=function(e,t){h.colorSets[e]=t},i.addCultureInfo=function(e,t){d.cultures[e]=t},i.formatNumber=function(e,t,i){if(i=i||"en",t=t||"#,##0.##",!d.cultures[i])throw"Unknown Culture Name";(0,c.numberFormat)(e,t,new l.default(i))},i.formatDate=function(e,t,i){if(i=i||"en",t=t||"DD MMM YYYY",!d.cultures[i])throw"Unknown Culture Name";(0,c.dateFormat)(e,t,new l.default(i))};n.version="v1.8.2"},{"../constants/culture":22,"../constants/themes":24,"../core/charts":28,"../core/culture_info":29,"../helpers/utils":39}]},{},[40])(40)});


/***/ }),

/***/ "./node_modules/canvasjs/dist/jquery.canvasjs.min.js":
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(jQuery) {/*
 CanvasJS HTML5 & JavaScript Charts - v1.8.0 Beta 2 - http://canvasjs.com/ 
 Copyright 2013 fenopix
*/
(function(){function O(a,b){a.prototype=Aa(b.prototype);a.prototype.constructor=a;a.base=b.prototype}function Aa(a){function b(){}b.prototype=a;return new b}function sa(a,b,c){"millisecond"===c?a.setMilliseconds(a.getMilliseconds()+1*b):"second"===c?a.setSeconds(a.getSeconds()+1*b):"minute"===c?a.setMinutes(a.getMinutes()+1*b):"hour"===c?a.setHours(a.getHours()+1*b):"day"===c?a.setDate(a.getDate()+1*b):"week"===c?a.setDate(a.getDate()+7*b):"month"===c?a.setMonth(a.getMonth()+1*b):"year"===c&&a.setFullYear(a.getFullYear()+
1*b);return a}function L(a,b){var c=!1;0>a&&(c=!0,a*=-1);a=""+a;for(b=b?b:1;a.length<b;)a="0"+a;return c?"-"+a:a}function Z(a){if(!a)return a;a=a.replace(/^\s\s*/,"");for(var b=/\s/,c=a.length;b.test(a.charAt(--c)););return a.slice(0,c+1)}function Ba(a){a.roundRect=function(a,c,d,e,g,f,h,p){h&&(this.fillStyle=h);p&&(this.strokeStyle=p);"undefined"===typeof g&&(g=5);this.lineWidth=f;this.beginPath();this.moveTo(a+g,c);this.lineTo(a+d-g,c);this.quadraticCurveTo(a+d,c,a+d,c+g);this.lineTo(a+d,c+e-g);
this.quadraticCurveTo(a+d,c+e,a+d-g,c+e);this.lineTo(a+g,c+e);this.quadraticCurveTo(a,c+e,a,c+e-g);this.lineTo(a,c+g);this.quadraticCurveTo(a,c,a+g,c);this.closePath();h&&this.fill();p&&0<f&&this.stroke()}}function ta(a,b){return a-b}function Ca(a,b){return a.x-b.x}function B(a){var b=((a&16711680)>>16).toString(16),c=((a&65280)>>8).toString(16);a=((a&255)>>0).toString(16);b=2>b.length?"0"+b:b;c=2>c.length?"0"+c:c;a=2>a.length?"0"+a:a;return"#"+b+c+a}function Da(a,b){var c=this.length>>>0,d=Number(b)||
0,d=0>d?Math.ceil(d):Math.floor(d);for(0>d&&(d+=c);d<c;d++)if(d in this&&this[d]===a)return d;return-1}function da(a,b,c){c=c||"normal";var d=a+"_"+b+"_"+c,e=ua[d];if(isNaN(e)){try{a="position:absolute; left:0px; top:-20000px; padding:0px;margin:0px;border:none;white-space:pre;line-height:normal;font-family:"+a+"; font-size:"+b+"px; font-weight:"+c+";";if(!T){var g=document.body;T=document.createElement("span");T.innerHTML="";var f=document.createTextNode("Mpgyi");T.appendChild(f);g.appendChild(T)}T.style.display=
"";T.setAttribute("style",a);e=Math.round(T.offsetHeight);T.style.display="none"}catch(h){e=Math.ceil(1.1*b)}e=Math.max(e,b);ua[d]=e}return e}function M(a,b){var c=[];lineDashTypeMap={solid:[],shortDash:[3,1],shortDot:[1,1],shortDashDot:[3,1,1,1],shortDashDotDot:[3,1,1,1,1,1],dot:[1,2],dash:[4,2],dashDot:[4,2,1,2],longDash:[8,2],longDashDot:[8,2,1,2],longDashDotDot:[8,2,1,2,1,2]};if(c=lineDashTypeMap[a||"solid"])for(var d=0;d<c.length;d++)c[d]*=b;else c=[];return c}function F(a,b,c,d){if(a.addEventListener)a.addEventListener(b,
c,d||!1);else if(a.attachEvent)a.attachEvent("on"+b,function(b){b=b||window.event;b.preventDefault=b.preventDefault||function(){b.returnValue=!1};b.stopPropagation=b.stopPropagation||function(){b.cancelBubble=!0};c.call(a,b)});else return!1}function va(a,b,c){a*=J;b*=J;a=c.getImageData(a,b,2,2).data;b=!0;for(c=0;4>c;c++)if(a[c]!==a[c+4]|a[c]!==a[c+8]|a[c]!==a[c+12]){b=!1;break}return b?a[0]<<16|a[1]<<8|a[2]:0}function N(a,b,c){return a in b?b[a]:c[a]}function ea(a,b,c){if(t&&wa){var d=a.getContext("2d");
fa=d.webkitBackingStorePixelRatio||d.mozBackingStorePixelRatio||d.msBackingStorePixelRatio||d.oBackingStorePixelRatio||d.backingStorePixelRatio||1;J=na/fa;a.width=b*J;a.height=c*J;na!==fa&&(a.style.width=b+"px",a.style.height=c+"px",d.scale(J,J))}else a.width=b,a.height=c}function U(a,b){var c=document.createElement("canvas");c.setAttribute("class","canvasjs-chart-canvas");ea(c,a,b);t||"undefined"===typeof G_vmlCanvasManager||G_vmlCanvasManager.initElement(c);return c}function xa(a,b,c){if(a&&b&&
c){c=c+"."+("jpeg"===b?"jpg":b);var d="image/"+b;a=a.toDataURL(d);var e=!1,g=document.createElement("a");g.download=c;g.href=a;g.target="_blank";if("undefined"!==typeof Blob&&new Blob){for(var f=a.replace(/^data:[a-z/]*;base64,/,""),f=atob(f),h=new ArrayBuffer(f.length),p=new Uint8Array(h),k=0;k<f.length;k++)p[k]=f.charCodeAt(k);b=new Blob([h],{type:"image/"+b});try{window.navigator.msSaveBlob(b,c),e=!0}catch(l){g.dataset.downloadurl=[d,g.download,g.href].join(":"),g.href=window.URL.createObjectURL(b)}}if(!e)try{event=
document.createEvent("MouseEvents"),event.initMouseEvent("click",!0,!1,window,0,0,0,0,0,!1,!1,!1,!1,0,null),g.dispatchEvent?g.dispatchEvent(event):g.fireEvent&&g.fireEvent("onclick")}catch(n){b=window.open(),b.document.write("<img src='"+a+"'></img><div>Please right click on the image and save it to your device</div>"),b.document.close()}}}function P(a,b,c){b.getAttribute("state")!==c&&(b.setAttribute("state",c),b.setAttribute("type","button"),b.style.position="relative",b.style.margin="0px 0px 0px 0px",
b.style.padding="3px 4px 0px 4px",b.style.cssFloat="left",b.setAttribute("title",a._cultureInfo[c+"Text"]),b.innerHTML="<img style='height:16px;' src='"+Ea[c].image+"' alt='"+a._cultureInfo[c+"Text"]+"' />")}function ga(){for(var a=null,b=0;b<arguments.length;b++)a=arguments[b],a.style&&(a.style.display="inline")}function R(){for(var a=null,b=0;b<arguments.length;b++)(a=arguments[b])&&a.style&&(a.style.display="none")}function G(a,b,c,d){this._defaultsKey=a;this.parent=d;this._eventListeners=[];d=
{};c&&(X[c]&&X[c][a])&&(d=X[c][a]);this._options=b?b:{};this.setOptions(this._options,d)}function u(a,b,c){this._publicChartReference=c;b=b||{};u.base.constructor.call(this,"Chart",b,b.theme?b.theme:"theme1");var d=this;this._containerId=a;this._objectsInitialized=!1;this.overlaidCanvasCtx=this.ctx=null;this._indexLabels=[];this._panTimerId=0;this._lastTouchEventType="";this._lastTouchData=null;this.isAnimating=!1;this.renderCount=0;this.panEnabled=this.disableToolTip=this.animatedRender=!1;this._defaultCursor=
"default";this.plotArea={canvas:null,ctx:null,x1:0,y1:0,x2:0,y2:0,width:0,height:0};this._dataInRenderedOrder=[];(this._container="string"===typeof this._containerId?document.getElementById(this._containerId):this._containerId)?(this._container.innerHTML="",b=a=0,a=this._options.width?this.width:0<this._container.clientWidth?this._container.clientWidth:this.width,b=this._options.height?this.height:0<this._container.clientHeight?this._container.clientHeight:this.height,this.width=a,this.height=b,this.x1=
this.y1=0,this.x2=this.width,this.y2=this.height,this._selectedColorSet="undefined"!==typeof V[this.colorSet]?V[this.colorSet]:V.colorSet1,this._canvasJSContainer=document.createElement("div"),this._canvasJSContainer.setAttribute("class","canvasjs-chart-container"),this._canvasJSContainer.style.position="relative",this._canvasJSContainer.style.textAlign="left",this._canvasJSContainer.style.cursor="auto",t||(this._canvasJSContainer.style.height="0px"),this._container.appendChild(this._canvasJSContainer),
this.canvas=U(a,b),this.canvas.style.position="absolute",this.canvas.getContext&&(this._canvasJSContainer.appendChild(this.canvas),this.ctx=this.canvas.getContext("2d"),this.ctx.textBaseline="top",Ba(this.ctx),t?this.plotArea.ctx=this.ctx:(this.plotArea.canvas=U(a,b),this.plotArea.canvas.style.position="absolute",this.plotArea.canvas.setAttribute("class","plotAreaCanvas"),this._canvasJSContainer.appendChild(this.plotArea.canvas),this.plotArea.ctx=this.plotArea.canvas.getContext("2d")),this.overlaidCanvas=
U(a,b),this.overlaidCanvas.style.position="absolute",this._canvasJSContainer.appendChild(this.overlaidCanvas),this.overlaidCanvasCtx=this.overlaidCanvas.getContext("2d"),this.overlaidCanvasCtx.textBaseline="top",this._eventManager=new $(this),F(window,"resize",function(){d._updateSize()&&d.render()}),this._toolBar=document.createElement("div"),this._toolBar.setAttribute("class","canvasjs-chart-toolbar"),this._toolBar.style.cssText="position: absolute; right: 1px; top: 1px;",this._canvasJSContainer.appendChild(this._toolBar),
this.bounds={x1:0,y1:0,x2:this.width,y2:this.height},F(this.overlaidCanvas,"click",function(a){d._mouseEventHandler(a)}),F(this.overlaidCanvas,"mousemove",function(a){d._mouseEventHandler(a)}),F(this.overlaidCanvas,"mouseup",function(a){d._mouseEventHandler(a)}),F(this.overlaidCanvas,"mousedown",function(a){d._mouseEventHandler(a);R(d._dropdownMenu)}),F(this.overlaidCanvas,"mouseout",function(a){d._mouseEventHandler(a)}),F(this.overlaidCanvas,window.navigator.msPointerEnabled?"MSPointerDown":"touchstart",
function(a){d._touchEventHandler(a)}),F(this.overlaidCanvas,window.navigator.msPointerEnabled?"MSPointerMove":"touchmove",function(a){d._touchEventHandler(a)}),F(this.overlaidCanvas,window.navigator.msPointerEnabled?"MSPointerUp":"touchend",function(a){d._touchEventHandler(a)}),F(this.overlaidCanvas,window.navigator.msPointerEnabled?"MSPointerCancel":"touchcancel",function(a){d._touchEventHandler(a)}),this._creditLink||(this._creditLink=document.createElement("a"),this._creditLink.setAttribute("class",
"canvasjs-chart-credit"),this._creditLink.setAttribute("style","outline:none;margin:0px;position:absolute;right:3px;top:"+(this.height-14)+"px;color:dimgrey;text-decoration:none;font-size:10px;font-family:Lucida Grande, Lucida Sans Unicode, Arial, sans-serif"),this._creditLink.setAttribute("tabIndex",-1),this._creditLink.setAttribute("target","_blank")),this._toolTip=new Q(this,this._options.toolTip,this.theme),this.axisY2=this.axisY=this.axisX=this.data=null,this.sessionVariables={axisX:{},axisY:{},
axisY2:{}})):window.console&&window.console.log('CanvasJS Error: Chart Container with id "'+this._containerId+'" was not found')}function ha(a,b){for(var c=[],d=0;d<a.length;d++)if(0==d)c.push(a[0]);else{var e,g,f;f=d-1;e=0===f?0:f-1;g=f===a.length-1?f:f+1;c[c.length]={x:a[f].x+(a[g].x-a[e].x)/b/3,y:a[f].y+(a[g].y-a[e].y)/b/3};f=d;e=0===f?0:f-1;g=f===a.length-1?f:f+1;c[c.length]={x:a[f].x-(a[g].x-a[e].x)/b/3,y:a[f].y-(a[g].y-a[e].y)/b/3};c[c.length]=a[d]}return c}function ya(a,b){if(null===a||"undefined"===
typeof a)return b;var c=parseFloat(a.toString())*(0<=a.toString().indexOf("%")?b/100:1);return!isNaN(c)&&c<=b&&0<=c?c:b}function Y(a,b,c,d,e){"undefined"===typeof e&&(e=0);this._padding=e;this._x1=a;this._y1=b;this._x2=c;this._y2=d;this._rightOccupied=this._leftOccupied=this._bottomOccupied=this._topOccupied=this._padding}function H(a,b){H.base.constructor.call(this,"TextBlock",b);this.ctx=a;this._isDirty=!0;this._wrappedText=null;this._lineHeight=da(this.fontFamily,this.fontSize,this.fontWeight)}
function aa(a,b){aa.base.constructor.call(this,"Title",b,a.theme);this.chart=a;this.canvas=a.canvas;this.ctx=this.chart.ctx;"undefined"===typeof this._options.fontSize&&(this.fontSize=this.chart.getAutoFontSize(this.fontSize));this.height=this.width=null;this.bounds={x1:null,y1:null,x2:null,y2:null}}function ia(a,b){ia.base.constructor.call(this,"Subtitle",b,a.theme);this.chart=a;this.canvas=a.canvas;this.ctx=this.chart.ctx;"undefined"===typeof this._options.fontSize&&(this.fontSize=this.chart.getAutoFontSize(this.fontSize));
this.height=this.width=null;this.bounds={x1:null,y1:null,x2:null,y2:null}}function ja(a,b,c){ja.base.constructor.call(this,"Legend",b,c);this.chart=a;this.canvas=a.canvas;this.ctx=this.chart.ctx;this.ghostCtx=this.chart._eventManager.ghostCtx;this.items=[];this.height=this.width=0;this.orientation=null;this.dataSeries=[];this.bounds={x1:null,y1:null,x2:null,y2:null};"undefined"===typeof this._options.fontSize&&(this.fontSize=this.chart.getAutoFontSize(this.fontSize));this.lineHeight=da(this.fontFamily,
this.fontSize,this.fontWeight);this.horizontalSpacing=this.fontSize}function oa(a,b){oa.base.constructor.call(this,b);this.chart=a;this.canvas=a.canvas;this.ctx=this.chart.ctx}function S(a,b,c,d,e){S.base.constructor.call(this,"DataSeries",b,c);this.chart=a;this.canvas=a.canvas;this._ctx=a.canvas.ctx;this.index=d;this.noDataPointsInPlotArea=0;this.id=e;this.chart._eventManager.objectMap[e]={id:e,objectType:"dataSeries",dataSeriesIndex:d};this.dataPointIds=[];this.plotUnit=[];this.axisY=this.axisX=
null;null===this.fillOpacity&&(this.type.match(/area/i)?this.fillOpacity=0.7:this.fillOpacity=1);this.axisPlacement=this.getDefaultAxisPlacement();"undefined"===typeof this._options.indexLabelFontSize&&(this.indexLabelFontSize=this.chart.getAutoFontSize(this.indexLabelFontSize))}function C(a,b,c,d){C.base.constructor.call(this,"Axis",b,a.theme);this.chart=a;this.canvas=a.canvas;this.ctx=a.ctx;this.intervalStartPosition=this.maxHeight=this.maxWidth=0;this.labels=[];this._labels=null;this.dataInfo=
{min:Infinity,max:-Infinity,viewPortMin:Infinity,viewPortMax:-Infinity,minDiff:Infinity};"axisX"===c?(this.sessionVariables=this.chart.sessionVariables[c],this._options.interval||(this.intervalType=null)):this.sessionVariables="left"===d||"top"===d?this.chart.sessionVariables.axisY:this.chart.sessionVariables.axisY2;"undefined"===typeof this._options.titleFontSize&&(this.titleFontSize=this.chart.getAutoFontSize(this.titleFontSize));"undefined"===typeof this._options.labelFontSize&&(this.labelFontSize=
this.chart.getAutoFontSize(this.labelFontSize));this.type=c;"axisX"!==c||b&&"undefined"!==typeof b.gridThickness||(this.gridThickness=0);this._position=d;this.lineCoordinates={x1:null,y1:null,x2:null,y2:null,width:null};this.labelAngle=(this.labelAngle%360+360)%360;90<this.labelAngle&&270>=this.labelAngle?this.labelAngle-=180:180<this.labelAngle&&270>=this.labelAngle?this.labelAngle-=180:270<this.labelAngle&&360>=this.labelAngle&&(this.labelAngle-=360);if(this._options.stripLines&&0<this._options.stripLines.length)for(this.stripLines=
[],b=0;b<this._options.stripLines.length;b++)this.stripLines.push(new ka(this.chart,this._options.stripLines[b],a.theme,++this.chart._eventManager.lastObjectId,this));this._titleTextBlock=null;this.hasOptionChanged("viewportMinimum")||isNaN(this.sessionVariables.newViewportMinimum)||null===this.sessionVariables.newViewportMinimum?this.sessionVariables.newViewportMinimum=null:this.viewportMinimum=this.sessionVariables.newViewportMinimum;this.hasOptionChanged("viewportMaximum")||isNaN(this.sessionVariables.newViewportMaximum)||
null===this.sessionVariables.newViewportMaximum?this.sessionVariables.newViewportMaximum=null:this.viewportMaximum=this.sessionVariables.newViewportMaximum;null!==this.minimum&&null!==this.viewportMinimum&&(this.viewportMinimum=Math.max(this.viewportMinimum,this.minimum));null!==this.maximum&&null!==this.viewportMaximum&&(this.viewportMaximum=Math.min(this.viewportMaximum,this.maximum));this.trackChanges("viewportMinimum");this.trackChanges("viewportMaximum")}function ka(a,b,c,d,e){ka.base.constructor.call(this,
"StripLine",b,c,e);this.id=d;this.chart=a;this.ctx=this.chart.ctx;this.label=this.label;this._thicknessType="pixel";null!==this.startValue&&null!==this.endValue&&(this.value=((this.startValue.getTime?this.startValue.getTime():this.startValue)+(this.endValue.getTime?this.endValue.getTime():this.endValue))/2,this.thickness=Math.max(this.endValue-this.startValue),this._thicknessType="value")}function Q(a,b,c){Q.base.constructor.call(this,"ToolTip",b,c);this.chart=a;this.canvas=a.canvas;this.ctx=this.chart.ctx;
this.currentDataPointIndex=this.currentSeriesIndex=-1;this._timerId=0;this._prevY=this._prevX=NaN;this._initialize()}function $(a){this.chart=a;this.lastObjectId=0;this.objectMap=[];this.rectangularRegionEventSubscriptions=[];this.previousDataPointEventObject=null;this.ghostCanvas=U(this.chart.width,this.chart.height);this.ghostCtx=this.ghostCanvas.getContext("2d");this.mouseoveredObjectMaps=[]}function ba(a){var b;a&&ca[a]&&(b=ca[a]);ba.base.constructor.call(this,"CultureInfo",b)}function pa(a){this.chart=
a;this.ctx=this.chart.plotArea.ctx;this.animations=[];this.animationRequestId=null}var t=!!document.createElement("canvas").getContext,la={Chart:{width:500,height:400,zoomEnabled:!1,zoomType:"x",backgroundColor:"white",theme:"theme1",animationEnabled:!1,animationDuration:1200,dataPointMaxWidth:null,colorSet:"colorSet1",culture:"en",creditText:"CanvasJS.com",interactivityEnabled:!0,exportEnabled:!1,exportFileName:"Chart",rangeChanging:null,rangeChanged:null},Title:{padding:0,text:null,verticalAlign:"top",
horizontalAlign:"center",fontSize:20,fontFamily:"Calibri",fontWeight:"normal",fontColor:"black",fontStyle:"normal",borderThickness:0,borderColor:"black",cornerRadius:0,backgroundColor:null,margin:5,wrap:!0,maxWidth:null,dockInsidePlotArea:!1},Subtitle:{padding:0,text:null,verticalAlign:"top",horizontalAlign:"center",fontSize:14,fontFamily:"Calibri",fontWeight:"normal",fontColor:"black",fontStyle:"normal",borderThickness:0,borderColor:"black",cornerRadius:0,backgroundColor:null,margin:2,wrap:!0,maxWidth:null,
dockInsidePlotArea:!1},Legend:{name:null,verticalAlign:"center",horizontalAlign:"right",fontSize:14,fontFamily:"calibri",fontWeight:"normal",fontColor:"black",fontStyle:"normal",cursor:null,itemmouseover:null,itemmouseout:null,itemmousemove:null,itemclick:null,dockInsidePlotArea:!1,reversed:!1,maxWidth:null,maxHeight:null,itemMaxWidth:null,itemWidth:null,itemWrap:!0,itemTextFormatter:null},ToolTip:{enabled:!0,shared:!1,animationEnabled:!0,content:null,contentFormatter:null,reversed:!1,backgroundColor:null,
borderColor:null,borderThickness:2,cornerRadius:5,fontSize:14,fontColor:"#000000",fontFamily:"Calibri, Arial, Georgia, serif;",fontWeight:"normal",fontStyle:"italic"},Axis:{minimum:null,maximum:null,viewportMinimum:null,viewportMaximum:null,interval:null,intervalType:null,title:null,titleFontColor:"black",titleFontSize:20,titleFontFamily:"arial",titleFontWeight:"normal",titleFontStyle:"normal",labelAngle:0,labelFontFamily:"arial",labelFontColor:"black",labelFontSize:12,labelFontWeight:"normal",labelFontStyle:"normal",
labelAutoFit:!1,labelWrap:!0,labelMaxWidth:null,labelFormatter:null,prefix:"",suffix:"",includeZero:!0,tickLength:5,tickColor:"black",tickThickness:1,lineColor:"black",lineThickness:1,lineDashType:"solid",gridColor:"A0A0A0",gridThickness:0,gridDashType:"solid",interlacedColor:null,valueFormatString:null,margin:2,stripLines:[]},StripLine:{value:null,startValue:null,endValue:null,color:"orange",opacity:null,thickness:2,lineDashType:"solid",label:"",labelBackgroundColor:"#EEEEEE",labelFontFamily:"arial",
labelFontColor:"orange",labelFontSize:12,labelFontWeight:"normal",labelFontStyle:"normal",labelFormatter:null,showOnTop:!1},DataSeries:{name:null,dataPoints:null,label:"",bevelEnabled:!1,highlightEnabled:!0,cursor:null,indexLabel:"",indexLabelPlacement:"auto",indexLabelOrientation:"horizontal",indexLabelFontColor:"black",indexLabelFontSize:12,indexLabelFontStyle:"normal",indexLabelFontFamily:"Arial",indexLabelFontWeight:"normal",indexLabelBackgroundColor:null,indexLabelLineColor:null,indexLabelLineThickness:1,
indexLabelLineDashType:"solid",indexLabelMaxWidth:null,indexLabelWrap:!0,indexLabelFormatter:null,lineThickness:2,lineDashType:"solid",color:null,risingColor:"white",fillOpacity:null,startAngle:0,radius:null,innerRadius:null,type:"column",xValueType:"number",axisYType:"primary",xValueFormatString:null,yValueFormatString:null,zValueFormatString:null,percentFormatString:null,showInLegend:null,legendMarkerType:null,legendMarkerColor:null,legendText:null,legendMarkerBorderColor:null,legendMarkerBorderThickness:null,
markerType:"circle",markerColor:null,markerSize:null,markerBorderColor:null,markerBorderThickness:null,mouseover:null,mouseout:null,mousemove:null,click:null,toolTipContent:null,visible:!0},TextBlock:{x:0,y:0,width:null,height:null,maxWidth:null,maxHeight:null,padding:0,angle:0,text:"",horizontalAlign:"center",fontSize:12,fontFamily:"calibri",fontWeight:"normal",fontColor:"black",fontStyle:"normal",borderThickness:0,borderColor:"black",cornerRadius:0,backgroundColor:null,textBaseline:"top"},CultureInfo:{decimalSeparator:".",
digitGroupSeparator:",",zoomText:"Zoom",panText:"Pan",resetText:"Reset",menuText:"More Options",saveJPGText:"Save as JPG",savePNGText:"Save as PNG",days:"Sunday Monday Tuesday Wednesday Thursday Friday Saturday".split(" "),shortDays:"Sun Mon Tue Wed Thu Fri Sat".split(" "),months:"January February March April May June July August September October November December".split(" "),shortMonths:"Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec".split(" ")}},ca={en:{}},V={colorSet1:"#369EAD #C24642 #7F6084 #86B402 #A2D1CF #C8B631 #6DBCEB #52514E #4F81BC #A064A1 #F79647".split(" "),
colorSet2:"#4F81BC #C0504E #9BBB58 #23BFAA #8064A1 #4AACC5 #F79647 #33558B".split(" "),colorSet3:"#8CA1BC #36845C #017E82 #8CB9D0 #708C98 #94838D #F08891 #0366A7 #008276 #EE7757 #E5BA3A #F2990B #03557B #782970".split(" ")},X={theme1:{Chart:{colorSet:"colorSet1"},Title:{fontFamily:t?"Calibri, Optima, Candara, Verdana, Geneva, sans-serif":"calibri",fontSize:33,fontColor:"#3A3A3A",fontWeight:"bold",verticalAlign:"top",margin:5},Subtitle:{fontFamily:t?"Calibri, Optima, Candara, Verdana, Geneva, sans-serif":
"calibri",fontSize:16,fontColor:"#3A3A3A",fontWeight:"bold",verticalAlign:"top",margin:5},Axis:{titleFontSize:26,titleFontColor:"#666666",titleFontFamily:t?"Calibri, Optima, Candara, Verdana, Geneva, sans-serif":"calibri",labelFontFamily:t?"Calibri, Optima, Candara, Verdana, Geneva, sans-serif":"calibri",labelFontSize:18,labelFontColor:"grey",tickColor:"#BBBBBB",tickThickness:2,gridThickness:2,gridColor:"#BBBBBB",lineThickness:2,lineColor:"#BBBBBB"},Legend:{verticalAlign:"bottom",horizontalAlign:"center",
fontFamily:t?"monospace, sans-serif,arial black":"calibri"},DataSeries:{indexLabelFontColor:"grey",indexLabelFontFamily:t?"Calibri, Optima, Candara, Verdana, Geneva, sans-serif":"calibri",indexLabelFontSize:18,indexLabelLineThickness:1}},theme2:{Chart:{colorSet:"colorSet2"},Title:{fontFamily:"impact, charcoal, arial black, sans-serif",fontSize:32,fontColor:"#333333",verticalAlign:"top",margin:5},Subtitle:{fontFamily:"impact, charcoal, arial black, sans-serif",fontSize:14,fontColor:"#333333",verticalAlign:"top",
margin:5},Axis:{titleFontSize:22,titleFontColor:"rgb(98,98,98)",titleFontFamily:t?"monospace, sans-serif,arial black":"arial",titleFontWeight:"bold",labelFontFamily:t?"monospace, Courier New, Courier":"arial",labelFontSize:16,labelFontColor:"grey",labelFontWeight:"bold",tickColor:"grey",tickThickness:2,gridThickness:2,gridColor:"grey",lineColor:"grey",lineThickness:0},Legend:{verticalAlign:"bottom",horizontalAlign:"center",fontFamily:t?"monospace, sans-serif,arial black":"arial"},DataSeries:{indexLabelFontColor:"grey",
indexLabelFontFamily:t?"Courier New, Courier, monospace":"arial",indexLabelFontWeight:"bold",indexLabelFontSize:18,indexLabelLineThickness:1}},theme3:{Chart:{colorSet:"colorSet1"},Title:{fontFamily:t?"Candara, Optima, Trebuchet MS, Helvetica Neue, Helvetica, Trebuchet MS, serif":"calibri",fontSize:32,fontColor:"#3A3A3A",fontWeight:"bold",verticalAlign:"top",margin:5},Subtitle:{fontFamily:t?"Candara, Optima, Trebuchet MS, Helvetica Neue, Helvetica, Trebuchet MS, serif":"calibri",fontSize:16,fontColor:"#3A3A3A",
fontWeight:"bold",verticalAlign:"top",margin:5},Axis:{titleFontSize:22,titleFontColor:"rgb(98,98,98)",titleFontFamily:t?"Verdana, Geneva, Calibri, sans-serif":"calibri",labelFontFamily:t?"Calibri, Optima, Candara, Verdana, Geneva, sans-serif":"calibri",labelFontSize:18,labelFontColor:"grey",tickColor:"grey",tickThickness:2,gridThickness:2,gridColor:"grey",lineThickness:2,lineColor:"grey"},Legend:{verticalAlign:"bottom",horizontalAlign:"center",fontFamily:t?"monospace, sans-serif,arial black":"calibri"},
DataSeries:{bevelEnabled:!0,indexLabelFontColor:"grey",indexLabelFontFamily:t?"Candara, Optima, Calibri, Verdana, Geneva, sans-serif":"calibri",indexLabelFontSize:18,indexLabelLineColor:"lightgrey",indexLabelLineThickness:2}}},D={numberDuration:1,yearDuration:314496E5,monthDuration:2592E6,weekDuration:6048E5,dayDuration:864E5,hourDuration:36E5,minuteDuration:6E4,secondDuration:1E3,millisecondDuration:1,dayOfWeekFromInt:"Sunday Monday Tuesday Wednesday Thursday Friday Saturday".split(" ")},ua={},T=
null,qa=function(){var a=/D{1,4}|M{1,4}|Y{1,4}|h{1,2}|H{1,2}|m{1,2}|s{1,2}|f{1,3}|t{1,2}|T{1,2}|K|z{1,3}|"[^"]*"|'[^']*'/g,b="Sunday Monday Tuesday Wednesday Thursday Friday Saturday".split(" "),c="Sun Mon Tue Wed Thu Fri Sat".split(" "),d="January February March April May June July August September October November December".split(" "),e="Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec".split(" "),g=/\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[-+]\d{4})?)\b/g,
f=/[^-+\dA-Z]/g;return function(h,p,k){var l=k?k.days:b,n=k?k.months:d,m=k?k.shortDays:c,q=k?k.shortMonths:e;k="";var r=!1;h=h&&h.getTime?h:h?new Date(h):new Date;if(isNaN(h))throw SyntaxError("invalid date");"UTC:"===p.slice(0,4)&&(p=p.slice(4),r=!0);k=r?"getUTC":"get";var s=h[k+"Date"](),w=h[k+"Day"](),v=h[k+"Month"](),x=h[k+"FullYear"](),t=h[k+"Hours"](),y=h[k+"Minutes"](),z=h[k+"Seconds"](),u=h[k+"Milliseconds"](),A=r?0:h.getTimezoneOffset();return k=p.replace(a,function(a){switch(a){case "D":return s;
case "DD":return L(s,2);case "DDD":return m[w];case "DDDD":return l[w];case "M":return v+1;case "MM":return L(v+1,2);case "MMM":return q[v];case "MMMM":return n[v];case "Y":return parseInt(String(x).slice(-2));case "YY":return L(String(x).slice(-2),2);case "YYY":return L(String(x).slice(-3),3);case "YYYY":return L(x,4);case "h":return t%12||12;case "hh":return L(t%12||12,2);case "H":return t;case "HH":return L(t,2);case "m":return y;case "mm":return L(y,2);case "s":return z;case "ss":return L(z,2);
case "f":return String(u).slice(0,1);case "ff":return L(String(u).slice(0,2),2);case "fff":return L(String(u).slice(0,3),3);case "t":return 12>t?"a":"p";case "tt":return 12>t?"am":"pm";case "T":return 12>t?"A":"P";case "TT":return 12>t?"AM":"PM";case "K":return r?"UTC":(String(h).match(g)||[""]).pop().replace(f,"");case "z":return(0<A?"-":"+")+Math.floor(Math.abs(A)/60);case "zz":return(0<A?"-":"+")+L(Math.floor(Math.abs(A)/60),2);case "zzz":return(0<A?"-":"+")+L(Math.floor(Math.abs(A)/60),2)+L(Math.abs(A)%
60,2);default:return a.slice(1,a.length-1)}})}}(),W=function(a,b,c){if(null===a)return"";a=Number(a);var d=0>a?!0:!1;d&&(a*=-1);var e=c?c.decimalSeparator:".",g=c?c.digitGroupSeparator:",",f="";b=String(b);var f=1,h=c="",p=-1,k=[],l=[],n=0,m=0,q=0,r=!1,s=0,h=b.match(/"[^"]*"|'[^']*'|[eE][+-]*[0]+|[,]+[.]|\u2030|./g);b=null;for(var w=0;h&&w<h.length;w++)if(b=h[w],"."===b&&0>p)p=w;else{if("%"===b)f*=100;else if("\u2030"===b){f*=1E3;continue}else if(","===b[0]&&"."===b[b.length-1]){f/=Math.pow(1E3,b.length-
1);p=w+b.length-1;continue}else"E"!==b[0]&&"e"!==b[0]||"0"!==b[b.length-1]||(r=!0);0>p?(k.push(b),"#"===b||"0"===b?n++:","===b&&q++):(l.push(b),"#"!==b&&"0"!==b||m++)}r&&(b=Math.floor(a),s=(0===b?"":String(b)).length-n,f/=Math.pow(10,s));0>p&&(p=w);f=(a*f).toFixed(m);b=f.split(".");f=(b[0]+"").split("");a=(b[1]+"").split("");f&&"0"===f[0]&&f.shift();for(w=r=h=m=p=0;0<k.length;)if(b=k.pop(),"#"===b||"0"===b)if(p++,p===n){var v=f,f=[];if("0"===b)for(b=n-m-(v?v.length:0);0<b;)v.unshift("0"),b--;for(;0<
v.length;)c=v.pop()+c,w++,0===w%r&&(h===q&&0<v.length)&&(c=g+c);d&&(c="-"+c)}else 0<f.length?(c=f.pop()+c,m++,w++):"0"===b&&(c="0"+c,m++,w++),0===w%r&&(h===q&&0<f.length)&&(c=g+c);else"E"!==b[0]&&"e"!==b[0]||"0"!==b[b.length-1]||!/[eE][+-]*[0]+/.test(b)?","===b?(h++,r=w,w=0,0<f.length&&(c=g+c)):c=1<b.length&&('"'===b[0]&&'"'===b[b.length-1]||"'"===b[0]&&"'"===b[b.length-1])?b.slice(1,b.length-1)+c:b+c:(b=0>s?b.replace("+","").replace("-",""):b.replace("-",""),c+=b.replace(/[0]+/,function(a){return L(s,
a.length)}));d="";for(g=!1;0<l.length;)b=l.shift(),"#"===b||"0"===b?0<a.length&&0!==Number(a.join(""))?(d+=a.shift(),g=!0):"0"===b&&(d+="0",g=!0):1<b.length&&('"'===b[0]&&'"'===b[b.length-1]||"'"===b[0]&&"'"===b[b.length-1])?d+=b.slice(1,b.length-1):"E"!==b[0]&&"e"!==b[0]||"0"!==b[b.length-1]||!/[eE][+-]*[0]+/.test(b)?d+=b:(b=0>s?b.replace("+","").replace("-",""):b.replace("-",""),d+=b.replace(/[0]+/,function(a){return L(s,a.length)}));return c+((g?e:"")+d)},ma=function(a){var b=0,c=0;a=a||window.event;
a.offsetX||0===a.offsetX?(b=a.offsetX,c=a.offsetY):a.layerX||0==a.layerX?(b=a.layerX,c=a.layerY):(b=a.pageX-a.target.offsetLeft,c=a.pageY-a.target.offsetTop);return{x:b,y:c}},wa=!0,na=window.devicePixelRatio||1,fa=1,J=wa?na/fa:1,Ea={reset:{image:"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAcCAYAAAAAwr0iAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAALEgAACxIB0t1+/AAAABx0RVh0U29mdHdhcmUAQWRvYmUgRmlyZXdvcmtzIENTNui8sowAAAKRSURBVEiJrdY/iF1FFMfxzwnZrGISUSR/JLGIhoh/QiRNBLWxMLIWEkwbgiAoFgoW2mhlY6dgpY2IlRBRxBSKhSAKIklWJRYuMZKAhiyopAiaTY7FvRtmZ+/ed9/zHRjezLw5v/O9d86cuZGZpmURAfdn5o9DfdZNLXpjz+LziPgyIl6MiG0jPTJzZBuyDrP4BVm0P/AKbljTb4ToY/gGewYA7KyCl+1b3DUYANvwbiHw0gCAGRzBOzjTAXEOu0cC4Ch+r5x/HrpdrcZmvIDFSucMtnYCYC++6HmNDw8FKDT34ETrf639/azOr5vwRk/g5fbeuABtgC04XWk9VQLciMP4EH/3AFzErRNC7MXlQmsesSoHsGPE23hmEoBW+61K66HMXFmIMvN8myilXS36R01ub+KfYvw43ZXwYDX+AHP4BAci4pFJomfmr/ihmNofESsBImJGk7mlncrM45n5JPbhz0kAWpsv+juxaX21YIPmVJS2uNzJMS6ZNexC0d+I7fUWXLFyz2kSZlpWPvASlmqAf/FXNXf3FAF2F/1LuFifAlionB6dRuSI2IwHi6lzmXmp6xR8XY0fiIh7psAwh+3FuDkRHQVjl+a8lkXjo0kLUKH7XaV5oO86PmZ1FTzyP4K/XGl9v/zwfbW7BriiuETGCP5ch9bc9f97HF/vcFzCa5gdEPgWq+t/4v0V63oE1uF4h0DiFJ7HnSWMppDdh1dxtsPvJ2wcBNAKbsJXa0Ck5opdaBPsRNu/usba09i1KsaAVzmLt3sghrRjuK1Tf4xkegInxwy8gKf7dKMVH2QRsV5zXR/Cftyu+aKaKbbkQrsdH+PTzLzcqzkOQAVzM+7FHdiqqe2/YT4zF/t8S/sPmawyvC974vcAAAAASUVORK5CYII="},
pan:{image:"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAALEgAACxIB0t1+/AAAABx0RVh0U29mdHdhcmUAQWRvYmUgRmlyZXdvcmtzIENTNui8sowAAAJVSURBVFiFvZe7a1RBGMV/x2hWI4JpfKCIiSBKOoOCkID/wP4BFqIIFkE02ChIiC8QDKlSiI3YqRBsBVGwUNAUdiIEUgjiAzQIIsuKJsfizsXr5t7d+8jmwLDfzHz3nLOzc7+ZxTZlGyDgZiWOCuJ9wH2gCUyuqQFgF/AGcKJNrYkBYBj40CIet+muGQi/96kM4WS7C/Tm5VUg7whJg8BkEGkCR4BDYfodsADUgP6wErO5iCtswsuJb32hdbXy8qzL5TIdmzJinHdZoZIBZcSFkGlAKs1Z3YCketZcBtouuaQNkrblMiBpBrhme7mAgU4wMCvpcFsDkq4C54DFVRTH9h+i6vlE0r5UA5ImgCuh28jB28iIs7BIVCOeStoZD64P4uPAjUTygKSx2FsK2TIwkugfk9Qkfd/E+yMWHQCeSRqx/R3gOp3LazfaS2C4B5gHDgD7U9x3E3uAH7KNpC3AHHAwTL4FHgM9GQ8vAaPA0dB/Abxqk2/gBLA9MXba9r1k/d4LfA3JtwueBeM58ucS+edXnAW23wP10N3advEi9CXizTnyN4bPS7Zn4sH/dq3t18AY4e1YLYSy3g/csj2VnFshZPuOpOeSKHCodUINuGj7YetE6je1PV9QoNPJ9StNHKodx7nRbiWrGHBGXAi5DUiqtQwtpcWK0Jubt8CltA5MEV1IfwO7+VffPwGfia5m34CT4bXujIIX0Qna1/cGMNqV/wUJE2czxD8CQ4X5Sl7Jz7SILwCDpbjKPBRMHAd+EtX4HWV5Spdc2w8kDQGPbH8py/MXMygM69/FKz4AAAAASUVORK5CYII="},
zoom:{image:"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAK6wAACusBgosNWgAAABx0RVh0U29mdHdhcmUAQWRvYmUgRmlyZXdvcmtzIENTNui8sowAAAMqSURBVFiFvdfbj91TFMDxz57U6GUEMS1aYzyMtCSSDhWjCZMInpAI3khE/QHtgzdRkXgSCS8SES9epKLi0oRKNETjRahREq2KS1stdRujtDPtbA97n5zdn9+5zJxTK9k5v3POXmt991p7r71+IcaoGwkhTOIebMRqzOBTvIG3Y4zTXRmqSoyx5cAKbMJOHMFJnMZ8/jyFaXyMR7G6nb1aH22cP4BvcBxziG3GKfyTIR9D6BYg1KUghPBCDveFlb/24Av8iuUYw41YVsz5G7uxKcZ4aMEpwGt5NY3V/YbHsQ6rcAHOw/kYxigewr5CZw4fYGxBKcCLOFEYehXrMdRhr5yLETxVScsOLOkKAPfn1TYMPIvLFrShUlS2FDZm8XRHACzFAWl3R2xbqPMCYhmeLCAOYEMngAczbcTvuHYxzguIy/FesR9e6gSwU/OoPYHBHgHgviIKX2Flq7k34KhmcVnbi/PC8JX4MgMcxb118wZwdz5aISscqx7VRcox7MrPQ7i+btIAJrAkf9+bI9EPmZY2IAxiTSuAldLq4Y9+AcSUh78KP0tbAcwU35cXMD1JCIFUoGiehlqAz6TNB1f1C0DK+0h+nsNPrQC2a4bqGmlD9kOGcWt+Po6pVgDvSxfJaSkFd4UQBvoAsBYbCoB3a2flM7slA0R8iyt6rAFDeDPbm8eOTpVwGD9qVq7nLbIaZnmksPU1JtsCZMXNmpdRxFasWITzh6Xj3LCzra1OxcD2QjHiGVzdpfORnMqZio2PcF23ABdJF1Np4BPptlyPi6WzPYBzpJZtHe7A6xW9cnyP8TqA//SEIYRL8Bxul7rihvwgtVn78WcGGZXa9HGd5TDujDHuOePXNiHdKjWgZX/YbsxLx/ktqbjVzTlcjUSnvI5JrdlUVp6WesZZ6R1hRrpq9+EVTGS9jTjYAuKIouGpbcurEkIYxC051KNSamazsc+xK8b4S0VnEi/j0hqTP+M27O258egQwZuzs7pI7Mf4WQXIEDc5s9sux+5+1Py2EmP8UOq6GvWhIScxfdYjUERiAt9Jd84J6a16zf8JEKT3yCm8g1UxRv8CC4pyRhzR1uUAAAAASUVORK5CYII="},
menu:{image:"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAgCAYAAAAbifjMAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAK6wAACusBgosNWgAAABx0RVh0U29mdHdhcmUAQWRvYmUgRmlyZXdvcmtzIENTNui8sowAAAAWdEVYdENyZWF0aW9uIFRpbWUAMDcvMTUvMTTPsvU0AAAAP0lEQVRIie2SMQoAIBDDUvH/X667g8sJJ9KOhYYOkW0qGaU1MPdC0vGSbV19EACo3YMPAFH5BUBUjsqfAPpVXtNgGDfxEDCtAAAAAElFTkSuQmCC"}};G.prototype.setOptions=function(a,b){if(la[this._defaultsKey]){var c=la[this._defaultsKey],d;for(d in c)c.hasOwnProperty(d)&&(this[d]=a&&d in a?a[d]:b&&d in
b?b[d]:c[d])}};G.prototype.updateOption=function(a){var b=la[this._defaultsKey],c=this._options.theme?this._options.theme:this.chart&&this.chart._options.theme?this.chart._options.theme:"theme1",d={},e=this[a];c&&(X[c]&&X[c][this._defaultsKey])&&(d=X[c][this._defaultsKey]);a in b&&(e=a in this._options?this._options[a]:d&&a in d?d[a]:b[a]);if(e===this[a])return!1;this[a]=e;return!0};G.prototype.trackChanges=function(a){if(!this.sessionVariables)throw"Session Variable Store not set";this.sessionVariables[a]=
this._options[a]};G.prototype.isBeingTracked=function(a){this._options._oldOptions||(this._options._oldOptions={});return this._options._oldOptions[a]?!0:!1};G.prototype.hasOptionChanged=function(a){if(!this.sessionVariables)throw"Session Variable Store not set";return this.sessionVariables[a]!==this._options[a]};G.prototype.addEventListener=function(a,b,c){a&&b&&(this._eventListeners[a]=this._eventListeners[a]||[],this._eventListeners[a].push({context:c||this,eventHandler:b}))};G.prototype.removeEventListener=
function(a,b){if(a&&b&&this._eventListeners[a])for(var c=this._eventListeners[a],d=0;d<c.length;d++)if(c[d].eventHandler===b){c[d].splice(d,1);break}};G.prototype.removeAllEventListeners=function(){this._eventListeners=[]};G.prototype.dispatchEvent=function(a,b,c){if(a&&this._eventListeners[a]){b=b||{};for(var d=this._eventListeners[a],e=0;e<d.length;e++)d[e].eventHandler.call(d[e].context,b)}"function"===typeof this[a]&&this[a].call(c||this.chart._publicChartReference,b)};O(u,G);u.prototype._updateOptions=
function(){var a=this;this.updateOption("width");this.updateOption("height");this.updateOption("dataPointMaxWidth");this.updateOption("interactivityEnabled");this.updateOption("theme");this.updateOption("colorSet")&&(this._selectedColorSet="undefined"!==typeof V[this.colorSet]?V[this.colorSet]:V.colorSet1);this.updateOption("backgroundColor");this.backgroundColor||(this.backgroundColor="rgba(0,0,0,0)");this.updateOption("culture");this._cultureInfo=new ba(this._options.culture);this.updateOption("animationEnabled");
this.animationEnabled=this.animationEnabled&&t;this.updateOption("animationDuration");this.updateOption("rangeChanging");this.updateOption("rangeChanged");this._options.zoomEnabled?(this._zoomButton||(R(this._zoomButton=document.createElement("button")),P(this,this._zoomButton,"pan"),this._toolBar.appendChild(this._zoomButton),F(this._zoomButton,"click",function(){a.zoomEnabled?(a.zoomEnabled=!1,a.panEnabled=!0,P(a,a._zoomButton,"zoom")):(a.zoomEnabled=!0,a.panEnabled=!1,P(a,a._zoomButton,"pan"));
a.render()})),this._resetButton||(R(this._resetButton=document.createElement("button")),P(this,this._resetButton,"reset"),this._toolBar.appendChild(this._resetButton),F(this._resetButton,"click",function(){a._toolTip.hide();a.zoomEnabled||a.panEnabled?(a.zoomEnabled=!0,a.panEnabled=!1,P(a,a._zoomButton,"pan"),a._defaultCursor="default",a.overlaidCanvas.style.cursor=a._defaultCursor):(a.zoomEnabled=!1,a.panEnabled=!1);a.sessionVariables.axisX&&(a.sessionVariables.axisX.newViewportMinimum=null,a.sessionVariables.axisX.newViewportMaximum=
null);a.sessionVariables.axisY&&(a.sessionVariables.axisY.newViewportMinimum=null,a.sessionVariables.axisY.newViewportMaximum=null);a.sessionVariables.axisY2&&(a.sessionVariables.axisY2.newViewportMinimum=null,a.sessionVariables.axisY2.newViewportMaximum=null);a.resetOverlayedCanvas();R(a._zoomButton,a._resetButton);a._dispatchRangeEvent("rangeChanging","reset");a.render();a._dispatchRangeEvent("rangeChanged","reset")}),this.overlaidCanvas.style.cursor=a._defaultCursor),this.zoomEnabled||this.panEnabled||
(this._zoomButton?(a._zoomButton.getAttribute("state")===a._cultureInfo.zoomText?(this.panEnabled=!0,this.zoomEnabled=!1):(this.zoomEnabled=!0,this.panEnabled=!1),ga(a._zoomButton,a._resetButton)):(this.zoomEnabled=!0,this.panEnabled=!1))):this.panEnabled=this.zoomEnabled=!1;this._menuButton?this.exportEnabled?ga(this._menuButton):R(this._menuButton):this.exportEnabled&&t&&(this._menuButton=document.createElement("button"),P(this,this._menuButton,"menu"),this._toolBar.appendChild(this._menuButton),
F(this._menuButton,"click",function(){"none"!==a._dropdownMenu.style.display||a._dropDownCloseTime&&500>=(new Date).getTime()-a._dropDownCloseTime.getTime()||(a._dropdownMenu.style.display="block",a._menuButton.blur(),a._dropdownMenu.focus())},!0));if(!this._dropdownMenu&&this.exportEnabled&&t){this._dropdownMenu=document.createElement("div");this._dropdownMenu.setAttribute("tabindex",-1);this._dropdownMenu.style.cssText="position: absolute; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none; cursor: pointer;right: 1px;top: 25px;min-width: 120px;outline: 0;border: 1px solid silver;font-size: 14px;font-family: Calibri, Verdana, sans-serif;padding: 5px 0px 5px 0px;text-align: left;background-color: #fff;line-height: 20px;box-shadow: 2px 2px 10px #888888;";
a._dropdownMenu.style.display="none";this._toolBar.appendChild(this._dropdownMenu);F(this._dropdownMenu,"blur",function(){R(a._dropdownMenu);a._dropDownCloseTime=new Date},!0);var b=document.createElement("div");b.style.cssText="padding: 2px 15px 2px 10px";b.innerHTML=this._cultureInfo.saveJPGText;this._dropdownMenu.appendChild(b);F(b,"mouseover",function(){this.style.backgroundColor="#EEEEEE"},!0);F(b,"mouseout",function(){this.style.backgroundColor="transparent"},!0);F(b,"click",function(){xa(a.canvas,
"jpg",a.exportFileName);R(a._dropdownMenu)},!0);b=document.createElement("div");b.style.cssText="padding: 2px 15px 2px 10px";b.innerHTML=this._cultureInfo.savePNGText;this._dropdownMenu.appendChild(b);F(b,"mouseover",function(){this.style.backgroundColor="#EEEEEE"},!0);F(b,"mouseout",function(){this.style.backgroundColor="transparent"},!0);F(b,"click",function(){xa(a.canvas,"png",a.exportFileName);R(a._dropdownMenu)},!0)}"none"!==this._toolBar.style.display&&this._zoomButton&&(this.panEnabled?P(a,
a._zoomButton,"zoom"):P(a,a._zoomButton,"pan"),a._resetButton.getAttribute("state")!==a._cultureInfo.resetText&&P(a,a._resetButton,"reset"));if("undefined"===typeof la.Chart.creditHref)this.creditHref="http://canvasjs.com/",this.creditText="CanvasJS.com";else var c=this.updateOption("creditText"),d=this.updateOption("creditHref");if(0===this.renderCount||c||d)this._creditLink.setAttribute("href",this.creditHref),this._creditLink.innerHTML=this.creditText;this.creditHref&&this.creditText?this._creditLink.parentElement||
this._canvasJSContainer.appendChild(this._creditLink):this._creditLink.parentElement&&this._canvasJSContainer.removeChild(this._creditLink);this._options.toolTip&&this._toolTip._options!==this._options.toolTip&&(this._toolTip._options=this._options.toolTip);for(var e in this._toolTip._options)this._toolTip._options.hasOwnProperty(e)&&this._toolTip.updateOption(e)};u.prototype._updateSize=function(){var a=0,b=0;this._options.width?a=this.width:this.width=a=0<this._container.clientWidth?this._container.clientWidth:
this.width;this._options.height?b=this.height:this.height=b=0<this._container.clientHeight?this._container.clientHeight:this.height;return this.canvas.width!==a*J||this.canvas.height!==b*J?(ea(this.canvas,a,b),ea(this.overlaidCanvas,a,b),ea(this._eventManager.ghostCanvas,a,b),!0):!1};u.prototype._initialize=function(){this._animator?this._animator.cancelAllAnimations():this._animator=new pa(this);this.removeAllEventListeners();this.disableToolTip=!1;this._axes=[];this.pieDoughnutClickHandler=null;
this.animationRequestId&&this.cancelRequestAnimFrame.call(window,this.animationRequestId);this._updateOptions();this.animatedRender=t&&this.animationEnabled&&0===this.renderCount;this._updateSize();this.clearCanvas();this.ctx.beginPath();this.axisY2=this.axisY=this.axisX=null;this._indexLabels=[];this._dataInRenderedOrder=[];this._events=[];this._eventManager&&this._eventManager.reset();this.plotInfo={axisPlacement:null,axisXValueType:null,plotTypes:[]};this.layoutManager=new Y(0,0,this.width,this.height,
2);this.plotArea.layoutManager&&this.plotArea.layoutManager.reset();this.data=[];for(var a=0,b=0;b<this._options.data.length;b++)if(a++,!this._options.data[b].type||0<=u._supportedChartTypes.indexOf(this._options.data[b].type)){var c=new S(this,this._options.data[b],this.theme,a-1,++this._eventManager.lastObjectId);null===c.name&&(c.name="DataSeries "+a);null===c.color?1<this._options.data.length?(c._colorSet=[this._selectedColorSet[c.index%this._selectedColorSet.length]],c.color=this._selectedColorSet[c.index%
this._selectedColorSet.length]):c._colorSet="line"===c.type||"stepLine"===c.type||"spline"===c.type||"area"===c.type||"stepArea"===c.type||"splineArea"===c.type||"stackedArea"===c.type||"stackedArea100"===c.type||"rangeArea"===c.type||"rangeSplineArea"===c.type||"candlestick"===c.type||"ohlc"===c.type?[this._selectedColorSet[0]]:this._selectedColorSet:c._colorSet=[c.color];null===c.markerSize&&(("line"===c.type||"stepLine"===c.type||"spline"===c.type)&&c.dataPoints&&c.dataPoints.length<this.width/
16||"scatter"===c.type)&&(c.markerSize=8);"bubble"!==c.type&&"scatter"!==c.type||!c.dataPoints||c.dataPoints.sort(Ca);this.data.push(c);var d=c.axisPlacement,e;"normal"===d?"xySwapped"===this.plotInfo.axisPlacement?e='You cannot combine "'+c.type+'" with bar chart':"none"===this.plotInfo.axisPlacement?e='You cannot combine "'+c.type+'" with pie chart':null===this.plotInfo.axisPlacement&&(this.plotInfo.axisPlacement="normal"):"xySwapped"===d?"normal"===this.plotInfo.axisPlacement?e='You cannot combine "'+
c.type+'" with line, area, column or pie chart':"none"===this.plotInfo.axisPlacement?e='You cannot combine "'+c.type+'" with pie chart':null===this.plotInfo.axisPlacement&&(this.plotInfo.axisPlacement="xySwapped"):"none"==d&&("normal"===this.plotInfo.axisPlacement?e='You cannot combine "'+c.type+'" with line, area, column or bar chart':"xySwapped"===this.plotInfo.axisPlacement?e='You cannot combine "'+c.type+'" with bar chart':null===this.plotInfo.axisPlacement&&(this.plotInfo.axisPlacement="none"));
if(e&&window.console){window.console.log(e);return}}this._objectsInitialized=!0};u._supportedChartTypes=function(a){a.indexOf||(a.indexOf=Da);return a}("line stepLine spline column area stepArea splineArea bar bubble scatter stackedColumn stackedColumn100 stackedBar stackedBar100 stackedArea stackedArea100 candlestick ohlc rangeColumn rangeBar rangeArea rangeSplineArea pie doughnut funnel".split(" "));u.prototype.render=function(a){a&&(this._options=a);this._initialize();var b=[];for(a=0;a<this.data.length;a++)if("normal"===
this.plotInfo.axisPlacement||"xySwapped"===this.plotInfo.axisPlacement)this.data[a].axisYType&&"primary"!==this.data[a].axisYType?"secondary"===this.data[a].axisYType&&(this.axisY2||("normal"===this.plotInfo.axisPlacement?this._axes.push(this.axisY2=new C(this,this._options.axisY2,"axisY","right")):"xySwapped"===this.plotInfo.axisPlacement&&this._axes.push(this.axisY2=new C(this,this._options.axisY2,"axisY","top"))),this.data[a].axisY=this.axisY2):(this.axisY||("normal"===this.plotInfo.axisPlacement?
this._axes.push(this.axisY=new C(this,this._options.axisY,"axisY","left")):"xySwapped"===this.plotInfo.axisPlacement&&this._axes.push(this.axisY=new C(this,this._options.axisY,"axisY","bottom"))),this.data[a].axisY=this.axisY),this.axisX||("normal"===this.plotInfo.axisPlacement?this._axes.push(this.axisX=new C(this,this._options.axisX,"axisX","bottom")):"xySwapped"===this.plotInfo.axisPlacement&&this._axes.push(this.axisX=new C(this,this._options.axisX,"axisX","left"))),this.data[a].axisX=this.axisX;
this.axisY&&this.axisY2&&(0<this.axisY.gridThickness&&"undefined"===typeof this.axisY2._options.gridThickness?this.axisY2.gridThickness=0:0<this.axisY2.gridThickness&&"undefined"===typeof this.axisY._options.gridThickness&&(this.axisY.gridThickness=0));var c=!1;if(0<this._axes.length&&(this.zoomEnabled||this.panEnabled))for(a=0;a<this._axes.length;a++)if(null!==this._axes[a].viewportMinimum||null!==this._axes[a].viewportMaximum){c=!0;break}c?ga(this._zoomButton,this._resetButton):R(this._zoomButton,
this._resetButton);this._processData();this._options.title&&(this._title=new aa(this,this._options.title),this._title.dockInsidePlotArea?b.push(this._title):this._title.render());if(this._options.subtitles)for(a=0;a<this._options.subtitles.length;a++)this.subtitles=[],c=new ia(this,this._options.subtitles[a]),this.subtitles.push(c),c.dockInsidePlotArea?b.push(c):c.render();this.legend=new ja(this,this._options.legend,this.theme);for(a=0;a<this.data.length;a++)(this.data[a].showInLegend||"pie"===this.data[a].type||
"doughnut"===this.data[a].type)&&this.legend.dataSeries.push(this.data[a]);this.legend.dockInsidePlotArea?b.push(this.legend):this.legend.render();if("normal"===this.plotInfo.axisPlacement||"xySwapped"===this.plotInfo.axisPlacement)C.setLayoutAndRender(this.axisX,this.axisY,this.axisY2,this.plotInfo.axisPlacement,this.layoutManager.getFreeSpace());else if("none"===this.plotInfo.axisPlacement)this.preparePlotArea();else return;a=0;for(a in b)b.hasOwnProperty(a)&&b[a].render();var d=[];if(this.animatedRender){var e=
U(this.width,this.height);e.getContext("2d").drawImage(this.canvas,0,0,this.width,this.height)}for(a=0;a<this.plotInfo.plotTypes.length;a++)for(b=this.plotInfo.plotTypes[a],c=0;c<b.plotUnits.length;c++){var g=b.plotUnits[c],f=null;g.targetCanvas=null;this.animatedRender&&(g.targetCanvas=U(this.width,this.height),g.targetCanvasCtx=g.targetCanvas.getContext("2d"));"line"===g.type?f=this.renderLine(g):"stepLine"===g.type?f=this.renderStepLine(g):"spline"===g.type?f=this.renderSpline(g):"column"===g.type?
f=this.renderColumn(g):"bar"===g.type?f=this.renderBar(g):"area"===g.type?f=this.renderArea(g):"stepArea"===g.type?f=this.renderStepArea(g):"splineArea"===g.type?f=this.renderSplineArea(g):"stackedColumn"===g.type?f=this.renderStackedColumn(g):"stackedColumn100"===g.type?f=this.renderStackedColumn100(g):"stackedBar"===g.type?f=this.renderStackedBar(g):"stackedBar100"===g.type?f=this.renderStackedBar100(g):"stackedArea"===g.type?f=this.renderStackedArea(g):"stackedArea100"===g.type?f=this.renderStackedArea100(g):
"bubble"===g.type?f=f=this.renderBubble(g):"scatter"===g.type?f=this.renderScatter(g):"pie"===g.type?this.renderPie(g):"doughnut"===g.type?this.renderPie(g):"candlestick"===g.type?f=this.renderCandlestick(g):"ohlc"===g.type?f=this.renderCandlestick(g):"rangeColumn"===g.type?f=this.renderRangeColumn(g):"rangeBar"===g.type?f=this.renderRangeBar(g):"rangeArea"===g.type?f=this.renderRangeArea(g):"rangeSplineArea"===g.type&&(f=this.renderRangeSplineArea(g));for(var h=0;h<g.dataSeriesIndexes.length;h++)this._dataInRenderedOrder.push(this.data[g.dataSeriesIndexes[h]]);
this.animatedRender&&f&&d.push(f)}this.animatedRender&&0<this._indexLabels.length&&(a=U(this.width,this.height).getContext("2d"),d.push(this.renderIndexLabels(a)));var p=this;0<d.length?(p.disableToolTip=!0,p._animator.animate(200,p.animationDuration,function(a){p.ctx.clearRect(0,0,p.width,p.height);p.ctx.drawImage(e,0,0,Math.floor(p.width*J),Math.floor(p.height*J),0,0,p.width,p.height);for(var c=0;c<d.length;c++)f=d[c],1>a&&"undefined"!==typeof f.startTimePercent?a>=f.startTimePercent&&f.animationCallback(f.easingFunction(a-
f.startTimePercent,0,1,1-f.startTimePercent),f):f.animationCallback(f.easingFunction(a,0,1,1),f);p.dispatchEvent("dataAnimationIterationEnd",{chart:p})},function(){d=[];for(var a=0;a<p.plotInfo.plotTypes.length;a++)for(var c=p.plotInfo.plotTypes[a],b=0;b<c.plotUnits.length;b++)c.plotUnits[b].targetCanvas=null;e=null;p.disableToolTip=!1})):(0<p._indexLabels.length&&p.renderIndexLabels(),p.dispatchEvent("dataAnimationIterationEnd",{chart:p}));this.attachPlotAreaEventHandlers();this.zoomEnabled||(this.panEnabled||
!this._zoomButton||"none"===this._zoomButton.style.display)||R(this._zoomButton,this._resetButton);this._toolTip._updateToolTip();this.renderCount++};u.prototype.attachPlotAreaEventHandlers=function(){this.attachEvent({context:this,chart:this,mousedown:this._plotAreaMouseDown,mouseup:this._plotAreaMouseUp,mousemove:this._plotAreaMouseMove,cursor:this.zoomEnabled?"col-resize":"move",cursor:this.panEnabled?"move":"default",capture:!0,bounds:this.plotArea})};u.prototype.categoriseDataSeries=function(){for(var a=
"",b=0;b<this.data.length;b++)if(a=this.data[b],a.dataPoints&&(0!==a.dataPoints.length&&a.visible)&&0<=u._supportedChartTypes.indexOf(a.type)){for(var c=null,d=!1,e=null,g=!1,f=0;f<this.plotInfo.plotTypes.length;f++)if(this.plotInfo.plotTypes[f].type===a.type){d=!0;c=this.plotInfo.plotTypes[f];break}d||(c={type:a.type,totalDataSeries:0,plotUnits:[]},this.plotInfo.plotTypes.push(c));for(f=0;f<c.plotUnits.length;f++)if(c.plotUnits[f].axisYType===a.axisYType){g=!0;e=c.plotUnits[f];break}g||(e={type:a.type,
previousDataSeriesCount:0,index:c.plotUnits.length,plotType:c,axisYType:a.axisYType,axisY:"primary"===a.axisYType?this.axisY:this.axisY2,axisX:this.axisX,dataSeriesIndexes:[],yTotals:[]},c.plotUnits.push(e));c.totalDataSeries++;e.dataSeriesIndexes.push(b);a.plotUnit=e}for(b=0;b<this.plotInfo.plotTypes.length;b++)for(c=this.plotInfo.plotTypes[b],f=a=0;f<c.plotUnits.length;f++)c.plotUnits[f].previousDataSeriesCount=a,a+=c.plotUnits[f].dataSeriesIndexes.length};u.prototype.assignIdToDataPoints=function(){for(var a=
0;a<this.data.length;a++){var b=this.data[a];if(b.dataPoints)for(var c=b.dataPoints.length,d=0;d<c;d++)b.dataPointIds[d]=++this._eventManager.lastObjectId}};u.prototype._processData=function(){this.assignIdToDataPoints();this.categoriseDataSeries();for(var a=0;a<this.plotInfo.plotTypes.length;a++)for(var b=this.plotInfo.plotTypes[a],c=0;c<b.plotUnits.length;c++){var d=b.plotUnits[c];"line"===d.type||"stepLine"===d.type||"spline"===d.type||"column"===d.type||"area"===d.type||"stepArea"===d.type||"splineArea"===
d.type||"bar"===d.type||"bubble"===d.type||"scatter"===d.type?this._processMultiseriesPlotUnit(d):"stackedColumn"===d.type||"stackedBar"===d.type||"stackedArea"===d.type?this._processStackedPlotUnit(d):"stackedColumn100"===d.type||"stackedBar100"===d.type||"stackedArea100"===d.type?this._processStacked100PlotUnit(d):"candlestick"!==d.type&&"ohlc"!==d.type&&"rangeColumn"!==d.type&&"rangeBar"!==d.type&&"rangeArea"!==d.type&&"rangeSplineArea"!==d.type||this._processMultiYPlotUnit(d)}};u.prototype._processMultiseriesPlotUnit=
function(a){if(a.dataSeriesIndexes&&!(1>a.dataSeriesIndexes.length))for(var b=a.axisY.dataInfo,c=a.axisX.dataInfo,d,e,g=!1,f=0;f<a.dataSeriesIndexes.length;f++){var h=this.data[a.dataSeriesIndexes[f]],p=0,k=!1,l=!1;if("normal"===h.axisPlacement||"xySwapped"===h.axisPlacement)var n=this.sessionVariables.axisX.newViewportMinimum?this.sessionVariables.axisX.newViewportMinimum:this._options.axisX&&this._options.axisX.viewportMinimum?this._options.axisX.viewportMinimum:this._options.axisX&&this._options.axisX.minimum?
this._options.axisX.minimum:-Infinity,m=this.sessionVariables.axisX.newViewportMaximum?this.sessionVariables.axisX.newViewportMaximum:this._options.axisX&&this._options.axisX.viewportMaximum?this._options.axisX.viewportMaximum:this._options.axisX&&this._options.axisX.maximum?this._options.axisX.maximum:Infinity;if(h.dataPoints[p].x&&h.dataPoints[p].x.getTime||"dateTime"===h.xValueType)g=!0;for(p=0;p<h.dataPoints.length;p++){"undefined"===typeof h.dataPoints[p].x&&(h.dataPoints[p].x=p);h.dataPoints[p].x.getTime?
(g=!0,d=h.dataPoints[p].x.getTime()):d=h.dataPoints[p].x;e=h.dataPoints[p].y;d<c.min&&(c.min=d);d>c.max&&(c.max=d);e<b.min&&(b.min=e);e>b.max&&(b.max=e);if(0<p){var q=d-h.dataPoints[p-1].x;0>q&&(q*=-1);c.minDiff>q&&0!==q&&(c.minDiff=q);null!==e&&null!==h.dataPoints[p-1].y&&(q=e-h.dataPoints[p-1].y,0>q&&(q*=-1),b.minDiff>q&&0!==q&&(b.minDiff=q))}if(!(d<n)||k){if(!k&&(k=!0,0<p)){p-=2;continue}if(d>m&&!l)l=!0;else if(d>m&&l)continue;h.dataPoints[p].label&&(a.axisX.labels[d]=h.dataPoints[p].label);d<
c.viewPortMin&&(c.viewPortMin=d);d>c.viewPortMax&&(c.viewPortMax=d);null!==e&&(e<b.viewPortMin&&(b.viewPortMin=e),e>b.viewPortMax&&(b.viewPortMax=e))}}this.plotInfo.axisXValueType=h.xValueType=g?"dateTime":"number"}};u.prototype._processStackedPlotUnit=function(a){if(a.dataSeriesIndexes&&!(1>a.dataSeriesIndexes.length)){for(var b=a.axisY.dataInfo,c=a.axisX.dataInfo,d,e,g=!1,f=[],h=[],p=0;p<a.dataSeriesIndexes.length;p++){var k=this.data[a.dataSeriesIndexes[p]],l=0,n=!1,m=!1;if("normal"===k.axisPlacement||
"xySwapped"===k.axisPlacement)var q=this.sessionVariables.axisX.newViewportMinimum?this.sessionVariables.axisX.newViewportMinimum:this._options.axisX&&this._options.axisX.viewportMinimum?this._options.axisX.viewportMinimum:this._options.axisX&&this._options.axisX.minimum?this._options.axisX.minimum:-Infinity,r=this.sessionVariables.axisX.newViewportMaximum?this.sessionVariables.axisX.newViewportMaximum:this._options.axisX&&this._options.axisX.viewportMaximum?this._options.axisX.viewportMaximum:this._options.axisX&&
this._options.axisX.maximum?this._options.axisX.maximum:Infinity;if(k.dataPoints[l].x&&k.dataPoints[l].x.getTime||"dateTime"===k.xValueType)g=!0;for(l=0;l<k.dataPoints.length;l++){"undefined"===typeof k.dataPoints[l].x&&(k.dataPoints[l].x=l);k.dataPoints[l].x.getTime?(g=!0,d=k.dataPoints[l].x.getTime()):d=k.dataPoints[l].x;e=k.dataPoints[l].y;d<c.min&&(c.min=d);d>c.max&&(c.max=d);if(0<l){var s=d-k.dataPoints[l-1].x;0>s&&(s*=-1);c.minDiff>s&&0!==s&&(c.minDiff=s);null!==e&&null!==k.dataPoints[l-1].y&&
(s=e-k.dataPoints[l-1].y,0>s&&(s*=-1),b.minDiff>s&&0!==s&&(b.minDiff=s))}if(!(d<q)||n){if(!n&&(n=!0,0<l)){l-=2;continue}if(d>r&&!m)m=!0;else if(d>r&&m)continue;k.dataPoints[l].label&&(a.axisX.labels[d]=k.dataPoints[l].label);d<c.viewPortMin&&(c.viewPortMin=d);d>c.viewPortMax&&(c.viewPortMax=d);null!==e&&(a.yTotals[d]=(a.yTotals[d]?a.yTotals[d]:0)+Math.abs(e),0<=e?f[d]=f[d]?f[d]+e:e:h[d]=h[d]?h[d]+e:e)}}this.plotInfo.axisXValueType=k.xValueType=g?"dateTime":"number"}for(l in f)f.hasOwnProperty(l)&&
!isNaN(l)&&(a=f[l],a<b.min&&(b.min=a),a>b.max&&(b.max=a),l<c.viewPortMin||l>c.viewPortMax||(a<b.viewPortMin&&(b.viewPortMin=a),a>b.viewPortMax&&(b.viewPortMax=a)));for(l in h)h.hasOwnProperty(l)&&!isNaN(l)&&(a=h[l],a<b.min&&(b.min=a),a>b.max&&(b.max=a),l<c.viewPortMin||l>c.viewPortMax||(a<b.viewPortMin&&(b.viewPortMin=a),a>b.viewPortMax&&(b.viewPortMax=a)))}};u.prototype._processStacked100PlotUnit=function(a){if(a.dataSeriesIndexes&&!(1>a.dataSeriesIndexes.length)){for(var b=a.axisY.dataInfo,c=a.axisX.dataInfo,
d,e,g=!1,f=!1,h=!1,p=[],k=0;k<a.dataSeriesIndexes.length;k++){var l=this.data[a.dataSeriesIndexes[k]],n=0,m=!1,q=!1;if("normal"===l.axisPlacement||"xySwapped"===l.axisPlacement)var r=this.sessionVariables.axisX.newViewportMinimum?this.sessionVariables.axisX.newViewportMinimum:this._options.axisX&&this._options.axisX.viewportMinimum?this._options.axisX.viewportMinimum:this._options.axisX&&this._options.axisX.minimum?this._options.axisX.minimum:-Infinity,s=this.sessionVariables.axisX.newViewportMaximum?
this.sessionVariables.axisX.newViewportMaximum:this._options.axisX&&this._options.axisX.viewportMaximum?this._options.axisX.viewportMaximum:this._options.axisX&&this._options.axisX.maximum?this._options.axisX.maximum:Infinity;if(l.dataPoints[n].x&&l.dataPoints[n].x.getTime||"dateTime"===l.xValueType)g=!0;for(n=0;n<l.dataPoints.length;n++){"undefined"===typeof l.dataPoints[n].x&&(l.dataPoints[n].x=n);l.dataPoints[n].x.getTime?(g=!0,d=l.dataPoints[n].x.getTime()):d=l.dataPoints[n].x;e=l.dataPoints[n].y;
d<c.min&&(c.min=d);d>c.max&&(c.max=d);if(0<n){var w=d-l.dataPoints[n-1].x;0>w&&(w*=-1);c.minDiff>w&&0!==w&&(c.minDiff=w);null!==e&&null!==l.dataPoints[n-1].y&&(w=e-l.dataPoints[n-1].y,0>w&&(w*=-1),b.minDiff>w&&0!==w&&(b.minDiff=w))}if(!(d<r)||m){if(!m&&(m=!0,0<n)){n-=2;continue}if(d>s&&!q)q=!0;else if(d>s&&q)continue;l.dataPoints[n].label&&(a.axisX.labels[d]=l.dataPoints[n].label);d<c.viewPortMin&&(c.viewPortMin=d);d>c.viewPortMax&&(c.viewPortMax=d);null!==e&&(a.yTotals[d]=(a.yTotals[d]?a.yTotals[d]:
0)+Math.abs(e),0<=e?f=!0:h=!0,p[d]=p[d]?p[d]+Math.abs(e):Math.abs(e))}}this.plotInfo.axisXValueType=l.xValueType=g?"dateTime":"number"}f&&!h?(b.max=99,b.min=1):f&&h?(b.max=99,b.min=-99):!f&&h&&(b.max=-1,b.min=-99);b.viewPortMin=b.min;b.viewPortMax=b.max;a.dataPointYSums=p}};u.prototype._processMultiYPlotUnit=function(a){if(a.dataSeriesIndexes&&!(1>a.dataSeriesIndexes.length))for(var b=a.axisY.dataInfo,c=a.axisX.dataInfo,d,e,g,f,h=!1,p=0;p<a.dataSeriesIndexes.length;p++){var k=this.data[a.dataSeriesIndexes[p]],
l=0,n=!1,m=!1;if("normal"===k.axisPlacement||"xySwapped"===k.axisPlacement)var q=this.sessionVariables.axisX.newViewportMinimum?this.sessionVariables.axisX.newViewportMinimum:this._options.axisX&&this._options.axisX.viewportMinimum?this._options.axisX.viewportMinimum:this._options.axisX&&this._options.axisX.minimum?this._options.axisX.minimum:-Infinity,r=this.sessionVariables.axisX.newViewportMaximum?this.sessionVariables.axisX.newViewportMaximum:this._options.axisX&&this._options.axisX.viewportMaximum?
this._options.axisX.viewportMaximum:this._options.axisX&&this._options.axisX.maximum?this._options.axisX.maximum:Infinity;if(k.dataPoints[l].x&&k.dataPoints[l].x.getTime||"dateTime"===k.xValueType)h=!0;for(l=0;l<k.dataPoints.length;l++){"undefined"===typeof k.dataPoints[l].x&&(k.dataPoints[l].x=l);k.dataPoints[l].x.getTime?(h=!0,d=k.dataPoints[l].x.getTime()):d=k.dataPoints[l].x;(e=k.dataPoints[l].y)&&e.length&&(g=Math.min.apply(null,e),f=Math.max.apply(null,e));d<c.min&&(c.min=d);d>c.max&&(c.max=
d);g<b.min&&(b.min=g);f>b.max&&(b.max=f);if(0<l){var s=d-k.dataPoints[l-1].x;0>s&&(s*=-1);c.minDiff>s&&0!==s&&(c.minDiff=s);null!==e[0]&&null!==k.dataPoints[l-1].y[0]&&(s=e[0]-k.dataPoints[l-1].y[0],0>s&&(s*=-1),b.minDiff>s&&0!==s&&(b.minDiff=s))}if(!(d<q)||n){if(!n&&(n=!0,0<l)){l-=2;continue}if(d>r&&!m)m=!0;else if(d>r&&m)continue;k.dataPoints[l].label&&(a.axisX.labels[d]=k.dataPoints[l].label);d<c.viewPortMin&&(c.viewPortMin=d);d>c.viewPortMax&&(c.viewPortMax=d);null!==e&&(g<b.viewPortMin&&(b.viewPortMin=
g),f>b.viewPortMax&&(b.viewPortMax=f))}}this.plotInfo.axisXValueType=k.xValueType=h?"dateTime":"number"}};u.prototype.getDataPointAtXY=function(a,b,c){c=c||!1;for(var d=[],e=this._dataInRenderedOrder.length-1;0<=e;e--){var g=null;(g=this._dataInRenderedOrder[e].getDataPointAtXY(a,b,c))&&d.push(g)}a=null;b=!1;for(c=0;c<d.length;c++)if("line"===d[c].dataSeries.type||"stepLine"===d[c].dataSeries.type||"area"===d[c].dataSeries.type||"stepArea"===d[c].dataSeries.type)if(e=N("markerSize",d[c].dataPoint,
d[c].dataSeries)||8,d[c].distance<=e/2){b=!0;break}for(c=0;c<d.length;c++)b&&"line"!==d[c].dataSeries.type&&"stepLine"!==d[c].dataSeries.type&&"area"!==d[c].dataSeries.type&&"stepArea"!==d[c].dataSeries.type||(a?d[c].distance<=a.distance&&(a=d[c]):a=d[c]);return a};u.prototype.getObjectAtXY=function(a,b,c){var d=null;if(c=this.getDataPointAtXY(a,b,c||!1))d=c.dataSeries.dataPointIds[c.dataPointIndex];else if(t)d=va(a,b,this._eventManager.ghostCtx);else for(c=0;c<this.legend.items.length;c++){var e=
this.legend.items[c];a>=e.x1&&(a<=e.x2&&b>=e.y1&&b<=e.y2)&&(d=e.id)}return d};u.prototype.getAutoFontSize=function(a,b,c){a/=400;return Math.round(Math.min(this.width,this.height)*a)};u.prototype.resetOverlayedCanvas=function(){this.overlaidCanvasCtx.clearRect(0,0,this.width,this.height)};u.prototype.clearCanvas=function(){this.ctx.clearRect(0,0,this.width,this.height);this.backgroundColor&&(this.ctx.fillStyle=this.backgroundColor,this.ctx.fillRect(0,0,this.width,this.height))};u.prototype.attachEvent=
function(a){this._events.push(a)};u.prototype._touchEventHandler=function(a){if(a.changedTouches&&this.interactivityEnabled){var b=[],c=a.changedTouches,d=c?c[0]:a,e=null;switch(a.type){case "touchstart":case "MSPointerDown":b=["mousemove","mousedown"];this._lastTouchData=ma(d);this._lastTouchData.time=new Date;break;case "touchmove":case "MSPointerMove":b=["mousemove"];break;case "touchend":case "MSPointerUp":b="touchstart"===this._lastTouchEventType||"MSPointerDown"===this._lastTouchEventType?["mouseup",
"click"]:["mouseup"];break;default:return}if(!(c&&1<c.length)){e=ma(d);e.time=new Date;try{var g=e.y-this._lastTouchData.y,f=e.time-this._lastTouchData.time;if(15<Math.abs(g)&&(this._lastTouchData.scroll||200>f)){this._lastTouchData.scroll=!0;var h=window.parent||window;h&&h.scrollBy&&h.scrollBy(0,-g)}}catch(p){}this._lastTouchEventType=a.type;if(this._lastTouchData.scroll&&this.zoomEnabled)this.isDrag&&this.resetOverlayedCanvas(),this.isDrag=!1;else for(c=0;c<b.length;c++)e=b[c],g=document.createEvent("MouseEvent"),
g.initMouseEvent(e,!0,!0,window,1,d.screenX,d.screenY,d.clientX,d.clientY,!1,!1,!1,!1,0,null),d.target.dispatchEvent(g),a.preventManipulation&&a.preventManipulation(),a.preventDefault&&a.preventDefault()}}};u.prototype._dispatchRangeEvent=function(a,b){var c={};c.chart=this._publicChartReference;c.type=a;c.trigger=b;var d=[];this.axisX&&d.push("axisX");this.axisY&&d.push("axisY");this.axisY2&&d.push("axisY2");for(var e=0;e<d.length;e++)c[d[e]]={viewportMinimum:this[d[e]].sessionVariables.newViewportMinimum,
viewportMaximum:this[d[e]].sessionVariables.newViewportMaximum};this.dispatchEvent(a,c,this._publicChartReference)};u.prototype._mouseEventHandler=function(a){if(this.interactivityEnabled)if(this._ignoreNextEvent)this._ignoreNextEvent=!1;else{a.preventManipulation&&a.preventManipulation();a.preventDefault&&a.preventDefault();"undefined"===typeof a.target&&a.srcElement&&(a.target=a.srcElement);var b=ma(a),c=a.type,d,e;a.which?e=3==a.which:a.button&&(e=2==a.button);if(!e){if(u.capturedEventParam)d=
u.capturedEventParam,"mouseup"===c&&(u.capturedEventParam=null,d.chart.overlaidCanvas.releaseCapture?d.chart.overlaidCanvas.releaseCapture():document.body.removeEventListener("mouseup",d.chart._mouseEventHandler,!1)),d.hasOwnProperty(c)&&d[c].call(d.context,b.x,b.y);else if(this._events){for(e=0;e<this._events.length;e++)if(this._events[e].hasOwnProperty(c)){d=this._events[e];var g=d.bounds;if(b.x>=g.x1&&b.x<=g.x2&&b.y>=g.y1&&b.y<=g.y2){d[c].call(d.context,b.x,b.y);"mousedown"===c&&!0===d.capture?
(u.capturedEventParam=d,this.overlaidCanvas.setCapture?this.overlaidCanvas.setCapture():document.body.addEventListener("mouseup",this._mouseEventHandler,!1)):"mouseup"===c&&(d.chart.overlaidCanvas.releaseCapture?d.chart.overlaidCanvas.releaseCapture():document.body.removeEventListener("mouseup",this._mouseEventHandler,!1));break}else d=null}a.target.style.cursor=d&&d.cursor?d.cursor:this._defaultCursor}this._toolTip&&this._toolTip.enabled&&(c=this.plotArea,(b.x<c.x1||b.x>c.x2||b.y<c.y1||b.y>c.y2)&&
this._toolTip.hide());this.isDrag&&this.zoomEnabled||!this._eventManager||this._eventManager.mouseEventHandler(a)}}};u.prototype._plotAreaMouseDown=function(a,b){this.isDrag=!0;this.dragStartPoint={x:a,y:b}};u.prototype._plotAreaMouseUp=function(a,b){if(("normal"===this.plotInfo.axisPlacement||"xySwapped"===this.plotInfo.axisPlacement)&&this.isDrag){var c=b-this.dragStartPoint.y,d=a-this.dragStartPoint.x,e=0<=this.zoomType.indexOf("x"),g=0<=this.zoomType.indexOf("y"),f=!1;this.resetOverlayedCanvas();
if("xySwapped"===this.plotInfo.axisPlacement)var h=g,g=e,e=h;if(this.panEnabled||this.zoomEnabled){if(this.panEnabled)for(e=g=0;e<this._axes.length;e++)c=this._axes[e],c.viewportMinimum<c.minimum?(g=c.minimum-c.viewportMinimum,c.sessionVariables.newViewportMinimum=c.viewportMinimum+g,c.sessionVariables.newViewportMaximum=c.viewportMaximum+g,f=!0):c.viewportMaximum>c.maximum&&(g=c.viewportMaximum-c.maximum,c.sessionVariables.newViewportMinimum=c.viewportMinimum-g,c.sessionVariables.newViewportMaximum=
c.viewportMaximum-g,f=!0);else if((!e||2<Math.abs(d))&&(!g||2<Math.abs(c))&&this.zoomEnabled){if(!this.dragStartPoint)return;c=e?this.dragStartPoint.x:this.plotArea.x1;d=g?this.dragStartPoint.y:this.plotArea.y1;e=e?a:this.plotArea.x2;g=g?b:this.plotArea.y2;2<Math.abs(c-e)&&2<Math.abs(d-g)&&this._zoomPanToSelectedRegion(c,d,e,g)&&(f=!0)}f&&(this._ignoreNextEvent=!0,this._dispatchRangeEvent("rangeChanging","zoom"),this.render(),this._dispatchRangeEvent("rangeChanged","zoom"),f&&(this.zoomEnabled&&"none"===
this._zoomButton.style.display)&&(ga(this._zoomButton,this._resetButton),P(this,this._zoomButton,"pan"),P(this,this._resetButton,"reset")))}}this.isDrag=!1};u.prototype._plotAreaMouseMove=function(a,b){if(this.isDrag&&"none"!==this.plotInfo.axisPlacement){var c=0,d=0,e=c=null,e=0<=this.zoomType.indexOf("x"),g=0<=this.zoomType.indexOf("y");"xySwapped"===this.plotInfo.axisPlacement&&(c=g,g=e,e=c);c=this.dragStartPoint.x-a;d=this.dragStartPoint.y-b;2<Math.abs(c)&&8>Math.abs(c)&&(this.panEnabled||this.zoomEnabled)?
this._toolTip.hide():this.panEnabled||this.zoomEnabled||this._toolTip.mouseMoveHandler(a,b);(!e||2<Math.abs(c)||!g||2<Math.abs(d))&&(this.panEnabled||this.zoomEnabled)&&(this.panEnabled?(e={x1:e?this.plotArea.x1+c:this.plotArea.x1,y1:g?this.plotArea.y1+d:this.plotArea.y1,x2:e?this.plotArea.x2+c:this.plotArea.x2,y2:g?this.plotArea.y2+d:this.plotArea.y2},this._zoomPanToSelectedRegion(e.x1,e.y1,e.x2,e.y2,!0)&&(this._dispatchRangeEvent("rangeChanging","pan"),this.render(),this._dispatchRangeEvent("rangeChanged",
"pan"),this.dragStartPoint.x=a,this.dragStartPoint.y=b)):this.zoomEnabled&&(this.resetOverlayedCanvas(),c=this.overlaidCanvasCtx.globalAlpha,this.overlaidCanvasCtx.globalAlpha=0.7,this.overlaidCanvasCtx.fillStyle="#A0ABB8",this.overlaidCanvasCtx.fillRect(e?this.dragStartPoint.x:this.plotArea.x1,g?this.dragStartPoint.y:this.plotArea.y1,e?a-this.dragStartPoint.x:this.plotArea.x2-this.plotArea.x1,g?b-this.dragStartPoint.y:this.plotArea.y2-this.plotArea.y1),this.overlaidCanvasCtx.globalAlpha=c))}else this._toolTip.mouseMoveHandler(a,
b)};u.prototype._zoomPanToSelectedRegion=function(a,b,c,d,e){e=e||!1;var g=0<=this.zoomType.indexOf("x"),f=0<=this.zoomType.indexOf("y"),h=!1,p=[],k=[];this.axisX&&g&&p.push(this.axisX);this.axisY&&f&&p.push(this.axisY);this.axisY2&&f&&p.push(this.axisY2);g=[];for(f=0;f<p.length;f++){var l=p[f],n=l.convertPixelToValue({x:a,y:b}),m=l.convertPixelToValue({x:c,y:d});if(n>m)var q=m,m=n,n=q;if(isFinite(l.dataInfo.minDiff))if(!(Math.abs(m-n)<3*Math.abs(l.dataInfo.minDiff)||n<l.minimum||m>l.maximum))k.push(l),
g.push({val1:n,val2:m}),h=!0;else if(!e){h=!1;break}}if(h)for(f=0;f<k.length;f++)l=k[f],a=g[f],l.setViewPortRange(a.val1,a.val2);return h};u.prototype.preparePlotArea=function(){var a=this.plotArea,b=this.axisY?this.axisY:this.axisY2;!t&&(0<a.x1||0<a.y1)&&a.ctx.translate(a.x1,a.y1);this.axisX&&b?(a.x1=this.axisX.lineCoordinates.x1<this.axisX.lineCoordinates.x2?this.axisX.lineCoordinates.x1:b.lineCoordinates.x1,a.y1=this.axisX.lineCoordinates.y1<b.lineCoordinates.y1?this.axisX.lineCoordinates.y1:b.lineCoordinates.y1,
a.x2=this.axisX.lineCoordinates.x2>b.lineCoordinates.x2?this.axisX.lineCoordinates.x2:b.lineCoordinates.x2,a.y2=this.axisX.lineCoordinates.y2>this.axisX.lineCoordinates.y1?this.axisX.lineCoordinates.y2:b.lineCoordinates.y2,a.width=a.x2-a.x1,a.height=a.y2-a.y1):(b=this.layoutManager.getFreeSpace(),a.x1=b.x1,a.x2=b.x2,a.y1=b.y1,a.y2=b.y2,a.width=b.width,a.height=b.height);t||(a.canvas.width=a.width,a.canvas.height=a.height,a.canvas.style.left=a.x1+"px",a.canvas.style.top=a.y1+"px",(0<a.x1||0<a.y1)&&
a.ctx.translate(-a.x1,-a.y1));a.layoutManager=new Y(a.x1,a.y1,a.x2,a.y2,2)};u.prototype.getPixelCoordinatesOnPlotArea=function(a,b){return{x:this.axisX.getPixelCoordinatesOnAxis(a).x,y:this.axisY.getPixelCoordinatesOnAxis(b).y}};u.prototype.renderIndexLabels=function(a){a=a||this.plotArea.ctx;for(var b=this.plotArea,c=0,d=0,e=0,g=0,f=0,h=d=g=e=0,p=0;p<this._indexLabels.length;p++){var k=this._indexLabels[p],f=k.chartType.toLowerCase(),l,n,m=N("indexLabelFontColor",k.dataPoint,k.dataSeries),h=N("indexLabelFontSize",
k.dataPoint,k.dataSeries);l=N("indexLabelFontFamily",k.dataPoint,k.dataSeries);n=N("indexLabelFontStyle",k.dataPoint,k.dataSeries);var g=N("indexLabelFontWeight",k.dataPoint,k.dataSeries),d=N("indexLabelBackgroundColor",k.dataPoint,k.dataSeries),e=N("indexLabelMaxWidth",k.dataPoint,k.dataSeries),q=N("indexLabelWrap",k.dataPoint,k.dataSeries),r={percent:null,total:null},s=null;if(0<=k.dataSeries.type.indexOf("stacked")||"pie"===k.dataSeries.type||"doughnut"===k.dataSeries.type)r=this.getPercentAndTotal(k.dataSeries,
k.dataPoint);if(k.dataSeries.indexLabelFormatter||k.dataPoint.indexLabelFormatter)s={chart:this._options,dataSeries:k.dataSeries,dataPoint:k.dataPoint,index:k.indexKeyword,total:r.total,percent:r.percent};var w=k.dataPoint.indexLabelFormatter?k.dataPoint.indexLabelFormatter(s):k.dataPoint.indexLabel?this.replaceKeywordsWithValue(k.dataPoint.indexLabel,k.dataPoint,k.dataSeries,null,k.indexKeyword):k.dataSeries.indexLabelFormatter?k.dataSeries.indexLabelFormatter(s):k.dataSeries.indexLabel?this.replaceKeywordsWithValue(k.dataSeries.indexLabel,
k.dataPoint,k.dataSeries,null,k.indexKeyword):null;if(null!==w&&""!==w){var c=N("indexLabelPlacement",k.dataPoint,k.dataSeries),r=N("indexLabelOrientation",k.dataPoint,k.dataSeries),s=k.direction,v=k.dataSeries.axisX,x=k.dataSeries.axisY,m=new H(a,{x:0,y:0,maxWidth:e?e:0.5*this.width,maxHeight:q?5*h:1.5*h,angle:"horizontal"===r?0:-90,text:w,padding:0,backgroundColor:d,horizontalAlign:"left",fontSize:h,fontFamily:l,fontWeight:g,fontColor:m,fontStyle:n,textBaseline:"top"});m.measureText();if(0<=f.indexOf("line")||
0<=f.indexOf("area")||0<=f.indexOf("bubble")||0<=f.indexOf("scatter")){if(k.dataPoint.x<v.viewportMinimum||k.dataPoint.x>v.viewportMaximum||k.dataPoint.y<x.viewportMinimum||k.dataPoint.y>x.viewportMaximum)continue}else if(k.dataPoint.x<v.viewportMinimum||k.dataPoint.x>v.viewportMaximum)continue;e=g=2;"horizontal"===r?(d=m.width,h=m.height):(h=m.width,d=m.height);if("normal"===this.plotInfo.axisPlacement){if(0<=f.indexOf("line")||0<=f.indexOf("area"))c="auto",g=4;else if(0<=f.indexOf("stacked"))"auto"===
c&&(c="inside");else if("bubble"===f||"scatter"===f)c="inside";l=k.point.x-d/2;"inside"!==c?(d=b.y1,e=b.y2,0<s?(n=k.point.y-h-g,n<d&&(n="auto"===c?Math.max(k.point.y,d)+g:d+g)):(n=k.point.y+g,n>e-h-g&&(n="auto"===c?Math.min(k.point.y,e)-h-g:e-h-g))):(d=Math.max(k.bounds.y1,b.y1),e=Math.min(k.bounds.y2,b.y2),c=0<=f.indexOf("range")?0<s?Math.max(k.bounds.y1,b.y1)+h/2+g:Math.min(k.bounds.y2,b.y2)-h/2-g:(Math.max(k.bounds.y1,b.y1)+Math.min(k.bounds.y2,b.y2))/2,0<s?(n=Math.max(k.point.y,c)-h/2,n<d&&("bubble"===
f||"scatter"===f)&&(n=Math.max(k.point.y-h-g,b.y1+g))):(n=Math.min(k.point.y,c)-h/2,n>e-h-g&&("bubble"===f||"scatter"===f)&&(n=Math.min(k.point.y+g,b.y2-h-g))),n=Math.min(n,e-h))}else 0<=f.indexOf("line")||0<=f.indexOf("area")||0<=f.indexOf("scatter")?(c="auto",e=4):0<=f.indexOf("stacked")?"auto"===c&&(c="inside"):"bubble"===f&&(c="inside"),n=k.point.y-h/2,"inside"!==c?(g=b.x1,f=b.x2,0>s?(l=k.point.x-d-e,l<g&&(l="auto"===c?Math.max(k.point.x,g)+e:g+e)):(l=k.point.x+e,l>f-d-e&&(l="auto"===c?Math.min(k.point.x,
f)-d-e:f-d-e))):(g=Math.max(k.bounds.x1,b.x1),Math.min(k.bounds.x2,b.x2),c=0<=f.indexOf("range")?0>s?Math.max(k.bounds.x1,b.x1)+d/2+e:Math.min(k.bounds.x2,b.x2)-d/2-e:(Math.max(k.bounds.x1,b.x1)+Math.min(k.bounds.x2,b.x2))/2,l=0>s?Math.max(k.point.x,c)-d/2:Math.min(k.point.x,c)-d/2,l=Math.max(l,g));"vertical"===r&&(n+=h);m.x=l;m.y=n;m.render(!0)}}return{source:a,dest:this.plotArea.ctx,animationCallback:A.fadeInAnimation,easingFunction:A.easing.easeInQuad,animationBase:0,startTimePercent:0.7}};u.prototype.renderLine=
function(a){var b=a.targetCanvasCtx||this.plotArea.ctx;if(!(0>=a.dataSeriesIndexes.length)){var c=this._eventManager.ghostCtx;b.save();var d=this.plotArea;b.beginPath();b.rect(d.x1,d.y1,d.width,d.height);b.clip();for(var d=[],e=0;e<a.dataSeriesIndexes.length;e++){var g=a.dataSeriesIndexes[e],f=this.data[g];b.lineWidth=f.lineThickness;var h=f.dataPoints;b.setLineDash&&b.setLineDash(M(f.lineDashType,f.lineThickness));var p=f.id;this._eventManager.objectMap[p]={objectType:"dataSeries",dataSeriesIndex:g};
p=B(p);c.strokeStyle=p;c.lineWidth=0<f.lineThickness?Math.max(f.lineThickness,4):0;p=f._colorSet[0];b.strokeStyle=p;var k=!0,l=0,n,m;b.beginPath();if(0<h.length){for(var q=!1,l=0;l<h.length;l++)if(n=h[l].x.getTime?h[l].x.getTime():h[l].x,!(n<a.axisX.dataInfo.viewPortMin||n>a.axisX.dataInfo.viewPortMax))if("number"!==typeof h[l].y)0<l&&(b.stroke(),t&&c.stroke()),q=!0;else{n=a.axisX.conversionParameters.reference+a.axisX.conversionParameters.pixelPerUnit*(n-a.axisX.conversionParameters.minimum)+0.5<<
0;m=a.axisY.conversionParameters.reference+a.axisY.conversionParameters.pixelPerUnit*(h[l].y-a.axisY.conversionParameters.minimum)+0.5<<0;var r=f.dataPointIds[l];this._eventManager.objectMap[r]={id:r,objectType:"dataPoint",dataSeriesIndex:g,dataPointIndex:l,x1:n,y1:m};k||q?(b.beginPath(),b.moveTo(n,m),t&&(c.beginPath(),c.moveTo(n,m)),q=k=!1):(b.lineTo(n,m),t&&c.lineTo(n,m),0==l%500&&(b.stroke(),b.beginPath(),b.moveTo(n,m),t&&(c.stroke(),c.beginPath(),c.moveTo(n,m))));if(0<h[l].markerSize||0<f.markerSize){var s=
f.getMarkerProperties(l,n,m,b);d.push(s);r=B(r);t&&d.push({x:n,y:m,ctx:c,type:s.type,size:s.size,color:r,borderColor:r,borderThickness:s.borderThickness})}(h[l].indexLabel||f.indexLabel||h[l].indexLabelFormatter||f.indexLabelFormatter)&&this._indexLabels.push({chartType:"line",dataPoint:h[l],dataSeries:f,point:{x:n,y:m},direction:0<=h[l].y?1:-1,color:p})}b.stroke();t&&c.stroke()}}K.drawMarkers(d);b.restore();b.beginPath();t&&c.beginPath();return{source:b,dest:this.plotArea.ctx,animationCallback:A.xClipAnimation,
easingFunction:A.easing.linear,animationBase:0}}};u.prototype.renderStepLine=function(a){var b=a.targetCanvasCtx||this.plotArea.ctx;if(!(0>=a.dataSeriesIndexes.length)){var c=this._eventManager.ghostCtx;b.save();var d=this.plotArea;b.beginPath();b.rect(d.x1,d.y1,d.width,d.height);b.clip();for(var d=[],e=0;e<a.dataSeriesIndexes.length;e++){var g=a.dataSeriesIndexes[e],f=this.data[g];b.lineWidth=f.lineThickness;var h=f.dataPoints;b.setLineDash&&b.setLineDash(M(f.lineDashType,f.lineThickness));var p=
f.id;this._eventManager.objectMap[p]={objectType:"dataSeries",dataSeriesIndex:g};p=B(p);c.strokeStyle=p;c.lineWidth=0<f.lineThickness?Math.max(f.lineThickness,4):0;p=f._colorSet[0];b.strokeStyle=p;var k=!0,l=0,n,m;b.beginPath();if(0<h.length){for(var q=!1,l=0;l<h.length;l++)if(n=h[l].getTime?h[l].x.getTime():h[l].x,!(n<a.axisX.dataInfo.viewPortMin||n>a.axisX.dataInfo.viewPortMax))if("number"!==typeof h[l].y)0<l&&(b.stroke(),t&&c.stroke()),q=!0;else{var r=m;n=a.axisX.conversionParameters.reference+
a.axisX.conversionParameters.pixelPerUnit*(n-a.axisX.conversionParameters.minimum)+0.5<<0;m=a.axisY.conversionParameters.reference+a.axisY.conversionParameters.pixelPerUnit*(h[l].y-a.axisY.conversionParameters.minimum)+0.5<<0;var s=f.dataPointIds[l];this._eventManager.objectMap[s]={id:s,objectType:"dataPoint",dataSeriesIndex:g,dataPointIndex:l,x1:n,y1:m};k||q?(b.beginPath(),b.moveTo(n,m),t&&(c.beginPath(),c.moveTo(n,m)),q=k=!1):(b.lineTo(n,r),t&&c.lineTo(n,r),b.lineTo(n,m),t&&c.lineTo(n,m),0==l%500&&
(b.stroke(),b.beginPath(),b.moveTo(n,m),t&&(c.stroke(),c.beginPath(),c.moveTo(n,m))));if(0<h[l].markerSize||0<f.markerSize)r=f.getMarkerProperties(l,n,m,b),d.push(r),s=B(s),t&&d.push({x:n,y:m,ctx:c,type:r.type,size:r.size,color:s,borderColor:s,borderThickness:r.borderThickness});(h[l].indexLabel||f.indexLabel||h[l].indexLabelFormatter||f.indexLabelFormatter)&&this._indexLabels.push({chartType:"stepLine",dataPoint:h[l],dataSeries:f,point:{x:n,y:m},direction:0<=h[l].y?1:-1,color:p})}b.stroke();t&&c.stroke()}}K.drawMarkers(d);
b.restore();b.beginPath();t&&c.beginPath();return{source:b,dest:this.plotArea.ctx,animationCallback:A.xClipAnimation,easingFunction:A.easing.linear,animationBase:0}}};u.prototype.renderSpline=function(a){function b(a){a=ha(a,2);if(0<a.length){c.beginPath();t&&d.beginPath();c.moveTo(a[0].x,a[0].y);t&&d.moveTo(a[0].x,a[0].y);for(var b=0;b<a.length-3;b+=3)c.bezierCurveTo(a[b+1].x,a[b+1].y,a[b+2].x,a[b+2].y,a[b+3].x,a[b+3].y),t&&d.bezierCurveTo(a[b+1].x,a[b+1].y,a[b+2].x,a[b+2].y,a[b+3].x,a[b+3].y),0<
b&&0===b%3E3&&(c.stroke(),c.beginPath(),c.moveTo(a[b+3].x,a[b+3].y),t&&(d.stroke(),d.beginPath(),d.moveTo(a[b+3].x,a[b+3].y)));c.stroke();t&&d.stroke()}}var c=a.targetCanvasCtx||this.plotArea.ctx;if(!(0>=a.dataSeriesIndexes.length)){var d=this._eventManager.ghostCtx;c.save();var e=this.plotArea;c.beginPath();c.rect(e.x1,e.y1,e.width,e.height);c.clip();for(var e=[],g=0;g<a.dataSeriesIndexes.length;g++){var f=a.dataSeriesIndexes[g],h=this.data[f];c.lineWidth=h.lineThickness;var p=h.dataPoints;c.setLineDash&&
c.setLineDash(M(h.lineDashType,h.lineThickness));var k=h.id;this._eventManager.objectMap[k]={objectType:"dataSeries",dataSeriesIndex:f};k=B(k);d.strokeStyle=k;d.lineWidth=0<h.lineThickness?Math.max(h.lineThickness,4):0;k=h._colorSet[0];c.strokeStyle=k;var l=0,n,m,q=[];c.beginPath();if(0<p.length)for(l=0;l<p.length;l++)if(n=p[l].getTime?p[l].x.getTime():p[l].x,!(n<a.axisX.dataInfo.viewPortMin||n>a.axisX.dataInfo.viewPortMax))if("number"!==typeof p[l].y)0<l&&(b(q),q=[]);else{n=a.axisX.conversionParameters.reference+
a.axisX.conversionParameters.pixelPerUnit*(n-a.axisX.conversionParameters.minimum)+0.5<<0;m=a.axisY.conversionParameters.reference+a.axisY.conversionParameters.pixelPerUnit*(p[l].y-a.axisY.conversionParameters.minimum)+0.5<<0;var r=h.dataPointIds[l];this._eventManager.objectMap[r]={id:r,objectType:"dataPoint",dataSeriesIndex:f,dataPointIndex:l,x1:n,y1:m};q[q.length]={x:n,y:m};if(0<p[l].markerSize||0<h.markerSize){var s=h.getMarkerProperties(l,n,m,c);e.push(s);r=B(r);t&&e.push({x:n,y:m,ctx:d,type:s.type,
size:s.size,color:r,borderColor:r,borderThickness:s.borderThickness})}(p[l].indexLabel||h.indexLabel||p[l].indexLabelFormatter||h.indexLabelFormatter)&&this._indexLabels.push({chartType:"spline",dataPoint:p[l],dataSeries:h,point:{x:n,y:m},direction:0<=p[l].y?1:-1,color:k})}b(q)}K.drawMarkers(e);c.restore();c.beginPath();t&&d.beginPath();return{source:c,dest:this.plotArea.ctx,animationCallback:A.xClipAnimation,easingFunction:A.easing.linear,animationBase:0}}};var I=function(a,b,c,d,e,g,f,h,p,k,l,n,
m){"undefined"===typeof m&&(m=1);f=f||0;h=h||"black";var q=15<d-b&&15<e-c?8:0.35*Math.min(d-b,e-c);a.beginPath();a.moveTo(b,c);a.save();a.fillStyle=g;a.globalAlpha=m;a.fillRect(b,c,d-b,e-c);a.globalAlpha=1;0<f&&(m=0===f%2?0:0.5,a.beginPath(),a.lineWidth=f,a.strokeStyle=h,a.moveTo(b,c),a.rect(b-m,c-m,d-b+2*m,e-c+2*m),a.stroke());a.restore();!0===p&&(a.save(),a.beginPath(),a.moveTo(b,c),a.lineTo(b+q,c+q),a.lineTo(d-q,c+q),a.lineTo(d,c),a.closePath(),f=a.createLinearGradient((d+b)/2,c+q,(d+b)/2,c),f.addColorStop(0,
g),f.addColorStop(1,"rgba(255, 255, 255, .4)"),a.fillStyle=f,a.fill(),a.restore());!0===k&&(a.save(),a.beginPath(),a.moveTo(b,e),a.lineTo(b+q,e-q),a.lineTo(d-q,e-q),a.lineTo(d,e),a.closePath(),f=a.createLinearGradient((d+b)/2,e-q,(d+b)/2,e),f.addColorStop(0,g),f.addColorStop(1,"rgba(255, 255, 255, .4)"),a.fillStyle=f,a.fill(),a.restore());!0===l&&(a.save(),a.beginPath(),a.moveTo(b,c),a.lineTo(b+q,c+q),a.lineTo(b+q,e-q),a.lineTo(b,e),a.closePath(),f=a.createLinearGradient(b+q,(e+c)/2,b,(e+c)/2),f.addColorStop(0,
g),f.addColorStop(1,"rgba(255, 255, 255, 0.1)"),a.fillStyle=f,a.fill(),a.restore());!0===n&&(a.save(),a.beginPath(),a.moveTo(d,c),a.lineTo(d-q,c+q),a.lineTo(d-q,e-q),a.lineTo(d,e),f=a.createLinearGradient(d-q,(e+c)/2,d,(e+c)/2),f.addColorStop(0,g),f.addColorStop(1,"rgba(255, 255, 255, 0.1)"),a.fillStyle=f,f.addColorStop(0,g),f.addColorStop(1,"rgba(255, 255, 255, 0.1)"),a.fillStyle=f,a.fill(),a.closePath(),a.restore())};u.prototype.renderColumn=function(a){var b=a.targetCanvasCtx||this.plotArea.ctx;
if(!(0>=a.dataSeriesIndexes.length)){var c=null,d=this.plotArea,e=0,g,f,h,p=a.axisY.conversionParameters.reference+a.axisY.conversionParameters.pixelPerUnit*(0-a.axisY.conversionParameters.minimum)<<0,e=this.dataPointMaxWidth?this.dataPointMaxWidth:Math.min(0.15*this.width,0.9*(this.plotArea.width/a.plotType.totalDataSeries))<<0,k=a.axisX.dataInfo.minDiff,l=0.9*(d.width/Math.abs(a.axisX.viewportMaximum-a.axisX.viewportMinimum)*Math.abs(k)/a.plotType.totalDataSeries)<<0;l>e?l=e:Infinity===k?l=0.9*
(e/a.plotType.totalDataSeries):1>l&&(l=1);b.save();t&&this._eventManager.ghostCtx.save();b.beginPath();b.rect(d.x1,d.y1,d.width,d.height);b.clip();t&&(this._eventManager.ghostCtx.rect(d.x1,d.y1,d.width,d.height),this._eventManager.ghostCtx.clip());for(d=0;d<a.dataSeriesIndexes.length;d++){var k=a.dataSeriesIndexes[d],n=this.data[k],m=n.dataPoints;if(0<m.length)for(var q=5<l&&n.bevelEnabled?!0:!1,e=0;e<m.length;e++)if(m[e].getTime?h=m[e].x.getTime():h=m[e].x,!(h<a.axisX.dataInfo.viewPortMin||h>a.axisX.dataInfo.viewPortMax)&&
"number"===typeof m[e].y){g=a.axisX.conversionParameters.reference+a.axisX.conversionParameters.pixelPerUnit*(h-a.axisX.conversionParameters.minimum)+0.5<<0;f=a.axisY.conversionParameters.reference+a.axisY.conversionParameters.pixelPerUnit*(m[e].y-a.axisY.conversionParameters.minimum)+0.5<<0;g=g-a.plotType.totalDataSeries*l/2+(a.previousDataSeriesCount+d)*l<<0;var r=g+l<<0,s;0<=m[e].y?s=p:(s=f,f=p);f>s&&(s=f=s);c=m[e].color?m[e].color:n._colorSet[e%n._colorSet.length];I(b,g,f,r,s,c,0,null,q&&0<=m[e].y,
0>m[e].y&&q,!1,!1,n.fillOpacity);c=n.dataPointIds[e];this._eventManager.objectMap[c]={id:c,objectType:"dataPoint",dataSeriesIndex:k,dataPointIndex:e,x1:g,y1:f,x2:r,y2:s};c=B(c);t&&I(this._eventManager.ghostCtx,g,f,r,s,c,0,null,!1,!1,!1,!1);(m[e].indexLabel||n.indexLabel||m[e].indexLabelFormatter||n.indexLabelFormatter)&&this._indexLabels.push({chartType:"column",dataPoint:m[e],dataSeries:n,point:{x:g+(r-g)/2,y:0<=m[e].y?f:s},direction:0<=m[e].y?1:-1,bounds:{x1:g,y1:Math.min(f,s),x2:r,y2:Math.max(f,
s)},color:c})}}b.restore();t&&this._eventManager.ghostCtx.restore();a=Math.min(p,a.axisY.boundingRect.y2);return{source:b,dest:this.plotArea.ctx,animationCallback:A.yScaleAnimation,easingFunction:A.easing.easeOutQuart,animationBase:a}}};u.prototype.renderStackedColumn=function(a){var b=a.targetCanvasCtx||this.plotArea.ctx;if(!(0>=a.dataSeriesIndexes.length)){var c=null,d=this.plotArea,e=[],g=[],f=0,h,p=a.axisY.conversionParameters.reference+a.axisY.conversionParameters.pixelPerUnit*(0-a.axisY.conversionParameters.minimum)<<
0,f=this.dataPointMaxWidth?this.dataPointMaxWidth:0.15*this.width<<0,k=a.axisX.dataInfo.minDiff,l=0.9*(d.width/Math.abs(a.axisX.viewportMaximum-a.axisX.viewportMinimum)*Math.abs(k)/a.plotType.plotUnits.length)<<0;l>f?l=f:Infinity===k?l=f:1>l&&(l=1);b.save();t&&this._eventManager.ghostCtx.save();b.beginPath();b.rect(d.x1,d.y1,d.width,d.height);b.clip();t&&(this._eventManager.ghostCtx.rect(d.x1,d.y1,d.width,d.height),this._eventManager.ghostCtx.clip());for(k=0;k<a.dataSeriesIndexes.length;k++){var n=
a.dataSeriesIndexes[k],m=this.data[n],q=m.dataPoints;if(0<q.length){var r=5<l&&m.bevelEnabled?!0:!1;b.strokeStyle="#4572A7 ";for(f=0;f<q.length;f++)if(c=q[f].x.getTime?q[f].x.getTime():q[f].x,!(c<a.axisX.dataInfo.viewPortMin||c>a.axisX.dataInfo.viewPortMax)&&"number"===typeof q[f].y){d=a.axisX.conversionParameters.reference+a.axisX.conversionParameters.pixelPerUnit*(c-a.axisX.conversionParameters.minimum)+0.5<<0;h=a.axisY.conversionParameters.reference+a.axisY.conversionParameters.pixelPerUnit*(q[f].y-
a.axisY.conversionParameters.minimum);var s=d-a.plotType.plotUnits.length*l/2+a.index*l<<0,w=s+l<<0,v;if(0<=q[f].y){var x=e[c]?e[c]:0;h-=x;v=p-x;e[c]=x+(v-h)}else x=g[c]?g[c]:0,v=h+x,h=p+x,g[c]=x+(v-h);c=q[f].color?q[f].color:m._colorSet[f%m._colorSet.length];I(b,s,h,w,v,c,0,null,r&&0<=q[f].y,0>q[f].y&&r,!1,!1,m.fillOpacity);c=m.dataPointIds[f];this._eventManager.objectMap[c]={id:c,objectType:"dataPoint",dataSeriesIndex:n,dataPointIndex:f,x1:s,y1:h,x2:w,y2:v};c=B(c);t&&I(this._eventManager.ghostCtx,
s,h,w,v,c,0,null,!1,!1,!1,!1);(q[f].indexLabel||m.indexLabel||q[f].indexLabelFormatter||m.indexLabelFormatter)&&this._indexLabels.push({chartType:"stackedColumn",dataPoint:q[f],dataSeries:m,point:{x:d,y:0<=q[f].y?h:v},direction:0<=q[f].y?1:-1,bounds:{x1:s,y1:Math.min(h,v),x2:w,y2:Math.max(h,v)},color:c})}}}b.restore();t&&this._eventManager.ghostCtx.restore();a=Math.min(p,a.axisY.boundingRect.y2);return{source:b,dest:this.plotArea.ctx,animationCallback:A.yScaleAnimation,easingFunction:A.easing.easeOutQuart,
animationBase:a}}};u.prototype.renderStackedColumn100=function(a){var b=a.targetCanvasCtx||this.plotArea.ctx;if(!(0>=a.dataSeriesIndexes.length)){var c=null,d=this.plotArea,e=[],g=[],f=0,h,p=a.axisY.conversionParameters.reference+a.axisY.conversionParameters.pixelPerUnit*(0-a.axisY.conversionParameters.minimum)<<0,f=this.dataPointMaxWidth?this.dataPointMaxWidth:0.15*this.width<<0,k=a.axisX.dataInfo.minDiff,l=0.9*(d.width/Math.abs(a.axisX.viewportMaximum-a.axisX.viewportMinimum)*Math.abs(k)/a.plotType.plotUnits.length)<<
0;l>f?l=f:Infinity===k?l=f:1>l&&(l=1);b.save();t&&this._eventManager.ghostCtx.save();b.beginPath();b.rect(d.x1,d.y1,d.width,d.height);b.clip();t&&(this._eventManager.ghostCtx.rect(d.x1,d.y1,d.width,d.height),this._eventManager.ghostCtx.clip());for(k=0;k<a.dataSeriesIndexes.length;k++){var n=a.dataSeriesIndexes[k],m=this.data[n],q=m.dataPoints;if(0<q.length)for(var r=5<l&&m.bevelEnabled?!0:!1,f=0;f<q.length;f++)if(c=q[f].x.getTime?q[f].x.getTime():q[f].x,!(c<a.axisX.dataInfo.viewPortMin||c>a.axisX.dataInfo.viewPortMax)&&
"number"===typeof q[f].y){d=a.axisX.conversionParameters.reference+a.axisX.conversionParameters.pixelPerUnit*(c-a.axisX.conversionParameters.minimum)+0.5<<0;h=a.axisY.conversionParameters.reference+a.axisY.conversionParameters.pixelPerUnit*((0!==a.dataPointYSums[c]?100*(q[f].y/a.dataPointYSums[c]):0)-a.axisY.conversionParameters.minimum);var s=d-a.plotType.plotUnits.length*l/2+a.index*l<<0,w=s+l<<0,v;if(0<=q[f].y){var x=e[c]?e[c]:0;h-=x;v=p-x;e[c]=x+(v-h)}else x=g[c]?g[c]:0,v=h+x,h=p+x,g[c]=x+(v-
h);c=q[f].color?q[f].color:m._colorSet[f%m._colorSet.length];I(b,s,h,w,v,c,0,null,r&&0<=q[f].y,0>q[f].y&&r,!1,!1,m.fillOpacity);c=m.dataPointIds[f];this._eventManager.objectMap[c]={id:c,objectType:"dataPoint",dataSeriesIndex:n,dataPointIndex:f,x1:s,y1:h,x2:w,y2:v};c=B(c);t&&I(this._eventManager.ghostCtx,s,h,w,v,c,0,null,!1,!1,!1,!1);(q[f].indexLabel||m.indexLabel||q[f].indexLabelFormatter||m.indexLabelFormatter)&&this._indexLabels.push({chartType:"stackedColumn100",dataPoint:q[f],dataSeries:m,point:{x:d,
y:0<=q[f].y?h:v},direction:0<=q[f].y?1:-1,bounds:{x1:s,y1:Math.min(h,v),x2:w,y2:Math.max(h,v)},color:c})}}b.restore();t&&this._eventManager.ghostCtx.restore();a=Math.min(p,a.axisY.boundingRect.y2);return{source:b,dest:this.plotArea.ctx,animationCallback:A.yScaleAnimation,easingFunction:A.easing.easeOutQuart,animationBase:a}}};u.prototype.renderBar=function(a){var b=a.targetCanvasCtx||this.plotArea.ctx;if(!(0>=a.dataSeriesIndexes.length)){var c=null,d=this.plotArea,e=0,g,f,h,p=a.axisY.conversionParameters.reference+
a.axisY.conversionParameters.pixelPerUnit*(0-a.axisY.conversionParameters.minimum)<<0,e=this.dataPointMaxWidth?this.dataPointMaxWidth:Math.min(0.15*this.height,0.9*(this.plotArea.height/a.plotType.totalDataSeries))<<0,k=a.axisX.dataInfo.minDiff,l=0.9*(d.height/Math.abs(a.axisX.viewportMaximum-a.axisX.viewportMinimum)*Math.abs(k)/a.plotType.totalDataSeries)<<0;l>e?l=e:Infinity===k?l=0.9*(e/a.plotType.totalDataSeries):1>l&&(l=1);b.save();t&&this._eventManager.ghostCtx.save();b.beginPath();b.rect(d.x1,
d.y1,d.width,d.height);b.clip();t&&(this._eventManager.ghostCtx.rect(d.x1,d.y1,d.width,d.height),this._eventManager.ghostCtx.clip());for(d=0;d<a.dataSeriesIndexes.length;d++){var k=a.dataSeriesIndexes[d],n=this.data[k],m=n.dataPoints;if(0<m.length){var q=5<l&&n.bevelEnabled?!0:!1;b.strokeStyle="#4572A7 ";for(e=0;e<m.length;e++)if(m[e].getTime?h=m[e].x.getTime():h=m[e].x,!(h<a.axisX.dataInfo.viewPortMin||h>a.axisX.dataInfo.viewPortMax)&&"number"===typeof m[e].y){f=a.axisX.conversionParameters.reference+
a.axisX.conversionParameters.pixelPerUnit*(h-a.axisX.conversionParameters.minimum)+0.5<<0;g=a.axisY.conversionParameters.reference+a.axisY.conversionParameters.pixelPerUnit*(m[e].y-a.axisY.conversionParameters.minimum)+0.5<<0;f=f-a.plotType.totalDataSeries*l/2+(a.previousDataSeriesCount+d)*l<<0;var r=f+l<<0,s;0<=m[e].y?s=p:(s=g,g=p);c=m[e].color?m[e].color:n._colorSet[e%n._colorSet.length];I(b,s,f,g,r,c,0,null,q,!1,!1,!1,n.fillOpacity);c=n.dataPointIds[e];this._eventManager.objectMap[c]={id:c,objectType:"dataPoint",
dataSeriesIndex:k,dataPointIndex:e,x1:s,y1:f,x2:g,y2:r};c=B(c);t&&I(this._eventManager.ghostCtx,s,f,g,r,c,0,null,!1,!1,!1,!1);(m[e].indexLabel||n.indexLabel||m[e].indexLabelFormatter||n.indexLabelFormatter)&&this._indexLabels.push({chartType:"bar",dataPoint:m[e],dataSeries:n,point:{x:0<=m[e].y?g:s,y:f+(r-f)/2},direction:0<=m[e].y?1:-1,bounds:{x1:Math.min(s,g),y1:f,x2:Math.max(s,g),y2:r},color:c})}}}b.restore();t&&this._eventManager.ghostCtx.restore();a=Math.max(p,a.axisX.boundingRect.x2);return{source:b,
dest:this.plotArea.ctx,animationCallback:A.xScaleAnimation,easingFunction:A.easing.easeOutQuart,animationBase:a}}};u.prototype.renderStackedBar=function(a){var b=a.targetCanvasCtx||this.plotArea.ctx;if(!(0>=a.dataSeriesIndexes.length)){var c=null,d=this.plotArea,e=[],g=[],f=0,h,p=a.axisY.conversionParameters.reference+a.axisY.conversionParameters.pixelPerUnit*(0-a.axisY.conversionParameters.minimum)<<0,f=this.dataPointMaxWidth?this.dataPointMaxWidth:0.15*this.height<<0,k=a.axisX.dataInfo.minDiff,
l=0.9*(d.height/Math.abs(a.axisX.viewportMaximum-a.axisX.viewportMinimum)*Math.abs(k)/a.plotType.plotUnits.length)<<0;l>f?l=f:Infinity===k?l=f:1>l&&(l=1);b.save();t&&this._eventManager.ghostCtx.save();b.beginPath();b.rect(d.x1,d.y1,d.width,d.height);b.clip();t&&(this._eventManager.ghostCtx.rect(d.x1,d.y1,d.width,d.height),this._eventManager.ghostCtx.clip());for(k=0;k<a.dataSeriesIndexes.length;k++){var n=a.dataSeriesIndexes[k],m=this.data[n],q=m.dataPoints;if(0<q.length){var r=5<l&&m.bevelEnabled?
!0:!1;b.strokeStyle="#4572A7 ";for(f=0;f<q.length;f++)if(c=q[f].x.getTime?q[f].x.getTime():q[f].x,!(c<a.axisX.dataInfo.viewPortMin||c>a.axisX.dataInfo.viewPortMax)&&"number"===typeof q[f].y){d=a.axisX.conversionParameters.reference+a.axisX.conversionParameters.pixelPerUnit*(c-a.axisX.conversionParameters.minimum)+0.5<<0;h=a.axisY.conversionParameters.reference+a.axisY.conversionParameters.pixelPerUnit*(q[f].y-a.axisY.conversionParameters.minimum);var s=d-a.plotType.plotUnits.length*l/2+a.index*l<<
0,w=s+l<<0,v;if(0<=q[f].y){var x=e[c]?e[c]:0;v=p+x;h+=x;e[c]=x+(h-v)}else x=g[c]?g[c]:0,v=h-x,h=p-x,g[c]=x+(h-v);c=q[f].color?q[f].color:m._colorSet[f%m._colorSet.length];I(b,v,s,h,w,c,0,null,r,!1,!1,!1,m.fillOpacity);c=m.dataPointIds[f];this._eventManager.objectMap[c]={id:c,objectType:"dataPoint",dataSeriesIndex:n,dataPointIndex:f,x1:v,y1:s,x2:h,y2:w};c=B(c);t&&I(this._eventManager.ghostCtx,v,s,h,w,c,0,null,!1,!1,!1,!1);(q[f].indexLabel||m.indexLabel||q[f].indexLabelFormatter||m.indexLabelFormatter)&&
this._indexLabels.push({chartType:"stackedBar",dataPoint:q[f],dataSeries:m,point:{x:0<=q[f].y?h:v,y:d},direction:0<=q[f].y?1:-1,bounds:{x1:Math.min(v,h),y1:s,x2:Math.max(v,h),y2:w},color:c})}}}b.restore();t&&this._eventManager.ghostCtx.restore();a=Math.max(p,a.axisX.boundingRect.x2);return{source:b,dest:this.plotArea.ctx,animationCallback:A.xScaleAnimation,easingFunction:A.easing.easeOutQuart,animationBase:a}}};u.prototype.renderStackedBar100=function(a){var b=a.targetCanvasCtx||this.plotArea.ctx;
if(!(0>=a.dataSeriesIndexes.length)){var c=null,d=this.plotArea,e=[],g=[],f=0,h,p=a.axisY.conversionParameters.reference+a.axisY.conversionParameters.pixelPerUnit*(0-a.axisY.conversionParameters.minimum)<<0,f=this.dataPointMaxWidth?this.dataPointMaxWidth:0.15*this.height<<0,k=a.axisX.dataInfo.minDiff,l=0.9*(d.height/Math.abs(a.axisX.viewportMaximum-a.axisX.viewportMinimum)*Math.abs(k)/a.plotType.plotUnits.length)<<0;l>f?l=f:Infinity===k?l=f:1>l&&(l=1);b.save();t&&this._eventManager.ghostCtx.save();
b.beginPath();b.rect(d.x1,d.y1,d.width,d.height);b.clip();t&&(this._eventManager.ghostCtx.rect(d.x1,d.y1,d.width,d.height),this._eventManager.ghostCtx.clip());for(k=0;k<a.dataSeriesIndexes.length;k++){var n=a.dataSeriesIndexes[k],m=this.data[n],q=m.dataPoints;if(0<q.length){var r=5<l&&m.bevelEnabled?!0:!1;b.strokeStyle="#4572A7 ";for(f=0;f<q.length;f++)if(c=q[f].x.getTime?q[f].x.getTime():q[f].x,!(c<a.axisX.dataInfo.viewPortMin||c>a.axisX.dataInfo.viewPortMax)&&"number"===typeof q[f].y){d=a.axisX.conversionParameters.reference+
a.axisX.conversionParameters.pixelPerUnit*(c-a.axisX.conversionParameters.minimum)+0.5<<0;h=a.axisY.conversionParameters.reference+a.axisY.conversionParameters.pixelPerUnit*((0!==a.dataPointYSums[c]?100*(q[f].y/a.dataPointYSums[c]):0)-a.axisY.conversionParameters.minimum);var s=d-a.plotType.plotUnits.length*l/2+a.index*l<<0,w=s+l<<0,v;if(0<=q[f].y){var x=e[c]?e[c]:0;v=p+x;h+=x;e[c]=x+(h-v)}else x=g[c]?g[c]:0,v=h-x,h=p-x,g[c]=x+(h-v);c=q[f].color?q[f].color:m._colorSet[f%m._colorSet.length];I(b,v,
s,h,w,c,0,null,r,!1,!1,!1,m.fillOpacity);c=m.dataPointIds[f];this._eventManager.objectMap[c]={id:c,objectType:"dataPoint",dataSeriesIndex:n,dataPointIndex:f,x1:v,y1:s,x2:h,y2:w};c=B(c);t&&I(this._eventManager.ghostCtx,v,s,h,w,c,0,null,!1,!1,!1,!1);(q[f].indexLabel||m.indexLabel||q[f].indexLabelFormatter||m.indexLabelFormatter)&&this._indexLabels.push({chartType:"stackedBar100",dataPoint:q[f],dataSeries:m,point:{x:0<=q[f].y?h:v,y:d},direction:0<=q[f].y?1:-1,bounds:{x1:Math.min(v,h),y1:s,x2:Math.max(v,
h),y2:w},color:c})}}}b.restore();t&&this._eventManager.ghostCtx.restore();a=Math.max(p,a.axisX.boundingRect.x2);return{source:b,dest:this.plotArea.ctx,animationCallback:A.xScaleAnimation,easingFunction:A.easing.easeOutQuart,animationBase:a}}};u.prototype.renderArea=function(a){function b(){x&&(0<k.lineThickness&&c.stroke(),0>=a.axisY.viewportMinimum&&0<=a.axisY.viewportMaximum?v=w:0>a.axisY.viewportMaximum?v=g.y1:0<a.axisY.viewportMinimum&&(v=e.y2),c.lineTo(q,v),c.lineTo(x.x,v),c.closePath(),c.globalAlpha=
k.fillOpacity,c.fill(),c.globalAlpha=1,t&&(d.lineTo(q,v),d.lineTo(x.x,v),d.closePath(),d.fill()),c.beginPath(),c.moveTo(q,r),d.beginPath(),d.moveTo(q,r),x={x:q,y:r})}var c=a.targetCanvasCtx||this.plotArea.ctx;if(!(0>=a.dataSeriesIndexes.length)){var d=this._eventManager.ghostCtx,e=a.axisX.lineCoordinates,g=a.axisY.lineCoordinates,f=[],h=this.plotArea;c.save();t&&d.save();c.beginPath();c.rect(h.x1,h.y1,h.width,h.height);c.clip();t&&(d.beginPath(),d.rect(h.x1,h.y1,h.width,h.height),d.clip());for(h=
0;h<a.dataSeriesIndexes.length;h++){var p=a.dataSeriesIndexes[h],k=this.data[p],l=k.dataPoints,f=k.id;this._eventManager.objectMap[f]={objectType:"dataSeries",dataSeriesIndex:p};f=B(f);d.fillStyle=f;var f=[],n=!0,m=0,q,r,s,w=a.axisY.conversionParameters.reference+a.axisY.conversionParameters.pixelPerUnit*(0-a.axisY.conversionParameters.minimum)+0.5<<0,v,x=null;if(0<l.length){var E=k._colorSet[m%k._colorSet.length];c.fillStyle=E;c.strokeStyle=E;c.lineWidth=k.lineThickness;c.setLineDash&&c.setLineDash(M(k.lineDashType,
k.lineThickness));for(var y=!0;m<l.length;m++)if(s=l[m].x.getTime?l[m].x.getTime():l[m].x,!(s<a.axisX.dataInfo.viewPortMin||s>a.axisX.dataInfo.viewPortMax))if("number"!==typeof l[m].y)b(),y=!0;else{q=a.axisX.conversionParameters.reference+a.axisX.conversionParameters.pixelPerUnit*(s-a.axisX.conversionParameters.minimum)+0.5<<0;r=a.axisY.conversionParameters.reference+a.axisY.conversionParameters.pixelPerUnit*(l[m].y-a.axisY.conversionParameters.minimum)+0.5<<0;n||y?(c.beginPath(),c.moveTo(q,r),x=
{x:q,y:r},t&&(d.beginPath(),d.moveTo(q,r)),y=n=!1):(c.lineTo(q,r),t&&d.lineTo(q,r),0==m%250&&b());var z=k.dataPointIds[m];this._eventManager.objectMap[z]={id:z,objectType:"dataPoint",dataSeriesIndex:p,dataPointIndex:m,x1:q,y1:r};0!==l[m].markerSize&&(0<l[m].markerSize||0<k.markerSize)&&(s=k.getMarkerProperties(m,q,r,c),f.push(s),z=B(z),t&&f.push({x:q,y:r,ctx:d,type:s.type,size:s.size,color:z,borderColor:z,borderThickness:s.borderThickness}));(l[m].indexLabel||k.indexLabel||l[m].indexLabelFormatter||
k.indexLabelFormatter)&&this._indexLabels.push({chartType:"area",dataPoint:l[m],dataSeries:k,point:{x:q,y:r},direction:0<=l[m].y?1:-1,color:E})}b();K.drawMarkers(f)}}c.restore();t&&this._eventManager.ghostCtx.restore();return{source:c,dest:this.plotArea.ctx,animationCallback:A.xClipAnimation,easingFunction:A.easing.linear,animationBase:0}}};u.prototype.renderSplineArea=function(a){function b(){var b=ha(v,2);if(0<b.length){c.beginPath();c.moveTo(b[0].x,b[0].y);t&&(d.beginPath(),d.moveTo(b[0].x,b[0].y));
for(var f=0;f<b.length-3;f+=3)c.bezierCurveTo(b[f+1].x,b[f+1].y,b[f+2].x,b[f+2].y,b[f+3].x,b[f+3].y),t&&d.bezierCurveTo(b[f+1].x,b[f+1].y,b[f+2].x,b[f+2].y,b[f+3].x,b[f+3].y);0<k.lineThickness&&c.stroke();0>=a.axisY.viewportMinimum&&0<=a.axisY.viewportMaximum?s=r:0>a.axisY.viewportMaximum?s=g.y1:0<a.axisY.viewportMinimum&&(s=e.y2);w={x:b[0].x,y:b[0].y};c.lineTo(b[b.length-1].x,s);c.lineTo(w.x,s);c.closePath();c.globalAlpha=k.fillOpacity;c.fill();c.globalAlpha=1;t&&(d.lineTo(b[b.length-1].x,s),d.lineTo(w.x,
s),d.closePath(),d.fill())}}var c=a.targetCanvasCtx||this.plotArea.ctx;if(!(0>=a.dataSeriesIndexes.length)){var d=this._eventManager.ghostCtx,e=a.axisX.lineCoordinates,g=a.axisY.lineCoordinates,f=[],h=this.plotArea;c.save();t&&d.save();c.beginPath();c.rect(h.x1,h.y1,h.width,h.height);c.clip();t&&(d.beginPath(),d.rect(h.x1,h.y1,h.width,h.height),d.clip());for(h=0;h<a.dataSeriesIndexes.length;h++){var p=a.dataSeriesIndexes[h],k=this.data[p],l=k.dataPoints,f=k.id;this._eventManager.objectMap[f]={objectType:"dataSeries",
dataSeriesIndex:p};f=B(f);d.fillStyle=f;var f=[],n=0,m,q,r=a.axisY.conversionParameters.reference+a.axisY.conversionParameters.pixelPerUnit*(0-a.axisY.conversionParameters.minimum)+0.5<<0,s,w=null,v=[];if(0<l.length){color=k._colorSet[n%k._colorSet.length];c.fillStyle=color;c.strokeStyle=color;c.lineWidth=k.lineThickness;for(c.setLineDash&&c.setLineDash(M(k.lineDashType,k.lineThickness));n<l.length;n++)if(m=l[n].x.getTime?l[n].x.getTime():l[n].x,!(m<a.axisX.dataInfo.viewPortMin||m>a.axisX.dataInfo.viewPortMax))if("number"!==
typeof l[n].y)0<n&&(b(),v=[]);else{m=a.axisX.conversionParameters.reference+a.axisX.conversionParameters.pixelPerUnit*(m-a.axisX.conversionParameters.minimum)+0.5<<0;q=a.axisY.conversionParameters.reference+a.axisY.conversionParameters.pixelPerUnit*(l[n].y-a.axisY.conversionParameters.minimum)+0.5<<0;var x=k.dataPointIds[n];this._eventManager.objectMap[x]={id:x,objectType:"dataPoint",dataSeriesIndex:p,dataPointIndex:n,x1:m,y1:q};v[v.length]={x:m,y:q};if(0!==l[n].markerSize&&(0<l[n].markerSize||0<
k.markerSize)){var E=k.getMarkerProperties(n,m,q,c);f.push(E);x=B(x);t&&f.push({x:m,y:q,ctx:d,type:E.type,size:E.size,color:x,borderColor:x,borderThickness:E.borderThickness})}(l[n].indexLabel||k.indexLabel||l[n].indexLabelFormatter||k.indexLabelFormatter)&&this._indexLabels.push({chartType:"splineArea",dataPoint:l[n],dataSeries:k,point:{x:m,y:q},direction:0<=l[n].y?1:-1,color:color})}b();K.drawMarkers(f)}}c.restore();t&&this._eventManager.ghostCtx.restore();return{source:c,dest:this.plotArea.ctx,
animationCallback:A.xClipAnimation,easingFunction:A.easing.linear,animationBase:0}}};u.prototype.renderStepArea=function(a){function b(){x&&(0<k.lineThickness&&c.stroke(),0>=a.axisY.viewportMinimum&&0<=a.axisY.viewportMaximum?v=w:0>a.axisY.viewportMaximum?v=g.y1:0<a.axisY.viewportMinimum&&(v=e.y2),c.lineTo(q,v),c.lineTo(x.x,v),c.closePath(),c.globalAlpha=k.fillOpacity,c.fill(),c.globalAlpha=1,t&&(d.lineTo(q,v),d.lineTo(x.x,v),d.closePath(),d.fill()),c.beginPath(),c.moveTo(q,r),d.beginPath(),d.moveTo(q,
r),x={x:q,y:r})}var c=a.targetCanvasCtx||this.plotArea.ctx;if(!(0>=a.dataSeriesIndexes.length)){var d=this._eventManager.ghostCtx,e=a.axisX.lineCoordinates,g=a.axisY.lineCoordinates,f=[],h=this.plotArea;c.save();t&&d.save();c.beginPath();c.rect(h.x1,h.y1,h.width,h.height);c.clip();t&&(d.beginPath(),d.rect(h.x1,h.y1,h.width,h.height),d.clip());for(h=0;h<a.dataSeriesIndexes.length;h++){var p=a.dataSeriesIndexes[h],k=this.data[p],l=k.dataPoints,f=k.id;this._eventManager.objectMap[f]={objectType:"dataSeries",
dataSeriesIndex:p};f=B(f);d.fillStyle=f;var f=[],n=!0,m=0,q,r,s,w=a.axisY.conversionParameters.reference+a.axisY.conversionParameters.pixelPerUnit*(0-a.axisY.conversionParameters.minimum)+0.5<<0,v,x=null,E=!1;if(0<l.length){var y=k._colorSet[m%k._colorSet.length];c.fillStyle=y;c.strokeStyle=y;c.lineWidth=k.lineThickness;for(c.setLineDash&&c.setLineDash(M(k.lineDashType,k.lineThickness));m<l.length;m++)if(s=l[m].x.getTime?l[m].x.getTime():l[m].x,!(s<a.axisX.dataInfo.viewPortMin||s>a.axisX.dataInfo.viewPortMax)){var z=
r;"number"!==typeof l[m].y?(b(),E=!0):(q=a.axisX.conversionParameters.reference+a.axisX.conversionParameters.pixelPerUnit*(s-a.axisX.conversionParameters.minimum)+0.5<<0,r=a.axisY.conversionParameters.reference+a.axisY.conversionParameters.pixelPerUnit*(l[m].y-a.axisY.conversionParameters.minimum)+0.5<<0,n||E?(c.beginPath(),c.moveTo(q,r),x={x:q,y:r},t&&(d.beginPath(),d.moveTo(q,r)),E=n=!1):(c.lineTo(q,z),t&&d.lineTo(q,z),c.lineTo(q,r),t&&d.lineTo(q,r),0==m%250&&b()),z=k.dataPointIds[m],this._eventManager.objectMap[z]=
{id:z,objectType:"dataPoint",dataSeriesIndex:p,dataPointIndex:m,x1:q,y1:r},0!==l[m].markerSize&&(0<l[m].markerSize||0<k.markerSize)&&(s=k.getMarkerProperties(m,q,r,c),f.push(s),z=B(z),t&&f.push({x:q,y:r,ctx:d,type:s.type,size:s.size,color:z,borderColor:z,borderThickness:s.borderThickness})),(l[m].indexLabel||k.indexLabel||l[m].indexLabelFormatter||k.indexLabelFormatter)&&this._indexLabels.push({chartType:"stepArea",dataPoint:l[m],dataSeries:k,point:{x:q,y:r},direction:0<=l[m].y?1:-1,color:y}))}b();
K.drawMarkers(f)}}c.restore();t&&this._eventManager.ghostCtx.restore();return{source:c,dest:this.plotArea.ctx,animationCallback:A.xClipAnimation,easingFunction:A.easing.linear,animationBase:0}}};u.prototype.renderStackedArea=function(a){var b=a.targetCanvasCtx||this.plotArea.ctx;if(!(0>=a.dataSeriesIndexes.length)){var c=null,d=[],e=this.plotArea,g=[],f=[],h=0,p,k,l,n=a.axisY.conversionParameters.reference+a.axisY.conversionParameters.pixelPerUnit*(0-a.axisY.conversionParameters.minimum)<<0,m=this._eventManager.ghostCtx;
t&&m.beginPath();b.save();t&&m.save();b.beginPath();b.rect(e.x1,e.y1,e.width,e.height);b.clip();t&&(m.beginPath(),m.rect(e.x1,e.y1,e.width,e.height),m.clip());xValuePresent=[];for(e=0;e<a.dataSeriesIndexes.length;e++){var q=a.dataSeriesIndexes[e],r=this.data[q],s=r.dataPoints;r.dataPointIndexes=[];for(h=0;h<s.length;h++)q=s[h].x.getTime?s[h].x.getTime():s[h].x,r.dataPointIndexes[q]=h,xValuePresent[q]||(f.push(q),xValuePresent[q]=!0);f.sort(ta)}for(e=0;e<a.dataSeriesIndexes.length;e++){var q=a.dataSeriesIndexes[e],
r=this.data[q],s=r.dataPoints,w=!0,v=[],h=r.id;this._eventManager.objectMap[h]={objectType:"dataSeries",dataSeriesIndex:q};h=B(h);m.fillStyle=h;if(0<f.length){c=r._colorSet[0];b.fillStyle=c;b.strokeStyle=c;b.lineWidth=r.lineThickness;b.setLineDash&&b.setLineDash(M(r.lineDashType,r.lineThickness));for(h=0;h<f.length;h++){l=f[h];var x=null,x=0<=r.dataPointIndexes[l]?s[r.dataPointIndexes[l]]:{x:l,y:0};if(!(l<a.axisX.dataInfo.viewPortMin||l>a.axisX.dataInfo.viewPortMax)&&"number"===typeof x.y){p=a.axisX.conversionParameters.reference+
a.axisX.conversionParameters.pixelPerUnit*(l-a.axisX.conversionParameters.minimum)+0.5<<0;k=a.axisY.conversionParameters.reference+a.axisY.conversionParameters.pixelPerUnit*(x.y-a.axisY.conversionParameters.minimum);var E=g[l]?g[l]:0;k-=E;v.push({x:p,y:n-E});g[l]=n-k;if(w)b.beginPath(),b.moveTo(p,k),t&&(m.beginPath(),m.moveTo(p,k)),w=!1;else if(b.lineTo(p,k),t&&m.lineTo(p,k),0==h%250){for(0<r.lineThickness&&b.stroke();0<v.length;){var y=v.pop();b.lineTo(y.x,y.y);t&&m.lineTo(y.x,y.y)}b.closePath();
b.globalAlpha=r.fillOpacity;b.fill();b.globalAlpha=1;b.beginPath();b.moveTo(p,k);t&&(m.closePath(),m.fill(),m.beginPath(),m.moveTo(p,k));v.push({x:p,y:n-E})}if(0<=r.dataPointIndexes[l]){var z=r.dataPointIds[r.dataPointIndexes[l]];this._eventManager.objectMap[z]={id:z,objectType:"dataPoint",dataSeriesIndex:q,dataPointIndex:r.dataPointIndexes[l],x1:p,y1:k}}0<=r.dataPointIndexes[l]&&0!==x.markerSize&&(0<x.markerSize||0<r.markerSize)&&(l=r.getMarkerProperties(h,p,k,b),d.push(l),markerColor=B(z),t&&d.push({x:p,
y:k,ctx:m,type:l.type,size:l.size,color:markerColor,borderColor:markerColor,borderThickness:l.borderThickness}));(x.indexLabel||r.indexLabel||x.indexLabelFormatter||r.indexLabelFormatter)&&this._indexLabels.push({chartType:"stackedArea",dataPoint:x,dataSeries:r,point:{x:p,y:k},direction:0<=s[h].y?1:-1,color:c})}}for(0<r.lineThickness&&b.stroke();0<v.length;)y=v.pop(),b.lineTo(y.x,y.y),t&&m.lineTo(y.x,y.y);b.closePath();b.globalAlpha=r.fillOpacity;b.fill();b.globalAlpha=1;b.beginPath();b.moveTo(p,
k);t&&(m.closePath(),m.fill(),m.beginPath(),m.moveTo(p,k))}delete r.dataPointIndexes}K.drawMarkers(d);b.restore();t&&m.restore();return{source:b,dest:this.plotArea.ctx,animationCallback:A.xClipAnimation,easingFunction:A.easing.linear,animationBase:0}}};u.prototype.renderStackedArea100=function(a){var b=a.targetCanvasCtx||this.plotArea.ctx;if(!(0>=a.dataSeriesIndexes.length)){var c=null,d=this.plotArea,e=[],g=[],f=[],h=0,p,k,l,n=a.axisY.conversionParameters.reference+a.axisY.conversionParameters.pixelPerUnit*
(0-a.axisY.conversionParameters.minimum)<<0,m=this.dataPointMaxWidth?this.dataPointMaxWidth:0.15*this.width<<0,q=a.axisX.dataInfo.minDiff,q=0.9*d.width/Math.abs(a.axisX.viewportMaximum-a.axisX.viewportMinimum)*Math.abs(q)<<0,r=this._eventManager.ghostCtx;b.save();t&&r.save();b.beginPath();b.rect(d.x1,d.y1,d.width,d.height);b.clip();t&&(r.beginPath(),r.rect(d.x1,d.y1,d.width,d.height),r.clip());xValuePresent=[];for(d=0;d<a.dataSeriesIndexes.length;d++){var s=a.dataSeriesIndexes[d],w=this.data[s],v=
w.dataPoints;w.dataPointIndexes=[];for(h=0;h<v.length;h++)s=v[h].x.getTime?v[h].x.getTime():v[h].x,w.dataPointIndexes[s]=h,xValuePresent[s]||(f.push(s),xValuePresent[s]=!0);f.sort(ta)}for(d=0;d<a.dataSeriesIndexes.length;d++){var s=a.dataSeriesIndexes[d],w=this.data[s],v=w.dataPoints,x=!0,c=w.id;this._eventManager.objectMap[c]={objectType:"dataSeries",dataSeriesIndex:s};c=B(c);r.fillStyle=c;1==v.length&&(q=m);1>q?q=1:q>m&&(q=m);var E=[];if(0<f.length){c=w._colorSet[h%w._colorSet.length];b.fillStyle=
c;b.strokeStyle=c;b.lineWidth=w.lineThickness;b.setLineDash&&b.setLineDash(M(w.lineDashType,w.lineThickness));for(h=0;h<f.length;h++){l=f[h];var y=null,y=0<=w.dataPointIndexes[l]?v[w.dataPointIndexes[l]]:{x:l,y:0};if(!(l<a.axisX.dataInfo.viewPortMin||l>a.axisX.dataInfo.viewPortMax)&&"number"===typeof y.y){k=0!==a.dataPointYSums[l]?100*(y.y/a.dataPointYSums[l]):0;p=a.axisX.conversionParameters.reference+a.axisX.conversionParameters.pixelPerUnit*(l-a.axisX.conversionParameters.minimum)+0.5<<0;k=a.axisY.conversionParameters.reference+
a.axisY.conversionParameters.pixelPerUnit*(k-a.axisY.conversionParameters.minimum);var z=g[l]?g[l]:0;k-=z;E.push({x:p,y:n-z});g[l]=n-k;if(x)b.beginPath(),b.moveTo(p,k),t&&(r.beginPath(),r.moveTo(p,k)),x=!1;else if(b.lineTo(p,k),t&&r.lineTo(p,k),0==h%250){for(0<w.lineThickness&&b.stroke();0<E.length;){var u=E.pop();b.lineTo(u.x,u.y);t&&r.lineTo(u.x,u.y)}b.closePath();b.globalAlpha=w.fillOpacity;b.fill();b.globalAlpha=1;b.beginPath();b.moveTo(p,k);t&&(r.closePath(),r.fill(),r.beginPath(),r.moveTo(p,
k));E.push({x:p,y:n-z})}if(0<=w.dataPointIndexes[l]){var D=w.dataPointIds[w.dataPointIndexes[l]];this._eventManager.objectMap[D]={id:D,objectType:"dataPoint",dataSeriesIndex:s,dataPointIndex:w.dataPointIndexes[l],x1:p,y1:k}}0<=w.dataPointIndexes[l]&&0!==y.markerSize&&(0<y.markerSize||0<w.markerSize)&&(l=w.getMarkerProperties(h,p,k,b),e.push(l),markerColor=B(D),t&&e.push({x:p,y:k,ctx:r,type:l.type,size:l.size,color:markerColor,borderColor:markerColor,borderThickness:l.borderThickness}));(y.indexLabel||
w.indexLabel||y.indexLabelFormatter||w.indexLabelFormatter)&&this._indexLabels.push({chartType:"stackedArea100",dataPoint:y,dataSeries:w,point:{x:p,y:k},direction:0<=v[h].y?1:-1,color:c})}}for(0<w.lineThickness&&b.stroke();0<E.length;)u=E.pop(),b.lineTo(u.x,u.y),t&&r.lineTo(u.x,u.y);b.closePath();b.globalAlpha=w.fillOpacity;b.fill();b.globalAlpha=1;b.beginPath();b.moveTo(p,k);t&&(r.closePath(),r.fill(),r.beginPath(),r.moveTo(p,k))}delete w.dataPointIndexes}K.drawMarkers(e);b.restore();t&&r.restore();
return{source:b,dest:this.plotArea.ctx,animationCallback:A.xClipAnimation,easingFunction:A.easing.linear,animationBase:0}}};u.prototype.renderBubble=function(a){var b=a.targetCanvasCtx||this.plotArea.ctx,c=a.dataSeriesIndexes.length;if(!(0>=c)){var d=this.plotArea,e=0,g,f,h=this.dataPointMaxWidth?this.dataPointMaxWidth:0.15*this.width<<0,e=a.axisX.dataInfo.minDiff,c=0.9*(d.width/Math.abs(a.axisX.viewportMaximum-a.axisX.viewportMinimum)*Math.abs(e)/c)<<0;b.save();t&&this._eventManager.ghostCtx.save();
b.beginPath();b.rect(d.x1,d.y1,d.width,d.height);b.clip();t&&(this._eventManager.ghostCtx.rect(d.x1,d.y1,d.width,d.height),this._eventManager.ghostCtx.clip());for(var p=-Infinity,k=Infinity,l=0;l<a.dataSeriesIndexes.length;l++)for(var n=a.dataSeriesIndexes[l],m=this.data[n],q=m.dataPoints,r=0,e=0;e<q.length;e++)g=q[e].getTime?g=q[e].x.getTime():g=q[e].x,g<a.axisX.dataInfo.viewPortMin||g>a.axisX.dataInfo.viewPortMax||"undefined"===typeof q[e].z||(r=q[e].z,r>p&&(p=r),r<k&&(k=r));for(var s=25*Math.PI,
d=Math.max(Math.pow(0.25*Math.min(d.height,d.width)/2,2)*Math.PI,s),l=0;l<a.dataSeriesIndexes.length;l++)if(n=a.dataSeriesIndexes[l],m=this.data[n],q=m.dataPoints,1==q.length&&(c=h),1>c?c=1:c>h&&(c=h),0<q.length)for(b.strokeStyle="#4572A7 ",e=0;e<q.length;e++)if(g=q[e].getTime?g=q[e].x.getTime():g=q[e].x,!(g<a.axisX.dataInfo.viewPortMin||g>a.axisX.dataInfo.viewPortMax)&&"number"===typeof q[e].y){g=a.axisX.conversionParameters.reference+a.axisX.conversionParameters.pixelPerUnit*(g-a.axisX.conversionParameters.minimum)+
0.5<<0;f=a.axisY.conversionParameters.reference+a.axisY.conversionParameters.pixelPerUnit*(q[e].y-a.axisY.conversionParameters.minimum)+0.5<<0;var r=q[e].z,w=2*Math.max(Math.sqrt((p===k?d/2:s+(d-s)/(p-k)*(r-k))/Math.PI)<<0,1),r=m.getMarkerProperties(e,b);r.size=w;b.globalAlpha=m.fillOpacity;K.drawMarker(g,f,b,r.type,r.size,r.color,r.borderColor,r.borderThickness);b.globalAlpha=1;var v=m.dataPointIds[e];this._eventManager.objectMap[v]={id:v,objectType:"dataPoint",dataSeriesIndex:n,dataPointIndex:e,
x1:g,y1:f,size:w};w=B(v);t&&K.drawMarker(g,f,this._eventManager.ghostCtx,r.type,r.size,w,w,r.borderThickness);(q[e].indexLabel||m.indexLabel||q[e].indexLabelFormatter||m.indexLabelFormatter)&&this._indexLabels.push({chartType:"bubble",dataPoint:q[e],dataSeries:m,point:{x:g,y:f},direction:1,bounds:{x1:g-r.size/2,y1:f-r.size/2,x2:g+r.size/2,y2:f+r.size/2},color:null})}b.restore();t&&this._eventManager.ghostCtx.restore();return{source:b,dest:this.plotArea.ctx,animationCallback:A.fadeInAnimation,easingFunction:A.easing.easeInQuad,
animationBase:0}}};u.prototype.renderScatter=function(a){var b=a.targetCanvasCtx||this.plotArea.ctx,c=a.dataSeriesIndexes.length;if(!(0>=c)){var d=this.plotArea,e=0,g,f,h=this.dataPointMaxWidth?this.dataPointMaxWidth:0.15*this.width<<0,e=a.axisX.dataInfo.minDiff,c=0.9*(d.width/Math.abs(a.axisX.viewportMaximum-a.axisX.viewportMinimum)*Math.abs(e)/c)<<0;b.save();t&&this._eventManager.ghostCtx.save();b.beginPath();b.rect(d.x1,d.y1,d.width,d.height);b.clip();t&&(this._eventManager.ghostCtx.rect(d.x1,
d.y1,d.width,d.height),this._eventManager.ghostCtx.clip());for(var p=0;p<a.dataSeriesIndexes.length;p++){var k=a.dataSeriesIndexes[p],l=this.data[k],n=l.dataPoints;1==n.length&&(c=h);1>c?c=1:c>h&&(c=h);if(0<n.length){b.strokeStyle="#4572A7 ";Math.pow(0.3*Math.min(d.height,d.width)/2,2);for(var m=0,q=0,e=0;e<n.length;e++)if(g=n[e].getTime?g=n[e].x.getTime():g=n[e].x,!(g<a.axisX.dataInfo.viewPortMin||g>a.axisX.dataInfo.viewPortMax)&&"number"===typeof n[e].y){g=a.axisX.conversionParameters.reference+
a.axisX.conversionParameters.pixelPerUnit*(g-a.axisX.conversionParameters.minimum)+0.5<<0;f=a.axisY.conversionParameters.reference+a.axisY.conversionParameters.pixelPerUnit*(n[e].y-a.axisY.conversionParameters.minimum)+0.5<<0;var r=l.getMarkerProperties(e,g,f,b);b.globalAlpha=l.fillOpacity;K.drawMarker(r.x,r.y,r.ctx,r.type,r.size,r.color,r.borderColor,r.borderThickness);b.globalAlpha=1;Math.sqrt((m-g)*(m-g)+(q-f)*(q-f))<Math.min(r.size,5)&&n.length>Math.min(this.plotArea.width,this.plotArea.height)||
(m=l.dataPointIds[e],this._eventManager.objectMap[m]={id:m,objectType:"dataPoint",dataSeriesIndex:k,dataPointIndex:e,x1:g,y1:f},m=B(m),t&&K.drawMarker(r.x,r.y,this._eventManager.ghostCtx,r.type,r.size,m,m,r.borderThickness),(n[e].indexLabel||l.indexLabel||n[e].indexLabelFormatter||l.indexLabelFormatter)&&this._indexLabels.push({chartType:"scatter",dataPoint:n[e],dataSeries:l,point:{x:g,y:f},direction:1,bounds:{x1:g-r.size/2,y1:f-r.size/2,x2:g+r.size/2,y2:f+r.size/2},color:null}),m=g,q=f)}}}b.restore();
t&&this._eventManager.ghostCtx.restore();return{source:b,dest:this.plotArea.ctx,animationCallback:A.fadeInAnimation,easingFunction:A.easing.easeInQuad,animationBase:0}}};u.prototype.renderCandlestick=function(a){var b=a.targetCanvasCtx||this.plotArea.ctx,c=this._eventManager.ghostCtx;if(!(0>=a.dataSeriesIndexes.length)){var d=null,d=this.plotArea,e=0,g,f,h,p,k,l,e=this.dataPointMaxWidth?this.dataPointMaxWidth:0.015*this.width;g=a.axisX.dataInfo.minDiff;var n=0.7*d.width/Math.abs(a.axisX.viewportMaximum-
a.axisX.viewportMinimum)*Math.abs(g)<<0;n>e?n=e:Infinity===g?n=e:1>n&&(n=1);b.save();t&&c.save();b.beginPath();b.rect(d.x1,d.y1,d.width,d.height);b.clip();t&&(c.rect(d.x1,d.y1,d.width,d.height),c.clip());for(var m=0;m<a.dataSeriesIndexes.length;m++){var q=a.dataSeriesIndexes[m],r=this.data[q],s=r.dataPoints;if(0<s.length)for(var w=5<n&&r.bevelEnabled?!0:!1,e=0;e<s.length;e++)if(s[e].getTime?l=s[e].x.getTime():l=s[e].x,!(l<a.axisX.dataInfo.viewPortMin||l>a.axisX.dataInfo.viewPortMax)&&null!==s[e].y&&
s[e].y.length&&"number"===typeof s[e].y[0]&&"number"===typeof s[e].y[1]&&"number"===typeof s[e].y[2]&&"number"===typeof s[e].y[3]){g=a.axisX.conversionParameters.reference+a.axisX.conversionParameters.pixelPerUnit*(l-a.axisX.conversionParameters.minimum)+0.5<<0;f=a.axisY.conversionParameters.reference+a.axisY.conversionParameters.pixelPerUnit*(s[e].y[0]-a.axisY.conversionParameters.minimum)+0.5<<0;h=a.axisY.conversionParameters.reference+a.axisY.conversionParameters.pixelPerUnit*(s[e].y[1]-a.axisY.conversionParameters.minimum)+
0.5<<0;p=a.axisY.conversionParameters.reference+a.axisY.conversionParameters.pixelPerUnit*(s[e].y[2]-a.axisY.conversionParameters.minimum)+0.5<<0;k=a.axisY.conversionParameters.reference+a.axisY.conversionParameters.pixelPerUnit*(s[e].y[3]-a.axisY.conversionParameters.minimum)+0.5<<0;var v=g-n/2<<0,x=v+n<<0,d=s[e].color?s[e].color:r._colorSet[0],E=Math.round(Math.max(1,0.15*n)),u=0===E%2?0:0.5,z=r.dataPointIds[e];this._eventManager.objectMap[z]={id:z,objectType:"dataPoint",dataSeriesIndex:q,dataPointIndex:e,
x1:v,y1:f,x2:x,y2:h,x3:g,y3:p,x4:g,y4:k,borderThickness:E,color:d};b.strokeStyle=d;b.beginPath();b.lineWidth=E;c.lineWidth=Math.max(E,4);"candlestick"===r.type?(b.moveTo(g-u,h),b.lineTo(g-u,Math.min(f,k)),b.stroke(),b.moveTo(g-u,Math.max(f,k)),b.lineTo(g-u,p),b.stroke(),I(b,v,Math.min(f,k),x,Math.max(f,k),s[e].y[0]<=s[e].y[3]?r.risingColor:d,E,d,w,w,!1,!1,r.fillOpacity),t&&(d=B(z),c.strokeStyle=d,c.moveTo(g-u,h),c.lineTo(g-u,Math.min(f,k)),c.stroke(),c.moveTo(g-u,Math.max(f,k)),c.lineTo(g-u,p),c.stroke(),
I(c,v,Math.min(f,k),x,Math.max(f,k),d,0,null,!1,!1,!1,!1))):"ohlc"===r.type&&(b.moveTo(g-u,h),b.lineTo(g-u,p),b.stroke(),b.beginPath(),b.moveTo(g,f),b.lineTo(v,f),b.stroke(),b.beginPath(),b.moveTo(g,k),b.lineTo(x,k),b.stroke(),t&&(d=B(z),c.strokeStyle=d,c.moveTo(g-u,h),c.lineTo(g-u,p),c.stroke(),c.beginPath(),c.moveTo(g,f),c.lineTo(v,f),c.stroke(),c.beginPath(),c.moveTo(g,k),c.lineTo(x,k),c.stroke()));(s[e].indexLabel||r.indexLabel||s[e].indexLabelFormatter||r.indexLabelFormatter)&&this._indexLabels.push({chartType:r.type,
dataPoint:s[e],dataSeries:r,point:{x:v+(x-v)/2,y:h},direction:1,bounds:{x1:v,y1:Math.min(h,p),x2:x,y2:Math.max(h,p)},color:d})}}b.restore();t&&c.restore();return{source:b,dest:this.plotArea.ctx,animationCallback:A.fadeInAnimation,easingFunction:A.easing.easeInQuad,animationBase:0}}};u.prototype.renderRangeColumn=function(a){var b=a.targetCanvasCtx||this.plotArea.ctx;if(!(0>=a.dataSeriesIndexes.length)){var c=null,d=this.plotArea,e=0,g,f,e=this.dataPointMaxWidth?this.dataPointMaxWidth:0.03*this.width;
g=a.axisX.dataInfo.minDiff;var h=0.9*(d.width/Math.abs(a.axisX.viewportMaximum-a.axisX.viewportMinimum)*Math.abs(g)/a.plotType.totalDataSeries)<<0;h>e?h=e:Infinity===g?h=0.9*(e/a.plotType.totalDataSeries):1>h&&(h=1);b.save();t&&this._eventManager.ghostCtx.save();b.beginPath();b.rect(d.x1,d.y1,d.width,d.height);b.clip();t&&(this._eventManager.ghostCtx.rect(d.x1,d.y1,d.width,d.height),this._eventManager.ghostCtx.clip());for(var p=0;p<a.dataSeriesIndexes.length;p++){var k=a.dataSeriesIndexes[p],l=this.data[k],
n=l.dataPoints;if(0<n.length)for(var m=5<h&&l.bevelEnabled?!0:!1,e=0;e<n.length;e++)if(n[e].getTime?f=n[e].x.getTime():f=n[e].x,!(f<a.axisX.dataInfo.viewPortMin||f>a.axisX.dataInfo.viewPortMax)&&null!==n[e].y&&n[e].y.length&&"number"===typeof n[e].y[0]&&"number"===typeof n[e].y[1]){c=a.axisX.conversionParameters.reference+a.axisX.conversionParameters.pixelPerUnit*(f-a.axisX.conversionParameters.minimum)+0.5<<0;d=a.axisY.conversionParameters.reference+a.axisY.conversionParameters.pixelPerUnit*(n[e].y[0]-
a.axisY.conversionParameters.minimum)+0.5<<0;g=a.axisY.conversionParameters.reference+a.axisY.conversionParameters.pixelPerUnit*(n[e].y[1]-a.axisY.conversionParameters.minimum)+0.5<<0;var q=c-a.plotType.totalDataSeries*h/2+(a.previousDataSeriesCount+p)*h<<0,r=q+h<<0,c=n[e].color?n[e].color:l._colorSet[e%l._colorSet.length];if(d>g){var s=d,d=g;g=s}s=l.dataPointIds[e];this._eventManager.objectMap[s]={id:s,objectType:"dataPoint",dataSeriesIndex:k,dataPointIndex:e,x1:q,y1:d,x2:r,y2:g};I(b,q,d,r,g,c,0,
c,m,m,!1,!1,l.fillOpacity);c=B(s);t&&I(this._eventManager.ghostCtx,q,d,r,g,c,0,null,!1,!1,!1,!1);if(n[e].indexLabel||l.indexLabel||n[e].indexLabelFormatter||l.indexLabelFormatter)this._indexLabels.push({chartType:"rangeColumn",dataPoint:n[e],dataSeries:l,indexKeyword:0,point:{x:q+(r-q)/2,y:n[e].y[1]>=n[e].y[0]?g:d},direction:n[e].y[1]>=n[e].y[0]?-1:1,bounds:{x1:q,y1:Math.min(d,g),x2:r,y2:Math.max(d,g)},color:c}),this._indexLabels.push({chartType:"rangeColumn",dataPoint:n[e],dataSeries:l,indexKeyword:1,
point:{x:q+(r-q)/2,y:n[e].y[1]>=n[e].y[0]?d:g},direction:n[e].y[1]>=n[e].y[0]?1:-1,bounds:{x1:q,y1:Math.min(d,g),x2:r,y2:Math.max(d,g)},color:c})}}b.restore();t&&this._eventManager.ghostCtx.restore();return{source:b,dest:this.plotArea.ctx,animationCallback:A.fadeInAnimation,easingFunction:A.easing.easeInQuad,animationBase:0}}};u.prototype.renderRangeBar=function(a){var b=a.targetCanvasCtx||this.plotArea.ctx;if(!(0>=a.dataSeriesIndexes.length)){var c=null,d=this.plotArea,e=0,g,f,h,e=this.dataPointMaxWidth?
this.dataPointMaxWidth:Math.min(0.15*this.height,0.9*(this.plotArea.height/a.plotType.totalDataSeries))<<0;g=a.axisX.dataInfo.minDiff;var p=0.9*(d.height/Math.abs(a.axisX.viewportMaximum-a.axisX.viewportMinimum)*Math.abs(g)/a.plotType.totalDataSeries)<<0;p>e?p=e:Infinity===g?p=0.9*(e/a.plotType.totalDataSeries):1>p&&(p=1);b.save();t&&this._eventManager.ghostCtx.save();b.beginPath();b.rect(d.x1,d.y1,d.width,d.height);b.clip();t&&(this._eventManager.ghostCtx.rect(d.x1,d.y1,d.width,d.height),this._eventManager.ghostCtx.clip());
for(var k=0;k<a.dataSeriesIndexes.length;k++){var l=a.dataSeriesIndexes[k],n=this.data[l],m=n.dataPoints;if(0<m.length){var q=5<p&&n.bevelEnabled?!0:!1;b.strokeStyle="#4572A7 ";for(e=0;e<m.length;e++)if(m[e].getTime?h=m[e].x.getTime():h=m[e].x,!(h<a.axisX.dataInfo.viewPortMin||h>a.axisX.dataInfo.viewPortMax)&&null!==m[e].y&&m[e].y.length&&"number"===typeof m[e].y[0]&&"number"===typeof m[e].y[1]){d=a.axisY.conversionParameters.reference+a.axisY.conversionParameters.pixelPerUnit*(m[e].y[0]-a.axisY.conversionParameters.minimum)+
0.5<<0;g=a.axisY.conversionParameters.reference+a.axisY.conversionParameters.pixelPerUnit*(m[e].y[1]-a.axisY.conversionParameters.minimum)+0.5<<0;f=a.axisX.conversionParameters.reference+a.axisX.conversionParameters.pixelPerUnit*(h-a.axisX.conversionParameters.minimum)+0.5<<0;f=f-a.plotType.totalDataSeries*p/2+(a.previousDataSeriesCount+k)*p<<0;var r=f+p<<0;d>g&&(c=d,d=g,g=c);c=m[e].color?m[e].color:n._colorSet[e%n._colorSet.length];I(b,d,f,g,r,c,0,null,q,!1,!1,!1,n.fillOpacity);c=n.dataPointIds[e];
this._eventManager.objectMap[c]={id:c,objectType:"dataPoint",dataSeriesIndex:l,dataPointIndex:e,x1:d,y1:f,x2:g,y2:r};c=B(c);t&&I(this._eventManager.ghostCtx,d,f,g,r,c,0,null,!1,!1,!1,!1);if(m[e].indexLabel||n.indexLabel||m[e].indexLabelFormatter||n.indexLabelFormatter)this._indexLabels.push({chartType:"rangeBar",dataPoint:m[e],dataSeries:n,indexKeyword:0,point:{x:m[e].y[1]>=m[e].y[0]?d:g,y:f+(r-f)/2},direction:m[e].y[1]>=m[e].y[0]?-1:1,bounds:{x1:Math.min(d,g),y1:f,x2:Math.max(d,g),y2:r},color:c}),
this._indexLabels.push({chartType:"rangeBar",dataPoint:m[e],dataSeries:n,indexKeyword:1,point:{x:m[e].y[1]>=m[e].y[0]?g:d,y:f+(r-f)/2},direction:m[e].y[1]>=m[e].y[0]?1:-1,bounds:{x1:Math.min(d,g),y1:f,x2:Math.max(d,g),y2:r},color:c})}}}b.restore();t&&this._eventManager.ghostCtx.restore();return{source:b,dest:this.plotArea.ctx,animationCallback:A.fadeInAnimation,easingFunction:A.easing.easeInQuad,animationBase:0}}};u.prototype.renderRangeArea=function(a){function b(){if(w){var a=null;0<p.lineThickness&&
c.stroke();for(var b=f.length-1;0<=b;b--)a=f[b],c.lineTo(a.x,a.y),d.lineTo(a.x,a.y);c.closePath();c.globalAlpha=p.fillOpacity;c.fill();c.globalAlpha=1;d.fill();if(0<p.lineThickness){c.beginPath();c.moveTo(a.x,a.y);for(b=0;b<f.length;b++)a=f[b],c.lineTo(a.x,a.y);c.stroke()}c.beginPath();c.moveTo(m,q);d.beginPath();d.moveTo(m,q);w={x:m,y:q};f=[];f.push({x:m,y:r})}}var c=a.targetCanvasCtx||this.plotArea.ctx;if(!(0>=a.dataSeriesIndexes.length)){var d=this._eventManager.ghostCtx,e=[],g=this.plotArea;c.save();
t&&d.save();c.beginPath();c.rect(g.x1,g.y1,g.width,g.height);c.clip();t&&(d.beginPath(),d.rect(g.x1,g.y1,g.width,g.height),d.clip());for(g=0;g<a.dataSeriesIndexes.length;g++){var f=[],h=a.dataSeriesIndexes[g],p=this.data[h],k=p.dataPoints,e=p.id;this._eventManager.objectMap[e]={objectType:"dataSeries",dataSeriesIndex:h};e=B(e);d.fillStyle=e;var e=[],l=!0,n=0,m,q,r,s,w=null;if(0<k.length){var v=p._colorSet[n%p._colorSet.length];c.fillStyle=v;c.strokeStyle=v;c.lineWidth=p.lineThickness;c.setLineDash&&
c.setLineDash(M(p.lineDashType,p.lineThickness));for(var x=!0;n<k.length;n++)if(s=k[n].x.getTime?k[n].x.getTime():k[n].x,!(s<a.axisX.dataInfo.viewPortMin||s>a.axisX.dataInfo.viewPortMax))if(null!==k[n].y&&k[n].y.length&&"number"===typeof k[n].y[0]&&"number"===typeof k[n].y[1]){m=a.axisX.conversionParameters.reference+a.axisX.conversionParameters.pixelPerUnit*(s-a.axisX.conversionParameters.minimum)+0.5<<0;q=a.axisY.conversionParameters.reference+a.axisY.conversionParameters.pixelPerUnit*(k[n].y[0]-
a.axisY.conversionParameters.minimum)+0.5<<0;r=a.axisY.conversionParameters.reference+a.axisY.conversionParameters.pixelPerUnit*(k[n].y[1]-a.axisY.conversionParameters.minimum)+0.5<<0;l||x?(c.beginPath(),c.moveTo(m,q),w={x:m,y:q},f=[],f.push({x:m,y:r}),t&&(d.beginPath(),d.moveTo(m,q)),x=l=!1):(c.lineTo(m,q),f.push({x:m,y:r}),t&&d.lineTo(m,q),0==n%250&&b());s=p.dataPointIds[n];this._eventManager.objectMap[s]={id:s,objectType:"dataPoint",dataSeriesIndex:h,dataPointIndex:n,x1:m,y1:q,y2:r};if(0!==k[n].markerSize&&
(0<k[n].markerSize||0<p.markerSize)){var u=p.getMarkerProperties(n,m,r,c);e.push(u);var y=B(s);t&&e.push({x:m,y:r,ctx:d,type:u.type,size:u.size,color:y,borderColor:y,borderThickness:u.borderThickness});u=p.getMarkerProperties(n,m,q,c);e.push(u);y=B(s);t&&e.push({x:m,y:q,ctx:d,type:u.type,size:u.size,color:y,borderColor:y,borderThickness:u.borderThickness})}if(k[n].indexLabel||p.indexLabel||k[n].indexLabelFormatter||p.indexLabelFormatter)this._indexLabels.push({chartType:"rangeArea",dataPoint:k[n],
dataSeries:p,indexKeyword:0,point:{x:m,y:q},direction:k[n].y[0]<=k[n].y[1]?-1:1,color:v}),this._indexLabels.push({chartType:"rangeArea",dataPoint:k[n],dataSeries:p,indexKeyword:1,point:{x:m,y:r},direction:k[n].y[0]<=k[n].y[1]?1:-1,color:v})}else b(),x=!0;b();K.drawMarkers(e)}}c.restore();t&&this._eventManager.ghostCtx.restore();return{source:c,dest:this.plotArea.ctx,animationCallback:A.xClipAnimation,easingFunction:A.easing.linear,animationBase:0}}};u.prototype.renderRangeSplineArea=function(a){function b(){var a=
ha(q,2);if(0<a.length){c.beginPath();c.moveTo(a[0].x,a[0].y);t&&(d.beginPath(),d.moveTo(a[0].x,a[0].y));for(var b=0;b<a.length-3;b+=3)c.bezierCurveTo(a[b+1].x,a[b+1].y,a[b+2].x,a[b+2].y,a[b+3].x,a[b+3].y),t&&d.bezierCurveTo(a[b+1].x,a[b+1].y,a[b+2].x,a[b+2].y,a[b+3].x,a[b+3].y);0<h.lineThickness&&c.stroke();a=ha(r,2);c.lineTo(r[r.length-1].x,r[r.length-1].y);for(b=a.length-1;2<b;b-=3)c.bezierCurveTo(a[b-1].x,a[b-1].y,a[b-2].x,a[b-2].y,a[b-3].x,a[b-3].y),t&&d.bezierCurveTo(a[b-1].x,a[b-1].y,a[b-2].x,
a[b-2].y,a[b-3].x,a[b-3].y);c.closePath();c.globalAlpha=h.fillOpacity;c.fill();c.globalAlpha=1;if(0<h.lineThickness){c.beginPath();c.moveTo(r[r.length-1].x,r[r.length-1].y);for(b=a.length-1;2<b;b-=3)c.bezierCurveTo(a[b-1].x,a[b-1].y,a[b-2].x,a[b-2].y,a[b-3].x,a[b-3].y),t&&d.bezierCurveTo(a[b-1].x,a[b-1].y,a[b-2].x,a[b-2].y,a[b-3].x,a[b-3].y);c.stroke()}c.beginPath();t&&(d.closePath(),d.fill())}}var c=a.targetCanvasCtx||this.plotArea.ctx;if(!(0>=a.dataSeriesIndexes.length)){var d=this._eventManager.ghostCtx,
e=[],g=this.plotArea;c.save();t&&d.save();c.beginPath();c.rect(g.x1,g.y1,g.width,g.height);c.clip();t&&(d.beginPath(),d.rect(g.x1,g.y1,g.width,g.height),d.clip());for(g=0;g<a.dataSeriesIndexes.length;g++){var f=a.dataSeriesIndexes[g],h=this.data[f],p=h.dataPoints,e=h.id;this._eventManager.objectMap[e]={objectType:"dataSeries",dataSeriesIndex:f};e=B(e);d.fillStyle=e;var e=[],k=0,l,n,m,q=[],r=[];if(0<p.length){color=h._colorSet[k%h._colorSet.length];c.fillStyle=color;c.strokeStyle=color;c.lineWidth=
h.lineThickness;for(c.setLineDash&&c.setLineDash(M(h.lineDashType,h.lineThickness));k<p.length;k++)if(l=p[k].x.getTime?p[k].x.getTime():p[k].x,!(l<a.axisX.dataInfo.viewPortMin||l>a.axisX.dataInfo.viewPortMax))if(null!==p[k].y&&p[k].y.length&&"number"===typeof p[k].y[0]&&"number"===typeof p[k].y[1]){l=a.axisX.conversionParameters.reference+a.axisX.conversionParameters.pixelPerUnit*(l-a.axisX.conversionParameters.minimum)+0.5<<0;n=a.axisY.conversionParameters.reference+a.axisY.conversionParameters.pixelPerUnit*
(p[k].y[0]-a.axisY.conversionParameters.minimum)+0.5<<0;m=a.axisY.conversionParameters.reference+a.axisY.conversionParameters.pixelPerUnit*(p[k].y[1]-a.axisY.conversionParameters.minimum)+0.5<<0;var s=h.dataPointIds[k];this._eventManager.objectMap[s]={id:s,objectType:"dataPoint",dataSeriesIndex:f,dataPointIndex:k,x1:l,y1:n,y2:m};q[q.length]={x:l,y:n};r[r.length]={x:l,y:m};if(0!==p[k].markerSize&&(0<p[k].markerSize||0<h.markerSize)){var w=h.getMarkerProperties(k,l,n,c);e.push(w);var v=B(s);t&&e.push({x:l,
y:n,ctx:d,type:w.type,size:w.size,color:v,borderColor:v,borderThickness:w.borderThickness});w=h.getMarkerProperties(k,l,m,c);e.push(w);v=B(s);t&&e.push({x:l,y:m,ctx:d,type:w.type,size:w.size,color:v,borderColor:v,borderThickness:w.borderThickness})}if(p[k].indexLabel||h.indexLabel||p[k].indexLabelFormatter||h.indexLabelFormatter)this._indexLabels.push({chartType:"splineArea",dataPoint:p[k],dataSeries:h,indexKeyword:0,point:{x:l,y:n},direction:p[k].y[0]<=p[k].y[1]?-1:1,color:color}),this._indexLabels.push({chartType:"splineArea",
dataPoint:p[k],dataSeries:h,indexKeyword:1,point:{x:l,y:m},direction:p[k].y[0]<=p[k].y[1]?1:-1,color:color})}else 0<k&&(b(),q=[],r=[]);b();K.drawMarkers(e)}}c.restore();t&&this._eventManager.ghostCtx.restore();return{source:c,dest:this.plotArea.ctx,animationCallback:A.xClipAnimation,easingFunction:A.easing.linear,animationBase:0}}};var ra=function(a,b,c,d,e,g,f,h,p){"undefined"===typeof h&&(h=1);if(!t){var k=Number((f%(2*Math.PI)).toFixed(8));Number((g%(2*Math.PI)).toFixed(8))===k&&(f-=1E-4)}a.save();
a.globalAlpha=h;"pie"===e?(a.beginPath(),a.moveTo(b.x,b.y),a.arc(b.x,b.y,c,g,f,!1),a.fillStyle=d,a.strokeStyle="white",a.lineWidth=2,a.closePath(),a.fill()):"doughnut"===e&&(a.beginPath(),a.arc(b.x,b.y,c,g,f,!1),a.arc(b.x,b.y,p*c,f,g,!0),a.closePath(),a.fillStyle=d,a.strokeStyle="white",a.lineWidth=2,a.fill());a.globalAlpha=1;a.restore()};u.prototype.renderPie=function(a){function b(){if(k&&l){var a=0,b=0,c=0,d=0;for(y=0;y<l.length;y++){var e=l[y],g=k.dataPointIds[y],f={id:g,objectType:"dataPoint",
dataPointIndex:y,dataSeriesIndex:0};q.push(f);var h={percent:null,total:null},n=null,h=p.getPercentAndTotal(k,e);if(k.indexLabelFormatter||e.indexLabelFormatter)n={chart:p._options,dataSeries:k,dataPoint:e,total:h.total,percent:h.percent};h=e.indexLabelFormatter?e.indexLabelFormatter(n):e.indexLabel?p.replaceKeywordsWithValue(e.indexLabel,e,k,y):k.indexLabelFormatter?k.indexLabelFormatter(n):k.indexLabel?p.replaceKeywordsWithValue(k.indexLabel,e,k,y):e.label?e.label:"";p._eventManager.objectMap[g]=
f;f.center={x:x.x,y:x.y};f.y=e.y;f.radius=z;f.percentInnerRadius=D;f.indexLabelText=h;f.indexLabelPlacement=k.indexLabelPlacement;f.indexLabelLineColor=e.indexLabelLineColor?e.indexLabelLineColor:k.indexLabelLineColor?k.indexLabelLineColor:e.color?e.color:k._colorSet[y%k._colorSet.length];f.indexLabelLineThickness=e.indexLabelLineThickness?e.indexLabelLineThickness:k.indexLabelLineThickness;f.indexLabelLineDashType=e.indexLabelLineDashType?e.indexLabelLineDashType:k.indexLabelLineDashType;f.indexLabelFontColor=
e.indexLabelFontColor?e.indexLabelFontColor:k.indexLabelFontColor;f.indexLabelFontStyle=e.indexLabelFontStyle?e.indexLabelFontStyle:k.indexLabelFontStyle;f.indexLabelFontWeight=e.indexLabelFontWeight?e.indexLabelFontWeight:k.indexLabelFontWeight;f.indexLabelFontSize=e.indexLabelFontSize?e.indexLabelFontSize:k.indexLabelFontSize;f.indexLabelFontFamily=e.indexLabelFontFamily?e.indexLabelFontFamily:k.indexLabelFontFamily;f.indexLabelBackgroundColor=e.indexLabelBackgroundColor?e.indexLabelBackgroundColor:
k.indexLabelBackgroundColor?k.indexLabelBackgroundColor:null;f.indexLabelMaxWidth=e.indexLabelMaxWidth?e.indexLabelMaxWidth:k.indexLabelMaxWidth?k.indexLabelMaxWidth:0.33*m.width;f.indexLabelWrap="undefined"!==typeof e.indexLabelWrap?e.indexLabelWrap:k.indexLabelWrap;f.startAngle=0===y?k.startAngle?k.startAngle/180*Math.PI:0:q[y-1].endAngle;f.startAngle=(f.startAngle+2*Math.PI)%(2*Math.PI);f.endAngle=f.startAngle+2*Math.PI/u*Math.abs(e.y);e=(f.endAngle+f.startAngle)/2;e=(e+2*Math.PI)%(2*Math.PI);
f.midAngle=e;if(f.midAngle>Math.PI/2-t&&f.midAngle<Math.PI/2+t){if(0===a||q[c].midAngle>f.midAngle)c=y;a++}else if(f.midAngle>3*Math.PI/2-t&&f.midAngle<3*Math.PI/2+t){if(0===b||q[d].midAngle>f.midAngle)d=y;b++}f.hemisphere=e>Math.PI/2&&e<=3*Math.PI/2?"left":"right";f.indexLabelTextBlock=new H(p.plotArea.ctx,{fontSize:f.indexLabelFontSize,fontFamily:f.indexLabelFontFamily,fontColor:f.indexLabelFontColor,fontStyle:f.indexLabelFontStyle,fontWeight:f.indexLabelFontWeight,horizontalAlign:"left",backgroundColor:f.indexLabelBackgroundColor,
maxWidth:f.indexLabelMaxWidth,maxHeight:f.indexLabelWrap?5*f.indexLabelFontSize:1.5*f.indexLabelFontSize,text:f.indexLabelText,padding:0,textBaseline:"top"});f.indexLabelTextBlock.measureText()}g=e=0;h=!1;for(y=0;y<l.length;y++)f=q[(c+y)%l.length],1<a&&(f.midAngle>Math.PI/2-t&&f.midAngle<Math.PI/2+t)&&(e<=a/2&&!h?(f.hemisphere="right",e++):(f.hemisphere="left",h=!0));h=!1;for(y=0;y<l.length;y++)f=q[(d+y)%l.length],1<b&&(f.midAngle>3*Math.PI/2-t&&f.midAngle<3*Math.PI/2+t)&&(g<=b/2&&!h?(f.hemisphere=
"left",g++):(f.hemisphere="right",h=!0))}}function c(a){var b=p.plotArea.ctx;b.clearRect(m.x1,m.y1,m.width,m.height);b.fillStyle=p.backgroundColor;b.fillRect(m.x1,m.y1,m.width,m.height);for(b=0;b<l.length;b++){var c=q[b].startAngle,d=q[b].endAngle;if(d>c){var e=0.07*z*Math.cos(q[b].midAngle),g=0.07*z*Math.sin(q[b].midAngle),f=!1;if(l[b].exploded){if(1E-9<Math.abs(q[b].center.x-(x.x+e))||1E-9<Math.abs(q[b].center.y-(x.y+g)))q[b].center.x=x.x+e*a,q[b].center.y=x.y+g*a,f=!0}else if(0<Math.abs(q[b].center.x-
x.x)||0<Math.abs(q[b].center.y-x.y))q[b].center.x=x.x+e*(1-a),q[b].center.y=x.y+g*(1-a),f=!0;f&&(e={},e.dataSeries=k,e.dataPoint=k.dataPoints[b],e.index=b,p._toolTip.highlightObjects([e]));ra(p.plotArea.ctx,q[b].center,q[b].radius,l[b].color?l[b].color:k._colorSet[b%k._colorSet.length],k.type,c,d,k.fillOpacity,q[b].percentInnerRadius)}}a=p.plotArea.ctx;a.fillStyle="black";a.strokeStyle="grey";a.textBaseline="middle";a.lineJoin="round";for(b=b=0;b<l.length;b++)c=q[b],c.indexLabelText&&(c.indexLabelTextBlock.y-=
c.indexLabelTextBlock.height/2,d=0,d="left"===c.hemisphere?"inside"!==k.indexLabelPlacement?-(c.indexLabelTextBlock.width+n):-c.indexLabelTextBlock.width/2:"inside"!==k.indexLabelPlacement?n:-c.indexLabelTextBlock.width/2,c.indexLabelTextBlock.x+=d,c.indexLabelTextBlock.render(!0),c.indexLabelTextBlock.x-=d,c.indexLabelTextBlock.y+=c.indexLabelTextBlock.height/2,"inside"!==c.indexLabelPlacement&&(d=c.center.x+z*Math.cos(c.midAngle),e=c.center.y+z*Math.sin(c.midAngle),a.strokeStyle=c.indexLabelLineColor,
a.lineWidth=c.indexLabelLineThickness,a.setLineDash&&a.setLineDash(M(c.indexLabelLineDashType,c.indexLabelLineThickness)),a.beginPath(),a.moveTo(d,e),a.lineTo(c.indexLabelTextBlock.x,c.indexLabelTextBlock.y),a.lineTo(c.indexLabelTextBlock.x+("left"===c.hemisphere?-n:n),c.indexLabelTextBlock.y),a.stroke()),a.lineJoin="miter")}function d(a,b){var c=0,c=a.indexLabelTextBlock.y-a.indexLabelTextBlock.height/2,d=a.indexLabelTextBlock.y+a.indexLabelTextBlock.height/2,e=b.indexLabelTextBlock.y-b.indexLabelTextBlock.height/
2,g=b.indexLabelTextBlock.y+b.indexLabelTextBlock.height/2;return c=b.indexLabelTextBlock.y>a.indexLabelTextBlock.y?e-d:c-g}function e(a){for(var b=null,c=1;c<l.length;c++)if(b=(a+c+q.length)%q.length,q[b].hemisphere!==q[a].hemisphere){b=null;break}else if(q[b].indexLabelText&&b!==a&&(0>d(q[b],q[a])||("right"===q[a].hemisphere?q[b].indexLabelTextBlock.y>=q[a].indexLabelTextBlock.y:q[b].indexLabelTextBlock.y<=q[a].indexLabelTextBlock.y)))break;else b=null;return b}function g(a,b){b=b||0;var c=0,f=
x.y-1*indexLabelRadius,k=x.y+1*indexLabelRadius;if(0<=a&&a<l.length){var h=q[a];if(0>b&&h.indexLabelTextBlock.y<f||0<b&&h.indexLabelTextBlock.y>k)return 0;var m=b,n=0,p=0,p=n=n=0;0>m?h.indexLabelTextBlock.y-h.indexLabelTextBlock.height/2>f&&h.indexLabelTextBlock.y-h.indexLabelTextBlock.height/2+m<f&&(m=-(f-(h.indexLabelTextBlock.y-h.indexLabelTextBlock.height/2+m))):h.indexLabelTextBlock.y+h.indexLabelTextBlock.height/2<f&&h.indexLabelTextBlock.y+h.indexLabelTextBlock.height/2+m>k&&(m=h.indexLabelTextBlock.y+
h.indexLabelTextBlock.height/2+m-k);m=h.indexLabelTextBlock.y+m;f=0;f="right"===h.hemisphere?x.x+Math.sqrt(Math.pow(indexLabelRadius,2)-Math.pow(m-x.y,2)):x.x-Math.sqrt(Math.pow(indexLabelRadius,2)-Math.pow(m-x.y,2));p=x.x+z*Math.cos(h.midAngle);n=x.y+z*Math.sin(h.midAngle);n=Math.sqrt(Math.pow(f-p,2)+Math.pow(m-n,2));p=Math.acos(z/indexLabelRadius);n=Math.acos((indexLabelRadius*indexLabelRadius+z*z-n*n)/(2*z*indexLabelRadius));m=n<p?m-h.indexLabelTextBlock.y:0;f=null;for(k=1;k<l.length;k++)if(f=
(a-k+q.length)%q.length,q[f].hemisphere!==q[a].hemisphere){f=null;break}else if(q[f].indexLabelText&&q[f].hemisphere===q[a].hemisphere&&f!==a&&(0>d(q[f],q[a])||("right"===q[a].hemisphere?q[f].indexLabelTextBlock.y<=q[a].indexLabelTextBlock.y:q[f].indexLabelTextBlock.y>=q[a].indexLabelTextBlock.y)))break;else f=null;p=f;n=e(a);k=f=0;0>m?(k="right"===h.hemisphere?p:n,c=m,null!==k&&(p=-m,m=h.indexLabelTextBlock.y-h.indexLabelTextBlock.height/2-(q[k].indexLabelTextBlock.y+q[k].indexLabelTextBlock.height/
2),m-p<r&&(f=-p,k=g(k,f,recursionCount+1),+k.toFixed(v)>+f.toFixed(v)&&(c=m>r?-(m-r):-(p-(k-f)))))):0<m&&(k="right"===h.hemisphere?n:p,c=m,null!==k&&(p=m,m=q[k].indexLabelTextBlock.y-q[k].indexLabelTextBlock.height/2-(h.indexLabelTextBlock.y+h.indexLabelTextBlock.height/2),m-p<r&&(f=p,k=g(k,f,recursionCount+1),+k.toFixed(v)<+f.toFixed(v)&&(c=m>r?m-r:p-(f-k)))));c&&(m=h.indexLabelTextBlock.y+c,f=0,f="right"===h.hemisphere?x.x+Math.sqrt(Math.pow(indexLabelRadius,2)-Math.pow(m-x.y,2)):x.x-Math.sqrt(Math.pow(indexLabelRadius,
2)-Math.pow(m-x.y,2)),h.midAngle>Math.PI/2-t&&h.midAngle<Math.PI/2+t?(k=(a-1+q.length)%q.length,k=q[k],p=q[(a+1+q.length)%q.length],"left"===h.hemisphere&&"right"===k.hemisphere&&f>k.indexLabelTextBlock.x?f=k.indexLabelTextBlock.x-15:"right"===h.hemisphere&&("left"===p.hemisphere&&f<p.indexLabelTextBlock.x)&&(f=p.indexLabelTextBlock.x+15)):h.midAngle>3*Math.PI/2-t&&h.midAngle<3*Math.PI/2+t&&(k=(a-1+q.length)%q.length,k=q[k],p=q[(a+1+q.length)%q.length],"right"===h.hemisphere&&"left"===k.hemisphere&&
f<k.indexLabelTextBlock.x?f=k.indexLabelTextBlock.x+15:"left"===h.hemisphere&&("right"===p.hemisphere&&f>p.indexLabelTextBlock.x)&&(f=p.indexLabelTextBlock.x-15)),h.indexLabelTextBlock.y=m,h.indexLabelTextBlock.x=f,h.indexLabelAngle=Math.atan2(h.indexLabelTextBlock.y-x.y,h.indexLabelTextBlock.x-x.x))}return c}function f(){var a=p.plotArea.ctx;a.fillStyle="grey";a.strokeStyle="grey";a.font="16px Arial";a.textBaseline="middle";for(var b=a=0,c=0,f=!0,b=0;10>b&&(1>b||0<c);b++){if(k.radius||!k.radius&&
"undefined"!==typeof k.innerRadius&&null!==k.innerRadius&&z-c<=A)f=!1;f&&(z-=c);c=0;if("inside"!==k.indexLabelPlacement){indexLabelRadius=z*s;for(a=0;a<l.length;a++){var h=q[a];h.indexLabelTextBlock.x=x.x+indexLabelRadius*Math.cos(h.midAngle);h.indexLabelTextBlock.y=x.y+indexLabelRadius*Math.sin(h.midAngle);h.indexLabelAngle=h.midAngle;h.radius=z;h.percentInnerRadius=D}for(var t,w,a=0;a<l.length;a++){var h=q[a],u=e(a);if(null!==u){t=q[a];w=q[u];var y=0,y=d(t,w)-r;if(0>y){for(var B=w=0,C=0;C<l.length;C++)C!==
a&&q[C].hemisphere===h.hemisphere&&(q[C].indexLabelTextBlock.y<h.indexLabelTextBlock.y?w++:B++);w=y/(w+B||1)*B;var B=-1*(y-w),E=C=0;"right"===h.hemisphere?(C=g(a,w),B=-1*(y-C),E=g(u,B),+E.toFixed(v)<+B.toFixed(v)&&+C.toFixed(v)<=+w.toFixed(v)&&g(a,-(B-E))):(C=g(u,w),B=-1*(y-C),E=g(a,B),+E.toFixed(v)<+B.toFixed(v)&&+C.toFixed(v)<=+w.toFixed(v)&&g(u,-(B-E)))}}}}else for(a=0;a<l.length;a++)h=q[a],indexLabelRadius="pie"===k.type?0.7*z:0.8*z,u=x.x+indexLabelRadius*Math.cos(h.midAngle),w=x.y+indexLabelRadius*
Math.sin(h.midAngle),h.indexLabelTextBlock.x=u,h.indexLabelTextBlock.y=w;for(a=0;a<l.length;a++)if(h=q[a],u=h.indexLabelTextBlock.measureText(),0!==u.height&&0!==u.width)u=u=0,"right"===h.hemisphere?(u=m.x2-(h.indexLabelTextBlock.x+h.indexLabelTextBlock.width+n),u*=-1):u=m.x1-(h.indexLabelTextBlock.x-h.indexLabelTextBlock.width-n),0<u&&(!f&&h.indexLabelText&&(w="right"===h.hemisphere?m.x2-h.indexLabelTextBlock.x:h.indexLabelTextBlock.x-m.x1,0.3*h.indexLabelTextBlock.maxWidth>w?h.indexLabelText="":
h.indexLabelTextBlock.maxWidth=0.85*w,0.3*h.indexLabelTextBlock.maxWidth<w&&(h.indexLabelTextBlock.x-="right"===h.hemisphere?2:-2)),Math.abs(h.indexLabelTextBlock.y-h.indexLabelTextBlock.height/2-x.y)<z||Math.abs(h.indexLabelTextBlock.y+h.indexLabelTextBlock.height/2-x.y)<z)&&(u/=Math.abs(Math.cos(h.indexLabelAngle)),9<u&&(u*=0.3),u>c&&(c=u)),u=u=0,0<h.indexLabelAngle&&h.indexLabelAngle<Math.PI?(u=m.y2-(h.indexLabelTextBlock.y+h.indexLabelTextBlock.height/2+5),u*=-1):u=m.y1-(h.indexLabelTextBlock.y-
h.indexLabelTextBlock.height/2-5),0<u&&(!f&&h.indexLabelText&&(w=0<h.indexLabelAngle&&h.indexLabelAngle<Math.PI?-1:1,0===g(a,u*w)&&g(a,2*w)),Math.abs(h.indexLabelTextBlock.x-x.x)<z&&(u/=Math.abs(Math.sin(h.indexLabelAngle)),9<u&&(u*=0.3),u>c&&(c=u)));var F=function(a,b,c){for(var d=[],e=0;d.push(q[b]),b!==c;b=(b+1+l.length)%l.length);d.sort(function(a,b){return a.y-b.y});for(b=0;b<d.length;b++)if(c=d[b],e<0.7*a)e+=c.indexLabelTextBlock.height,c.indexLabelTextBlock.text="",c.indexLabelText="",c.indexLabelTextBlock.measureText();
else break};(function(){for(var a=-1,b=-1,c=0,f=!1,g=0;g<l.length;g++)if(f=!1,t=q[g],t.indexLabelText){var h=e(g);if(null!==h){var k=q[h];y=0;y=d(t,k);var m;if(m=0>y){m=t.indexLabelTextBlock.x;var p=t.indexLabelTextBlock.y-t.indexLabelTextBlock.height/2,r=t.indexLabelTextBlock.y+t.indexLabelTextBlock.height/2,s=k.indexLabelTextBlock.y-k.indexLabelTextBlock.height/2,w=k.indexLabelTextBlock.x+k.indexLabelTextBlock.width,v=k.indexLabelTextBlock.y+k.indexLabelTextBlock.height/2;m=t.indexLabelTextBlock.x+
t.indexLabelTextBlock.width<k.indexLabelTextBlock.x-n||m>w+n||p>v+n||r<s-n?!1:!0}m?(0>a&&(a=g),h!==a&&(b=h,c+=-y),0===g%Math.max(l.length/10,3)&&(f=!0)):f=!0;f&&(0<c&&0<=a&&0<=b)&&(F(c,a,b),b=a=-1,c=0)}}0<c&&F(c,a,b)})()}}function h(){p.plotArea.layoutManager.reset();p._title&&(p._title.dockInsidePlotArea||"center"===p._title.horizontalAlign&&"center"===p._title.verticalAlign)&&p._title.render();if(p.subtitles)for(var a=0;a<p.subtitles.length;a++){var b=p.subtitles[a];(b.dockInsidePlotArea||"center"===
b.horizontalAlign&&"center"===b.verticalAlign)&&b.render()}p.legend&&(p.legend.dockInsidePlotArea||"center"===p.legend.horizontalAlign&&"center"===p.legend.verticalAlign)&&p.legend.render()}var p=this;if(!(0>=a.dataSeriesIndexes.length)){var k=this.data[a.dataSeriesIndexes[0]],l=k.dataPoints,n=10,m=this.plotArea,q=[],r=2,s=1.3,t=20/180*Math.PI,v=6,x={x:(m.x2+m.x1)/2,y:(m.y2+m.y1)/2},u=0;a=!1;for(var y=0;y<l.length;y++)u+=Math.abs(l[y].y),!a&&("undefined"!==typeof l[y].indexLabel&&null!==l[y].indexLabel&&
0<l[y].indexLabel.toString().length)&&(a=!0),!a&&("undefined"!==typeof l[y].label&&null!==l[y].label&&0<l[y].label.toString().length)&&(a=!0);if(0!==u){a=a||"undefined"!==typeof k.indexLabel&&null!==k.indexLabel&&0<k.indexLabel.toString().length;var z="inside"!==k.indexLabelPlacement&&a?0.75*Math.min(m.width,m.height)/2:0.92*Math.min(m.width,m.height)/2;k.radius&&(z=ya(k.radius,z));var A="undefined"!==typeof k.innerRadius&&null!==k.innerRadius?ya(k.innerRadius,z):0.7*z,D=Math.min(A/z,(z-1)/z);this.pieDoughnutClickHandler=
function(a){p.isAnimating||(a=a.dataPoint,a.exploded=a.exploded?!1:!0,1<this.dataPoints.length&&p._animator.animate(0,500,function(a){c(a);h()}))};b();f();f();f();f();this.disableToolTip=!0;this._animator.animate(0,this.animatedRender?this.animationDuration:0,function(a){var b=p.plotArea.ctx;b.clearRect(m.x1,m.y1,m.width,m.height);b.fillStyle=p.backgroundColor;b.fillRect(m.x1,m.y1,m.width,m.height);a=q[0].startAngle+2*Math.PI*a;for(b=0;b<l.length;b++){var c=0===b?q[b].startAngle:d,d=c+(q[b].endAngle-
q[b].startAngle),e=!1;d>a&&(d=a,e=!0);var g=l[b].color?l[b].color:k._colorSet[b%k._colorSet.length];d>c&&ra(p.plotArea.ctx,q[b].center,q[b].radius,g,k.type,c,d,k.fillOpacity,q[b].percentInnerRadius);if(e)break}h()},function(){p.disableToolTip=!1;p._animator.animate(0,p.animatedRender?500:0,function(a){c(a);h()})})}}};u.prototype.animationRequestId=null;u.prototype.requestAnimFrame=function(){return window.requestAnimationFrame||window.webkitRequestAnimationFrame||window.mozRequestAnimationFrame||
window.oRequestAnimationFrame||window.msRequestAnimationFrame||function(a){window.setTimeout(a,1E3/60)}}();u.prototype.cancelRequestAnimFrame=window.cancelAnimationFrame||window.webkitCancelRequestAnimationFrame||window.mozCancelRequestAnimationFrame||window.oCancelRequestAnimationFrame||window.msCancelRequestAnimationFrame||clearTimeout;Y.prototype.registerSpace=function(a,b){"top"===a?this._topOccupied+=b.height:"bottom"===a?this._bottomOccupied+=b.height:"left"===a?this._leftOccupied+=b.width:
"right"===a&&(this._rightOccupied+=b.width)};Y.prototype.unRegisterSpace=function(a,b){"top"===a?this._topOccupied-=b.height:"bottom"===a?this._bottomOccupied-=b.height:"left"===a?this._leftOccupied-=b.width:"right"===a&&(this._rightOccupied-=b.width)};Y.prototype.getFreeSpace=function(){return{x1:this._x1+this._leftOccupied,y1:this._y1+this._topOccupied,x2:this._x2-this._rightOccupied,y2:this._y2-this._bottomOccupied,width:this._x2-this._x1-this._rightOccupied-this._leftOccupied,height:this._y2-
this._y1-this._bottomOccupied-this._topOccupied}};Y.prototype.reset=function(){this._rightOccupied=this._leftOccupied=this._bottomOccupied=this._topOccupied=this._padding};O(H,G);H.prototype.render=function(a){a&&this.ctx.save();var b=this.ctx.font;this.ctx.textBaseline=this.textBaseline;var c=0;this._isDirty&&this.measureText(this.ctx);this.ctx.translate(this.x,this.y+c);"middle"===this.textBaseline&&(c=-this._lineHeight/2);this.ctx.font=this._getFontString();this.ctx.rotate(Math.PI/180*this.angle);
var d=0,e=this.padding,g=null;(0<this.borderThickness&&this.borderColor||this.backgroundColor)&&this.ctx.roundRect(0,c,this.width,this.height,this.cornerRadius,this.borderThickness,this.backgroundColor,this.borderColor);this.ctx.fillStyle=this.fontColor;for(c=0;c<this._wrappedText.lines.length;c++)g=this._wrappedText.lines[c],"right"===this.horizontalAlign?d=this.width-g.width-this.padding:"left"===this.horizontalAlign?d=this.padding:"center"===this.horizontalAlign&&(d=(this.width-2*this.padding)/
2-g.width/2+this.padding),this.ctx.fillText(g.text,d,e),e+=g.height;this.ctx.font=b;a&&this.ctx.restore()};H.prototype.setText=function(a){this.text=a;this._isDirty=!0;this._wrappedText=null};H.prototype.measureText=function(){if(null===this.maxWidth)throw"Please set maxWidth and height for TextBlock";this._wrapText(this.ctx);this._isDirty=!1;return{width:this.width,height:this.height}};H.prototype._getLineWithWidth=function(a,b,c){a=String(a);if(!a)return{text:"",width:0};var d=c=0,e=a.length-1,
g=Infinity;for(this.ctx.font=this._getFontString();d<=e;){var g=Math.floor((d+e)/2),f=a.substr(0,g+1);c=this.ctx.measureText(f).width;if(c<b)d=g+1;else if(c>b)e=g-1;else break}c>b&&1<f.length&&(f=f.substr(0,f.length-1),c=this.ctx.measureText(f).width);b=!0;if(f.length===a.length||" "===a[f.length])b=!1;b&&(a=f.split(" "),1<a.length&&a.pop(),f=a.join(" "),c=this.ctx.measureText(f).width);return{text:f,width:c}};H.prototype._wrapText=function(){var a=new String(Z(String(this.text))),b=[],c=this.ctx.font,
d=0,e=0;for(this.ctx.font=this._getFontString();0<a.length;){var g=this.maxHeight-2*this.padding,f=this._getLineWithWidth(a,this.maxWidth-2*this.padding,!1);f.height=this._lineHeight;b.push(f);e=Math.max(e,f.width);d+=f.height;a=Z(a.slice(f.text.length,a.length));g&&d>g&&(f=b.pop(),d-=f.height)}this._wrappedText={lines:b,width:e,height:d};this.width=e+2*this.padding;this.height=d+2*this.padding;this.ctx.font=c};H.prototype._getFontString=function(){var a;a=""+(this.fontStyle?this.fontStyle+" ":"");
a+=this.fontWeight?this.fontWeight+" ":"";a+=this.fontSize?this.fontSize+"px ":"";var b=this.fontFamily?this.fontFamily+"":"";!t&&b&&(b=b.split(",")[0],"'"!==b[0]&&'"'!==b[0]&&(b="'"+b+"'"));return a+=b};O(aa,G);aa.prototype.render=function(){if(this.text){var a=this.dockInsidePlotArea?this.chart.plotArea:this.chart,b=a.layoutManager.getFreeSpace(),c=b.x1,d=b.y1,e=0,g=0,f=this.chart._menuButton&&this.chart.exportEnabled&&"top"===this.verticalAlign?22:0,h,p;"top"===this.verticalAlign||"bottom"===this.verticalAlign?
(null===this.maxWidth&&(this.maxWidth=b.width-4-f*("center"===this.horizontalAlign?2:1)),g=0.5*b.height-this.margin-2,e=0):"center"===this.verticalAlign&&("left"===this.horizontalAlign||"right"===this.horizontalAlign?(null===this.maxWidth&&(this.maxWidth=b.height-4),g=0.5*b.width-this.margin-2):"center"===this.horizontalAlign&&(null===this.maxWidth&&(this.maxWidth=b.width-4),g=0.5*b.height-4));this.wrap||(g=Math.min(g,Math.max(1.5*this.fontSize,this.fontSize+2.5*this.padding)));var g=new H(this.ctx,
{fontSize:this.fontSize,fontFamily:this.fontFamily,fontColor:this.fontColor,fontStyle:this.fontStyle,fontWeight:this.fontWeight,horizontalAlign:this.horizontalAlign,verticalAlign:this.verticalAlign,borderColor:this.borderColor,borderThickness:this.borderThickness,backgroundColor:this.backgroundColor,maxWidth:this.maxWidth,maxHeight:g,cornerRadius:this.cornerRadius,text:this.text,padding:this.padding,textBaseline:"top"}),k=g.measureText();"top"===this.verticalAlign||"bottom"===this.verticalAlign?("top"===
this.verticalAlign?(d=b.y1+2,p="top"):"bottom"===this.verticalAlign&&(d=b.y2-2-k.height,p="bottom"),"left"===this.horizontalAlign?c=b.x1+2:"center"===this.horizontalAlign?c=b.x1+b.width/2-k.width/2:"right"===this.horizontalAlign&&(c=b.x2-2-k.width-f),h=this.horizontalAlign,this.width=k.width,this.height=k.height):"center"===this.verticalAlign&&("left"===this.horizontalAlign?(c=b.x1+2,d=b.y2-2-(this.maxWidth/2-k.width/2),e=-90,p="left",this.width=k.height,this.height=k.width):"right"===this.horizontalAlign?
(c=b.x2-2,d=b.y1+2+(this.maxWidth/2-k.width/2),e=90,p="right",this.width=k.height,this.height=k.width):"center"===this.horizontalAlign&&(d=a.y1+(a.height/2-k.height/2),c=a.x1+(a.width/2-k.width/2),p="center",this.width=k.width,this.height=k.height),h="center");g.x=c;g.y=d;g.angle=e;g.horizontalAlign=h;g.render(!0);a.layoutManager.registerSpace(p,{width:this.width+("left"===p||"right"===p?this.margin+2:0),height:this.height+("top"===p||"bottom"===p?this.margin+2:0)});this.bounds={x1:c,y1:d,x2:c+this.width,
y2:d+this.height};this.ctx.textBaseline="top"}};O(ia,G);ia.prototype.render=aa.prototype.render;O(ja,G);ja.prototype.render=function(){var a=this.dockInsidePlotArea?this.chart.plotArea:this.chart,b=a.layoutManager.getFreeSpace(),c=null,d=0,e=0,g=0,f=0,h=[],p=[];"top"===this.verticalAlign||"bottom"===this.verticalAlign?(this.orientation="horizontal",c=this.verticalAlign,g=null!==this.maxWidth?this.maxWidth:0.7*b.width,f=null!==this.maxHeight?this.maxHeight:0.5*b.height):"center"===this.verticalAlign&&
(this.orientation="vertical",c=this.horizontalAlign,g=null!==this.maxWidth?this.maxWidth:0.5*b.width,f=null!==this.maxHeight?this.maxHeight:0.7*b.height);for(var k=0;k<this.dataSeries.length;k++){var l=this.dataSeries[k];if("pie"!==l.type&&"doughnut"!==l.type&&"funnel"!==l.type){var n=l.legendMarkerType?l.legendMarkerType:"line"!==l.type&&"stepLine"!==l.type&&"spline"!==l.type&&"scatter"!==l.type&&"bubble"!==l.type||!l.markerType?S.getDefaultLegendMarker(l.type):l.markerType,m=l.legendText?l.legendText:
this.itemTextFormatter?this.itemTextFormatter({chart:this.chart,legend:this._options,dataSeries:l,dataPoint:null}):l.name,q=l.legendMarkerColor?l.legendMarkerColor:l.markerColor?l.markerColor:l._colorSet[0],r=l.markerSize||"line"!==l.type&&"stepLine"!==l.type&&"spline"!==l.type?0.6*this.lineHeight:0,s=l.legendMarkerBorderColor?l.legendMarkerBorderColor:l.markerBorderColor,t=l.legendMarkerBorderThickness?l.legendMarkerBorderThickness:l.markerBorderThickness?Math.max(1,Math.round(0.2*r)):0,m=this.chart.replaceKeywordsWithValue(m,
l.dataPoints[0],l,k),n={markerType:n,markerColor:q,text:m,textBlock:null,chartType:l.type,markerSize:r,lineColor:l._colorSet[0],dataSeriesIndex:l.index,dataPointIndex:null,markerBorderColor:s,markerBorderThickness:t};h.push(n)}else for(var v=0;v<l.dataPoints.length;v++){var u=l.dataPoints[v],n=u.legendMarkerType?u.legendMarkerType:l.legendMarkerType?l.legendMarkerType:S.getDefaultLegendMarker(l.type),m=u.legendText?u.legendText:l.legendText?l.legendText:this.itemTextFormatter?this.itemTextFormatter({chart:this.chart,
legend:this._options,dataSeries:l,dataPoint:u}):u.name?u.name:"DataPoint: "+(v+1),q=u.legendMarkerColor?u.legendMarkerColor:l.legendMarkerColor?l.legendMarkerColor:u.color?u.color:l.color?l.color:l._colorSet[v%l._colorSet.length],r=0.6*this.lineHeight,s=u.legendMarkerBorderColor?u.legendMarkerBorderColor:l.legendMarkerBorderColor?l.legendMarkerBorderColor:u.markerBorderColor?u.markerBorderColor:l.markerBorderColor,t=u.legendMarkerBorderThickness?u.legendMarkerBorderThickness:l.legendMarkerBorderThickness?
l.legendMarkerBorderThickness:u.markerBorderThickness||l.markerBorderThickness?Math.max(1,Math.round(0.2*r)):0,m=this.chart.replaceKeywordsWithValue(m,u,l,v),n={markerType:n,markerColor:q,text:m,textBlock:null,chartType:l.type,markerSize:r,dataSeriesIndex:k,dataPointIndex:v,markerBorderColor:s,markerBorderThickness:t};(u.showInLegend||l.showInLegend&&!1!==u.showInLegend)&&h.push(n)}}!0===this.reversed&&h.reverse();if(0<h.length){l=null;v=m=u=0;m=null!==this.itemWidth?null!==this.itemMaxWidth?Math.min(this.itemWidth,
this.itemMaxWidth,g):Math.min(this.itemWidth,g):null!==this.itemMaxWidth?Math.min(this.itemMaxWidth,g):g;r=0===r?0.6*this.lineHeight:r;m-=r+0.1*this.horizontalSpacing;for(k=0;k<h.length;k++){n=h[k];if("line"===n.chartType||"spline"===n.chartType||"stepLine"===n.chartType)m-=2*0.1*this.lineHeight;if(!(0>=f||"undefined"===typeof f||0>=m||"undefined"===typeof m)){if("horizontal"===this.orientation){n.textBlock=new H(this.ctx,{x:0,y:0,maxWidth:m,maxHeight:this.itemWrap?f:this.lineHeight,angle:0,text:n.text,
horizontalAlign:"left",fontSize:this.fontSize,fontFamily:this.fontFamily,fontWeight:this.fontWeight,fontColor:this.fontColor,fontStyle:this.fontStyle,textBaseline:"top"});n.textBlock.measureText();null!==this.itemWidth&&(n.textBlock.width=this.itemWidth-(r+0.1*this.horizontalSpacing+("line"===n.chartType||"spline"===n.chartType||"stepLine"===n.chartType?2*0.1*this.lineHeight:0)));if(!l||l.width+Math.round(n.textBlock.width+0.1*this.horizontalSpacing+r+(0===l.width?0:this.horizontalSpacing)+("line"===
n.chartType||"spline"===n.chartType||"stepLine"===n.chartType?2*0.1*this.lineHeight:0))>g)l={items:[],width:0},p.push(l),this.height+=v,v=0;v=Math.max(v,n.textBlock.height)}else n.textBlock=new H(this.ctx,{x:0,y:0,maxWidth:m,maxHeight:!0===this.itemWrap?f:1.5*this.fontSize,angle:0,text:n.text,horizontalAlign:"left",fontSize:this.fontSize,fontFamily:this.fontFamily,fontWeight:this.fontWeight,fontColor:this.fontColor,fontStyle:this.fontStyle,textBaseline:"top"}),n.textBlock.measureText(),null!==this.itemWidth&&
(n.textBlock.width=this.itemWidth-(r+0.1*this.horizontalSpacing+("line"===n.chartType||"spline"===n.chartType||"stepLine"===n.chartType?2*0.1*this.lineHeight:0))),this.height<=f?(l={items:[],width:0},p.push(l)):(l=p[u],u=(u+1)%p.length),this.height+=n.textBlock.height;n.textBlock.x=l.width;n.textBlock.y=0;l.width+=Math.round(n.textBlock.width+0.1*this.horizontalSpacing+r+(0===l.width?0:this.horizontalSpacing)+("line"===n.chartType||"spline"===n.chartType||"stepLine"===n.chartType?2*0.1*this.lineHeight:
0));l.items.push(n);this.width=Math.max(l.width,this.width)}}this.height=!1===this.itemWrap?p.length*this.lineHeight:this.height+v;this.height=Math.min(f,this.height);this.width=Math.min(g,this.width)}"top"===this.verticalAlign?(e="left"===this.horizontalAlign?b.x1:"right"===this.horizontalAlign?b.x2-this.width:b.x1+b.width/2-this.width/2,d=b.y1):"center"===this.verticalAlign?(e="left"===this.horizontalAlign?b.x1:"right"===this.horizontalAlign?b.x2-this.width:b.x1+b.width/2-this.width/2,d=b.y1+b.height/
2-this.height/2):"bottom"===this.verticalAlign&&(e="left"===this.horizontalAlign?b.x1:"right"===this.horizontalAlign?b.x2-this.width:b.x1+b.width/2-this.width/2,d=b.y2-this.height);this.items=h;for(k=0;k<this.items.length;k++)n=h[k],n.id=++this.chart._eventManager.lastObjectId,this.chart._eventManager.objectMap[n.id]={id:n.id,objectType:"legendItem",legendItemIndex:k,dataSeriesIndex:n.dataSeriesIndex,dataPointIndex:n.dataPointIndex};for(k=b=0;k<p.length;k++){l=p[k];for(h=v=0;h<l.items.length;h++){n=
l.items[h];m=n.textBlock.x+e+(0===h?0.2*r:this.horizontalSpacing);q=d+b;u=m;this.chart.data[n.dataSeriesIndex].visible||(this.ctx.globalAlpha=0.5);this.ctx.save();this.ctx.rect(e,d,g,f);this.ctx.clip();if("line"===n.chartType||"stepLine"===n.chartType||"spline"===n.chartType)this.ctx.strokeStyle=n.lineColor,this.ctx.lineWidth=Math.ceil(this.lineHeight/8),this.ctx.beginPath(),this.ctx.moveTo(m-0.1*this.lineHeight,q+this.lineHeight/2),this.ctx.lineTo(m+0.7*this.lineHeight,q+this.lineHeight/2),this.ctx.stroke(),
u-=0.1*this.lineHeight;K.drawMarker(m+r/2,q+this.lineHeight/2,this.ctx,n.markerType,n.markerSize,n.markerColor,n.markerBorderColor,n.markerBorderThickness);n.textBlock.x=m+0.1*this.horizontalSpacing+r;if("line"===n.chartType||"stepLine"===n.chartType||"spline"===n.chartType)n.textBlock.x+=0.1*this.lineHeight;n.textBlock.y=q;n.textBlock.render(!0);this.ctx.restore();v=0<h?Math.max(v,n.textBlock.height):n.textBlock.height;this.chart.data[n.dataSeriesIndex].visible||(this.ctx.globalAlpha=1);m=B(n.id);
this.ghostCtx.fillStyle=m;this.ghostCtx.beginPath();this.ghostCtx.fillRect(u,n.textBlock.y,n.textBlock.x+n.textBlock.width-u,n.textBlock.height);n.x1=this.chart._eventManager.objectMap[n.id].x1=u;n.y1=this.chart._eventManager.objectMap[n.id].y1=n.textBlock.y;n.x2=this.chart._eventManager.objectMap[n.id].x2=n.textBlock.x+n.textBlock.width;n.y2=this.chart._eventManager.objectMap[n.id].y2=n.textBlock.y+n.textBlock.height}b+=v}a.layoutManager.registerSpace(c,{width:this.width+2+2,height:this.height+5+
5});this.bounds={x1:e,y1:d,x2:e+this.width,y2:d+this.height}};O(oa,G);oa.prototype.render=function(){var a=this.chart.layoutManager.getFreeSpace();this.ctx.fillStyle="red";this.ctx.fillRect(a.x1,a.y1,a.x2,a.y2)};O(S,G);S.prototype.getDefaultAxisPlacement=function(){var a=this.type;if("column"===a||"line"===a||"stepLine"===a||"spline"===a||"area"===a||"stepArea"===a||"splineArea"===a||"stackedColumn"===a||"stackedLine"===a||"bubble"===a||"scatter"===a||"stackedArea"===a||"stackedColumn100"===a||"stackedLine100"===
a||"stackedArea100"===a||"candlestick"===a||"ohlc"===a||"rangeColumn"===a||"rangeArea"===a||"rangeSplineArea"===a)return"normal";if("bar"===a||"stackedBar"===a||"stackedBar100"===a||"rangeBar"===a)return"xySwapped";if("pie"===a||"doughnut"===a||"funnel"===a)return"none";window.console.log("Unknown Chart Type: "+a);return null};S.getDefaultLegendMarker=function(a){if("column"===a||"stackedColumn"===a||"stackedLine"===a||"bar"===a||"stackedBar"===a||"stackedBar100"===a||"bubble"===a||"scatter"===a||
"stackedColumn100"===a||"stackedLine100"===a||"stepArea"===a||"candlestick"===a||"ohlc"===a||"rangeColumn"===a||"rangeBar"===a||"rangeArea"===a||"rangeSplineArea"===a)return"square";if("line"===a||"stepLine"===a||"spline"===a||"pie"===a||"doughnut"===a||"funnel"===a)return"circle";if("area"===a||"splineArea"===a||"stackedArea"===a||"stackedArea100"===a)return"triangle";window.console.log("Unknown Chart Type: "+a);return null};S.prototype.getDataPointAtX=function(a,b){if(!this.dataPoints||0===this.dataPoints.length)return null;
var c={dataPoint:null,distance:Infinity,index:NaN},d=null,e=0,g=0,f=1,h=Infinity,p=0,k=0,l=0;"none"!==this.chart.plotInfo.axisPlacement&&(l=this.dataPoints[this.dataPoints.length-1].x-this.dataPoints[0].x,l=0<l?Math.min(Math.max((this.dataPoints.length-1)/l*(a-this.dataPoints[0].x)>>0,0),this.dataPoints.length):0);for(;;){g=0<f?l+e:l-e;if(0<=g&&g<this.dataPoints.length){var d=this.dataPoints[g],n=Math.abs(d.x-a);n<c.distance&&(c.dataPoint=d,c.distance=n,c.index=g);d=Math.abs(d.x-a);d<=h?h=d:0<f?p++:
k++;if(1E3<p&&1E3<k)break}else if(0>l-e&&l+e>=this.dataPoints.length)break;-1===f?(e++,f=1):f=-1}return b||c.dataPoint.x!==a?b&&null!==c.dataPoint?c:null:c};S.prototype.getDataPointAtXY=function(a,b,c){if(!this.dataPoints||0===this.dataPoints.length)return null;c=c||!1;var d=[],e=0,g=0,f=1,h=!1,p=Infinity,k=0,l=0,n=0;"none"!==this.chart.plotInfo.axisPlacement&&(n=this.chart.axisX.getXValueAt({x:a,y:b}),g=this.dataPoints[this.dataPoints.length-1].x-this.dataPoints[0].x,n=0<g?Math.min(Math.max((this.dataPoints.length-
1)/g*(n-this.dataPoints[0].x)>>0,0),this.dataPoints.length):0);for(;;){g=0<f?n+e:n-e;if(0<=g&&g<this.dataPoints.length){var m=this.chart._eventManager.objectMap[this.dataPointIds[g]],q=this.dataPoints[g],r=null;if(m){switch(this.type){case "column":case "stackedColumn":case "stackedColumn100":case "bar":case "stackedBar":case "stackedBar100":case "rangeColumn":case "rangeBar":a>=m.x1&&(a<=m.x2&&b>=m.y1&&b<=m.y2)&&(d.push({dataPoint:q,dataPointIndex:g,dataSeries:this,distance:Math.min(Math.abs(m.x1-
a),Math.abs(m.x2-a),Math.abs(m.y1-b),Math.abs(m.y2-b))}),h=!0);break;case "line":case "stepLine":case "spline":case "area":case "stepArea":case "stackedArea":case "stackedArea100":case "splineArea":case "scatter":var s=N("markerSize",q,this)||4,t=c?20:s,r=Math.sqrt(Math.pow(m.x1-a,2)+Math.pow(m.y1-b,2));r<=t&&d.push({dataPoint:q,dataPointIndex:g,dataSeries:this,distance:r});g=Math.abs(m.x1-a);g<=p?p=g:0<f?k++:l++;r<=s/2&&(h=!0);break;case "rangeArea":case "rangeSplineArea":s=N("markerSize",q,this)||
4;t=c?20:s;r=Math.min(Math.sqrt(Math.pow(m.x1-a,2)+Math.pow(m.y1-b,2)),Math.sqrt(Math.pow(m.x1-a,2)+Math.pow(m.y2-b,2)));r<=t&&d.push({dataPoint:q,dataPointIndex:g,dataSeries:this,distance:r});g=Math.abs(m.x1-a);g<=p?p=g:0<f?k++:l++;r<=s/2&&(h=!0);break;case "bubble":s=m.size;r=Math.sqrt(Math.pow(m.x1-a,2)+Math.pow(m.y1-b,2));r<=s/2&&(d.push({dataPoint:q,dataPointIndex:g,dataSeries:this,distance:r}),h=!0);break;case "pie":case "doughnut":s=m.center;t="doughnut"===this.type?m.percentInnerRadius*m.radius:
0;r=Math.sqrt(Math.pow(s.x-a,2)+Math.pow(s.y-b,2));r<m.radius&&r>t&&(r=Math.atan2(b-s.y,a-s.x),0>r&&(r+=2*Math.PI),r=Number(((180*(r/Math.PI)%360+360)%360).toFixed(12)),s=Number(((180*(m.startAngle/Math.PI)%360+360)%360).toFixed(12)),t=Number(((180*(m.endAngle/Math.PI)%360+360)%360).toFixed(12)),0===t&&1<m.endAngle&&(t=360),s>=t&&0!==q.y&&(t+=360,r<s&&(r+=360)),r>s&&r<t&&(d.push({dataPoint:q,dataPointIndex:g,dataSeries:this,distance:0}),h=!0));break;case "candlestick":if(a>=m.x1-m.borderThickness/
2&&a<=m.x2+m.borderThickness/2&&b>=m.y2-m.borderThickness/2&&b<=m.y3+m.borderThickness/2||Math.abs(m.x2-a+m.x1-a)<m.borderThickness&&b>=m.y1&&b<=m.y4)d.push({dataPoint:q,dataPointIndex:g,dataSeries:this,distance:Math.min(Math.abs(m.x1-a),Math.abs(m.x2-a),Math.abs(m.y2-b),Math.abs(m.y3-b))}),h=!0;break;case "ohlc":if(Math.abs(m.x2-a+m.x1-a)<m.borderThickness&&b>=m.y2&&b<=m.y3||a>=m.x1&&a<=(m.x2+m.x1)/2&&b>=m.y1-m.borderThickness/2&&b<=m.y1+m.borderThickness/2||a>=(m.x1+m.x2)/2&&a<=m.x2&&b>=m.y4-m.borderThickness/
2&&b<=m.y4+m.borderThickness/2)d.push({dataPoint:q,dataPointIndex:g,dataSeries:this,distance:Math.min(Math.abs(m.x1-a),Math.abs(m.x2-a),Math.abs(m.y2-b),Math.abs(m.y3-b))}),h=!0}if(h||1E3<k&&1E3<l)break}}else if(0>n-e&&n+e>=this.dataPoints.length)break;-1===f?(e++,f=1):f=-1}a=null;for(b=0;b<d.length;b++)a?d[b].distance<=a.distance&&(a=d[b]):a=d[b];return a};S.prototype.getMarkerProperties=function(a,b,c,d){var e=this.dataPoints;return{x:b,y:c,ctx:d,type:e[a].markerType?e[a].markerType:this.markerType,
size:e[a].markerSize?e[a].markerSize:this.markerSize,color:e[a].markerColor?e[a].markerColor:this.markerColor?this.markerColor:e[a].color?e[a].color:this.color?this.color:this._colorSet[a%this._colorSet.length],borderColor:e[a].markerBorderColor?e[a].markerBorderColor:this.markerBorderColor?this.markerBorderColor:null,borderThickness:e[a].markerBorderThickness?e[a].markerBorderThickness:this.markerBorderThickness?this.markerBorderThickness:null}};O(C,G);C.prototype.createLabels=function(){var a,b=
0,c,d=0,e=0,b=0;if("bottom"===this._position||"top"===this._position)b=this.lineCoordinates.width/Math.abs(this.viewportMaximum-this.viewportMinimum)*this.interval,d=this.labelAutoFit?"undefined"===typeof this._options.labelMaxWidth?0.9*b>>0:this.labelMaxWidth:"undefined"===typeof this._options.labelMaxWidth?0.7*this.chart.width>>0:this.labelMaxWidth,e="undefined"===typeof this._options.labelWrap||this.labelWrap?0.5*this.chart.height>>0:1.5*this.labelFontSize;else if("left"===this._position||"right"===
this._position)b=this.lineCoordinates.height/Math.abs(this.viewportMaximum-this.viewportMinimum)*this.interval,d=this.labelAutoFit?"undefined"===typeof this._options.labelMaxWidth?0.3*this.chart.width>>0:this.labelMaxWidth:"undefined"===typeof this._options.labelMaxWidth?0.5*this.chart.width>>0:this.labelMaxWidth,e="undefined"===typeof this._options.labelWrap||this.labelWrap?2*b>>0:1.5*this.labelFontSize;if("axisX"===this.type&&"dateTime"===this.chart.plotInfo.axisXValueType)for(c=sa(new Date(this.viewportMaximum),
this.interval,this.intervalType),b=this.intervalStartPosition;b<c;sa(b,this.interval,this.intervalType))a=b.getTime(),a=this.labelFormatter?this.labelFormatter({chart:this.chart,axis:this._options,value:b,label:this.labels[b]?this.labels[b]:null}):"axisX"===this.type&&this.labels[a]?this.labels[a]:qa(b,this.valueFormatString,this.chart._cultureInfo),a=new H(this.ctx,{x:0,y:0,maxWidth:d,maxHeight:e,angle:this.labelAngle,text:this.prefix+a+this.suffix,horizontalAlign:"left",fontSize:this.labelFontSize,
fontFamily:this.labelFontFamily,fontWeight:this.labelFontWeight,fontColor:this.labelFontColor,fontStyle:this.labelFontStyle,textBaseline:"middle"}),this._labels.push({position:b.getTime(),textBlock:a,effectiveHeight:null});else{c=this.viewportMaximum;if(this.labels&&this.labels.length){a=Math.ceil(this.interval);for(var g=Math.ceil(this.intervalStartPosition),f=!1,b=g;b<this.viewportMaximum;b+=a)if(this.labels[b])f=!0;else{f=!1;break}f&&(this.interval=a,this.intervalStartPosition=g)}for(b=this.intervalStartPosition;b<=
c;b=parseFloat((b+this.interval).toFixed(14)))a=this.labelFormatter?this.labelFormatter({chart:this.chart,axis:this._options,value:b,label:this.labels[b]?this.labels[b]:null}):"axisX"===this.type&&this.labels[b]?this.labels[b]:W(b,this.valueFormatString,this.chart._cultureInfo),a=new H(this.ctx,{x:0,y:0,maxWidth:d,maxHeight:e,angle:this.labelAngle,text:this.prefix+a+this.suffix,horizontalAlign:"left",fontSize:this.labelFontSize,fontFamily:this.labelFontFamily,fontWeight:this.labelFontWeight,fontColor:this.labelFontColor,
fontStyle:this.labelFontStyle,textBaseline:"middle",borderThickness:0}),this._labels.push({position:b,textBlock:a,effectiveHeight:null})}for(b=0;b<this.stripLines.length;b++)c=this.stripLines[b],a=new H(this.ctx,{x:0,y:0,backgroundColor:c.labelBackgroundColor,maxWidth:d,maxHeight:e,angle:this.labelAngle,text:c.labelFormatter?c.labelFormatter({chart:this.chart,axis:this,stripLine:c}):c.label,horizontalAlign:"left",fontSize:c.labelFontSize,fontFamily:c.labelFontFamily,fontWeight:c.labelFontWeight,fontColor:c._options.labelFontColor||
c.color,fontStyle:c.labelFontStyle,textBaseline:"middle",borderThickness:0}),this._labels.push({position:c.value,textBlock:a,effectiveHeight:null,stripLine:c})};C.prototype.createLabelsAndCalculateWidth=function(){var a=0;this._labels=[];if("left"===this._position||"right"===this._position)for(this.createLabels(),i=0;i<this._labels.length;i++){var b=this._labels[i].textBlock.measureText(),c=0,c=0===this.labelAngle?b.width:b.width*Math.cos(Math.PI/180*Math.abs(this.labelAngle))+b.height/2*Math.sin(Math.PI/
180*Math.abs(this.labelAngle));a<c&&(a=c);this._labels[i].effectiveWidth=c}return(this.title?da(this.titleFontFamily,this.titleFontSize,this.titleFontWeight)+2:0)+a+this.tickLength+5};C.prototype.createLabelsAndCalculateHeight=function(){var a=0;this._labels=[];var b,c=0;this.createLabels();if("bottom"===this._position||"top"===this._position)for(c=0;c<this._labels.length;c++){b=this._labels[c].textBlock;b=b.measureText();var d=0,d=0===this.labelAngle?b.height:b.width*Math.sin(Math.PI/180*Math.abs(this.labelAngle))+
b.height/2*Math.cos(Math.PI/180*Math.abs(this.labelAngle));a<d&&(a=d);this._labels[c].effectiveHeight=d}return(this.title?da(this.titleFontFamily,this.titleFontSize,this.titleFontWeight)+2:0)+a+this.tickLength+5};C.setLayoutAndRender=function(a,b,c,d,e){var g,f,h,p=a.chart,k=p.ctx;a.calculateAxisParameters();b&&b.calculateAxisParameters();c&&c.calculateAxisParameters();var l=b?b.margin:0;if("normal"===d){a.lineCoordinates={};var n=Math.ceil(b?b.createLabelsAndCalculateWidth():0);g=Math.round(e.x1+
n+l);a.lineCoordinates.x1=g;l=Math.ceil(c?c.createLabelsAndCalculateWidth():0);f=Math.round(e.x2-l>a.chart.width-10?a.chart.width-10:e.x2-l);a.lineCoordinates.x2=f;a.lineCoordinates.width=Math.abs(f-g);var m=Math.ceil(a.createLabelsAndCalculateHeight());d=Math.round(e.y2-m-a.margin);h=Math.round(e.y2-a.margin);a.lineCoordinates.y1=d;a.lineCoordinates.y2=d;a.boundingRect={x1:g,y1:d,x2:f,y2:h,width:f-g,height:h-d};b&&(g=Math.round(e.x1+b.margin),d=Math.round(10>e.y1?10:e.y1),f=Math.round(e.x1+n+b.margin),
h=Math.round(e.y2-m-a.margin),b.lineCoordinates={x1:f,y1:d,x2:f,y2:h,height:Math.abs(h-d)},b.boundingRect={x1:g,y1:d,x2:f,y2:h,width:f-g,height:h-d});c&&(g=Math.round(a.lineCoordinates.x2),d=Math.round(10>e.y1?10:e.y1),f=Math.round(g+l+c.margin),h=Math.round(e.y2-m-a.margin),c.lineCoordinates={x1:g,y1:d,x2:g,y2:h,height:Math.abs(h-d)},c.boundingRect={x1:g,y1:d,x2:f,y2:h,width:f-g,height:h-d});a.calculateValueToPixelConversionParameters();b&&b.calculateValueToPixelConversionParameters();c&&c.calculateValueToPixelConversionParameters();
k.save();k.rect(5,a.boundingRect.y1,a.chart.width-10,a.boundingRect.height);k.clip();a.renderLabelsTicksAndTitle();k.restore();b&&b.renderLabelsTicksAndTitle();c&&c.renderLabelsTicksAndTitle()}else{n=Math.ceil(a.createLabelsAndCalculateWidth());b&&(b.lineCoordinates={},g=Math.round(e.x1+n+a.margin),f=Math.round(e.x2>b.chart.width-10?b.chart.width-10:e.x2),b.lineCoordinates.x1=g,b.lineCoordinates.x2=f,b.lineCoordinates.width=Math.abs(f-g));c&&(c.lineCoordinates={},g=Math.round(e.x1+n+a.margin),f=Math.round(e.x2>
c.chart.width-10?c.chart.width-10:e.x2),c.lineCoordinates.x1=g,c.lineCoordinates.x2=f,c.lineCoordinates.width=Math.abs(f-g));var m=Math.ceil(b?b.createLabelsAndCalculateHeight():0),q=Math.ceil(c?c.createLabelsAndCalculateHeight():0);b&&(d=Math.round(e.y2-m-b.margin),h=Math.round(e.y2-l>b.chart.height-10?b.chart.height-10:e.y2-l),b.lineCoordinates.y1=d,b.lineCoordinates.y2=d,b.boundingRect={x1:g,y1:d,x2:f,y2:h,width:f-g,height:m});c&&(d=Math.round(e.y1+c.margin),h=e.y1+c.margin+q,c.lineCoordinates.y1=
h,c.lineCoordinates.y2=h,c.boundingRect={x1:g,y1:d,x2:f,y2:h,width:f-g,height:q});g=Math.round(e.x1+a.margin);d=Math.round(c?c.lineCoordinates.y2:10>e.y1?10:e.y1);f=Math.round(e.x1+n+a.margin);h=Math.round(b?b.lineCoordinates.y1:e.y2-l>a.chart.height-10?a.chart.height-10:e.y2-l);a.lineCoordinates={x1:f,y1:d,x2:f,y2:h,height:Math.abs(h-d)};a.boundingRect={x1:g,y1:d,x2:f,y2:h,width:f-g,height:h-d};a.calculateValueToPixelConversionParameters();b&&b.calculateValueToPixelConversionParameters();c&&c.calculateValueToPixelConversionParameters();
b&&b.renderLabelsTicksAndTitle();c&&c.renderLabelsTicksAndTitle();a.renderLabelsTicksAndTitle()}p.preparePlotArea();e=a.chart.plotArea;k.save();k.rect(e.x1,e.y1,Math.abs(e.x2-e.x1),Math.abs(e.y2-e.y1));k.clip();a.renderStripLinesOfThicknessType("value");b&&b.renderStripLinesOfThicknessType("value");c&&c.renderStripLinesOfThicknessType("value");a.renderInterlacedColors();b&&b.renderInterlacedColors();c&&c.renderInterlacedColors();k.restore();a.renderGrid();b&&b.renderGrid();c&&c.renderGrid();a.renderAxisLine();
b&&b.renderAxisLine();c&&c.renderAxisLine();a.renderStripLinesOfThicknessType("pixel");b&&b.renderStripLinesOfThicknessType("pixel");c&&c.renderStripLinesOfThicknessType("pixel")};C.prototype.renderLabelsTicksAndTitle=function(){var a=!1,b=0,c=1,d=0;0!==this.labelAngle&&360!==this.labelAngle&&(c=1.2);if("undefined"===typeof this._options.interval){if("bottom"===this._position||"top"===this._position){for(e=0;e<this._labels.length;e++)g=this._labels[e],g.position<this.viewportMinimum||g.stripLine||
(g=g.textBlock.width*Math.cos(Math.PI/180*this.labelAngle)+g.textBlock.height*Math.sin(Math.PI/180*this.labelAngle),b+=g);b>this.lineCoordinates.width*c&&(a=!0)}if("left"===this._position||"right"===this._position){for(e=0;e<this._labels.length;e++)g=this._labels[e],g.position<this.viewportMinimum||g.stripLine||(g=g.textBlock.height*Math.cos(Math.PI/180*this.labelAngle)+g.textBlock.width*Math.sin(Math.PI/180*this.labelAngle),b+=g);b>this.lineCoordinates.height*c&&(a=!0)}}if("bottom"===this._position){for(var e=
0,g,e=0;e<this._labels.length;e++)if(g=this._labels[e],!(g.position<this.viewportMinimum||g.position>this.viewportMaximum)){b=this.getPixelCoordinatesOnAxis(g.position);if(this.tickThickness&&!this._labels[e].stripLine||this._labels[e].stripLine&&"pixel"===this._labels[e].stripLine._thicknessType)this._labels[e].stripLine?(c=this._labels[e].stripLine,this.ctx.lineWidth=c.thickness,this.ctx.strokeStyle=c.color):(this.ctx.lineWidth=this.tickThickness,this.ctx.strokeStyle=this.tickColor),c=1===this.ctx.lineWidth%
2?(b.x<<0)+0.5:b.x<<0,this.ctx.beginPath(),this.ctx.moveTo(c,b.y<<0),this.ctx.lineTo(c,b.y+this.tickLength<<0),this.ctx.stroke();if(!a||0===d++%2||this._labels[e].stripLine)0===g.textBlock.angle?(b.x-=g.textBlock.width/2,b.y+=this.tickLength+g.textBlock.fontSize/2):(b.x-=0>this.labelAngle?g.textBlock.width*Math.cos(Math.PI/180*this.labelAngle):0,b.y+=this.tickLength+Math.abs(0>this.labelAngle?g.textBlock.width*Math.sin(Math.PI/180*this.labelAngle)-5:5)),g.textBlock.x=b.x,g.textBlock.y=b.y,g.textBlock.render(!0)}this.title&&
(this._titleTextBlock=new H(this.ctx,{x:this.lineCoordinates.x1,y:this.boundingRect.y2-this.titleFontSize-5,maxWidth:this.lineCoordinates.width,maxHeight:1.5*this.titleFontSize,angle:0,text:this.title,horizontalAlign:"center",fontSize:this.titleFontSize,fontFamily:this.titleFontFamily,fontWeight:this.titleFontWeight,fontColor:this.titleFontColor,fontStyle:this.titleFontStyle,textBaseline:"top"}),this._titleTextBlock.measureText(),this._titleTextBlock.x=this.lineCoordinates.x1+this.lineCoordinates.width/
2-this._titleTextBlock.width/2,this._titleTextBlock.y=this.boundingRect.y2-this._titleTextBlock.height-3,this._titleTextBlock.render(!0))}else if("top"===this._position){for(e=0;e<this._labels.length;e++)if(g=this._labels[e],!(g.position<this.viewportMinimum||g.position>this.viewportMaximum)){b=this.getPixelCoordinatesOnAxis(g.position);if(this.tickThickness&&!this._labels[e].stripLine||this._labels[e].stripLine&&"pixel"===this._labels[e].stripLine._thicknessType)this._labels[e].stripLine?(c=this._labels[e].stripLine,
this.ctx.lineWidth=c.thickness,this.ctx.strokeStyle=c.color):(this.ctx.lineWidth=this.tickThickness,this.ctx.strokeStyle=this.tickColor),c=1===this.ctx.lineWidth%2?(b.x<<0)+0.5:b.x<<0,this.ctx.beginPath(),this.ctx.moveTo(c,b.y<<0),this.ctx.lineTo(c,b.y-this.tickLength<<0),this.ctx.stroke();if(!a||0===d++%2||this._labels[e].stripLine)0===g.textBlock.angle?(b.x-=g.textBlock.width/2,b.y-=this.tickLength+g.textBlock.height/2):(b.x-=0<this.labelAngle?g.textBlock.width*Math.cos(Math.PI/180*this.labelAngle):
0,b.y-=this.tickLength+Math.abs(0<this.labelAngle?g.textBlock.width*Math.sin(Math.PI/180*this.labelAngle)+5:5)),g.textBlock.x=b.x,g.textBlock.y=b.y,g.textBlock.render(!0)}this.title&&(this._titleTextBlock=new H(this.ctx,{x:this.lineCoordinates.x1,y:this.boundingRect.y1+1,maxWidth:this.lineCoordinates.width,maxHeight:1.5*this.titleFontSize,angle:0,text:this.title,horizontalAlign:"center",fontSize:this.titleFontSize,fontFamily:this.titleFontFamily,fontWeight:this.titleFontWeight,fontColor:this.titleFontColor,
fontStyle:this.titleFontStyle,textBaseline:"top"}),this._titleTextBlock.measureText(),this._titleTextBlock.x=this.lineCoordinates.x1+this.lineCoordinates.width/2-this._titleTextBlock.width/2,this._titleTextBlock.render(!0))}else if("left"===this._position){for(e=0;e<this._labels.length;e++)if(g=this._labels[e],!(g.position<this.viewportMinimum||g.position>this.viewportMaximum)){b=this.getPixelCoordinatesOnAxis(g.position);if(this.tickThickness&&!this._labels[e].stripLine||this._labels[e].stripLine&&
"pixel"===this._labels[e].stripLine._thicknessType)this._labels[e].stripLine?(c=this._labels[e].stripLine,this.ctx.lineWidth=c.thickness,this.ctx.strokeStyle=c.color):(this.ctx.lineWidth=this.tickThickness,this.ctx.strokeStyle=this.tickColor),c=1===this.ctx.lineWidth%2?(b.y<<0)+0.5:b.y<<0,this.ctx.beginPath(),this.ctx.moveTo(b.x<<0,c),this.ctx.lineTo(b.x-this.tickLength<<0,c),this.ctx.stroke();if(!a||0===d++%2||this._labels[e].stripLine)g.textBlock.x=b.x-g.textBlock.width*Math.cos(Math.PI/180*this.labelAngle)-
this.tickLength-5,g.textBlock.y=0===this.labelAngle?b.y:b.y-g.textBlock.width*Math.sin(Math.PI/180*this.labelAngle),g.textBlock.render(!0)}this.title&&(this._titleTextBlock=new H(this.ctx,{x:this.boundingRect.x1+1,y:this.lineCoordinates.y2,maxWidth:this.lineCoordinates.height,maxHeight:1.5*this.titleFontSize,angle:-90,text:this.title,horizontalAlign:"center",fontSize:this.titleFontSize,fontFamily:this.titleFontFamily,fontWeight:this.titleFontWeight,fontColor:this.titleFontColor,fontStyle:this.titleFontStyle,
textBaseline:"top"}),this._titleTextBlock.measureText(),this._titleTextBlock.y=this.lineCoordinates.height/2+this._titleTextBlock.width/2+this.lineCoordinates.y1,this._titleTextBlock.render(!0))}else if("right"===this._position){for(e=0;e<this._labels.length;e++)if(g=this._labels[e],!(g.position<this.viewportMinimum||g.position>this.viewportMaximum)){b=this.getPixelCoordinatesOnAxis(g.position);if(this.tickThickness&&!this._labels[e].stripLine||this._labels[e].stripLine&&"pixel"===this._labels[e].stripLine._thicknessType)this._labels[e].stripLine?
(c=this._labels[e].stripLine,this.ctx.lineWidth=c.thickness,this.ctx.strokeStyle=c.color):(this.ctx.lineWidth=this.tickThickness,this.ctx.strokeStyle=this.tickColor),c=1===this.ctx.lineWidth%2?(b.y<<0)+0.5:b.y<<0,this.ctx.beginPath(),this.ctx.moveTo(b.x<<0,c),this.ctx.lineTo(b.x+this.tickLength<<0,c),this.ctx.stroke();if(!a||0===d++%2||this._labels[e].stripLine)g.textBlock.x=b.x+this.tickLength+5,g.textBlock.y=b.y,g.textBlock.render(!0)}this.title&&(this._titleTextBlock=new H(this.ctx,{x:this.boundingRect.x2-
1,y:this.lineCoordinates.y2,maxWidth:this.lineCoordinates.height,maxHeight:1.5*this.titleFontSize,angle:90,text:this.title,horizontalAlign:"center",fontSize:this.titleFontSize,fontFamily:this.titleFontFamily,fontWeight:this.titleFontWeight,fontColor:this.titleFontColor,fontStyle:this.titleFontStyle,textBaseline:"top"}),this._titleTextBlock.measureText(),this._titleTextBlock.y=this.lineCoordinates.height/2-this._titleTextBlock.width/2+this.lineCoordinates.y1,this._titleTextBlock.render(!0))}};C.prototype.renderInterlacedColors=
function(){var a=this.chart.plotArea.ctx,b,c,d=this.chart.plotArea,e=0;b=!0;if(("bottom"===this._position||"top"===this._position)&&this.interlacedColor)for(a.fillStyle=this.interlacedColor,e=0;e<this._labels.length;e++)this._labels[e].stripLine||(b?(b=this.getPixelCoordinatesOnAxis(this._labels[e].position),c=e+1>=this._labels.length-1?this.getPixelCoordinatesOnAxis(this.viewportMaximum):this.getPixelCoordinatesOnAxis(this._labels[e+1].position),a.fillRect(b.x,d.y1,Math.abs(c.x-b.x),Math.abs(d.y1-
d.y2)),b=!1):b=!0);else if(("left"===this._position||"right"===this._position)&&this.interlacedColor)for(a.fillStyle=this.interlacedColor,e=0;e<this._labels.length;e++)this._labels[e].stripLine||(b?(c=this.getPixelCoordinatesOnAxis(this._labels[e].position),b=e+1>=this._labels.length-1?this.getPixelCoordinatesOnAxis(this.viewportMaximum):this.getPixelCoordinatesOnAxis(this._labels[e+1].position),a.fillRect(d.x1,b.y,Math.abs(d.x1-d.x2),Math.abs(b.y-c.y)),b=!1):b=!0);a.beginPath()};C.prototype.renderStripLinesOfThicknessType=
function(a){if(this.stripLines&&0<this.stripLines.length&&a)for(var b=0,b=0;b<this.stripLines.length;b++){var c=this.stripLines[b];c._thicknessType===a&&("pixel"===a&&(c.value<this.viewportMinimum||c.value>this.viewportMaximum)||(c.showOnTop?this.chart.addEventListener("dataAnimationIterationEnd",c.render,c):c.render()))}};C.prototype.renderGrid=function(){if(this.gridThickness&&0<this.gridThickness){var a=this.chart.ctx,b,c=this.chart.plotArea;a.lineWidth=this.gridThickness;a.strokeStyle=this.gridColor;
a.setLineDash&&a.setLineDash(M(this.gridDashType,this.gridThickness));if("bottom"===this._position||"top"===this._position)for(d=0;d<this._labels.length&&!this._labels[d].stripLine;d++)this._labels[d].position<this.viewportMinimum||this._labels[d].position>this.viewportMaximum||(a.beginPath(),b=this.getPixelCoordinatesOnAxis(this._labels[d].position),b=1===a.lineWidth%2?(b.x<<0)+0.5:b.x<<0,a.moveTo(b,c.y1<<0),a.lineTo(b,c.y2<<0),a.stroke());else if("left"===this._position||"right"===this._position)for(var d=
0;d<this._labels.length&&!this._labels[d].stripLine;d++)0===d&&"axisY"===this.type&&this.chart.axisX&&this.chart.axisX.lineThickness||(this._labels[d].position<this.viewportMinimum||this._labels[d].position>this.viewportMaximum)||(a.beginPath(),b=this.getPixelCoordinatesOnAxis(this._labels[d].position),b=1===a.lineWidth%2?(b.y<<0)+0.5:b.y<<0,a.moveTo(c.x1<<0,b),a.lineTo(c.x2<<0,b),a.stroke())}};C.prototype.renderAxisLine=function(){var a=this.chart.ctx;if("bottom"===this._position||"top"===this._position){if(this.lineThickness){a.lineWidth=
this.lineThickness;a.strokeStyle=this.lineColor?this.lineColor:"black";a.setLineDash&&a.setLineDash(M(this.lineDashType,this.lineThickness));var b=1===this.lineThickness%2?(this.lineCoordinates.y1<<0)+0.5:this.lineCoordinates.y1<<0;a.beginPath();a.moveTo(this.lineCoordinates.x1,b);a.lineTo(this.lineCoordinates.x2,b);a.stroke()}}else"left"!==this._position&&"right"!==this._position||!this.lineThickness||(a.lineWidth=this.lineThickness,a.strokeStyle=this.lineColor,a.setLineDash&&a.setLineDash(M(this.lineDashType,
this.lineThickness)),b=1===this.lineThickness%2?(this.lineCoordinates.x1<<0)+0.5:this.lineCoordinates.x1<<0,a.beginPath(),a.moveTo(b,this.lineCoordinates.y1),a.lineTo(b,this.lineCoordinates.y2),a.stroke())};C.prototype.getPixelCoordinatesOnAxis=function(a){var b={};if("bottom"===this._position||"top"===this._position){var c=this.conversionParameters.pixelPerUnit;b.x=this.conversionParameters.reference+c*(a-this.viewportMinimum);b.y=this.lineCoordinates.y1}if("left"===this._position||"right"===this._position)c=
-this.conversionParameters.pixelPerUnit,b.y=this.conversionParameters.reference-c*(a-this.viewportMinimum),b.x=this.lineCoordinates.x2;return b};C.prototype.convertPixelToValue=function(a){if(!a)return null;var b=0;return b=this.conversionParameters.minimum+(("left"===this._position||"right"===this._position?a.y:a.x)-this.conversionParameters.reference)/this.conversionParameters.pixelPerUnit};C.prototype.setViewPortRange=function(a,b){this.sessionVariables.newViewportMinimum=this.viewportMinimum=
Math.min(a,b);this.sessionVariables.newViewportMaximum=this.viewportMaximum=Math.max(a,b)};C.prototype.getXValueAt=function(a){if(!a)return null;var b=null;"left"===this._position?b=(this.chart.axisX.viewportMaximum-this.chart.axisX.viewportMinimum)/this.chart.axisX.lineCoordinates.height*(this.chart.axisX.lineCoordinates.y2-a.y)+this.chart.axisX.viewportMinimum:"bottom"===this._position&&(b=(this.chart.axisX.viewportMaximum-this.chart.axisX.viewportMinimum)/this.chart.axisX.lineCoordinates.width*
(a.x-this.chart.axisX.lineCoordinates.x1)+this.chart.axisX.viewportMinimum);return b};C.prototype.calculateValueToPixelConversionParameters=function(a){this.reversed=!1;a={pixelPerUnit:null,minimum:null,reference:null};var b=this.lineCoordinates.width,c=this.lineCoordinates.height;a.minimum=this.viewportMinimum;if("bottom"===this._position||"top"===this._position)a.pixelPerUnit=(this.reversed?-1:1)*b/Math.abs(this.viewportMaximum-this.viewportMinimum),a.reference=this.reversed?this.lineCoordinates.x2:
this.lineCoordinates.x1;if("left"===this._position||"right"===this._position)a.pixelPerUnit=(this.reversed?1:-1)*c/Math.abs(this.viewportMaximum-this.viewportMinimum),a.reference=this.reversed?this.lineCoordinates.y1:this.lineCoordinates.y2;this.conversionParameters=a};C.prototype.calculateAxisParameters=function(){var a=this.chart.layoutManager.getFreeSpace(),b=!1;"bottom"===this._position||"top"===this._position?(this.maxWidth=a.width,this.maxHeight=a.height):(this.maxWidth=a.height,this.maxHeight=
a.width);var a="axisX"===this.type?500>this.maxWidth?8:Math.max(6,Math.floor(this.maxWidth/62)):Math.max(Math.floor(this.maxWidth/40),2),c,d,e,g;g=0;if(null===this.viewportMinimum||isNaN(this.viewportMinimum))this.viewportMinimum=this.minimum;if(null===this.viewportMaximum||isNaN(this.viewportMaximum))this.viewportMaximum=this.maximum;"axisX"===this.type?(c=null!==this.viewportMinimum?this.viewportMinimum:this.dataInfo.viewPortMin,d=null!==this.viewportMaximum?this.viewportMaximum:this.dataInfo.viewPortMax,
0===d-c&&(g="undefined"===typeof this._options.interval?0.4:this._options.interval,d+=g,c-=g),Infinity!==this.dataInfo.minDiff?e=this.dataInfo.minDiff:1<d-c?e=0.5*Math.abs(d-c):(e=1,"dateTime"===this.chart.plotInfo.axisXValueType&&(b=!0))):"axisY"===this.type&&(c=null!==this.viewportMinimum?this.viewportMinimum:this.dataInfo.viewPortMin,d=null!==this.viewportMaximum?this.viewportMaximum:this.dataInfo.viewPortMax,isFinite(c)||isFinite(d)?isFinite(c)?isFinite(d)||(d=c):c=d:(d="undefined"===typeof this._options.interval?
-Infinity:this._options.interval,c=0),0===c&&0===d?(d+=9,c=0):0===d-c?(g=Math.min(Math.abs(0.01*Math.abs(d)),5),d+=g,c-=g):c>d?(g=Math.min(Math.abs(0.01*Math.abs(d-c)),5),0<=d?c=d-g:d=c+g):(g=Math.min(Math.abs(0.01*Math.abs(d-c)),0.05),0!==d&&(d+=g),0!==c&&(c-=g)),e=Infinity!==this.dataInfo.minDiff?this.dataInfo.minDiff:1<d-c?0.5*Math.abs(d-c):1,this.includeZero&&(null===this.viewportMinimum||isNaN(this.viewportMinimum))&&0<c&&(c=0),this.includeZero&&(null===this.viewportMaximum||isNaN(this.viewportMaximum))&&
0>d&&(d=0));g=(isNaN(this.viewportMaximum)||null===this.viewportMaximum?d:this.viewportMaximum)-(isNaN(this.viewportMinimum)||null===this.viewportMinimum?c:this.viewportMinimum);if("axisX"===this.type&&"dateTime"===this.chart.plotInfo.axisXValueType){this.intervalType||(g/1<=a?(this.interval=1,this.intervalType="millisecond"):g/2<=a?(this.interval=2,this.intervalType="millisecond"):g/5<=a?(this.interval=5,this.intervalType="millisecond"):g/10<=a?(this.interval=10,this.intervalType="millisecond"):
g/20<=a?(this.interval=20,this.intervalType="millisecond"):g/50<=a?(this.interval=50,this.intervalType="millisecond"):g/100<=a?(this.interval=100,this.intervalType="millisecond"):g/200<=a?(this.interval=200,this.intervalType="millisecond"):g/250<=a?(this.interval=250,this.intervalType="millisecond"):g/300<=a?(this.interval=300,this.intervalType="millisecond"):g/400<=a?(this.interval=400,this.intervalType="millisecond"):g/500<=a?(this.interval=500,this.intervalType="millisecond"):g/(1*D.secondDuration)<=
a?(this.interval=1,this.intervalType="second"):g/(2*D.secondDuration)<=a?(this.interval=2,this.intervalType="second"):g/(5*D.secondDuration)<=a?(this.interval=5,this.intervalType="second"):g/(10*D.secondDuration)<=a?(this.interval=10,this.intervalType="second"):g/(15*D.secondDuration)<=a?(this.interval=15,this.intervalType="second"):g/(20*D.secondDuration)<=a?(this.interval=20,this.intervalType="second"):g/(30*D.secondDuration)<=a?(this.interval=30,this.intervalType="second"):g/(1*D.minuteDuration)<=
a?(this.interval=1,this.intervalType="minute"):g/(2*D.minuteDuration)<=a?(this.interval=2,this.intervalType="minute"):g/(5*D.minuteDuration)<=a?(this.interval=5,this.intervalType="minute"):g/(10*D.minuteDuration)<=a?(this.interval=10,this.intervalType="minute"):g/(15*D.minuteDuration)<=a?(this.interval=15,this.intervalType="minute"):g/(20*D.minuteDuration)<=a?(this.interval=20,this.intervalType="minute"):g/(30*D.minuteDuration)<=a?(this.interval=30,this.intervalType="minute"):g/(1*D.hourDuration)<=
a?(this.interval=1,this.intervalType="hour"):g/(2*D.hourDuration)<=a?(this.interval=2,this.intervalType="hour"):g/(3*D.hourDuration)<=a?(this.interval=3,this.intervalType="hour"):g/(6*D.hourDuration)<=a?(this.interval=6,this.intervalType="hour"):g/(1*D.dayDuration)<=a?(this.interval=1,this.intervalType="day"):g/(2*D.dayDuration)<=a?(this.interval=2,this.intervalType="day"):g/(4*D.dayDuration)<=a?(this.interval=4,this.intervalType="day"):g/(1*D.weekDuration)<=a?(this.interval=1,this.intervalType="week"):
g/(2*D.weekDuration)<=a?(this.interval=2,this.intervalType="week"):g/(3*D.weekDuration)<=a?(this.interval=3,this.intervalType="week"):g/(1*D.monthDuration)<=a?(this.interval=1,this.intervalType="month"):g/(2*D.monthDuration)<=a?(this.interval=2,this.intervalType="month"):g/(3*D.monthDuration)<=a?(this.interval=3,this.intervalType="month"):g/(6*D.monthDuration)<=a?(this.interval=6,this.intervalType="month"):(this.interval=g/(1*D.yearDuration)<=a?1:g/(2*D.yearDuration)<=a?2:g/(4*D.yearDuration)<=a?
4:Math.floor(C.getNiceNumber(g/(a-1),!0)/D.yearDuration),this.intervalType="year"));if(null===this.viewportMinimum||isNaN(this.viewportMinimum))this.viewportMinimum=c-e/2;if(null===this.viewportMaximum||isNaN(this.viewportMaximum))this.viewportMaximum=d+e/2;this.valueFormatString||(b?this.valueFormatString="MMM DD YYYY HH:mm":"year"===this.intervalType?this.valueFormatString="YYYY":"month"===this.intervalType?this.valueFormatString="MMM YYYY":"week"===this.intervalType?this.valueFormatString="MMM DD YYYY":
"day"===this.intervalType?this.valueFormatString="MMM DD YYYY":"hour"===this.intervalType?this.valueFormatString="hh:mm TT":"minute"===this.intervalType?this.valueFormatString="hh:mm TT":"second"===this.intervalType?this.valueFormatString="hh:mm:ss TT":"millisecond"===this.intervalType&&(this.valueFormatString="fff'ms'"))}else{this.intervalType="number";g=C.getNiceNumber(g,!1);this.interval=this._options&&this._options.interval?this._options.interval:C.getNiceNumber(g/(a-1),!0);if(null===this.viewportMinimum||
isNaN(this.viewportMinimum))this.viewportMinimum="axisX"===this.type?c-e/2:Math.floor(c/this.interval)*this.interval;if(null===this.viewportMaximum||isNaN(this.viewportMaximum))this.viewportMaximum="axisX"===this.type?d+e/2:Math.ceil(d/this.interval)*this.interval;0===this.viewportMaximum&&0===this.viewportMinimum&&(0===this._options.viewportMinimum?this.viewportMaximum+=10:0===this._options.viewportMaximum&&(this.viewportMinimum-=10),this._options&&"undefined"===typeof this._options.interval&&(this.interval=
C.getNiceNumber((this.viewportMaximum-this.viewportMinimum)/(a-1),!0)))}if(null===this.minimum||null===this.maximum)if("axisX"===this.type?(c=null!==this.minimum?this.minimum:this.dataInfo.min,d=null!==this.maximum?this.maximum:this.dataInfo.max,0===d-c&&(g="undefined"===typeof this._options.interval?0.4:this._options.interval,d+=g,c-=g),e=Infinity!==this.dataInfo.minDiff?this.dataInfo.minDiff:1<d-c?0.5*Math.abs(d-c):1):"axisY"===this.type&&(c=null!==this.minimum?this.minimum:this.dataInfo.min,d=
null!==this.maximum?this.maximum:this.dataInfo.max,isFinite(c)||isFinite(d)?0===c&&0===d?(d+=9,c=0):0===d-c?(g=Math.min(Math.abs(0.01*Math.abs(d)),5),d+=g,c-=g):c>d?(g=Math.min(Math.abs(0.01*Math.abs(d-c)),5),0<=d?c=d-g:d=c+g):(g=Math.min(Math.abs(0.01*Math.abs(d-c)),0.05),0!==d&&(d+=g),0!==c&&(c-=g)):(d="undefined"===typeof this._options.interval?-Infinity:this._options.interval,c=0),e=Infinity!==this.dataInfo.minDiff?this.dataInfo.minDiff:1<d-c?0.5*Math.abs(d-c):1,this.includeZero&&(null===this.minimum||
isNaN(this.minimum))&&0<c&&(c=0),this.includeZero&&(null===this.maximum||isNaN(this.maximum))&&0>d&&(d=0)),"axisX"===this.type&&"dateTime"===this.chart.plotInfo.axisXValueType){if(null===this.minimum||isNaN(this.minimum))this.minimum=c-e/2;if(null===this.maximum||isNaN(this.maximum))this.maximum=d+e/2}else this.intervalType="number",null===this.minimum&&(this.minimum="axisX"===this.type?c-e/2:Math.floor(c/this.interval)*this.interval,this.minimum=Math.min(this.minimum,null===this.sessionVariables.viewportMinimum||
isNaN(this.sessionVariables.viewportMinimum)?Infinity:this.sessionVariables.viewportMinimum)),null===this.maximum&&(this.maximum="axisX"===this.type?d+e/2:Math.ceil(d/this.interval)*this.interval,this.maximum=Math.max(this.maximum,null===this.sessionVariables.viewportMaximum||isNaN(this.sessionVariables.viewportMaximum)?-Infinity:this.sessionVariables.viewportMaximum)),0===this.maximum&&0===this.minimum&&(0===this._options.minimum?this.maximum+=10:0===this._options.maximum&&(this.minimum-=10));this.viewportMinimum=
Math.max(this.viewportMinimum,this.minimum);this.viewportMaximum=Math.min(this.viewportMaximum,this.maximum);this.intervalStartPosition="axisX"===this.type&&"dateTime"===this.chart.plotInfo.axisXValueType?this.getLabelStartPoint(new Date(this.viewportMinimum),this.intervalType,this.interval):Math.floor((this.viewportMinimum+0.2*this.interval)/this.interval)*this.interval;if(!this.valueFormatString&&(this.valueFormatString="#,##0.##",g=Math.abs(this.viewportMaximum-this.viewportMinimum),1>g)){b=Math.floor(Math.abs(Math.log(g)/
Math.LN10))+2;if(isNaN(b)||!isFinite(b))b=2;if(2<b)for(c=0;c<b-2;c++)this.valueFormatString+="#"}};C.getNiceNumber=function(a,b){var c=Math.floor(Math.log(a)/Math.LN10),d=a/Math.pow(10,c);return Number(((b?1.5>d?1:3>d?2:7>d?5:10:1>=d?1:2>=d?2:5>=d?5:10)*Math.pow(10,c)).toFixed(20))};C.prototype.getLabelStartPoint=function(){var a=D[this.intervalType+"Duration"]*this.interval,a=new Date(Math.floor(this.viewportMinimum/a)*a);if("millisecond"!==this.intervalType)if("second"===this.intervalType)0<a.getMilliseconds()&&
(a.setSeconds(a.getSeconds()+1),a.setMilliseconds(0));else if("minute"===this.intervalType){if(0<a.getSeconds()||0<a.getMilliseconds())a.setMinutes(a.getMinutes()+1),a.setSeconds(0),a.setMilliseconds(0)}else if("hour"===this.intervalType){if(0<a.getMinutes()||0<a.getSeconds()||0<a.getMilliseconds())a.setHours(a.getHours()+1),a.setMinutes(0),a.setSeconds(0),a.setMilliseconds(0)}else if("day"===this.intervalType){if(0<a.getHours()||0<a.getMinutes()||0<a.getSeconds()||0<a.getMilliseconds())a.setDate(a.getDate()+
1),a.setHours(0),a.setMinutes(0),a.setSeconds(0),a.setMilliseconds(0)}else if("week"===this.intervalType){if(0<a.getDay()||0<a.getHours()||0<a.getMinutes()||0<a.getSeconds()||0<a.getMilliseconds())a.setDate(a.getDate()+(7-a.getDay())),a.setHours(0),a.setMinutes(0),a.setSeconds(0),a.setMilliseconds(0)}else if("month"===this.intervalType){if(1<a.getDate()||0<a.getHours()||0<a.getMinutes()||0<a.getSeconds()||0<a.getMilliseconds())a.setMonth(a.getMonth()+1),a.setDate(1),a.setHours(0),a.setMinutes(0),
a.setSeconds(0),a.setMilliseconds(0)}else"year"===this.intervalType&&(0<a.getMonth()||1<a.getDate()||0<a.getHours()||0<a.getMinutes()||0<a.getSeconds()||0<a.getMilliseconds())&&(a.setFullYear(a.getFullYear()+1),a.setMonth(0),a.setDate(1),a.setHours(0),a.setMinutes(0),a.setSeconds(0),a.setMilliseconds(0));return a};O(ka,G);ka.prototype.render=function(){var a=this.parent.getPixelCoordinatesOnAxis(this.value),b=Math.abs("pixel"===this._thicknessType?this.thickness:this.parent.conversionParameters.pixelPerUnit*
this.thickness);if(0<b){var c=null===this.opacity?1:this.opacity;this.ctx.strokeStyle=this.color;this.ctx.beginPath();var d=this.ctx.globalAlpha;this.ctx.globalAlpha=c;B(this.id);var e,g,f,h;this.ctx.lineWidth=b;this.ctx.setLineDash&&this.ctx.setLineDash(M(this.lineDashType,b));if("bottom"===this.parent._position||"top"===this.parent._position)e=g=1===this.ctx.lineWidth%2?(a.x<<0)+0.5:a.x<<0,f=this.chart.plotArea.y1,h=this.chart.plotArea.y2;else if("left"===this.parent._position||"right"===this.parent._position)f=
h=1===this.ctx.lineWidth%2?(a.y<<0)+0.5:a.y<<0,e=this.chart.plotArea.x1,g=this.chart.plotArea.x2;this.ctx.moveTo(e,f);this.ctx.lineTo(g,h);this.ctx.stroke();this.ctx.globalAlpha=d}};O(Q,G);Q.prototype._initialize=function(){if(this.enabled){this.container=document.createElement("div");this.container.setAttribute("class","canvasjs-chart-tooltip");this.container.style.position="absolute";this.container.style.height="auto";this.container.style.boxShadow="1px 1px 2px 2px rgba(0,0,0,0.1)";this.container.style.zIndex=
"1000";this.container.style.display="none";var a;a='<div style=" width: auto;height: auto;min-width: 50px;';a+="line-height: auto;";a+="margin: 0px 0px 0px 0px;";a+="padding: 5px;";a+="font-family: Calibri, Arial, Georgia, serif;";a+="font-weight: normal;";a+="font-style: "+(t?"italic;":"normal;");a+="font-size: 14px;";a+="color: #000000;";a+="text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.1);";a+="text-align: left;";a+="border: 2px solid gray;";a+=t?"background: rgba(255,255,255,.9);":"background: rgb(255,255,255);";
a+="text-indent: 0px;";a+="white-space: nowrap;";a+="border-radius: 5px;";a+="-moz-user-select:none;";a+="-khtml-user-select: none;";a+="-webkit-user-select: none;";a+="-ms-user-select: none;";a+="user-select: none;";t||(a+="filter: alpha(opacity = 90);",a+="filter: progid:DXImageTransform.Microsoft.Shadow(Strength=3, Direction=135, Color='#666666');");a+='} "> Sample Tooltip</div>';this.container.innerHTML=a;this.contentDiv=this.container.firstChild;this.container.style.borderRadius=this.contentDiv.style.borderRadius;
this.chart._canvasJSContainer.appendChild(this.container)}};Q.prototype.mouseMoveHandler=function(a,b){this._lastUpdated&&40>(new Date).getTime()-this._lastUpdated||(this._lastUpdated=(new Date).getTime(),this._updateToolTip(a,b))};Q.prototype._updateToolTip=function(a,b){if(!this.chart.disableToolTip){if("undefined"===typeof a||"undefined"===typeof b){if(isNaN(this._prevX)||isNaN(this._prevY))return;a=this._prevX;b=this._prevY}else this._prevX=a,this._prevY=b;var c=null,d=null,e=[],g=0;if(this.shared&&
this.enabled&&"none"!==this.chart.plotInfo.axisPlacement){g="xySwapped"===this.chart.plotInfo.axisPlacement?(this.chart.axisX.viewportMaximum-this.chart.axisX.viewportMinimum)/this.chart.axisX.lineCoordinates.height*(this.chart.axisX.lineCoordinates.y2-b)+this.chart.axisX.viewportMinimum:(this.chart.axisX.viewportMaximum-this.chart.axisX.viewportMinimum)/this.chart.axisX.lineCoordinates.width*(a-this.chart.axisX.lineCoordinates.x1)+this.chart.axisX.viewportMinimum;d=[];for(c=0;c<this.chart.data.length;c++){var f=
this.chart.data[c].getDataPointAtX(g,!0);f&&0<=f.index&&(f.dataSeries=this.chart.data[c],null!==f.dataPoint.y&&d.push(f))}if(0===d.length)return;d.sort(function(a,b){return a.distance-b.distance});g=d[0];for(c=0;c<d.length;c++)d[c].dataPoint.x.valueOf()===g.dataPoint.x.valueOf()&&e.push(d[c]);d=null}else{if(f=this.chart.getDataPointAtXY(a,b,!0))this.currentDataPointIndex=f.dataPointIndex,this.currentSeriesIndex=f.dataSeries.index;else if(t)if(f=va(a,b,this.chart._eventManager.ghostCtx),0<f&&"undefined"!==
typeof this.chart._eventManager.objectMap[f]){eventObject=this.chart._eventManager.objectMap[f];if("legendItem"===eventObject.objectType)return;this.currentSeriesIndex=eventObject.dataSeriesIndex;this.currentDataPointIndex=0<=eventObject.dataPointIndex?eventObject.dataPointIndex:-1}else this.currentDataPointIndex=-1;else this.currentDataPointIndex=-1;if(0<=this.currentSeriesIndex){d=this.chart.data[this.currentSeriesIndex];f={};if(0<=this.currentDataPointIndex)c=d.dataPoints[this.currentDataPointIndex],
f.dataSeries=d,f.dataPoint=c,f.index=this.currentDataPointIndex,f.distance=Math.abs(c.x-g);else{if(!this.enabled||"line"!==d.type&&"stepLine"!==d.type&&"spline"!==d.type&&"area"!==d.type&&"stepArea"!==d.type&&"splineArea"!==d.type&&"stackedArea"!==d.type&&"stackedArea100"!==d.type&&"rangeArea"!==d.type&&"rangeSplineArea"!==d.type&&"candlestick"!==d.type&&"ohlc"!==d.type)return;g=d.axisX.conversionParameters.minimum+(a-d.axisX.conversionParameters.reference)/d.axisX.conversionParameters.pixelPerUnit;
f=d.getDataPointAtX(g,!0);f.dataSeries=d;this.currentDataPointIndex=f.index;c=f.dataPoint}if(null!==f.dataPoint.y)if(f.dataSeries.axisY)if(0<f.dataPoint.y.length){for(c=g=0;c<f.dataPoint.y.length;c++)f.dataPoint.y[c]<f.dataSeries.axisY.viewportMinimum?g--:f.dataPoint.y[c]>f.dataSeries.axisY.viewportMaximum&&g++;g<f.dataPoint.y.length&&g>-f.dataPoint.y.length&&e.push(f)}else f.dataPoint.y>=f.dataSeries.axisY.viewportMinimum&&f.dataPoint.y<=f.dataSeries.axisY.viewportMaximum&&e.push(f);else e.push(f)}}if(0<
e.length&&(this.highlightObjects(e),this.enabled))if(g="",g=this.getToolTipInnerHTML({entries:e}),null!==g){this.contentDiv.innerHTML=g;this.contentDiv.innerHTML=g;g=!1;"none"===this.container.style.display&&(g=!0,this.container.style.display="block");try{this.contentDiv.style.background=this.backgroundColor?this.backgroundColor:t?"rgba(255,255,255,.9)":"rgb(255,255,255)",this.contentDiv.style.borderRightColor=this.contentDiv.style.borderLeftColor=this.contentDiv.style.borderColor=this.borderColor?
this.borderColor:e[0].dataPoint.color?e[0].dataPoint.color:e[0].dataSeries.color?e[0].dataSeries.color:e[0].dataSeries._colorSet[e[0].index%e[0].dataSeries._colorSet.length],this.contentDiv.style.borderWidth=this.borderThickness||0===this.borderThickness?this.borderThickness+"px":"2px",this.contentDiv.style.borderRadius=this.cornerRadius||0===this.cornerRadius?this.cornerRadius+"px":"5px",this.container.style.borderRadius=this.contentDiv.style.borderRadius,this.contentDiv.style.fontSize=this.fontSize||
0===this.fontSize?this.fontSize+"px":"14px",this.contentDiv.style.color=this.fontColor?this.fontColor:"#000000",this.contentDiv.style.fontFamily=this.fontFamily?this.fontFamily:"Calibri, Arial, Georgia, serif;",this.contentDiv.style.fontWeight=this.fontWeight?this.fontWeight:"normal",this.contentDiv.style.fontStyle=this.fontStyle?this.fontStyle:t?"italic":"normal"}catch(h){}"pie"===e[0].dataSeries.type||"doughnut"===e[0].dataSeries.type||"funnel"===e[0].dataSeries.type||"bar"===e[0].dataSeries.type||
"rangeBar"===e[0].dataSeries.type||"stackedBar"===e[0].dataSeries.type||"stackedBar100"===e[0].dataSeries.type?toolTipLeft=a-10-this.container.clientWidth:(toolTipLeft=e[0].dataSeries.axisX.conversionParameters.reference+e[0].dataSeries.axisX.conversionParameters.pixelPerUnit*(e[0].dataPoint.x-e[0].dataSeries.axisX.conversionParameters.minimum)-this.container.clientWidth<<0,toolTipLeft-=10);0>toolTipLeft&&(toolTipLeft+=this.container.clientWidth+20);toolTipLeft+this.container.clientWidth>this.chart._container.clientWidth&&
(toolTipLeft=Math.max(0,this.chart._container.clientWidth-this.container.clientWidth));toolTipLeft+="px";e=1!==e.length||this.shared||"line"!==e[0].dataSeries.type&&"stepLine"!==e[0].dataSeries.type&&"spline"!==e[0].dataSeries.type&&"area"!==e[0].dataSeries.type&&"stepArea"!==e[0].dataSeries.type&&"splineArea"!==e[0].dataSeries.type&&"stackedArea"!==e[0].dataSeries.type&&"stackedArea100"!==e[0].dataSeries.type?"bar"===e[0].dataSeries.type||"rangeBar"===e[0].dataSeries.type||"stackedBar"===e[0].dataSeries.type||
"stackedBar100"===e[0].dataSeries.type?e[0].dataSeries.axisX.conversionParameters.reference+e[0].dataSeries.axisX.conversionParameters.pixelPerUnit*(e[0].dataPoint.x-e[0].dataSeries.axisX.viewportMinimum)+0.5<<0:b:e[0].dataSeries.axisY.conversionParameters.reference+e[0].dataSeries.axisY.conversionParameters.pixelPerUnit*(e[0].dataPoint.y-e[0].dataSeries.axisY.viewportMinimum)+0.5<<0;e=-e+10;0<e+this.container.clientHeight+5&&(e-=e+this.container.clientHeight+5-0);this.container.style.left=toolTipLeft;
this.container.style.bottom=e+"px";!this.animationEnabled||g?this.disableAnimation():this.enableAnimation()}else this.hide(!1)}};Q.prototype.highlightObjects=function(a){var b=this.chart.overlaidCanvasCtx;this.chart.resetOverlayedCanvas();b.clearRect(0,0,this.chart.width,this.chart.height);b.save();var c=this.chart.plotArea,d=0;b.rect(c.x1,c.y1,c.x2-c.x1,c.y2-c.y1);b.clip();for(c=0;c<a.length;c++){var e=a[c];if((e=this.chart._eventManager.objectMap[e.dataSeries.dataPointIds[e.index]])&&e.objectType&&
"dataPoint"===e.objectType){var d=this.chart.data[e.dataSeriesIndex],g=d.dataPoints[e.dataPointIndex],f=e.dataPointIndex;!1===g.highlightEnabled||!0!==d.highlightEnabled&&!0!==g.highlightEnabled||("line"===d.type||"stepLine"===d.type||"spline"===d.type||"scatter"===d.type||"area"===d.type||"stepArea"===d.type||"splineArea"===d.type||"stackedArea"===d.type||"stackedArea100"===d.type||"rangeArea"===d.type||"rangeSplineArea"===d.type?(g=d.getMarkerProperties(f,e.x1,e.y1,this.chart.overlaidCanvasCtx),
g.size=Math.max(1.5*g.size<<0,10),g.borderColor=g.borderColor||"#FFFFFF",g.borderThickness=g.borderThickness||Math.ceil(0.1*g.size),K.drawMarkers([g]),"undefined"!==typeof e.y2&&(g=d.getMarkerProperties(f,e.x1,e.y2,this.chart.overlaidCanvasCtx),g.size=Math.max(1.5*g.size<<0,10),g.borderColor=g.borderColor||"#FFFFFF",g.borderThickness=g.borderThickness||Math.ceil(0.1*g.size),K.drawMarkers([g]))):"bubble"===d.type?(g=d.getMarkerProperties(f,e.x1,e.y1,this.chart.overlaidCanvasCtx),g.size=e.size,g.color=
"white",g.borderColor="white",b.globalAlpha=0.3,K.drawMarkers([g]),b.globalAlpha=1):"column"===d.type||"stackedColumn"===d.type||"stackedColumn100"===d.type||"bar"===d.type||"rangeBar"===d.type||"stackedBar"===d.type||"stackedBar100"===d.type||"rangeColumn"===d.type?I(b,e.x1,e.y1,e.x2,e.y2,"white",0,null,!1,!1,!1,!1,0.3):"pie"===d.type||"doughnut"===d.type?ra(b,e.center,e.radius,"white",d.type,e.startAngle,e.endAngle,0.3,e.percentInnerRadius):"candlestick"===d.type?(b.globalAlpha=1,b.strokeStyle=
e.color,b.lineWidth=2*e.borderThickness,d=0===b.lineWidth%2?0:0.5,b.beginPath(),b.moveTo(e.x3-d,e.y2),b.lineTo(e.x3-d,Math.min(e.y1,e.y4)),b.stroke(),b.beginPath(),b.moveTo(e.x3-d,Math.max(e.y1,e.y4)),b.lineTo(e.x3-d,e.y3),b.stroke(),I(b,e.x1,Math.min(e.y1,e.y4),e.x2,Math.max(e.y1,e.y4),"transparent",2*e.borderThickness,e.color,!1,!1,!1,!1),b.globalAlpha=1):"ohlc"===d.type&&(b.globalAlpha=1,b.strokeStyle=e.color,b.lineWidth=2*e.borderThickness,d=0===b.lineWidth%2?0:0.5,b.beginPath(),b.moveTo(e.x3-
d,e.y2),b.lineTo(e.x3-d,e.y3),b.stroke(),b.beginPath(),b.moveTo(e.x3,e.y1),b.lineTo(e.x1,e.y1),b.stroke(),b.beginPath(),b.moveTo(e.x3,e.y4),b.lineTo(e.x2,e.y4),b.stroke(),b.globalAlpha=1))}}b.restore();b.globalAlpha=1;b.beginPath()};Q.prototype.getToolTipInnerHTML=function(a){a=a.entries;for(var b=null,c=null,d=null,e=0,g="",f=!0,h=0;h<a.length;h++)if(a[h].dataSeries.toolTipContent||a[h].dataPoint.toolTipContent){f=!1;break}if(f&&(this.content&&"function"===typeof this.content||this.contentFormatter))a=
{chart:this.chart,toolTip:this._options,entries:a},b=this.contentFormatter?this.contentFormatter(a):this.content(a);else if(this.shared&&"none"!==this.chart.plotInfo.axisPlacement){for(var p="",h=0;h<a.length;h++)if(c=a[h].dataSeries,d=a[h].dataPoint,e=a[h].index,g="",0===h&&(f&&!this.content)&&(p+="undefined"!==typeof this.chart.axisX.labels[d.x]?this.chart.axisX.labels[d.x]:"{x}",p+="</br>",p=this.chart.replaceKeywordsWithValue(p,d,c,e)),null!==d.toolTipContent&&("undefined"!==typeof d.toolTipContent||
null!==c._options.toolTipContent)){if("line"===c.type||"stepLine"===c.type||"spline"===c.type||"area"===c.type||"stepArea"===c.type||"splineArea"===c.type||"column"===c.type||"bar"===c.type||"scatter"===c.type||"stackedColumn"===c.type||"stackedColumn100"===c.type||"stackedBar"===c.type||"stackedBar100"===c.type||"stackedArea"===c.type||"stackedArea100"===c.type)g+=d.toolTipContent?d.toolTipContent:c.toolTipContent?c.toolTipContent:this.content&&"function"!==typeof this.content?this.content:"<span style='\"'color:{color};'\"'>{name}:</span>&nbsp;&nbsp;{y}";
else if("bubble"===c.type)g+=d.toolTipContent?d.toolTipContent:c.toolTipContent?c.toolTipContent:this.content&&"function"!==typeof this.content?this.content:"<span style='\"'color:{color};'\"'>{name}:</span>&nbsp;&nbsp;{y}, &nbsp;&nbsp;{z}";else if("pie"===c.type||"doughnut"===c.type||"funnel"===c.type)g+=d.toolTipContent?d.toolTipContent:c.toolTipContent?c.toolTipContent:this.content&&"function"!==typeof this.content?this.content:"&nbsp;&nbsp;{y}";else if("rangeColumn"===c.type||"rangeBar"===c.type||
"rangeArea"===c.type||"rangeSplineArea"===c.type)g+=d.toolTipContent?d.toolTipContent:c.toolTipContent?c.toolTipContent:this.content&&"function"!==typeof this.content?this.content:"<span style='\"'color:{color};'\"'>{name}:</span>&nbsp;&nbsp;{y[0]},&nbsp;{y[1]}";else if("candlestick"===c.type||"ohlc"===c.type)g+=d.toolTipContent?d.toolTipContent:c.toolTipContent?c.toolTipContent:this.content&&"function"!==typeof this.content?this.content:"<span style='\"'color:{color};'\"'>{name}:</span><br/>Open: &nbsp;&nbsp;{y[0]}<br/>High: &nbsp;&nbsp;&nbsp;{y[1]}<br/>Low:&nbsp;&nbsp;&nbsp;{y[2]}<br/>Close: &nbsp;&nbsp;{y[3]}";
null===b&&(b="");!0===this.reversed?(b=this.chart.replaceKeywordsWithValue(g,d,c,e)+b,h<a.length-1&&(b="</br>"+b)):(b+=this.chart.replaceKeywordsWithValue(g,d,c,e),h<a.length-1&&(b+="</br>"))}null!==b&&(b=p+b)}else{c=a[0].dataSeries;d=a[0].dataPoint;e=a[0].index;if(null===d.toolTipContent||"undefined"===typeof d.toolTipContent&&null===c._options.toolTipContent)return null;if("line"===c.type||"stepLine"===c.type||"spline"===c.type||"area"===c.type||"stepArea"===c.type||"splineArea"===c.type||"column"===
c.type||"bar"===c.type||"scatter"===c.type||"stackedColumn"===c.type||"stackedColumn100"===c.type||"stackedBar"===c.type||"stackedBar100"===c.type||"stackedArea"===c.type||"stackedArea100"===c.type)g=d.toolTipContent?d.toolTipContent:c.toolTipContent?c.toolTipContent:this.content&&"function"!==typeof this.content?this.content:"<span style='\"'color:{color};'\"'>"+(d.label?"{label}":"{x}")+" :</span>&nbsp;&nbsp;{y}";else if("bubble"===c.type)g=d.toolTipContent?d.toolTipContent:c.toolTipContent?c.toolTipContent:
this.content&&"function"!==typeof this.content?this.content:"<span style='\"'color:{color};'\"'>"+(d.label?"{label}":"{x}")+":</span>&nbsp;&nbsp;{y}, &nbsp;&nbsp;{z}";else if("pie"===c.type||"doughnut"===c.type||"funnel"===c.type)g=d.toolTipContent?d.toolTipContent:c.toolTipContent?c.toolTipContent:this.content&&"function"!==typeof this.content?this.content:(d.name?"{name}:&nbsp;&nbsp;":d.label?"{label}:&nbsp;&nbsp;":"")+"{y}";else if("rangeColumn"===c.type||"rangeBar"===c.type||"rangeArea"===c.type||
"rangeSplineArea"===c.type)g=d.toolTipContent?d.toolTipContent:c.toolTipContent?c.toolTipContent:this.content&&"function"!==typeof this.content?this.content:"<span style='\"'color:{color};'\"'>"+(d.label?"{label}":"{x}")+" :</span>&nbsp;&nbsp;{y[0]}, &nbsp;{y[1]}";else if("candlestick"===c.type||"ohlc"===c.type)g=d.toolTipContent?d.toolTipContent:c.toolTipContent?c.toolTipContent:this.content&&"function"!==typeof this.content?this.content:"<span style='\"'color:{color};'\"'>"+(d.label?"{label}":"{x}")+
"</span><br/>Open: &nbsp;&nbsp;{y[0]}<br/>High: &nbsp;&nbsp;&nbsp;{y[1]}<br/>Low: &nbsp;&nbsp;&nbsp;&nbsp;{y[2]}<br/>Close: &nbsp;&nbsp;{y[3]}";null===b&&(b="");b+=this.chart.replaceKeywordsWithValue(g,d,c,e)}return b};Q.prototype.enableAnimation=function(){this.container.style.WebkitTransition||(this.container.style.WebkitTransition="left .2s ease-out, bottom .2s ease-out",this.container.style.MozTransition="left .2s ease-out, bottom .2s ease-out",this.container.style.MsTransition="left .2s ease-out, bottom .2s ease-out",
this.container.style.transition="left .2s ease-out, bottom .2s ease-out")};Q.prototype.disableAnimation=function(){this.container.style.WebkitTransition&&(this.container.style.WebkitTransition="",this.container.style.MozTransition="",this.container.style.MsTransition="",this.container.style.transition="")};Q.prototype.hide=function(a){this.enabled&&(this.container.style.display="none",this.currentSeriesIndex=-1,this._prevY=this._prevX=NaN,("undefined"===typeof a||a)&&this.chart.resetOverlayedCanvas())};
u.prototype.getPercentAndTotal=function(a,b){var c=null,d=null,e=null;if(0<=a.type.indexOf("stacked"))d=0,c=b.x.getTime?b.x.getTime():b.x,c in a.plotUnit.yTotals&&(d=a.plotUnit.yTotals[c],e=isNaN(b.y)?0:0===d?0:100*(b.y/d));else if("pie"===a.type||"doughnut"===a.type){for(i=d=0;i<a.dataPoints.length;i++)isNaN(a.dataPoints[i].y)||(d+=a.dataPoints[i].y);e=isNaN(b.y)?0:100*(b.y/d)}return{percent:e,total:d}};u.prototype.replaceKeywordsWithValue=function(a,b,c,d,e){var g=this;e="undefined"===typeof e?
0:e;if((0<=c.type.indexOf("stacked")||"pie"===c.type||"doughnut"===c.type)&&(0<=a.indexOf("#percent")||0<=a.indexOf("#total"))){var f="#percent",h="#total",p=this.getPercentAndTotal(c,b),h=isNaN(p.total)?h:p.total,f=isNaN(p.percent)?f:p.percent;do{p="";if(c.percentFormatString)p=c.percentFormatString;else{var p="#,##0.",k=Math.max(Math.ceil(Math.log(1/Math.abs(f))/Math.LN10),2);if(isNaN(k)||!isFinite(k))k=2;for(var l=0;l<k;l++)p+="#"}a=a.replace("#percent",W(f,p,g._cultureInfo));a=a.replace("#total",
W(h,c.yValueFormatString?c.yValueFormatString:"#,##0.########"))}while(0<=a.indexOf("#percent")||0<=a.indexOf("#total"))}return a.replace(/\{.*?\}|"[^"]*"|'[^']*'/g,function(a){if('"'===a[0]&&'"'===a[a.length-1]||"'"===a[0]&&"'"===a[a.length-1])return a.slice(1,a.length-1);a=Z(a.slice(1,a.length-1));a=a.replace("#index",e);var f=null;try{var h=a.match(/(.*?)\s*\[\s*(.*?)\s*\]/);h&&0<h.length&&(f=Z(h[2]),a=Z(h[1]))}catch(k){}h=null;if("color"===a)return b.color?b.color:c.color?c.color:c._colorSet[d%
c._colorSet.length];if(b.hasOwnProperty(a))h=b;else if(c.hasOwnProperty(a))h=c;else return"";h=h[a];null!==f&&(h=h[f]);return"x"===a?g.axisX&&"dateTime"===g.plotInfo.axisXValueType?qa(h,b.xValueFormatString?b.xValueFormatString:c.xValueFormatString?c.xValueFormatString:g.axisX&&g.axisX.valueFormatString?g.axisX.valueFormatString:"DD MMM YY",g._cultureInfo):W(h,b.xValueFormatString?b.xValueFormatString:c.xValueFormatString?c.xValueFormatString:"#,##0.########",g._cultureInfo):"y"===a?W(h,b.yValueFormatString?
b.yValueFormatString:c.yValueFormatString?c.yValueFormatString:"#,##0.########",g._cultureInfo):"z"===a?W(h,b.zValueFormatString?b.zValueFormatString:c.zValueFormatString?c.zValueFormatString:"#,##0.########",g._cultureInfo):h})};$.prototype.reset=function(){this.lastObjectId=0;this.objectMap=[];this.rectangularRegionEventSubscriptions=[];this.previousDataPointEventObject=null;this.eventObjects=[];t&&(this.ghostCtx.clearRect(0,0,this.chart.width,this.chart.height),this.ghostCtx.beginPath())};$.prototype.getNewObjectTrackingId=
function(){return++this.lastObjectId};$.prototype.mouseEventHandler=function(a){if("mousemove"===a.type||"click"===a.type){var b=[],c=ma(a),d=null;if((d=this.chart.getObjectAtXY(c.x,c.y,!1))&&"undefined"!==typeof this.objectMap[d])if(d=this.objectMap[d],"dataPoint"===d.objectType){var e=this.chart.data[d.dataSeriesIndex],g=e.dataPoints[d.dataPointIndex],f=d.dataPointIndex;d.eventParameter={x:c.x,y:c.y,dataPoint:g,dataSeries:e._options,dataPointIndex:f,dataSeriesIndex:e.index,chart:this.chart._publicChartReference};
d.eventContext={context:g,userContext:g,mouseover:"mouseover",mousemove:"mousemove",mouseout:"mouseout",click:"click"};b.push(d);d=this.objectMap[e.id];d.eventParameter={x:c.x,y:c.y,dataPoint:g,dataSeries:e._options,dataPointIndex:f,dataSeriesIndex:e.index,chart:this.chart._publicChartReference};d.eventContext={context:e,userContext:e._options,mouseover:"mouseover",mousemove:"mousemove",mouseout:"mouseout",click:"click"};b.push(this.objectMap[e.id])}else"legendItem"===d.objectType&&(e=this.chart.data[d.dataSeriesIndex],
g=null!==d.dataPointIndex?e.dataPoints[d.dataPointIndex]:null,d.eventParameter={x:c.x,y:c.y,dataSeries:e._options,dataPoint:g,dataPointIndex:d.dataPointIndex,dataSeriesIndex:d.dataSeriesIndex,chart:this.chart._publicChartReference},d.eventContext={context:this.chart.legend,userContext:this.chart.legend._options,mouseover:"itemmouseover",mousemove:"itemmousemove",mouseout:"itemmouseout",click:"itemclick"},b.push(d));e=[];for(c=0;c<this.mouseoveredObjectMaps.length;c++){g=!0;for(d=0;d<b.length;d++)if(b[d].id===
this.mouseoveredObjectMaps[c].id){g=!1;break}g?this.fireEvent(this.mouseoveredObjectMaps[c],"mouseout",a):e.push(this.mouseoveredObjectMaps[c])}this.mouseoveredObjectMaps=e;for(c=0;c<b.length;c++){e=!1;for(d=0;d<this.mouseoveredObjectMaps.length;d++)if(b[c].id===this.mouseoveredObjectMaps[d].id){e=!0;break}e||(this.fireEvent(b[c],"mouseover",a),this.mouseoveredObjectMaps.push(b[c]));"click"===a.type?this.fireEvent(b[c],"click",a):"mousemove"===a.type&&this.fireEvent(b[c],"mousemove",a)}}};$.prototype.fireEvent=
function(a,b,c){if(a&&b){var d=a.eventParameter,e=a.eventContext,g=a.eventContext.userContext;g&&(e&&g[e[b]])&&g[e[b]].call(g,d);"mouseout"!==b?g.cursor&&g.cursor!==c.target.style.cursor&&(c.target.style.cursor=g.cursor):(c.target.style.cursor=this.chart._defaultCursor,delete a.eventParameter,delete a.eventContext);"click"===b&&("dataPoint"===a.objectType&&this.chart.pieDoughnutClickHandler)&&this.chart.pieDoughnutClickHandler.call(this.chart.data[a.dataSeriesIndex],d)}};O(ba,G);pa.prototype.animate=
function(a,b,c,d,e){var g=this;this.chart.isAnimating=!0;e=e||A.easing.linear;c&&this.animations.push({startTime:(new Date).getTime()+(a?a:0),duration:b,animationCallback:c,onComplete:d});for(a=[];0<this.animations.length;)if(b=this.animations.shift(),c=(new Date).getTime(),d=0,b.startTime<=c&&(d=e(Math.min(c-b.startTime,b.duration),0,1,b.duration),d=Math.min(d,1),isNaN(d)||!isFinite(d))&&(d=1),1>d&&a.push(b),b.animationCallback(d),1<=d&&b.onComplete)b.onComplete();this.animations=a;0<this.animations.length?
this.animationRequestId=this.chart.requestAnimFrame.call(window,function(){g.animate.call(g)}):this.chart.isAnimating=!1};pa.prototype.cancelAllAnimations=function(){this.animations=[];this.animationRequestId&&this.chart.cancelRequestAnimFrame.call(window,this.animationRequestId);this.animationRequestId=null;this.chart.isAnimating=!1};var A={yScaleAnimation:function(a,b){if(0!==a){var c=b.dest,d=b.source.canvas,e=b.animationBase;c.drawImage(d,0,0,d.width,d.height,0,e-e*a,c.canvas.width/J,a*c.canvas.height/
J)}},xScaleAnimation:function(a,b){if(0!==a){var c=b.dest,d=b.source.canvas,e=b.animationBase;c.drawImage(d,0,0,d.width,d.height,e-e*a,0,a*c.canvas.width/J,c.canvas.height/J)}},xClipAnimation:function(a,b){if(0!==a){var c=b.dest,d=b.source.canvas;c.save();0<a&&c.drawImage(d,0,0,d.width*a,d.height,0,0,d.width*a/J,d.height/J);c.restore()}},fadeInAnimation:function(a,b){if(0!==a){var c=b.dest,d=b.source.canvas;c.save();c.globalAlpha=a;c.drawImage(d,0,0,d.width,d.height,0,0,c.canvas.width/J,c.canvas.height/
J);c.restore()}},easing:{linear:function(a,b,c,d){return c*a/d+b},easeOutQuad:function(a,b,c,d){return-c*(a/=d)*(a-2)+b},easeOutQuart:function(a,b,c,d){return-c*((a=a/d-1)*a*a*a-1)+b},easeInQuad:function(a,b,c,d){return c*(a/=d)*a+b},easeInQuart:function(a,b,c,d){return c*(a/=d)*a*a*a+b}}},K={drawMarker:function(a,b,c,d,e,g,f,h){if(c){var p=1;c.fillStyle=g?g:"#000000";c.strokeStyle=f?f:"#000000";c.lineWidth=h?h:0;"circle"===d?(c.moveTo(a,b),c.beginPath(),c.arc(a,b,e/2,0,2*Math.PI,!1),g&&c.fill(),
h&&(f?c.stroke():(p=c.globalAlpha,c.globalAlpha=0.15,c.strokeStyle="black",c.stroke(),c.globalAlpha=p))):"square"===d?(c.beginPath(),c.rect(a-e/2,b-e/2,e,e),g&&c.fill(),h&&(f?c.stroke():(p=c.globalAlpha,c.globalAlpha=0.15,c.strokeStyle="black",c.stroke(),c.globalAlpha=p))):"triangle"===d?(c.beginPath(),c.moveTo(a-e/2,b+e/2),c.lineTo(a+e/2,b+e/2),c.lineTo(a,b-e/2),c.closePath(),g&&c.fill(),h&&(f?c.stroke():(p=c.globalAlpha,c.globalAlpha=0.15,c.strokeStyle="black",c.stroke(),c.globalAlpha=p)),c.beginPath()):
"cross"===d&&(c.strokeStyle=g,c.lineWidth=e/4,c.beginPath(),c.moveTo(a-e/2,b-e/2),c.lineTo(a+e/2,b+e/2),c.stroke(),c.moveTo(a+e/2,b-e/2),c.lineTo(a-e/2,b+e/2),c.stroke())}},drawMarkers:function(a){for(var b=0;b<a.length;b++){var c=a[b];K.drawMarker(c.x,c.y,c.ctx,c.type,c.size,c.color,c.borderColor,c.borderThickness)}}},za={Chart:function(a,b){var c=new u(a,b,this);this.render=function(){c.render(this.options)};this.options=c._options},addColorSet:function(a,b){V[a]=b},addCultureInfo:function(a,b){ca[a]=
b},formatNumber:function(a,b,c){c=c||"en";if(ca[c])return W(a,b||"#,##0.##",new ba(c));throw"Unknown Culture Name";},formatDate:function(a,b,c){c=c||"en";if(ca[c])return qa(a,b||"DD MMM YYYY",new ba(c));throw"Unknown Culture Name";}};za.Chart.version="v1.8.0 Beta 2";window.CanvasJS=za})();
/*
  excanvas is used to support IE678 which do not implement HTML5 Canvas Element. You can safely remove the following excanvas code if you don't need to support older browsers.

  Copyright 2006 Google Inc. https://code.google.com/p/explorercanvas/
  Licensed under the Apache License, Version 2.0
*/
document.createElement("canvas").getContext||function(){function V(){return this.context_||(this.context_=new C(this))}function W(a,b,c){var g=M.call(arguments,2);return function(){return a.apply(b,g.concat(M.call(arguments)))}}function N(a){return String(a).replace(/&/g,"&amp;").replace(/"/g,"&quot;")}function O(a){a.namespaces.g_vml_||a.namespaces.add("g_vml_","urn:schemas-microsoft-com:vml","#default#VML");a.namespaces.g_o_||a.namespaces.add("g_o_","urn:schemas-microsoft-com:office:office","#default#VML");
a.styleSheets.ex_canvas_||(a=a.createStyleSheet(),a.owningElement.id="ex_canvas_",a.cssText="canvas{display:inline-block;overflow:hidden;text-align:left;width:300px;height:150px}")}function X(a){var b=a.srcElement;switch(a.propertyName){case "width":b.getContext().clearRect();b.style.width=b.attributes.width.nodeValue+"px";b.firstChild.style.width=b.clientWidth+"px";break;case "height":b.getContext().clearRect(),b.style.height=b.attributes.height.nodeValue+"px",b.firstChild.style.height=b.clientHeight+
"px"}}function Y(a){a=a.srcElement;a.firstChild&&(a.firstChild.style.width=a.clientWidth+"px",a.firstChild.style.height=a.clientHeight+"px")}function D(){return[[1,0,0],[0,1,0],[0,0,1]]}function t(a,b){for(var c=D(),g=0;3>g;g++)for(var e=0;3>e;e++){for(var f=0,d=0;3>d;d++)f+=a[g][d]*b[d][e];c[g][e]=f}return c}function P(a,b){b.fillStyle=a.fillStyle;b.lineCap=a.lineCap;b.lineJoin=a.lineJoin;b.lineWidth=a.lineWidth;b.miterLimit=a.miterLimit;b.shadowBlur=a.shadowBlur;b.shadowColor=a.shadowColor;b.shadowOffsetX=
a.shadowOffsetX;b.shadowOffsetY=a.shadowOffsetY;b.strokeStyle=a.strokeStyle;b.globalAlpha=a.globalAlpha;b.font=a.font;b.textAlign=a.textAlign;b.textBaseline=a.textBaseline;b.arcScaleX_=a.arcScaleX_;b.arcScaleY_=a.arcScaleY_;b.lineScale_=a.lineScale_}function Q(a){var b=a.indexOf("(",3),c=a.indexOf(")",b+1),b=a.substring(b+1,c).split(",");if(4!=b.length||"a"!=a.charAt(3))b[3]=1;return b}function E(a,b,c){return Math.min(c,Math.max(b,a))}function F(a,b,c){0>c&&c++;1<c&&c--;return 1>6*c?a+6*(b-a)*c:
1>2*c?b:2>3*c?a+6*(b-a)*(2/3-c):a}function G(a){if(a in H)return H[a];var b,c=1;a=String(a);if("#"==a.charAt(0))b=a;else if(/^rgb/.test(a)){c=Q(a);b="#";for(var g,e=0;3>e;e++)g=-1!=c[e].indexOf("%")?Math.floor(255*(parseFloat(c[e])/100)):+c[e],b+=v[E(g,0,255)];c=+c[3]}else if(/^hsl/.test(a)){e=c=Q(a);b=parseFloat(e[0])/360%360;0>b&&b++;g=E(parseFloat(e[1])/100,0,1);e=E(parseFloat(e[2])/100,0,1);if(0==g)g=e=b=e;else{var f=0.5>e?e*(1+g):e+g-e*g,d=2*e-f;g=F(d,f,b+1/3);e=F(d,f,b);b=F(d,f,b-1/3)}b="#"+
v[Math.floor(255*g)]+v[Math.floor(255*e)]+v[Math.floor(255*b)];c=c[3]}else b=Z[a]||a;return H[a]={color:b,alpha:c}}function C(a){this.m_=D();this.mStack_=[];this.aStack_=[];this.currentPath_=[];this.fillStyle=this.strokeStyle="#000";this.lineWidth=1;this.lineJoin="miter";this.lineCap="butt";this.miterLimit=1*q;this.globalAlpha=1;this.font="10px sans-serif";this.textAlign="left";this.textBaseline="alphabetic";this.canvas=a;var b="width:"+a.clientWidth+"px;height:"+a.clientHeight+"px;overflow:hidden;position:absolute",
c=a.ownerDocument.createElement("div");c.style.cssText=b;a.appendChild(c);b=c.cloneNode(!1);b.style.backgroundColor="red";b.style.filter="alpha(opacity=0)";a.appendChild(b);this.element_=c;this.lineScale_=this.arcScaleY_=this.arcScaleX_=1}function R(a,b,c,g){a.currentPath_.push({type:"bezierCurveTo",cp1x:b.x,cp1y:b.y,cp2x:c.x,cp2y:c.y,x:g.x,y:g.y});a.currentX_=g.x;a.currentY_=g.y}function S(a,b){var c=G(a.strokeStyle),g=c.color,c=c.alpha*a.globalAlpha,e=a.lineScale_*a.lineWidth;1>e&&(c*=e);b.push("<g_vml_:stroke",
' opacity="',c,'"',' joinstyle="',a.lineJoin,'"',' miterlimit="',a.miterLimit,'"',' endcap="',$[a.lineCap]||"square",'"',' weight="',e,'px"',' color="',g,'" />')}function T(a,b,c,g){var e=a.fillStyle,f=a.arcScaleX_,d=a.arcScaleY_,k=g.x-c.x,n=g.y-c.y;if(e instanceof w){var h=0,l=g=0,u=0,m=1;if("gradient"==e.type_){h=e.x1_/f;c=e.y1_/d;var p=s(a,e.x0_/f,e.y0_/d),h=s(a,h,c),h=180*Math.atan2(h.x-p.x,h.y-p.y)/Math.PI;0>h&&(h+=360);1E-6>h&&(h=0)}else p=s(a,e.x0_,e.y0_),g=(p.x-c.x)/k,l=(p.y-c.y)/n,k/=f*q,
n/=d*q,m=x.max(k,n),u=2*e.r0_/m,m=2*e.r1_/m-u;f=e.colors_;f.sort(function(a,b){return a.offset-b.offset});d=f.length;p=f[0].color;c=f[d-1].color;k=f[0].alpha*a.globalAlpha;a=f[d-1].alpha*a.globalAlpha;for(var n=[],r=0;r<d;r++){var t=f[r];n.push(t.offset*m+u+" "+t.color)}b.push('<g_vml_:fill type="',e.type_,'"',' method="none" focus="100%"',' color="',p,'"',' color2="',c,'"',' colors="',n.join(","),'"',' opacity="',a,'"',' g_o_:opacity2="',k,'"',' angle="',h,'"',' focusposition="',g,",",l,'" />')}else e instanceof
I?k&&n&&b.push("<g_vml_:fill",' position="',-c.x/k*f*f,",",-c.y/n*d*d,'"',' type="tile"',' src="',e.src_,'" />'):(e=G(a.fillStyle),b.push('<g_vml_:fill color="',e.color,'" opacity="',e.alpha*a.globalAlpha,'" />'))}function s(a,b,c){a=a.m_;return{x:q*(b*a[0][0]+c*a[1][0]+a[2][0])-r,y:q*(b*a[0][1]+c*a[1][1]+a[2][1])-r}}function z(a,b,c){isFinite(b[0][0])&&(isFinite(b[0][1])&&isFinite(b[1][0])&&isFinite(b[1][1])&&isFinite(b[2][0])&&isFinite(b[2][1]))&&(a.m_=b,c&&(a.lineScale_=aa(ba(b[0][0]*b[1][1]-b[0][1]*
b[1][0]))))}function w(a){this.type_=a;this.r1_=this.y1_=this.x1_=this.r0_=this.y0_=this.x0_=0;this.colors_=[]}function I(a,b){if(!a||1!=a.nodeType||"IMG"!=a.tagName)throw new A("TYPE_MISMATCH_ERR");if("complete"!=a.readyState)throw new A("INVALID_STATE_ERR");switch(b){case "repeat":case null:case "":this.repetition_="repeat";break;case "repeat-x":case "repeat-y":case "no-repeat":this.repetition_=b;break;default:throw new A("SYNTAX_ERR");}this.src_=a.src;this.width_=a.width;this.height_=a.height}
function A(a){this.code=this[a];this.message=a+": DOM Exception "+this.code}var x=Math,k=x.round,J=x.sin,K=x.cos,ba=x.abs,aa=x.sqrt,q=10,r=q/2;navigator.userAgent.match(/MSIE ([\d.]+)?/);var M=Array.prototype.slice;O(document);var U={init:function(a){a=a||document;a.createElement("canvas");a.attachEvent("onreadystatechange",W(this.init_,this,a))},init_:function(a){a=a.getElementsByTagName("canvas");for(var b=0;b<a.length;b++)this.initElement(a[b])},initElement:function(a){if(!a.getContext){a.getContext=
V;O(a.ownerDocument);a.innerHTML="";a.attachEvent("onpropertychange",X);a.attachEvent("onresize",Y);var b=a.attributes;b.width&&b.width.specified?a.style.width=b.width.nodeValue+"px":a.width=a.clientWidth;b.height&&b.height.specified?a.style.height=b.height.nodeValue+"px":a.height=a.clientHeight}return a}};U.init();for(var v=[],d=0;16>d;d++)for(var B=0;16>B;B++)v[16*d+B]=d.toString(16)+B.toString(16);var Z={aliceblue:"#F0F8FF",antiquewhite:"#FAEBD7",aquamarine:"#7FFFD4",azure:"#F0FFFF",beige:"#F5F5DC",
bisque:"#FFE4C4",black:"#000000",blanchedalmond:"#FFEBCD",blueviolet:"#8A2BE2",brown:"#A52A2A",burlywood:"#DEB887",cadetblue:"#5F9EA0",chartreuse:"#7FFF00",chocolate:"#D2691E",coral:"#FF7F50",cornflowerblue:"#6495ED",cornsilk:"#FFF8DC",crimson:"#DC143C",cyan:"#00FFFF",darkblue:"#00008B",darkcyan:"#008B8B",darkgoldenrod:"#B8860B",darkgray:"#A9A9A9",darkgreen:"#006400",darkgrey:"#A9A9A9",darkkhaki:"#BDB76B",darkmagenta:"#8B008B",darkolivegreen:"#556B2F",darkorange:"#FF8C00",darkorchid:"#9932CC",darkred:"#8B0000",
darksalmon:"#E9967A",darkseagreen:"#8FBC8F",darkslateblue:"#483D8B",darkslategray:"#2F4F4F",darkslategrey:"#2F4F4F",darkturquoise:"#00CED1",darkviolet:"#9400D3",deeppink:"#FF1493",deepskyblue:"#00BFFF",dimgray:"#696969",dimgrey:"#696969",dodgerblue:"#1E90FF",firebrick:"#B22222",floralwhite:"#FFFAF0",forestgreen:"#228B22",gainsboro:"#DCDCDC",ghostwhite:"#F8F8FF",gold:"#FFD700",goldenrod:"#DAA520",grey:"#808080",greenyellow:"#ADFF2F",honeydew:"#F0FFF0",hotpink:"#FF69B4",indianred:"#CD5C5C",indigo:"#4B0082",
ivory:"#FFFFF0",khaki:"#F0E68C",lavender:"#E6E6FA",lavenderblush:"#FFF0F5",lawngreen:"#7CFC00",lemonchiffon:"#FFFACD",lightblue:"#ADD8E6",lightcoral:"#F08080",lightcyan:"#E0FFFF",lightgoldenrodyellow:"#FAFAD2",lightgreen:"#90EE90",lightgrey:"#D3D3D3",lightpink:"#FFB6C1",lightsalmon:"#FFA07A",lightseagreen:"#20B2AA",lightskyblue:"#87CEFA",lightslategray:"#778899",lightslategrey:"#778899",lightsteelblue:"#B0C4DE",lightyellow:"#FFFFE0",limegreen:"#32CD32",linen:"#FAF0E6",magenta:"#FF00FF",mediumaquamarine:"#66CDAA",
mediumblue:"#0000CD",mediumorchid:"#BA55D3",mediumpurple:"#9370DB",mediumseagreen:"#3CB371",mediumslateblue:"#7B68EE",mediumspringgreen:"#00FA9A",mediumturquoise:"#48D1CC",mediumvioletred:"#C71585",midnightblue:"#191970",mintcream:"#F5FFFA",mistyrose:"#FFE4E1",moccasin:"#FFE4B5",navajowhite:"#FFDEAD",oldlace:"#FDF5E6",olivedrab:"#6B8E23",orange:"#FFA500",orangered:"#FF4500",orchid:"#DA70D6",palegoldenrod:"#EEE8AA",palegreen:"#98FB98",paleturquoise:"#AFEEEE",palevioletred:"#DB7093",papayawhip:"#FFEFD5",
peachpuff:"#FFDAB9",peru:"#CD853F",pink:"#FFC0CB",plum:"#DDA0DD",powderblue:"#B0E0E6",rosybrown:"#BC8F8F",royalblue:"#4169E1",saddlebrown:"#8B4513",salmon:"#FA8072",sandybrown:"#F4A460",seagreen:"#2E8B57",seashell:"#FFF5EE",sienna:"#A0522D",skyblue:"#87CEEB",slateblue:"#6A5ACD",slategray:"#708090",slategrey:"#708090",snow:"#FFFAFA",springgreen:"#00FF7F",steelblue:"#4682B4",tan:"#D2B48C",thistle:"#D8BFD8",tomato:"#FF6347",turquoise:"#40E0D0",violet:"#EE82EE",wheat:"#F5DEB3",whitesmoke:"#F5F5F5",yellowgreen:"#9ACD32"},
H={},L={},$={butt:"flat",round:"round"},d=C.prototype;d.clearRect=function(){this.textMeasureEl_&&(this.textMeasureEl_.removeNode(!0),this.textMeasureEl_=null);this.element_.innerHTML=""};d.beginPath=function(){this.currentPath_=[]};d.moveTo=function(a,b){var c=s(this,a,b);this.currentPath_.push({type:"moveTo",x:c.x,y:c.y});this.currentX_=c.x;this.currentY_=c.y};d.lineTo=function(a,b){var c=s(this,a,b);this.currentPath_.push({type:"lineTo",x:c.x,y:c.y});this.currentX_=c.x;this.currentY_=c.y};d.bezierCurveTo=
function(a,b,c,g,e,f){e=s(this,e,f);a=s(this,a,b);c=s(this,c,g);R(this,a,c,e)};d.quadraticCurveTo=function(a,b,c,g){a=s(this,a,b);c=s(this,c,g);g={x:this.currentX_+2/3*(a.x-this.currentX_),y:this.currentY_+2/3*(a.y-this.currentY_)};R(this,g,{x:g.x+(c.x-this.currentX_)/3,y:g.y+(c.y-this.currentY_)/3},c)};d.arc=function(a,b,c,g,e,f){c*=q;var d=f?"at":"wa",k=a+K(g)*c-r,n=b+J(g)*c-r;g=a+K(e)*c-r;e=b+J(e)*c-r;k!=g||f||(k+=0.125);a=s(this,a,b);k=s(this,k,n);g=s(this,g,e);this.currentPath_.push({type:d,
x:a.x,y:a.y,radius:c,xStart:k.x,yStart:k.y,xEnd:g.x,yEnd:g.y})};d.rect=function(a,b,c,g){this.moveTo(a,b);this.lineTo(a+c,b);this.lineTo(a+c,b+g);this.lineTo(a,b+g);this.closePath()};d.strokeRect=function(a,b,c,g){var e=this.currentPath_;this.beginPath();this.moveTo(a,b);this.lineTo(a+c,b);this.lineTo(a+c,b+g);this.lineTo(a,b+g);this.closePath();this.stroke();this.currentPath_=e};d.fillRect=function(a,b,c,g){var e=this.currentPath_;this.beginPath();this.moveTo(a,b);this.lineTo(a+c,b);this.lineTo(a+
c,b+g);this.lineTo(a,b+g);this.closePath();this.fill();this.currentPath_=e};d.createLinearGradient=function(a,b,c,g){var e=new w("gradient");e.x0_=a;e.y0_=b;e.x1_=c;e.y1_=g;return e};d.createRadialGradient=function(a,b,c,g,e,f){var d=new w("gradientradial");d.x0_=a;d.y0_=b;d.r0_=c;d.x1_=g;d.y1_=e;d.r1_=f;return d};d.drawImage=function(a,b){var c,g,e,d,r,y,n,h;e=a.runtimeStyle.width;d=a.runtimeStyle.height;a.runtimeStyle.width="auto";a.runtimeStyle.height="auto";var l=a.width,u=a.height;a.runtimeStyle.width=
e;a.runtimeStyle.height=d;if(3==arguments.length)c=arguments[1],g=arguments[2],r=y=0,n=e=l,h=d=u;else if(5==arguments.length)c=arguments[1],g=arguments[2],e=arguments[3],d=arguments[4],r=y=0,n=l,h=u;else if(9==arguments.length)r=arguments[1],y=arguments[2],n=arguments[3],h=arguments[4],c=arguments[5],g=arguments[6],e=arguments[7],d=arguments[8];else throw Error("Invalid number of arguments");var m=s(this,c,g),p=[];p.push(" <g_vml_:group",' coordsize="',10*q,",",10*q,'"',' coordorigin="0,0"',' style="width:',
10,"px;height:",10,"px;position:absolute;");if(1!=this.m_[0][0]||this.m_[0][1]||1!=this.m_[1][1]||this.m_[1][0]){var t=[];t.push("M11=",this.m_[0][0],",","M12=",this.m_[1][0],",","M21=",this.m_[0][1],",","M22=",this.m_[1][1],",","Dx=",k(m.x/q),",","Dy=",k(m.y/q),"");var v=s(this,c+e,g),w=s(this,c,g+d);c=s(this,c+e,g+d);m.x=x.max(m.x,v.x,w.x,c.x);m.y=x.max(m.y,v.y,w.y,c.y);p.push("padding:0 ",k(m.x/q),"px ",k(m.y/q),"px 0;filter:progid:DXImageTransform.Microsoft.Matrix(",t.join(""),", sizingmethod='clip');")}else p.push("top:",
k(m.y/q),"px;left:",k(m.x/q),"px;");p.push(' ">','<g_vml_:image src="',a.src,'"',' style="width:',q*e,"px;"," height:",q*d,'px"',' cropleft="',r/l,'"',' croptop="',y/u,'"',' cropright="',(l-r-n)/l,'"',' cropbottom="',(u-y-h)/u,'"'," />","</g_vml_:group>");this.element_.insertAdjacentHTML("BeforeEnd",p.join(""))};d.stroke=function(a){var b=[];b.push("<g_vml_:shape",' filled="',!!a,'"',' style="position:absolute;width:',10,"px;height:",10,'px;"',' coordorigin="0,0"',' coordsize="',10*q,",",10*q,'"',
' stroked="',!a,'"',' path="');for(var c={x:null,y:null},d={x:null,y:null},e=0;e<this.currentPath_.length;e++){var f=this.currentPath_[e];switch(f.type){case "moveTo":b.push(" m ",k(f.x),",",k(f.y));break;case "lineTo":b.push(" l ",k(f.x),",",k(f.y));break;case "close":b.push(" x ");f=null;break;case "bezierCurveTo":b.push(" c ",k(f.cp1x),",",k(f.cp1y),",",k(f.cp2x),",",k(f.cp2y),",",k(f.x),",",k(f.y));break;case "at":case "wa":b.push(" ",f.type," ",k(f.x-this.arcScaleX_*f.radius),",",k(f.y-this.arcScaleY_*
f.radius)," ",k(f.x+this.arcScaleX_*f.radius),",",k(f.y+this.arcScaleY_*f.radius)," ",k(f.xStart),",",k(f.yStart)," ",k(f.xEnd),",",k(f.yEnd))}if(f){if(null==c.x||f.x<c.x)c.x=f.x;if(null==d.x||f.x>d.x)d.x=f.x;if(null==c.y||f.y<c.y)c.y=f.y;if(null==d.y||f.y>d.y)d.y=f.y}}b.push(' ">');a?T(this,b,c,d):S(this,b);b.push("</g_vml_:shape>");this.element_.insertAdjacentHTML("beforeEnd",b.join(""))};d.fill=function(){this.stroke(!0)};d.closePath=function(){this.currentPath_.push({type:"close"})};d.save=function(){var a=
{};P(this,a);this.aStack_.push(a);this.mStack_.push(this.m_);this.m_=t(D(),this.m_)};d.restore=function(){this.aStack_.length&&(P(this.aStack_.pop(),this),this.m_=this.mStack_.pop())};d.translate=function(a,b){z(this,t([[1,0,0],[0,1,0],[a,b,1]],this.m_),!1)};d.rotate=function(a){var b=K(a);a=J(a);z(this,t([[b,a,0],[-a,b,0],[0,0,1]],this.m_),!1)};d.scale=function(a,b){this.arcScaleX_*=a;this.arcScaleY_*=b;z(this,t([[a,0,0],[0,b,0],[0,0,1]],this.m_),!0)};d.transform=function(a,b,c,d,e,f){z(this,t([[a,
b,0],[c,d,0],[e,f,1]],this.m_),!0)};d.setTransform=function(a,b,c,d,e,f){z(this,[[a,b,0],[c,d,0],[e,f,1]],!0)};d.drawText_=function(a,b,c,d,e){var f=this.m_;d=0;var r=1E3,t=0,n=[],h;h=this.font;if(L[h])h=L[h];else{var l=document.createElement("div").style;try{l.font=h}catch(u){}h=L[h]={style:l.fontStyle||"normal",variant:l.fontVariant||"normal",weight:l.fontWeight||"normal",size:l.fontSize||10,family:l.fontFamily||"sans-serif"}}var l=h,m=this.element_;h={};for(var p in l)h[p]=l[p];p=parseFloat(m.currentStyle.fontSize);
m=parseFloat(l.size);"number"==typeof l.size?h.size=l.size:-1!=l.size.indexOf("px")?h.size=m:-1!=l.size.indexOf("em")?h.size=p*m:-1!=l.size.indexOf("%")?h.size=p/100*m:-1!=l.size.indexOf("pt")?h.size=m/0.75:h.size=p;h.size*=0.981;p=h.style+" "+h.variant+" "+h.weight+" "+h.size+"px "+h.family;m=this.element_.currentStyle;l=this.textAlign.toLowerCase();switch(l){case "left":case "center":case "right":break;case "end":l="ltr"==m.direction?"right":"left";break;case "start":l="rtl"==m.direction?"right":
"left";break;default:l="left"}switch(this.textBaseline){case "hanging":case "top":t=h.size/1.75;break;case "middle":break;default:case null:case "alphabetic":case "ideographic":case "bottom":t=-h.size/2.25}switch(l){case "right":d=1E3;r=0.05;break;case "center":d=r=500}b=s(this,b+0,c+t);n.push('<g_vml_:line from="',-d,' 0" to="',r,' 0.05" ',' coordsize="100 100" coordorigin="0 0"',' filled="',!e,'" stroked="',!!e,'" style="position:absolute;width:1px;height:1px;">');e?S(this,n):T(this,n,{x:-d,y:0},
{x:r,y:h.size});e=f[0][0].toFixed(3)+","+f[1][0].toFixed(3)+","+f[0][1].toFixed(3)+","+f[1][1].toFixed(3)+",0,0";b=k(b.x/q)+","+k(b.y/q);n.push('<g_vml_:skew on="t" matrix="',e,'" ',' offset="',b,'" origin="',d,' 0" />','<g_vml_:path textpathok="true" />','<g_vml_:textpath on="true" string="',N(a),'" style="v-text-align:',l,";font:",N(p),'" /></g_vml_:line>');this.element_.insertAdjacentHTML("beforeEnd",n.join(""))};d.fillText=function(a,b,c,d){this.drawText_(a,b,c,d,!1)};d.strokeText=function(a,
b,c,d){this.drawText_(a,b,c,d,!0)};d.measureText=function(a){this.textMeasureEl_||(this.element_.insertAdjacentHTML("beforeEnd",'<span style="position:absolute;top:-20000px;left:0;padding:0;margin:0;border:none;white-space:pre;"></span>'),this.textMeasureEl_=this.element_.lastChild);var b=this.element_.ownerDocument;this.textMeasureEl_.innerHTML="";this.textMeasureEl_.style.font=this.font;this.textMeasureEl_.appendChild(b.createTextNode(a));return{width:this.textMeasureEl_.offsetWidth}};d.clip=function(){};
d.arcTo=function(){};d.createPattern=function(a,b){return new I(a,b)};w.prototype.addColorStop=function(a,b){b=G(b);this.colors_.push({offset:a,color:b.color,alpha:b.alpha})};d=A.prototype=Error();d.INDEX_SIZE_ERR=1;d.DOMSTRING_SIZE_ERR=2;d.HIERARCHY_REQUEST_ERR=3;d.WRONG_DOCUMENT_ERR=4;d.INVALID_CHARACTER_ERR=5;d.NO_DATA_ALLOWED_ERR=6;d.NO_MODIFICATION_ALLOWED_ERR=7;d.NOT_FOUND_ERR=8;d.NOT_SUPPORTED_ERR=9;d.INUSE_ATTRIBUTE_ERR=10;d.INVALID_STATE_ERR=11;d.SYNTAX_ERR=12;d.INVALID_MODIFICATION_ERR=
13;d.NAMESPACE_ERR=14;d.INVALID_ACCESS_ERR=15;d.VALIDATION_ERR=16;d.TYPE_MISMATCH_ERR=17;G_vmlCanvasManager=U;CanvasRenderingContext2D=C;CanvasGradient=w;CanvasPattern=I;DOMException=A}();
/*
 CanvasJS jQuery Charting Plugin - http://canvasjs.com/ 
 Copyright 2013 fenopix
*/
(function(b,c,d,e){b.fn.CanvasJSChart=function(a){if(a){var b=this.first();a=new CanvasJS.Chart(this[0],a);b.children(".canvasjs-chart-container").data("canvasjsChartRef",a);a.render();return this}return this.first().children(".canvasjs-chart-container").data("canvasjsChartRef")}})(jQuery,window,document);

/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__("./node_modules/jquery/dist/jquery.js")))

/***/ }),

/***/ "./node_modules/css-loader/index.js!./node_modules/switchery/switchery.css":
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__("./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "/*\n *\n * Main stylesheet for Switchery.\n * http://abpetkov.github.io/switchery/\n *\n */\n\n.switchery {\n  background-color: #fff;\n  border: 1px solid #dfdfdf;\n  border-radius: 20px;\n  cursor: pointer;\n  display: inline-block;\n  height: 30px;\n  position: relative;\n  vertical-align: middle;\n  width: 50px;\n}\n\n.switchery > small {\n  background: #fff;\n  border-radius: 100%;\n  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);\n  height: 30px;\n  position: absolute;\n  top: 0;\n  width: 30px;\n}", ""]);

// exports


/***/ }),

/***/ "./node_modules/css-loader/lib/css-base.js":
/***/ (function(module, exports) {

/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Tobias Koppers @sokra
*/
// css base code, injected by the css-loader
module.exports = function(useSourceMap) {
	var list = [];

	// return the list of modules as css string
	list.toString = function toString() {
		return this.map(function (item) {
			var content = cssWithMappingToString(item, useSourceMap);
			if(item[2]) {
				return "@media " + item[2] + "{" + content + "}";
			} else {
				return content;
			}
		}).join("");
	};

	// import a list of modules into the list
	list.i = function(modules, mediaQuery) {
		if(typeof modules === "string")
			modules = [[null, modules, ""]];
		var alreadyImportedModules = {};
		for(var i = 0; i < this.length; i++) {
			var id = this[i][0];
			if(typeof id === "number")
				alreadyImportedModules[id] = true;
		}
		for(i = 0; i < modules.length; i++) {
			var item = modules[i];
			// skip already imported module
			// this implementation is not 100% perfect for weird media query combinations
			//  when a module is imported multiple times with different media queries.
			//  I hope this will never occur (Hey this way we have smaller bundles)
			if(typeof item[0] !== "number" || !alreadyImportedModules[item[0]]) {
				if(mediaQuery && !item[2]) {
					item[2] = mediaQuery;
				} else if(mediaQuery) {
					item[2] = "(" + item[2] + ") and (" + mediaQuery + ")";
				}
				list.push(item);
			}
		}
	};
	return list;
};

function cssWithMappingToString(item, useSourceMap) {
	var content = item[1] || '';
	var cssMapping = item[3];
	if (!cssMapping) {
		return content;
	}

	if (useSourceMap && typeof btoa === 'function') {
		var sourceMapping = toComment(cssMapping);
		var sourceURLs = cssMapping.sources.map(function (source) {
			return '/*# sourceURL=' + cssMapping.sourceRoot + source + ' */'
		});

		return [content].concat(sourceURLs).concat([sourceMapping]).join('\n');
	}

	return [content].join('\n');
}

// Adapted from convert-source-map (MIT)
function toComment(sourceMap) {
	// eslint-disable-next-line no-undef
	var base64 = btoa(unescape(encodeURIComponent(JSON.stringify(sourceMap))));
	var data = 'sourceMappingURL=data:application/json;charset=utf-8;base64,' + base64;

	return '/*# ' + data + ' */';
}


/***/ }),

/***/ "./node_modules/jquery-datatables-checkboxes/js/dataTables.checkboxes.min.js":
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*! Checkboxes 1.2.11
 *  Copyright (c) Gyrocode (www.gyrocode.com)
 *  License: MIT License
 */
(function(factory){if(true){!(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__("./node_modules/jquery/dist/jquery.js"),__webpack_require__("./node_modules/datatables.net/js/jquery.dataTables.js")], __WEBPACK_AMD_DEFINE_RESULT__ = (function($){return factory($,window,document);}).apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));}else{if(typeof exports==="object"){module.exports=function(root,$){if(!root){root=window;}if(!$||!$.fn.dataTable){$=require("datatables.net")(root,$).$;}return factory($,root,root.document);};}else{factory(jQuery,window,document);}}}(function($,window,document){var DataTable=$.fn.dataTable;var Checkboxes=function(settings){if(!DataTable.versionCheck||!DataTable.versionCheck("1.10.8")){throw"DataTables Checkboxes requires DataTables 1.10.8 or newer";}this.s={dt:new DataTable.Api(settings),columns:[],data:[],dataDisabled:[],ignoreSelect:false};this.s.ctx=this.s.dt.settings()[0];if(this.s.ctx.checkboxes){return;}settings.checkboxes=this;this._constructor();};Checkboxes.prototype={_constructor:function(){var self=this;var dt=self.s.dt;var ctx=self.s.ctx;var hasCheckboxes=false;var hasCheckboxesSelectRow=false;for(var i=0;i<ctx.aoColumns.length;i++){if(ctx.aoColumns[i].checkboxes){var $colHeader=$(dt.column(i).header());hasCheckboxes=true;if(!$.isPlainObject(ctx.aoColumns[i].checkboxes)){ctx.aoColumns[i].checkboxes={};}ctx.aoColumns[i].checkboxes=$.extend({},Checkboxes.defaults,ctx.aoColumns[i].checkboxes);var colOptions={"searchable":false,"orderable":false};if(ctx.aoColumns[i].sClass===""){colOptions["className"]="dt-checkboxes-cell";}else{colOptions["className"]=ctx.aoColumns[i].sClass+" dt-checkboxes-cell";}if(ctx.aoColumns[i].sWidthOrig===null){colOptions["width"]="1%";}if(ctx.aoColumns[i].mRender===null){colOptions["render"]=function(){return'<input type="checkbox" class="dt-checkboxes">';};}DataTable.ext.internal._fnColumnOptions(ctx,i,colOptions);$colHeader.removeClass("sorting");$colHeader.off(".dt");if(ctx.sAjaxSource===null){var cells=dt.cells("tr",i);cells.invalidate("data");$(cells.nodes()).addClass(colOptions["className"]);}self.s.data[i]={};self.s.dataDisabled[i]={};self.s.columns.push(i);if(ctx.aoColumns[i].checkboxes.selectRow){if(ctx._select){hasCheckboxesSelectRow=true;}else{ctx.aoColumns[i].checkboxes.selectRow=false;}}if(ctx.aoColumns[i].checkboxes.selectAll){$colHeader.data("html",$colHeader.html());if(ctx.aoColumns[i].checkboxes.selectAllRender!==null){var selectAllHtml="";if($.isFunction(ctx.aoColumns[i].checkboxes.selectAllRender)){selectAllHtml=ctx.aoColumns[i].checkboxes.selectAllRender();}else{if(typeof ctx.aoColumns[i].checkboxes.selectAllRender==="string"){selectAllHtml=ctx.aoColumns[i].checkboxes.selectAllRender;}}$colHeader.html(selectAllHtml).addClass("dt-checkboxes-select-all").attr("data-col",i);}}}}if(hasCheckboxes){self.loadState();var $table=$(dt.table().node());var $tableBody=$(dt.table().body());var $tableContainer=$(dt.table().container());if(hasCheckboxesSelectRow){$table.addClass("dt-checkboxes-select");$table.on("user-select.dt.dtCheckboxes",function(e,dt,type,cell,originalEvent){self.onDataTablesUserSelect(e,dt,type,cell,originalEvent);});$table.on("select.dt.dtCheckboxes deselect.dt.dtCheckboxes",function(e,api,type,indexes){self.onDataTablesSelectDeselect(e,type,indexes);});dt.select.info(false);$table.on("draw.dt.dtCheckboxes select.dt.dtCheckboxes deselect.dt.dtCheckboxes",function(){self.showInfoSelected();});}$table.on("draw.dt.dtCheckboxes",function(e){self.onDataTablesDraw(e);});$tableBody.on("click.dtCheckboxes","input.dt-checkboxes",function(e){self.onClick(e,this);});$tableContainer.on("click.dtCheckboxes",'thead th.dt-checkboxes-select-all input[type="checkbox"]',function(e){self.onClickSelectAll(e,this);});$tableContainer.on("click.dtCheckboxes","thead th.dt-checkboxes-select-all",function(){$('input[type="checkbox"]',this).not(":disabled").trigger("click");});if(!hasCheckboxesSelectRow){$tableContainer.on("click.dtCheckboxes","tbody td.dt-checkboxes-cell",function(){$('input[type="checkbox"]',this).not(":disabled").trigger("click");});}$tableContainer.on("click.dtCheckboxes","thead th.dt-checkboxes-select-all label, tbody td.dt-checkboxes-cell label",function(e){e.preventDefault();});$(document).on("click.dtCheckboxes",'.fixedHeader-floating thead th.dt-checkboxes-select-all input[type="checkbox"]',function(e){if(ctx._fixedHeader){if(ctx._fixedHeader.dom["header"].floating){self.onClickSelectAll(e,this);}}});$(document).on("click.dtCheckboxes",".fixedHeader-floating thead th.dt-checkboxes-select-all",function(){if(ctx._fixedHeader){if(ctx._fixedHeader.dom["header"].floating){$('input[type="checkbox"]',this).trigger("click");}}});$table.on("init.dt.dtCheckboxes",function(){self.onDataTablesInit();});$table.on("stateSaveParams.dt.dtCheckboxes",function(e,settings,data){self.onDataTablesStateSave(e,settings,data);});$table.one("destroy.dt.dtCheckboxes",function(e,settings){self.onDataTablesDestroy(e,settings);});}},onDataTablesInit:function(){var self=this;var dt=self.s.dt;var ctx=self.s.ctx;if(!ctx.oFeatures.bServerSide){if(ctx.oFeatures.bStateSave){self.updateState();}$(dt.table().node()).on("xhr.dt.dtCheckboxes",function(e,settings,json,xhr){self.onDataTablesXhr(e.settings,json,xhr);});}},onDataTablesUserSelect:function(e,dt,type,cell){var self=this;var cellIdx=cell.index();var rowIdx=cellIdx.row;var colIdx=self.getSelectRowColIndex();var cellData=dt.cell({row:rowIdx,column:colIdx}).data();if(!self.isCellSelectable(colIdx,cellData)){e.preventDefault();}},onDataTablesSelectDeselect:function(e,type,indexes){var self=this;var dt=self.s.dt;if(self.s.ignoreSelect){return;}if(type==="row"){var colIdx=self.getSelectRowColIndex();if(colIdx!==null){var cells=dt.cells(indexes,colIdx);self.updateData(cells,colIdx,(e.type==="select")?true:false);self.updateCheckbox(cells,colIdx,(e.type==="select")?true:false);self.updateSelectAll(colIdx);}}},onDataTablesStateSave:function(e,settings,data){var self=this;var ctx=self.s.ctx;data.checkboxes=[];$.each(self.s.columns,function(index,colIdx){if(ctx.aoColumns[colIdx].checkboxes.stateSave){data.checkboxes[colIdx]=self.s.data[colIdx];}});},onDataTablesDestroy:function(){var self=this;var dt=self.s.dt;var $table=$(dt.table().node());var $tableBody=$(dt.table().body());var $tableContainer=$(dt.table().container());$(document).off("click.dtCheckboxes");$tableContainer.off(".dtCheckboxes");$tableBody.off(".dtCheckboxes");$table.off(".dtCheckboxes");self.s.data={};self.s.dataDisabled={};$(".dt-checkboxes-select-all",$table).each(function(index,el){$(el).html($(el).data("html")).removeClass("dt-checkboxes-select-all");});},onDataTablesDraw:function(){var self=this;var ctx=self.s.ctx;if(ctx.oFeatures.bServerSide||ctx.oFeatures.bDeferRender){self.updateStateCheckboxes({page:"current",search:"none"});}$.each(self.s.columns,function(index,colIdx){self.updateSelectAll(colIdx);});},onDataTablesXhr:function(){var self=this;var dt=self.s.dt;var ctx=self.s.ctx;var $table=$(dt.table().node());$.each(self.s.columns,function(index,colIdx){self.s.data[colIdx]={};self.s.dataDisabled[colIdx]={};});if(ctx.oFeatures.bStateSave){self.loadState();$table.one("draw.dt.dtCheckboxes",function(){self.updateState();});}},updateData:function(cells,colIdx,isSelected){var self=this;var dt=self.s.dt;var ctx=self.s.ctx;if(ctx.aoColumns[colIdx].checkboxes){var cellsData=cells.data();cellsData.each(function(cellData){if(isSelected){ctx.checkboxes.s.data[colIdx][cellData]=1;}else{delete ctx.checkboxes.s.data[colIdx][cellData];}});if(ctx.oFeatures.bStateSave){if(ctx.aoColumns[colIdx].checkboxes.stateSave){dt.state.save();}}}},updateSelect:function(selector,isSelected){var self=this;var dt=self.s.dt;var ctx=self.s.ctx;if(ctx._select){self.s.ignoreSelect=true;if(isSelected){dt.rows(selector).select();}else{dt.rows(selector).deselect();}self.s.ignoreSelect=false;}},updateCheckbox:function(cells,colIdx,isSelected){var self=this;var ctx=self.s.ctx;var cellNodes=cells.nodes();if(cellNodes.length){$("input.dt-checkboxes",cellNodes).not(":disabled").prop("checked",isSelected);if($.isFunction(ctx.aoColumns[colIdx].checkboxes.selectCallback)){ctx.aoColumns[colIdx].checkboxes.selectCallback(cellNodes,isSelected);}}},updateState:function(){var self=this;self.updateStateCheckboxes({page:"all",search:"none"});$.each(self.s.columns,function(index,colIdx){self.updateSelectAll(colIdx);});},updateStateCheckboxes:function(opts){var self=this;var dt=self.s.dt;var ctx=self.s.ctx;dt.cells("tr",self.s.columns,opts).every(function(rowIdx,colIdx){var cellData=this.data();var isCellSelectable=self.isCellSelectable(colIdx,cellData);if(ctx.checkboxes.s.data[colIdx].hasOwnProperty(cellData)){self.updateCheckbox(this,colIdx,true);if(ctx.aoColumns[colIdx].checkboxes.selectRow&&isCellSelectable){self.updateSelect(rowIdx,true);}}if(!isCellSelectable){$("input.dt-checkboxes",this.node()).prop("disabled",true);}});},onClick:function(e,ctrl){var self=this;var dt=self.s.dt;var ctx=self.s.ctx;var cellSelector;var $cell=$(ctrl).closest("td");if($cell.parents(".DTFC_Cloned").length){cellSelector=dt.fixedColumns().cellIndex($cell);}else{cellSelector=$cell;}var cell=dt.cell(cellSelector);var cellIdx=cell.index();var colIdx=cellIdx.column;if(!ctx.aoColumns[colIdx].checkboxes.selectRow){cell.checkboxes.select(ctrl.checked);e.stopPropagation();}else{setTimeout(function(){var cellData=cell.data();var hasData=self.s.data[colIdx].hasOwnProperty(cellData);if(hasData!==ctrl.checked){self.updateCheckbox(cell,colIdx,hasData);self.updateSelectAll(colIdx);}},0);}},onClickSelectAll:function(e,ctrl){var self=this;var dt=self.s.dt;var ctx=self.s.ctx;var colIdx=null;var $th=$(ctrl).closest("th");if($th.parents(".DTFC_Cloned").length){var cellIdx=dt.fixedColumns().cellIndex($th);colIdx=cellIdx.column;}else{colIdx=dt.column($th).index();}$(ctrl).data("is-changed",true);dt.column(colIdx,{page:((ctx.aoColumns[colIdx].checkboxes&&ctx.aoColumns[colIdx].checkboxes.selectAllPages)?"all":"current"),search:"applied"}).checkboxes.select(ctrl.checked);e.stopPropagation();},loadState:function(){var self=this;var dt=self.s.dt;var ctx=self.s.ctx;if(ctx.oFeatures.bStateSave){var state=dt.state.loaded();$.each(self.s.columns,function(index,colIdx){if(state&&state.checkboxes&&state.checkboxes.hasOwnProperty(colIdx)){if(ctx.aoColumns[colIdx].checkboxes.stateSave){self.s.data[colIdx]=state.checkboxes[colIdx];}}});}},updateSelectAll:function(colIdx){var self=this;var dt=self.s.dt;var ctx=self.s.ctx;if(ctx.aoColumns[colIdx].checkboxes&&ctx.aoColumns[colIdx].checkboxes.selectAll){var cells=dt.cells("tr",colIdx,{page:((ctx.aoColumns[colIdx].checkboxes.selectAllPages)?"all":"current"),search:"applied"});var $tableContainer=dt.table().container();var $checkboxesSelectAll=$('.dt-checkboxes-select-all[data-col="'+colIdx+'"] input[type="checkbox"]',$tableContainer);var countChecked=0;var countDisabled=0;var cellsData=cells.data();$.each(cellsData,function(index,cellData){if(self.isCellSelectable(colIdx,cellData)){if(self.s.data[colIdx].hasOwnProperty(cellData)){countChecked++;}}else{countDisabled++;}});if(ctx._fixedHeader){if(ctx._fixedHeader.dom["header"].floating){$checkboxesSelectAll=$('.fixedHeader-floating .dt-checkboxes-select-all[data-col="'+colIdx+'"] input[type="checkbox"]');}}var isSelected;var isIndeterminate;if(countChecked===0){isSelected=false;isIndeterminate=false;}else{if((countChecked+countDisabled)===cellsData.length){isSelected=true;isIndeterminate=false;}else{isSelected=true;isIndeterminate=true;}}var isChanged=$checkboxesSelectAll.data("is-changed");var isSelectedNow=$checkboxesSelectAll.prop("checked");var isIndeterminateNow=$checkboxesSelectAll.prop("indeterminate");if(isChanged||isSelectedNow!==isSelected||isIndeterminateNow!==isIndeterminate){$checkboxesSelectAll.data("is-changed",false);$checkboxesSelectAll.prop({"checked":isSelected,"indeterminate":isIndeterminate});if($.isFunction(ctx.aoColumns[colIdx].checkboxes.selectAllCallback)){ctx.aoColumns[colIdx].checkboxes.selectAllCallback($checkboxesSelectAll.closest("th").get(0),isSelected,isIndeterminate);}}}},showInfoSelected:function(){var self=this;var dt=self.s.dt;var ctx=self.s.ctx;if(!ctx.aanFeatures.i){return;}var colIdx=self.getSelectRowColIndex();if(colIdx!==null){var countRows=0;for(var cellData in ctx.checkboxes.s.data[colIdx]){if(ctx.checkboxes.s.data[colIdx].hasOwnProperty(cellData)){countRows++;}}var add=function($el,name,num){$el.append($('<span class="select-item"/>').append(dt.i18n("select."+name+"s",{_:"%d "+name+"s selected",0:"",1:"1 "+name+" selected"},num)));};$.each(ctx.aanFeatures.i,function(i,el){var $el=$(el);var $output=$('<span class="select-info"/>');add($output,"row",countRows);var $existing=$el.children("span.select-info");if($existing.length){$existing.remove();}if($output.text()!==""){$el.append($output);}});}},isCellSelectable:function(colIdx,cellData){var self=this;var ctx=self.s.ctx;if(ctx.checkboxes.s.dataDisabled[colIdx].hasOwnProperty(cellData)){return false;}else{return true;}},getCellIndex:function(cell){var self=this;var dt=self.s.dt;var ctx=self.s.ctx;if(ctx._oFixedColumns){return dt.fixedColumns().cellIndex(cell);}else{return dt.cell(cell).index();}},getSelectRowColIndex:function(){var self=this;var ctx=self.s.ctx;var colIdx=null;for(var i=0;i<ctx.aoColumns.length;i++){if(ctx.aoColumns[i].checkboxes&&ctx.aoColumns[i].checkboxes.selectRow){colIdx=i;break;}}return colIdx;},updateFixedColumn:function(colIdx){var self=this;var dt=self.s.dt;var ctx=self.s.ctx;if(ctx._oFixedColumns){var leftCols=ctx._oFixedColumns.s.iLeftColumns;var rightCols=ctx.aoColumns.length-ctx._oFixedColumns.s.iRightColumns-1;if(colIdx<leftCols||colIdx>rightCols){dt.fixedColumns().update();}}}};Checkboxes.defaults={stateSave:true,selectRow:false,selectAll:true,selectAllPages:true,selectCallback:null,selectAllCallback:null,selectAllRender:'<input type="checkbox">'};var Api=$.fn.dataTable.Api;Api.register("checkboxes()",function(){return this;});Api.registerPlural("columns().checkboxes.select()","column().checkboxes.select()",function(state){if(typeof state==="undefined"){state=true;}return this.iterator("column-rows",function(ctx,colIdx,i,j,rowsIdx){if(ctx.aoColumns[colIdx].checkboxes){var selector=[];$.each(rowsIdx,function(index,rowIdx){selector.push({row:rowIdx,column:colIdx});});var cells=this.cells(selector);var cellsData=cells.data();var rowsSelectableIdx=[];selector=[];$.each(cellsData,function(index,cellData){if(ctx.checkboxes.isCellSelectable(colIdx,cellData)){selector.push({row:rowsIdx[index],column:colIdx});rowsSelectableIdx.push(rowsIdx[index]);}});cells=this.cells(selector);ctx.checkboxes.updateData(cells,colIdx,state);ctx.checkboxes.updateCheckbox(cells,colIdx,state);if(ctx.aoColumns[colIdx].checkboxes.selectRow){ctx.checkboxes.updateSelect(rowsSelectableIdx,state);}if(ctx._oFixedColumns){setTimeout(function(){ctx.checkboxes.updateSelectAll(colIdx);},0);}else{ctx.checkboxes.updateSelectAll(colIdx);}ctx.checkboxes.updateFixedColumn(colIdx);}},1);});Api.registerPlural("cells().checkboxes.select()","cell().checkboxes.select()",function(state){if(typeof state==="undefined"){state=true;}return this.iterator("cell",function(ctx,rowIdx,colIdx){if(ctx.aoColumns[colIdx].checkboxes){var cells=this.cells([{row:rowIdx,column:colIdx}]);var cellData=this.cell({row:rowIdx,column:colIdx}).data();if(ctx.checkboxes.isCellSelectable(colIdx,cellData)){ctx.checkboxes.updateData(cells,colIdx,state);ctx.checkboxes.updateCheckbox(cells,colIdx,state);if(ctx.aoColumns[colIdx].checkboxes.selectRow){ctx.checkboxes.updateSelect(rowIdx,state);}if(ctx._oFixedColumns){setTimeout(function(){ctx.checkboxes.updateSelectAll(colIdx);},0);}else{ctx.checkboxes.updateSelectAll(colIdx);}ctx.checkboxes.updateFixedColumn(colIdx);}}},1);});Api.registerPlural("cells().checkboxes.enable()","cell().checkboxes.enable()",function(state){if(typeof state==="undefined"){state=true;}return this.iterator("cell",function(ctx,rowIdx,colIdx){if(ctx.aoColumns[colIdx].checkboxes){var cell=this.cell({row:rowIdx,column:colIdx});var cellData=cell.data();if(state){delete ctx.checkboxes.s.dataDisabled[colIdx][cellData];}else{ctx.checkboxes.s.dataDisabled[colIdx][cellData]=1;}var cellNode=cell.node();if(cellNode){$("input.dt-checkboxes",cellNode).prop("disabled",!state);}if(ctx.aoColumns[colIdx].checkboxes.selectRow){if(ctx.checkboxes.s.data[colIdx].hasOwnProperty(cellData)){ctx.checkboxes.updateSelect(rowIdx,state);}}}},1);});Api.registerPlural("cells().checkboxes.disable()","cell().checkboxes.disable()",function(state){if(typeof state==="undefined"){state=true;}return this.checkboxes.enable(!state);});Api.registerPlural("columns().checkboxes.deselect()","column().checkboxes.deselect()",function(state){if(typeof state==="undefined"){state=true;}return this.checkboxes.select(!state);});Api.registerPlural("cells().checkboxes.deselect()","cell().checkboxes.deselect()",function(state){if(typeof state==="undefined"){state=true;}return this.checkboxes.select(!state);});Api.registerPlural("columns().checkboxes.deselectAll()","column().checkboxes.deselectAll()",function(){return this.iterator("column",function(ctx,colIdx){if(ctx.aoColumns[colIdx].checkboxes){ctx.checkboxes.s.data[colIdx]={};this.column(colIdx).checkboxes.select(false);}},1);});Api.registerPlural("columns().checkboxes.selected()","column().checkboxes.selected()",function(){return this.iterator("column-rows",function(ctx,colIdx,i,j,rowsIdx){if(ctx.aoColumns[colIdx].checkboxes){var data=[];if(ctx.oFeatures.bServerSide){$.each(ctx.checkboxes.s.data[colIdx],function(cellData){if(ctx.checkboxes.isCellSelectable(colIdx,cellData)){data.push(cellData);}});}else{var selector=[];$.each(rowsIdx,function(index,rowIdx){selector.push({row:rowIdx,column:colIdx});});var cells=this.cells(selector);var cellsData=cells.data();$.each(cellsData,function(index,cellData){if(ctx.checkboxes.s.data[colIdx].hasOwnProperty(cellData)){if(ctx.checkboxes.isCellSelectable(colIdx,cellData)){data.push(cellData);}}});}return data;}else{return[];}},1);});Checkboxes.version="1.2.11";$.fn.DataTable.Checkboxes=Checkboxes;$.fn.dataTable.Checkboxes=Checkboxes;$(document).on("preInit.dt.dtCheckboxes",function(e,settings){if(e.namespace!=="dt"){return;}new Checkboxes(settings);});return Checkboxes;}));

/***/ }),

/***/ "./node_modules/popper.js/dist/umd/popper.js":
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(global) {/**!
 * @fileOverview Kickass library to create and place poppers near their reference elements.
 * @version 1.16.1
 * @license
 * Copyright (c) 2016 Federico Zivolo and contributors
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
(function (global, factory) {
	 true ? module.exports = factory() :
	typeof define === 'function' && define.amd ? define(factory) :
	(global.Popper = factory());
}(this, (function () { 'use strict';

var isBrowser = typeof window !== 'undefined' && typeof document !== 'undefined' && typeof navigator !== 'undefined';

var timeoutDuration = function () {
  var longerTimeoutBrowsers = ['Edge', 'Trident', 'Firefox'];
  for (var i = 0; i < longerTimeoutBrowsers.length; i += 1) {
    if (isBrowser && navigator.userAgent.indexOf(longerTimeoutBrowsers[i]) >= 0) {
      return 1;
    }
  }
  return 0;
}();

function microtaskDebounce(fn) {
  var called = false;
  return function () {
    if (called) {
      return;
    }
    called = true;
    window.Promise.resolve().then(function () {
      called = false;
      fn();
    });
  };
}

function taskDebounce(fn) {
  var scheduled = false;
  return function () {
    if (!scheduled) {
      scheduled = true;
      setTimeout(function () {
        scheduled = false;
        fn();
      }, timeoutDuration);
    }
  };
}

var supportsMicroTasks = isBrowser && window.Promise;

/**
* Create a debounced version of a method, that's asynchronously deferred
* but called in the minimum time possible.
*
* @method
* @memberof Popper.Utils
* @argument {Function} fn
* @returns {Function}
*/
var debounce = supportsMicroTasks ? microtaskDebounce : taskDebounce;

/**
 * Check if the given variable is a function
 * @method
 * @memberof Popper.Utils
 * @argument {Any} functionToCheck - variable to check
 * @returns {Boolean} answer to: is a function?
 */
function isFunction(functionToCheck) {
  var getType = {};
  return functionToCheck && getType.toString.call(functionToCheck) === '[object Function]';
}

/**
 * Get CSS computed property of the given element
 * @method
 * @memberof Popper.Utils
 * @argument {Eement} element
 * @argument {String} property
 */
function getStyleComputedProperty(element, property) {
  if (element.nodeType !== 1) {
    return [];
  }
  // NOTE: 1 DOM access here
  var window = element.ownerDocument.defaultView;
  var css = window.getComputedStyle(element, null);
  return property ? css[property] : css;
}

/**
 * Returns the parentNode or the host of the element
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element
 * @returns {Element} parent
 */
function getParentNode(element) {
  if (element.nodeName === 'HTML') {
    return element;
  }
  return element.parentNode || element.host;
}

/**
 * Returns the scrolling parent of the given element
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element
 * @returns {Element} scroll parent
 */
function getScrollParent(element) {
  // Return body, `getScroll` will take care to get the correct `scrollTop` from it
  if (!element) {
    return document.body;
  }

  switch (element.nodeName) {
    case 'HTML':
    case 'BODY':
      return element.ownerDocument.body;
    case '#document':
      return element.body;
  }

  // Firefox want us to check `-x` and `-y` variations as well

  var _getStyleComputedProp = getStyleComputedProperty(element),
      overflow = _getStyleComputedProp.overflow,
      overflowX = _getStyleComputedProp.overflowX,
      overflowY = _getStyleComputedProp.overflowY;

  if (/(auto|scroll|overlay)/.test(overflow + overflowY + overflowX)) {
    return element;
  }

  return getScrollParent(getParentNode(element));
}

/**
 * Returns the reference node of the reference object, or the reference object itself.
 * @method
 * @memberof Popper.Utils
 * @param {Element|Object} reference - the reference element (the popper will be relative to this)
 * @returns {Element} parent
 */
function getReferenceNode(reference) {
  return reference && reference.referenceNode ? reference.referenceNode : reference;
}

var isIE11 = isBrowser && !!(window.MSInputMethodContext && document.documentMode);
var isIE10 = isBrowser && /MSIE 10/.test(navigator.userAgent);

/**
 * Determines if the browser is Internet Explorer
 * @method
 * @memberof Popper.Utils
 * @param {Number} version to check
 * @returns {Boolean} isIE
 */
function isIE(version) {
  if (version === 11) {
    return isIE11;
  }
  if (version === 10) {
    return isIE10;
  }
  return isIE11 || isIE10;
}

/**
 * Returns the offset parent of the given element
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element
 * @returns {Element} offset parent
 */
function getOffsetParent(element) {
  if (!element) {
    return document.documentElement;
  }

  var noOffsetParent = isIE(10) ? document.body : null;

  // NOTE: 1 DOM access here
  var offsetParent = element.offsetParent || null;
  // Skip hidden elements which don't have an offsetParent
  while (offsetParent === noOffsetParent && element.nextElementSibling) {
    offsetParent = (element = element.nextElementSibling).offsetParent;
  }

  var nodeName = offsetParent && offsetParent.nodeName;

  if (!nodeName || nodeName === 'BODY' || nodeName === 'HTML') {
    return element ? element.ownerDocument.documentElement : document.documentElement;
  }

  // .offsetParent will return the closest TH, TD or TABLE in case
  // no offsetParent is present, I hate this job...
  if (['TH', 'TD', 'TABLE'].indexOf(offsetParent.nodeName) !== -1 && getStyleComputedProperty(offsetParent, 'position') === 'static') {
    return getOffsetParent(offsetParent);
  }

  return offsetParent;
}

function isOffsetContainer(element) {
  var nodeName = element.nodeName;

  if (nodeName === 'BODY') {
    return false;
  }
  return nodeName === 'HTML' || getOffsetParent(element.firstElementChild) === element;
}

/**
 * Finds the root node (document, shadowDOM root) of the given element
 * @method
 * @memberof Popper.Utils
 * @argument {Element} node
 * @returns {Element} root node
 */
function getRoot(node) {
  if (node.parentNode !== null) {
    return getRoot(node.parentNode);
  }

  return node;
}

/**
 * Finds the offset parent common to the two provided nodes
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element1
 * @argument {Element} element2
 * @returns {Element} common offset parent
 */
function findCommonOffsetParent(element1, element2) {
  // This check is needed to avoid errors in case one of the elements isn't defined for any reason
  if (!element1 || !element1.nodeType || !element2 || !element2.nodeType) {
    return document.documentElement;
  }

  // Here we make sure to give as "start" the element that comes first in the DOM
  var order = element1.compareDocumentPosition(element2) & Node.DOCUMENT_POSITION_FOLLOWING;
  var start = order ? element1 : element2;
  var end = order ? element2 : element1;

  // Get common ancestor container
  var range = document.createRange();
  range.setStart(start, 0);
  range.setEnd(end, 0);
  var commonAncestorContainer = range.commonAncestorContainer;

  // Both nodes are inside #document

  if (element1 !== commonAncestorContainer && element2 !== commonAncestorContainer || start.contains(end)) {
    if (isOffsetContainer(commonAncestorContainer)) {
      return commonAncestorContainer;
    }

    return getOffsetParent(commonAncestorContainer);
  }

  // one of the nodes is inside shadowDOM, find which one
  var element1root = getRoot(element1);
  if (element1root.host) {
    return findCommonOffsetParent(element1root.host, element2);
  } else {
    return findCommonOffsetParent(element1, getRoot(element2).host);
  }
}

/**
 * Gets the scroll value of the given element in the given side (top and left)
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element
 * @argument {String} side `top` or `left`
 * @returns {number} amount of scrolled pixels
 */
function getScroll(element) {
  var side = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'top';

  var upperSide = side === 'top' ? 'scrollTop' : 'scrollLeft';
  var nodeName = element.nodeName;

  if (nodeName === 'BODY' || nodeName === 'HTML') {
    var html = element.ownerDocument.documentElement;
    var scrollingElement = element.ownerDocument.scrollingElement || html;
    return scrollingElement[upperSide];
  }

  return element[upperSide];
}

/*
 * Sum or subtract the element scroll values (left and top) from a given rect object
 * @method
 * @memberof Popper.Utils
 * @param {Object} rect - Rect object you want to change
 * @param {HTMLElement} element - The element from the function reads the scroll values
 * @param {Boolean} subtract - set to true if you want to subtract the scroll values
 * @return {Object} rect - The modifier rect object
 */
function includeScroll(rect, element) {
  var subtract = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;

  var scrollTop = getScroll(element, 'top');
  var scrollLeft = getScroll(element, 'left');
  var modifier = subtract ? -1 : 1;
  rect.top += scrollTop * modifier;
  rect.bottom += scrollTop * modifier;
  rect.left += scrollLeft * modifier;
  rect.right += scrollLeft * modifier;
  return rect;
}

/*
 * Helper to detect borders of a given element
 * @method
 * @memberof Popper.Utils
 * @param {CSSStyleDeclaration} styles
 * Result of `getStyleComputedProperty` on the given element
 * @param {String} axis - `x` or `y`
 * @return {number} borders - The borders size of the given axis
 */

function getBordersSize(styles, axis) {
  var sideA = axis === 'x' ? 'Left' : 'Top';
  var sideB = sideA === 'Left' ? 'Right' : 'Bottom';

  return parseFloat(styles['border' + sideA + 'Width']) + parseFloat(styles['border' + sideB + 'Width']);
}

function getSize(axis, body, html, computedStyle) {
  return Math.max(body['offset' + axis], body['scroll' + axis], html['client' + axis], html['offset' + axis], html['scroll' + axis], isIE(10) ? parseInt(html['offset' + axis]) + parseInt(computedStyle['margin' + (axis === 'Height' ? 'Top' : 'Left')]) + parseInt(computedStyle['margin' + (axis === 'Height' ? 'Bottom' : 'Right')]) : 0);
}

function getWindowSizes(document) {
  var body = document.body;
  var html = document.documentElement;
  var computedStyle = isIE(10) && getComputedStyle(html);

  return {
    height: getSize('Height', body, html, computedStyle),
    width: getSize('Width', body, html, computedStyle)
  };
}

var classCallCheck = function (instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
};

var createClass = function () {
  function defineProperties(target, props) {
    for (var i = 0; i < props.length; i++) {
      var descriptor = props[i];
      descriptor.enumerable = descriptor.enumerable || false;
      descriptor.configurable = true;
      if ("value" in descriptor) descriptor.writable = true;
      Object.defineProperty(target, descriptor.key, descriptor);
    }
  }

  return function (Constructor, protoProps, staticProps) {
    if (protoProps) defineProperties(Constructor.prototype, protoProps);
    if (staticProps) defineProperties(Constructor, staticProps);
    return Constructor;
  };
}();





var defineProperty = function (obj, key, value) {
  if (key in obj) {
    Object.defineProperty(obj, key, {
      value: value,
      enumerable: true,
      configurable: true,
      writable: true
    });
  } else {
    obj[key] = value;
  }

  return obj;
};

var _extends = Object.assign || function (target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i];

    for (var key in source) {
      if (Object.prototype.hasOwnProperty.call(source, key)) {
        target[key] = source[key];
      }
    }
  }

  return target;
};

/**
 * Given element offsets, generate an output similar to getBoundingClientRect
 * @method
 * @memberof Popper.Utils
 * @argument {Object} offsets
 * @returns {Object} ClientRect like output
 */
function getClientRect(offsets) {
  return _extends({}, offsets, {
    right: offsets.left + offsets.width,
    bottom: offsets.top + offsets.height
  });
}

/**
 * Get bounding client rect of given element
 * @method
 * @memberof Popper.Utils
 * @param {HTMLElement} element
 * @return {Object} client rect
 */
function getBoundingClientRect(element) {
  var rect = {};

  // IE10 10 FIX: Please, don't ask, the element isn't
  // considered in DOM in some circumstances...
  // This isn't reproducible in IE10 compatibility mode of IE11
  try {
    if (isIE(10)) {
      rect = element.getBoundingClientRect();
      var scrollTop = getScroll(element, 'top');
      var scrollLeft = getScroll(element, 'left');
      rect.top += scrollTop;
      rect.left += scrollLeft;
      rect.bottom += scrollTop;
      rect.right += scrollLeft;
    } else {
      rect = element.getBoundingClientRect();
    }
  } catch (e) {}

  var result = {
    left: rect.left,
    top: rect.top,
    width: rect.right - rect.left,
    height: rect.bottom - rect.top
  };

  // subtract scrollbar size from sizes
  var sizes = element.nodeName === 'HTML' ? getWindowSizes(element.ownerDocument) : {};
  var width = sizes.width || element.clientWidth || result.width;
  var height = sizes.height || element.clientHeight || result.height;

  var horizScrollbar = element.offsetWidth - width;
  var vertScrollbar = element.offsetHeight - height;

  // if an hypothetical scrollbar is detected, we must be sure it's not a `border`
  // we make this check conditional for performance reasons
  if (horizScrollbar || vertScrollbar) {
    var styles = getStyleComputedProperty(element);
    horizScrollbar -= getBordersSize(styles, 'x');
    vertScrollbar -= getBordersSize(styles, 'y');

    result.width -= horizScrollbar;
    result.height -= vertScrollbar;
  }

  return getClientRect(result);
}

function getOffsetRectRelativeToArbitraryNode(children, parent) {
  var fixedPosition = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;

  var isIE10 = isIE(10);
  var isHTML = parent.nodeName === 'HTML';
  var childrenRect = getBoundingClientRect(children);
  var parentRect = getBoundingClientRect(parent);
  var scrollParent = getScrollParent(children);

  var styles = getStyleComputedProperty(parent);
  var borderTopWidth = parseFloat(styles.borderTopWidth);
  var borderLeftWidth = parseFloat(styles.borderLeftWidth);

  // In cases where the parent is fixed, we must ignore negative scroll in offset calc
  if (fixedPosition && isHTML) {
    parentRect.top = Math.max(parentRect.top, 0);
    parentRect.left = Math.max(parentRect.left, 0);
  }
  var offsets = getClientRect({
    top: childrenRect.top - parentRect.top - borderTopWidth,
    left: childrenRect.left - parentRect.left - borderLeftWidth,
    width: childrenRect.width,
    height: childrenRect.height
  });
  offsets.marginTop = 0;
  offsets.marginLeft = 0;

  // Subtract margins of documentElement in case it's being used as parent
  // we do this only on HTML because it's the only element that behaves
  // differently when margins are applied to it. The margins are included in
  // the box of the documentElement, in the other cases not.
  if (!isIE10 && isHTML) {
    var marginTop = parseFloat(styles.marginTop);
    var marginLeft = parseFloat(styles.marginLeft);

    offsets.top -= borderTopWidth - marginTop;
    offsets.bottom -= borderTopWidth - marginTop;
    offsets.left -= borderLeftWidth - marginLeft;
    offsets.right -= borderLeftWidth - marginLeft;

    // Attach marginTop and marginLeft because in some circumstances we may need them
    offsets.marginTop = marginTop;
    offsets.marginLeft = marginLeft;
  }

  if (isIE10 && !fixedPosition ? parent.contains(scrollParent) : parent === scrollParent && scrollParent.nodeName !== 'BODY') {
    offsets = includeScroll(offsets, parent);
  }

  return offsets;
}

function getViewportOffsetRectRelativeToArtbitraryNode(element) {
  var excludeScroll = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;

  var html = element.ownerDocument.documentElement;
  var relativeOffset = getOffsetRectRelativeToArbitraryNode(element, html);
  var width = Math.max(html.clientWidth, window.innerWidth || 0);
  var height = Math.max(html.clientHeight, window.innerHeight || 0);

  var scrollTop = !excludeScroll ? getScroll(html) : 0;
  var scrollLeft = !excludeScroll ? getScroll(html, 'left') : 0;

  var offset = {
    top: scrollTop - relativeOffset.top + relativeOffset.marginTop,
    left: scrollLeft - relativeOffset.left + relativeOffset.marginLeft,
    width: width,
    height: height
  };

  return getClientRect(offset);
}

/**
 * Check if the given element is fixed or is inside a fixed parent
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element
 * @argument {Element} customContainer
 * @returns {Boolean} answer to "isFixed?"
 */
function isFixed(element) {
  var nodeName = element.nodeName;
  if (nodeName === 'BODY' || nodeName === 'HTML') {
    return false;
  }
  if (getStyleComputedProperty(element, 'position') === 'fixed') {
    return true;
  }
  var parentNode = getParentNode(element);
  if (!parentNode) {
    return false;
  }
  return isFixed(parentNode);
}

/**
 * Finds the first parent of an element that has a transformed property defined
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element
 * @returns {Element} first transformed parent or documentElement
 */

function getFixedPositionOffsetParent(element) {
  // This check is needed to avoid errors in case one of the elements isn't defined for any reason
  if (!element || !element.parentElement || isIE()) {
    return document.documentElement;
  }
  var el = element.parentElement;
  while (el && getStyleComputedProperty(el, 'transform') === 'none') {
    el = el.parentElement;
  }
  return el || document.documentElement;
}

/**
 * Computed the boundaries limits and return them
 * @method
 * @memberof Popper.Utils
 * @param {HTMLElement} popper
 * @param {HTMLElement} reference
 * @param {number} padding
 * @param {HTMLElement} boundariesElement - Element used to define the boundaries
 * @param {Boolean} fixedPosition - Is in fixed position mode
 * @returns {Object} Coordinates of the boundaries
 */
function getBoundaries(popper, reference, padding, boundariesElement) {
  var fixedPosition = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : false;

  // NOTE: 1 DOM access here

  var boundaries = { top: 0, left: 0 };
  var offsetParent = fixedPosition ? getFixedPositionOffsetParent(popper) : findCommonOffsetParent(popper, getReferenceNode(reference));

  // Handle viewport case
  if (boundariesElement === 'viewport') {
    boundaries = getViewportOffsetRectRelativeToArtbitraryNode(offsetParent, fixedPosition);
  } else {
    // Handle other cases based on DOM element used as boundaries
    var boundariesNode = void 0;
    if (boundariesElement === 'scrollParent') {
      boundariesNode = getScrollParent(getParentNode(reference));
      if (boundariesNode.nodeName === 'BODY') {
        boundariesNode = popper.ownerDocument.documentElement;
      }
    } else if (boundariesElement === 'window') {
      boundariesNode = popper.ownerDocument.documentElement;
    } else {
      boundariesNode = boundariesElement;
    }

    var offsets = getOffsetRectRelativeToArbitraryNode(boundariesNode, offsetParent, fixedPosition);

    // In case of HTML, we need a different computation
    if (boundariesNode.nodeName === 'HTML' && !isFixed(offsetParent)) {
      var _getWindowSizes = getWindowSizes(popper.ownerDocument),
          height = _getWindowSizes.height,
          width = _getWindowSizes.width;

      boundaries.top += offsets.top - offsets.marginTop;
      boundaries.bottom = height + offsets.top;
      boundaries.left += offsets.left - offsets.marginLeft;
      boundaries.right = width + offsets.left;
    } else {
      // for all the other DOM elements, this one is good
      boundaries = offsets;
    }
  }

  // Add paddings
  padding = padding || 0;
  var isPaddingNumber = typeof padding === 'number';
  boundaries.left += isPaddingNumber ? padding : padding.left || 0;
  boundaries.top += isPaddingNumber ? padding : padding.top || 0;
  boundaries.right -= isPaddingNumber ? padding : padding.right || 0;
  boundaries.bottom -= isPaddingNumber ? padding : padding.bottom || 0;

  return boundaries;
}

function getArea(_ref) {
  var width = _ref.width,
      height = _ref.height;

  return width * height;
}

/**
 * Utility used to transform the `auto` placement to the placement with more
 * available space.
 * @method
 * @memberof Popper.Utils
 * @argument {Object} data - The data object generated by update method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */
function computeAutoPlacement(placement, refRect, popper, reference, boundariesElement) {
  var padding = arguments.length > 5 && arguments[5] !== undefined ? arguments[5] : 0;

  if (placement.indexOf('auto') === -1) {
    return placement;
  }

  var boundaries = getBoundaries(popper, reference, padding, boundariesElement);

  var rects = {
    top: {
      width: boundaries.width,
      height: refRect.top - boundaries.top
    },
    right: {
      width: boundaries.right - refRect.right,
      height: boundaries.height
    },
    bottom: {
      width: boundaries.width,
      height: boundaries.bottom - refRect.bottom
    },
    left: {
      width: refRect.left - boundaries.left,
      height: boundaries.height
    }
  };

  var sortedAreas = Object.keys(rects).map(function (key) {
    return _extends({
      key: key
    }, rects[key], {
      area: getArea(rects[key])
    });
  }).sort(function (a, b) {
    return b.area - a.area;
  });

  var filteredAreas = sortedAreas.filter(function (_ref2) {
    var width = _ref2.width,
        height = _ref2.height;
    return width >= popper.clientWidth && height >= popper.clientHeight;
  });

  var computedPlacement = filteredAreas.length > 0 ? filteredAreas[0].key : sortedAreas[0].key;

  var variation = placement.split('-')[1];

  return computedPlacement + (variation ? '-' + variation : '');
}

/**
 * Get offsets to the reference element
 * @method
 * @memberof Popper.Utils
 * @param {Object} state
 * @param {Element} popper - the popper element
 * @param {Element} reference - the reference element (the popper will be relative to this)
 * @param {Element} fixedPosition - is in fixed position mode
 * @returns {Object} An object containing the offsets which will be applied to the popper
 */
function getReferenceOffsets(state, popper, reference) {
  var fixedPosition = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : null;

  var commonOffsetParent = fixedPosition ? getFixedPositionOffsetParent(popper) : findCommonOffsetParent(popper, getReferenceNode(reference));
  return getOffsetRectRelativeToArbitraryNode(reference, commonOffsetParent, fixedPosition);
}

/**
 * Get the outer sizes of the given element (offset size + margins)
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element
 * @returns {Object} object containing width and height properties
 */
function getOuterSizes(element) {
  var window = element.ownerDocument.defaultView;
  var styles = window.getComputedStyle(element);
  var x = parseFloat(styles.marginTop || 0) + parseFloat(styles.marginBottom || 0);
  var y = parseFloat(styles.marginLeft || 0) + parseFloat(styles.marginRight || 0);
  var result = {
    width: element.offsetWidth + y,
    height: element.offsetHeight + x
  };
  return result;
}

/**
 * Get the opposite placement of the given one
 * @method
 * @memberof Popper.Utils
 * @argument {String} placement
 * @returns {String} flipped placement
 */
function getOppositePlacement(placement) {
  var hash = { left: 'right', right: 'left', bottom: 'top', top: 'bottom' };
  return placement.replace(/left|right|bottom|top/g, function (matched) {
    return hash[matched];
  });
}

/**
 * Get offsets to the popper
 * @method
 * @memberof Popper.Utils
 * @param {Object} position - CSS position the Popper will get applied
 * @param {HTMLElement} popper - the popper element
 * @param {Object} referenceOffsets - the reference offsets (the popper will be relative to this)
 * @param {String} placement - one of the valid placement options
 * @returns {Object} popperOffsets - An object containing the offsets which will be applied to the popper
 */
function getPopperOffsets(popper, referenceOffsets, placement) {
  placement = placement.split('-')[0];

  // Get popper node sizes
  var popperRect = getOuterSizes(popper);

  // Add position, width and height to our offsets object
  var popperOffsets = {
    width: popperRect.width,
    height: popperRect.height
  };

  // depending by the popper placement we have to compute its offsets slightly differently
  var isHoriz = ['right', 'left'].indexOf(placement) !== -1;
  var mainSide = isHoriz ? 'top' : 'left';
  var secondarySide = isHoriz ? 'left' : 'top';
  var measurement = isHoriz ? 'height' : 'width';
  var secondaryMeasurement = !isHoriz ? 'height' : 'width';

  popperOffsets[mainSide] = referenceOffsets[mainSide] + referenceOffsets[measurement] / 2 - popperRect[measurement] / 2;
  if (placement === secondarySide) {
    popperOffsets[secondarySide] = referenceOffsets[secondarySide] - popperRect[secondaryMeasurement];
  } else {
    popperOffsets[secondarySide] = referenceOffsets[getOppositePlacement(secondarySide)];
  }

  return popperOffsets;
}

/**
 * Mimics the `find` method of Array
 * @method
 * @memberof Popper.Utils
 * @argument {Array} arr
 * @argument prop
 * @argument value
 * @returns index or -1
 */
function find(arr, check) {
  // use native find if supported
  if (Array.prototype.find) {
    return arr.find(check);
  }

  // use `filter` to obtain the same behavior of `find`
  return arr.filter(check)[0];
}

/**
 * Return the index of the matching object
 * @method
 * @memberof Popper.Utils
 * @argument {Array} arr
 * @argument prop
 * @argument value
 * @returns index or -1
 */
function findIndex(arr, prop, value) {
  // use native findIndex if supported
  if (Array.prototype.findIndex) {
    return arr.findIndex(function (cur) {
      return cur[prop] === value;
    });
  }

  // use `find` + `indexOf` if `findIndex` isn't supported
  var match = find(arr, function (obj) {
    return obj[prop] === value;
  });
  return arr.indexOf(match);
}

/**
 * Loop trough the list of modifiers and run them in order,
 * each of them will then edit the data object.
 * @method
 * @memberof Popper.Utils
 * @param {dataObject} data
 * @param {Array} modifiers
 * @param {String} ends - Optional modifier name used as stopper
 * @returns {dataObject}
 */
function runModifiers(modifiers, data, ends) {
  var modifiersToRun = ends === undefined ? modifiers : modifiers.slice(0, findIndex(modifiers, 'name', ends));

  modifiersToRun.forEach(function (modifier) {
    if (modifier['function']) {
      // eslint-disable-line dot-notation
      console.warn('`modifier.function` is deprecated, use `modifier.fn`!');
    }
    var fn = modifier['function'] || modifier.fn; // eslint-disable-line dot-notation
    if (modifier.enabled && isFunction(fn)) {
      // Add properties to offsets to make them a complete clientRect object
      // we do this before each modifier to make sure the previous one doesn't
      // mess with these values
      data.offsets.popper = getClientRect(data.offsets.popper);
      data.offsets.reference = getClientRect(data.offsets.reference);

      data = fn(data, modifier);
    }
  });

  return data;
}

/**
 * Updates the position of the popper, computing the new offsets and applying
 * the new style.<br />
 * Prefer `scheduleUpdate` over `update` because of performance reasons.
 * @method
 * @memberof Popper
 */
function update() {
  // if popper is destroyed, don't perform any further update
  if (this.state.isDestroyed) {
    return;
  }

  var data = {
    instance: this,
    styles: {},
    arrowStyles: {},
    attributes: {},
    flipped: false,
    offsets: {}
  };

  // compute reference element offsets
  data.offsets.reference = getReferenceOffsets(this.state, this.popper, this.reference, this.options.positionFixed);

  // compute auto placement, store placement inside the data object,
  // modifiers will be able to edit `placement` if needed
  // and refer to originalPlacement to know the original value
  data.placement = computeAutoPlacement(this.options.placement, data.offsets.reference, this.popper, this.reference, this.options.modifiers.flip.boundariesElement, this.options.modifiers.flip.padding);

  // store the computed placement inside `originalPlacement`
  data.originalPlacement = data.placement;

  data.positionFixed = this.options.positionFixed;

  // compute the popper offsets
  data.offsets.popper = getPopperOffsets(this.popper, data.offsets.reference, data.placement);

  data.offsets.popper.position = this.options.positionFixed ? 'fixed' : 'absolute';

  // run the modifiers
  data = runModifiers(this.modifiers, data);

  // the first `update` will call `onCreate` callback
  // the other ones will call `onUpdate` callback
  if (!this.state.isCreated) {
    this.state.isCreated = true;
    this.options.onCreate(data);
  } else {
    this.options.onUpdate(data);
  }
}

/**
 * Helper used to know if the given modifier is enabled.
 * @method
 * @memberof Popper.Utils
 * @returns {Boolean}
 */
function isModifierEnabled(modifiers, modifierName) {
  return modifiers.some(function (_ref) {
    var name = _ref.name,
        enabled = _ref.enabled;
    return enabled && name === modifierName;
  });
}

/**
 * Get the prefixed supported property name
 * @method
 * @memberof Popper.Utils
 * @argument {String} property (camelCase)
 * @returns {String} prefixed property (camelCase or PascalCase, depending on the vendor prefix)
 */
function getSupportedPropertyName(property) {
  var prefixes = [false, 'ms', 'Webkit', 'Moz', 'O'];
  var upperProp = property.charAt(0).toUpperCase() + property.slice(1);

  for (var i = 0; i < prefixes.length; i++) {
    var prefix = prefixes[i];
    var toCheck = prefix ? '' + prefix + upperProp : property;
    if (typeof document.body.style[toCheck] !== 'undefined') {
      return toCheck;
    }
  }
  return null;
}

/**
 * Destroys the popper.
 * @method
 * @memberof Popper
 */
function destroy() {
  this.state.isDestroyed = true;

  // touch DOM only if `applyStyle` modifier is enabled
  if (isModifierEnabled(this.modifiers, 'applyStyle')) {
    this.popper.removeAttribute('x-placement');
    this.popper.style.position = '';
    this.popper.style.top = '';
    this.popper.style.left = '';
    this.popper.style.right = '';
    this.popper.style.bottom = '';
    this.popper.style.willChange = '';
    this.popper.style[getSupportedPropertyName('transform')] = '';
  }

  this.disableEventListeners();

  // remove the popper if user explicitly asked for the deletion on destroy
  // do not use `remove` because IE11 doesn't support it
  if (this.options.removeOnDestroy) {
    this.popper.parentNode.removeChild(this.popper);
  }
  return this;
}

/**
 * Get the window associated with the element
 * @argument {Element} element
 * @returns {Window}
 */
function getWindow(element) {
  var ownerDocument = element.ownerDocument;
  return ownerDocument ? ownerDocument.defaultView : window;
}

function attachToScrollParents(scrollParent, event, callback, scrollParents) {
  var isBody = scrollParent.nodeName === 'BODY';
  var target = isBody ? scrollParent.ownerDocument.defaultView : scrollParent;
  target.addEventListener(event, callback, { passive: true });

  if (!isBody) {
    attachToScrollParents(getScrollParent(target.parentNode), event, callback, scrollParents);
  }
  scrollParents.push(target);
}

/**
 * Setup needed event listeners used to update the popper position
 * @method
 * @memberof Popper.Utils
 * @private
 */
function setupEventListeners(reference, options, state, updateBound) {
  // Resize event listener on window
  state.updateBound = updateBound;
  getWindow(reference).addEventListener('resize', state.updateBound, { passive: true });

  // Scroll event listener on scroll parents
  var scrollElement = getScrollParent(reference);
  attachToScrollParents(scrollElement, 'scroll', state.updateBound, state.scrollParents);
  state.scrollElement = scrollElement;
  state.eventsEnabled = true;

  return state;
}

/**
 * It will add resize/scroll events and start recalculating
 * position of the popper element when they are triggered.
 * @method
 * @memberof Popper
 */
function enableEventListeners() {
  if (!this.state.eventsEnabled) {
    this.state = setupEventListeners(this.reference, this.options, this.state, this.scheduleUpdate);
  }
}

/**
 * Remove event listeners used to update the popper position
 * @method
 * @memberof Popper.Utils
 * @private
 */
function removeEventListeners(reference, state) {
  // Remove resize event listener on window
  getWindow(reference).removeEventListener('resize', state.updateBound);

  // Remove scroll event listener on scroll parents
  state.scrollParents.forEach(function (target) {
    target.removeEventListener('scroll', state.updateBound);
  });

  // Reset state
  state.updateBound = null;
  state.scrollParents = [];
  state.scrollElement = null;
  state.eventsEnabled = false;
  return state;
}

/**
 * It will remove resize/scroll events and won't recalculate popper position
 * when they are triggered. It also won't trigger `onUpdate` callback anymore,
 * unless you call `update` method manually.
 * @method
 * @memberof Popper
 */
function disableEventListeners() {
  if (this.state.eventsEnabled) {
    cancelAnimationFrame(this.scheduleUpdate);
    this.state = removeEventListeners(this.reference, this.state);
  }
}

/**
 * Tells if a given input is a number
 * @method
 * @memberof Popper.Utils
 * @param {*} input to check
 * @return {Boolean}
 */
function isNumeric(n) {
  return n !== '' && !isNaN(parseFloat(n)) && isFinite(n);
}

/**
 * Set the style to the given popper
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element - Element to apply the style to
 * @argument {Object} styles
 * Object with a list of properties and values which will be applied to the element
 */
function setStyles(element, styles) {
  Object.keys(styles).forEach(function (prop) {
    var unit = '';
    // add unit if the value is numeric and is one of the following
    if (['width', 'height', 'top', 'right', 'bottom', 'left'].indexOf(prop) !== -1 && isNumeric(styles[prop])) {
      unit = 'px';
    }
    element.style[prop] = styles[prop] + unit;
  });
}

/**
 * Set the attributes to the given popper
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element - Element to apply the attributes to
 * @argument {Object} styles
 * Object with a list of properties and values which will be applied to the element
 */
function setAttributes(element, attributes) {
  Object.keys(attributes).forEach(function (prop) {
    var value = attributes[prop];
    if (value !== false) {
      element.setAttribute(prop, attributes[prop]);
    } else {
      element.removeAttribute(prop);
    }
  });
}

/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by `update` method
 * @argument {Object} data.styles - List of style properties - values to apply to popper element
 * @argument {Object} data.attributes - List of attribute properties - values to apply to popper element
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The same data object
 */
function applyStyle(data) {
  // any property present in `data.styles` will be applied to the popper,
  // in this way we can make the 3rd party modifiers add custom styles to it
  // Be aware, modifiers could override the properties defined in the previous
  // lines of this modifier!
  setStyles(data.instance.popper, data.styles);

  // any property present in `data.attributes` will be applied to the popper,
  // they will be set as HTML attributes of the element
  setAttributes(data.instance.popper, data.attributes);

  // if arrowElement is defined and arrowStyles has some properties
  if (data.arrowElement && Object.keys(data.arrowStyles).length) {
    setStyles(data.arrowElement, data.arrowStyles);
  }

  return data;
}

/**
 * Set the x-placement attribute before everything else because it could be used
 * to add margins to the popper margins needs to be calculated to get the
 * correct popper offsets.
 * @method
 * @memberof Popper.modifiers
 * @param {HTMLElement} reference - The reference element used to position the popper
 * @param {HTMLElement} popper - The HTML element used as popper
 * @param {Object} options - Popper.js options
 */
function applyStyleOnLoad(reference, popper, options, modifierOptions, state) {
  // compute reference element offsets
  var referenceOffsets = getReferenceOffsets(state, popper, reference, options.positionFixed);

  // compute auto placement, store placement inside the data object,
  // modifiers will be able to edit `placement` if needed
  // and refer to originalPlacement to know the original value
  var placement = computeAutoPlacement(options.placement, referenceOffsets, popper, reference, options.modifiers.flip.boundariesElement, options.modifiers.flip.padding);

  popper.setAttribute('x-placement', placement);

  // Apply `position` to popper before anything else because
  // without the position applied we can't guarantee correct computations
  setStyles(popper, { position: options.positionFixed ? 'fixed' : 'absolute' });

  return options;
}

/**
 * @function
 * @memberof Popper.Utils
 * @argument {Object} data - The data object generated by `update` method
 * @argument {Boolean} shouldRound - If the offsets should be rounded at all
 * @returns {Object} The popper's position offsets rounded
 *
 * The tale of pixel-perfect positioning. It's still not 100% perfect, but as
 * good as it can be within reason.
 * Discussion here: https://github.com/FezVrasta/popper.js/pull/715
 *
 * Low DPI screens cause a popper to be blurry if not using full pixels (Safari
 * as well on High DPI screens).
 *
 * Firefox prefers no rounding for positioning and does not have blurriness on
 * high DPI screens.
 *
 * Only horizontal placement and left/right values need to be considered.
 */
function getRoundedOffsets(data, shouldRound) {
  var _data$offsets = data.offsets,
      popper = _data$offsets.popper,
      reference = _data$offsets.reference;
  var round = Math.round,
      floor = Math.floor;

  var noRound = function noRound(v) {
    return v;
  };

  var referenceWidth = round(reference.width);
  var popperWidth = round(popper.width);

  var isVertical = ['left', 'right'].indexOf(data.placement) !== -1;
  var isVariation = data.placement.indexOf('-') !== -1;
  var sameWidthParity = referenceWidth % 2 === popperWidth % 2;
  var bothOddWidth = referenceWidth % 2 === 1 && popperWidth % 2 === 1;

  var horizontalToInteger = !shouldRound ? noRound : isVertical || isVariation || sameWidthParity ? round : floor;
  var verticalToInteger = !shouldRound ? noRound : round;

  return {
    left: horizontalToInteger(bothOddWidth && !isVariation && shouldRound ? popper.left - 1 : popper.left),
    top: verticalToInteger(popper.top),
    bottom: verticalToInteger(popper.bottom),
    right: horizontalToInteger(popper.right)
  };
}

var isFirefox = isBrowser && /Firefox/i.test(navigator.userAgent);

/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by `update` method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */
function computeStyle(data, options) {
  var x = options.x,
      y = options.y;
  var popper = data.offsets.popper;

  // Remove this legacy support in Popper.js v2

  var legacyGpuAccelerationOption = find(data.instance.modifiers, function (modifier) {
    return modifier.name === 'applyStyle';
  }).gpuAcceleration;
  if (legacyGpuAccelerationOption !== undefined) {
    console.warn('WARNING: `gpuAcceleration` option moved to `computeStyle` modifier and will not be supported in future versions of Popper.js!');
  }
  var gpuAcceleration = legacyGpuAccelerationOption !== undefined ? legacyGpuAccelerationOption : options.gpuAcceleration;

  var offsetParent = getOffsetParent(data.instance.popper);
  var offsetParentRect = getBoundingClientRect(offsetParent);

  // Styles
  var styles = {
    position: popper.position
  };

  var offsets = getRoundedOffsets(data, window.devicePixelRatio < 2 || !isFirefox);

  var sideA = x === 'bottom' ? 'top' : 'bottom';
  var sideB = y === 'right' ? 'left' : 'right';

  // if gpuAcceleration is set to `true` and transform is supported,
  //  we use `translate3d` to apply the position to the popper we
  // automatically use the supported prefixed version if needed
  var prefixedProperty = getSupportedPropertyName('transform');

  // now, let's make a step back and look at this code closely (wtf?)
  // If the content of the popper grows once it's been positioned, it
  // may happen that the popper gets misplaced because of the new content
  // overflowing its reference element
  // To avoid this problem, we provide two options (x and y), which allow
  // the consumer to define the offset origin.
  // If we position a popper on top of a reference element, we can set
  // `x` to `top` to make the popper grow towards its top instead of
  // its bottom.
  var left = void 0,
      top = void 0;
  if (sideA === 'bottom') {
    // when offsetParent is <html> the positioning is relative to the bottom of the screen (excluding the scrollbar)
    // and not the bottom of the html element
    if (offsetParent.nodeName === 'HTML') {
      top = -offsetParent.clientHeight + offsets.bottom;
    } else {
      top = -offsetParentRect.height + offsets.bottom;
    }
  } else {
    top = offsets.top;
  }
  if (sideB === 'right') {
    if (offsetParent.nodeName === 'HTML') {
      left = -offsetParent.clientWidth + offsets.right;
    } else {
      left = -offsetParentRect.width + offsets.right;
    }
  } else {
    left = offsets.left;
  }
  if (gpuAcceleration && prefixedProperty) {
    styles[prefixedProperty] = 'translate3d(' + left + 'px, ' + top + 'px, 0)';
    styles[sideA] = 0;
    styles[sideB] = 0;
    styles.willChange = 'transform';
  } else {
    // othwerise, we use the standard `top`, `left`, `bottom` and `right` properties
    var invertTop = sideA === 'bottom' ? -1 : 1;
    var invertLeft = sideB === 'right' ? -1 : 1;
    styles[sideA] = top * invertTop;
    styles[sideB] = left * invertLeft;
    styles.willChange = sideA + ', ' + sideB;
  }

  // Attributes
  var attributes = {
    'x-placement': data.placement
  };

  // Update `data` attributes, styles and arrowStyles
  data.attributes = _extends({}, attributes, data.attributes);
  data.styles = _extends({}, styles, data.styles);
  data.arrowStyles = _extends({}, data.offsets.arrow, data.arrowStyles);

  return data;
}

/**
 * Helper used to know if the given modifier depends from another one.<br />
 * It checks if the needed modifier is listed and enabled.
 * @method
 * @memberof Popper.Utils
 * @param {Array} modifiers - list of modifiers
 * @param {String} requestingName - name of requesting modifier
 * @param {String} requestedName - name of requested modifier
 * @returns {Boolean}
 */
function isModifierRequired(modifiers, requestingName, requestedName) {
  var requesting = find(modifiers, function (_ref) {
    var name = _ref.name;
    return name === requestingName;
  });

  var isRequired = !!requesting && modifiers.some(function (modifier) {
    return modifier.name === requestedName && modifier.enabled && modifier.order < requesting.order;
  });

  if (!isRequired) {
    var _requesting = '`' + requestingName + '`';
    var requested = '`' + requestedName + '`';
    console.warn(requested + ' modifier is required by ' + _requesting + ' modifier in order to work, be sure to include it before ' + _requesting + '!');
  }
  return isRequired;
}

/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by update method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */
function arrow(data, options) {
  var _data$offsets$arrow;

  // arrow depends on keepTogether in order to work
  if (!isModifierRequired(data.instance.modifiers, 'arrow', 'keepTogether')) {
    return data;
  }

  var arrowElement = options.element;

  // if arrowElement is a string, suppose it's a CSS selector
  if (typeof arrowElement === 'string') {
    arrowElement = data.instance.popper.querySelector(arrowElement);

    // if arrowElement is not found, don't run the modifier
    if (!arrowElement) {
      return data;
    }
  } else {
    // if the arrowElement isn't a query selector we must check that the
    // provided DOM node is child of its popper node
    if (!data.instance.popper.contains(arrowElement)) {
      console.warn('WARNING: `arrow.element` must be child of its popper element!');
      return data;
    }
  }

  var placement = data.placement.split('-')[0];
  var _data$offsets = data.offsets,
      popper = _data$offsets.popper,
      reference = _data$offsets.reference;

  var isVertical = ['left', 'right'].indexOf(placement) !== -1;

  var len = isVertical ? 'height' : 'width';
  var sideCapitalized = isVertical ? 'Top' : 'Left';
  var side = sideCapitalized.toLowerCase();
  var altSide = isVertical ? 'left' : 'top';
  var opSide = isVertical ? 'bottom' : 'right';
  var arrowElementSize = getOuterSizes(arrowElement)[len];

  //
  // extends keepTogether behavior making sure the popper and its
  // reference have enough pixels in conjunction
  //

  // top/left side
  if (reference[opSide] - arrowElementSize < popper[side]) {
    data.offsets.popper[side] -= popper[side] - (reference[opSide] - arrowElementSize);
  }
  // bottom/right side
  if (reference[side] + arrowElementSize > popper[opSide]) {
    data.offsets.popper[side] += reference[side] + arrowElementSize - popper[opSide];
  }
  data.offsets.popper = getClientRect(data.offsets.popper);

  // compute center of the popper
  var center = reference[side] + reference[len] / 2 - arrowElementSize / 2;

  // Compute the sideValue using the updated popper offsets
  // take popper margin in account because we don't have this info available
  var css = getStyleComputedProperty(data.instance.popper);
  var popperMarginSide = parseFloat(css['margin' + sideCapitalized]);
  var popperBorderSide = parseFloat(css['border' + sideCapitalized + 'Width']);
  var sideValue = center - data.offsets.popper[side] - popperMarginSide - popperBorderSide;

  // prevent arrowElement from being placed not contiguously to its popper
  sideValue = Math.max(Math.min(popper[len] - arrowElementSize, sideValue), 0);

  data.arrowElement = arrowElement;
  data.offsets.arrow = (_data$offsets$arrow = {}, defineProperty(_data$offsets$arrow, side, Math.round(sideValue)), defineProperty(_data$offsets$arrow, altSide, ''), _data$offsets$arrow);

  return data;
}

/**
 * Get the opposite placement variation of the given one
 * @method
 * @memberof Popper.Utils
 * @argument {String} placement variation
 * @returns {String} flipped placement variation
 */
function getOppositeVariation(variation) {
  if (variation === 'end') {
    return 'start';
  } else if (variation === 'start') {
    return 'end';
  }
  return variation;
}

/**
 * List of accepted placements to use as values of the `placement` option.<br />
 * Valid placements are:
 * - `auto`
 * - `top`
 * - `right`
 * - `bottom`
 * - `left`
 *
 * Each placement can have a variation from this list:
 * - `-start`
 * - `-end`
 *
 * Variations are interpreted easily if you think of them as the left to right
 * written languages. Horizontally (`top` and `bottom`), `start` is left and `end`
 * is right.<br />
 * Vertically (`left` and `right`), `start` is top and `end` is bottom.
 *
 * Some valid examples are:
 * - `top-end` (on top of reference, right aligned)
 * - `right-start` (on right of reference, top aligned)
 * - `bottom` (on bottom, centered)
 * - `auto-end` (on the side with more space available, alignment depends by placement)
 *
 * @static
 * @type {Array}
 * @enum {String}
 * @readonly
 * @method placements
 * @memberof Popper
 */
var placements = ['auto-start', 'auto', 'auto-end', 'top-start', 'top', 'top-end', 'right-start', 'right', 'right-end', 'bottom-end', 'bottom', 'bottom-start', 'left-end', 'left', 'left-start'];

// Get rid of `auto` `auto-start` and `auto-end`
var validPlacements = placements.slice(3);

/**
 * Given an initial placement, returns all the subsequent placements
 * clockwise (or counter-clockwise).
 *
 * @method
 * @memberof Popper.Utils
 * @argument {String} placement - A valid placement (it accepts variations)
 * @argument {Boolean} counter - Set to true to walk the placements counterclockwise
 * @returns {Array} placements including their variations
 */
function clockwise(placement) {
  var counter = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;

  var index = validPlacements.indexOf(placement);
  var arr = validPlacements.slice(index + 1).concat(validPlacements.slice(0, index));
  return counter ? arr.reverse() : arr;
}

var BEHAVIORS = {
  FLIP: 'flip',
  CLOCKWISE: 'clockwise',
  COUNTERCLOCKWISE: 'counterclockwise'
};

/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by update method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */
function flip(data, options) {
  // if `inner` modifier is enabled, we can't use the `flip` modifier
  if (isModifierEnabled(data.instance.modifiers, 'inner')) {
    return data;
  }

  if (data.flipped && data.placement === data.originalPlacement) {
    // seems like flip is trying to loop, probably there's not enough space on any of the flippable sides
    return data;
  }

  var boundaries = getBoundaries(data.instance.popper, data.instance.reference, options.padding, options.boundariesElement, data.positionFixed);

  var placement = data.placement.split('-')[0];
  var placementOpposite = getOppositePlacement(placement);
  var variation = data.placement.split('-')[1] || '';

  var flipOrder = [];

  switch (options.behavior) {
    case BEHAVIORS.FLIP:
      flipOrder = [placement, placementOpposite];
      break;
    case BEHAVIORS.CLOCKWISE:
      flipOrder = clockwise(placement);
      break;
    case BEHAVIORS.COUNTERCLOCKWISE:
      flipOrder = clockwise(placement, true);
      break;
    default:
      flipOrder = options.behavior;
  }

  flipOrder.forEach(function (step, index) {
    if (placement !== step || flipOrder.length === index + 1) {
      return data;
    }

    placement = data.placement.split('-')[0];
    placementOpposite = getOppositePlacement(placement);

    var popperOffsets = data.offsets.popper;
    var refOffsets = data.offsets.reference;

    // using floor because the reference offsets may contain decimals we are not going to consider here
    var floor = Math.floor;
    var overlapsRef = placement === 'left' && floor(popperOffsets.right) > floor(refOffsets.left) || placement === 'right' && floor(popperOffsets.left) < floor(refOffsets.right) || placement === 'top' && floor(popperOffsets.bottom) > floor(refOffsets.top) || placement === 'bottom' && floor(popperOffsets.top) < floor(refOffsets.bottom);

    var overflowsLeft = floor(popperOffsets.left) < floor(boundaries.left);
    var overflowsRight = floor(popperOffsets.right) > floor(boundaries.right);
    var overflowsTop = floor(popperOffsets.top) < floor(boundaries.top);
    var overflowsBottom = floor(popperOffsets.bottom) > floor(boundaries.bottom);

    var overflowsBoundaries = placement === 'left' && overflowsLeft || placement === 'right' && overflowsRight || placement === 'top' && overflowsTop || placement === 'bottom' && overflowsBottom;

    // flip the variation if required
    var isVertical = ['top', 'bottom'].indexOf(placement) !== -1;

    // flips variation if reference element overflows boundaries
    var flippedVariationByRef = !!options.flipVariations && (isVertical && variation === 'start' && overflowsLeft || isVertical && variation === 'end' && overflowsRight || !isVertical && variation === 'start' && overflowsTop || !isVertical && variation === 'end' && overflowsBottom);

    // flips variation if popper content overflows boundaries
    var flippedVariationByContent = !!options.flipVariationsByContent && (isVertical && variation === 'start' && overflowsRight || isVertical && variation === 'end' && overflowsLeft || !isVertical && variation === 'start' && overflowsBottom || !isVertical && variation === 'end' && overflowsTop);

    var flippedVariation = flippedVariationByRef || flippedVariationByContent;

    if (overlapsRef || overflowsBoundaries || flippedVariation) {
      // this boolean to detect any flip loop
      data.flipped = true;

      if (overlapsRef || overflowsBoundaries) {
        placement = flipOrder[index + 1];
      }

      if (flippedVariation) {
        variation = getOppositeVariation(variation);
      }

      data.placement = placement + (variation ? '-' + variation : '');

      // this object contains `position`, we want to preserve it along with
      // any additional property we may add in the future
      data.offsets.popper = _extends({}, data.offsets.popper, getPopperOffsets(data.instance.popper, data.offsets.reference, data.placement));

      data = runModifiers(data.instance.modifiers, data, 'flip');
    }
  });
  return data;
}

/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by update method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */
function keepTogether(data) {
  var _data$offsets = data.offsets,
      popper = _data$offsets.popper,
      reference = _data$offsets.reference;

  var placement = data.placement.split('-')[0];
  var floor = Math.floor;
  var isVertical = ['top', 'bottom'].indexOf(placement) !== -1;
  var side = isVertical ? 'right' : 'bottom';
  var opSide = isVertical ? 'left' : 'top';
  var measurement = isVertical ? 'width' : 'height';

  if (popper[side] < floor(reference[opSide])) {
    data.offsets.popper[opSide] = floor(reference[opSide]) - popper[measurement];
  }
  if (popper[opSide] > floor(reference[side])) {
    data.offsets.popper[opSide] = floor(reference[side]);
  }

  return data;
}

/**
 * Converts a string containing value + unit into a px value number
 * @function
 * @memberof {modifiers~offset}
 * @private
 * @argument {String} str - Value + unit string
 * @argument {String} measurement - `height` or `width`
 * @argument {Object} popperOffsets
 * @argument {Object} referenceOffsets
 * @returns {Number|String}
 * Value in pixels, or original string if no values were extracted
 */
function toValue(str, measurement, popperOffsets, referenceOffsets) {
  // separate value from unit
  var split = str.match(/((?:\-|\+)?\d*\.?\d*)(.*)/);
  var value = +split[1];
  var unit = split[2];

  // If it's not a number it's an operator, I guess
  if (!value) {
    return str;
  }

  if (unit.indexOf('%') === 0) {
    var element = void 0;
    switch (unit) {
      case '%p':
        element = popperOffsets;
        break;
      case '%':
      case '%r':
      default:
        element = referenceOffsets;
    }

    var rect = getClientRect(element);
    return rect[measurement] / 100 * value;
  } else if (unit === 'vh' || unit === 'vw') {
    // if is a vh or vw, we calculate the size based on the viewport
    var size = void 0;
    if (unit === 'vh') {
      size = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
    } else {
      size = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
    }
    return size / 100 * value;
  } else {
    // if is an explicit pixel unit, we get rid of the unit and keep the value
    // if is an implicit unit, it's px, and we return just the value
    return value;
  }
}

/**
 * Parse an `offset` string to extrapolate `x` and `y` numeric offsets.
 * @function
 * @memberof {modifiers~offset}
 * @private
 * @argument {String} offset
 * @argument {Object} popperOffsets
 * @argument {Object} referenceOffsets
 * @argument {String} basePlacement
 * @returns {Array} a two cells array with x and y offsets in numbers
 */
function parseOffset(offset, popperOffsets, referenceOffsets, basePlacement) {
  var offsets = [0, 0];

  // Use height if placement is left or right and index is 0 otherwise use width
  // in this way the first offset will use an axis and the second one
  // will use the other one
  var useHeight = ['right', 'left'].indexOf(basePlacement) !== -1;

  // Split the offset string to obtain a list of values and operands
  // The regex addresses values with the plus or minus sign in front (+10, -20, etc)
  var fragments = offset.split(/(\+|\-)/).map(function (frag) {
    return frag.trim();
  });

  // Detect if the offset string contains a pair of values or a single one
  // they could be separated by comma or space
  var divider = fragments.indexOf(find(fragments, function (frag) {
    return frag.search(/,|\s/) !== -1;
  }));

  if (fragments[divider] && fragments[divider].indexOf(',') === -1) {
    console.warn('Offsets separated by white space(s) are deprecated, use a comma (,) instead.');
  }

  // If divider is found, we divide the list of values and operands to divide
  // them by ofset X and Y.
  var splitRegex = /\s*,\s*|\s+/;
  var ops = divider !== -1 ? [fragments.slice(0, divider).concat([fragments[divider].split(splitRegex)[0]]), [fragments[divider].split(splitRegex)[1]].concat(fragments.slice(divider + 1))] : [fragments];

  // Convert the values with units to absolute pixels to allow our computations
  ops = ops.map(function (op, index) {
    // Most of the units rely on the orientation of the popper
    var measurement = (index === 1 ? !useHeight : useHeight) ? 'height' : 'width';
    var mergeWithPrevious = false;
    return op
    // This aggregates any `+` or `-` sign that aren't considered operators
    // e.g.: 10 + +5 => [10, +, +5]
    .reduce(function (a, b) {
      if (a[a.length - 1] === '' && ['+', '-'].indexOf(b) !== -1) {
        a[a.length - 1] = b;
        mergeWithPrevious = true;
        return a;
      } else if (mergeWithPrevious) {
        a[a.length - 1] += b;
        mergeWithPrevious = false;
        return a;
      } else {
        return a.concat(b);
      }
    }, [])
    // Here we convert the string values into number values (in px)
    .map(function (str) {
      return toValue(str, measurement, popperOffsets, referenceOffsets);
    });
  });

  // Loop trough the offsets arrays and execute the operations
  ops.forEach(function (op, index) {
    op.forEach(function (frag, index2) {
      if (isNumeric(frag)) {
        offsets[index] += frag * (op[index2 - 1] === '-' ? -1 : 1);
      }
    });
  });
  return offsets;
}

/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by update method
 * @argument {Object} options - Modifiers configuration and options
 * @argument {Number|String} options.offset=0
 * The offset value as described in the modifier description
 * @returns {Object} The data object, properly modified
 */
function offset(data, _ref) {
  var offset = _ref.offset;
  var placement = data.placement,
      _data$offsets = data.offsets,
      popper = _data$offsets.popper,
      reference = _data$offsets.reference;

  var basePlacement = placement.split('-')[0];

  var offsets = void 0;
  if (isNumeric(+offset)) {
    offsets = [+offset, 0];
  } else {
    offsets = parseOffset(offset, popper, reference, basePlacement);
  }

  if (basePlacement === 'left') {
    popper.top += offsets[0];
    popper.left -= offsets[1];
  } else if (basePlacement === 'right') {
    popper.top += offsets[0];
    popper.left += offsets[1];
  } else if (basePlacement === 'top') {
    popper.left += offsets[0];
    popper.top -= offsets[1];
  } else if (basePlacement === 'bottom') {
    popper.left += offsets[0];
    popper.top += offsets[1];
  }

  data.popper = popper;
  return data;
}

/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by `update` method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */
function preventOverflow(data, options) {
  var boundariesElement = options.boundariesElement || getOffsetParent(data.instance.popper);

  // If offsetParent is the reference element, we really want to
  // go one step up and use the next offsetParent as reference to
  // avoid to make this modifier completely useless and look like broken
  if (data.instance.reference === boundariesElement) {
    boundariesElement = getOffsetParent(boundariesElement);
  }

  // NOTE: DOM access here
  // resets the popper's position so that the document size can be calculated excluding
  // the size of the popper element itself
  var transformProp = getSupportedPropertyName('transform');
  var popperStyles = data.instance.popper.style; // assignment to help minification
  var top = popperStyles.top,
      left = popperStyles.left,
      transform = popperStyles[transformProp];

  popperStyles.top = '';
  popperStyles.left = '';
  popperStyles[transformProp] = '';

  var boundaries = getBoundaries(data.instance.popper, data.instance.reference, options.padding, boundariesElement, data.positionFixed);

  // NOTE: DOM access here
  // restores the original style properties after the offsets have been computed
  popperStyles.top = top;
  popperStyles.left = left;
  popperStyles[transformProp] = transform;

  options.boundaries = boundaries;

  var order = options.priority;
  var popper = data.offsets.popper;

  var check = {
    primary: function primary(placement) {
      var value = popper[placement];
      if (popper[placement] < boundaries[placement] && !options.escapeWithReference) {
        value = Math.max(popper[placement], boundaries[placement]);
      }
      return defineProperty({}, placement, value);
    },
    secondary: function secondary(placement) {
      var mainSide = placement === 'right' ? 'left' : 'top';
      var value = popper[mainSide];
      if (popper[placement] > boundaries[placement] && !options.escapeWithReference) {
        value = Math.min(popper[mainSide], boundaries[placement] - (placement === 'right' ? popper.width : popper.height));
      }
      return defineProperty({}, mainSide, value);
    }
  };

  order.forEach(function (placement) {
    var side = ['left', 'top'].indexOf(placement) !== -1 ? 'primary' : 'secondary';
    popper = _extends({}, popper, check[side](placement));
  });

  data.offsets.popper = popper;

  return data;
}

/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by `update` method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */
function shift(data) {
  var placement = data.placement;
  var basePlacement = placement.split('-')[0];
  var shiftvariation = placement.split('-')[1];

  // if shift shiftvariation is specified, run the modifier
  if (shiftvariation) {
    var _data$offsets = data.offsets,
        reference = _data$offsets.reference,
        popper = _data$offsets.popper;

    var isVertical = ['bottom', 'top'].indexOf(basePlacement) !== -1;
    var side = isVertical ? 'left' : 'top';
    var measurement = isVertical ? 'width' : 'height';

    var shiftOffsets = {
      start: defineProperty({}, side, reference[side]),
      end: defineProperty({}, side, reference[side] + reference[measurement] - popper[measurement])
    };

    data.offsets.popper = _extends({}, popper, shiftOffsets[shiftvariation]);
  }

  return data;
}

/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by update method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */
function hide(data) {
  if (!isModifierRequired(data.instance.modifiers, 'hide', 'preventOverflow')) {
    return data;
  }

  var refRect = data.offsets.reference;
  var bound = find(data.instance.modifiers, function (modifier) {
    return modifier.name === 'preventOverflow';
  }).boundaries;

  if (refRect.bottom < bound.top || refRect.left > bound.right || refRect.top > bound.bottom || refRect.right < bound.left) {
    // Avoid unnecessary DOM access if visibility hasn't changed
    if (data.hide === true) {
      return data;
    }

    data.hide = true;
    data.attributes['x-out-of-boundaries'] = '';
  } else {
    // Avoid unnecessary DOM access if visibility hasn't changed
    if (data.hide === false) {
      return data;
    }

    data.hide = false;
    data.attributes['x-out-of-boundaries'] = false;
  }

  return data;
}

/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by `update` method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */
function inner(data) {
  var placement = data.placement;
  var basePlacement = placement.split('-')[0];
  var _data$offsets = data.offsets,
      popper = _data$offsets.popper,
      reference = _data$offsets.reference;

  var isHoriz = ['left', 'right'].indexOf(basePlacement) !== -1;

  var subtractLength = ['top', 'left'].indexOf(basePlacement) === -1;

  popper[isHoriz ? 'left' : 'top'] = reference[basePlacement] - (subtractLength ? popper[isHoriz ? 'width' : 'height'] : 0);

  data.placement = getOppositePlacement(placement);
  data.offsets.popper = getClientRect(popper);

  return data;
}

/**
 * Modifier function, each modifier can have a function of this type assigned
 * to its `fn` property.<br />
 * These functions will be called on each update, this means that you must
 * make sure they are performant enough to avoid performance bottlenecks.
 *
 * @function ModifierFn
 * @argument {dataObject} data - The data object generated by `update` method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {dataObject} The data object, properly modified
 */

/**
 * Modifiers are plugins used to alter the behavior of your poppers.<br />
 * Popper.js uses a set of 9 modifiers to provide all the basic functionalities
 * needed by the library.
 *
 * Usually you don't want to override the `order`, `fn` and `onLoad` props.
 * All the other properties are configurations that could be tweaked.
 * @namespace modifiers
 */
var modifiers = {
  /**
   * Modifier used to shift the popper on the start or end of its reference
   * element.<br />
   * It will read the variation of the `placement` property.<br />
   * It can be one either `-end` or `-start`.
   * @memberof modifiers
   * @inner
   */
  shift: {
    /** @prop {number} order=100 - Index used to define the order of execution */
    order: 100,
    /** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
    enabled: true,
    /** @prop {ModifierFn} */
    fn: shift
  },

  /**
   * The `offset` modifier can shift your popper on both its axis.
   *
   * It accepts the following units:
   * - `px` or unit-less, interpreted as pixels
   * - `%` or `%r`, percentage relative to the length of the reference element
   * - `%p`, percentage relative to the length of the popper element
   * - `vw`, CSS viewport width unit
   * - `vh`, CSS viewport height unit
   *
   * For length is intended the main axis relative to the placement of the popper.<br />
   * This means that if the placement is `top` or `bottom`, the length will be the
   * `width`. In case of `left` or `right`, it will be the `height`.
   *
   * You can provide a single value (as `Number` or `String`), or a pair of values
   * as `String` divided by a comma or one (or more) white spaces.<br />
   * The latter is a deprecated method because it leads to confusion and will be
   * removed in v2.<br />
   * Additionally, it accepts additions and subtractions between different units.
   * Note that multiplications and divisions aren't supported.
   *
   * Valid examples are:
   * ```
   * 10
   * '10%'
   * '10, 10'
   * '10%, 10'
   * '10 + 10%'
   * '10 - 5vh + 3%'
   * '-10px + 5vh, 5px - 6%'
   * ```
   * > **NB**: If you desire to apply offsets to your poppers in a way that may make them overlap
   * > with their reference element, unfortunately, you will have to disable the `flip` modifier.
   * > You can read more on this at this [issue](https://github.com/FezVrasta/popper.js/issues/373).
   *
   * @memberof modifiers
   * @inner
   */
  offset: {
    /** @prop {number} order=200 - Index used to define the order of execution */
    order: 200,
    /** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
    enabled: true,
    /** @prop {ModifierFn} */
    fn: offset,
    /** @prop {Number|String} offset=0
     * The offset value as described in the modifier description
     */
    offset: 0
  },

  /**
   * Modifier used to prevent the popper from being positioned outside the boundary.
   *
   * A scenario exists where the reference itself is not within the boundaries.<br />
   * We can say it has "escaped the boundaries" — or just "escaped".<br />
   * In this case we need to decide whether the popper should either:
   *
   * - detach from the reference and remain "trapped" in the boundaries, or
   * - if it should ignore the boundary and "escape with its reference"
   *
   * When `escapeWithReference` is set to`true` and reference is completely
   * outside its boundaries, the popper will overflow (or completely leave)
   * the boundaries in order to remain attached to the edge of the reference.
   *
   * @memberof modifiers
   * @inner
   */
  preventOverflow: {
    /** @prop {number} order=300 - Index used to define the order of execution */
    order: 300,
    /** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
    enabled: true,
    /** @prop {ModifierFn} */
    fn: preventOverflow,
    /**
     * @prop {Array} [priority=['left','right','top','bottom']]
     * Popper will try to prevent overflow following these priorities by default,
     * then, it could overflow on the left and on top of the `boundariesElement`
     */
    priority: ['left', 'right', 'top', 'bottom'],
    /**
     * @prop {number} padding=5
     * Amount of pixel used to define a minimum distance between the boundaries
     * and the popper. This makes sure the popper always has a little padding
     * between the edges of its container
     */
    padding: 5,
    /**
     * @prop {String|HTMLElement} boundariesElement='scrollParent'
     * Boundaries used by the modifier. Can be `scrollParent`, `window`,
     * `viewport` or any DOM element.
     */
    boundariesElement: 'scrollParent'
  },

  /**
   * Modifier used to make sure the reference and its popper stay near each other
   * without leaving any gap between the two. Especially useful when the arrow is
   * enabled and you want to ensure that it points to its reference element.
   * It cares only about the first axis. You can still have poppers with margin
   * between the popper and its reference element.
   * @memberof modifiers
   * @inner
   */
  keepTogether: {
    /** @prop {number} order=400 - Index used to define the order of execution */
    order: 400,
    /** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
    enabled: true,
    /** @prop {ModifierFn} */
    fn: keepTogether
  },

  /**
   * This modifier is used to move the `arrowElement` of the popper to make
   * sure it is positioned between the reference element and its popper element.
   * It will read the outer size of the `arrowElement` node to detect how many
   * pixels of conjunction are needed.
   *
   * It has no effect if no `arrowElement` is provided.
   * @memberof modifiers
   * @inner
   */
  arrow: {
    /** @prop {number} order=500 - Index used to define the order of execution */
    order: 500,
    /** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
    enabled: true,
    /** @prop {ModifierFn} */
    fn: arrow,
    /** @prop {String|HTMLElement} element='[x-arrow]' - Selector or node used as arrow */
    element: '[x-arrow]'
  },

  /**
   * Modifier used to flip the popper's placement when it starts to overlap its
   * reference element.
   *
   * Requires the `preventOverflow` modifier before it in order to work.
   *
   * **NOTE:** this modifier will interrupt the current update cycle and will
   * restart it if it detects the need to flip the placement.
   * @memberof modifiers
   * @inner
   */
  flip: {
    /** @prop {number} order=600 - Index used to define the order of execution */
    order: 600,
    /** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
    enabled: true,
    /** @prop {ModifierFn} */
    fn: flip,
    /**
     * @prop {String|Array} behavior='flip'
     * The behavior used to change the popper's placement. It can be one of
     * `flip`, `clockwise`, `counterclockwise` or an array with a list of valid
     * placements (with optional variations)
     */
    behavior: 'flip',
    /**
     * @prop {number} padding=5
     * The popper will flip if it hits the edges of the `boundariesElement`
     */
    padding: 5,
    /**
     * @prop {String|HTMLElement} boundariesElement='viewport'
     * The element which will define the boundaries of the popper position.
     * The popper will never be placed outside of the defined boundaries
     * (except if `keepTogether` is enabled)
     */
    boundariesElement: 'viewport',
    /**
     * @prop {Boolean} flipVariations=false
     * The popper will switch placement variation between `-start` and `-end` when
     * the reference element overlaps its boundaries.
     *
     * The original placement should have a set variation.
     */
    flipVariations: false,
    /**
     * @prop {Boolean} flipVariationsByContent=false
     * The popper will switch placement variation between `-start` and `-end` when
     * the popper element overlaps its reference boundaries.
     *
     * The original placement should have a set variation.
     */
    flipVariationsByContent: false
  },

  /**
   * Modifier used to make the popper flow toward the inner of the reference element.
   * By default, when this modifier is disabled, the popper will be placed outside
   * the reference element.
   * @memberof modifiers
   * @inner
   */
  inner: {
    /** @prop {number} order=700 - Index used to define the order of execution */
    order: 700,
    /** @prop {Boolean} enabled=false - Whether the modifier is enabled or not */
    enabled: false,
    /** @prop {ModifierFn} */
    fn: inner
  },

  /**
   * Modifier used to hide the popper when its reference element is outside of the
   * popper boundaries. It will set a `x-out-of-boundaries` attribute which can
   * be used to hide with a CSS selector the popper when its reference is
   * out of boundaries.
   *
   * Requires the `preventOverflow` modifier before it in order to work.
   * @memberof modifiers
   * @inner
   */
  hide: {
    /** @prop {number} order=800 - Index used to define the order of execution */
    order: 800,
    /** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
    enabled: true,
    /** @prop {ModifierFn} */
    fn: hide
  },

  /**
   * Computes the style that will be applied to the popper element to gets
   * properly positioned.
   *
   * Note that this modifier will not touch the DOM, it just prepares the styles
   * so that `applyStyle` modifier can apply it. This separation is useful
   * in case you need to replace `applyStyle` with a custom implementation.
   *
   * This modifier has `850` as `order` value to maintain backward compatibility
   * with previous versions of Popper.js. Expect the modifiers ordering method
   * to change in future major versions of the library.
   *
   * @memberof modifiers
   * @inner
   */
  computeStyle: {
    /** @prop {number} order=850 - Index used to define the order of execution */
    order: 850,
    /** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
    enabled: true,
    /** @prop {ModifierFn} */
    fn: computeStyle,
    /**
     * @prop {Boolean} gpuAcceleration=true
     * If true, it uses the CSS 3D transformation to position the popper.
     * Otherwise, it will use the `top` and `left` properties
     */
    gpuAcceleration: true,
    /**
     * @prop {string} [x='bottom']
     * Where to anchor the X axis (`bottom` or `top`). AKA X offset origin.
     * Change this if your popper should grow in a direction different from `bottom`
     */
    x: 'bottom',
    /**
     * @prop {string} [x='left']
     * Where to anchor the Y axis (`left` or `right`). AKA Y offset origin.
     * Change this if your popper should grow in a direction different from `right`
     */
    y: 'right'
  },

  /**
   * Applies the computed styles to the popper element.
   *
   * All the DOM manipulations are limited to this modifier. This is useful in case
   * you want to integrate Popper.js inside a framework or view library and you
   * want to delegate all the DOM manipulations to it.
   *
   * Note that if you disable this modifier, you must make sure the popper element
   * has its position set to `absolute` before Popper.js can do its work!
   *
   * Just disable this modifier and define your own to achieve the desired effect.
   *
   * @memberof modifiers
   * @inner
   */
  applyStyle: {
    /** @prop {number} order=900 - Index used to define the order of execution */
    order: 900,
    /** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
    enabled: true,
    /** @prop {ModifierFn} */
    fn: applyStyle,
    /** @prop {Function} */
    onLoad: applyStyleOnLoad,
    /**
     * @deprecated since version 1.10.0, the property moved to `computeStyle` modifier
     * @prop {Boolean} gpuAcceleration=true
     * If true, it uses the CSS 3D transformation to position the popper.
     * Otherwise, it will use the `top` and `left` properties
     */
    gpuAcceleration: undefined
  }
};

/**
 * The `dataObject` is an object containing all the information used by Popper.js.
 * This object is passed to modifiers and to the `onCreate` and `onUpdate` callbacks.
 * @name dataObject
 * @property {Object} data.instance The Popper.js instance
 * @property {String} data.placement Placement applied to popper
 * @property {String} data.originalPlacement Placement originally defined on init
 * @property {Boolean} data.flipped True if popper has been flipped by flip modifier
 * @property {Boolean} data.hide True if the reference element is out of boundaries, useful to know when to hide the popper
 * @property {HTMLElement} data.arrowElement Node used as arrow by arrow modifier
 * @property {Object} data.styles Any CSS property defined here will be applied to the popper. It expects the JavaScript nomenclature (eg. `marginBottom`)
 * @property {Object} data.arrowStyles Any CSS property defined here will be applied to the popper arrow. It expects the JavaScript nomenclature (eg. `marginBottom`)
 * @property {Object} data.boundaries Offsets of the popper boundaries
 * @property {Object} data.offsets The measurements of popper, reference and arrow elements
 * @property {Object} data.offsets.popper `top`, `left`, `width`, `height` values
 * @property {Object} data.offsets.reference `top`, `left`, `width`, `height` values
 * @property {Object} data.offsets.arrow] `top` and `left` offsets, only one of them will be different from 0
 */

/**
 * Default options provided to Popper.js constructor.<br />
 * These can be overridden using the `options` argument of Popper.js.<br />
 * To override an option, simply pass an object with the same
 * structure of the `options` object, as the 3rd argument. For example:
 * ```
 * new Popper(ref, pop, {
 *   modifiers: {
 *     preventOverflow: { enabled: false }
 *   }
 * })
 * ```
 * @type {Object}
 * @static
 * @memberof Popper
 */
var Defaults = {
  /**
   * Popper's placement.
   * @prop {Popper.placements} placement='bottom'
   */
  placement: 'bottom',

  /**
   * Set this to true if you want popper to position it self in 'fixed' mode
   * @prop {Boolean} positionFixed=false
   */
  positionFixed: false,

  /**
   * Whether events (resize, scroll) are initially enabled.
   * @prop {Boolean} eventsEnabled=true
   */
  eventsEnabled: true,

  /**
   * Set to true if you want to automatically remove the popper when
   * you call the `destroy` method.
   * @prop {Boolean} removeOnDestroy=false
   */
  removeOnDestroy: false,

  /**
   * Callback called when the popper is created.<br />
   * By default, it is set to no-op.<br />
   * Access Popper.js instance with `data.instance`.
   * @prop {onCreate}
   */
  onCreate: function onCreate() {},

  /**
   * Callback called when the popper is updated. This callback is not called
   * on the initialization/creation of the popper, but only on subsequent
   * updates.<br />
   * By default, it is set to no-op.<br />
   * Access Popper.js instance with `data.instance`.
   * @prop {onUpdate}
   */
  onUpdate: function onUpdate() {},

  /**
   * List of modifiers used to modify the offsets before they are applied to the popper.
   * They provide most of the functionalities of Popper.js.
   * @prop {modifiers}
   */
  modifiers: modifiers
};

/**
 * @callback onCreate
 * @param {dataObject} data
 */

/**
 * @callback onUpdate
 * @param {dataObject} data
 */

// Utils
// Methods
var Popper = function () {
  /**
   * Creates a new Popper.js instance.
   * @class Popper
   * @param {Element|referenceObject} reference - The reference element used to position the popper
   * @param {Element} popper - The HTML / XML element used as the popper
   * @param {Object} options - Your custom options to override the ones defined in [Defaults](#defaults)
   * @return {Object} instance - The generated Popper.js instance
   */
  function Popper(reference, popper) {
    var _this = this;

    var options = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
    classCallCheck(this, Popper);

    this.scheduleUpdate = function () {
      return requestAnimationFrame(_this.update);
    };

    // make update() debounced, so that it only runs at most once-per-tick
    this.update = debounce(this.update.bind(this));

    // with {} we create a new object with the options inside it
    this.options = _extends({}, Popper.Defaults, options);

    // init state
    this.state = {
      isDestroyed: false,
      isCreated: false,
      scrollParents: []
    };

    // get reference and popper elements (allow jQuery wrappers)
    this.reference = reference && reference.jquery ? reference[0] : reference;
    this.popper = popper && popper.jquery ? popper[0] : popper;

    // Deep merge modifiers options
    this.options.modifiers = {};
    Object.keys(_extends({}, Popper.Defaults.modifiers, options.modifiers)).forEach(function (name) {
      _this.options.modifiers[name] = _extends({}, Popper.Defaults.modifiers[name] || {}, options.modifiers ? options.modifiers[name] : {});
    });

    // Refactoring modifiers' list (Object => Array)
    this.modifiers = Object.keys(this.options.modifiers).map(function (name) {
      return _extends({
        name: name
      }, _this.options.modifiers[name]);
    })
    // sort the modifiers by order
    .sort(function (a, b) {
      return a.order - b.order;
    });

    // modifiers have the ability to execute arbitrary code when Popper.js get inited
    // such code is executed in the same order of its modifier
    // they could add new properties to their options configuration
    // BE AWARE: don't add options to `options.modifiers.name` but to `modifierOptions`!
    this.modifiers.forEach(function (modifierOptions) {
      if (modifierOptions.enabled && isFunction(modifierOptions.onLoad)) {
        modifierOptions.onLoad(_this.reference, _this.popper, _this.options, modifierOptions, _this.state);
      }
    });

    // fire the first update to position the popper in the right place
    this.update();

    var eventsEnabled = this.options.eventsEnabled;
    if (eventsEnabled) {
      // setup event listeners, they will take care of update the position in specific situations
      this.enableEventListeners();
    }

    this.state.eventsEnabled = eventsEnabled;
  }

  // We can't use class properties because they don't get listed in the
  // class prototype and break stuff like Sinon stubs


  createClass(Popper, [{
    key: 'update',
    value: function update$$1() {
      return update.call(this);
    }
  }, {
    key: 'destroy',
    value: function destroy$$1() {
      return destroy.call(this);
    }
  }, {
    key: 'enableEventListeners',
    value: function enableEventListeners$$1() {
      return enableEventListeners.call(this);
    }
  }, {
    key: 'disableEventListeners',
    value: function disableEventListeners$$1() {
      return disableEventListeners.call(this);
    }

    /**
     * Schedules an update. It will run on the next UI update available.
     * @method scheduleUpdate
     * @memberof Popper
     */


    /**
     * Collection of utilities useful when writing custom modifiers.
     * Starting from version 1.7, this method is available only if you
     * include `popper-utils.js` before `popper.js`.
     *
     * **DEPRECATION**: This way to access PopperUtils is deprecated
     * and will be removed in v2! Use the PopperUtils module directly instead.
     * Due to the high instability of the methods contained in Utils, we can't
     * guarantee them to follow semver. Use them at your own risk!
     * @static
     * @private
     * @type {Object}
     * @deprecated since version 1.8
     * @member Utils
     * @memberof Popper
     */

  }]);
  return Popper;
}();

/**
 * The `referenceObject` is an object that provides an interface compatible with Popper.js
 * and lets you use it as replacement of a real DOM node.<br />
 * You can use this method to position a popper relatively to a set of coordinates
 * in case you don't have a DOM node to use as reference.
 *
 * ```
 * new Popper(referenceObject, popperNode);
 * ```
 *
 * NB: This feature isn't supported in Internet Explorer 10.
 * @name referenceObject
 * @property {Function} data.getBoundingClientRect
 * A function that returns a set of coordinates compatible with the native `getBoundingClientRect` method.
 * @property {number} data.clientWidth
 * An ES6 getter that will return the width of the virtual reference element.
 * @property {number} data.clientHeight
 * An ES6 getter that will return the height of the virtual reference element.
 */


Popper.Utils = (typeof window !== 'undefined' ? window : global).PopperUtils;
Popper.placements = placements;
Popper.Defaults = Defaults;

return Popper;

})));
//# sourceMappingURL=popper.js.map

/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__("./node_modules/webpack/buildin/global.js")))

/***/ }),

/***/ "./node_modules/process/browser.js":
/***/ (function(module, exports) {

// shim for using process in browser
var process = module.exports = {};

// cached from whatever global is present so that test runners that stub it
// don't break things.  But we need to wrap it in a try catch in case it is
// wrapped in strict mode code which doesn't define any globals.  It's inside a
// function because try/catches deoptimize in certain engines.

var cachedSetTimeout;
var cachedClearTimeout;

function defaultSetTimout() {
    throw new Error('setTimeout has not been defined');
}
function defaultClearTimeout () {
    throw new Error('clearTimeout has not been defined');
}
(function () {
    try {
        if (typeof setTimeout === 'function') {
            cachedSetTimeout = setTimeout;
        } else {
            cachedSetTimeout = defaultSetTimout;
        }
    } catch (e) {
        cachedSetTimeout = defaultSetTimout;
    }
    try {
        if (typeof clearTimeout === 'function') {
            cachedClearTimeout = clearTimeout;
        } else {
            cachedClearTimeout = defaultClearTimeout;
        }
    } catch (e) {
        cachedClearTimeout = defaultClearTimeout;
    }
} ())
function runTimeout(fun) {
    if (cachedSetTimeout === setTimeout) {
        //normal enviroments in sane situations
        return setTimeout(fun, 0);
    }
    // if setTimeout wasn't available but was latter defined
    if ((cachedSetTimeout === defaultSetTimout || !cachedSetTimeout) && setTimeout) {
        cachedSetTimeout = setTimeout;
        return setTimeout(fun, 0);
    }
    try {
        // when when somebody has screwed with setTimeout but no I.E. maddness
        return cachedSetTimeout(fun, 0);
    } catch(e){
        try {
            // When we are in I.E. but the script has been evaled so I.E. doesn't trust the global object when called normally
            return cachedSetTimeout.call(null, fun, 0);
        } catch(e){
            // same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error
            return cachedSetTimeout.call(this, fun, 0);
        }
    }


}
function runClearTimeout(marker) {
    if (cachedClearTimeout === clearTimeout) {
        //normal enviroments in sane situations
        return clearTimeout(marker);
    }
    // if clearTimeout wasn't available but was latter defined
    if ((cachedClearTimeout === defaultClearTimeout || !cachedClearTimeout) && clearTimeout) {
        cachedClearTimeout = clearTimeout;
        return clearTimeout(marker);
    }
    try {
        // when when somebody has screwed with setTimeout but no I.E. maddness
        return cachedClearTimeout(marker);
    } catch (e){
        try {
            // When we are in I.E. but the script has been evaled so I.E. doesn't  trust the global object when called normally
            return cachedClearTimeout.call(null, marker);
        } catch (e){
            // same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error.
            // Some versions of I.E. have different rules for clearTimeout vs setTimeout
            return cachedClearTimeout.call(this, marker);
        }
    }



}
var queue = [];
var draining = false;
var currentQueue;
var queueIndex = -1;

function cleanUpNextTick() {
    if (!draining || !currentQueue) {
        return;
    }
    draining = false;
    if (currentQueue.length) {
        queue = currentQueue.concat(queue);
    } else {
        queueIndex = -1;
    }
    if (queue.length) {
        drainQueue();
    }
}

function drainQueue() {
    if (draining) {
        return;
    }
    var timeout = runTimeout(cleanUpNextTick);
    draining = true;

    var len = queue.length;
    while(len) {
        currentQueue = queue;
        queue = [];
        while (++queueIndex < len) {
            if (currentQueue) {
                currentQueue[queueIndex].run();
            }
        }
        queueIndex = -1;
        len = queue.length;
    }
    currentQueue = null;
    draining = false;
    runClearTimeout(timeout);
}

process.nextTick = function (fun) {
    var args = new Array(arguments.length - 1);
    if (arguments.length > 1) {
        for (var i = 1; i < arguments.length; i++) {
            args[i - 1] = arguments[i];
        }
    }
    queue.push(new Item(fun, args));
    if (queue.length === 1 && !draining) {
        runTimeout(drainQueue);
    }
};

// v8 likes predictible objects
function Item(fun, array) {
    this.fun = fun;
    this.array = array;
}
Item.prototype.run = function () {
    this.fun.apply(null, this.array);
};
process.title = 'browser';
process.browser = true;
process.env = {};
process.argv = [];
process.version = ''; // empty string to avoid regexp issues
process.versions = {};

function noop() {}

process.on = noop;
process.addListener = noop;
process.once = noop;
process.off = noop;
process.removeListener = noop;
process.removeAllListeners = noop;
process.emit = noop;
process.prependListener = noop;
process.prependOnceListener = noop;

process.listeners = function (name) { return [] }

process.binding = function (name) {
    throw new Error('process.binding is not supported');
};

process.cwd = function () { return '/' };
process.chdir = function (dir) {
    throw new Error('process.chdir is not supported');
};
process.umask = function() { return 0; };


/***/ }),

/***/ "./node_modules/style-loader/lib/addStyles.js":
/***/ (function(module, exports, __webpack_require__) {

/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Tobias Koppers @sokra
*/

var stylesInDom = {};

var	memoize = function (fn) {
	var memo;

	return function () {
		if (typeof memo === "undefined") memo = fn.apply(this, arguments);
		return memo;
	};
};

var isOldIE = memoize(function () {
	// Test for IE <= 9 as proposed by Browserhacks
	// @see http://browserhacks.com/#hack-e71d8692f65334173fee715c222cb805
	// Tests for existence of standard globals is to allow style-loader
	// to operate correctly into non-standard environments
	// @see https://github.com/webpack-contrib/style-loader/issues/177
	return window && document && document.all && !window.atob;
});

var getElement = (function (fn) {
	var memo = {};

	return function(selector) {
		if (typeof memo[selector] === "undefined") {
			memo[selector] = fn.call(this, selector);
		}

		return memo[selector]
	};
})(function (target) {
	return document.querySelector(target)
});

var singleton = null;
var	singletonCounter = 0;
var	stylesInsertedAtTop = [];

var	fixUrls = __webpack_require__("./node_modules/style-loader/lib/urls.js");

module.exports = function(list, options) {
	if (typeof DEBUG !== "undefined" && DEBUG) {
		if (typeof document !== "object") throw new Error("The style-loader cannot be used in a non-browser environment");
	}

	options = options || {};

	options.attrs = typeof options.attrs === "object" ? options.attrs : {};

	// Force single-tag solution on IE6-9, which has a hard limit on the # of <style>
	// tags it will allow on a page
	if (!options.singleton) options.singleton = isOldIE();

	// By default, add <style> tags to the <head> element
	if (!options.insertInto) options.insertInto = "head";

	// By default, add <style> tags to the bottom of the target
	if (!options.insertAt) options.insertAt = "bottom";

	var styles = listToStyles(list, options);

	addStylesToDom(styles, options);

	return function update (newList) {
		var mayRemove = [];

		for (var i = 0; i < styles.length; i++) {
			var item = styles[i];
			var domStyle = stylesInDom[item.id];

			domStyle.refs--;
			mayRemove.push(domStyle);
		}

		if(newList) {
			var newStyles = listToStyles(newList, options);
			addStylesToDom(newStyles, options);
		}

		for (var i = 0; i < mayRemove.length; i++) {
			var domStyle = mayRemove[i];

			if(domStyle.refs === 0) {
				for (var j = 0; j < domStyle.parts.length; j++) domStyle.parts[j]();

				delete stylesInDom[domStyle.id];
			}
		}
	};
};

function addStylesToDom (styles, options) {
	for (var i = 0; i < styles.length; i++) {
		var item = styles[i];
		var domStyle = stylesInDom[item.id];

		if(domStyle) {
			domStyle.refs++;

			for(var j = 0; j < domStyle.parts.length; j++) {
				domStyle.parts[j](item.parts[j]);
			}

			for(; j < item.parts.length; j++) {
				domStyle.parts.push(addStyle(item.parts[j], options));
			}
		} else {
			var parts = [];

			for(var j = 0; j < item.parts.length; j++) {
				parts.push(addStyle(item.parts[j], options));
			}

			stylesInDom[item.id] = {id: item.id, refs: 1, parts: parts};
		}
	}
}

function listToStyles (list, options) {
	var styles = [];
	var newStyles = {};

	for (var i = 0; i < list.length; i++) {
		var item = list[i];
		var id = options.base ? item[0] + options.base : item[0];
		var css = item[1];
		var media = item[2];
		var sourceMap = item[3];
		var part = {css: css, media: media, sourceMap: sourceMap};

		if(!newStyles[id]) styles.push(newStyles[id] = {id: id, parts: [part]});
		else newStyles[id].parts.push(part);
	}

	return styles;
}

function insertStyleElement (options, style) {
	var target = getElement(options.insertInto)

	if (!target) {
		throw new Error("Couldn't find a style target. This probably means that the value for the 'insertInto' parameter is invalid.");
	}

	var lastStyleElementInsertedAtTop = stylesInsertedAtTop[stylesInsertedAtTop.length - 1];

	if (options.insertAt === "top") {
		if (!lastStyleElementInsertedAtTop) {
			target.insertBefore(style, target.firstChild);
		} else if (lastStyleElementInsertedAtTop.nextSibling) {
			target.insertBefore(style, lastStyleElementInsertedAtTop.nextSibling);
		} else {
			target.appendChild(style);
		}
		stylesInsertedAtTop.push(style);
	} else if (options.insertAt === "bottom") {
		target.appendChild(style);
	} else {
		throw new Error("Invalid value for parameter 'insertAt'. Must be 'top' or 'bottom'.");
	}
}

function removeStyleElement (style) {
	if (style.parentNode === null) return false;
	style.parentNode.removeChild(style);

	var idx = stylesInsertedAtTop.indexOf(style);
	if(idx >= 0) {
		stylesInsertedAtTop.splice(idx, 1);
	}
}

function createStyleElement (options) {
	var style = document.createElement("style");

	options.attrs.type = "text/css";

	addAttrs(style, options.attrs);
	insertStyleElement(options, style);

	return style;
}

function createLinkElement (options) {
	var link = document.createElement("link");

	options.attrs.type = "text/css";
	options.attrs.rel = "stylesheet";

	addAttrs(link, options.attrs);
	insertStyleElement(options, link);

	return link;
}

function addAttrs (el, attrs) {
	Object.keys(attrs).forEach(function (key) {
		el.setAttribute(key, attrs[key]);
	});
}

function addStyle (obj, options) {
	var style, update, remove, result;

	// If a transform function was defined, run it on the css
	if (options.transform && obj.css) {
	    result = options.transform(obj.css);

	    if (result) {
	    	// If transform returns a value, use that instead of the original css.
	    	// This allows running runtime transformations on the css.
	    	obj.css = result;
	    } else {
	    	// If the transform function returns a falsy value, don't add this css.
	    	// This allows conditional loading of css
	    	return function() {
	    		// noop
	    	};
	    }
	}

	if (options.singleton) {
		var styleIndex = singletonCounter++;

		style = singleton || (singleton = createStyleElement(options));

		update = applyToSingletonTag.bind(null, style, styleIndex, false);
		remove = applyToSingletonTag.bind(null, style, styleIndex, true);

	} else if (
		obj.sourceMap &&
		typeof URL === "function" &&
		typeof URL.createObjectURL === "function" &&
		typeof URL.revokeObjectURL === "function" &&
		typeof Blob === "function" &&
		typeof btoa === "function"
	) {
		style = createLinkElement(options);
		update = updateLink.bind(null, style, options);
		remove = function () {
			removeStyleElement(style);

			if(style.href) URL.revokeObjectURL(style.href);
		};
	} else {
		style = createStyleElement(options);
		update = applyToTag.bind(null, style);
		remove = function () {
			removeStyleElement(style);
		};
	}

	update(obj);

	return function updateStyle (newObj) {
		if (newObj) {
			if (
				newObj.css === obj.css &&
				newObj.media === obj.media &&
				newObj.sourceMap === obj.sourceMap
			) {
				return;
			}

			update(obj = newObj);
		} else {
			remove();
		}
	};
}

var replaceText = (function () {
	var textStore = [];

	return function (index, replacement) {
		textStore[index] = replacement;

		return textStore.filter(Boolean).join('\n');
	};
})();

function applyToSingletonTag (style, index, remove, obj) {
	var css = remove ? "" : obj.css;

	if (style.styleSheet) {
		style.styleSheet.cssText = replaceText(index, css);
	} else {
		var cssNode = document.createTextNode(css);
		var childNodes = style.childNodes;

		if (childNodes[index]) style.removeChild(childNodes[index]);

		if (childNodes.length) {
			style.insertBefore(cssNode, childNodes[index]);
		} else {
			style.appendChild(cssNode);
		}
	}
}

function applyToTag (style, obj) {
	var css = obj.css;
	var media = obj.media;

	if(media) {
		style.setAttribute("media", media)
	}

	if(style.styleSheet) {
		style.styleSheet.cssText = css;
	} else {
		while(style.firstChild) {
			style.removeChild(style.firstChild);
		}

		style.appendChild(document.createTextNode(css));
	}
}

function updateLink (link, options, obj) {
	var css = obj.css;
	var sourceMap = obj.sourceMap;

	/*
		If convertToAbsoluteUrls isn't defined, but sourcemaps are enabled
		and there is no publicPath defined then lets turn convertToAbsoluteUrls
		on by default.  Otherwise default to the convertToAbsoluteUrls option
		directly
	*/
	var autoFixUrls = options.convertToAbsoluteUrls === undefined && sourceMap;

	if (options.convertToAbsoluteUrls || autoFixUrls) {
		css = fixUrls(css);
	}

	if (sourceMap) {
		// http://stackoverflow.com/a/26603875
		css += "\n/*# sourceMappingURL=data:application/json;base64," + btoa(unescape(encodeURIComponent(JSON.stringify(sourceMap)))) + " */";
	}

	var blob = new Blob([css], { type: "text/css" });

	var oldSrc = link.href;

	link.href = URL.createObjectURL(blob);

	if(oldSrc) URL.revokeObjectURL(oldSrc);
}


/***/ }),

/***/ "./node_modules/style-loader/lib/urls.js":
/***/ (function(module, exports) {


/**
 * When source maps are enabled, `style-loader` uses a link element with a data-uri to
 * embed the css on the page. This breaks all relative urls because now they are relative to a
 * bundle instead of the current page.
 *
 * One solution is to only use full urls, but that may be impossible.
 *
 * Instead, this function "fixes" the relative urls to be absolute according to the current page location.
 *
 * A rudimentary test suite is located at `test/fixUrls.js` and can be run via the `npm test` command.
 *
 */

module.exports = function (css) {
  // get current location
  var location = typeof window !== "undefined" && window.location;

  if (!location) {
    throw new Error("fixUrls requires window.location");
  }

	// blank or null?
	if (!css || typeof css !== "string") {
	  return css;
  }

  var baseUrl = location.protocol + "//" + location.host;
  var currentDir = baseUrl + location.pathname.replace(/\/[^\/]*$/, "/");

	// convert each url(...)
	/*
	This regular expression is just a way to recursively match brackets within
	a string.

	 /url\s*\(  = Match on the word "url" with any whitespace after it and then a parens
	   (  = Start a capturing group
	     (?:  = Start a non-capturing group
	         [^)(]  = Match anything that isn't a parentheses
	         |  = OR
	         \(  = Match a start parentheses
	             (?:  = Start another non-capturing groups
	                 [^)(]+  = Match anything that isn't a parentheses
	                 |  = OR
	                 \(  = Match a start parentheses
	                     [^)(]*  = Match anything that isn't a parentheses
	                 \)  = Match a end parentheses
	             )  = End Group
              *\) = Match anything and then a close parens
          )  = Close non-capturing group
          *  = Match anything
       )  = Close capturing group
	 \)  = Match a close parens

	 /gi  = Get all matches, not the first.  Be case insensitive.
	 */
	var fixedCss = css.replace(/url\s*\(((?:[^)(]|\((?:[^)(]+|\([^)(]*\))*\))*)\)/gi, function(fullMatch, origUrl) {
		// strip quotes (if they exist)
		var unquotedOrigUrl = origUrl
			.trim()
			.replace(/^"(.*)"$/, function(o, $1){ return $1; })
			.replace(/^'(.*)'$/, function(o, $1){ return $1; });

		// already a full url? no change
		if (/^(#|data:|http:\/\/|https:\/\/|file:\/\/\/)/i.test(unquotedOrigUrl)) {
		  return fullMatch;
		}

		// convert the url to a full url
		var newUrl;

		if (unquotedOrigUrl.indexOf("//") === 0) {
		  	//TODO: should we add protocol?
			newUrl = unquotedOrigUrl;
		} else if (unquotedOrigUrl.indexOf("/") === 0) {
			// path should be relative to the base url
			newUrl = baseUrl + unquotedOrigUrl; // already starts with '/'
		} else {
			// path should be relative to current directory
			newUrl = currentDir + unquotedOrigUrl.replace(/^\.\//, ""); // Strip leading './'
		}

		// send back the fixed url(...)
		return "url(" + JSON.stringify(newUrl) + ")";
	});

	// send back the fixed css
	return fixedCss;
};


/***/ }),

/***/ "./node_modules/switchery/switchery.css":
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__("./node_modules/css-loader/index.js!./node_modules/switchery/switchery.css");
if(typeof content === 'string') content = [[module.i, content, '']];
// Prepare cssTransformation
var transform;

var options = {}
options.transform = transform
// add the styles to the DOM
var update = __webpack_require__("./node_modules/style-loader/lib/addStyles.js")(content, options);
if(content.locals) module.exports = content.locals;
// Hot Module Replacement
if(false) {
	// When the styles change, update the <style> tags
	if(!content.locals) {
		module.hot.accept("!!../css-loader/index.js!./switchery.css", function() {
			var newContent = require("!!../css-loader/index.js!./switchery.css");
			if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
			update(newContent);
		});
	}
	// When the module is disposed, remove the <style> tags
	module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ "./node_modules/switchery/switchery.js":
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(process) {
/**
 * Switchery 0.1.1
 * http://abpetkov.github.io/switchery/
 *
 * Authored by Alexander Petkov
 * https://github.com/abpetkov
 *
 * Copyright 2013, Alexander Petkov
 * License: The MIT License (MIT)
 * http://opensource.org/licenses/MIT
 *
 */

/**
 * Expose `Switchery`.
 */

module.exports = Switchery;

/**
 * Set Switchery default values.
 *
 * @api public
 */

var defaults = {
    color    : '#64bd63'
  , className: 'switchery'
  , disabled : false
  , speed    : '0.1s'
};

if (process.browser != null) {
  __webpack_require__("./node_modules/switchery/switchery.css");
}

/**
 * Create Switchery object.
 *
 * @param {Object} element
 * @param {Object} options
 * @api public
 */

function Switchery(element, options) {
  if (!(this instanceof Switchery)) return new Switchery(options);

  this.element = element;
  this.options = options || {};

  for (var i in defaults) {
    if (!(i in this.options)) {
      this.options[i] = defaults[i];
    }
  }

  if (this.element.type == 'checkbox') this.init();
}

/**
 * Hide the target element.
 *
 * @api private
 */

Switchery.prototype.hide = function() {
  this.element.style.display = 'none';
};

/**
 * Show custom switch after the target element.
 *
 * @api private
 */

Switchery.prototype.show = function() {
  var switcher = this.create();
  this.element.parentNode.appendChild(switcher);
};

/**
 * Create custom switch.
 *
 * @returns {Object} this.switcher
 * @api private
 */

Switchery.prototype.create = function() {
  this.switcher = document.createElement('span');
  this.jack = document.createElement('small');
  this.switcher.appendChild(this.jack);
  this.switcher.className = this.options.className;

  return this.switcher;
};

/**
 * See if input is checked.
 *
 * @returns {Boolean}
 * @api private
 */

Switchery.prototype.isChecked = function() {
  return this.element.checked;
};

/**
 * See if switcher should be disabled.
 *
 * @returns {Boolean}
 * @api private
 */

Switchery.prototype.isDisabled = function() {
  return this.options.disabled || this.element.disabled;
};

/**
 * Set switch jack proper position.
 *
 * @param {Boolean} clicked - we need this in order to uncheck the input when the switch is clicked
 * @api private
 */

Switchery.prototype.setPosition = function (clicked) {
  var checked = this.isChecked()
    , switcher = this.switcher
    , jack = this.jack;

  if (clicked && checked) checked = false;
  else if (clicked && !checked) checked = true;

  if (checked === true) {
    this.element.checked = true;

    if (window.getComputedStyle) jack.style.left = parseInt(window.getComputedStyle(switcher).width) - jack.offsetWidth + 'px';
    else jack.style.left = parseInt(switcher.currentStyle['width']) - jack.offsetWidth + 'px';

    if (this.options.color) this.colorize();
  } else {
    jack.style.left = '0';
    this.element.checked = false;
    this.switcher.style.backgroundColor = '';
    this.switcher.style.borderColor =  '';
  }
};

/**
 * Set speed.
 *
 * @api private
 */

Switchery.prototype.setSpeed = function() {
  this.switcher.style.transitionDuration = this.options.speed;
  this.jack.style.transitionDuration = this.options.speed;
};

/**
 * Copy the input name and id attributes.
 *
 * @api private
 */

Switchery.prototype.setAttributes = function() {
  var id = this.element.getAttribute('id')
    , name = this.element.getAttribute('name');

  if (id) this.switcher.setAttribute('id', id);
  if (name) this.switcher.setAttribute('name', name);
};

/**
 * Set switch color.
 *
 * @api private
 */

Switchery.prototype.colorize = function() {
  this.switcher.style.backgroundColor = this.options.color;
  this.switcher.style.borderColor = this.options.color;
};

/**
 * Handle the switch click event.
 *
 * @api private
 */

Switchery.prototype.handleClick = function() {
  var $this = this
    , switcher = this.switcher;

  if (this.isDisabled() === false) {
    if (switcher.addEventListener) {
      switcher.addEventListener('click', function() {
        $this.setPosition(true);
      });
    } else {
      switcher.attachEvent('onclick', function() {
        $this.setPosition(true);
      });
    }
  } else {
    this.element.disabled = true;
  }
};

/**
 * Initialize Switchery.
 *
 * @api private
 */

Switchery.prototype.init = function() {
  this.hide();
  this.show();
  this.setSpeed();
  this.setPosition();
  this.setAttributes();
  this.handleClick();
};
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__("./node_modules/process/browser.js")))

/***/ }),

/***/ "./node_modules/toastr/toastr.js":
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*
 * Toastr
 * Copyright 2012-2015
 * Authors: John Papa, Hans Fjällemark, and Tim Ferrell.
 * All Rights Reserved.
 * Use, reproduction, distribution, and modification of this code is subject to the terms and
 * conditions of the MIT license, available at http://www.opensource.org/licenses/mit-license.php
 *
 * ARIA Support: Greta Krafsig
 *
 * Project: https://github.com/CodeSeven/toastr
 */
/* global define */
(function (define) {
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__("./node_modules/jquery/dist/jquery.js")], __WEBPACK_AMD_DEFINE_RESULT__ = (function ($) {
        return (function () {
            var $container;
            var listener;
            var toastId = 0;
            var toastType = {
                error: 'error',
                info: 'info',
                success: 'success',
                warning: 'warning'
            };

            var toastr = {
                clear: clear,
                remove: remove,
                error: error,
                getContainer: getContainer,
                info: info,
                options: {},
                subscribe: subscribe,
                success: success,
                version: '2.1.4',
                warning: warning
            };

            var previousToast;

            return toastr;

            ////////////////

            function error(message, title, optionsOverride) {
                return notify({
                    type: toastType.error,
                    iconClass: getOptions().iconClasses.error,
                    message: message,
                    optionsOverride: optionsOverride,
                    title: title
                });
            }

            function getContainer(options, create) {
                if (!options) { options = getOptions(); }
                $container = $('#' + options.containerId);
                if ($container.length) {
                    return $container;
                }
                if (create) {
                    $container = createContainer(options);
                }
                return $container;
            }

            function info(message, title, optionsOverride) {
                return notify({
                    type: toastType.info,
                    iconClass: getOptions().iconClasses.info,
                    message: message,
                    optionsOverride: optionsOverride,
                    title: title
                });
            }

            function subscribe(callback) {
                listener = callback;
            }

            function success(message, title, optionsOverride) {
                return notify({
                    type: toastType.success,
                    iconClass: getOptions().iconClasses.success,
                    message: message,
                    optionsOverride: optionsOverride,
                    title: title
                });
            }

            function warning(message, title, optionsOverride) {
                return notify({
                    type: toastType.warning,
                    iconClass: getOptions().iconClasses.warning,
                    message: message,
                    optionsOverride: optionsOverride,
                    title: title
                });
            }

            function clear($toastElement, clearOptions) {
                var options = getOptions();
                if (!$container) { getContainer(options); }
                if (!clearToast($toastElement, options, clearOptions)) {
                    clearContainer(options);
                }
            }

            function remove($toastElement) {
                var options = getOptions();
                if (!$container) { getContainer(options); }
                if ($toastElement && $(':focus', $toastElement).length === 0) {
                    removeToast($toastElement);
                    return;
                }
                if ($container.children().length) {
                    $container.remove();
                }
            }

            // internal functions

            function clearContainer (options) {
                var toastsToClear = $container.children();
                for (var i = toastsToClear.length - 1; i >= 0; i--) {
                    clearToast($(toastsToClear[i]), options);
                }
            }

            function clearToast ($toastElement, options, clearOptions) {
                var force = clearOptions && clearOptions.force ? clearOptions.force : false;
                if ($toastElement && (force || $(':focus', $toastElement).length === 0)) {
                    $toastElement[options.hideMethod]({
                        duration: options.hideDuration,
                        easing: options.hideEasing,
                        complete: function () { removeToast($toastElement); }
                    });
                    return true;
                }
                return false;
            }

            function createContainer(options) {
                $container = $('<div/>')
                    .attr('id', options.containerId)
                    .addClass(options.positionClass);

                $container.appendTo($(options.target));
                return $container;
            }

            function getDefaults() {
                return {
                    tapToDismiss: true,
                    toastClass: 'toast',
                    containerId: 'toast-container',
                    debug: false,

                    showMethod: 'fadeIn', //fadeIn, slideDown, and show are built into jQuery
                    showDuration: 300,
                    showEasing: 'swing', //swing and linear are built into jQuery
                    onShown: undefined,
                    hideMethod: 'fadeOut',
                    hideDuration: 1000,
                    hideEasing: 'swing',
                    onHidden: undefined,
                    closeMethod: false,
                    closeDuration: false,
                    closeEasing: false,
                    closeOnHover: true,

                    extendedTimeOut: 1000,
                    iconClasses: {
                        error: 'toast-error',
                        info: 'toast-info',
                        success: 'toast-success',
                        warning: 'toast-warning'
                    },
                    iconClass: 'toast-info',
                    positionClass: 'toast-top-right',
                    timeOut: 5000, // Set timeOut and extendedTimeOut to 0 to make it sticky
                    titleClass: 'toast-title',
                    messageClass: 'toast-message',
                    escapeHtml: false,
                    target: 'body',
                    closeHtml: '<button type="button">&times;</button>',
                    closeClass: 'toast-close-button',
                    newestOnTop: true,
                    preventDuplicates: false,
                    progressBar: false,
                    progressClass: 'toast-progress',
                    rtl: false
                };
            }

            function publish(args) {
                if (!listener) { return; }
                listener(args);
            }

            function notify(map) {
                var options = getOptions();
                var iconClass = map.iconClass || options.iconClass;

                if (typeof (map.optionsOverride) !== 'undefined') {
                    options = $.extend(options, map.optionsOverride);
                    iconClass = map.optionsOverride.iconClass || iconClass;
                }

                if (shouldExit(options, map)) { return; }

                toastId++;

                $container = getContainer(options, true);

                var intervalId = null;
                var $toastElement = $('<div/>');
                var $titleElement = $('<div/>');
                var $messageElement = $('<div/>');
                var $progressElement = $('<div/>');
                var $closeElement = $(options.closeHtml);
                var progressBar = {
                    intervalId: null,
                    hideEta: null,
                    maxHideTime: null
                };
                var response = {
                    toastId: toastId,
                    state: 'visible',
                    startTime: new Date(),
                    options: options,
                    map: map
                };

                personalizeToast();

                displayToast();

                handleEvents();

                publish(response);

                if (options.debug && console) {
                    console.log(response);
                }

                return $toastElement;

                function escapeHtml(source) {
                    if (source == null) {
                        source = '';
                    }

                    return source
                        .replace(/&/g, '&amp;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#39;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;');
                }

                function personalizeToast() {
                    setIcon();
                    setTitle();
                    setMessage();
                    setCloseButton();
                    setProgressBar();
                    setRTL();
                    setSequence();
                    setAria();
                }

                function setAria() {
                    var ariaValue = '';
                    switch (map.iconClass) {
                        case 'toast-success':
                        case 'toast-info':
                            ariaValue =  'polite';
                            break;
                        default:
                            ariaValue = 'assertive';
                    }
                    $toastElement.attr('aria-live', ariaValue);
                }

                function handleEvents() {
                    if (options.closeOnHover) {
                        $toastElement.hover(stickAround, delayedHideToast);
                    }

                    if (!options.onclick && options.tapToDismiss) {
                        $toastElement.click(hideToast);
                    }

                    if (options.closeButton && $closeElement) {
                        $closeElement.click(function (event) {
                            if (event.stopPropagation) {
                                event.stopPropagation();
                            } else if (event.cancelBubble !== undefined && event.cancelBubble !== true) {
                                event.cancelBubble = true;
                            }

                            if (options.onCloseClick) {
                                options.onCloseClick(event);
                            }

                            hideToast(true);
                        });
                    }

                    if (options.onclick) {
                        $toastElement.click(function (event) {
                            options.onclick(event);
                            hideToast();
                        });
                    }
                }

                function displayToast() {
                    $toastElement.hide();

                    $toastElement[options.showMethod](
                        {duration: options.showDuration, easing: options.showEasing, complete: options.onShown}
                    );

                    if (options.timeOut > 0) {
                        intervalId = setTimeout(hideToast, options.timeOut);
                        progressBar.maxHideTime = parseFloat(options.timeOut);
                        progressBar.hideEta = new Date().getTime() + progressBar.maxHideTime;
                        if (options.progressBar) {
                            progressBar.intervalId = setInterval(updateProgress, 10);
                        }
                    }
                }

                function setIcon() {
                    if (map.iconClass) {
                        $toastElement.addClass(options.toastClass).addClass(iconClass);
                    }
                }

                function setSequence() {
                    if (options.newestOnTop) {
                        $container.prepend($toastElement);
                    } else {
                        $container.append($toastElement);
                    }
                }

                function setTitle() {
                    if (map.title) {
                        var suffix = map.title;
                        if (options.escapeHtml) {
                            suffix = escapeHtml(map.title);
                        }
                        $titleElement.append(suffix).addClass(options.titleClass);
                        $toastElement.append($titleElement);
                    }
                }

                function setMessage() {
                    if (map.message) {
                        var suffix = map.message;
                        if (options.escapeHtml) {
                            suffix = escapeHtml(map.message);
                        }
                        $messageElement.append(suffix).addClass(options.messageClass);
                        $toastElement.append($messageElement);
                    }
                }

                function setCloseButton() {
                    if (options.closeButton) {
                        $closeElement.addClass(options.closeClass).attr('role', 'button');
                        $toastElement.prepend($closeElement);
                    }
                }

                function setProgressBar() {
                    if (options.progressBar) {
                        $progressElement.addClass(options.progressClass);
                        $toastElement.prepend($progressElement);
                    }
                }

                function setRTL() {
                    if (options.rtl) {
                        $toastElement.addClass('rtl');
                    }
                }

                function shouldExit(options, map) {
                    if (options.preventDuplicates) {
                        if (map.message === previousToast) {
                            return true;
                        } else {
                            previousToast = map.message;
                        }
                    }
                    return false;
                }

                function hideToast(override) {
                    var method = override && options.closeMethod !== false ? options.closeMethod : options.hideMethod;
                    var duration = override && options.closeDuration !== false ?
                        options.closeDuration : options.hideDuration;
                    var easing = override && options.closeEasing !== false ? options.closeEasing : options.hideEasing;
                    if ($(':focus', $toastElement).length && !override) {
                        return;
                    }
                    clearTimeout(progressBar.intervalId);
                    return $toastElement[method]({
                        duration: duration,
                        easing: easing,
                        complete: function () {
                            removeToast($toastElement);
                            clearTimeout(intervalId);
                            if (options.onHidden && response.state !== 'hidden') {
                                options.onHidden();
                            }
                            response.state = 'hidden';
                            response.endTime = new Date();
                            publish(response);
                        }
                    });
                }

                function delayedHideToast() {
                    if (options.timeOut > 0 || options.extendedTimeOut > 0) {
                        intervalId = setTimeout(hideToast, options.extendedTimeOut);
                        progressBar.maxHideTime = parseFloat(options.extendedTimeOut);
                        progressBar.hideEta = new Date().getTime() + progressBar.maxHideTime;
                    }
                }

                function stickAround() {
                    clearTimeout(intervalId);
                    progressBar.hideEta = 0;
                    $toastElement.stop(true, true)[options.showMethod](
                        {duration: options.showDuration, easing: options.showEasing}
                    );
                }

                function updateProgress() {
                    var percentage = ((progressBar.hideEta - (new Date().getTime())) / progressBar.maxHideTime) * 100;
                    $progressElement.width(percentage + '%');
                }
            }

            function getOptions() {
                return $.extend({}, getDefaults(), toastr.options);
            }

            function removeToast($toastElement) {
                if (!$container) { $container = getContainer(); }
                if ($toastElement.is(':visible')) {
                    return;
                }
                $toastElement.remove();
                $toastElement = null;
                if ($container.children().length === 0) {
                    $container.remove();
                    previousToast = undefined;
                }
            }

        })();
    }).apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
}(__webpack_require__("./node_modules/webpack/buildin/amd-define.js")));


/***/ }),

/***/ "./node_modules/webpack/buildin/amd-define.js":
/***/ (function(module, exports) {

module.exports = function() {
	throw new Error("define cannot be used indirect");
};


/***/ }),

/***/ "./resources/js/app.js":
/***/ (function(module, exports, __webpack_require__) {

/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

__webpack_require__("./resources/js/bootstrap.js");
__webpack_require__("./resources/js/theme.js");

/***/ }),

/***/ "./resources/js/bootstrap.js":
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(__webpack_provided_window_dot_Popper, __webpack_provided_window_dot_jQuery) {//window.maskPhone = require('jquery-input-mask-phone-number/dist/jquery-input-mask-phone-number.min.js')


//window.bootstrapMulty = require('bootstrap-multiselect/dist/js/bootstrap-multiselect.js');
window.datatableCheckbox = __webpack_require__("./node_modules/jquery-datatables-checkboxes/js/dataTables.checkboxes.min.js");

// window.gijgo = require('gijgo/js/gijgo.min.js');

// window.bootstrapMulty = require('bootstrap-multiselect/dist/js/bootstrap-multiselect.js');

window.bootstrapMulty = __webpack_require__("./node_modules/bootstrap-multiselect/dist/js/bootstrap-multiselect.js");

window.CanvasJS = __webpack_require__("./node_modules/canvasjs/dist/canvasjs.min.js");
window.CanvasJSJ = __webpack_require__("./node_modules/canvasjs/dist/jquery.canvasjs.min.js");

window.summernote = __webpack_require__("./node_modules/summernote/dist/summernote.js");

window.bootstrapToggle = __webpack_require__("./node_modules/bootstrap-toggle/js/bootstrap-toggle.js");

window.toastr = __webpack_require__("./node_modules/toastr/toastr.js");

window._ = __webpack_require__("./node_modules/lodash/lodash.js");

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

window.Dropzone = __webpack_require__("./node_modules/dropzone/dist/dropzone.js");

window.iCheck = __webpack_require__("./node_modules/icheck/icheck.js");

window.Dropzone = __webpack_require__("./node_modules/dropzone/dist/dropzone.js");

window.iCheck = __webpack_require__("./node_modules/icheck/icheck.js");

window.select2 = __webpack_require__("./node_modules/select2/dist/js/select2.js");

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
  __webpack_provided_window_dot_Popper = __webpack_require__("./node_modules/popper.js/dist/esm/popper.js").default;
  window.$ = __webpack_provided_window_dot_jQuery = __webpack_require__("./node_modules/jquery/dist/jquery.js");

  __webpack_require__("./node_modules/bootstrap/dist/js/bootstrap.js");
  window.Switchery = __webpack_require__("./node_modules/switchery/switchery.js");
} catch (e) {
  console.log(e);
}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = __webpack_require__("./node_modules/axios/index.js");

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

var token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
  window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {}
//console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');


/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo'

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     encrypted: true
// });
//
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__("./node_modules/popper.js/dist/umd/popper.js"), __webpack_require__("./node_modules/jquery/dist/jquery.js")))

/***/ }),

/***/ "./resources/js/theme.js":
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(jQuery) {(function ($) {
    "use strict"; // Start of use strict

    // Toggle the side navigation

    $("#sidebarToggle, #sidebarToggleTop").on('click', function (e) {
        $("body").toggleClass("sidebar-toggled");
        $(".sidebar").toggleClass("toggled");
        if ($(".sidebar").hasClass("toggled")) {
            $('.sidebar .collapse').collapse('hide');
        };
    });

    // Close any open menu accordions when window is resized below 768px
    $(window).resize(function () {
        if ($(window).width() < 768) {
            $('.sidebar .collapse').collapse('hide');
        };
    });

    // Prevent the content wrapper from scrolling when the fixed side navigation hovered over
    $('body.fixed-nav .sidebar').on('mousewheel DOMMouseScroll wheel', function (e) {
        if ($(window).width() > 768) {
            var e0 = e.originalEvent,
                delta = e0.wheelDelta || -e0.detail;
            this.scrollTop += (delta < 0 ? 1 : -1) * 30;
            e.preventDefault();
        }
    });

    // Scroll to top button appear
    $(document).on('scroll', function () {
        var scrollDistance = $(this).scrollTop();
        if (scrollDistance > 100) {
            $('.scroll-to-top').fadeIn();
        } else {
            $('.scroll-to-top').fadeOut();
        }
    });

    // Smooth scrolling using jQuery easing
    $(document).on('click', 'a.scroll-to-top', function (e) {
        var $anchor = $(this);
        $('html, body').stop().animate({
            scrollTop: $($anchor.attr('href')).offset().top
        }, 1000, 'easeInOutExpo');
        e.preventDefault();
    });
    if (typeof $.fn.dataTable != 'undefined') {
        $.extend(true, $.fn.dataTable.defaults, {
            responsive: true,
            language: {
                search: "",
                searchPlaceholder: "Search"
            },
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 tableFilter'Bf>><'row'<'col-sm-12'tr>><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            buttons: [{ extend: 'csvHtml5', className: 'btn btn-sm btn-primary', text: '<i class="fas fa-download"></i> Export' }]
        });
    }
    if ($(".js-switch")[0] && typeof Switchery != "undefined") {
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        elems.forEach(function (html) {
            new Switchery(html, {
                color: '#2e59d9',
                className: 'switchery switchery-small'
            });
        });
    }
})(jQuery); // End of use strict
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__("./node_modules/jquery/dist/jquery.js")))

/***/ }),

/***/ "./resources/scss/app.scss":
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 0:
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__("./resources/js/app.js");
module.exports = __webpack_require__("./resources/scss/app.scss");


/***/ })

},[0]);