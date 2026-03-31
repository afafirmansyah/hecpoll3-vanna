import os
from pathlib import Path
from vanna.chromadb import ChromaDB_VectorStore
from vanna.google import GoogleGeminiChat
from app.config import GEMINI_API_KEY, GEMINI_MODEL, MSSQL_CONN_STR

PROJECT_ROOT = Path(__file__).resolve().parents[1]
CHROMADB_PATH = Path(os.getenv("CHROMADB_PATH", str(PROJECT_ROOT / "data" / "chromadb_data"))).resolve()
CHROMADB_PATH.mkdir(parents=True, exist_ok=True)


class MyVanna(ChromaDB_VectorStore, GoogleGeminiChat):
    def __init__(self, config=None):
        ChromaDB_VectorStore.__init__(self, config={"path": str(CHROMADB_PATH)})
        GoogleGeminiChat.__init__(self, config={"api_key": GEMINI_API_KEY, "model": GEMINI_MODEL})


vn = MyVanna()
vn.connect_to_mssql(odbc_conn_str=MSSQL_CONN_STR)
