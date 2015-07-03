var cm = cm || {};

cm.PostWall = function(o) {

	this._options = {

		postSubmit : null,
		wallDiv : null,
		wallUl : null
		// need wall ul
	};

	cm.extend(this._options, o);
	cm.extend(this, cm.DisposeSupport);

	this._wallDiv = this._options.wallDiv;
	this._wallUl = this._options.wallUl;
	this._postSubmit = this._options.postSubmit;

	this._olderPostSwitch = new cm.MorePostsSwitch({
		mainDivId : 'more-post-div',
		type : 'older'
	});
	
	if (this._postSubmit != null) {
		var self = this;
		this._postSubmit._boss = self;
		this._postSubmit._wallUl = this._wallUl;
		this._postSubmit._setOnSuccess( this._addPost );
	}

	// make everything work
	this._shoutOrders();

	// make wallDiv appear
	$( this._wallDiv ).show('slow');

	if (qs.qsGet['plink'] != undefined) {
		$('#post-' + qs.qsGet['plink']).goTo();
	}
};

cm.PostWall.prototype = {

	// adds created post html to beginning of ul
	_addPost: function(data, f) {

		// clear post
		this._clearPost();

		if (data.error != 0) {
		  alert(data.error);
		}
		else {
		  $( this._wallUl ).prepend(data.html);
		  $( this._wallUl ).children().first().hide();
		  $( this._wallUl ).children().first().fadeIn('fast');

		  // function from a parent
		  this._boss._shoutOrders();
		}
	},
	_shoutOrders: function() {

		this._primeShowReplies();
		this._primeRequestReplies();
		this._primeReplyForms();
		this._primeDeletes();
		this._primeDeletePosts();
		this._primeShowReplies();
		this._primeMorePosts();
	},
	_primeShowReplies: function() {

		var self = this;

		$(".show_reply").off("submit");
		$(".show_reply").on("submit", function(e) {
			e.preventDefault();
			// check if replies have already been fetched
			var replies_div = $( e.target ).parent().siblings('div.replies');

			var rd_children = $( replies_div ).children('ul').children();

			if ( rd_children.length <= 4 ) {
				var postForm = $(this).serialize();
				var getReply = new Ajax({
					requestType: 'POST',
					requestUrl: cm.home_path + '/network_show_reply.php',
						requestParameters: ' ',
						data: postForm,
						dataType: 'string',
						sendNow: true
				}, function(data) {
					// success function
					var response = JSON.parse(data);
					if (response.error == 0) {
						var targ = e.target;
						$( targ ).children(':submit').val('Hide Replies');
						$( targ ).parent().siblings('div.replies').children('ul').html(response.html);
						self._primeDeletes();
					}
				}, function(response, rStatus) {

				});
			}
			else {
				if ($( e.target ).children(':submit').val() == 'Hide Replies') {
					$( replies_div ).hide();
					$( e.target ).children(':submit').val('Show Replies');
				}
				else {
					$( replies_div ).show();
					$( e.target ).children(':submit').val('Hide Replies');
					self._primeDeletes();
				}
			}
		});
	},
	_primeRequestReplies: function() {

		var self = this;

		$(".request_reply").off("submit");
		$(".request_reply").on("submit", function(e) {
			e.preventDefault();
			var postForm = $(this).serialize();
			if( $( e.target ).children('button').text() == 'Reply') {
				var requestReply = new Ajax({
					requestType: 'POST',
					requestUrl: cm.home_path + '/network_request_reply.php',
						requestParameters: ' ',
						data: postForm,
						dataType: 'string',
						sendNow: true
				}, function(data) {
					// success function
					var response = JSON.parse(data);
					if (response.error == 0) {
						var targ = e.target;
						$( e.target ).parents('li.network-post').children('div.prompt').show();
						$( targ ).children('button').text('Cancel');
						$( targ ).parents('li.network-post').children('div.prompt').html(response.html);
						self._primeReplyForms();
					}
				}, function(response, rStatus) {

				});
			}
			else {
				$( e.target ).parents('li.network-post').children('div.prompt').hide();
				$( e.target ).children('button').text('Reply');
			}
		});
	},
	_primeReplyForms: function() {
		var self = this;

		$( '.reply_form' ).off('submit');
		$( '.reply_form' ).on('submit', function(e) {
			// prevent default behavior
			e.preventDefault();
			// get target
			var targ = e.target;
			var postForm = $( targ ).serialize();

			var action = 'network_post_reply.php';

			// check for tweet
			if (postForm.indexOf('id_twitter') > -1)
				action = 'network_tweet_reply.php';

			var sendReply = new Ajax({
				requestType: 'POST',
				requestUrl: cm.home_path + '/' + action,
					requestParameters: ' ',
					data: postForm,
					dataType: 'string',
					sendNow: true
			}, function(data) {
				// success function
				var response = JSON.parse(data);
				if (response.error == 0) {
					// get div
					var targ = e.target;

					// put all lis out on display
					$( targ ).parents( 'li.network-post' ).children('div.replies').children('ul').html(response.html);

					// activate showreplies button, change to hide replies 
					// get ul
					// if there are over 4 of them
					if( $( targ ).parents( 'li.network-post' ).children('div.replies').children('ul').children().length > 4) {
						$( targ ).parents( 'div.prompt' ).siblings( 'div.show_reply_div' ).children('form.show_reply').show();
						$( targ ).parents( 'div.prompt' ).siblings( 'div.show_reply_div' ).children('form.show_reply').children(':submit').val('Hide Replies');
					}

					// increment replies
					//  a) get reply count
					var replyCount = $( targ ).parents( 'div.prompt' ).siblings('div.replies').children('ul.replies').children().length;
					//  b) increment in obvious place
					$( targ ).parents( 'div.prompt' ).siblings( 'div.post-info' ).children('p.reply_count').text(replyCount + ' replies');
					//  c) increment in hidden submit
					$( targ ).parents( 'div.prompt' ).siblings( 'div.post-info' ).children('form.post_delete').children("input[name='replies']").val(replyCount)
					//  d) increment at top of page

					// turn tweets into other things
					//
					// add id_twitter
					if (postForm.indexOf('id_twitter') > -1) {
						// request reply form change
						$( targ ).parents( 'div.prompt' ).siblings('div.post-info').children("div.reply-button").children("form.request_reply").children("input[name='tid']").val(response.id_cmtweet);
						// reply form change
						$( targ ).parents( 'div.prompt' ).children('form.reply_form').children("input[name='id_cmtweet']").val(response.id_cmtweet);
					}

					// activate delete buttons
					self._primeDeletes();

					// clear out reply thing and change text
					$( targ ).parents( 'div.prompt' ).siblings( 'div.post-info' ).children('div.reply-button').children('form.request_reply').children('button').text('Reply');
					$( targ ).parents( 'div.prompt' ).hide();
				}
			}, function(response, rStatus) {

			});
		});
	},
	_primeDeletes: function() {

		// event handler
		$( '.delete_reply' ).off('submit');
		$( '.delete_reply' ).on('submit', function(e) {
			// prevent default behavior
			e.preventDefault();
			// get target

			if (confirm("Are you sure you want to delete this reply?")) {
				var targ = e.target;
				var postForm = $( targ ).serialize();

				var action = 'network_reply_delete.php';

				if (postForm.indexOf('tid') > -1)
					action = 'network_tweet_reply_delete.php';

				var sendReply = new Ajax({
					requestType: 'POST',
					requestUrl: cm.home_path + '/' + action,
						requestParameters: ' ',
						data: postForm,
						dataType: 'string',
						sendNow: true
				}, function(data) {
					// success function
					var response = JSON.parse(data);
					
					if (response.error == 0) {
						// delete parent li
						// get form
						var targ = e.target;
						var ul = $( targ ).parents('ul.replies');

						$( targ ).parents('li.reply').remove();

						// delete whole post
						if (response.status == 'postdelete') {
							$( ul ).parents('li.network-post').remove();
						}
						else {
							//var children = ul.children();
							//var replyCount =  children.length;
							var replyCount = ul.children().length;
							// update reply counts elsewhere
							// post info div
							var div = $( ul ).parents('div.replies').siblings('div.post-info');

							//  1) hidden input on delete post
							$( div ).children('div.reply-button.delete').children('form.post_delete').children("input[name|='replies']").val(replyCount);
							//  2) reply count
							$( div ).children('p.reply_count').text(replyCount + ' replies');

							// update 
							// if ul is now less than 4...
							if ( replyCount <= 4 ) {
								// hide show replies form
								$( ul ).parents('div.replies').siblings('div.show_reply_div').children('form.show_reply').children(':submit').val('Show Replies');
								$( ul ).parents('div.replies').siblings('div.show_reply_div').children('form.show_reply').hide();

								/*
								$( div ).children('div.reply-button').children('form.show_reply').children(':submit').val('Show Replies');
								$( div ).children('div.reply-button').children('form.show_reply').hide();
								*/
							}	
						}
					}
				}, function(response, rStatus) {

				});
			}
		});
	},
	_primeDeletePosts: function() {
		var self = this;

		$('.post_delete').off('submit');
		$('.post_delete').on('submit', function(e) {
			// prevent default behavior 
			e.preventDefault();

			if (confirm("Are you sure you want to delete this post?")) {
				var postForm = $( e.target ).serialize();

				postDelete = new Ajax({
					requestType: 'POST',
					requestUrl: cm.home_path + '/network_post_delete.php',
						requestHeaders: ' ',
						data: postForm,
						dataType: 'string',
						sendNow: true
				}, function(data) {
					var response = JSON.parse(data);

					if (response.error == 0) {
						var targ = $( e.target );
						if (response.status == 'destroyed') {
							// remove li 
							targ.parents('li.network-post').remove();
							// decrement number of posts up top
						}

						if (response.status == 'wiped') {
							targ.parents('li.network-post').replaceWith(response.html);
							self._shoutOrders();
						}
					}
				}, function(response, rStatus) {

				});
			}
		});
	},
	_primeMorePosts: function() {
		var self = this;
		var olderPostSwitch = this._olderPostSwitch;

		$( ".more_posts" ).unbind('submit');
		$('.more_posts').on('submit', function(e) {
			
			// prevent default
			e.preventDefault();

			// get form
			var targ = $(e.target);
			var postForm = $( targ ).serialize();

			var morePostsForm = $( targ );
			var moreTweets = $( morePostsForm ).children("input[name='nmp_more_tweets']").val();
			var morePosts = $( morePostsForm ).children("input[name='nmp_more_posts']").val();

			var operation = 'network_more_posts.php';
			var handlingTweets = false;

			if (moreTweets == "1" && morePosts != "1") {
				handlingTweets = true;
				operation = 'network_more_tweets.php';
			}
			
			var requestPosts = new Ajax({
					requestType: 'POST',
					requestUrl: cm.home_path + '/' + operation,
					requestHeaders: ' ',
					data: postForm,
					dataType: 'string',
					sendNow: true
			}, function(data) {
				var response = JSON.parse(data);
				// add stuff to post wall
				$("#post-wall-ul").append(response.html);

				if (handlingTweets) {

					// update more posts stuff
					olderPostSwitch._updateAll(response.postSwitchValues);
				}

				// with new stuff, call primes
				self._shoutOrders();

				$('#nmp_more_posts').val(response['nmp_more_posts']);

				if(response['continue'] == 'n') {
					$( targ ).hide();
				}
				else {
					$('#nmp_lb').val(response['lb']);
				}
				
			}, function() {
			});
		});
	}
};

cm.MorePostsSwitch = function(o) {

	this._options = {

		mainDivId : 'more-post-div',
		type: 'older'
	};

	cm.extend(this._options, o);
	cm.extend(this, cm.DisposeSupport);

	// get div
	this.mainDiv = document.getElementById( this._options.mainDivId );

	this.nmp_form;

	this._inputs = {
		nmp_lb : null,
		nmp_ub : null,
		nmp_nid : null,
		nmp_tweet_until_date : null,
		nmp_initial: null,
		nmp_more_posts : null,
		nmp_more_tweets : null,
		nmp_last_updated : null,
		nmp_cur_location_scope : null,
		nmp_max_location_scope : null,
		nmp_cur_origin_scope : null,
		nmp_max_origin_scope : null
	};

	this._activate();
};

cm.MorePostsSwitch.prototype = {

	_activate: function() {

		this.nmp_form = $( this.mainDiv ).children('form.more_posts');

		$( this.nmp_form ).children('input');

		var keys = cm.objectKeys( this._inputs );

		// assign all things to input
		for (var i = 0; i < keys.length; i++) {
			var cur_key = keys[i];
			this._inputs[cur_key] = $( this.nmp_form ).children("input[name='" + cur_key + "']");
		}
	},
	_updateValue: function(element, value) {
		$( this._inputs[element] ).val( value );
	},
	_updateAll: function(values) {

		var keys = cm.objectKeys( values );

		for (var i = 0; i < keys.length; i++) {
			
			var cur_key = keys[i];
			this._updateValue(cur_key, values[cur_key]);
		}
	}
};
