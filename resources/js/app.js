import './bootstrap';

import Alpine from 'alpinejs';
import jQuery from 'jquery';
import K, { timed } from './K.js'
import dateDropper from './plugins/datedropper-javascript.js';
import dayjs from 'dayjs';
import weekOfYear from 'dayjs/plugin/weekOfYear.js';
import tippy from 'tippy.js';
import Cookies from 'js-cookie';
import DataTable from 'datatables.net-dt'
import 'datatables.net-responsive-dt';

window.Alpine = Alpine;
window.jQuery = jQuery;
window.$ = jQuery;
window.K = K;
window.DateDropper = dateDropper;
window.dayjs = dayjs;
window.tippy = tippy;
window.Cookies = Cookies;
window.DataTable = DataTable;
window.timed = timed;

dayjs.extend(weekOfYear);

dayjs.prototype.weekOfMonth = function() {
	return Math.floor((this.date() - 1) / 7 + 1);
};

dayjs.prototype.weeksInMonth = function() {
	return Math.ceil(this.daysInMonth() / 7);
};

dayjs.prototype.isInLastWeek = function() {
	return this.daysInMonth() - this.date() + 1 <= 7;
};

dayjs.prototype.weeksToEnd = function() {
	return this.weeksInMonth() + 1 - this.weekOfMonth();
};

// console.log(dayjs('2024-07-24').isInLastWeek());

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
	const ignore = ['.select2', '.no-tooltip'];

	$('[title][title!=""]').each(function() {
		const $el = $(this),
			title = $el.attr('title'),
			data = $el.data();
		if (!title) return;

		for (const item of ignore) {
			if ($el.hasClass(item) || $el.closest(item).length) {
				$(this).attr('title', '');
				return;
			}
		}

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
	}, window.location.pathname == '/map' ? 1000 : 0);
});
