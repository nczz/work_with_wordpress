(function($) {

    function reload_status() {
        var list = $('.plugins_list[type="checkbox"]');
        for (var i = 0; i < list.length; ++i) {
            var p = JSON.parse($('#p_' + (i + 1)).val());
            if (p.file != '' && !p.activated) {
                $('.mxp-activate[data-id="p_' + p.id + '"]').prop('disabled', false);
            }
            if (p.status == 'install') {
                $('.mxp-install[data-id="p_' + p.id + '"]').prop('disabled', false)
            }
        }
    }

    $(document).ajaxStop(function() {
        reload_status();
    });

    $(document).ready(function() {
        reload_status();
        $('.mxp-install').click(function() {
            var id = $(this).data('id');
            $('div.' + id).html('<font color=red>安裝中...</font>');
            var info = JSON.parse($('#' + id).val());
            var data = {
                'action': 'mxp_install_plugin',
                'nonce': Mxp_AJAX.nonce,
                'activated': info.activated,
                'dlink': info.dlink,
                'slug': info.slug,
                'file': info.file,
                'name': info.name,
                'version': info.version
            };
            var self = this;
            $(this).prop('disabled', true);
            $(document).queue(function(next) {
                $.post(ajaxurl, data, function(res) {
                    if (res.success) {
                        var update_info = JSON.parse(res.data.info);
                        info.file = update_info.file;
                        info.status = update_info.status;
                        $('div.' + id).html('<font color=blue>安裝成功！</font>');
                        $('#' + id).val(JSON.stringify(info));
                    } else {
                        $('div.' + id).html('<font color=red>' + res.data.errorMessage + '</font>');
                        $(self).prop('disabled', true);
                    }
                    next();
                });
            });
        });
        $('.mxp-activate').click(function() {
            var id = $(this).data('id');
            $('div.' + id).html('<font color=red>啟動中...</font>');
            var info = JSON.parse($('#' + id).val());
            var data = {
                'action': 'mxp_activate_plugin',
                'nonce': Mxp_AJAX.nonce,
                'activated': info.activated,
                'dlink': info.dlink,
                'slug': info.slug,
                'file': info.file,
                'name': info.name,
                'version': info.version
            };
            var self = this;
            $(this).prop('disabled', true);
            $(document).queue(function(next) {
                $.post(ajaxurl, data, function(res) {
                    if (res.success) {
                        var update_info = res.data;
                        info.activated = update_info.activated;
                        $('div.' + id).html('<font color=blue>啟動成功！</br>(需重新整理畫面)</font>');
                        $('#' + id).val(JSON.stringify(info));
                    } else {
                        if (res.data) {
                            $('div.' + id).html('<font color=red>' + res.data.msg + '</font>');
                            $(self).prop('disabled', true);
                        } else {
                            $('div.' + id).html('<font color=red>外掛有設定回應（待全部安裝完成後重新整理頁面即可）</font>');
                        }
                    }
                    next();
                });
            });
        });
        $('.button.action').click(function() {
            var id = $(this).attr('id');
            var select = '';
            if (id === 'doaction') {
                select = $('select[name="action"]');
            } else {
                select = $('select[name="action2"]');
            }
            var list = $('.plugins_list[type="checkbox"]');
            for (var i = 0; i < list.length; ++i) {
                var id = $(list[i]).data('id');
                var p = JSON.parse($('#' + id).val());
                if (p.file != '' && !p.activated && !$('.mxp-activate[data-id="p_' + p.id + '"]').prop('disabled')) {
                    if (select.val() === 'activate' && $('.plugins_list[data-id="' + id + '"]').is(":checked")) $('.mxp-activate[data-id="p_' + p.id + '"]').trigger('click');
                }
                if (p.status == 'install' && !$('.mxp-install[data-id="p_' + p.id + '"]').prop('disabled')) {
                    if (select.val() === 'install' && $('.plugins_list[data-id="' + id + '"]').is(":checked")) $('.mxp-install[data-id="p_' + p.id + '"]').trigger('click');
                }
            }

        });
    });
})(jQuery);
