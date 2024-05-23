/**
 * iCheck
 */
require('icheck/icheck');

/**
 * Datetimepicker
 */
require('eonasdan-bootstrap-datetimepicker/src/js/bootstrap-datetimepicker');

/**
 * Error handler
 * @author ttdat
 */
function errorHandle() {
    $('img').on("error", function () {
        this.src = '/images/default/img_not_found.webp';
    });
}

/**
 * datetimepicker
 * @author ttdat
 * @status [status]
 * @return {[type]} [description]
 */
function datetimepickerInit() {
    var toggle_delay = $('[toggle-display]');
    
    var datepickder = $(toggle_delay.data('target'));
    if (typeof datepickder.datetimepicker === 'function') {
        datepickder.datetimepicker({ format: 'hh:mm DD-MM-YYYY', locale: 'vi' });
        if (typeof datepickder.val() !== 'undefined' && datepickder.val() != '') {
            datepickder.show();
            toggle_delay.prop('checked', true);
            $(this).iCheck('update');
        }

        toggle_delay.change(function () {
            if ($(this).prop('checked')) {
                $($(this).data('target')).show();
                datepickder.datetimepicker('destroy');
                datepickder.datetimepicker({ format: 'hh:mm DD-MM-YYYY', locale: 'vi', defaultDate: new Date() });
            } else {
                let target = $($(this).data('target'));
                target.hide();
                target.val('');
            }
        });
    }
}

/**
 * Icheck
 * @author ttdat
 * @status [status]
 * @return {[type]} [description]
 */
function iCheckInit() {
    $('[data-toggle="tooltip"]').tooltip();
    $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
        checkboxClass: 'icheckbox_minimal-blue',
        radioClass   : 'iradio_minimal-blue'
    });
    
    $('input').on('ifChanged', function (event) { $(event.target).trigger('change'); });
    $('input[type=checkbox].parent').change(function (e) {
        $('input[type=checkbox].child, input[type=checkbox].parent').prop('checked', e.target.checked).iCheck('update');
    });
    $('input[type=checkbox].child').change(function (e) {
        var checkAll = false;
        if (e.target.checked) {
            var checkAll = true;
            $('input[type=checkbox].child').each(function (index, item) {
                if (!item.checked) {
                    checkAll = false;
                    return false;
                }
            });
        }
        $('input[type=checkbox].parent').prop('checked', checkAll).iCheck('update');
    });
}

function iCheckDestroy() {
    $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck('destroy');
}


/**
 * Page init require
 * @author ttdat
 */
 (() => {
    // Auto open treeview
    ((url) => {
        if($('.sidebar-menu .treeview a[href$="' + url + '"]').length > 0) {
            $('.sidebar-menu .treeview a[href$="' + url + '"]').parents('li').addClass('active').parents('.treeview').addClass('active');
        }

        if($('.sidebar-menu li a[href$="' + url + '"]').length > 0) {
            $('.sidebar-menu li a[href$="' + url + '"]').parents('li').addClass('active').parents('.treeview').addClass('active');
        }
    })(location.pathname);

    // Khởi tạo iCheck
    if (document.getElementById("menu-config") == null) {
        iCheckInit();
    }

    // Khởi tạo datetimepicker
    datetimepickerInit();

    /**
     * Error handle
     */
    errorHandle();

    // Fix lỗi responsive table và dropdown menu z-index
    (() => {
        $('.table-responsive').on('show.bs.dropdown', function () {
            $('.table-responsive').css( "overflow", "inherit" );
        });
        $('.table-responsive').on('hide.bs.dropdown', function () {
            $('.table-responsive').css( "overflow", "auto" );
        });
    })();
})()