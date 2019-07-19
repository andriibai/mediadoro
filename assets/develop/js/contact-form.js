$(document).ready(function() {

    $('#form').on('submit', function (e) {
        e.preventDefault();
        var $that = $(this);
        var userName = $that.find("input[name='name']").val();
        var customerEmail = $that.find("input[name='email']").val();
        var mail_pattern = /[0-9a-z_]+@[0-9a-z_]+\.[a-z]{2,5}/i;
        var check_mail = mail_pattern.test(customerEmail);

        if (check_mail === true) {
            $.ajax({
                type: 'POST',
                url: '/wp-admin/admin-ajax.php',
                data: {
                    action: 'contacts_send_msg',
                    //nonce: localizedScript._wp_nonce,
                    name: userName,
                    email: customerEmail,
                    captcha: grecaptcha.getResponse()
                },
                success: function (result) {
                    console.log(result);
                    var status = JSON.parse(result);
                    if (status.status === 'success') {
                        $that[0].reset();
                        grecaptcha.reset();

                        $('#form').append('<p class="form__success-info">Your message was sent successfully. Thanks.</p>');
                        setTimeout(function(){
                            $('.form__success-info').fadeOut(600);
                        }, 3000);

                    } else {
                        $that.find('.g-recaptcha > div').css({'border': '1px solid red'});
                        setTimeout(function () {
                            $that.find('.g-recaptcha > div').css({'border': 'none'});
                        }, 3000);
                    }

                },

                error: function (jqxhr, status, exception) {
                    console.log('Exception:', exception);
                }
            });

        } else {
            if (check_mail === false) {
                $that.find("input[name='email']").attr('placeholder','Not valid email');
                $that.find("input[name='email']").css({'border-width': '0 0 2px'});
                setTimeout(function () {
                    $that.find("input[name='email']").attr('placeholder','Email');
                    $that.find("input[name='email']").css({'border-width': '0 0 1px'});
                }, 2000);
            } else {
                $that.find("input[name='email']").attr('placeholder','Email');
                $that.find("input[name='email']").css({'border-width': '0 0 1px'});
            }
        }
    });

});