<?php

/**
 * PHP Error Log Viewer.
 * Check readme.md for more information.
 *
 *  * Disclamer
 * - This contains code for deleting your log-file.
 * - It is meant for development-environments
 */

global $settings;

$log_handler = new LogHandler();
$log_handler->handle_ajax_requests();

/**
 * Read, process, delete log files. Output as json.
 */
class LogHandler {

	private $json = 'log-viewer.json';

	/**
	 * Contains grouped content of the log file.
	 *
	 * @var array
	 */
	public $content = array();

	/**
	 * Index used for grouping same messages.
	 * Created via crc32().
	 *
	 * @var int[]
	 */
	public $index = array();

	/**
	 * The size of the file.
	 *
	 * @var int
	 */
	public $filesize = 0;

	/**
	 * The settings which are being applied.
	 *
	 * @var array
	 */
	public $settings = array();

	/**
	 * The default setting
	 *
	 * @var array
	 */
	public $default_settings = array(
		'file_path'           => false,
		'vscode_links'        => true, // Stack trace references files. link them to your repo (https://code.visualstudio.com/docs/editor/command-line#_opening-vs-code-with-urls).
		'vscode_path_search'  => '', // This is needed if you develop on a vm. like '/srv/www/...'.
		'vscode_path_replace' => '', // The local path to your repo. like 'c:/users/...'.
		'file_list' => []
	);

	public function __construct() {
		global $settings;
		$this->get_settings();
		$settings = $this->settings;
	}

	private function save_settings($settings = []) {
		$this->settings = array_merge($this->settings, $settings);
		file_put_contents($this->json, json_encode($this->settings));
	}

	private function get_settings() {
		if (!file_exists($this->json))
			$this->save_settings();
		$this->settings = array_merge(
			$this->default_settings,
			json_decode(file_get_contents($this->json), true),
			['root' => __DIR__]
		);
	}

	public function handle_ajax_requests() {
		$this->ajax_handle_errors();
		if (isset($_GET['get_log'])) {
			$this->ajax_json_log();
		} elseif (isset($_GET['delete_log'])) {
			$this->ajax_delete();
		} elseif (isset($_GET['filesize'])) {
			$this->ajax_filesize();
		} elseif (isset($_GET['file_path'])) {
			$this->ajax_file_path(['file_path' => $_GET['file_path']]);
		} elseif (isset($_GET['find_logs'])) {
			$this->find_logs();
		}
	}

	public function ajax_handle_errors() {
		$used = array_diff(array('get_log', 'delete_log', 'filesize'), array_keys($_GET));
		if (count($used) === 3) {
			return;
		}
		$log_file_valid = $this->is_file_valid();
		if (!$log_file_valid) {
			$this->ajax_header();
			echo $log_file_valid;
			die();
		}
	}

	/**
	 * Read the log-file.
	 *
	 * @return string|false The read string or false on failure.
	 */
	public function get_file() {
		if (!$this->settings['file_path']) return false;
		$my_file = fopen($this->settings['file_path'], 'r');
		$size    = $this->get_size();
		return ($my_file && $size) ? fread($my_file, $size) : false;
	}

	/**
	 * Get the size of the log-file.
	 *
	 * @return int|false The size of the log file in bytes or false.
	 */
	public function get_size() {
		if (!$this->settings['file_path']) return false;

		if (empty($this->filesize)) {
			$this->filesize = filesize($this->settings['file_path']);
		}
		return $this->filesize;
	}

	/**
	 * Check if a file is valid.
	 *
	 * @return boolean|string true or error message.
	 */
	public function is_file_valid() {
		if (!$this->settings['file_path']) return false;

		if (!file_exists($this->settings['file_path'])) {
			return 'The file you specified does not exist (' . $this->settings['file_path'] . ')';
		}

		if (0 == $this->get_size()) {
			return 'Your log file is empty.';
		}

		$mbs = $this->get_size() / 1024 / 1024; // in MB.

		if ($mbs > 100 && !isset($_GET['ignore'])) {
			return ("Aborting. debug.log is larger than 100 MB ($mbs).
				If you want to continue anyway add the 'ignore' queryvar");
		}
		return true;
	}

	/**
	 * Triggers preg_replace_callback which calls
	 * replace_callback function which stores values in $this->content.
	 *
	 * @param string $raw The content of the log file.
	 * @return void
	 */
	public function parse($raw) {
		$error = preg_replace_callback('~^\[(\d[^\]]*)\]((?:[^\r\n]*[\r\n]?(?!\[\d).*)*)~m', array($this, 'replace_callback'), $raw);
	}

	public function link_vscode_files($string) {
		$string = preg_replace_callback('$([A-Z]:)?([\\\/][^:(\s]+)(?: on line |[:\(])([0-9]+)\)?$', array($this, 'vscode_link_filter'), $string);
		return $string;
	}

	public function vscode_link_filter($matches) {
		$link = 'vscode://file/' . $matches[1] . $matches[2] . ':' . $matches[3];
		$root = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['DOCUMENT_ROOT'];
		$val  = parse_url($root, PHP_URL_QUERY) ?? '';
		parse_str($val, $get_array);
		$link = str_replace($this->settings['vscode_path_search'], $this->settings['vscode_path_replace'], $link);
		return "<a href='$link'>" . $matches[0] . '</a>';
	}

	/**
	 * Callback function which is triggered by preg_replace_callback.
	 * Doesn't return but writes to $this->content.
	 *
	 * @param array $arr
	 * looks like that:
	 * array (
	 *      0   =>  [01-Jun-2016 09:24:02 UTC] PHP Fatal error:  Allowed memory size of 456 bytes exhausted (tried to allocate 27 bytes) in ...
	 *      1   =>  [01-Jun-2016 09:24:02 UTC]
	 *      2   =>  PHP Fatal error:  Allowed memory size of 56 bytes exhausted (tried to allocate 15627 bytes) in ... *
	 *)
	 * @return void
	 */
	public function replace_callback($arr) {
		$err_id = crc32(trim($arr[2])); // create a unique identifier for the error message.
		if (!isset($this->content[$err_id])) { // we have a new error.
			$this->content[$err_id]        = array();
			$this->content[$err_id]['id']  = $err_id; // err_id.
			$this->content[$err_id]['cnt'] = 1; // counter.
			$this->index[] = $err_id;
		} else { // we already have that error...
			$this->content[$err_id]['cnt']++; // counter.
		}

		$date = date_create($arr[1]); // false if no valid date.
		$this->content[$err_id]['time'] = $date ? $date->format(DateTime::ATOM) : $arr[1]; // ISO8601, readable in js
		$message = htmlspecialchars(trim($arr[2]), ENT_QUOTES);
		$this->content[$err_id]['msg'] = $this->settings['vscode_links'] ? $this->link_vscode_files($message) : $message;
		$this->content[$err_id]['cls'] = implode(
			' ',
			array_slice(
				str_word_count($this->content[$err_id]['msg'], 2),
				1,
				2
			)
		); // the first few words of the message become class items.
	}

	public function delete() {
		if (!$this->settings['file_path']) return false;

		if (!file_exists($this->settings['file_path'])) {
			return 'there was no file to delete';
		}

		if (!is_writeable(realpath($this->settings['file_path']))) {
			return 'debug.log is not writable';
		}

		$f = @fopen($this->settings['file_path'], 'r+');

		if ($f !== false) {
			ftruncate($f, 0);
			fclose($f);
			return 'emptied file';
		} else {
			return 'file could not be emptied';
		}
	}

	public function ajax_header() {
		header('Content-Type: application/json');
		header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
		header('Cache-Control: post-check=0, pre-check=0', false);
		header('Pragma: no-cache');
	}

	public function ajax_json_log() {
		$this->ajax_header();
		$file = $this->get_file();
		if (!$file) {
			die("File is empty or can't be opened.");
		}
		$this->parse($file); // writes to $this->content. preg_replace_callback is odd.
		echo (json_encode(array_values($this->content)));
		die();
	}

	public function ajax_delete() {
		$this->ajax_header();
		echo $this->delete();
		die();
	}

	public function ajax_filesize() {
		$this->ajax_header();
		echo json_encode($this->get_size());
		die();
	}

	public function ajax_file_path($settings) {
		$this->ajax_header();
		$this->save_settings($settings);
		die();
	}

	private function find_logs() {
		$name = ini_get('error_log');
		$dir = new RecursiveDirectoryIterator(__DIR__ . '/');
		$ite = new RecursiveIteratorIterator($dir);
		$files = new RegexIterator($ite, "/.*$name/", RegexIterator::GET_MATCH);
		$fileList = array();
		foreach ($files as $file) {
			$fileList = array_merge($fileList, $file);
		}
		$this->save_settings(['file_list' => $fileList]);
		$this->first();

		echo json_encode($this->settings);
		// error_log(json_encode($this->settings, JSON_PRETTY_PRINT));
		die();
	}

	public function first() {
		$this->settings['file_path'] = count($this->settings['file_list']) ? $this->settings['file_list'][0] : false;
		$this->save_settings();
	}
}

?>

<html>

<head>
	<meta charset="utf-8">
	<meta content="width=device-width,initial-scale=1,minimal-ui" name="viewport">
	<title>Log Viewer</title>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
	<link href="https://fonts.googleapis.com/css?family=Roboto+Mono" rel="stylesheet">
	<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />

	<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>

	<style>
		#app {
			min-height: calc(100vh - 36px);
			font-family: 'Roboto', sans-serif;
		}

		pre {
			white-space: pre-wrap;
			margin-bottom: 0;
		}

		table tr td:first-of-type {
			padding-left: 20px;
		}

		table tr td:last-of-type {
			padding-right: 20px;
		}

		table tbody td pre {
			font-family: 'Roboto Mono', monospace;
			font-size: 13px;
		}

		table thead th {
			font-size: 13px;
		}

		.sk-cube-grid {
			position: absolute;
			width: 100px;
			height: 100px;
			display: grid;
			grid-template-columns: 1fr 1fr 1fr;
			grid-template-rows: 1fr 1fr 1fr;
			gap: 2px;
			left: 50%;
			top: 50%;
			transform: translate(-50%, -50%);
			visibility: visible;
			opacity: 1;
			transition: all 0.3 ease-in-out;
		}

		.sk-cube-grid.hide {
			visibility: hidden;
			opacity: 0;
		}

		.sk-cube-grid .sk-cube {
			width: 100%;
			height: 100%;
			background-color: #b62f2f;
			-webkit-animation: sk-cubeGridScaleDelay 1.3s infinite ease-in-out;
			animation: sk-cubeGridScaleDelay 1.3s infinite ease-in-out
		}

		.sk-cube-grid .sk-cube1 {
			-webkit-animation-delay: 0.2s;
			animation-delay: 0.2s
		}

		.sk-cube-grid .sk-cube2 {
			-webkit-animation-delay: 0.3s;
			animation-delay: 0.3s
		}

		.sk-cube-grid .sk-cube3 {
			-webkit-animation-delay: 0.4s;
			animation-delay: 0.4s
		}

		.sk-cube-grid .sk-cube4 {
			-webkit-animation-delay: 0.1s;
			animation-delay: 0.1s
		}

		.sk-cube-grid .sk-cube5 {
			-webkit-animation-delay: 0.2s;
			animation-delay: 0.2s
		}

		.sk-cube-grid .sk-cube6 {
			-webkit-animation-delay: 0.3s;
			animation-delay: 0.3s
		}

		.sk-cube-grid .sk-cube7 {
			-webkit-animation-delay: 0s;
			animation-delay: 0s
		}

		.sk-cube-grid .sk-cube8 {
			-webkit-animation-delay: 0.1s;
			animation-delay: 0.1s
		}

		.sk-cube-grid .sk-cube9 {
			-webkit-animation-delay: 0.2s;
			animation-delay: 0.2s
		}

		@keyframes sk-cubeGridScaleDelay {

			0%,
			70%,
			100% {
				-webkit-transform: scale3D(1, 1, 1);
				transform: scale3D(1, 1, 1)
			}

			35% {
				-webkit-transform: scale3D(0, 0, 1);
				transform: scale3D(0, 0, 1)
			}
		}

		.text-string {
			color: #0ab164;
		}

		.text-number {
			color: #d08869;
		}

		pre a {
			color: #5e5bad;
		}

		pre a:hover {
			color: #343360;
		}
	</style>
</head>

<body class="p-3 bg-dark">
	<div id="app" class="card">
		<div class="card-header d-flex justify-content-between align-items-center gap-3">
			<div class="menu-wrap d-flex align-items-center gap-3">

				<select id="select-menu" class="form-select" aria-label=".form-select example" style="width:300px">
					<?php if (count($settings['file_list'])) {
						foreach ($settings['file_list'] as $file) { ?>
							<option<?= ($file == $settings['file_path'] ? ' selected' : '') ?> value="<?= $file ?>"><?= str_replace($settings['root'] . '\\', '', $file) ?></option>
							<?php }
					} else { ?>
							<option selected>No log files were found!</option>
						<?php } ?>

				</select>
				<button id="refresh-btn" class="btn btn-info rounded-pill" style="height:40px" title="Reacquire the logs within the directories.">
					<span class="fas fa-sync-alt text-white"></span>
				</button>

				<div id="filesize" class="badge rounded-pill bg-secondary fw-normal fs-6">0 KB</div>

			</div>

			<h4 class="card-title m-0">Log Viewer</h4>

			<!-- <div class="input-group" style="width:300px">
				<input id="filter-input" type="text" class="form-control" placeholder="Filter (RegEx)" aria-label="Username" aria-describedby="basic-addon1">
			</div> -->

			<div class="menu-wrap d-flex align-items-center gap-3">

				<div class="form-check form-switch">
					<input class="form-check-input" type="checkbox" role="switch" id="reload-switch" checked>
					<label class="form-check-label" for="reload-switch">Auto Reload</label>
				</div>

				<button id="trash-btn" class="btn btn-danger rounded-pill" style="height:40px" title="Clear the currently selected log.">
					<span class="fas fa-trash"></span>
				</button>
			</div>
		</div>

		<div class="card-body ps-0 pe-0">

			<table class="table table-hover">
				<caption id="no-entries" class="text-center">There are no entries in the selected error log.</caption>

				<colgroup class="border-top-0">
					<col span="1" style="width:70px">
					<col span="1" style="width:120px">
					<col span="1" style="width:auto">
				</colgroup>

				<thead class="border-top-0">
					<th scope="col">Count</th>
					<th scope="col">Time</th>
					<th scope="col">Message</th>
				</thead>
				<tbody id="table-rows">
					<!-- <tr>
						<td class="table-danger">2</td>
						<td class="table-danger">20/12/2021, 07:48:20</td>
						<td>
							<pre>WordPress database error Table 'howtomake_db.wp_comments' doesn't exist for query
SELECT comment_approved, COUNT( * ) AS total
FROM wp_comments

GROUP BY comment_approved
made by require('wp-blog-header.php'), require_once('wp-includes/template-loader.php'), include('/themes/howtomake_s/views/404.php'), wp_footer, do_action('wp_footer'), WP_Hook->do_action, WP_Hook->apply_filters, wp_admin_bar_render, do_action_ref_array('admin_bar_menu'), WP_Hook->do_action, WP_Hook->apply_filters, wp_admin_bar_comments_menu, wp_count_comments, get_comment_count, W3TC\DbCache_WpdbNew->query, W3TC\DbCache_WpdbInjection_QueryCaching->query, W3TC\_CallUnderlying->query, W3TC\DbCache_WpdbNew->query, W3TC\DbCache_WpdbInjection->query, W3TC\DbCache_WpdbNew->default_query</pre>
						</td>
					</tr>
					<tr>
						<td class="table-danger">5</td>
						<td class="table-danger">18/12/2021, 13:03:50</td>
						<td>
							<pre>PHP Fatal error:  Uncaught Error: Call to undefined function merge_settings() in <a href="vscode://file/E:\laragon\www\howtomake\log-viewer.php:74">E:\laragon\www\howtomake\log-viewer.php:74</a>
Stack trace:
#0 <a href="vscode://file/E:\laragon\www\howtomake\log-viewer.php:80">E:\laragon\www\howtomake\log-viewer.php(80)</a>: LogHandler-&gt;save_settings()
#1 <a href="vscode://file/E:\laragon\www\howtomake\log-viewer.php:70">E:\laragon\www\howtomake\log-viewer.php(70)</a>: LogHandler-&gt;get_settings()
#2 <a href="vscode://file/E:\laragon\www\howtomake\log-viewer.php:11">E:\laragon\www\howtomake\log-viewer.php(11)</a>: LogHandler-&gt;__construct()
#3 {main}
  thrown in <a href="vscode://file/E:\laragon\www\howtomake\log-viewer.php:74">E:\laragon\www\howtomake\log-viewer.php on line 74</a></pre>
						</td>
					</tr> -->
				</tbody>
			</table>

			<div id="loader" class="sk-cube-grid hide">
				<div class="sk-cube sk-cube1"></div>
				<div class="sk-cube sk-cube2"></div>
				<div class="sk-cube sk-cube3"></div>
				<div class="sk-cube sk-cube4"></div>
				<div class="sk-cube sk-cube5"></div>
				<div class="sk-cube sk-cube6"></div>
				<div class="sk-cube sk-cube7"></div>
				<div class="sk-cube sk-cube8"></div>
				<div class="sk-cube sk-cube9"></div>
			</div>
		</div>
	</div>

	<script>
		const toLower = text => {
			return text.toString().toLowerCase()
		}

		const searchByName = (items, term) => {
			if (term) {
				var regex = new RegExp(toLower(term), 'gim');
				return items.filter(item => regex.test(item.msg));
				//return items.filter(item => toLower(item.msg).includes(toLower(term)))
			}
			return items;
		}

		const App = {
			currentSort: 'time',
			currentSortOrder: 'desc',
			search: null,
			rowsRaw: [],
			rowsDisplay: [],
			filesize: 0,
			loading: false,
			delete: '',
			autoreload: true,
			error: false,
			errorMessage: 'Something went wrong',
			documentHidden: false,

			mounted() {
				this.update();
				setInterval(() => this.update(), 4000);
				$(document).on('visibilitychange', () => {
					this.documentHidden = document.hidden;
				});
			},

			loader: {
				show: () => {
					App.loading = true;
					$('#loader').removeClass('hide')
				},
				hide: () => {
					App.loading = false;
					$('#loader').addClass('hide')
				}
			},

			empty: {
				show: () => $('#no-entries').show(),
				hide: () => $('#no-entries').hide()
			},

			update(force = false) {
				if (!force && (!this.autoreload || this.documentHidden)) {
					console.log('autoreload is disabled or the window is hidden');
					return;
				}

				if (this.loading == true) {
					console.log('looks like loading didn\'t finish yet.');
					return;
				}

				this.loading = true;
				this.getFilesize(data => {
					const size = data
					if (typeof data == 'string') {
						console.log('something went wrong...')
						this.rowsDisplay = [];
						this.error = true;
						this.errorMessage = data;
						this.loading = false
						this.filesize = 0;
						return
					}

					if (size != this.filesize) {
						this.setFilesize(size);
						this.getLog(this);
					} else {
						console.log('nothing changed')
						this.loading = false
					}
				})
			},

			setFilesize(size) {
				this.filesize = size;
				$('#filesize').html(this.readableFilesize());
			},

			readableFilesize: function() {
				if (this.filesize > (1024 * 1024)) // filesize is in bytes
					return (Math.round(this.filesize / 1024 / 102) / 10) + ' MB';
				else
					return (Math.round(this.filesize / 102) / 10) + ' KB';
			},

			readableDateTime(dateTimeString) {
				let date = new Date(dateTimeString);
				return isNaN(date) ? dateTimeString : date.toLocaleString();
			},

			highlightLog(log) {

				const decodeHTML = html => {
					const txt = document.createElement('textarea');
					txt.innerHTML = html;
					return txt.value;
				};

				log = decodeHTML(log);
				log = $(`<div>${log}</div>`);
				log.highlight(/("[^"]*")/g, 'text-string');
				log.highlight(/(?<![\w])('[^']*')/g, 'text-string');
				log.highlight(/\b([0-9]+|true|false|null)\b/g, 'text-number');

				return log.html();
			},

			getFilesize(cb) {
				$.getJSON('/log-viewer.php', {
					filesize: null
				}, data => cb(data));
			},

			getLog() {
				$.getJSON('/log-viewer.php', {
					get_log: null
				}, data => this.setNewData(data));
			},

			setNewData(data) {
				this.rowsRaw = data
				this.loading = false
				this.rowsDisplay = this.filterSearch();
				this.rowsDisplay.sort(this.compareEntries());
				this.setLog();
			},

			setLog() {
				const $tbl = $('#table-rows');
				$tbl.html('');
				this.empty.show();
				if (!this.rowsDisplay.length) {
					this.loading = false;
					return;
				}
				const logs = [];
				$.each(this.rowsDisplay, (i, row) => {
					const $tr = $('<tr>');
					let cls = row.cls.toLowerCase()
						.replace('error', 'danger')
						.replace(/\b(\w)/g, 'table-$1'),
						log = this.highlightLog(row.msg);

					logs.push(log);
					cls = !cls.match(/-danger|-warning/) ? 'table-info' : cls;

					$tr.append($('<td />', {
						html: row.cnt,
						class: cls
					})).append($('<td />', {
						html: this.readableDateTime(row.time),
						class: cls
					})).append($('<td />', {
						html: `<pre>${log}</pre>`
					}));
					$tbl.append($tr);
				});

				this.loading = false;
				this.empty.hide();
			},

			filterSearch() {
				if (this.search == "") {
					return this.rowsRaw
				} else {
					return searchByName(this.rowsRaw, this.search)
				}
			},

			searchTable() {
				this.rowsDisplay = this.filterSearch();
			},

			compareEntries() {
				const sortBy = this.currentSort,
					multiplier = this.currentSortOrder === 'desc' ? -1 : 1;
				return (a, b) => {
					const aAttr = a[sortBy];
					const bAttr = b[sortBy];
					if (aAttr === bAttr) {
						return 0
					} else if (typeof aAttr === 'number' && typeof bAttr === 'number') {
						return (aAttr - bAttr) * multiplier // numerical compare, negate if descending
					}
					return String(aAttr).localeCompare(String(bAttr)) * multiplier;
				}
			},

			deleteLog() {
				$.get('/log-viewer.php', {
					delete_log: null
				}, data => (this.delete = data));
				this.rowsDisplay = [];
				this.setFilesize(0);
				this.setLog();
			},

			getList() {
				this.loader.show();

				$.getJSON('/log-viewer.php', {
					find_logs: null
				}, data => {
					const $menu = $('#select-menu');

					$menu.html('');
					if (data.file_list.length) {
						$.each(data.file_list, (i, file) => {
							const $opt = $('<option \>', {
								html: file.replace(`${data.root}\\`, ''),
								value: file
							});

							if (file == data.file_path)
								$opt.prop('selected', true);

							$opt.appendTo($menu);
						});
					} else {
						$menu.append('<option \>', {
							html: 'No log files were found!',
							selected: null
						});
					}

					this.update(true);
					this.loader.hide();
				});
			},

			setFile(file) {
				$.get('/log-viewer.php', {
					file_path: file
				});
				this.update();
			}
		}

		if (!$('#select-menu option[value]').length) App.getList();
		$('#refresh-btn').on('click', () => {
			App.getList();
		});

		$('#trash-btn').on('click', () => {
			App.deleteLog();
		});

		$('#reload-switch').on('change', function() {
			App.autoreload = $(this).is(':checked');
		});

		$('#select-menu').on('change', function() {
			App.setFile($(':selected', this).val());
		});

		App.mounted();

		$.fn.highlight = function(pattern, cls) {

			this.each(function() {
				$(this).contents().each(function() {
					if (this.nodeType === 3 && pattern.test(this.nodeValue)) {
						$(this).replaceWith(this.nodeValue.replace(pattern, `<span class="${cls}">$1</span>`));
					} else if (!$(this).hasClass(cls)) {
						$(this).highlight(pattern, cls);
					}
				});
			});
			return this;
		};
	</script>

</body>

</html>

<?php
exit;

// echo json_encode(r_search(__DIR__ . '/', "/.*$name/"));

// $regPattern should be using regular expression
// function r_search($folder, $regPattern) {
// 	$dir = new RecursiveDirectoryIterator($folder);
// 	$ite = new RecursiveIteratorIterator($dir);
// 	$files = new RegexIterator($ite, $regPattern, RegexIterator::GET_MATCH);
// 	$fileList = array();
// 	foreach($files as $file) {
// 		$fileList = array_merge($fileList, $file);
// 	}
// 	return $fileList;
// }
