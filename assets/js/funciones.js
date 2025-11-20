$(function() { //$(document).ready(function(){
	
	// Tabs de servicios
	$('#servicios-tab a').click( function () {
		$('#servicios-tab-responsive a').removeClass('active');
		$('#servicios-tab-responsive a[href="' + $(this).attr('href') + '"]').addClass('active');
	});
	
	$('#servicios-tab-responsive a').click( function () {
		$('#servicios-tab a').removeClass('active');
		$('#servicios-tab a[href="' + $(this).attr('href') + '"]').addClass('active');
	});
	
	// Proyectos
	var proyectos = $('.lista-proyectos');
	proyectos.isotope({
		// options
		itemSelector: '.item-proyectos',
		layoutMode: 'masonry'
	});
	
	$('.filtro-proyectos').click( function () {
		var filtro = $(this).data('filtro');
		proyectos.isotope({
			filter: filtro
		});
		
		$('.filtro-proyectos').removeClass('active');
		$(this).addClass('active');
	});
	
	setTimeout( function () {
		proyectos.isotope({
			filter: '*'
		});
	},1000);
	
	// aviso cookies https://www.w3schools.com/js/js_cookies.asp
	var cookies = document.cookie.split("; "); // leo todas las cookies y las separo en un array
	
	if ( $.inArray('aviso_cookies=1', cookies) < 0 ) { // si no encuentro la cookie 'aviso_cookies=1'
		$('#aviso_cookies').css('display', 'block'); // muestro el aviso
	}
	
	$('#aviso_cookies_cerrar').click(function(){ // al clicar en el icono de cerrar
		document.cookie = "aviso_cookies=1;path=/;"; // genero la cookie, sin fecha de expiración (se borrará al finalizar la navegación) y sirve para todo el dominio
		$('#aviso_cookies').css('display', 'none'); // Esconde el aviso de cookies
	});
	
	// Formulario de contacto
	window.addEventListener('load', function() {
		// Fetch all the forms we want to apply custom Bootstrap validation styles to
		var forms = document.getElementsByClassName('needs-validation');
		// Loop over them and prevent submission
		var validation = Array.prototype.filter.call(forms, function(form) {
			form.addEventListener('submit', function(event) {
				form.classList.add('was-validated');
				
				if (form.checkValidity() === false) {
					event.preventDefault();
					event.stopPropagation();					
				} else {					
					// se envía el formulario
					event.preventDefault();
					event.stopPropagation();
					
					grecaptcha.ready(function() {
						grecaptcha.execute('6LeXGAMbAAAAANOnv2-fIuaL4zzHPYA8mRXrs7uo', {action: 'submit'}).then(function(token) {
							$('[name="g-recaptcha-response"]').val(token); // guardo el valor del "token" en el <input type="hidden" name="g-recaptcha-response">
							// Add your logic to submit to your backend server here.			

							$('form.needs-validation').append('<div class="my-3 text-center aviso-ajax"><i class="fa-solid fa-circle-notch fa-spin fa-3x"></i></div>');
							
							$.ajax({
								method: "POST",
								url: "envio.php",
								data: $('form.needs-validation').serialize()
							})
							.done(function( resultado ) {
								// una vez recojo el resultado
								$('.aviso-ajax').remove();
								
								if ( resultado == "ok" ) {
									$('form.needs-validation').append('<div class="alert alert-success my-3 lead aviso-ajax" role="alert"><i class="fas fa-check fa-lg"></i> El mensaje se ha enviado correctamente.</div>').hide().fadeIn();
									
									form.reset();
									form.classList.remove('was-validated');
									grecaptcha.reset();
									
								} else {
									$('form.needs-validation').append('<div class="alert alert-danger my-3 lead aviso-ajax" role="alert"><i class="fas fa-times fa-lg"></i> Error en el envío.</div>').hide().fadeIn();
									console.log(resultado);
								}
								
								setTimeout(function(){
									$('.aviso-ajax').fadeOut(function(){
										$('.aviso-ajax').remove();
									});
								},5000);
							}); //ajax.done

						}); //grecaptcha.execute
					});	//grecaptcha.ready
					
				}

			}, false);
		});
	}, false);

}); // $(document).ready( function() {});