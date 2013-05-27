(function($) {

	$.fn.charCount = function(options){
	  
		function calculate(obj){
			var count = $(obj).val().length;
			$("#ccar").html("Caracteres:"+count);
			$("#cmsg").html("SMS:"+(parseInt(count/160)+1));
		};
				
		this.each(function() {  			
			calculate(this); 
			$(this).keyup(function(){calculate(this)});
			$(this).change(function(){calculate(this)});
		});
	  
	};

})(jQuery);
