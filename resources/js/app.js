import './bootstrap';

// import Alpine from 'alpinejs';
import jQuery from 'jquery';
import K, { timed } from './K.js'
import dateDropper from './plugins/datedropper-javascript.js';
import dayjs from 'dayjs';
import weekOfYear from 'dayjs/plugin/weekOfYear.js';
import advancedFormat from 'dayjs/plugin/advancedFormat.js';
import tippy from 'tippy.js';
import Cookies from 'js-cookie';
import DataTable from 'datatables.net-dt'
import 'datatables.net-responsive-dt';

// window.Alpine = Alpine;
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
dayjs.extend(advancedFormat);

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
	const ignore = ['.select2', '.no-tooltip'],
		skip = ['.skip-tooltip'];

	$('[title][title!=""]').each(function() {
		const $el = $(this),
			title = $el.attr('title'),
			data = $el.data();
		if (!title) return;

		for (const item of skip) {
			if ($el.hasClass(item) || $el.closest(item).length) {
				return;
			}
		}

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

window.addTooltip = function(el) {
	const content = el.find('tooltip'),
		data = el.data();
	if (!content.html()) return;

	let t;
	if (t = data.tippy) {
		t.setContent(content[0]);
	} else {
		t = tippy(el[0], {
			content: content[0],
			touch: false,
			placement: data.tooltipPosition || 'bottom'
		});
		el.data('tippy', t);
	}
}

const Notify = {
	type: {
		success: {
			class: 'bg-green-100 border-green-400 text-gray-800',
			icon: 'far fa-check-circle text-green-700'
		},
		error: {
			class: 'bg-red-100 border-red-400 text-gray-800',
			icon: 'far fa-exclamation-triangle text-red-700'
		},
		info: {
			class: 'bg-blue-100 border-blue-400 text-gray-800',
			icon: 'far fa-info-circle text-blue-700'
		},
	},

	init() {
		this.type.message = this.type.success;
		this.type.warning = this.type.error;
		this.type.status = this.type.info;

		this.$root = $('notify-box');
		this.$blank = $('blank-notify notify-wrap', this.$root);
		this.$slot = $('notify-slot', this.$root);

		this.$root.on('click', 'button', function() {
			$(this).closest('notify').each(Notify.close);
		});

		K.each(this.$root.data('messages'), (type, message) => {
			this.message(message, type);
		});
	},

	close() {
		$(this).closest('notify-wrap').height(0);
		$(this).removeClass('opacity-100 scale-100 translate-y-0')
			.addClass('opacity-0 scale-80 -translate-y-24');
		setTimeout(() => {
			$(this).closest('notify-wrap').remove();
		}, 1000)
	},

	message(message, type = 'info', timeout = 5000) {
		if (!(type in this.type))
			return;

		const el = this.$blank.clone();
		$('[icon]', el).addClass(this.type[type].icon);
		$('notify', el).addClass(this.type[type].class);
		$('message', el).text(message);

		this.$slot.append(el);
		const height = el.height();
		el.height(0);

		el.addClass('duration-500').height(height + 16);
		$('notify', el).removeClass('opacity-0 scale-80 -translate-y-24')
			.addClass('opacity-100 scale-100 translate-y-0');

		setTimeout(() => {
			this.close.call($('notify', el));
		}, timeout);
	}
};
window.Notify = Notify;

$(() => {
	Notify.init();
	refreshAll();

	$('[hide-id]').on('click', function(e) {
		e.preventDefault();

		const $el = $(`[hide=${$(this).attr('hide-id')}]`);
		if ($el.hasClass('hidden')) {
			$el.removeClass('hidden');
			$(this).text('hide more')
		} else {
			$el.addClass('hidden');
			$(this).text('show more')
		}
	});
});

// window.addEventListener('load', e => {
// 	console.log(document.referrer);
// });