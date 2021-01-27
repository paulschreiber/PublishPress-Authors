jQuery(function ($) {
    // $('form#updateusers #reassign_user').parent('li').after($('#delete_option_author_wrapper')).remove();

    $('#delete_option_author_wrapper select').ppma_select2({
        ajax: {
            url: ajaxurl,
            dataType: 'json',
            delay: 0,
            data: function (params) {
                return {
                    action: 'authors_search',
                    nonce: $('#publishpress_authors_search_nonce').val(),
                    q: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data.results
                };
            },
            cache: false
        }
    });
});
