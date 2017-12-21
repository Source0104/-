$(function(){
    $('.main_bottom').click(function(){

        if($('.m_hide').css('display')=='none'){
            
             $('.main_bottom').css({'background':'url("../addons/yj_yd/template/mobile/images/show.jpg") no-repeat','background-size':'100% 1.4rem'});
            $('.m_hide').fadeIn();
        }else{
            $('.main_bottom').css({'background':'url("../addons/yj_yd/template/mobile/images/hide.jpg") no-repeat','background-size':'100% 1.4rem'});
            $('.m_hide').fadeOut();
        }
    })
})