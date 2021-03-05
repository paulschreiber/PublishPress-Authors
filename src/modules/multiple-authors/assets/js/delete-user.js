jQuery(function ($) {
    $('#updateusers #delete_option0').parents('fieldset').remove();

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
