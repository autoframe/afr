
/* exported AjaxSender */

/*eslint-disable */
if (typeof window === 'undefined') {
	/**
	 * NodeJS dependencies
	 */
	XMLHttpRequest = require('xmlhttprequest').XMLHttpRequest;
}
/*eslint-enable */

/**
 * Callback function used for the XHR events
 *
 * @callback eventCallback
 * @param {Object} response The XHR response
 */

/**
 * AjaxSender Class used to handle ajax calls
 */
class AjaxSender {
	/**
	 * Creates an instance of AjaxSender
	 * @param {String}					url                   			URL to send the call to
	 * @param {Object}					[parameters]            		Request parameters
	 * @param {String}					[parameters.method=GET]			Request method
	 * @param {Object|FormData}			[parameters.data]				Request data
	 * @param {String}					[parameters.responseType=json]	Request response type
	 * @param {Object.<String, String>}	[parameters.headers]			Request headers
	 * @param {eventCallback}			[parameters.progress]			Callback for the progress event
	 * @param {eventCallback}			[parameters.load]				Callback for the load event
	 * @param {eventCallback}			[parameters.error]				Callback for the error event
	 * @param {eventCallback}			[parameters.uploadProgress]		Callback for the upload progress event
	 * @param {eventCallback}			[parameters.uploadLoad]			Callback for the upload progress event
	 * @param {Boolean}		         	[parameters.wait]		        Don't send the request right away (enables the use of asPromise)
	 */
	constructor(url, parameters) {
		this.url = url;

		/**
		 * The request corresponding XMLHttpRequest
		 * @type {XMLHttpRequest}
		 */
		this.xhr = new XMLHttpRequest();

		// Default values
		this._parameters = {
			method: parameters.method || 'GET',
			data: parameters.data || {},
			responseType: parameters.responseType || 'json',
			headers: parameters.headers || {},
			progress: parameters.progress,
			load: parameters.load,
			error: parameters.error,
			uploadProgress: parameters.uploadProgress,
			uploadLoad: parameters.uploadLoad
		};

		if(!this._parameters.wait){
			this.send();
		}
	}

	/**
	 * Handle callback attachment
	 * @return {Promise} Resolved when data is loaded
	 * @private
	 */
	_handleCallbacks() {
		/**
		 * DOWNLOAD CALLBACKS
		 */
		if (this._parameters.progress) {
			this.xhr.addEventListener('progress', () => {
				Reflect.apply(this._parameters.progress, null, [this.xhr.response]);
			});
		}
		const promise = new Promise((resolve, reject) => {
			if (this._parameters.load || this._returnPromise) {
				this.xhr.addEventListener('load', () => {
					if (this.xhr.status == 200) {
						resolve(this.xhr.response);
						if(this._parameters.load) Reflect.apply(this._parameters.load, null, [this.xhr.response]);
					} else {
						if (this._parameters.error) {
							Reflect.apply(this._parameters.error, null, [this.xhr]);
						} else {
							console.log(this.xhr);
						}

						reject(this.xhr);
					}
				});
			}
		});

		this.xhr.addEventListener('error', this._parameters.error ? () => {
			Reflect.apply(this._parameters.error, null, [this.xhr]);
		} : () => {
			console.log(this.xhr);
		});

		/**
		 * UPLOAD CALLBACKS
		 */
		if (this._parameters.uploadProgress) {
			if(this.xhr.upload){
				this.xhr.upload.addEventListener('progress', () => {
					Reflect.apply(this._parameters.uploadProgress, null, [this.xhr.response]);
				});
			}else{
				console.log('Upload callbacks are unavailable in a NodeJS environment.');
			}
		}
		if (this._parameters.uploadLoad) {
			if(this.xhr.upload){
				this.xhr.upload.addEventListener('load', () => {
					Reflect.apply(this._parameters.uploadLoad, null, [this.xhr.response]);
				});
			}else{
				console.log('Upload callbacks are unavailable in a NodeJS environment.');
			}
		}

		if(this.xhr.upload){
			this.xhr.upload.addEventListener('error', this._parameters.error ? () => {
				Reflect.apply(this._parameters.error, null, [this.xhr]);
			} : () => {
				console.log(this.xhr);
			});
		}

		return promise;
	}

	/**
	 * Encode an object for an URL use
	 * @param {Object} object Object to transform
	 * @param {String} prefix Parameter needed for the recursion
	 * @private
	 */
	_objectToURL(object, prefix) {
		const str = [];

		for (const p in object) {
			if (Reflect.ownKeys(object).includes(p)) {
				const k = prefix ? prefix + '[' + p + ']' : p,
					v = object[p];

				str.push(v !== null && typeof v === 'object' ? this._objectToURL(v, k) : encodeURIComponent(k) + '=' + encodeURIComponent(v));
			}
		}

		return str.join('&');
	}

	/**
	 * Sets the .send() return type to a Promise
	 * @returns {AjaxSender} The current AjaxSender
	 */
	asPromise() {
		this._returnPromise = true;

		return this;
	}

	/**
	 * Stops any outgoing request
	 * @returns {AjaxSender} The current AjaxSender
	 */
	stop() {
		this.xhr.abort();

		return this;
	}

	/**
	 * Send the request (if wait == true in init)
	 * @returns {AjaxSender|Promise} The current AjaxSender OR a Promise
	 */
	send() {
		const loadPromise = this._handleCallbacks();

		/**
		 * Response type
		 */
		this.xhr.responseType = this._parameters.responseType;

		/**
		 * Request method
		 */
		if (this._parameters.method == 'GET') {
			const urlObject = new URL(this.url);

			urlObject.search += (urlObject.search.length && Object.keys(this._parameters.data).length ? '&' : '') + this._objectToURL(this._parameters.data);
			this.xhr.open('GET', urlObject.href);
		} else {
			this.xhr.open(this._parameters.method, this.url);
		}

		/**
		 * Request headers
		 */
		Object.entries(this._parameters.headers).forEach(([header, value]) => this.xhr.setRequestHeader(header, value));

		/**
		 * Data handling
		 */
		if (this._parameters.method == 'GET') {
			this.xhr.send();
		} else {
			if (typeof window !== 'undefined' && this._parameters.data instanceof FormData) {
				if(![...this._parameters.data.values()].find(e => e instanceof File)){
					this._parameters.data.processData = false;
					this._parameters.data.contentType = false;
				}

				this.xhr.send(this._parameters.data);
			} else {
				this.xhr.send(JSON.stringify(this._parameters.data));
			}
		}

		return this._returnPromise ? loadPromise : this;
	}
}

// eslint-disable-next-line no-undef
if (typeof window === 'undefined') module.exports = AjaxSender;