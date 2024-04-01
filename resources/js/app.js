import './bootstrap';

import Alpine from 'alpinejs';
import jQuery from 'jquery';
import K from './K.js'
import dateDropper from './plugins/datedropper-javascript.js';
import dayjs from 'dayjs';
import tippy from 'tippy.js';
import Cookies from 'js-cookie';

window.Alpine = Alpine;
window.$ = jQuery;
window.K = K;
window.DateDropper = dateDropper;
window.dayjs = dayjs;
window.tippy = tippy;
window.Cookies = Cookies;

import 'tippy.js/dist/tippy.css';

// import jquery plugins here
import select2 from 'select2';
// @ts-ignore:disable-next-line
select2($);
import './plugins/timedropper-jquery.js';

import.meta.glob([
	'../images/**',
	'../fonts/**',
]);

/**
 * Used to refresh all tooltips when triggered.
 */
const refreshAll = function() {
	$('[title][title!=""]').each(function() {
		const $el = $(this),
			title = $el.attr('title'),
			data = $el.data();
		if (!title) return;

		let t;
		if (t = data.tippy) {
			t.setContent(title);
		} else {
			t = tippy($el[0], {
				content: title,
				touch: false,
				placement: data.tooltipPosition || 'bottom'
			});
			$el.data('tippy', t);
		}

		$(this).attr('title', '');
	});

	K.imgToSvg('svg-swap');
};
window.refreshAll = refreshAll;

$(() => {
	refreshAll();
	setTimeout(() => {
		Alpine.start();
	}, 1000);
});
