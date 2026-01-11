import flask

# Define a minimal AuthInterface if not already provided elsewhere
class AuthInterface:
    def get_user(self, flask_request):
        raise NotImplementedError
    def is_logged_in(self, user):
        raise NotImplementedError
    def override_config_for_user(self, user, config):
        raise NotImplementedError
    def login_form(self):
        raise NotImplementedError
    def login_handler(self, flask_request):
        raise NotImplementedError
    def callback_handler(self, flask_request):
        raise NotImplementedError
    def logout_handler(self, flask_request):
        raise NotImplementedError

class SimplePassword(AuthInterface):
    def __init__(self, users: list): 
        self.users = users

    def get_user(self, flask_request) -> any:
        return flask_request.cookies.get('user')

    def is_logged_in(self, user: any) -> bool:
        return user is not None

    def override_config_for_user(self, user: any, config: dict) -> dict:
        return config

    def login_form(self) -> str:
        return '''
        <html>
        <head>
          <meta charset="UTF-8" />
          <link rel="icon" type="image/png" href="/hectronic.svg" />
          <meta name="viewport" content="width=device-width, initial-scale=1.0" />
          <title>Sign In | Hectronic ChatBot</title>
        </head>
        <body style="background:#0f172a; font-family:'Roboto Slab','Segoe UI',Arial,sans-serif; color:#f1f5f9; margin:0; padding:0;">
          <div style="max-width:400px;margin:60px auto;background:#1e293b;border-radius:18px;box-shadow:0 8px 32px rgba(60,72,100,0.18);padding:2rem 2rem;">
            <div style="text-align:center;">
              <img src="/hectronic.png" alt="Hectronic Logo" style="width:300px;">
            </div>
            <p style="text-align:center; color:#cbd5e1; margin-bottom:2rem;">
              Please sign in to access the Hectronic ChatBot.
            </p>
            <div style="margin-top:0rem;">
              <form action="/auth/login" method="POST">
          <div style="margin-bottom:1.2rem;">
            <label for="email" style="display:block;font-size:0.95rem;color:#cbd5e1;font-weight:500;margin-bottom:0.3rem;">Email</label>
            <input type="email" id="email" name="email" required
              style="border:1px solid #334155;border-radius:8px;padding:0.75rem 1rem;font-size:1rem;width:100%;background:#0f172a;color:#f1f5f9;transition:border 0.2s;">
          </div>
          <div style="margin-bottom:1.2rem;">
            <label for="password" style="display:block;font-size:0.95rem;color:#cbd5e1;font-weight:500;margin-bottom:0.3rem;">Password</label>
            <input type="password" id="password" name="password" required
              style="border:1px solid #334155;border-radius:8px;padding:0.75rem 1rem;font-size:1rem;width:100%;background:#0f172a;color:#f1f5f9;transition:border 0.2s;">
          </div>
          <button type="submit"
            style="width:100%;background:linear-gradient(90deg,#2563eb 0%,#1d4ed8 100%);color:#fff;border:none;border-radius:8px;padding:0.75rem 1rem;font-size:1rem;font-weight:600;cursor:pointer;transition:background 0.2s,box-shadow 0.2s;box-shadow:0 2px 8px rgba(37,99,235,0.08);">
            Sign in
          </button>
              </form>
            </div>
          </div>
        </body>
        </html>
        '''

    def login_handler(self, flask_request) -> str:
        email = flask_request.form.get('email', '')
        password = flask_request.form.get('password', '')

        for user in self.users:
            if user["email"] == email and user["password"] == password:
                response = flask.make_response('Logged in as ' + email)
                response.set_cookie('user', email)
                response.headers['Location'] = '/'
                response.status_code = 302
                return response
        # Jika gagal, tampilkan pesan error yang jelas
        return flask.make_response('Login failed.', 401)

    def callback_handler(self, flask_request) -> str:
        user = flask_request.args['user']
        response = flask.make_response('Logged in as ' + user)
        response.set_cookie('user', user)
        return response

    def logout_handler(self, flask_request) -> str:
        response = flask.make_response('', 302)
        response.delete_cookie('user')
        response.headers['Location'] = '/auth/login'
        return response

    def logout_button(self) -> str:
        return '''
        <button onclick="window.location.href='/auth/logout'">Sign Out</button>
        '''
