function showToast(message, type = 'success') {
    const toastElement = document.getElementById('liveToast');
    const toastMessage = document.getElementById('toastMessage');

    toastElement.className = `toast align-items-center text-white bg-${type} border-0`;
    toastMessage.innerText = message;

    const toast = new bootstrap.Toast(toastElement, { delay: 3000 });
    toast.show();
}