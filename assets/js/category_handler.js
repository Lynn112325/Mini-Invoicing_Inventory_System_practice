document.getElementById('saveCategoryBtn').addEventListener('click', function () {
    const categoryName = document.getElementById('category_name').value;
    const errorDiv = document.getElementById('modal-error');

    if (!categoryName) {
        errorDiv.innerText = "Please enter a category name.";
        errorDiv.classList.remove('d-none');
        return;
    }

    fetch(BASE_URL + 'modules/category/ajax_add_category.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'name=' + encodeURIComponent(categoryName)
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // add new category to the select dropdown
                const select = document.getElementById('category_id');
                const option = new Option(data.name, data.id);
                select.add(option);
                select.value = data.id;

                const saveBtn = document.getElementById('saveCategoryBtn');
                if (saveBtn) saveBtn.blur();

                // close the modal and reset the form
                const categoryForm = document.getElementById('addCategoryForm');
                if (categoryForm) {
                    categoryForm.reset();
                }
                const modalElement = document.getElementById('addCategoryModal');
                const modalInstance = bootstrap.Modal.getInstance(modalElement);
                if (modalInstance) {
                    modalElement.setAttribute('aria-hidden', 'true');
                    modalInstance.hide();
                }

                errorDiv.classList.add('d-none');

                // Remove backdrop and reset body styles after modal is hidden
                modalElement.addEventListener('hidden.bs.modal', function () {
                    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                    document.body.style.overflow = 'auto';
                    document.body.style.paddingRight = '0px';
                }, { once: true });

                showToast("Category added successfully!");
            } else {
                errorDiv.innerText = data.message || "Error adding category.";
                errorDiv.classList.remove('d-none');
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            errorDiv.innerText = ApiErrorHandler.parse(error);
        });
});