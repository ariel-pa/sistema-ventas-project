$("#frmAcceso").on ('submit',function(e)
{
	e.preventDefault();
	loginAcceso=$("#loginAcceso").val();
	claveAcceso=$("#claveAcceso").val();

	$.post ("../ajax/usuario.php?op=verificar", {"loginAcceso": loginAcceso, "claveAcceso": claveAcceso}, function(data){
		data = JSON.parse(data);
		if (data != null){
			$(location).attr("href","escritorio.php");

		}else{
			bootbox.alert("Usuario y/o password incorrectos");
			$("#claveAcceso").val('');
		}
	});

})
