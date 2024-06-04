$(function(){
    console.log('script.js included');

    $("#form").on('submit', function(e){
        e.preventDefault()
        var $form = $(this);
        var $result = $("#result");
        var data = $form.serializeArray();

        console.log('sending: ' + data);
        $result.attr('hidden',true);

        $.ajax({
			url: "./backend/calculate-price.php", 
            type: "POST",
            data: data,
			success: function(response) {
                console.log("received:");
				console.log(response);
				$result.attr('hidden',false);
                $result.html(response);
			},
		});
    })
});
