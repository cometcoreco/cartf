/* 
========================================
    Shop Responsive Sidebar
========================================
*/
$(document).on('click', '.shop-close-content-icon, .responsive-overlay', function() {
    $('.shop-close-content, .responsive-overlay').removeClass('active');
});

$(document).on('click', '.shop-icon-sidebar', function() {
    $('.responsive-overlay').addClass('active');
    $('.shop-close-content').toggleClass('active');
});

/* 
========================================
    Click Active Class
========================================
*/
$(document).on('click', '.active-list .item', function() {
    $(this).siblings().removeClass('active');
    $(this).toggleClass('active');
});

/* 
========================================
    Click expand Slide Up Down
========================================
*/
$(document).on('click', '.single-shop-left-title .title', function(e) {
    var shopTitle = $(this).parent('.single-shop-left-title');
    if (shopTitle.hasClass('open')) {
        shopTitle.removeClass('open');
        shopTitle.find('.single-shop-left-inner').removeClass('open');
        shopTitle.find('.single-shop-left-inner').slideUp(300, "swing");
    } else {
        shopTitle.addClass('open');
        shopTitle.children('.single-shop-left-inner').slideDown(300, "swing");
        shopTitle.siblings('.single-shop-left-title').children('.single-shop-left-inner').slideUp(300, "swing");
        shopTitle.siblings('.single-shop-left-title').removeClass('open');
    }
});

/* 
========================================
    Filter List 
========================================
*/

$('.filter-list .list.active').find('.check-input').prop('checked', true);

$(document).on('click', '.filter-list .list', function() {
    elFilter = $(this);
    elFilter.toggleClass('active');
    elFilter.siblings().removeClass('active');

    $(this, '.filter-list .list.active').find('.check-input').prop('checked', true);
    $(this).siblings().find('.check-input').prop('checked', false);

    if(!elFilter.hasClass('active')){
        elFilter.find('.check-input').prop('checked', false);
    }

});