$(document).ready(function() {
    $('#add_user_to_group_form_uid').autoComplete({
        resolverSettings: {
            url: '/api/autocomplete/uid'
        }
    });

    $('#confirm-delete').on('show.bs.modal', function(e) {
        $(this).find('#uid').text($(e.relatedTarget).attr("uid"));
        $(this).find('#group_cn').text($(e.relatedTarget).attr("group_cn"));
        $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
    });
});