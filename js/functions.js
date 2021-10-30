function showModal(id, showOverlay=false) {
    $("#"+id).show();
    if (showOverlay) $(".page-overlay").show();
    let escapeListener = document.addEventListener("keyup", function(e){
        if (e.key === "Escape") {
            hideModal(id, escapeListener);
        }
    })
}

function hideModal(id, escapeListener) {
    $("#"+id).hide();
    $(".page-overlay").hide();
}