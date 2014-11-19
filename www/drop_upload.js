/**
 * Created by Maxim Omelchenko on 12.11.2014 at 15:29.
 */

var sjsDropFileUpload = {

    /**
     * Function to bind DOM element to upload file on drop
     * @param options Object of handlers to perform on different actions.
     */
    dropFileUpload : function(options) {

        /**
         * Function to perform when file is dragged into the element area
         * @type {function|undefined}
         */
        var dragEnter = undefined;

        /**
         * Function to perform when file is dragged over the element area
         * @type {function|undefined}
         */
        var dragOver = undefined;

        /**
         * Function to perform when file is dragged out the element area
         * @type {function|undefined}
         */
        var dragLeave = undefined;

        /**
         * Function to perform when file is dropped inside the element area
         * @type {function|undefined}
         */
        var drop = undefined;

        /**
         * This function is called when new file is added to upload queue
         * @type {function|undefined}
         */
        var fileAdded = undefined;

        /**
         * Function to perform before ONE file is sent
         * @type {function|undefined}
         */
        var sending = undefined;

        /**
         * Function performs over time file is sanding
         * @type {function|undefined}
         */
        var uploadProgress = undefined;

        /**
         * Function, called each time file is successfully loaded
         * @type {function|undefined}
         */
        var successFile = undefined;

        /**
         * External error handler
         * @type {function|undefined}
         */
        var error = undefined;

        /**
         * Function to be called after file load queue is empty
         * @type {function|undefined}
         */
        var completeAll = undefined;

        /**
         * DOM element to bind on drag
         * @type {Element}
         */
        var elem = this.DOMElement;

        /**
         * variable to store SamsonJS object
         * @type {sjsDropFileUpload}
         */
        var sjsElem = this;

        /**
         * Asynchronous controller url
         * @type {string|undefined}
         */
        var url;

        /**
         * Max file size
         * @type {Number}
         */
        var maxSize = (elem.hasAttribute('__maxsize')) ? parseInt(elem.getAttribute('__maxsize'))/1024/1024 : 2;

        /**
         * Variables to store elements, to show file upload progress
         */
        var progressBlock, progressBars, progressText, progressBytes;

        // Bind all input options
        if (typeof options === 'object') {
            url = options.url;
            dragEnter = options.dragEnter;
            dragOver = options.dragOver;
            dragLeave = options.dragLeave;
            drop = options.drop;
            fileAdded = options.fileAdded;
            sending = options.sending;
            uploadProgress = options.uploadProgress;
            successFile = options.successFile;
            completeAll = options.completeAll;
        }

        // URL to send file
        url = (url === undefined) ? (elem.hasAttribute('__action_upload')) ? elem.getAttribute('__action_upload') : '/upload' : url;

        /**
         * Dropzone object to perform dragging
         * Configure it with input parameters
         */
        var zone = new Dropzone(elem, {url : url,
            maxFilesize: maxSize,
            acceptedFiles: 'image/*',
            previewsContainer: false,
            clickable: false
        });

        // perform input parameters
        zone.on('dragenter', function(e){
            (dragEnter === undefined) ? sjsElem.css('background-color', 'rgba(131, 239, 81, 0.3)') : dragEnter(sjsElem, e);
        });
        zone.on('dragover', function(e){
            (dragOver === undefined) ? sjsElem.css('background-color', 'rgba(131, 239, 81, 0.3)') : dragOver(sjsElem, e);
        });
        zone.on('dragleave', function(e){
            (dragLeave === undefined) ? sjsElem.css('background-color', 'inherit') : dragLeave(sjsElem, e);
        });
        zone.on('drop', function(e){
            (drop === undefined) ? sjsElem.css('background-color', 'inherit') : drop(sjsElem, e);
        });
        zone.on('sending', function(file, xhr){
            if (sending === undefined) {
                xhr.setRequestHeader("Cache-Control", "no-cache");
                xhr.setRequestHeader("Content-Type", "multipart/form-data");
                xhr.setRequestHeader("X-File-Name", encodeURIComponent(file.name));
                xhr.setRequestHeader("X-File-Size", file.size);
                xhr.setRequestHeader("X-File-Type", file.type);
                xhr.setRequestHeader('SJSAsync', 'true');
                xhr.setRequestHeader('Accept', '*/*');
                //xhr.send(file);
            } else {
                sending(sjsElem, file, xhr)
            }
        });
        zone.on('addedfile', function(file){
            if (fileAdded === undefined) {
                sjsElem.append('<div class="__upload_process">' +
                '<div class="__progress_bar"><p></p></div>' +
                '<div class="__upload_text">' +
                '<label class="__progress_text">Загрузка файла</label>' +
                '<label class="__progress_bytes"></label>' +
                '</div>' +
                '</div>');
                progressBlock = s('.__upload_process');
                progressBars = s('.__progress_bar p', progressBlock);
                progressText = s('.__progress_text', progressBlock);
                progressBytes = s('.__progress_bytes', progressBlock);
            } else {
                fileAdded(sjsElem, file);
            }
        });
        zone.on('uploadprogress', function(file, percent, bytesSent){
            if (uploadProgress === undefined) {
                if (progressBars.elements[0]) {
                    var process = progressBars.elements[0];
                    var bytes = progressBytes.elements[0];
                    process.width(percent + '%');
                    bytes.html(bytesSent + '/' + file.size + ' B');
                }
            } else {
                uploadProgress(sjsElem, file, percent, bytesSent);
            }
        });
        zone.on('success', function(response){
            if (successFile === undefined) {
                var block = progressBlock.elements[0];
                block.removeClass('__upload_process');
                block.addClass('__upload_complete');
                var text = progressText.elements[0];
                text.html('Загрузка завершена');
                progressBlock.elements.splice(0, 1);
                progressBars.elements.splice(0, 1);
                progressText.elements.splice(0, 1);
                progressBytes.elements.splice(0, 1);
            } else {
                successFile(response);
            }
        });
        zone.on('queuecomplete', function(){
            if (completeAll === undefined) {

            } else {
                completeAll(sjsElem);
            }
        });
        zone.on('error', function(file, msg){
            (error === undefined) ? alert(msg) : error(sjsElem, file, msg);
            console.log(msg);
        });
    }

};

// Add plugin to SamsonJS
SamsonJS.extend(sjsDropFileUpload);
