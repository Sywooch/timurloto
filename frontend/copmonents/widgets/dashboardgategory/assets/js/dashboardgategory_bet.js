$(document).ready(function () {
    DashboardCategory.init();
    // DashboardCategory.test();

    $('.trigger-collapse').on('click',function (e) {
        DashboardCategory.sendData(this,$(this).data());
        // $(this).parents('.collapsed-type').toggleClass('active_coll_main').find('.collapse-block').stop().slideToggle(400);
         e.preventDefault();
        return false;
    });

    $(document).on("click", ".trigger-sub-collapse", function(e) {
         DashboardCategoryGroup.sendData(this,$(this).data());
       // $(this).parent().toggleClass('active_coll').find('.sub-collapse').stop().slideToggle(400);
         e.preventDefault();
        return false;
    });


    $(document).on("click", ".turnire_fin", function(e) {
        console.log('turnire_fin')
        DashboardCategoryFinlink.sendData(this,$(this).data());
        // $(this).parent().toggleClass('active_coll').find('.sub-collapse').stop().slideToggle(400);
        e.preventDefault();
        return false;
    });

});

var DashboardCategory={
    csrf:null,
    csrf_param:null,
      init:function () {
          this.csrf = jQuery('meta[name=csrf-token]').attr("content");
          this.csrf_param = jQuery('meta[name=csrf-param]').attr("content");
      },
    sendData: function (el,data, link) {
        if (!link) {
            link = '/dashboard/get-by-country';
        }
        data[this.csrf_param] = this.csrf;
        jQuery.post(link, data,
            function (json) {
                if (json.result === 'fail') {
                    console.log(json.error);
                }
                else {
                    DashboardCategory.render(el,json);
                }
            }, "json");

        return false;
    },
    render: function (el,json) {
        // console.log($(el).data());
        $('#child_colapse_'+$(el).data('id')).html(json.html);

        $(el).parents('.collapsed-type').toggleClass('active_coll_main').find('.collapse-block').stop().slideToggle(400);
    },

    test:function () {
        console.log(this.csrf)
        console.log(this.csrf_param)
    }
};

var DashboardCategoryGroup={
    csrf:null,
    csrf_param:null,
    init:function () {
        this.csrf = jQuery('meta[name=csrf-token]').attr("content");
        this.csrf_param = jQuery('meta[name=csrf-param]').attr("content");
    },
    sendData: function (el,data, link) {
        if (!link) {
            link = '/dashboard/get-by-country-group';
        }
        data[this.csrf_param] = this.csrf;
        jQuery.post(link, data,
            function (json) {
                if (json.result === 'fail') {
                    console.log(json.error);
                }
                else {
                    DashboardCategoryGroup.render(el,json);
                }
            }, "json");
        return false;
    },
    render: function (el,json) {
        // console.log($(el).data());
         $('#child_sub_colapse_'+$(el).data('id')).html(json.html);
        //$(this).parent().toggleClass('active_coll').find('.sub-collapse').stop().slideToggle(400);
      $(el).parent().toggleClass('active_coll').find('.sub-collapse').stop().slideToggle(400);
       // $(el).parents('.collapsed-type').toggleClass('active_coll_main').find('.collapse-block').stop().slideToggle(400);
    },
    test:function () {
        console.log(this.csrf)
        console.log(this.csrf_param)
    }
};


var DashboardCategoryFinlink={
    csrf:null,
    csrf_param:null,
    init:function () {
        this.csrf = jQuery('meta[name=csrf-token]').attr("content");
        this.csrf_param = jQuery('meta[name=csrf-param]').attr("content");
    },
    sendData: function (el,data, link) {
        if (!link) {
            link = '/dashboard/get-by-country-group-fin';
        }
        data[this.csrf_param] = this.csrf;
        jQuery.post(link, data,
            function (json) {
                if (json.result === 'fail') {
                    console.log(json.error);
                }
                else {
                    DashboardCategoryFinlink.render_nav(el,json);
                    DashboardCategoryFinlink.render_block(el,json);
                }
            }, "json");
        return false;
    },
    render_nav:function (el,json) {

    },
    render_block:function (el,json) {
        $('#dashboard_center_block_tab_blocks').html(json.html_block);
    },
    test:function () {
        console.log(this.csrf)
        console.log(this.csrf_param)
    }
};





