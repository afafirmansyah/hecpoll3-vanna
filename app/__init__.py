from pathlib import Path
from flask import Flask


def create_app():
    project_root = Path(__file__).resolve().parents[1]
    static_folder = project_root / "static"

    app = Flask(__name__, static_url_path="", static_folder=str(static_folder))

    from app.routes import api
    app.register_blueprint(api)

    return app
