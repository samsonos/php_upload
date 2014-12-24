/**
 * Created by Maxim Omelchenko on 13.11.2014 at 17:50.
 */

var sjsFileUpload = {

    /**
     * Function to bind DOM element to upload file on drop
     * @param options Object of handlers to perform on different actions.
     */
    fileUpload : function(options) {

        /**
         * Selector of input element
         * @type {selector|undefined}
         */
        var inputSelector = undefined;

        /**
         * This function will perform before clicking on download button
         * @type {function|undefined}
         */
        var start = undefined;

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
         * @type {Element|*}
         */
        var elem = this.DOMElement;

        /**
         * Variable to store SamsonJS element
         * @type {sjsFileUpload}
         */
        var sjsElem = this;

        /**
         * Stores SamsonJS input element
         */
        var input;

        /**
         * Variable to store files from input type="file" element
         */
        var files;

        /**
         * Variable to store XMLHTTPRequest handlers.
         */
        var uploadStatus;

        /**
         * An HTMLHTTPRequest object to perform asynchronous request
         * @type {XMLHttpRequest}
         */
        var xhr;

        /**
         * Asynchronous controller url
         * @type {string|undefined}
         */
        var url;

        /**
         * Max file size
         * @type {Number}
         */
        var maxSize = (elem.hasAttribute('__maxsize')) ? parseInt(elem.getAttribute('__maxsize')) : 2000000;

        /**
         * Variables to store elements, to show file upload progress
         */
        var progressBlocks, progressBars, progressTexts, progressBytes, loadPercent = 0;

        // Bind all input options
        if (typeof options === 'object') {
            url = options.url;
            inputSelector = options.inputSelector;
            start = options.start;
            fileAdded = options.fileAdded;
            sending = options.sending;
            uploadProgress = options.uploadProgress;
            successFile = options.successFile;
            completeAll = options.completeAll;
        }

        // URL to send file
        url = (url === undefined) ? (elem.hasAttribute('__action_upload')) ? elem.getAttribute('__action_upload') : '/upload' : url;

        if (inputSelector === undefined) {
            sjsElem.append('<div class="__btn_upload">' +
            '<input class="__input_file" type="file" multiple>' +
            '<label class="__progress_text">Загрузить файл</label>' +
            '</div>');
            input = s('.__input_file', sjsElem);
        } else {
            input = s(inputSelector);
        }

        input.change(function(){

            files = input.DOMElement.files;

            if (start === undefined) {
                //console.log(sjsElem.parent);
            } else {
                start(sjsElem, files)
            }
            sjsElem.css('display', 'none');

            // Create blocks and call user function on file add
            for (var i = 0; i < files.length; i++) {
                (function(file){
                    if (fileAdded === undefined) {
                        sjsElem.parent().append('<div class="__upload_process">' +
                        '<div class="__progress_bar"><p></p></div>' +
                        '<div class="__upload_text">' +
                        '<label class="__progress_text">Загрузка файла</label>' +
                        '<label class="__progress_bytes"></label>' +
                        '</div>' +
                        '</div>');
                    } else {
                        fileAdded(sjsElem, file);
                    }
                }(files[i]));
            }

            progressBlocks = s('.__upload_process');
            progressBars = s('.__progress_bar p', progressBlocks);
            progressTexts = s('.__progress_text', progressBlocks);
            progressBytes = s('.__progress_bytes', progressBlocks);

            for (i = 0; i < files.length; i++) {

                xhr = new XMLHttpRequest();
                (function(file, _i){
                    loadPercent = 0;
                    uploadStatus = xhr.upload;

                    uploadStatus.addEventListener('progress', function(e){
                        var c = e.lengthComputable ? Math.ceil(100 * (e.loaded / e.total)) - 1 : 0;
                        loadPercent = (loadPercent < c) ? c : loadPercent;
                        if (uploadProgress === undefined) {
                            if (progressBlocks.elements[_i]) {
                                progressBars.elements[_i].width(loadPercent + '%');
                                progressBytes.elements[_i].html(e.loaded + '/' + e.total + 'B');
                            }
                        } else {
                            uploadProgress(sjsElem, file, loadPercent, e.loaded);
                        }
                    });

                    uploadStatus.addEventListener('error', function(){
                        (error === undefined) ? alert('Невозможно загрузить файл!') : error(sjsElem, 'Failed to upload file!');
                    });

                    xhr.open("POST", url, true);
                    if (sending === undefined) {
                        xhr.setRequestHeader("Cache-Control", "no-cache");
                        xhr.setRequestHeader("Content-Type", "multipart/form-data");
                        xhr.setRequestHeader("X-File-Name", encodeURIComponent(file.name));
                        xhr.setRequestHeader("X-File-Size", file.size);
                        xhr.setRequestHeader("X-File-Type", file.type);
                        // Add special async header
                        xhr.setRequestHeader('SJSAsync', 'true');
                    } else {
                        sending(sjsElem, file, xhr);
                    }

                    if (maxSize > file.size) {
                        xhr.send(file);
                    } else {
                        (error === undefined) ? alert('Файл слишком большой (' + file.size + 'B). Максимальный размер файла: ' + maxSize + 'B.') : error(sjsElem, 'File is too big!');
                        if (completeAll != undefined) { completeAll(sjsElem); }
                    }
                    xhr.onreadystatechange = function(){
                        if (xhr.readyState == 4) {
                            if (successFile === undefined) {
                                progressBars.elements[_i].width('100%');
                                progressTexts.elements[_i].html('Загрузка завершена');
                                var block = progressBlocks.elements[_i];
                                block.removeClass('__upload_process');
                                block.addClass('__upload_complete');
                            } else {
                                successFile(xhr.response);
                            }
                            if ((_i == files.length - 1) && (completeAll != undefined)) {
                                var parent = elem.parentNode;
                                parent.removeChild(elem);
                                parent.appendChild(elem);
                                completeAll(sjsElem);

                            }
                        }
                    };

                })(files[i], i);
            }

        });
    }

};

// Add plugin to SamsonJS
SamsonJS.extend(sjsFileUpload);
