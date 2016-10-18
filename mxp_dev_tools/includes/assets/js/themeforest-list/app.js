(function($) {
    function download(path) {
        var iframe = document.createElement('iframe');
        iframe.setAttribute('src', path);
        document.body.appendChild(iframe);
    }
    $(document).ready(function() {
        $('.install_other').click(function() {
        	$(this).prop('disabled', true);
            var id = $(this).data().id;
            var dlink = $(this).data().dlink;
            download(dlink);
        });
        $('.install_theme').click(function() {
            $(this).prop('disabled', true);
            $(this).text('下載安裝中');
            var id = $(this).data().id;
            var dlink = $(this).data().dlink;
            var data = {
                'action': 'mxp_install_theme',
                'nonce': Mxp_AJAX.nonce,
                'dlink': dlink,
            };
            var self = this;
            $(document).queue(function(next) {
                $.post(ajaxurl, data, function(res) {
                    if (res.success) {
                        $('.activate_theme[data-id="' + id + '"]').show();
                    } else {
                        $(self).text(res.data.errorMessage);
                        $(self).prop('disabled', false);
                    }
                    next();
                });
            });
        });
        $('.activate_theme').click(function() {
            location.href = Mxp_AJAX.themesurl;
        });
        $('.install_plugin').click(function() {
            $(this).prop('disabled', true);
            $(this).text('下載安裝中');
            var id = $(this).data().id;
            var dlink = $(this).data().dlink;
            var name = $(this).data().name;
            var data = {
                'action': 'mxp_install_plugin',
                'nonce': Mxp_AJAX.nonce,
                'activated': false,
                'dlink': dlink,
                'slug': 'none',
                'file': 'false',
                'name': name,
                'version': 'none'
            };
            var self = this;
            $(document).queue(function(next) {
                $.post(ajaxurl, data, function(res) {
                    if (res.success) {
                        $('.activate_plugin[data-id="' + id + '"]').show();
                    } else {
                        $(self).text(res.data.errorMessage);
                        $(self).prop('disabled', false);
                    }
                    next();
                });
            });
        });
        $('.activate_plugin').click(function() {
            $(this).prop('disabled', true);
            $(this).text('啟用中');
            var name = $(this).data().name;
            var data = {
                'action': 'mxp_activate_plugin',
                'nonce': Mxp_AJAX.nonce,
                'activated': false,
                'dlink': 'none',
                'slug': 'none',
                'name': name,
                'version': 'none'
            };
            var self = this;
            $(document).queue(function(next) {
                $.post(ajaxurl, data, function(res) {
                    if (res.success) {
                        $(self).text('安裝成功');
                    } else {
                        $(self).text('安裝失敗');
                        $(self).prop('disabled', false);
                    }
                    next();
                });
            });
        });
    });
})(jQuery);
