import jQuery from 'jquery';
import K, { timed } from './K.js'

window.jQuery = jQuery;
window.$ = jQuery;
window.K = K;

$(() => {
	let total = 0;
	const url = $('#assetURL').val(),
		_token = $('meta[name="csrf-token"]').attr('content'),
		$el = $('#pushRows'),
		$spinner = $('#spinner'),
		$item = $('[is=row]'),
		$counter = $('#counter'),
		$page = $('#pagination'),
		$length = $('#length'),
		$future = $('#future'),
		$search = $('#search'),
		$sort = $('.sort-button'),
		length = () => parseInt($length.val() || 10),
		page = () => parseInt($page.data('page') || 1),
		pages = () => Math.ceil(total / length()),
		search = () => $search.val().trim(),
		future = () => $future.is(':checked') ? 1 : 0,
		by = () => $('.sort-active').data('by'),
		dir = () => $('.sort-active').hasClass('sort-asc') ? 'asc' : 'desc';

	const populatePage = (resetPage = false) => {
		resetPage && $page.data('page', 1) && K.removeURLParam('page');
		$('tr:not(.keep)', $el).remove();
		$spinner.show();

		$.ajax({
			url,
			method: "POST",
			data: {
				_token,
				length: length(),
				page: page(),
				future: future(),
				by: by(),
				dir: dir(),
				search: search()
			},
			success: function(data) {
				console.log(data);

				if (data.items && data.items.length > 0)
					generateRows(data.items);

				const start = ((page() - 1) * length()) + 1,
					x = `${start} to ${start + data.items.length - 1} of ${data.filtered}`;

				$counter.text(`${x}${data.total != data.filtered ? ` ( filtered from ${data.total} total )` : ''}`);
				$spinner.hide();
				refreshAll();
				total = data.filtered;
				buildPagination();
			}
		});
	}

	const generateRows = rows => {
		K.each(rows, (index, row) => {
			let $row;

			$row = $item.clone();
			$row.data('modal', row.modal.edit);
			$('.hide-receipt', $row).data('modal', row.modal.receipt);
			$('.hide-changes', $row).data('modal', row.modal.changes);
			$row.attr('id', `edit${row.id}`);

			if (!row.has_changes)
				$('.hide-changes', $row).addClass('hidden');
			if (!row.has_image)
				$('.hide-receipt', $row).addClass('hidden');
			if (!row.is_future)
				$('.hide-future', $row).addClass('hidden');
			else
				$row.addClass('is-future');
			if (!row.is_repeat)
				$('.hide-repeat', $row).addClass('hidden');

			K.each(row, (key, value) => {
				$(`.${key}`, $row).html(value);
			});

			$el.append($row.removeClass('hidden skip-tooltip keep'));
		});
	}

	// build the page number links
	const buildPagination = () => {
		const btnClass = 'cursor-pointer border border-gray-300 rounded-md px-1 min-w-6 bg-gray-100 text-center leading-7 shadow-sm';

		const $p = $page;
		$p.html('');

		if (page() > 1) {
			$p.append($('<i />', {
				class: `fal fa-angles-left ${btnClass}`,
				page: 1,
				title: 'First'
			}));
			$p.append($('<i />', {
				class: `fal fa-angle-left ${btnClass}`,
				page: page() - 1,
				title: 'Previous'
			}));
		}

		let start = page() - 3,
			end = page() + 3;
		start = start < 1 ? 1 : start;
		end = end > pages() ? pages() : end;

		if (pages() <= 7) {
			start = 1;
			end = pages();
		} else if (page() <= 3) {
			start = 1;
			end = 7;
		} else if (end >= pages()) {
			start = pages() - 6;
			end = pages();
		}

		for (let i = start; i <= end; i++) {
			$p.append($('<span />', {
				class: `${page() === i ? 'border-indigo-300 text-indigo-500 active' : 'border-gray-300'}  cursor-pointer border rounded-md px-1 min-w-6 bg-gray-100 text-center leading-7 shadow-sm`,
				page: i,
				title: `Page ${i}`,
				text: i
			}));
		}

		if (page() != pages()) {
			$p.append($('<i />', {
				class: `fal fa-angle-right ${btnClass}`,
				page: page() + 1,
				title: 'Next'
			}));
			$p.append($('<i />', {
				class: `fal fa-angles-right ${btnClass}`,
				page: pages(),
				title: 'Last'
			}));
		}
	}

	// update items on search input
	const timer = timed();
	$search.on('input', () => {
		timer.run(() => {
			const s = search();
			if (!s.length) K.removeURLParam('search');
			else K.addURLParam('search', s);
			populatePage(true);
		}, 800);
	});

	// change page on pagination click
	$page.on('click', '[page]:not(.active)', (e) => {
		const p = parseInt($(e.target).attr('page'));
		K.addURLParam('page', p);
		$page.data('page', p);
		populatePage();
	});

	$length.on('change', (e) => {
		const l = parseInt($(e.target).val());
		K.addURLParam('length', l);
		populatePage(true);
	});

	$future.on('change', (e) => {
		const f = future();
		if (!f) K.removeURLParam('future');
		else K.addURLParam('future', 1);
		populatePage(true);
	});

	$sort.on('click', function() {
		const by = $(this).data('by'),
			dir = $(this).hasClass('sort-asc') ? 'desc' : 'asc';
		$('.sort-button').removeClass('sort-asc sort-desc sort-active');
		$(this).addClass(`sort-${dir} sort-active`);
		K.addURLParam('by', by);
		K.addURLParam('dir', dir);
		populatePage(true);
	});

	const time = timed();
	time.run(() => {
		populatePage();
	}, 100);

});