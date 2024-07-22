/*
 Classes, functions and prototypes used in websites by Kraggle
 (c) 2019 Kraggle
*/

const arr = [],
	indexOf = arr.indexOf,
	isWindow = function isWindow(obj) {
		return obj != null && obj === obj.window;
	},
	class2type = {},
	toString = class2type.toString,
	hasOwn = class2type.hasOwnProperty,
	fnToString = hasOwn.toString,
	ObjectFunctionString = fnToString.call(Object);

const K = {
	version: '2.0',

	// function extend(dest: Object, src?: Object): Object
	//  Has an `K.extend` shortcut.

	/**
	 * Merges the properties of the `src` object (or multiple objects) into
	 * `dest` object and returns the latter.
	 *
	 * @param {boolean|object} first `True` for deep merge or the `dest`
	 *      object to merge other objects into. Can be an empty object.
	 * @param  {...object} more All other `src` objects to merge into
	 *      to the first `dest` object.
	 * @returns {object} The first object in the arguments.
	 */
	extend(first, ...more) {
		let options, name, src, copy, copyIsArray, clone,
			target = {},
			i = 0,
			deep = false;
		const length = more.length;

		// Handle a deep copy situation
		if (K.type(first) == 'boolean') {
			deep = first;
			target = more[i] || {};
			i++;
		} else
			target = first;

		// Handle case when target is a string or something (possible in deep copy)
		if (K.type(target) !== 'object' && !K.isFunction(target)) {
			target = {};
		}

		// Extend K itself if only one argument is passed
		if (i === length) {
			target = this;
			i--;
		}

		for (; i < length; i++) {
			options = more[i];

			// Only deal with defined values
			if (options == null) continue;

			// Extend the base object
			for (name in options) {
				src = target[name];
				copy = options[name];

				// Prevent never-ending loop
				if (target === copy) continue;

				// Recurse if we're merging plain objects or arrays
				if (deep && copy && (K.isPlainObject(copy) ||
					(copyIsArray = K.isArray(copy)))) {

					if (copyIsArray) {
						copyIsArray = false;
						clone = src && K.isArray(src) ? src : [];

					} else {
						clone = src && K.isPlainObject(src) ? src : {};
					}

					// Never move original objects, clone them
					target[name] = K.extend(deep, clone, copy);

					// Don't bring in undefined values
				} else if (copy !== undefined) {
					target[name] = copy;
				}
			}
		}

		// Return the modified object
		return target;
	},

	/**
	 * Iterate through an object or array. To break the loop, return false.
	 *
	 * @param {Object[]}      obj      An array or object to iterate over.
	 * @param {Function} callback The function to call for each object.
	 * @returns {Object[]} The input object.
	 */
	each(obj, callback) {
		let length,
			i = 0;

		if (K.isArray(obj)) {
			length = obj.length;
			for (; i < length; i++) {
				if (callback.call(obj[i], i, obj[i]) === false) {
					break;
				}
			}
		} else {
			for (const key in obj) {
				if (callback.call(obj[key], key, obj[key]) === false) {
					break;
				}
			}
		}

		return obj;
	},

	/**
	 * Get the type of passed in value.
	 *
	 * @param {*} obj Any value to check for.
	 * @returns {string} The type of the passed in value.
	 */
	type(obj) {
		if (obj == null) return obj + '';

		// Support: Android <=2.3 only (functionish RegExp)
		return typeof obj === 'object' || typeof obj === 'function' ?
			class2type[toString.call(obj)] || 'object' :
			typeof obj;
	},

	/**
	 * See if the passed in object is a function.
	 *
	 * @param {*} obj The object to check.
	 * @returns {boolean} True if the passed in value is a function.
	 */
	isFunction(obj) {
		return K.type(obj) === 'function';
	},

	isDate(obj) {
		return Object.prototype.toString.call(obj) === '[object Date]';
	},

	/**
	 * See if the passed in object is a plain object.
	 *
	 * @param {*} obj The object to check.
	 * @returns {boolean} True if the passed in value is an object.
	 */
	isPlainObject(obj) {

		// Detect obvious negatives
		// Use toString instead of jQuery.type to catch host objects
		if (!obj || toString.call(obj) !== '[object Object]') {
			return false;
		}

		const proto = Object.getPrototypeOf(obj);

		// Objects with no prototype (e.g., `Object.create( null )`) are plain
		if (!proto) {
			return true;
		}

		// Objects with prototype are plain iff they were constructed by a global Object function
		const Ctor = hasOwn.call(proto, 'constructor') && proto.constructor;
		return typeof Ctor === 'function' && fnToString.call(Ctor) === ObjectFunctionString;
	},

	isObject(obj) {
		return Object.prototype.toString.call(obj) === '[object Object]';
	},

	isValue(obj) {
		return !this.isObject(obj) && !this.isArray(obj);
	},

	/**
	 * Check if the passed in object is an array.
	 *
	 * @param {*} obj The object to check.
	 * @returns {boolean} True if the passed in value is an array.
	 */
	isArray: Array.isArray || function(obj) {
		return (Object.prototype.toString.call(obj) === '[object Array]');
	},

	/**
	 * Get the index of a value in an array.
	 *
	 * @param {Array} array The array to search.
	 * @param {*} el The value to search for.
	 * @returns {number} The index of the value if found, -1 otherwise.
	 */
	indexOf(array, el) {
		for (let i = 0; i < array.length; i++) {
			if (array[i] === el) return i;
		}
		return -1;
	},

	/**
	 * Get the variables from the URL.
	 *
	 * @param {string} [name] The value of the variable.
	 * @returns {object|string} The variable or an object of all variables.
	 */
	urlParam(name) {
		const a = {};
		let r = window.location.href.match(/\?([^#]*)/);

		if (r == null)
			return name ? '' : {};

		r = r[1].split('&');

		K.each(r, function(_k, v) {
			v = v.split('=');
			v[0] && (a[v[0]] = decodeURI(v[1]));
		});

		return name ? a[name] || '' : a;
	},

	/**
	 * Add URL params without reload.
	 * 
	 * @param {string} key The key of the param to add.
	 * @param {string} value The value of the param to add.
	 */
	addURLParam(key, value) {
		const ps = this.urlParam();
		ps[key] = value;
		const qp = new URLSearchParams(ps);
		history.replaceState(null, '', "?" + qp.toString());
	},

	/**
	 * Remove URL params without reload.
	 * 
	 * @param {string} key The key of the param to add.
	 */
	removeURLParam(key) {
		const ps = this.urlParam();
		delete ps[key];
		const qp = new URLSearchParams(ps);
		history.replaceState(null, '', "?" + qp.toString());
	},

	// Delete keys from object that exist in array
	// (send {} as first argument to make new object)
	curtail() {
		let a = arguments[0],
			e = arguments[1];

		if (!Object.keys(a).length && Object.keys(e).length) {
			a = K.extend({}, arguments[1]);
			e = arguments[2];
		}

		if (e && typeof e != 'object')
			e = [e];

		K.each(e, function(_i, v) {
			if (v in a) delete a[v];
		});

		return a;
	},

	// Delete keys from object that don't exist in array
	// (send {} as first argument to make new object)
	reduce() {
		let a = arguments[0],
			e = arguments[1];

		if (!Object.keys(a).length && Object.keys(e).length) {
			a = K.extend({}, arguments[1]);
			e = arguments[2];
		}

		if (e && typeof e != 'object')
			e = [e];

		K.each(a, function(k) {
			!K.has(k, e) && delete a[k];
		});

		return a;
	},

	inArray: (value, obj, i) => {
		if (obj == null) return -1;

		if (K.type(value) == 'object') {
			for (let j = 0; j < obj.length; j++)
				if (K.equals(obj[j], value)) return j;
			return -1;
		}
		return indexOf.call(obj, value, i);
	},

	isInArray: (value, obj) => {
		switch (K.type(obj)) {
			case 'array':
				return K.inArray(value, obj) == -1 ? false : true;
			case 'object':
				return value in obj;
			default:
				return false;
		}
	},

	local: (key, value) => {
		if (value === undefined) {
			if (key === undefined)
				return localStorage;

			return JSON.parse(localStorage.getItem(key));
		}

		localStorage.setItem(key, JSON.stringify(value));
	},

	localRemove: key => localStorage.removeItem(key),

	isIterable(obj) {
		return K.has(K.type(obj), ['array', 'object']);
	},

	// checks if both passed variables match
	equals: (obj1, obj2) => {
		const type1 = K.type(obj1),
			type2 = K.type(obj2);

		// compare if they both match types
		if (type1 == type2) {

			switch (type1) {
				case 'array':
					if (K.empty(obj1) && K.empty(obj2)) return true;
					if (obj1.length != obj2.length) return false;

					for (let i = 0, len = obj1.length; i < len; i++) {
						if (!K.in(obj1[i], obj2)) return false;
					}
					break;

				case 'object':
					if (K.empty(obj1) && K.empty(obj2)) return true;

					for (const p in obj1) {
						// Check property exists on both objects
						if (!K.has(p, obj2)) return false;
						if (!K.equals(obj1[p], obj2[p])) return false;
					}

					// Check obj2 for any extra properties
					for (const p in obj2) {
						if (typeof (obj1[p]) == 'undefined') return false;
					}
					break;

				case 'number':
				case 'string':
				case 'boolean':
					if (obj1 != obj2) return false;
					break;

				case 'function':
					if (obj1.toString() != obj2.toString()) return false;
					break;

				default:
					if (!obj1 != !obj2) return false;
			}
		} else {
			if (K.empty(obj1) && K.empty(obj2)) return true;

			switch (type1) {
				case 'array':
				case 'object':
					// return false as the types don't match
					return false;
				case 'number':
					if (!obj1 && K.Util.isNull(obj2)) return true;
					if (obj1 && !obj2) return false;
					return false;
				case 'string':
				case 'boolean':
					if (obj1 != obj2) return false;
					break;
				default:
					if (!obj1 != !obj2) return false;
					break;
			}
		}

		// if we have gotten here we are all good, so return true
		return true;
	},

	diff: (function() {
		return {
			CREATED: 'created',
			UPDATED: 'updated',
			REMOVED: 'deleted',
			UNCHANGED: 'unchanged',

			map(obj1, obj2) {
				if (K.isFunction(obj1) || K.isFunction(obj2)) {
					throw 'Cannot compare functions';
				}

				if (K.isValue(obj1) || K.isValue(obj2)) {
					return {
						type: this.compare(obj1, obj2),
						data: {
							new: obj1,
							old: obj2
						}
					}
				}

				const diff = {};
				for (const key in obj1) {
					if (K.isFunction(obj1[key]))
						continue;

					let val2 = undefined;
					if (obj2[key] !== undefined)
						val2 = obj2[key];

					const check = this.map(obj1[key], val2);
					check && (diff[key] = check);
				}

				for (const key in obj2) {
					if (K.isFunction(obj2[key]) || diff[key] !== undefined)
						continue;

					diff[key] = this.map(undefined, obj2[key]);
				}

				for (const key in diff)
					if (diff[key].type == this.UNCHANGED)
						delete diff[key];

				return diff;
			},

			compare(val1, val2) {
				if ((val1 === val2) || K.isDate(val1) && K.isDate(val2) && val1.getTime() === val2.getTime())
					return this.UNCHANGED;
				if (val1 === undefined)
					return this.CREATED;
				if (val2 === undefined)
					return this.REMOVED;
				return this.UPDATED;
			}
		}
	})(),

	isNull: obj => K.has(K.type(obj), ['null', 'undefined']),

	length: item => {
		const type = K.type(item);
		if (type == 'object') return Object.keys(item).length;
		else if (type == 'array') return item.length;
		return 0;
	},

	empty: obj => {
		switch (K.type(obj)) {
			case 'object':
				if (Object.keys(obj).length) return false;
				return true;
			case 'array':
				return !obj.length;
			case 'number':
			case 'boolean':
				return false;
			default:
				return !obj;
		}
	},

	getPropByString: (obj, string) => {
		if (!string)
			return obj;

		const props = string.split('.');

		let i = 0;
		for (let iLen = props.length - 1; i < iLen; i++) {
			const prop = props[i],
				candidate = obj[prop];

			if (candidate !== undefined) obj = candidate;
			else break;
		}
		return obj[props[i]];
	},

	isNumeric: value => K.in(K.type(value), ['number', 'string']) && !isNaN(parseFloat(value)) && isFinite(value),

	isBoolean: value => K.in(value, [0, 1, '0', '1', 'true', 'false', true, false]),

	toBoolean: value => {
		if (K.isBoolean(value)) {
			return K.isInArray(value, ['1', 1, true, 'true']);
		}
		return !!value;
	},

	parse: item => {
		item = K.JSON.parse(item);
		if (K.isNumeric(item)) item = parseInt(item);
		else if (K.type(item) != 'boolean' && K.isBoolean(item)) item = item == 'true';
		return item;
	},

	includeHTML: () => {
		let i, el, file, x;
		/* Loop through a collection of all HTML elements: */
		const z = document.querySelectorAll('[include]');
		for (i = 0; i < z.length; i++) {
			el = z[i];
			/*search for elements with a certain attribute:*/
			file = el.getAttribute('include');
			if (file) {
				/* Make an HTTP request using the attribute value as the file name: */
				x = new XMLHttpRequest();
				x.onreadystatechange = function() {
					if (this.readyState == 4) {
						if (this.status == 200) el.innerHTML = this.responseText;
						if (this.status == 404) el.innerHTML = 'Page not found.';
						/* Remove the attribute, and call this function once more: */
						el.removeAttribute('include');
						K.includeHTML();
					}
				};
				x.open('GET', file, true);
				x.send();
				/* Exit the function: */
				return;
			}
		}
	},

	imgToSvg: (cls, callback) => {
		const doc = document;

		doc.querySelectorAll(`img.${cls}`).forEach(img => {
			const imgId = img.id,
				imgClass = img.className,
				imgURL = img.src,
				imgStyle = img.getAttribute('style'),
				x = new XMLHttpRequest();

			x.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					const svg = this.responseXML.getElementsByTagName('svg')[0];

					// replace the id
					imgId && (svg.id = imgId);

					// replace the classes
					imgClass && svg.setAttribute('class', imgClass.removeClass('svg-me'));

					// remove invalid attribute
					svg.removeAttribute('xmlns:a');

					// set the viewbox if it's not set
					const vB = svg.getAttribute('viewBox'),
						w = svg.getAttribute('width'),
						h = svg.getAttribute('height');
					!vB && w && h && (svg.setAttribute('viewBox', `0 0 ${h} ${w}`));

					imgStyle && svg.setAttribute('style', imgStyle);

					// replace the img with the svg
					img.replaceWith(svg);

					callback && callback.call(svg);
				}
			};
			x.open('GET', imgURL, true);
			x.send();
		});
	},

	loadStyles: stylesheets => {
		stylesheets.forEach(url => {
			const link = document.createElement('link');
			link.setAttribute('rel', 'stylesheet');
			link.setAttribute('href', url);

			document.head.appendChild(link);
		});
	},

	loadStylesWithCondition: (ifTrue, stylesheets) => ifTrue && K.loadStyles(stylesheets),

	/**
	 * Returns a random element from an array.
	 * 
	 * @param {array} array The array to choose from.
	 * @returns The random element.
	 */
	choice: array => array[K.random(0, array.length)],

	/**
	 * Convert a `Date` into an sql compatible `timestamp`. 
	 * 
	 * @param {Date} [date] The date to convert. Defaults to the current date.
	 * @returns {string} The date converted into a sql timestamp.
	 */
	dateToTimestamp: (date = new Date()) => date.toISOString().replace(/(T|\.\d+Z)/g, ' ').trim(),

	/**
	 * Generate a random string at a given length.
	 * 
	 * @param {number} [length] The length of the output string.
	 * @returns {string} The generated string.
	 */
	generateID(length = 10) {
		const result = [],
			chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz',
			len = chars.length;
		for (let i = 0; i < length; i++)
			result.push(chars.charAt(Math.floor(Math.random() * len)));
		return result.join('');
	},

	/**
	 * Used to delay the script execution for a given number of milliseconds.
	 * 
	 * @param {number} ms The delay in milliseconds.
	 * @returns {Promise<void>}
	 */
	delay: (ms) => new Promise(resolve => setTimeout(resolve, ms)),

	/**
	 * Get a value from an object with a given key.
	 * 
	 * @param {object} obj The object.
	 * @param {string} key The key to find. Can be like `key.key` 
	 */
	getValue(obj, key) {
		if (K.type(obj) != 'object') return obj;
		let value;
		const keys = key.split('.');
		for (key of keys) {
			value = obj[key];
			if (K.type(value) != 'object') break;
		}
		return value;
	},

	/**
	 * Get the current timestamp.
	 * 
	 * @param {boolean} [ms] Default `true`, wether to return as milliseconds. 
	 */
	now: (ms = true) => ms ? new Date().getTime() : new Date(),

	/**
	 * Returns a random number between the input min and max.
	 * 
	 * @param {number} [min] The min possible number.
	 * @param {number} [max] The max possible number.
	 * @returns The random number.
	 */
	random: (min = 0, max = 100) => Math.floor(Math.random() * (max - min + 1) + min),

	noCommas: x => x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","),

	noFormatter(num, digits) {
		const lookup = [{
			value: 1,
			symbol: ""
		},
		{
			value: 1e3,
			symbol: "k"
		},
		{
			value: 1e6,
			symbol: "M"
		},
		{
			value: 1e9,
			symbol: "G"
		},
		{
			value: 1e12,
			symbol: "T"
		},
		{
			value: 1e15,
			symbol: "P"
		},
		{
			value: 1e18,
			symbol: "E"
		}
		];
		const rx = /\.0+$|(\.[0-9]*[1-9])0+$/;
		let item = lookup.slice().reverse().find(function(i) {
			return num >= i.value;
		});
		return item ? (num / item.value).toFixed(digits).replace(rx, "$1") + item.symbol : "0";
	},

	ordinalSuffix(i) {
		let j = i % 10,
			k = i % 100;
		if (j === 1 && k !== 11) {
			return i + "st";
		}
		if (j === 2 && k !== 12) {
			return i + "nd";
		}
		if (j === 3 && k !== 13) {
			return i + "rd";
		}
		return i + "th";
	},

	ordinalWord(i) {
		if (i == 1) {
			return "first";
		}
		if (i == 2) {
			return "second";
		}
		if (i == 3) {
			return "third";
		}
		if (i == 4) {
			return "fourth";
		}
		if (i == 5) {
			return "fifth";
		}
		return "";
	},

	/**
	 * Convert a string to a slug.
	 * 
	 * @param {string} string The string to convert.
	 */
	toSlug(string) {
		return string.toLowerCase()
			.replace(/ /g, '-')
			.replace(/[^\w-]+/g, '')
			.replace(/-+/g, '-');
	}
};

K.JSON = {
	stringify(obj) {
		if (K.in(K.type(obj), ['array', 'object']))
			return JSON.stringify(obj);
		return obj;
	},

	parse(string) {
		try {
			return JSON.parse(string);
		} catch (e) {
			return string;
		}
	}
};

/*
 * @namespace Util
 *
 * Various utility functions, used by internally.
 */

K.Util = {
	// @function create(proto: Object, properties?: Object): Object
	// Compatibility polyfill for [Object.create](https://developer.mozilla.org/docs/Web/JavaScript/Reference/Global_Objects/Object/create)
	create: Object.create || (function() {
		function F() {
			// do nothing
		}
		return function(Proto) {
			F.prototype = Proto;
			return new F();
		};
	}()),

	// @function bind(fn: Function, …): Function
	// Returns a new function bound to the arguments passed, like [Function.prototype.bind](https://developer.mozilla.org/docs/Web/JavaScript/Reference/Global_Objects/Function/bind).
	// Has a `K.bind()` shortcut.
	bind(fn, obj) {
		const slice = Array.prototype.slice;

		if (fn.bind) {
			return fn.bind.apply(fn, slice.call(arguments, 1));
		}

		const args = slice.call(arguments, 2);

		return function() {
			return fn.apply(obj, args.length ? args.concat(slice.call(arguments)) : arguments);
		};
	},

	// @function stamp(obj: Object): Number
	// Returns the unique ID of an object, assigning it one if it doesn't have it.
	stamp(obj) {
		/*eslint-disable */
		obj._k_id = obj._k_id || ++K.Util.lastId;
		return obj._k_id;
		/*eslint-enable */
	},

	// @property lastId: Number
	// Last unique ID used by [`stamp()`](#util-stamp)
	lastId: 0,

	// @function throttle(fn: Function, time: Number, context: Object): Function
	// Returns a function which executes function `fn` with the given scope `context`
	// (so that the `this` keyword refers to `context` inside `fn`'s code). The function
	// `fn` will be called no more than one time per given amount of `time`. The arguments
	// received by the bound function will be any arguments passed when binding the
	// function, followed by any arguments passed when invoking the bound function.
	// Has an `K.bind` shortcut.
	throttle(fn, time, context) {
		let lock, args;

		const later = () => {
			// reset lock and call if queued
			lock = false;
			if (args) {
				wrapperFn.apply(context, args);
				args = false;
			}
		};

		const wrapperFn = () => {
			if (lock) {
				// called too soon, queue to call later
				args = arguments;

			} else {
				// call and lock until later
				fn.apply(context, arguments);
				setTimeout(later, time);
				lock = true;
			}
		};

		return wrapperFn;
	},

	// @function wrapNum(num: Number, range: Number[], includeMax?: Boolean): Number
	// Returns the number `num` modulo `range` in such a way so it lies within
	// `range[0]` and `range[1]`. The returned value will be always smaller than
	// `range[1]` unless `includeMax` is set to `true`.
	wrapNum(x, range, includeMax) {
		const max = range[1],
			min = range[0],
			d = max - min;
		return x === max && includeMax ? x : ((((x - min) % d) + d) % d) + min;
	},

	// @function falseFn(): Function
	// Returns a function which always returns `false`.
	falseFn() {
		return false;
	},

	// @function formatNum(num: Number, digits?: Number): Number
	// Returns the number `num` rounded to `digits` decimals, or to 5 decimals by default.
	formatNum(num, digits) {
		const pow = Math.pow(10, digits || 5);
		return Math.round(num * pow) / pow;
	},

	// @function trim(str: String): String
	// Compatibility polyfill for [String.prototype.trim](https://developer.mozilla.org/docs/Web/JavaScript/Reference/Global_Objects/String/Trim)
	trim(str) {
		return str.trim ? str.trim() : str.replace(/^\s+|\s+$/g, '');
	},

	// @function splitWords(str: String): String[]
	// Trims and splits the string on whitespace and returns the array of parts.
	splitWords(str) {
		return K.Util.trim(str).split(/\s+/);
	},

	// @function setOptions(obj: Object, options: Object): Object
	// Merges the given properties to the `options` of the `obj` object, returning the resulting options. See `Class options`. Has an `K.setOptions` shortcut.
	setOptions(obj, options) {
		if (!K.has('options', obj)) {
			obj.options = obj.options ? K.Util.create(obj.options) : {};
		}
		for (const i in options) {
			obj.options[i] = options[i];
		}
		return obj.options;
	},

	// @function getParamString(obj: Object, existingUrl?: String, uppercase?: Boolean): String
	// Converts an object into a parameter URL string, e.g. `{a: 'foo', b: 'bar'}`
	// translates to `'?a=foo&b=bar'`. If `existingUrl` is set, the parameters will
	// be appended at the end. If `uppercase` is `true`, the parameter names will
	// be uppercased (e.g. `'?A=foo&B=bar'`)
	getParamString(obj, existingUrl, uppercase) {
		const params = [];
		for (const i in obj) {
			params.push(encodeURIComponent(uppercase ? i.toUpperCase() : i) + '=' + encodeURIComponent(obj[i]));
		}
		return ((!existingUrl || existingUrl.indexOf('?') === -1) ? '?' : '&') + params.join('&');
	},

	// @function template(str: String, data: Object): String
	// Simple templating facility, accepts a template string of the form `'Hello {a}, {b}'`
	// and a data object like `{a: 'foo', b: 'bar'}`, returns evaluated string
	// `('Hello foo, bar')`. You can also specify functions instead of strings for
	// data values — they will be evaluated passing `data` as an argument.
	template(str, data) {
		return str.replace(K.Util.templateRe, function(string, key) {
			let value = data[key];

			if (value === undefined) {
				throw new Error('No value provided for variable ' + string);

			} else if (typeof value === 'function') {
				value = value(data);
			}
			return value;
		});
	},

	templateRe: /\{ *([\w_-]+) *\}/g,

	cookie: {
		assign(target) {
			for (let i = 1; i < arguments.length; i++) {
				const source = arguments[i];
				for (let key in source)
					target[key] = source[key];
			}
			return target;
		},

		defaultConverter: {
			read(value) {
				return value.replace(/(%[\dA-F]{2})+/gi, decodeURIComponent)
			},
			write(value) {
				return encodeURIComponent(value).replace(
					/%(2[346BF]|3[AC-F]|40|5[BDE]|60|7[BCD])/g,
					decodeURIComponent
				)
			}
		},

		init(converter, defaultAttributes) {
			function set(key, value, attributes) {
				if (typeof document === 'undefined') return;

				attributes = K.Util.cookie.assign({}, defaultAttributes, attributes);

				if (typeof attributes.expires === 'number')
					attributes.expires = new Date(Date.now() + attributes.expires * 864e5);

				if (attributes.expires)
					attributes.expires = attributes.expires.toUTCString();

				key = encodeURIComponent(key)
					.replace(/%(2[346B]|5E|60|7C)/g, decodeURIComponent)
					.replace(/[()]/g, escape);

				value = converter.write(value, key);

				let stringifiedAttributes = '';
				for (const attributeName in attributes) {
					if (!attributes[attributeName]) continue;

					stringifiedAttributes += '; ' + attributeName;

					if (attributes[attributeName] === true) continue;

					stringifiedAttributes += '=' + attributes[attributeName].split(';')[0];
				}

				return (document.cookie = key + '=' + value + stringifiedAttributes)
			}

			function get(key) {
				if (typeof document === 'undefined' || (arguments.length && !key)) return;

				// To prevent the for loop in the first place assign an empty array
				// in case there are no cookies at all.
				const cookies = document.cookie ? document.cookie.split('; ') : [],
					jar = {};
				for (const cookie of cookies) {
					const parts = cookie.split('=');
					let value = parts.slice(1).join('=');

					if (value[0] === '=')
						value = value.slice(1, -1);

					try {
						const foundKey = K.Util.cookie.defaultConverter.read(parts[0]);
						jar[foundKey] = converter.read(value, foundKey);

						if (key === foundKey) break;
					} catch (e) { }
				}

				return key ? jar[key] : jar
			}

			return Object.create({
				set: set,
				get: get,
				remove: function(key, attributes) {
					set(
						key,
						'',
						K.Util.cookie.assign({}, attributes, {
							expires: -1
						})
					);
				},
				withAttributes: function(attributes) {
					return K.Util.cookie.init(this.converter, K.Util.cookie.assign({}, this.attributes, attributes))
				},
				withConverter: function(converter) {
					return K.Util.cookie.init(K.Util.cookie.assign({}, this.converter, converter), this.attributes)
				}
			}, {
				attributes: {
					value: Object.freeze(defaultAttributes)
				},
				converter: {
					value: Object.freeze(converter)
				}
			})
		}
	}
};

K.cookie = K.Util.cookie.init(K.Util.cookie.defaultConverter, {
	path: '/'
});

// alternatives for functions
K.has = K.inArray;
K.in = K.inArray;
K.isIn = K.isInArray;
K.isWindow = isWindow;

K.evaluate = {
	'+': (a, b) => a + b,
	'-': (a, b) => a - b,
	'*': (a, b) => a * b,
	'/': (a, b) => a / b,
	'<': (a, b) => a < b,
	'>': (a, b) => a > b,
	'>=': (a, b) => a >= b,
	'<=': (a, b) => a <= b
};

K.supportsPassive = false;
try {
	const opts = Object.defineProperty({}, 'passive', {
		get: () => K.supportsPassive = true
	});
	window.addEventListener('testPassive', null, opts);
	window.removeEventListener('testPassive', null, opts);
} catch (e) {
	/* EMPTY */
}

// Populate the class2type map
K.each('Boolean Number String Function Array Date RegExp Object Error Symbol'.split(' '), function(_i, name) {
	class2type['[object ' + name + ']'] = name.toLowerCase();
});

export function isArrayLike(obj) {

	// Support: real iOS 8.2 only (not reproducible in simulator)
	// `in` check used to prevent JIT error (gh-2145)
	// hasOwn isn't used here due to false negatives
	// regarding Nodelist length in IE
	const length = !!obj && 'length' in obj && obj.length,
		type = K.type(obj);

	if (type === 'function' || K.isWindow(obj)) {
		return false;
	}

	return type === 'array' || length === 0 ||
		(typeof length === 'number' && length > 0 && (length - 1) in obj);
}

const rNotHtmlWhite = (/[^\x20\t\r\n\f]+/g);

export function classesToArray(value) {
	if (Array.isArray(value)) {
		return value;
	}
	if (typeof value === 'string') {
		return value.match(rNotHtmlWhite) || [];
	}
	return [];
}

export function stripAndCollapse(value) {
	const tokens = value.match(rNotHtmlWhite) || [];
	return tokens.join(' ');
}

K.isMobile = () => navigator.userAgent.bMatch(/Android|BlackBerry|iPhone|iPad|iPod|Opera Mini|IEMobile/i);

// MARK: [ [  P R O T O T Y P E S  ] ]
if (!Array.prototype.removeDupes) {
	Array.prototype.removeDupes = function() {
		const r = [],
			a = [];

		K.each(this, function(_i, v) {
			const vs = JSON.stringify(v);
			if (!K.in(vs, r)) {
				r.push(vs);
				a.push(v);
			}
		});
		return a;
	};
}

if (!String.prototype.filter) {
	String.prototype.filter = function() {
		const s = this.toString().split(' '),
			r = [];

		s.clean('');

		if (s.length < 2)
			return s[0];

		K.each(s, function(_i, v) {
			if (!K.in(v, r))
				r.push(v);
		});

		return r.join(' ');
	};
}

if (!Array.prototype.clean) {
	Array.prototype.clean = function(deleteValue) {
		for (let i = 0; i < this.length; i++) {
			if (this[i] == deleteValue) {
				this.splice(i, 1);
				i--;
			}
		}
		return this;
	};
}

if (!Array.prototype.average) {
	Array.prototype.average = function() {
		if (this.length) {
			const sum = this.reduce((a, b) => a + b);
			return sum / this.length;
		}

		return 0;
	};
}

if (!String.prototype.bMatch) {
	String.prototype.bMatch = function(regExp) {
		const s = this.toString();

		if (s.match(regExp) === null)
			return false;

		return true;
	};
}

if (!String.prototype.contains) {
	String.prototype.contains = function(str = '', matchCase = false) {
		let s = this.toString();

		if (!matchCase) {
			s = s.toLowerCase();
			str = str.toLowerCase();
		}

		if (s.indexOf(str) == -1)
			return false;

		return true;
	};
}

// Adds a space before each uppercase letter but not when they are grouped [e.g. FBI]
if (!String.prototype.space) {
	String.prototype.space = function() {
		const s = this.toString();

		return s.replace(/([A-Z])([A-Z])([a-z])|([a-z])([A-Z])/g, '$1$4 $2$3$5').trim();
	};
}

if (!String.prototype.titleCase) {
	String.prototype.titleCase = function() {
		const s = this.toString();

		return s.replace(/(?:^|\s)\w/g, function(match) {
			return match.toUpperCase();
		});
	};
}

// Change the first character in a string to uppercase
if (!String.prototype.firstToUpper) {
	String.prototype.firstToUpper = function() {
		const s = this.toString();
		return s.substr(0, 1).toUpperCase() + s.substr(1);
	};
}

// Checks that two strings are equal, with optional case sensitivity
if (!String.prototype.equals) {
	String.prototype.equals = function(other, matchCase = false) {
		if (K.type(other) != 'string') return false;

		let me = this.toString();
		if (!matchCase) {
			me = me.toLowerCase();
			other = other.toLowerCase();
		}
		return me == other;
	};
}

if (!String.prototype.addClass) {
	String.prototype.addClass = function(value) {

		const classes = classesToArray(value),
			string = this.toString();
		let cur, curValue, cls, j;

		if (classes.length) {
			curValue = string || '';
			cur = ` ${stripAndCollapse(curValue)} `;

			if (cur) {
				j = 0;
				while ((cls = classes[j++])) {
					if (cur.indexOf(` ${cls} `) < 0)
						cur += cls + ' ';
				}

				return stripAndCollapse(cur);
			}
		}

		return string;
	};
}

if (!String.prototype.removeClass) {
	String.prototype.removeClass = function(value) {

		const classes = classesToArray(value),
			string = this.toString();
		let cur, curValue, cls, j;

		if (classes.length) {
			curValue = string || '';
			cur = ` ${stripAndCollapse(curValue)} `;

			if (cur) {
				j = 0;
				while ((cls = classes[j++])) {

					// Remove *all* instances
					while (cur.indexOf(` ${cls} `) > -1) {
						cur = cur.replace(` ${cls} `, ' ');
					}
				}

				return stripAndCollapse(cur);
			}
		}

		return string;
	};
}

// Get a string/regex value from another string
if (!String.prototype.get) {
	String.prototype.get = function(value) {
		const string = this.toString(),
			match = string.match(value);
		return K.empty(match) ? '' : match[0];
	};
}

if (!Date.prototype.addDays) {
	Date.prototype.addDays = function(days) {
		this.setTime(this.getTime() + (days * 24 * 60 * 60 * 1000));
		return this;
	};
}

if (!Date.prototype.addHours) {
	Date.prototype.addHours = function(hrs) {
		this.setTime(this.getTime() + (hrs * 60 * 60 * 1000));
		return this;
	};
}

if (!Date.prototype.addMinutes) {
	Date.prototype.addMinutes = function(mins) {
		this.setTime(this.getTime() + (mins * 60 * 1000));
		return this;
	};
}

// const date = new Date();
// date.addDays(-2);
// console.log(date);

export default K;

class Timed {
	run(callback, time) {
		this.time = time;
		this.callback = callback;

		this._action();
	}

	_action() {
		this.timeout && (clearTimeout(this.timeout));
		this.timeout = setTimeout(this.callback, this.time);
	}
}

export function timed() {
	return new Timed();
}
