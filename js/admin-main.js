$(function () {
    var body = $('body');
    var flash = $('#flash');
    var isSchedulePage = body.hasClass('schedulePage');
    var focusedDiv;
    if (flash.is(':visible')) {
        flash.setRemoveTimeout(5000);
    }
    var gt = new Gettext({ 'domain': 'messages' });

    /* ===================================================== */
    /*               Initial Stuff and Anchors               */

        setEditableTextfields();

        $('.attributeSelect').each(function () {
            var input = $(this);
        });

        $(window).on('beforeunload', function () {
            if ($('.unsaved').length > 0)
                return "There are some unsaved changes on this page.";
        });

        $('span[class$="Toggle"]').on('click', function () {
            resetActiveField();
            var toggler = $(this).attr('class');
            var element = $('#' + toggler.substr(0, toggler.length - 6));
            if (element.is(':visible')) {
                resetActiveField();
                element.slideToggle("normal", function () {
                    ofh = new $.fn.dataTable.FixedHeader(table);
                });
            } else {
                element.slideToggle();
                body.children(".fixedHeader").each(function () {
                    $(this).remove();
                });
            }
        });

    /* ===================================================== */
    /*                Editable Table Related                 */

        $(window).on('resize', function () {
            if (!focusedDiv) {
                body.children(".fixedHeader").each(function () {
                    $(this).remove();
                });
                dTable.draw();
                ofh = new $.fn.dataTable.FixedHeader(dTable);
            }
        });

        $('.editableTable td').on('mouseover mouseout', function () {
            var cols = $('colgroup');
            var i = $(this).prevAll('td').length;
    //        $(this).parent().toggleClass('hover');
            $(cols[i]).toggleClass('hover');
        });

        $('table').on('mouseleave', function () {
            $('colgroup').removeClass('hover');
        });

        /**
         * @description Submit the standardForm
         */
        $(document).on('submit', '.standardForm', function () {
            if (confirm(gt.gettext("Are you sure you want to submit the form? All unsaved changes will be lost!"))) {
                var form = $(this);
                var parent = form.parent();
                parent.toggleLoadingImage();
                if(form.attr('id') == "productForm"){
                    var data = [];
                    form.find('.attributes li').each(function(){
                        var entity = {};
                        var liItem = $(this);
                        entity["name"] = liItem.children('.attributeName').html();
                        entity["value"] = liItem.children('.attributeValue').html();
                        entity["position"] = liItem.children('.attributePosition').html();
                        data.push(entity);
                    });
                }else{
                    data = {};
                }
                form.ajaxSubmit({
                    target: '#' + form.attr('id'),
                    type: "post",
                    data:{
                      "extraData" : data
                    },
                    success: function (responseText) {
                        if (responseText.redirect) {
                            location.reload(true);
                        }
                        setEditableTextfields();
                        parent.toggleLoadingImage();
                    }
                });
            }
            return false;
        });

        /**
         * @description Manages the editable table when a cell is double clicked
         */
        $(document).on('dblclick', '.editableTable td', function () {
            var td = $(this);
            if (!td.hasClass('activeField') && !td.hasClass('editTextfield') && !td.hasClass('unEditable')) {
                resetActiveField();
                td.addClass('activeField');
                var content = td.text();
                var element;
                var editPanel = $('#editPanel');
                editPanel.appendTo(body);
                var editInput = editPanel.children(':first');

                // position related
                var position = td.offset();
                var width = td.outerWidth();
                editPanel.css('width', width - 1);
                editPanel.css('margin-left', position.left);
                editPanel.css('top', position.top + td.outerHeight(true)).show();

                var form = $('.standardForm');
                var tableId = table.attr('id');
                var pageId = table.attr('class').split(' ')[1].substr(7);
                var id = td.attr('class').split(' ')[0].substr(pageId.length).lowerize();
                if (td.hasClass('editSelect') || td.hasClass('editMultiSelect')) {
                    element = td.hasClass('editMultiSelect') ? form.find('[name="' + pageId + "[" + id + '][]"]').clone() : form.find('[name="' + pageId + "[" + id + ']"]').clone();
                    var options = element.find('option');
                    if (td.hasClass('excludeCurrent')) {
                        var rowId = dTable.row(td.parent()).data()[0];
                        options.each(function () {
                            var option = $(this);
                            if (option.val() == rowId) option.detach();
                        });
                    }
                    td.find('span[class^="option"]').each(function () {
                        var optionId = $(this).attr('class').substr(7);
                        options.filter(function () {
                            return $(this).val() == optionId;
                        }).attr('selected', 'selected');
                    });
                    editInput.replaceWith(element);
                } else if (td.hasClass('editDatetime')) {
                    if (content != '') {
                        var date;
                        var splitDate = content.split('-');
                        date = new Date(splitDate[2], splitDate[1] - 1, splitDate[0])
                    } else {
                        date = new Date();
                    }
                    element = $('<input />', {
                        "id": "mydate",
                        "value": content
                    });
                    editInput.replaceWith(element).datePicker().focus();
                } else if (td.hasClass('editFile') || td.hasClass('editImage')) {
                    var fileForm = $('<form />', {
                        "action": baseUrl + '/upload-file',
                        "enctype": "multipart/form-data",
                        "class": "fileForm"
                    });
                    $('<input />', {
                        "type": "hidden",
                        "name": "fileMeta",
                        "value": td.children('.fileMeta').html()
                    }).appendTo(fileForm);
                    element = $('<input />', {
                        "type": "file",
                        "name": "uploadFile"
                    }).appendTo(fileForm);
                    editInput.replaceWith(fileForm).focus();
                } else {
                    element = form.find('[name="' + pageId + "[" + id + ']"]').clone();
                    element.val(content);
                    editInput.replaceWith(element);
                }
                element.addClass('editInput').focus();
            }
        });

        /**
         * @description Saves the edit panel if it's visible and the enter button is pressed
         */
        $(window).keypress(function (e) {
            if (e.keyCode == 13) {
                if ($('#editPanel').is(':visible')) {
                    updateEditedField();
                }
            }
        });

        /**
         * @description Saves the edit panel when done is pressed
         */
        $(document).on('click', '#editDone', function () {
            updateEditedField();
        });

        var table = $('.editableTable');
        var dTable = $('.editableTable, #changelogs').DataTable({
            "lengthMenu": [
                [5, 10, 25, 50, -1],
                [5, 10, 25, 50, "All"]
            ],
            "aoColumnDefs": [
                { 'bSortable': false, 'aTargets': table.find('th').length - 1 },

                { "visible": false, "targets": [ 0 ] }
            ],
            "pagingType": "full_numbers",
            "order": [
                [ 1, "asc" ]
            ],
            "dom": 'C<"clear">lfrtip',
            colVis: {
                order: 'alpha'
            },
            "iDisplayLength": 5
        }).on('length.dt', function (e, settings, len) {
            body.children(".fixedHeader").each(function () {
                $(this).remove();
            });
            dTable.draw();
            ofh = new $.fn.dataTable.FixedHeader(dTable);
        });
        var ofh = new $.fn.dataTable.FixedHeader(dTable);

        /**
         * @description Saves the changes to the database
         */
        $('#saveChanges').on('click', function () {
            var entities = [];
            var table = isSchedulePage ? $('.scheduleTable') : $('.editableTable');
            var id = table.attr('class').split(' ')[1].substr(7);
            var flag = false;
            table.find('tbody .unsaved').each(function () {
                var that = $(this);
                var entity = {};
                entity["id"] = dTable.row(that).data()[0];
                that.children('td').each(function () {
                    var td = $(this);
                    if (td.hasClass('required')) {
                        var value = "";
                        if (td.hasClass('editTextfield')) {
                            value = td.children('textarea').val();
                        } else {
                            value = td.text()
                        }
                        if (empty(value)) {
                            td.addClass("redBorder");
                            flag = true;
                        }
                    }
                    td.removeClass("redBorder");
                    if (!td.hasClass('delete')) {
                        var field = td.attr('class').split(' ')[0].substr(id.length);
                        if (td.hasClass('editImage')) {
                            var fileImage = td.find('.fileImage');
                            if (fileImage.hasClass('changed')) {
                                var img = fileImage.children('img');
                                if (img.length > 0) {
                                    var split = img.attr('src').split('/');
                                    entity[field] = split[split.length - 1];
                                } else {
                                    entity[field] = null;
                                }
                            }
                        } else if (td.hasClass('editFile')) {
                            var filename = td.children('.fileName');
                            if (filename.hasClass("changed")) {
                                var name = $.trim(filename.html());
                                entity[field] = name != '-' ? name : null;
                            }
                        } else if (td.hasClass('editSelect')) {
                            var option = td.children('span');
                            if (option.length > 0)
                                entity[field] = option.attr('class').substr(7);
                        } else if (td.hasClass('editMultiSelect')) {
                            var optionArray = [];
                            td.children('span').each(function () {
                                optionArray.push($(this).attr('class').substr(7));
                            });
                            entity[field] = optionArray.length > 0 ? optionArray : null;
                        } else if (td.hasClass('editTextfield')) {
                            entity[field] = td.children('textarea').val();
                        }else if(td.hasClass('editList')){
                            var fields = [];
                            td.find('ul li').each(function(){
                                var liItem = $(this);
                                var entry = {};
                                liItem.children('span').each(function(){
                                   entry[$(this).attr('class')] = $(this).html();
                                });
                                fields.push(entry);
                            });
                            entity[field] = fields;
                        } else {
                            entity[field] = $.trim(td.text()) == "-" ? null : td.text();
                        }
                    }
                });
                entities.push(entity);
                if (flag) return false;

            });
            if (flag) {
                addMessage(gt.gettext("Some required fields are empty."));
                return false;
            }
            console.log(entities);
            if (entities.length > 0) {
                $('tr.unsaved').removeClass('unsaved');
                $.ajax({
                    url: baseUrl + '/admin/' + table.attr('id') + '/save',
                    type: "POST",
                    data: {
                        "entities": entities
                    }
                }).success(function (data) {
                    if (data.success == 1) {
    //                    dTable.draw();
    //                    $('td.unsaved').removeClass('unsaved');
                        location.reload(true);
                    }
                    addMessage(data.message);
                }).error(function () {
                    addMessage(gt.gettext("Something with wrong, please try again."));
                });
            } else {
                addMessage(gt.gettext("There are no changes to save."));
            }
        });

        /**
         * @description Deletes a row/entry from the database
         */
        $(document).on('click','td.delete', function () {
                if (confirm(gt.gettext("Are you sure about this action?"))) {
                    var td = $(this);
                    var url = table.attr('id');
                    var id = dTable.row(td.parent()).data()[0]
                    $.ajax({
                        url: baseUrl + '/admin/' + url + '/remove',
                        type: "POST",
                        data: {
                            "id": id
                        }
                    }).success(function (data) {
                        if (data.success == 1) {
                            dTable.row(td.parent()).remove().draw();
                            location.reload(true);
                        }
                        addMessage(data.message);
                    }).error(function () {
                        addMessage(gt.gettext("Something with wrong, please try again."));
                    });
                }
            }
        );

    /* ===================================================== */
    /*                Textfield Editor Related               */

        /**
         * @description Creates the text editor
         */
        $(document).on('click', '.textEditorToggle', function (e) {
            var toggler = $(this);
            var sourceTextArea = toggler.siblings('textarea');
            sourceTextArea.addClass('activeTextfield');
            var stageWrapper = $('<div/>', {
                'id': "stageWrapper"
            }).prependTo($('body'));

            var stage = $('<div/>', {
                'id': 'stage'
            }).appendTo(stageWrapper);

            var textareaWrapper = $('<div/>', {
                'id': 'textareaWrapper'
            }).appendTo(stage);

            var textarea = $('<textarea/>', {
                "id": "stageTextarea",
                "html": sourceTextArea.val()
            }).appendTo(textareaWrapper);

            var doneBtn = $('<span />', {
                "class": "done",
                "html": "Done"
            }).appendTo(textareaWrapper);

            textarea.sceditor({
                plugins: 'xhtml',
                style: "../css/scedit/jquery.sceditor.default.min.css",
                emoticonsRoot: "../images/",
                disallowedTags: ['script', 'div']
            });

            focusedDiv = stageWrapper;
            stageWrapper.focusLight();
            body.addClass('unscrollable');
        });

        $(document).on('click', '#stage .done', function () {
            var activeEditor = $('.activeTextfield');
            var newVal = $('#stageTextarea').sceditor('instance').val();
            if (activeEditor.val() != newVal) {
                activeEditor.closest('tr').addClass('unsaved');
                activeEditor.html(newVal);
            }
            activeEditor.removeClass('activeTextfield');
            focusedDiv.unfocusLight().detach();

            body.removeClass('unscrollable');
        });


    /* ===================================================== */
    /*                  Attributes  Related                  */

        $(document).on('click', '.editAttributes', function () {
            var span = $(this);

            var stageWrapper = $('<div/>', {
                'id': "stageWrapper",
                'class' : "wider"
            }).prependTo(body);

            $(
                '<div id="stage">' +
                        '<div id="attributeList">' +
                            '<table>' +
                                '<thead>' +
                                    '<tr>' +
                                        '<th class="invisible">Id</th>' +
                                        '<th>Name</th>' +
                                        '<th>Value</th>' +
                                        '<th class="invisible">Position</th>' +
                                    '</tr>' +
                                '</thead>' +
                                '<tbody></tbody>' +
                            '</table>' +
                        '</div>' +
                        '<div id="attributeOptions">' +
                            '<span class="addAttribute button">Add Attribute</span>' +
                            '<span class="removeAttribute button">Remove Attribute</span>' +
                            '<span class="moveUp button">Move Up</span>' +
                            '<span class="moveDown button">Move Down</span>' +
                            '<span class="attributeEditDone button">Done</span> ' +
                        '</div>' +
                '</div>'
            ).appendTo(stageWrapper);

            var attributeListBody = $('#attributeList tbody');
            span.siblings('ul').children('li').each(function(){
                var attribute = $(this);
                    attributeListBody.append(
                        '<tr>' +
                            '<td class="invisible">' +
                                '<input type="text" class="attributeId" value="' + attribute.children('.attributeId').html() + '" />' +
                            '</td>' +
                            '<td>' +
                                '<input type="text" class="attributeName" value="' + attribute.children('.attributeName').html() + '" />' +
                            '</td>' +
                            '<td>' +
                                '<input type="text" class="attributeValue" value="' + attribute.children('.attributeValue').html() + '" />' +
                            '</td>' +
                            '<td class="invisible">' +
                                '<input type="text" class="attributePosition" value="' + attribute.children('.attributePosition').html() + '" />' +
                            '</td>' +
                        '</tr>'
                    )
            });
            span.addClass('activeEditedField');
            focusedDiv = stageWrapper;
            stageWrapper.focusLight();
            body.addClass('unscrollable');
        });

        $(document).on('click', '.addAttribute', function(){
            var attributeListBody = $('#attributeList tbody');
            var lastPosition = parseInt(attributeListBody.find('tr:last-of-type .attributePosition').val());
            lastPosition = empty(lastPosition) || isNaN(lastPosition) ? 1 : (lastPosition+1);
            $(
                '<tr>' +
                    '<td class="invisible">' +
                        '<input type="text" class="attributeId" />' +
                    '</td>' +
                    '<td>' +
                        '<input type="text" class="attributeName" />' +
                    '</td>' +
                    '<td>' +
                        '<input type="text" class="attributeValue" />' +
                    '</td>' +
                    '<td class="invisible">' +
                        '<input type="text" class="attributePosition" value="' + lastPosition + '" />' +
                    '</td>' +
                '</tr>'
            ).appendTo('#attributeList tbody');
        });

        $(document).on('click','#attributeList tbody tr',function(){
           $(this).parent().find('tr.active').removeClass('active');
                $(this).toggleClass('active');
        });

        $(document).on('click', '.moveUp', function(){
            $('#attributeList').find('tr.active').each(function(){
                var tr = $(this);
                var prev = tr.prev('tr');
                if(prev.length > 0){
                    var position = tr.find('.attributePosition:first');
                    var prevPosition = prev.find('.attributePosition:first');
                    var temp = position.val();
                    position.attr('value',prevPosition.val());
                    prevPosition.attr('value',temp);
                    prev.before(tr);
                }
            });
        });

        $(document).on('click', '.moveDown', function(){
            $('#attributeList').find('tr.active').each(function(){
                var tr = $(this);
                var next = tr.next('tr');
                if(next.length > 0){
                    var position = tr.find('.attributePosition:first');
                    var nextPosition = next.find('.attributePosition:first');
                    var temp = position.val();
                    position.attr('value',nextPosition.val());
                    nextPosition.attr('value',temp);
                    next.after(tr);
                }
            });
        });

        $(document).on('click','.removeAttribute',function(){
            $('.activeEditedField').closest('tr').addClass('unsaved');
            $('#attributeList').find('tr.active').detach();
        });

        $(document).on('click','.attributeEditDone',function(){
            var activeEditedField = $('.activeEditedField');
            var attributes = activeEditedField.siblings('ul');
            attributes.empty();
            $('#attributeList').find('tbody').children('tr').each(function(){
                var tr = $(this);
                var name = tr.find('.attributeName');
                var value = tr.find('.attributeValue');
                if(empty(name.val())){
                    name.addClass('redBorder');
                    flag = true;
                }else{
                    name.removeClass('redBorder');
                }
                if(empty(value.val())){
                    value.addClass('redBorder');
                    flag = true;
                }else{
                    value.removeClass('redBorder')
                }
                if(!flag){
                    attributes.append(
                            '<li>' +
                            '<span class="attributeId">' + tr.find('.attributeId').val() + '</span>' +
                            '<span class="attributeName">' + tr.find('.attributeName').val() + '</span>' +
                            '<span class="attributeValue">' + tr.find('.attributeValue').val() + '</span>' +
                            '<span class="attributePosition">' + tr.find('.attributePosition').val() + '</span>' +
                            '</li>'
                    );
                }
            });
            if(!flag){
                var flag = false;
                if(activeEditedField.closest('.editableTable').length > 0 && attributes.children('li').length > 0){
                    activeEditedField.closest('tr').addClass('unsaved');
                }
                focusedDiv.unfocusLight().detach();
                activeEditedField.removeClass('activeEditedField');
                body.removeClass('unscrollable');
            }
        });
});