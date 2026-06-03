<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NILEST Transcript Application System</title>

  <link rel="stylesheet" href="./assets/css/index.css" />
</head>

<body>
  <main>
    <div class="container">

      <!-- Logo and Institute Name -->
      <div class="logo-section">
        <img src="./assets/images/nilest-logo.png" alt="NILEST Logo" class="logo-img">
      </div>

      <div class="apply-dash">

        <!-- Application Guide -->
        <section class="apply">
          <h4>How to Apply for Transcript</h4>

          <p>→ Login <a href="student-login.php">here</a> to access the student dashboard.</p>
          <p>→ Click on Apply.</p>
          <p>→ Fill in the correct recipient address.</p>
          <p>→ Click on Submit.</p>
          <p>
            → On the dashboard, you can access the
            <strong>Transcript Status</strong> menu to verify the status of your
            application.
          </p>
        </section>

        <!-- Staff Login -->
        <section class="staff-login">
          <h4>Staff Login</h4>

          <form action="login-process.php" method="post" enctype="multipart/form-data">

            <label for="user">Username:</label>
            <input
              type="text"
              name="user"
              id="user"
              placeholder="Username"
              required
            />

            <label for="pass">Password:</label>
            <input
              type="password"
              name="pass"
              id="pass"
              placeholder="Password"
              required
            />

            <div class="buttons">
              <button type="submit" class="submit">
                Login
              </button>

              <button type="reset" class="reset">
                Clear
              </button>
            </div>

          </form>
          <p><a href="#">Forgotten Password?</a></p>
          <p><a href="#">Help?</a></p>
        </section>

      </div>
    </div>
  </main>
</body>
</html>