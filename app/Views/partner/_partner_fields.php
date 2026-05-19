<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label class="form-label">Name<span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($partner['name'] ?? '') ?>" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($partner['email'] ?? '') ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label">Phone</label>
        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($partner['phone'] ?? '') ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label">Address</label>
        <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($partner['address'] ?? '') ?>">
    </div>
</div>