if ($.fn.liteUploader) {
    jQuery(document).ready(function($) {    
        $('.postfiles').liteUploader(
		{
			script: './dropplets/includes/uploader.php',
			maxSizeInBytes: 1048576,
			typeMessage: '',
			before: function ()
			{
				$('#details').html('');
				$('#response').html('Uploading...');
			},
			each: function (file, errors)
			{
				var i, errorsDisp = '';

				if (errors.length > 0)
				{
					$('#response').html('One or more files did not pass validation');

					for (i = 0; i < errors.length; i++)
					{
						errorsDisp += '<br />' + errors[i].message;
					}
				}

				$('#details').append('<p>Name: ' + file.name + ' Type: ' + file.type + ' Size:' + file.size + errorsDisp + '</p>');
			},
			success: function (response)
			{
				$('#dp-uploaded').html(response);
				window.setTimeout(function(){location.reload()},2000)
			}
		});
    });
}
    jQuery(document).ready(function($) {
        $(".dp-open").click(function(){
            var myelement = $(this).attr("href")
            $(myelement).animate({left:"0"}, 200);
            $.cookies.set('dp-panel', 'open');
            $("body").css({ overflowY: 'hidden' });
            return false;
        });
        
        $(".dp-close").click(function(){
            var myelement = $(this).attr("href")
            $(myelement).animate({left:"-300px"}, 200);
            $.cookies.set('dp-panel', 'closed');
            $("body").css({ overflowY: 'auto' });
            return false;
        });
        
        $(".dp-toggle").click(function(){
            var myelement = $(this).attr("href")
            $(myelement).toggle();
            $(this).next('button.dp-button-submit').toggle();
            return false;
        });
        
        // For Input Labels
        $('input, textarea').focus(function () {
            $(this).prev('label').hide(200);
        })
        .blur(function () {
            $(this).prev('label').show(200);
        });
    });



        var infinite = true;
        var next_page = 2;
        var loading = false;
        var no_more_posts = false;
        $(function() {
            function load_next_page() {
                $.ajax({
                    url: "index.php?page=" + next_page,
                    beforeSend: function () {
                        $('body').append('<article class="loading-frame"><div class="row"><div class="one-quarter meta"></div><div class="three-quarters"><img src="./templates//loading.gif" alt="Loading"></div></div></article>');
                        $("body").animate({ scrollTop: $("body").scrollTop() + 250 }, 1000);
                    },
                    success: function (res) {
                        next_page++;
                        var result = $.parseHTML(res);
                        var articles = $(result).filter(function() {
                            return $(this).is('article');
                        });
                        if (articles.length < 2) {  //There's always one default article, so we should check if  < 2
                            $('.loading-frame').html('You\'ve reached the end of this list.');
                            no_more_posts = true;
                        }  else {
                            $('.loading-frame').remove();
                            $('body').append(articles);
                        }
                        loading = false;
                    },
                    error: function() {
                        $('.loading-frame').html('An error occurred while loading posts.');
                        //keep loading equal to false to avoid multiple loads. An error will require a manual refresh
                    }
                });
            }

            $(window).scroll(function() {
                var when_to_load = $(window).scrollTop() * 0.32;
                if (infinite && (loading != true && !no_more_posts) && $(window).scrollTop() + when_to_load > ($(document).height()- $(window).height() ) ) {
                    // Sometimes the scroll function may be called several times until the loading is set to true.
                    // So we need to set it as soon as possible
                    loading = true;
                    setTimeout(load_next_page,500);
                }
            });
        });