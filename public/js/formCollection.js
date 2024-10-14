$(document).ready(function() {

    let list = $("#list-item-collection");
    let counter = list.data('widget-counter');

    $(".add-item").click(function() {
        let newWidget = list.data('prototype');
        newWidget = newWidget.replace(/__name__/g, counter);

        counter++;

        list.data('widget-counter', counter);
        list.append(newWidget);

        $(".remove-cibling-item").click(function() {
            removeCiblingElement($(this).closest('tr'));
        });
    });

    $(".remove-item").click(function() {
        counter--;
        list.data('widget-counter', counter);
        list.children().last().remove();
    });

    $(".remove-cibling-item").click(function() {
        removeCiblingElement($(this).closest('tr'));
    });

    function removeCiblingElement(element) {
        element.remove();
    }
});