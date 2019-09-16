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

	this.post_template = document.getElementById('post-template').innerHTML;
	this.tweet_template = document.getElementById('tweet-template').innerHTML;
	this.reply_template = document.getElementById('reply-template').innerHTML;

	this._posts = [];
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
	//this._shoutOrders();

	// make wallDiv appear
	$( this._wallDiv ).show('slow');

	if (qs.qsGet) {
		if (qs.qsGet['plink'] != undefined) {
			$('#post-' + qs.qsGet['plink']).goTo();
		}
	}

	this._init();
};

cm.PostWall.prototype = {

	_init: function() {

		var self = this;

		// delete posts
		//this._primeDeletePosts();
		$('.post_delete').on('submit', function(e) {
			self._deletePost(e);
		}); 

		// show post replies
		//this._primeShowReplies();
		$(".show_reply").on("submit", function(e) {
			self._showReplies(e)
		});

		// more post request
		//this._primeMorePosts();
		$('.more_posts').on('submit', function(e) {
			self._fetchMorePosts(e);
		});

		// reply forms
		//this._primeReplyForms();
		$( '.reply_form' ).on('submit', function(e) {
			self._postAReply(e);
		}); 

		// delete replies 
		//this._primeDelete();
		$( '.delete_reply' ).on('submit', function(e) {
			self._deleteReply(e)
		});

		// show replies
		//this._primeShowReplies();

		// post javascript stuff 
		//this._primePostInputBehavior();
	},
	_getPost: function(target) {
		var post = $( target ).parents( '.network-post' )[0];
		return this._scrunchPost( post );
	},
	_getTweet: function() {
		var post = $( target ).parents( '.network-post' )[0];
		return this._scrunchPost( post );
	},
	_getReply: function() {
		var post = $( target ).parents( '.network-post' )[0];
		return this._scrunchPost( post );
	},
	_getReplyParent: function(target) {
		var post = $( target ).parents( '.network-post' )[0];
		return this._scrunchPost( post );
	},
	_postAReply: function(e) {

		e.preventDefault();

		var self = this;

		// get target
		var targ = e.target;
		var postForm = $( targ ).serialize();

		var parent_post = this._getReplyParent( targ );

		var action = 'network_post_reply.php';

		// check for tweet
		if (postForm.indexOf('id_twitter') > -1) {
			action = 'network_tweet_reply.php';
		}

		var sendReply = new cm.Ajax({
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
				//$( targ ).parents( 'li.network-post' ).children('div.replies').children('ul').html(response.html);
				$( parent_post['replies-ul'] ).html(response.html);

				// activate showreplies button, change to hide replies 
				// get ul
				// if there are over 4 of them
				//if( $( targ ).parents( 'li.network-post' ).children('div.replies').children('ul').children().length > 4) {
				//	$( targ ).parents( 'div.prompt' ).siblings( 'div.show_reply_div' ).children('form.show_reply').show();
				//	$( targ ).parents( 'div.prompt' ).siblings( 'div.show_reply_div' ).children('form.show_reply').children(':submit').val('Hide Replies');
				//}
				if( $( parent_post['replies-ul'] ).children().length > 4) {
					$( parent_post['show_reply']).show();
					$( parent_post['show_reply_button']).val('Hide Replies');
				}

				// increment replies
				//  a) get reply count
				//var replyCount = $( targ ).parents( 'div.prompt' ).siblings('div.replies').children('ul.replies').children().length;
				var replyCount = $( parent_post['replies-ul'] ).children().length;
				//  b) increment in obvious place
			//	$( targ ).parents( 'div.prompt' ).siblings( 'div.post-info' ).children('p.reply_count').text(replyCount + ' replies');
				$( parent_post['reply_count'] ).text( replyCount + ' replies' ); 
				//  c) increment in hidden submit
				//$( targ ).parents( 'div.prompt' ).siblings( 'div.post-info' ).children('form.post_delete').children("input[name='replies']").val(replyCount)
				$( parent_post['reply_count']).val(replyCount);

				//  d) increment at top of page

				// turn tweets into other things
				//
				// add id_twitter
				if (postForm.indexOf('id_twitter') > -1) {
					// request reply form change
					//$( targ ).parents( 'div.prompt' ).siblings('div.post-info').children("div.reply-button").children("form.request_reply").children("input[name='tid']").val(response.id_cmtweet);
					// reply form change
					//$( targ ).parents( 'div.prompt' ).children('form.reply_form').children("input[name='id_cmtweet']").val(response.id_cmtweet);
					$( parent_post['input_cmtweet']).val( response.id_cmtweet );
				}

				// activate delete buttons
				self._primeDeleteReply();

				// clear out reply thing and change text
				//$( targ ).parents( 'div.prompt' ).siblings( 'div.post-info' ).children('div.reply-button').children('form.request_reply').children('button').text('Reply');
				//$( targ ).parents( 'div.prompt' ).hide();
				$( parent_post['reply-text'] ).val('');
			}
		}, function(response, rStatus) {

		});
	},
	_showReplies: function(e) {

		var self = this;
		e.preventDefault();

		// get post_map
		var post_map = this._getPost( e.target );

		/*
		// check if replies have already been fetched
		var replies_div = $( e.target ).parent().siblings('div.replies');

		var rd_children = $( post_map['replies'] ).children('ul').children();
		*/

		if ( $( post_map['replies-ul'] ).children().length <= 4 ) {
			var postForm = $( e.target ).serialize();
			var getReply = new cm.Ajax({
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
					//$( targ ).children(':submit').val('Hide Replies');
					$( post_map['show_replies_submit'] ).val('Hide Replies');

					//$( targ ).parent().siblings('div.replies').children('ul').html(response.html);
					$( post_map['replies-ul'] ).html( response.html );
					self._primeDeleteReply();
				}
			}, function(response, rStatus) {

			});
		}
		else {
			//if ($( e.target ).children(':submit').val() == 'Hide Replies') {
			if ($( post_map['show_replies_submit'] ).val() == 'Hide Replies') {
				//$( replies_div ).hide();
				$( post_map['replies-div'] ).slideUp();
				//$( e.target ).children(':submit').val('Show Replies');
				$( post_map['show_replies_submit'] ).val('Show Replies');
			}
			else {
				//$( replies_div ).show();
				$( post_map['replies-div'] ).slideDown();
				//$( e.target ).children(':submit').val('Hide Replies');
				$( post_map['show_replies_submit'] ).val('Hide Replies');
				self._primeDeleteReply();
			}
		}
	},
	_deletePost: function(e) {

		e.preventDefault();
		
		// not generating a map for this one
		//  because the post is easily found in this situation

		if (confirm("Are you sure you want to delete this post?")) {
			var postForm = $( e.target ).serialize();

			postDelete = new cm.Ajax({
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
	},
	_deleteReply: function(e) {

		e.preventDefault();

		var parent_post = this._getReplyParent( e.target );

		if (confirm("Are you sure you want to delete this reply?")) {
			var targ = e.target;
			var postForm = $( targ ).serialize();

			var action = 'network_reply_delete.php';

			if (postForm.indexOf('tid') > -1)
				action = 'network_tweet_reply_delete.php';

			var sendReply = new cm.Ajax({
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
						/*
						//var children = ul.children();
						//var replyCount =  children.length;
						// update reply counts elsewhere
						// post info div
						var div = $( ul ).parents('div.replies').siblings('div.post-info');

						//  1) hidden input on delete post
						$( div ).children('div.reply-button.delete').children('form.post_delete').children("input[name|='replies']").val(replyCount);
						//  2) reply count
						$( div ).children('p.reply_count').text(replyCount + ' replies');
						*/
						var replyCount = $( parent_post['replies-ul'] ).children.length;

						// update 
						// if ul is now less than 4...
						if ( replyCount <= 4 ) {
							// hide show replies form
							//$( ul ).parents('div.replies').siblings('div.show_reply_div').children('form.show_reply').children(':submit').val('Show Replies');
							//$( ul ).parents('div.replies').siblings('div.show_reply_div').children('form.show_reply').hide();
							$( parent_post['show_replies_submit'] ).val('Show Replies');
							$( parent_post['show_replies_submit'] ).hide();


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
	},
	_fetchMorePosts: function(e) {

			e.preventDefault();
			
			var self = this;

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
			
			var requestPosts = new cm.Ajax({
					requestType: 'POST',
					requestUrl: cm.home_path + '/' + operation,
					requestHeaders: ' ',
					data: postForm,
					dataType: 'string',
					sendNow: true
			}, function(data) {
				var response = JSON.parse(data);

				// add stuff to post wall
				if (response.error == "Success") {
					$("#post-wall-ul").append(response.html);
				}
				else {
					$("#post-wall-nmp-error").text("Cannot find more posts.");
					$("#post-wall-nmp-error").show();
					$("#post-wall-nmp-error").hide(1000);
				}

				if (handlingTweets) {

					// update more posts stuff
					self._olderPostSwitch._updateAll(response.postSwitchValues);
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
				// NOTHING!!!
			});
	},
	// adds created post html to beginning of ul
	_addPost: function(data, f) {

		// clear post
		this._clearPost();

		if (data.error != 0) {
		  alert(data.error);
		}
		else {
		  $( this._wallUl ).prepend(data.html);
		  //$( this._wallUl ).children().first().hide();
		  //$( this._wallUl ).children().first().fadeIn('fast');

		  // function from a parent
		  this._boss._shoutOrders();
		}
	},
	_shoutOrders: function() {

		this._primeShowReplies();
		this._primeRequestReplies();
		this._primeReplyForms();
		this._primeDeleteReply();
		this._primeDeletePosts();
		this._primeShowReplies();
		this._primeMorePosts();
		this._primePostInputBehavior();
	},
	_primePostInputBehavior: function() {

		/*
		$("textarea.post-text, textarea.reply-text").off("change");
		$("textarea.post-text, textarea.reply-text").on("change", function(e) {

			e.preventDefault();

			function resize() {
				
			}

			alert( $(e.target).attr('rows'));
		});
		*/
	},
	_primeShowReplies: function() {

		var self = this;

		$(".show_reply").off("submit");
		$(".show_reply").on("submit", function(e) {

			self._showReplies(e);
			/*
			e.preventDefault();
			// check if replies have already been fetched
			var replies_div = $( e.target ).parent().siblings('div.replies');

			var rd_children = $( replies_div ).children('ul').children();

			if ( rd_children.length <= 4 ) {
				var postForm = $(this).serialize();
				var getReply = new cm.Ajax({
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
			*/
		});
	},
	_primeRequestReplies: function() {

		var self = this;

		$(".request_reply").off("submit");
		$(".request_reply").on("submit", function(e) {
			e.preventDefault();
			var postForm = $(this).serialize();
			if( $( e.target ).children('button').text() == 'Reply') {
				var requestReply = new cm.Ajax({
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
						self._primePostInputBehavior();
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

			self._postAReply(e);
			/*
			// prevent default behavior
			e.preventDefault();
			// get target
			var targ = e.target;
			var postForm = $( targ ).serialize();

			var action = 'network_post_reply.php';

			// check for tweet
			if (postForm.indexOf('id_twitter') > -1)
				action = 'network_tweet_reply.php';

			var sendReply = new cm.Ajax({
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
				else {
					var targ = e.target;
					var error_span = $( targ ).children( '.reply-error' );

					// erase error span text and show 
					$( error_span ).text('');
					$( error_span ).show();

					// set error message
					$( error_span ).text( response.error ).delay(2000).fadeOut('slow');
				}

			}, function(response, rStatus) {

			});
			*/
		});
	},
	_primeDeleteReply: function() {

		var self = this;

		// event handler
		$( '.delete_reply' ).off('submit');
		$( '.delete_reply' ).on('submit', function(e) {
			
			self._deleteReply(e);
			/*
			// prevent default behavior
			e.preventDefault();
			// get target

			if (confirm("Are you sure you want to delete this reply?")) {
				var targ = e.target;
				var postForm = $( targ ).serialize();

				var action = 'network_reply_delete.php';

				if (postForm.indexOf('tid') > -1)
					action = 'network_tweet_reply_delete.php';

				var sendReply = new cm.Ajax({
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
			/*
							}	
						}
					}
				}, function(response, rStatus) {

				});
			}
			*/
		});
	},
	_primeDeletePosts: function() {
		var self = this;

		$('.post_delete').off('submit');
		$('.post_delete').on('submit', function(e) {
			// prevent default behavior 
		//	e.preventDefault();

			self._deletePost(e);
			/*
			if (confirm("Are you sure you want to delete this post?")) {
				var postForm = $( e.target ).serialize();

				postDelete = new cm.Ajax({
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
			*/
		});
	},
	_primeMorePosts: function() {
		var self = this;
		var olderPostSwitch = this._olderPostSwitch;

		$( ".more_posts" ).unbind('submit');
		$('.more_posts').on('submit', function(e) {
			
			self._fetchMorePosts(e);
			/*
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
			
			var requestPosts = new cm.Ajax({
					requestType: 'POST',
					requestUrl: cm.home_path + '/' + operation,
					requestHeaders: ' ',
					data: postForm,
					dataType: 'string',
					sendNow: true
			}, function(data) {
				var response = JSON.parse(data);

				// add stuff to post wall
				if (response.error == "Success") {
					$("#post-wall-ul").append(response.html);
				}
				else {
					$("#post-wall-nmp-error").text("Cannot find more posts.");
					$("#post-wall-nmp-error").show();
					$("#post-wall-nmp-error").hide(1000);
				}

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
			*/
		});
	},
	_scrunchPost: function(element) {
		return new cm.ElementMap(element);
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
		nmp_more_posts : null,
		nmp_more_tweets : null,
		nmp_cur_roster_level : null
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

cm.FileUploader = function(o) {

	// load options
	this._options = {
		element: null,
		button: null,
		panel: null,
		errorLabel: null,
		generateFromElement: null,
		maxFiles: 0,
		acceptedFileTypes: [],
		sizeLimit: 0, // in MB
		classes: {
			form: 'fileupload-form',
			panel: 'fileupload-panel',
			button: 'fileupload-button',
			error: 'fileupload-error'
		}
	};

	// extend user things
	cm.extend(this._options, o);
	cm.extend(this, cm.DisposeSupport);

	// important elements
	this._element = this._options.element;
	this._panel = this._createPreviewPanel(this._find(this._element, 'panel'));
	this._button = this._createInputButton(this._find(this._element, 'button'));
	this._errorLabel = this._find(this._element, 'error');

	cm.css(this._errorLabel, {
		color: 'red'
	});

	// input list
	this._inputList = {};
	this._inputList_size = 0;
	this._inputList_i = 0;

	// birth a new input tag
	this._inputList[this._inputList_i] = this._button._createFileInput(this._inputList_i);
	this._inputList_size++;
	this._inputList_i++;
}

cm.FileUploader.prototype = {
	_find: function(parent, type) {
		var element = cm.getByClass(parent, this._options.classes[type])[0];
		if (!element){
		    throw new Error('element not found ' + type);
		}
		return element;
	},
	_displayError: function(msg) {
		// simple for now
		$( this._errorLabel ).text(msg);
		$( this._errorLabel ).show();
		$( this._errorLabel ).delay(4000).fadeOut(1000);
	},
	_checkFile: function(file) {

		var sFileName = file.name;
		var sFileExtension = sFileName.split('.')[sFileName.split('.').length - 1].toLowerCase();
		var iFileSize = file.size;
		var iConvert = (file.size / 1048576).toFixed(2);
		var iSizeLimit = (this._options.sizeLimit / 1048576).toFixed(2);

		// check file size
		if (iFileSize > this._options.sizeLimit) {
			this._displayError('File must be smaller than ' + iSizeLimit + ' MB');
			return false;
		}

		// check if is right extension
		var i = 0;
		for (i; i < this._options.acceptedFileTypes.length; i++) {
			if (sFileExtension == this._options.acceptedFileTypes[i])
				break;
		}

		if (i == this._options.acceptedFileTypes.length) {
			this._displayError('Not an accepted file type');
			return false;
		}

		return true;

		/*
		/// OR together the accepted extensions and NOT it. Then OR the size cond.
		/// It's easier to see this way, but just a suggestion - no requirement.
		if (!(sFileExtension === "pdf" ||
			sFileExtension === "doc" ||
			sFileExtension === "docx") || iFileSize > 10485760) { /// 10 mb
				txt = "File type : " + sFileExtension + "\n\n";
				txt += "Size: " + iConvert + " MB \n\n";
				txt += "Please make sure your file is in pdf or doc format and less than 10 MB.\n\n";
				alert(txt);
			}
			*/
	},
	_clearPost: function() {

		// get parent element
		var length = $( '.fileupload-button' ).children().length;

		// remove all but the last element
		for (var i = 0; i < length-1; i++) {
			$( '.fileupload-button input:first' ).remove();
			delete this._inputList[i];
		}

		// move last thing to first place, if images were uploaded
		if (length > 1) {
			this._inputList[0] = this._inputList[i];
			delete this._inputList[i];
		}
		
		// reset inputList vars
		this._inputListSize = 1;
		this._inputList_i = 0;

		this._panel._clearImages();
	},
	_createInputButton: function(element) {
		var self = this;

		cm.css(element, {
			color:'white',
			position: 'relative',
			overflow: 'hidden'
		});
		
		var button = new cm.InputButton({
			element: element,
		    multiple: this._options.multiple && cm.UploadHandlerXhr.isSupported(),
		    acceptFiles: this._options.acceptFiles,
		    inputName: 'fileupload[]',
		    onChange: function(input){
			    self._onInputChange(input);
		    },
		    inputLabeledBy: $(this._options.element).attr('input-labeled-by')
		});

		return button;	
	},
	_createPreviewPanel: function(element) {
		var self = this;

		var panel = new cm.PreviewPanel({
			element: element
		});

		return panel;
	},
	_onInputChange: function(input) {
		// check to see if there's room
		if (this._button._element.childNodes.length > this._options.maxFiles) {
			this._displayError('You are only allowed to submit ' + this._options.maxFiles + ' files');
			return false;
		}

		// check file to see if they pass muster
		//
		//  -- Don't be fooled. Only one file is checked
		//
		for (var i=0; i<input.files.length; i++) {
			if (!this._checkFile(input.files[i]))
				return false;
		}

		var inputList = this._inputList;
		var InputButton = this._button;

		// create a delete button
		var button = document.createElement('button');
		cm.addClass(button, 'upload-img-delete')

		button.innerHTML = '&#10006';
		button.onclick = function(e) {
			e.preventDefault();
			// remove input from existence
			input.parentElement.removeChild(input);
			delete inputList[input.arrayIndex];
		}

		this._panel._addImages(input, button);

		cm.css(input, {
			display: 'none'
		});

		// tell button to create a new input
		this._inputList[this._inputList_i] = this._button._createFileInput(this.inputList_i);
		this._inputList_i++;	// increment current index 
	},
	_removeBlankInput: function() {
		var button = this._button._element;
		var inputs = button.childNodes;
		var inputList_i = this.inputList_i;
		var lastI = inputs.length - 1;

		if (inputs[lastI].value === "")
			button.removeChild(inputs[lastI]);
	},
	_reinstateInput: function() {
		this._button._clearFiles();
		this._button._createFileInput(this.inputList_i);
	}
}

cm.InputButton = function(o) {

	this._options = {
		element: null,
		multiple: true,
		acceptFiles: true,
		inputLabeledBy: null,
		inputList: null,
		onChange: function(input) {},
		hoverClass: 'qq-upload-button-hover',
		focusClass: 'qq-upload-button-focus'

	}

	// user options n jazz
	cm.extend(this._options, o);
	cm.extend(this, cm.DisposeSupport);

	this._element = this._options.element;
	this._curInput = null;
}

cm.InputButton.prototype = {
	_createFileInput: function(index) {

		var input = document.createElement("input");
		input.arrayIndex = index;

		if (this._options.multiple){
			input.setAttribute("multiple", "multiple");
		}

		if (this._options.acceptFiles) input.setAttribute("accept", this._options.acceptFiles);

		input.setAttribute("type", "file");
		input.setAttribute("name", this._options.inputName);

		if (this._options["inputLabeledBy"]) {
			input.setAttribute("aria-labelledby", this._options["inputLabeledBy"]);
		}

		cm.css(input, {
			position: 'absolute',
			// in Opera only 'browse' button
			// is clickable and it is located at
			// the right side of the input
			right: 0,
			top: 0,
			fontFamily: 'Arial',
			// 4 persons reported this, the max values that worked for them were 243, 236, 236, 118
			fontSize: '118px',
			margin: 0,
			padding: 0,
			cursor: 'pointer',
			opacity: 0
		});

		this._element.appendChild(input);

		var self = this;
		this._attach(input, 'change', function(){
			self._options.onChange(input);
		});

		this._attach(input, 'mouseover', function(){
			cm.addClass(self._element, self._options.hoverClass);
		});
		this._attach(input, 'mouseout', function(){
			cm.removeClass(self._element, self._options.hoverClass);
		});
		this._attach(input, 'focus', function(){
			cm.addClass(self._element, self._options.focusClass);
		});
		this._attach(input, 'blur', function(){
			cm.removeClass(self._element, self._options.focusClass);
		});

		// IE and Opera, unfortunately have 2 tab stops on file input
		// which is unacceptable in our case, disable keyboard access
		if (window.attachEvent){
			// it is IE or Opera
			input.setAttribute('tabIndex', "-1");
		}

		return input;
	},
	/*
	 * Empties upload button of all files
	 */
	_clearFiles : function() {
		$( this._element ).empty();
	}
}

cm.PreviewPanel = function(o) {

	this._options = {
		element: null
	};	

	cm.extend(this._options, o);
	cm.extend(this, cm.DisposeSupport);

	this._element = this._options.element;

	// create an awesome ul
	this._ul = document.createElement('ul');
	this._element.appendChild(this._ul);

	// CSS PARTY!!!
	// make button suitable container for input
	cm.css(this._element, {
		overflow: 'hidden',
		// Make sure browse button is in the right side
		// in Internet Explorer
		direction: 'ltr'
	});

	cm.css(this._ul, {
		display: 'inline',
		'listStyleType': 'none'
	});

	// Add a clear after the ul
	var clear_div = document.createElement('div');
	clear_div.innerHTML = '&nbsp;';
	this._element.appendChild( clear_div );
	$( clear_div ).addClass('clear');
}

cm.PreviewPanel.prototype = {

	_addImages: function(input, deleteButton) {
		var reader = new FileReader();

		var ul = this._ul;
		var div = document.createElement('div');
		var li = document.createElement('li');
		
		deleteButton.ul = ul;
		deleteButton.div = div;
		deleteButton.li = li;

		$( li ).addClass('upload-post-img');
		
		this._attach(deleteButton, 'click', function() {

			// redeclare for closure purposes

			$( deleteButton.li ).slideUp('slow', 'easeInQuad', function() {
				deleteButton.ul.removeChild(deleteButton.li);
			});
		});

		// counts how many times event has fired
		var counter = 0;
		var target = input.files.length;
		
		// create new images,
		// append to div
		// finally, append to panel
		reader.onload = function (e) {

			var img = document.createElement('img');
			img.src = e.target.result;
			
			$( img ).addClass('upload-post-img');

			div.appendChild(img);
			counter++;

			// if this is the last image
			if (counter >= target) {
				// append stuff
				div.appendChild(deleteButton);
				li.appendChild(div);
				$( li ).hide();
				ul.appendChild(li);
				$( li ).slideDown('slow', 'easeInQuad');
			}
		}

		// add images
		for (var i=0; i < target; i++)
			reader.readAsDataURL(input.files[i]);
	},
	_clearImages: function() {
		// probably add a fade
		$( this._ul ).children().fadeOut('slow').remove();	
	}
}

cm.PostSubmit = function(o, FileUpload) {
	// this._form
	// this._action
	// this._onSuccess
	// this._onFailure
	
	this._options = {
		submit: null,
		form: null,
		action: null,
		ajax: null,
		submitInsert: function() {},
		onSuccess: function(data) {},
		onFailure: function(data) {},
		classes: {
			submit: 'fileupload-submit'
		}
	};

	cm.extend(this._options, o);
	cm.extend(this, cm.DisposeSupport);

	// elements
	this._form = this._options.form;
	this._clickStart = this._find(this._form, 'submit');

	// stuff
	this._action = this._options.action;

	// element map
	this._map = new cm.ElementMap(this._form);

	// functions
	this._submitInsert = this._options.submitInsert;
	this._onSuccess = this._options.onSuccess;
	this._onFailure = this._options.onFailure;

	// file uploader
	this._FileUpload = FileUpload;

	// attach on click event
	var self = this;


	if (this._options.ajax === true) {

		// prevent action
		this._attach(self._clickStart, 'click', function(e) {
			e.preventDefault();
			self._submit(e);
		});
	}
}

cm.PostSubmit.prototype = {
	_find: function(parent, type) {
		var element = cm.getByClass(parent, this._options.classes[type])[0];
		if (!element){
			throw new Error('element not found ' + type);
		}
		return element;
	},
	_submit: function(e) {

		var self = this;
		var fup = this._FileUpload;
		var resetInput = fup._removeBlankInput();

		$( this._map['post-loading-bar-container'] ).show();

		// get form data
		var formData = new FormData(this._form);

		var ajx = new cm.Ajax({
		    requestType: 'POST',
		    requestUrl: this._action,
		    requestParameters: ' ',
		    data: formData,
		    dataType: 'FormData',
		    sendNow: true
		}, function(data) { 
			fup._reinstateInput();
			$( self._map['post-loading-bar-container'] ).hide();
			self._clearPost();

			data = JSON.parse(data);
			self._onSuccess(data);
		}, function(data) {
			fup._reinstateInput();
			self._clearPost();
			var ff = self._onFailure.bind(data);
			ff();
		});
	},
	_clearPost: function() {

		// replace with specified post area later
		$('.post-text').val('');

		// cancel images
		this._FileUpload._clearPost();
	},
	_setOnSuccess: function(f) {
		this._onSuccess = f;
	},
}

