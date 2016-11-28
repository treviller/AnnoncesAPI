	var $listePhotos;
	var nbPhotos;
	var editMode;
	
	var $addButtonWithoutLi = $('<button id="addButton" class="btn btn-default">Ajouter une autre photo</button>');
	var $addButton = $('<li class="list-group-item row"></li>').append($addButtonWithoutLi);

	jQuery(document).ready(function() {
	   
		if($('#photos-add-mode').length)
		{
			$listePhotos = $('#photos-add-mode');
			editMode = false;
		}
		else if($('#photos-edit-mode').length)
		{
			$listePhotos = $('#photos-edit-mode');
			editMode = true;
		}
		
	    nbPhotos = ($listePhotos.find(':input').length /2);
	     
	    //Initialisation
		for(i=0 ; i<nbPhotos ; i++)
		{
			if(editMode)
			{
				$('#annoncesbundle_annonce_photos_'+i+'_file').attr('disabled', 'disabled');
			}
			else
			{
				$($('#photos-add-mode li')[i]).attr('id', 'photo'+i);
			}
			$('#annoncesbundle_annonce_photos_'+i).after(createRemoveButton(i));
		}
	    
	    $listePhotos.append($addButton);
	    
	    if(nbPhotos >= 3)
	    {
	    	$('#addButton').attr('disabled', 'disabled');
	    }

	    $addButton.on('click', function(e) {
	        
	        e.preventDefault();

	        addPhoto($listePhotos, $addButton);
	    });
	});

	function addPhoto($collectionHolder, $addButton) {
	    
	    var prototype = $collectionHolder.data('prototype');
	    
	    var newPhotoForm = prototype.replace(/__name__/g, nbPhotos);
	    
	    nbPhotos++;
	    
	    if(nbPhotos >= 3)
		{
	    	$('#addButton').attr('disabled', 'disabled');
		}

	    var $newPhotoFormLi = $('<li id="photo'+(nbPhotos-1)+'" class="list-group-item row"></li>').append(newPhotoForm);
	    $addButton.before($newPhotoFormLi);
	    $('#annoncesbundle_annonce_photos_'+(nbPhotos-1)).after(createRemoveButton((nbPhotos-1)));
	}
	
	function createRemoveButton(index)
	{
		$button = $('<button id="'+index+'" class="btn btn-danger">Supprimer</button>');

		$button.on('click', function(e)
			{
				e.preventDefault();
				$('#photo'+$(this).attr('id')).remove();
				refreshIndex();
				nbPhotos--;
				if(nbPhotos == 0)
				{
					addPhoto($listePhotos, $addButton);
				}
				$('#addButton').removeAttr('disabled');
			});

		return $button
	}
	
	function refreshIndex()
	{
		for(i = 0; i < ($('li.list-group-item.row').length - 1); i++)
		{
			$($('ul li div div input')[i]).attr('id', 'annoncesbundle_annonce_photos_'+i+'_file');
			$($('ul li div div input')[i]).attr('name', 'annoncesbundle_annonce[photos]['+i+'][file]');
			$($('ul li div:not(div.form-group)')[i]).attr('id', 'annoncesbundle_annonce_photos_'+i);
			$($('ul li div div label')[i]).attr('for', 'annoncesbundle_annonce_photos_'+i+'_file');
			$($('ul li button')[i]).attr('id', i);
			if(editMode)
			{
				$($('#photos-edit-mode li')[i]).attr('id', 'photo'+i);
			}
			else
			{
				$($('#photos-add-mode li')[i]).attr('id', 'photo'+i);
			}
		}
	}
	