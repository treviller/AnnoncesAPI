$( function() {
		    $( "#annoncesbundle_annonce_city" ).autocomplete({
		      source: function(request, response){
		    	  $.ajax({
		              url : 'http://ws.geonames.org/searchJSON', // on appelle le script JSON
		              dataType : 'json', // on spécifie bien que le type de données est en JSON
		              data : {
		                  name_startsWith : $('#annoncesbundle_annonce_city').val(), // on donne la chaîne de caractère tapée dans le champ de recherche
		                  maxRows : 15,
		                  country : 'FR',
		                  username : 'treviller'
		              },
		              success : function(donnee){
		                  response($.map(donnee.geonames, function(objet){
		                      return objet.name; // on retourne cette forme de suggestion
		                  }));
		              }
		          });
		      }
		    });
});