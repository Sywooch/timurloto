$(function () {

    $('.trigg-op-block_DEL').on('click', function () {

        UserSubscriber.showPop(this);

        // if($(this).parents('.drop-open-block').hasClass('locked-bet')) {
        //     $(this).parents('.drop-open-block').removeClass('locked-bet');
        // } else {
        //     $(this).parents('.drop-open-block').addClass('locked-bet');
        // }
        return false;
    });


    // $('.trig-val').on('click', function () {
    //     $(this).parents().find('.drop-list').stop().fadeOut(400);
    //     if ($(this).parents('.drop-open-block').hasClass('locked-bet')) {
    //         $(this).parents('.drop-open-block').removeClass('locked-bet');
    //     } else {
    //         $(this).parents('.drop-open-block').addClass('locked-bet');
    //     }
    //     return false;
    // });
    // $('.btn-subscribe').on('click', function () {
    //     $(this).toggleClass('subscribed');
    //     return false;
    // });
})



$(document).ready(function () {
    UserSubscriber.init();
});

var UserSubscriber={
    csrf:null,
    csrf_param:null,
    init:function () {
        this.csrf = jQuery('meta[name=csrf-token]').attr("content");
        this.csrf_param = jQuery('meta[name=csrf-param]').attr("content");
        $(document).on("click", ".trigg-op-block", function(e) {
            if($(this).parent().hasClass('locked-bet')){
                UserSubscriber.test('is locked hide on prod');
                UserSubscriber.removeSubscriber(this);



                // UserSubscriber.showPop(this);
            }else{
                UserSubscriber.test('is Open');
                UserSubscriber.showPop(this);
            }
            e.preventDefault();
            return false;
        });

        $(document).on("click", ".trig-val", function(e) {

            UserSubscriber.addSubscriber(this);
            UserSubscriber.showOpen(this);
            e.preventDefault();
            return false;
        });

        console.log('Init UserSubscriber');
    },

    addSubscriber: function (el) {
        var data = {};
        data.Subscriber = {};
        data.Subscriber.id = $('#period_parent').data("parent-id");
        data.Subscriber.period = $(el).data('value');
        data[this.csrf_param] = this.csrf;
        $.ajax({
            url: "/account/addsubscriber",
            type: "POST",
            data: data,
            dataType: "json",
            success: function (json) {
                if (json) {
                    UserSubscriber.test(json);
                  //  SmartCart.getFromCart(); // update cart

                } else {
                    UserSubscriber.test('No json');
                }
            }
        });
    },
    removeSubscriber:function (el) {


        var data = {};
        data.Subscriber = {};
       data.Subscriber.id = $('#period_parent').data("parent-id");
       data[this.csrf_param] = this.csrf;
       $.ajax({
            url: "/account/remove-subscriber",
            type: "POST",
            data: data,
            dataType: "json",
            success: function (json) {
                if (json) {
                    UserSubscriber.test(json);
                    UserSubscriber.showOpen(el)
                } else {
                    UserSubscriber.test('No json');
                }
            }
        });

    },

  showPop:function (el) {
      $(el).parents('.drop-open-block').find('.drop-list').stop().fadeToggle(400);
  },
    // showOpenAfterCloce:function (el) {
    //     $(el).parents('.drop-open-block').find('.drop-list').stop().fadeToggle(400);
    // },
    showOpen:function (el) {
          $(el).parents().find('.drop-list').stop().fadeOut(400);
        if ($(el).parents('.drop-open-block').hasClass('locked-bet')) {
            $(el).parents('.drop-open-block').removeClass('locked-bet');
        } else {
            $(el).parents('.drop-open-block').addClass('locked-bet');
        }

    },


    reloadDom:function (el){
        var count_items = $('.bet-coup-list li').length;
        if(count_items >= 1){
            $('.no-bet-selected-text').fadeOut(400);
            setTimeout(function () {
                $('.coupon-tabs-wrapper-inner').fadeIn(400);
            },410);
        } else {
            $('.coupon-tabs-wrapper-inner').fadeOut(400);
            setTimeout(function () {
                $('.no-bet-selected-text').fadeIn(400);
            },410);
        }
        if(count_items > 1){
            $('.ordinator').removeClass('active');
            $('.express').addClass('active');
            $('.all-coeficient,.delete-block').slideDown(400);
        } else {
            $('.ordinator').addClass('active');
            $('.express').removeClass('active');
            $('.all-coeficient,.delete-block').slideUp(400);
        }
        $('.open-coupon .count-coup').text(count_items);
        $(el).parents('.row-collapse').find('.bet-parent-val').removeClass('selected');
        $(el).toggleClass('selected');
    },

    test:function (data) {
        console.log(data)
    }
};

function showNotification(message) {

    $('.notification-calculate').html(message);
    $('.notification-calculate').fadeIn(400);
    setTimeout(function () {
        $('.notification-calculate').fadeOut(400);
    },2000);
}





