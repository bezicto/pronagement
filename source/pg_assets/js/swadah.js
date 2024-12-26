tinymce.init({
    selector: '#wadahComposer',
    menubar: false,
    branding: false,
});

function openPopup(url,winWidth,winHeight) {
    window.open(url, "popup_id", "scrollbars=yes,resizable=no,width="+winWidth+",height="+winHeight);
    return false;
}
