/**
 * function to close the message error
 * @param {Event} e 
 */
function close_message_error(e) {
    const messageError = e.target.parentElement
    messageError.style.opacity = 0;
    messageError.style.visibility = "hidden";
    setTimeout(() => {
        messageError.style.display = "none";
    }, 300);
}