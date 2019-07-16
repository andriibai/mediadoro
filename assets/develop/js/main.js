$(document).ready(function() {

    /*===========================SCROLL SPY=============================*/
    $(window).on('scroll', function () {
        var sections = $('section');
        var nav = $('.menu');
        var navHeight = nav.outerHeight();
        var curPos = $(this).scrollTop();
        sections.each(function() {
            var top = $(this).offset().top - navHeight;
            var bottom = top + $(this).outerHeight();

            if (curPos >= top && curPos <= bottom) {
                nav.find('a').removeClass('menu__link-current');
                //sections.removeClass('active');
                //$(this).addClass('menu__link-current');
                nav.find('a[href="#'+$(this).attr('id')+'"]').addClass('menu__link-current');
            }

        });
    });

    /*============================MENU LINK=============================*/
    var menuLink = $('.menu__list .menu__item .menu__link');
    menuLink.click(function(e){
        var href = $(this).attr('href'),
            offsetTop = href === "#" ? 0 : $(href).offset().top;
        $('html, body').stop().animate({
            scrollTop: offsetTop
        }, 700);
        e.preventDefault();
    });

    var linkDot = $('.menu__list .menu__item');
    linkDot.hover(function () {
        if (window.innerWidth > 747){
            $(this).addClass('menu__item-dot');
        }
    }, function () {
        if (window.innerWidth > 747) {
            $(this).removeClass('menu__item-dot');
        }
    });

    /*========================HEADER FIXED================================*/
    var headerResize = $('.header__content').data('header-resize');
    if (headerResize == 1) {
        $(window).bind('scroll', toggleNavClass);
    }
    function toggleNavClass() {
        var scrollTop = $(window).scrollTop();
        $('.header__content').toggleClass('js-fixed', scrollTop > 100);
    }

    /*==========================MOBILE MENU==============================*/
    var nav = $('.menu');
    var menuMobLink = $('.header__menu-mob');
    var overlay = $('.header__overlay');
    menuMobLink.click(function(e) {
        e.preventDefault();
        menuMobLink.toggleClass('open');
        nav.toggleClass('active');
        //hederfixed.addClass('fixed');

        if (menuMobLink.hasClass('open')){
            overlay.fadeIn(600);
        } else{
            overlay.fadeOut(600);
        }
    });

    overlay.click(function(e){
        e.preventDefault();
        menuMobLink.removeClass('open');
        overlay.fadeOut(600);
        nav.toggleClass('active');
    });

    var menuLink = $('.menu__list .menu__item .menu__link');
    menuLink.on('click', function (e){
        e.preventDefault();
        menuMobLink.removeClass('open');
        overlay.fadeOut(600);
        nav.toggleClass('active');
    });

    $(document).on('keydown', function(e) {
        if (e.keyCode == 27) {
            menuMobLink.removeClass('open');
            overlay.fadeOut(600);
            nav.toggleClass('active');
        }
    });

    /*=========================BUTTON TOP===============================*/
    var btnTop = $('.button__top');
    $(window).scroll(function() {
        if ($(window).scrollTop() > 300) {
            btnTop.addClass('show');
        } else {
            btnTop.removeClass('show');
        }
    });
    btnTop.on('click', function(e) {
        e.preventDefault();
        $('html, body').animate({scrollTop:0}, '300');
    });


    /*=========================BUTTONS DOWN==============================*/
    var btnContact = $('.about__btn-contact');
    btnContact.click(function() {
        $('html,body').animate({
                scrollTop: $("#contacts").offset().top},
            'slow');
    });

    var btnDown = $('.button__down');
    btnDown.click(function() {
        $('html,body').animate({
                scrollTop: $("#services").offset().top},
            'slow');
    });

});