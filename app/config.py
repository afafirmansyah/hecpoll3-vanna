import os
import json
from dotenv import load_dotenv

load_dotenv()

FLASK_DEBUG = os.getenv("FLASK_DEBUG", "false").lower() == "true"
FLASK_HOST = os.getenv("FLASK_HOST", "0.0.0.0")
FLASK_PORT = int(os.getenv("FLASK_PORT", 5000))

GEMINI_API_KEY = os.getenv("GEMINI_API_KEY")
GEMINI_MODEL = os.getenv("GEMINI_MODEL", "models/gemini-2.5-flash")

MSSQL_CONN_STR = os.getenv("MSSQL_CONN_STR")

AUTH_USERS = json.loads(os.getenv("AUTH_USERS", '[{"email":"admin@hectronic.in","password":"admin"}]'))
