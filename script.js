$(document).ready(function () {
    /* The following code is executed once the DOM is loaded */
    $(".todoList").sortable({
        axis: 'y', // Only vertical movements allowed
        containment: 'window', // Constrained by the window
        update: function () {		// The function is called after the todos are rearranged
            // The toArray method returns an array with the ids of the todos
            var arr = $(".todoList").sortable('toArray');
            // Striping the todo- prefix of the ids:
            arr = $.map(arr, function (val, key) {
                return val.replace('todo-', '');
            });
            // Saving with AJAX
            $.get('ajax.php', {action: 'rearrange', positions: arr});
        },
        /* Opera fix: */
        stop: function (e, ui) {
            ui.item.css({'top': '0', 'left': '0'});
        }
    });
    // A global variable, holding a jQuery object 
    // containing the current todo item:
    var currentTODO;
    // If any link in the todo is clicked, assign
    // the todo item to the currentTODO variable for later use.
    $('.todo a').live('click', function (e) {
        currentTODO = $(this).closest('.todo');
        currentTODO.data('id', currentTODO.attr('id').replace('todo-', ''));
        e.preventDefault();
    });
    // Listening for a click on a delete button:
    $('.todo a.delete').live('click', function () {
        $.get("ajax.php", {"action": "delete", "id": currentTODO.data('id')}, function (msg) {
            // Appending the new todo and fading it into view:
            currentTODO.fadeOut('fast');
        });
        e.preventDefault();
    });
    // Listening for a click on a edit button
    $('.todo a.edit').live('click', function () {
        var container = currentTODO.find('.text');
        var text = container.text();
        var currentID = currentTODO.attr('id').replace('todo-', '');
        if (!currentTODO.data('origText')) {
            // Saving the current value of the ToDo so we can
            // restore it later if the user discards the changes:

            currentTODO.data('origText', container.text());
        } else {
            // This will block the edit button if the edit box is already open:
            return false;
        }
        // Appending the save and cancel links:
        container.empty();
        container.append(
                '<input class="mdl-textfield__input editfield" type="text" id="input-' + currentID + '" value="' + text + '">' +
                '<div class="editTodo">' +
                '<a class="saveChanges" href="#">Save</a> or <a class="discardChanges" href="#">Cancel</a>' +
                '</div>'
                );

    });
    // The cancel edit link:
    $('.todo a.discardChanges').live('click', function () {
        currentTODO.find('.text')
                .text(currentTODO.data('origText'))
                .end()
                .removeData('origText');
    });
    // The save changes link:
    $('.todo a.saveChanges').live('click', function () {
        var text = currentTODO.find("input[type=text]").val();
        $.get("ajax.php", {'action': 'edit', 'id': currentTODO.data('id'), 'text': text}, function (msg) {});
        currentTODO.removeData('origText')
                .find(".text")
                .text(text);
    });
    // The Add New ToDo button:
    $('#addButton').click(function (e) {
        $.get("ajax.php", {'action': 'new', 'text': 'New Todo Item.', 'rand': Math.random()}, function (msg) {
            // Appending the new todo and fading it into view:
            $(msg).hide().appendTo('.todoList').fadeIn();
            location.reload();
        });
        e.preventDefault();
    });
});// Closing $(document).ready()