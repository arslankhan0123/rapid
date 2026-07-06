'use strict';

$(document).ready(function () {
    $('#filter_status').select2({
        width: '150px',
    });

    $('#billing_type').select2({
        width: '180px',
    });
});

$(document).on('change', '#filter_status', function () {
    window.livewire.emit('filterProjectsByStatus', $(this).val());
});

$(document).on('change', '#billing_type', function () {
    window.livewire.emit('filterProjectsByBillingType', $(this).val());
});

document.addEventListener('livewire:load', function (event) {
    Livewire.hook('message.processed', (message, component) => {
        let $owl = $('.owl-carousel');
        $owl.trigger('destroy.owl.carousel');

        $owl.html($owl.find('.owl-stage-outer').html()).
            removeClass('owl-loaded');
        livewireLoadOwel($owl);
    });
});
