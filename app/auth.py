import flask
import pyodbc
import bcrypt
from app.config import MSSQL_CONN_STR


def _check_webuser(login, password):
    try:
        conn = pyodbc.connect(MSSQL_CONN_STR)
        cursor = conn.cursor()
        cursor.execute(
            "SELECT email, name, password FROM WEBUSERS WHERE (email=? OR username=?) AND is_active=1",
            login, login
        )
        row = cursor.fetchone()
        conn.close()
        if row and bcrypt.checkpw(password.encode("utf-8"), row.password.encode("utf-8")):
            return row.email, row.name
    except Exception as e:
        print("DB auth error:", e)
    return None, None


class DBAuth:
    def get_user(self, flask_request):
        return flask_request.cookies.get("user")

    def is_logged_in(self, user):
        return user is not None

    def login_form(self):
        return '''
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/svg+xml" href="/hectronic.svg">
  <title>HecPoll 3 - ChatBot</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" crossorigin="anonymous">
  <style>
    *, *::before, *::after { box-sizing: border-box; }
    html, body { margin: 0; padding: 0; height: 100%; overflow: hidden; }
    body {
      background: linear-gradient(135deg, #3b82f6 0%, #14b8a6 100%);
      display: flex; align-items: center; justify-content: center;
      padding: 20px; font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
    }
    .login-card {
      background: white; border-radius: 12px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
      padding: 2rem; width: 100%; max-width: 380px; border: none;
    }
    .logo { max-width: 280px; height: auto; margin-bottom: 1rem; }
    .text-center { text-align: center; display: flex; justify-content: center; align-items: center; }
    .mb-3 { margin-bottom: 1rem; }
    .mb-4 { margin-bottom: 1.5rem; }
    .me-2 { margin-right: 0.5rem; }
    .form-label { display: block; margin-bottom: 0.5rem; font-weight: 500; color: #374151; font-size: 0.875rem; }
    .form-control {
      border-radius: 8px; border: 1px solid #ddd; padding: 12px 15px;
      font-size: 1rem; width: 100%; box-sizing: border-box; transition: all 0.3s ease;
    }
    .form-control:focus { border-color: #3b82f6; box-shadow: 0 0 0 0.2rem rgba(59,130,246,0.25); outline: none; }
    .password-field { position: relative; }
    .password-toggle {
      position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
      background: none; border: none; color: #6c757d; cursor: pointer; padding: 0;
    }
    .password-toggle:hover { color: #3b82f6; }
    .btn-primary {
      background: linear-gradient(135deg, #3b82f6 0%, #14b8a6 100%);
      border: none; border-radius: 8px; padding: 12px;
      font-weight: 600; width: 100%; color: white; cursor: pointer;
      font-size: 1rem; transition: all 0.3s ease;
    }
    .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 5px 15px rgba(59,130,246,0.4); }
    .alert-danger {
      background-color: #fef2f2; color: #dc2626;
      border: 1px solid #fecaca; border-radius: 8px;
      padding: 8px 12px; margin-bottom: 1rem; font-size: 0.75rem;
    }
  </style>
</head>
<body>
  <div class="login-card">
    <div class="text-center mb-4">
      <img src="/hectronic.png" alt="Hectronic" class="logo">
    </div>
    <form action="/login" method="POST">
      <div id="error-msg" class="alert-danger" style="display:none;"><i class="fas fa-exclamation-triangle me-2"></i>Invalid credentials or account inactive</div>
      <div class="mb-3">
        <label for="username" class="form-label">
          <i class="fas fa-user me-2"></i>Username
        </label>
        <input type="text" class="form-control" id="username" name="username" required autofocus>
      </div>
      <div class="mb-4">
        <label for="password" class="form-label">
          <i class="fas fa-lock me-2"></i>Password
        </label>
        <div class="password-field">
          <input type="password" class="form-control" id="password" name="password" required>
          <button type="button" class="password-toggle" onclick="togglePassword()">
            <i class="fas fa-eye" id="toggleIcon"></i>
          </button>
        </div>
      </div>
      <button type="submit" class="btn-primary">
        <i class="fas fa-sign-in-alt me-2"></i>Sign In
      </button>
    </form>
  </div>
  <script>
    if (window.location.search.includes('error=1')) {
      document.getElementById('error-msg').style.display = 'block';
    }
    function togglePassword() {
      const p = document.getElementById("password");
      const i = document.getElementById("toggleIcon");
      if (p.type === "password") { p.type = "text"; i.classList.replace("fa-eye","fa-eye-slash"); }
      else { p.type = "password"; i.classList.replace("fa-eye-slash","fa-eye"); }
    }
  </script>
</body>
</html>
        '''

    def login_handler(self, flask_request):
        login = flask_request.form.get("username", "")
        password = flask_request.form.get("password", "")

        email, name = _check_webuser(login, password)
        if email:
            response = flask.make_response("", 302)
            response.set_cookie("user", email)
            response.set_cookie("user_name", name or email.split("@")[0], samesite="Lax")
            response.headers["Location"] = "/"
            return response

        return flask.redirect("/login?error=1")

    def logout_handler(self, flask_request):
        response = flask.make_response("", 302)
        response.delete_cookie("user")
        response.delete_cookie("user_name")
        response.headers["Location"] = "/login"
        return response
