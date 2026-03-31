from app import create_app
from app.config import FLASK_DEBUG, FLASK_HOST, FLASK_PORT

app = create_app()

if __name__ == "__main__":
    app.run(debug=FLASK_DEBUG, host=FLASK_HOST, port=FLASK_PORT)
