(function($) {
    $(document).ready(function() {
        $('.mxp-dissmis-btn').click(function() {
            var self = this;
            var data = {
                'action': 'mxp_dismiss_notice',
                'nonce': Mxp_AJAX_dashboard.nonce,
                'key': $(this).data().key,
            };
            $.post(ajaxurl, data, function(res) {
                if (res.success) {
                    $('#'+$(self).data().key).hide();
                } else {
                    //Error? That's my problem...
                }
            });
        });
    });

})(jQuery);
