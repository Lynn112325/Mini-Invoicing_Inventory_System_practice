<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <title>ERP System Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            height: 100vh;
            display: flex;
            align-items: center;
            background-color: #f5f5f5;
        }

        .form-signin {
            width: 100%;
            max-width: 330px;
            padding: 15px;
            margin: auto;
        }
    </style>
</head>

<body class="text-center">
    <main class="form-signin">
        <form method="POST" action="?route=login_process">
            <h1 class="h3 mb-3 fw-normal">Please Login to the ERP System</h1>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" style="color: red; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <div class="form-floating mb-2">
                <input type="text" name="username" class="form-control"
                    value="<?= htmlspecialchars($username ?? ''); ?>"
                    id="floatingInput" placeholder="Username" required>
                <label for="floatingInput">Username</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" name="password" class="form-control"
                    value="<?= htmlspecialchars($password ?? ''); ?>"
                    id="floatingPassword" placeholder="Password" required>
                <label for="floatingPassword">Password</label>
            </div>

            <button class="w-100 btn btn-lg btn-primary" type="submit">Login</button>
            <p class="mt-5 mb-3 text-muted">© 2026 ERP System</p>
        </form>
    </main>
</body>

</html>