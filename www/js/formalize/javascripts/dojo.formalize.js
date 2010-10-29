//
// Note: This file depends on the Dojo library.
//

// Automatically calls all functions in FORMALIZE.init
dojo.addOnLoad(function() {
	FORMALIZE.go();
});

// Module pattern:
// http://yuiblog.com/blog/2007/06/12/module-pattern/
var FORMALIZE = (function(window, document, undefined) {
	// Private constants.
	var PLACEHOLDER_SUPPORTED = 'placeholder' in document.createElement('input');
	var AUTOFOCUS_SUPPORTED = 'autofocus' in document.createElement('input');
	var WEBKIT = 'webkitAppearance' in document.createElement('select').style;
	var IE6 = parseInt(dojo.isIE, 10) === 6;
	var IE7 = parseInt(dojo.isIE, 10) === 7;

	// Expose innards of FORMALIZE.
	return {
		// FORMALIZE.go
		go: function() {
			for (var i in FORMALIZE.init) {
				FORMALIZE.init[i]();
			}
		},
		// FORMALIZE.init
		init: {
			detect_webkit: function() {
				if (!WEBKIT) {
					return;
				}

				// Tweaks for Safari + Chrome.
				dojo.query('html').addClass('is_webkit');
			},
			// FORMALIZE.init.full_input_size
			full_input_size: function() {
				if (!IE7 || !dojo.query('textarea, input.input_full').length) {
					return;
				}

				// This fixes width: 100% on <textarea> and class="input_full".
				// It ensures that form elements don't go wider than container.
				dojo.query('textarea, input.input_full').forEach(function(el) {
					var new_el = el.cloneNode(false);
					var span = document.createElement('span');

					span.className = 'input_full_wrap';
					span.appendChild(new_el);
					el.parentNode.replaceChild(span, el);
				});
			},
			// FORMALIZE.init.ie6_skin_inputs
			ie6_skin_inputs: function() {
				// Test for Internet Explorer 6.
				if (!IE6 || !dojo.query('input, select, textarea').length) {
					// Exit if the browser is not IE6,
					// or if no form elements exist.
					return;
				}

				// For <input type="submit" />, etc.
				var button_regex = /button|submit|reset/;

				// For <input type="text" />, etc.
				var type_regex = /date|datetime|datetime-local|email|month|number|password|range|search|tel|text|time|url|week/;

				dojo.query('input').forEach(function(el) {
					// Is it a button?
					if (el.getAttribute('type').match(button_regex)) {
						dojo.addClass(el, 'ie6_button');

						/* Is it disabled? */
						if (el.disabled) {
							dojo.addClass(el, 'ie6_button_disabled');
						}
					}
					// Or is it a textual input?
					else if (el.getAttribute('type').match(type_regex)) {
						dojo.addClass(el, 'ie6_input');

						/* Is it disabled? */
						if (el.disabled) {
							dojo.addClass(el, 'ie6_input_disabled');
						}
					}
				});

				dojo.query('textarea, select').forEach(function(el) {
					/* Is it disabled? */
					if (el.disabled) {
						dojo.addClass(el, 'ie6_input_disabled');
					}
				});
			},
			// FORMALIZE.init.placeholder
			placeholder: function() {
				if (PLACEHOLDER_SUPPORTED || !dojo.query('[placeholder]').length) {
					// Exit if placeholder is supported natively,
					// or if page does not have any placeholder.
					return;
				}

				function add_placeholder(el) {
					var text = el.getAttribute('placeholder');

					if (!el.value || el.value === text) {
						el.value = text;
						dojo.addClass(el, 'placeholder_text');
					}
				}

				dojo.query('[placeholder]').forEach(function(el) {
					add_placeholder(el);

					dojo.connect(el, 'onfocus', function() {
						var text = el.getAttribute('placeholder');

						if (el.value === text) {
							el.value = '';
							dojo.removeClass(el, 'placeholder_text');
						}
					});

					dojo.connect(el, 'onblur', function() {
						add_placeholder(el);
					});
				});

				// Prevent <form> from accidentally
				// submitting the placeholder text.
				dojo.query('form').forEach(function(form) {
					dojo.connect(form, 'onsubmit', function() {
						dojo.query('[placeholder]', form).forEach(function(el) {
							var text = el.getAttribute('placeholder');

							if (el.value === text) {
								el.value = '';
							}
						});
					});

					dojo.connect(form, 'onreset', function() {
						dojo.query('[placeholder]', form).forEach(function(el) {
							setTimeout(function() {
								add_placeholder(el);
							}, 50);
						});
					});
				});
			},
			// FORMALIZE.init.autofocus
			autofocus: function() {
				if (AUTOFOCUS_SUPPORTED || !dojo.query('[autofocus]').length) {
					return;
				}

				dojo.query('[autofocus]')[0].focus();
			}
		}
	};
// Alias window, document.
})(this, this.document);