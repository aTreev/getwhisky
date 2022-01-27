let ordersShown = 0;
let ordersToShow = 10;
let orders_array = [];
let hideCompletedOrders = false;

function prepareAdminOrderPage() {
    getOrders()
    .then(function(result){
        orders_array = result.html_array;
        prepareHideCompletedCheckbox();
        handlePagination();

    });
}

function prepareHideCompletedCheckbox() {
    $("#hide-completed-orders").click(function(){
        if ($(this).is(":checked")) {
            $("[status=dispatched]").hide();
            $("[status=dispatched").next().hide();
            hideCompletedOrders = true;
        } else {
            $("[status=dispatched]").show();
            hideCompletedOrders = false;
        }
    })
}

function handlePagination() {
    $("#orders-shown").remove();

    for(ordersShown; ordersShown < ordersToShow; ordersShown++) {
        $("#admin-order-root").append(orders_array[ordersShown]);
        if (ordersShown === orders_array.length) break;
    }

    // add showMore button
    if (ordersShown < orders_array.length) {
        $(".table-wrapper").append(`<div id='orders-shown'><p>Showing ${ordersShown} of ${orders_array.length} orders</p><button id='show-more'>Show more</button></div>`);

        // show more button logic
        $("#show-more").click(function(){
            // reset search field
            $("#order-search").val("");
            $("#order-search").trigger("input");
            // Reset hideCompleted checkbox
            $("#hide-completed-orders").prop("checked", false);
            hideCompletedOrders = false;
            // update global variable and recursive call
            ordersToShow += 10;
            handlePagination();
        });
    }
    addOrderEventListeners();
    prepareOrderSearch();
}

function addOrderEventListeners() {
    // Loop through each row in the table body
    $("tbody tr[name=order]").off();
    $("tbody tr[name=order]").each(function(){
        // get the orderid via attribute
        const orderid = $(this).attr("orderid");
        const thisTr = $(this);

        // add functionality to the set dispatched buttons
        $(`#set-order-dispatched-${orderid}`).off();
        $(`#set-order-dispatched-${orderid}`).click(function(){
            if (!confirm(`Are you sure you want to update order #${orderid} status to dispatched?`)) return;
            // Update order status to dispatched
            setOrderDispatched(orderid)
            .then(function(result){
                if (result.result == 1) {
                    // success update html and recursive call to add eventlistener
                    new Alert(true, `order #${orderid} set to dispatched`);
                    $(`tr[name=order-items-${orderid}]`).remove();
                    thisTr.replaceWith(result.new_html);
                    addOrderEventListeners();
                } else {
                    new Alert(false, "An error occurred, please try again");
                }
            })
        });

        // Toggle the view of order's items when button clicked
        $(`#view-order-items-${orderid}`).off();
        $(`#view-order-items-${orderid}`).click(function(){
            $(`tr[name=order-items-${orderid}]`).toggle();
        })
    });
}

function prepareOrderSearch() {
    const tableRows = $("tbody tr[name=order]");
    $("#order-search").off();
    $("#order-search").bind("input", function(){
        const searchInput = $(this).val();

        tableRows.each(function(){
            let matches = 0;
            if ($(this).children().text().toLowerCase().includes(searchInput.toLowerCase())) {
                matches++;
            }
            // if any of the row's tds match
            if (matches > 0) {
                // if td status == dispatched and hideCompletedCheckbox is checked return
                if ($(this).attr("status") == "dispatched" && hideCompletedOrders == true) return;
                // Show tr
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
}


function getOrders() {
    return new Promise(function(resolve){
        $.ajax({
            url: "../php/ajax-handlers/order-management-handler.php",
            method: "POST",
            data: {function : 1}
        })
        .done(function(result){
            resolve(JSON.parse(result));
        })
    });
}

function setOrderDispatched(orderid) {
    return new Promise(function(resolve){
        $.ajax({
            url: "../php/ajax-handlers/order-management-handler.php",
            method: "POST",
            data: {function: 2, orderid: orderid}
        })
        .done(function(result) {
            console.log(result);
            resolve(JSON.parse(result))
        });
    });
}