var uploadFileHandler = function( selector, options )
{	
	// Define default options
	if( options == undefined ) options = { };
	
	// File selected event
	s(selector).change( function(input)
	{			
		// Get file object for uploading
		var file = input.DOMElement.files[0];
		
		// Get parent block
		var p = input.parent();	
		
		// Get DOM elements
		var progress = s('.__progress_bar', p );
		var filename = s('.__file_name', p );			
		var line = s( 'p', progress);
		var progress_text = s('.__progress_text', p );

		// Loading status
		p.addClass('loading');
		
		// Loaded progress
		var loaded_percent = 0;			
		
		// Create async upload request
		var xhr = new XMLHttpRequest();
	    var uploadStatus = xhr.upload;
		    
	    /** Output upload status */
	    var showStatus = function( text )
	    {		    	
	    	filename.val( text );
	    	
	    	p.removeClass('loading');	    	
	    };

	    // Upload progress handler
	    uploadStatus.addEventListener("progress", function (ev)
	    {	    	
	    	// Calculate loaded part
        	var c = ev.lengthComputable ? Math.ceil(100 * (ev.loaded / ev.total)) - 1 : 0;
        	
        	// If file not yet loaded and this is not "old" progress event
            if ( c > loaded_percent ) loaded_percent = c;
            
            // Draw progress bar
            line.width( loaded_percent+'%' );
            
            // Output progress text
            progress_text.html(c+'%');
            
            if( options.progress != undefined ) options.progress(file); 
            
	    }, false);

	    // Upload error handler
	    uploadStatus.addEventListener("error", function (ev) 
	    {	    	
	    	if( options.error != undefined ) options.error(file); 
	    	
	    	showStatus('Ошибка загрузки файла');
	    	
	    }, false);
	    
	    // Upload success handler
	    uploadStatus.addEventListener("load", function (ev) 
	    {	 
	    	if( options.finish != undefined ) options.finish(file);
	    	
	    	showStatus( file.name );		 
	    	
	    	// Upload field is not empty anymore
	    	p.removeClass('empty');	    	
	    	
	    }, false);
	    
	    // If start handler is passed - call it
	    if( options.start != undefined ) options.start();
	    
	    // Get upload controller url
	    var url = s('.__action', p ).val();	    

	    // Perform request
	    xhr.open( "POST", url, true );
        xhr.setRequestHeader("Cache-Control", "no-cache");
        xhr.setRequestHeader("Content-Type", "multipart/form-data");
        xhr.setRequestHeader("X-File-Name", encodeURIComponent(file.name) );
        xhr.setRequestHeader("X-File-Size", file.size );
        xhr.setRequestHeader("X-File-Type", file.type );
        //xhr.setRequestHeader("Content-Type", "application/octet-stream");
        xhr.send( file );
        
        // Response handler
        xhr.onreadystatechange = function() 
		{
			// Если это финальная стадия обработки асинхронного запроса
			if ( xhr.readyState == 4 ) 
			{
				if(options.response) 
				{
					 // Draw progress bar
		            line.width( '100%' );
		            
		            // Output progress text
		            progress_text.html('100%');
					
					options.response(  xhr.responseText.trim(), xhr.status, xhr );				
				}
			}			
		};
	});
};

/** Automaticly bind uploader by special class */
s('.__upload').pageInit( uploadFileHandler );