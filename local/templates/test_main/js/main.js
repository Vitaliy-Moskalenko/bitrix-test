$(document).ready(function () {
    $("input[name=phone]").mask("+7 (999) 999 99 99");
    $('.modal__close').click(function () {
        $('.success').hide();
		$('.modal__block').fadeIn(500);
    });

    $('.modal__block').on("submit", function(e){
        e.preventDefault();

        var $form = $(this),
            dataGoal = $(this).attr('data-goal'),
            data = {};

        $form.find('input').each(function(){
            var $field = $(this);
            if (($field.attr('type') == 'radio') || ($field.attr('type') == 'checkbox')) {
                if ($field.is(':checked')) {
                    data[$field.attr('name')] = $field.val();
                }
            } else {
                data[$field.attr('name')] = convertHTML($field.val());
            }
        });
        $form.find('textarea').each(function(){
            var $field = $(this);
            data[$field.attr('name')] = convertHTML($field.val());
        });
        $form.find('select').each(function(){
            var $field = $(this);
            data[$field.attr('name')] = $field.val();
        });
		
        $.ajax({
            url: "/local/ajax/callback.php",
            type: "POST",
            data: data,
            success: function(data) {
                data = JSON.parse(data);
                if (data.success == true) {
                    $form.hide();
                    $('.success .modal__sub-title').text(data.messages);
                    $('.success').fadeIn(150);
                } else {
                    var messages = data.messages.join(",<br>");
                    $form.append('<div class="error"><span class="error__close">×</span>' +
                        '<div class="modal__sub-title">'+messages+'</div></div>');
                    $('.error__close').click(function () {
                        $('.error').hide();
                    });
                }
            },
        });
    });
});	

function convertHTML(input) {
  input = input.replace('>', '&gt;');
  input = input.replace('<', '&lt;');

  return input;
}