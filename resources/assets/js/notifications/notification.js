'use strict';

let notifications = Handlebars.compile(
    '<a href="#" data-id="{{id}}" class="dropdown-item dropdown-item-unread readNotification"\n' +
    '                   id="readNotification">\n' +
    '                    <div class="dropdown-item-icon bg-primary text-white">\n' +
    '                        <i class="{{icon}}" style="line-height: unset;"></i>\n' +
    '                    </div>\n' +
    '                    <div class="dropdown-item-desc text-dark notification-title" style="width: 100%;">{{title}}\n' +
    '                        <div class="">\n' +
    '                            <span class="notification-for-text" style="line-break: anywhere;color: grey">{{description}}</span>\n' +
    '                        </div>\n' +
    '                        <div class="float-right">\n' +
    '                            <small class="notification-time">{{time}}</small>\n' +
    '                        </div>\n' +
    '                    </div>\n' +
    '                </a>');

$(document).ready(function () {
    getNotifications();
});

setInterval(function () {
    getNotifications();
}, 120000);

window.getNotifications = function () {
    $.ajax({
        url: '/get-notifications',
        method: 'GET',
        success: function (result) {
            if (result.success) {
                $('.notification-content').find('a').remove();
                if (result.data.length > 0) {
                    $('.notification-content').css('overflow-y', 'auto');
                    $('.nav-link.notification-toggle').addClass('beep');
                    $('#allRead').removeClass('d-none');
                    $('.empty-notification').addClass('d-none');
                    $.each(result.data, function (el, val) {
                        $('.notification-content').append(notifications({
                            id: val.id,
                            title: val.title,
                            description: val.description,
                            icon: HeaderNotificationIconJS(val.type),
                            time: moment(val.created_at).fromNow(),
                        }));
                    });
                } else {
                    $('#allRead').addClass('d-none');
                    $('.empty-notification').removeClass('d-none');
                }
            }
        },
    });
};

$(document).on('click', '#readNotification', function (e) {
    e.preventDefault();
    e.stopPropagation();
    let notificationId = $(this).data('id');
    let notification = $(this);
    notification.remove();
    $.ajax({
        type: 'POST',
        url: '/notification/' + notificationId + '/read',
        data: { notificationId: notificationId },
        success: function () {
            let notificationCounter = document.getElementsByClassName(
                'readNotification').length;
            if (notificationCounter == 0) {
                $('#allRead').addClass('d-none');
                $('.empty-notification').removeClass('d-none');
                $('.nav-link.notification-toggle').removeClass('beep');
            }
        },
        error: function (error) {
            manageAjaxErrors(error);
        },
    });
});

$(document).on('click', '#allRead', function (e) {
    e.preventDefault();
    e.stopPropagation();
    $.ajax({
        type: 'POST',
        url: '/read-all-notification',
        success: function () {
            $('.readNotification').remove();
            $('#allRead').addClass('d-none');
            $('.empty-notification').removeClass('d-none');
            $('.nav-link.notification-toggle').removeClass('beep');
        },
        error: function (error) {
            manageAjaxErrors(error);
        },
    });
});

function HeaderNotificationIconJS (model) {
    let className = model.substring(11);
    if (className == 'Project') {
        return 'fas fa-folder-open';
    } else if (className == 'Task') {
        return 'fas fa-tasks';
    } else if (className == 'Invoice') {
        return 'fas fa-file-invoice';
    } else if (className == 'User') {
        return 'fas fa-users';
    } else {
        return 'fas fa-bell';
    }
}
