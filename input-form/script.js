$(document).ready(function(){
  $('.payment__field').blur(function(){
    if($(this).val() !==""){
      $(this).addClass('payment__field--filled');
    }else{
      $(this).removeClass('payment__field--filled');
    }
  });
});