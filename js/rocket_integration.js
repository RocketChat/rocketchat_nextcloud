/*(function ($, window, document) {
    var url = OC.generateUrl('/apps/rocketchat_nextcloud/file');

    $(document).ready(function () {
        if ($('#dir').length > 0) {
            OCA.Files.fileActions.registerAction({
                name: 'open-rocket',
                displayName: 'Chat',
                mime: 'all',
                order: 1,
                permissions: OC.PERMISSION_ALL,
                type: OCA.Files.FileActions.TYPE_DROPDOWN, // @TODO MUST CHECK THIS.
                icon: OC.imagePath('rocketchat_nextcloud', 'rocket-logo-black.png'),
                actionHandler: function (filename, context) {
                    openMessenger(filename, context.$file);
                }
            });
        }
    });
*/
    /**
     * Open chat for file.
     *
     * @param fileName
     * @param $file
     */
/*
    function openMessenger(fileName, $file) {
        var data = {
            id: $file.attr('data-id'),
            name: $file.attr('data-file'),
            isGroupFolder: $file.attr('data-mounttype') === 'group' ? '1' : '0',
        };

        $.ajax({
            url: url,
            type: "post",
            data: data,
            success: function (data) {
                var win = window.open(data.redirect, '_blank');

                win.focus();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('Something went wrong');
            },
        });
    }
})($, window, document);
*/
