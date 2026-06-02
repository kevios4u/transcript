<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NILEST Transcript Application System</title>

  <link rel="stylesheet" href="./assets/css/student-login.css" />
</head>

<body>
  <main>
    <div class="container">
  
      <!-- Logo -->
      <div class="logo">
        <img src="./assets/images/nilest-logo.png"
             alt="NILEST Logo">
      </div>

      <div class="apply-dash">
        <!-- Student Login -->
        <section class="staff-login">
          <h4>Student Login</h4>

          <form action="student-login-process.php" method="post" enctype="multipart/form-data">

            <label for="user">Reg No.:</label>
            <input
              type="text"
              name="user"
              id="user"
              placeholder="Registration Number"
              required
            />

            <label for="pass">State of Origin:</label>
            <input
              type="text"
              name="pass"
              id="pass"
              placeholder="State of Origin"
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

            <div class="info">
              <a href="#">Help?</a>
              <a href="index.php">Back to Home</a>
            </div>

          </form>
        </section>

      </div>
    </div>
  </main>
  <footer class="login-footer">
    <p>Contact us: 07012345678 | info@yahoo.com</p>
    <p>
      <a href="index.php">Home</a>
      <a href="#">Help</a>
    </p>
  </footer>
</body>
</html>
