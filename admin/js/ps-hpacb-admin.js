(function ($) {
    'use strict';

    $(document).on('click', '.ps_hpacb', function (e) {
        e.preventDefault();

        var term_type = $(this).attr('ps_hpacb_term');
        var term_id = $(this).attr('id');
        var term_title = $(this).attr('title');
        var term_option = $(this).attr('ps_hpacb_term_option');
        var object_id = $(this).attr('ps_hpacb_term_id');
        ($).ajax({
            type: "POST",
            url: ps_hpacb_ajax.url,
            async: false,
            data: {
                action: "ps_hpacb_update_meta",
                nonce: ps_hpacb_ajax.nonce,
                term_type: term_type,
                term_id: term_id,
                term_option: term_option,
                visibility: term_title,
                object_id: object_id,
            },
            success: function (data) {
                var object_id = '#' + data.id;
                if (data.do_action == "Hide") {
                    $(object_id).attr("title", "Hidden");
                    $(object_id).attr("class", "ps_hpacb dashicons dashicons-yes-alt");
                    $(object_id).attr("style", "cursor:pointer;color:green;");

                } else if (data.do_action == "Show") {
                    $(object_id).attr("title", "Visible");
                    $(object_id).attr("class", "ps_hpacb dashicons dashicons-no-alt");
                    $(object_id).attr("style", "cursor:pointer;color:red;");
                }
            }

        });
    });

})(jQuery);