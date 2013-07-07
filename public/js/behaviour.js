$(document).ready(function()
{
	// NAVIGAZIONE SUPERFISH
	$('ul.sf-menu').superfish();

    // INIZIO SCRIPT SLIDING LABELS
    $('form#login-sidebar div.form_element_text, form#login-sidebar div.form_element_password').css({
            'position' : 'relative',
            'overflow' : 'hidden'
    });
    $('form#login-sidebar div.form_element_text input, form#login-sidebar div.form_element_password input').css({
            'width' : '75%'
    });
    
    $('form#login-sidebar div.form_element_text label, form#login-sidebar div.form_element_password label').each(function(){
        var restingPosition = '5px';
        
        // style the label with JS for progressive enhancement
        $(this).css({
            'position' : 'absolute',
            'top' : '3px',
            'left' : restingPosition,
            'display' : 'inline'//,
            //'z-index' : '99'
        });
        
        // grab the input value
        var inputval = $(this).next('input').val();
        
        // grab the label width, then add 5 pixels to it
        var labelwidth = $(this).width();
        var labelmove = labelwidth + 10;
        
        //onload, check if a field is filled out, if so, move the label out of the way
        if(inputval !== ''){
            $(this).stop().animate({ 'left':'-'+labelmove }, 1);
        }    	
        
        // if the input is empty on focus move the label to the left
        // if it's empty on blur, move it back
        $('input').focus(function(){
            var label = $(this).prev('label');
            var width = $(label).width();
            var adjust = width + 5;
            var value = $(this).val();
            
            if(value == ''){
                label.stop().animate({ 'left':'-'+adjust }, 'fast');
            } else {
                label.css({ 'left':'-'+adjust });
            }
        }).blur(function(){
            var label = $(this).prev('label');
            var value = $(this).val();
            
            if(value == ''){
                label.stop().animate({ 'left':restingPosition }, 'fast');
            }	
            
        });	    	
    })


// INIZIO SCRIPT CONTROLLO ISCRIZIONE

    $("#registrazione").validate(
    {
	errorElement: "p",

    //serve a mettere gli errori alla fine del div che contiene gli elementi, altrimenti si vedrebbe male nelle chebox
    errorPlacement: function(error, element) {
        error.appendTo( element.parent('div') );
    },

	rules:
        {
            nome: "required",
            cognome: "required",
            
            password: 
		{
		required: true,
		minlength: 4
		},
            conferma_password:
            {
                required: true,
                equalTo: "#registrazione_password"
            },
            email:
            {
                required: true,
                email: true
            },
	    conferma_email:
	    {
                required: true,
                equalTo: "#registrazione_email"
            },
            accetto_condizioni: "required"
          },
        
	messages:
        {
		nome: " Inserisci il nome",
		cognome: " Inserisci il tuo cognome!",
		password: 
			{
      			required: "Inserire una password",
      			minlength: "La password deve essere almeno di 4 caratteri"
    			},
		conferma_password: " La conferma della password non è corretta",
		conferma_email: " La conferma email non è corretta!",
		email: 
			{
      			required: "Inserire una email",
      			email: "Email non valida"
    			},
		accetto_condizioni: "Non hai accettato i termini del servizio!"
        }
	
    });

});