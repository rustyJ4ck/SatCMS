/**
 * legacy code
 */

$(function(){

       // validable just selector dont work                  
       $("form.validable").each(function(k,v){
               $.metadata.setType("attr", "validate");  
               $(v).validate({
                   submitHandler: function(form) {
                        $(form).ajaxSubmit({dataType : 'json', cache: false, success: function(data){
                                    tf.submit_handler($(form), data);
                                }
                        });
                   }
                   , invalidHandler: function(form, validator) {
                        var errors = validator.numberOfInvalids();
                        if (errors) {
                            $.tf_message('Заполните все обязательные поля ('+errors+')', true);
                        }
                   }
                   , highlight: function(element, errorClass) {
                            //$(element).addClass(errorClass).parent().prev().children("select").addClass(errorClass);
                   }
               });
       });
       
       // validable just selector dont work (no ajax)
       $("form._validable").each(function(k,v){
               $.metadata.setType("attr", "validate");  
               $(v).validate({
                   
                 highlight: false
               
               , invalidHandler: function(form, validator) {
                        var errors = validator.numberOfInvalids();
                        if (errors) {
                            $.tf_message('Заполните все обязательные поля ('+errors+')', true);
                        }
                   }
               
               });
       });   

       $('form.submitable').submit(function(){
            $(this).ajaxSubmit({dataType : 'json', cache: false, success: function(data){
                tf.submit_handler($(form), data);
                }
            });
            return false;
       });

    //datepicker
    $.datepicker.setDefaults(
    {closeText:"\u0417\u0430\u043a\u0440\u044b\u0442\u044c",prevText:"&#x3c;\u041f\u0440\u0435\u0434",nextText:"\u0421\u043b\u0435\u0434&#x3e;",currentText:"\u0421\u0435\u0433\u043e\u0434\u043d\u044f",monthNames:["\u042f\u043d\u0432\u0430\u0440\u044c","\u0424\u0435\u0432\u0440\u0430\u043b\u044c","\u041c\u0430\u0440\u0442","\u0410\u043f\u0440\u0435\u043b\u044c","\u041c\u0430\u0439","\u0418\u044e\u043d\u044c","\u0418\u044e\u043b\u044c","\u0410\u0432\u0433\u0443\u0441\u0442",
    "\u0421\u0435\u043d\u0442\u044f\u0431\u0440\u044c","\u041e\u043a\u0442\u044f\u0431\u0440\u044c","\u041d\u043e\u044f\u0431\u0440\u044c","\u0414\u0435\u043a\u0430\u0431\u0440\u044c"],monthNamesShort:["\u042f\u043d\u0432","\u0424\u0435\u0432","\u041c\u0430\u0440","\u0410\u043f\u0440","\u041c\u0430\u0439","\u0418\u044e\u043d","\u0418\u044e\u043b","\u0410\u0432\u0433","\u0421\u0435\u043d","\u041e\u043a\u0442","\u041d\u043e\u044f","\u0414\u0435\u043a"],dayNames:["\u0432\u043e\u0441\u043a\u0440\u0435\u0441\u0435\u043d\u044c\u0435",
    "\u043f\u043e\u043d\u0435\u0434\u0435\u043b\u044c\u043d\u0438\u043a","\u0432\u0442\u043e\u0440\u043d\u0438\u043a","\u0441\u0440\u0435\u0434\u0430","\u0447\u0435\u0442\u0432\u0435\u0440\u0433","\u043f\u044f\u0442\u043d\u0438\u0446\u0430","\u0441\u0443\u0431\u0431\u043e\u0442\u0430"],dayNamesShort:["\u0432\u0441\u043a","\u043f\u043d\u0434","\u0432\u0442\u0440","\u0441\u0440\u0434","\u0447\u0442\u0432","\u043f\u0442\u043d","\u0441\u0431\u0442"],dayNamesMin:["\u0412\u0441","\u041f\u043d","\u0412\u0442",
    "\u0421\u0440","\u0427\u0442","\u041f\u0442","\u0421\u0431"],weekHeader:"\u041d\u0435",dateFormat:"dd.mm.yy",firstDay:1,isRTL:false,showMonthAfterYear:false,yearSuffix:""}
    );

       
    // date bind
    $('.date').datepicker();       
    
    $('.date_hm').bind('change blur', function() {

      var _controlName = this.getAttribute('name');
      var controlNamePrefix = _controlName.slice(-1);
      var controlName = _controlName.substr(0, _controlName.length - 2);
      
      if ('h' == controlNamePrefix || 'm' == controlNamePrefix) {
          var val = $('input[name='+_controlName+']').val();
          $('input[name='+_controlName+']').val(("0" + val).slice(-2));
          // console.log(controlNamePrefix);
          // console.log(("0" + val).slice(-2));
      }
      
      $('input[name='+controlName+']').val(
        $('input[name='+controlName+'_d]').val()
        + ' '
        + parseInt($('input[name='+controlName+'_h]').val())
        + ':'
        + parseInt($('input[name='+controlName+'_m]').val())
      );
   
    } ); 
    
    
    $('input[type=checkbox].remove-image').change(
        function(){
            var $this = $(this);
            if ($this.is(':checked')) {
                $('input[type=file][name=' + this.name + ']').attr('disabled', true);    
                $('input[type=text][name=' + this.name + '_url]').attr('disabled', true);
            }
            else {
                $('input[type=file][name=' + this.name + ']').attr('disabled', false);    
                $('input[type=text][name=' + this.name + '_url]').attr('disabled', false);            
            }
        }
    );
       
});