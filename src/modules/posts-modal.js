// Custom modal handler
import {
	check,
	uncheck,
	notification
} from './helpers';
export default function getPostsModal(obj) {

	const html = document.querySelector('html');
	const modal = document.querySelector(obj.modal_el);
	const open_modal_btn = document.querySelector(obj.open_modal_btn);
	const close_modal_btn = modal.querySelector('.wdss-modal-header i.fa-times');

	let total_posts = wdss_localize.total_post_count;
	let total_pages_info = Math.ceil(total_posts / 100);
	window.is_setup_stage = true;

	function Init() {
		const modal_body = modal.querySelector('.wdss-modal-body');
		const info_panel = modal.querySelector('.wdss-modal-informaion-panel');
		const context = document.querySelector('#wdss-exclude-posts-table tbody');

		const execute_btn = modal.querySelector('.wdss-button.submit');
		const toggle_all_btn = modal.querySelector('.wdss-button.toggle-all');
		const get_posts_btn = modal.querySelector('.wdss-button.get-posts');

		const total_posts_text = modal.querySelector('.wdss-modal-posts-count');
		let total_posts_text_number = total_posts_text.querySelector('strong');

		const welcome_msg = modal.querySelector('.wdss-modal-welcome-msg');
		const not_found_msg_template = '<span class="wdss-modal-not-found-msg">No results...</span>';
		const load_more_btn_template = '<button type="button" class="wdss-button load-more">Fetch next page</button>';

		const completed_msg_template = '<span class="msg successful">Completed!<br><small>Please wait several minutes while changes are implementing</small></span>';
		const error_msg_template = '<span class="msg error">An Error occured!<br><smallLook in console for more details</small></span>';

		const modal_title = modal.querySelector('.wdss-modal-title');
		const modal_title_value = 'Delete Broken Featured Images';

		let load_more_btn;
		let fetched_posts = [];
		let is_lite_mode = false;
		let max_posts_per_fetch;
		let min_posts_per_fetch = 100;

		if(modal_title.childNodes.length < 1) {
			modal_title.insertAdjacentHTML('afterbegin', modal_title_value);
		}

		if(window.next_fetched_page === undefined) {
			notification('Enter max posts per fetch count. The minimum is 100: ', 'prompt', openModal);
			console.log(`Total fetchable pages: ${total_pages_info}`);
		}
		else {
			notification('Please, reload the page first', 'info');
		}

		function openModal(set_max_posts_per_fetch) {
			max_posts_per_fetch = set_max_posts_per_fetch >= min_posts_per_fetch ? set_max_posts_per_fetch : 800;

			const lite_mode_msg_template = `<small>Lite-mode: max ${max_posts_per_fetch} posts per fetches</small>`;

			if (total_posts > max_posts_per_fetch) {
				is_lite_mode = true;
				if (modal_title) {
					modal_title.insertAdjacentHTML('afterend', lite_mode_msg_template);
				}
			}
			if (is_lite_mode) total_posts = max_posts_per_fetch;
		
			window.next_fetched_page = Math.ceil(total_posts / 100);

			modal.classList.add('active');
			html.classList.add('fixed');
		}

		close_modal_btn.addEventListener('click', closeModal);

		function closeModal() {
			modal.classList.remove('active');
			html.classList.remove('fixed');
			let notifications = Array.from(document.querySelectorAll('.notification'));
			notifications.forEach(notification =>  {
				notification.remove();
			})
		}
		document.onkeydown = ((e) => e.key === 'Esc' || e.key === 'Escape' ? closeModal() : null);

		function getPostsFromTable() {
			return Array.from(document.querySelectorAll('.wdss-table-row.post'));
		}

		function updateFetchedPostsNumber() {
			let total_posts_amount = getPostsFromTable().length;

			total_posts_text.classList.add('active');
			if (total_posts_text_number) {
				return total_posts_text_number.innerHTML = total_posts_amount;
			}
			return;
		}


		function toggleAllCheckboxes() {
			let posts = getPostsFromTable();
			posts.forEach(post => {
				let checkbox = post.querySelector('.wdss-table-post__select input[type="checkbox"]');
				if (checkbox.hasAttribute('checked')) {
					uncheck(checkbox);
				} else {
					check(checkbox);
				}
			});
		}


		function fetchMorePostsHandler() {

			toggle_all_btn.addEventListener('click', toggleAllCheckboxes);

			if (total_pages_info === window.next_fetched_page) {
				notification('You achived the last page', 'info');
				load_more_btn.classList.add('inactive');
				return;
			}

			let not_found_msg = modal.querySelector('.wdss-modal-not-found-msg');
			if (not_found_msg) not_found_msg.remove();

			load_more_btn.classList.add('inactive');
			modal_body.classList.add('loading');
			toggle_all_btn.classList.add('inactive');
			execute_btn.classList.add('inactive');

			try {
				fetch(document.location.origin + `/wp-json/wp/v2/posts?per_page=100&page=${window.next_fetched_page}`)
					.then(response => {
						return response.json();
					})
					.then(data => {

						let data_obj = {
							fetched_list: JSON.stringify(data),
							action: obj.fetch_action
						};
						data_obj[obj.fetch_nonce_name] = obj.fetch_nonce_value;

						jQuery.ajax({
							url: wdss_localize.url,
							type: 'post',
							data: data_obj,
							success: function(response) {
								console.log(`Current fetched page: ${window.next_fetched_page}`);
								window.next_fetched_page++;

								if (window.next_fetched_page < total_pages_info) {
									console.log(`Next page to fetch: ${window.next_fetched_page}`);
								} else {
									console.log(`This is the last page to fetch`);
								}

								let info_msg = Array.from(modal.querySelectorAll('.msg'));

								if (info_msg) {
									info_msg.forEach(msg => msg.remove());
								}

								modal_body.classList.remove('loading');
								load_more_btn.classList.remove('inactive');
								toggle_all_btn.classList.remove('inactive');
								execute_btn.classList.remove('inactive');

								if (response) {
									context.insertAdjacentHTML('beforeend', response);
									if (not_found_msg) not_found_msg.remove();
								} else {
									info_panel.insertAdjacentHTML('afterbegin', not_found_msg_template);
									toggle_all_btn.classList.add('inactive');
									execute_btn.classList.add('inactive');
								}

								updateFetchedPostsNumber();
							}
						});
					})
			} catch (e) {
				console.log(e);
			}
		}

		async function fetchPostsMainHandler() {
			modal_body.classList.add('loading');

			function fetchPosts() {
				let promises = []

				for (let i = 1; i <= window.next_fetched_page; i++) {
					try {
						promises.push(new Promise((resolve, reject) => {
							fetch(document.location.origin + `/wp-json/wp/v2/posts?per_page=100&page=${i}`)
								.then(response => {
									return response.json();
								})
								.then(data => {
									fetched_posts = fetched_posts.concat(data);
									resolve();
								})
								.catch(error => {
									console.log(error);
									reject();
								});
						}));
					} catch (e) {
						break;
					}
				}
				window.next_fetched_page++;
				if (window.next_fetched_page < total_pages_info) {
					console.log(`Next page to fetch: ${window.next_fetched_page}`);
				} else {
					console.log(`This is the last page to fetch`);
				}

				return Promise.allSettled(promises);
			}

			await fetchPosts(window.next_fetched_page);
			console.log(`Fetched posts in first query: ${fetched_posts.length}`);

			let data_obj = {
				fetched_list: JSON.stringify(fetched_posts),
				action: obj.fetch_action
			};
			data_obj[obj.fetch_nonce_name] = obj.fetch_nonce_value;

			jQuery.ajax({
				url: wdss_localize.url,
				type: 'post',
				data: data_obj,
				success: function(response) {
					modalHandler.call(context, response);
					modal_body.classList.remove('loading');
					get_posts_btn.classList.add('inactive');
				}
			});
		}

		get_posts_btn.addEventListener('click', getPostsHandler);

		function getPostsHandler() {

			let notification_message = modal.querySelector('.msg');
			if (notification_message) notification_message.remove();

			get_posts_btn.classList.add('inactive');
			welcome_msg.classList.remove('active');

			let not_found_msg = modal.querySelector('.wdss-modal-not-found-msg');
			if (not_found_msg) not_found_msg.remove();

			console.log(`Current fetched pages: ${window.next_fetched_page}`);
			window.next_fetched_page = Math.ceil(total_posts / 100);
			fetchPostsMainHandler();
		}

		function modalHandler($content) {
			this.insertAdjacentHTML('beforeend', $content);
			updateFetchedPostsNumber();

			let posts_list = getPostsFromTable();

			if (is_lite_mode && !modal.querySelector('.wdss-button.load-more')) {
				get_posts_btn.insertAdjacentHTML('beforebegin', load_more_btn_template);
				load_more_btn = modal.querySelector('.wdss-button.load-more');
			}

			if (load_more_btn) {
				load_more_btn.addEventListener('click', function() {
					fetchMorePostsHandler();
				});
			}

			function clearAll() {
				execute_btn.classList.add('inactive');
				toggle_all_btn.classList.add('inactive');
				total_posts_text.classList.remove('active');
				total_posts_text_number.innerHTML = '';

				let table_rows = Array.from(modal.querySelectorAll('.wdss-table-row.post'));

				table_rows.forEach(row => {
					row.parentNode.removeChild(row);
				});
			}

			if (posts_list.length > 0) {
				posts_list.forEach(post => {
					post.addEventListener('click', () => {
						let checkbox = post.querySelector('.wdss-table-post__select input[type="checkbox"]');
						if (checkbox.hasAttribute('checked')) {
							uncheck(checkbox);
						} else {
							check(checkbox);
						}
					});
				});

				get_posts_btn.classList.add('inactive');
				execute_btn.classList.remove('inactive');
				toggle_all_btn.classList.remove('inactive');

				toggle_all_btn.addEventListener('click', toggleAllCheckboxes);
			} else {
				info_panel.insertAdjacentHTML('afterbegin', not_found_msg_template);
				toggle_all_btn.classList.add('inactive');
				execute_btn.classList.add('inactive');
			}

			execute_btn.addEventListener('click', () => {
				const proceded_posts = Array.from(modal.querySelectorAll('.wdss-table-post__select input[type="checkbox"]:checked'));
				let proceded_posts_ids_arr = [];
				let proceded_posts_ids;

				if (!proceded_posts.length) {
					notification('Please, select the posts which will be proceded', 'info');
					return;
				}
				if (!confirm('Are you ready to start?')) return;

				get_posts_btn.classList.remove('inactive');
				proceded_posts.forEach(post => {
					proceded_posts_ids_arr.push(post.value);
				});

				proceded_posts_ids = proceded_posts_ids_arr.join(',');
				console.log(`Selected IDs: ${proceded_posts_ids}`);
				clearAll();

				let data_obj = {
					selected_list: JSON.stringify(proceded_posts_ids),
					action: obj.post_action
				};
				data_obj[obj.post_nonce_name] = obj.post_nonce_value;

				jQuery.ajax({
					url: wdss_localize.url,
					type: 'post',
					data: data_obj,
					success: function() {
						get_posts_btn.classList.add('inactive');
						info_panel.insertAdjacentHTML('afterbegin', completed_msg_template);
					},
					error: function(error) {
						info_panel.insertAdjacentHTML('afterbegin', error_msg_template);
						console.log(error);
					}
				});
			});
		}
	}

	if (open_modal_btn) {
		open_modal_btn.addEventListener('mousedown', Init);
	}
}