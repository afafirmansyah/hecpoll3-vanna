from functools import wraps
from flask import Blueprint, jsonify, Response, request, redirect
import flask
import uuid

from app.cache import MemoryCache
from app.auth import DBAuth
from app.vanna_setup import vn
from app.chat_history import ChatHistory

api = Blueprint("api", __name__)
cache = MemoryCache()
auth = DBAuth()
chat_history = ChatHistory()


def login_required(f):
    @wraps(f)
    def decorated_function(*args, **kwargs):
        user = auth.get_user(request)
        if not auth.is_logged_in(user):
            return redirect("/login")
        return f(*args, **kwargs)
    return decorated_function


def requires_cache(fields):
    def decorator(f):
        @wraps(f)
        def decorated(*args, **kwargs):
            id = request.args.get("id")
            if id is None:
                return jsonify({"type": "error", "error": "No id provided"})
            for field in fields:
                if cache.get(id=id, field=field) is None:
                    return jsonify({"type": "error", "error": f"No {field} found"})
            field_values = {field: cache.get(id=id, field=field) for field in fields}
            field_values["id"] = id
            return f(*args, **field_values, **kwargs)
        return decorated
    return decorator


# --- Auth routes ---

@api.route("/login", methods=["GET", "POST"])
def login():
    if request.method == "POST":
        return auth.login_handler(request)
    return auth.login_form()


@api.route("/logout")
def logout():
    return auth.logout_handler(request)


# --- API routes ---

@api.route("/api/v0/generate_sql", methods=["GET"])
def generate_sql():
    question = request.args.get("question")
    if question is None:
        return jsonify({"type": "error", "error": "No question provided"})
    id = cache.generate_id(question=question)
    sql = vn.generate_sql(question=question)
    cache.set(id=id, field="question", value=question)
    cache.set(id=id, field="sql", value=sql)
    return jsonify({"type": "sql", "id": id, "text": sql})


@api.route("/api/v0/run_sql", methods=["GET"])
@requires_cache(["sql"])
def run_sql(id: str, sql: str):
    try:
        df = vn.run_sql(sql=sql)
        cache.set(id=id, field="df", value=df)
        return jsonify({"type": "df", "id": id, "df": df.head(10).to_json(orient="records")})
    except Exception as e:
        return jsonify({"type": "error", "error": str(e)})


@api.route("/api/v0/download_csv", methods=["GET"])
@requires_cache(["df"])
def download_csv(id: str, df):
    csv = df.to_csv()
    return Response(csv, mimetype="text/csv", headers={"Content-disposition": f"attachment; filename={id}.csv"})


@api.route("/api/v0/generate_plotly_figure", methods=["GET"])
@requires_cache(["df", "question", "sql"])
def generate_plotly_figure(id: str, df, question, sql):
    try:
        code = vn.generate_plotly_code(question=question, sql=sql, df_metadata=f"Running df.dtypes gives:\n {df.dtypes}")
        fig = vn.get_plotly_figure(plotly_code=code, df=df, dark_mode=False)
        fig_json = fig.to_json()
        cache.set(id=id, field="fig_json", value=fig_json)
        return jsonify({"type": "plotly_figure", "id": id, "fig": fig_json})
    except Exception as e:
        import traceback
        traceback.print_exc()
        return jsonify({"type": "error", "error": str(e)})


@api.route("/api/v0/get_training_data", methods=["GET"])
def get_training_data():
    user = auth.get_user(request)
    if not auth.is_logged_in(user):
        return jsonify({"error": "Unauthorized"}), 401
    
    # Only allow specific users
    allowed_users = ["fauzi@hectronic.in", "mani@hectronic.in"]
    if user not in allowed_users:
        return jsonify({"error": "Forbidden"}), 403
    
    df = vn.get_training_data()
    return jsonify({"type": "df", "id": "training_data", "df": df.to_json(orient="records")})


@api.route("/api/v0/remove_training_data", methods=["POST"])
def remove_training_data():
    user = auth.get_user(request)
    if not auth.is_logged_in(user):
        return jsonify({"error": "Unauthorized"}), 401
    
    # Only allow specific users
    allowed_users = ["fauzi@hectronic.in", "mani@hectronic.in"]
    if user not in allowed_users:
        return jsonify({"error": "Forbidden"}), 403
    
    id = flask.request.json.get("id")
    if id is None:
        return jsonify({"type": "error", "error": "No id provided"})
    if vn.remove_training_data(id=id):
        return jsonify({"success": True})
    return jsonify({"type": "error", "error": "Couldn't remove training data"})


@api.route("/api/v0/bulk_remove_training_data", methods=["POST"])
def bulk_remove_training_data():
    user = auth.get_user(request)
    if not auth.is_logged_in(user):
        return jsonify({"error": "Unauthorized"}), 401
    
    # Only allow specific users
    allowed_users = ["fauzi@hectronic.in", "mani@hectronic.in"]
    if user not in allowed_users:
        return jsonify({"error": "Forbidden"}), 403
    
    ids = flask.request.json.get("ids", [])
    if not ids:
        return jsonify({"type": "error", "error": "No ids provided"})
    failed = []
    for id in ids:
        if not vn.remove_training_data(id=id):
            failed.append(id)
    if failed:
        return jsonify({"success": False, "error": f"Failed to remove {len(failed)} item(s)"})
    return jsonify({"success": True})


@api.route("/api/v0/train", methods=["POST"])
def add_training_data():
    user = auth.get_user(request)
    if not auth.is_logged_in(user):
        return jsonify({"error": "Unauthorized"}), 401
    
    # Only allow specific users
    allowed_users = ["fauzi@hectronic.in", "mani@hectronic.in"]
    if user not in allowed_users:
        return jsonify({"error": "Forbidden"}), 403
    
    question = flask.request.json.get("question")
    sql = flask.request.json.get("sql")
    ddl = flask.request.json.get("ddl")
    documentation = flask.request.json.get("documentation")
    try:
        id = vn.train(question=question, sql=sql, ddl=ddl, documentation=documentation)
        return jsonify({"id": id})
    except Exception as e:
        print("TRAINING ERROR", e)
        return jsonify({"type": "error", "error": str(e)})


@api.route("/api/v0/generate_followup_questions", methods=["GET"])
@requires_cache(["df", "question", "sql"])
def generate_followup_questions(id: str, df, question, sql):
    followup_questions = vn.generate_followup_questions(question=question, sql=sql, df=df)
    cache.set(id=id, field="followup_questions", value=followup_questions)
    return jsonify({"type": "question_list", "id": id, "questions": followup_questions, "header": "Here are some followup questions you can ask:"})


@api.route("/api/v0/load_question", methods=["GET"])
@requires_cache(["question", "sql", "df", "fig_json", "followup_questions"])
def load_question(id: str, question, sql, df, fig_json, followup_questions):
    try:
        return jsonify({
            "type": "question_cache", "id": id, "question": question, "sql": sql,
            "df": df.head(10).to_json(orient="records"), "fig": fig_json, "followup_questions": followup_questions,
        })
    except Exception as e:
        return jsonify({"type": "error", "error": str(e)})


@api.route("/api/v0/get_question_history", methods=["GET"])
def get_question_history():
    return jsonify({"type": "question_history", "questions": cache.get_all(field_list=["question"])})


@api.route("/api/v0/ask", methods=["POST"])
def ask_question():
    question = flask.request.json.get("question")
    if question is None:
        return jsonify({"error": "No question provided"})
    
    try:
        # Generate SQL
        sql = vn.generate_sql(question=question)
        
        # Run SQL
        df = vn.run_sql(sql=sql)
        
        # Return results
        return jsonify({
            "text": f"I found {len(df)} result(s) for your question.",
            "sql": sql,
            "df": df.head(100).to_json(orient="records") if not df.empty else None
        })
    except Exception as e:
        import traceback
        traceback.print_exc()
        return jsonify({"error": str(e)})


@api.route("/api/v0/health", methods=["GET"])
def health_check():
    try:
        vn.run_sql("SELECT 1")
        return jsonify({"status": "ok"})
    except Exception:
        return jsonify({"status": "error"}), 503


@api.route("/api/v0/chat/sessions", methods=["GET"])
def get_chat_sessions():
    user = auth.get_user(request)
    if not auth.is_logged_in(user):
        return jsonify({"error": "Unauthorized"}), 401
    
    sessions = chat_history.get_user_sessions(user)
    return jsonify({"sessions": sessions})


@api.route("/api/v0/chat/sessions", methods=["POST"])
def save_chat_session():
    user = auth.get_user(request)
    if not auth.is_logged_in(user):
        return jsonify({"error": "Unauthorized"}), 401
    
    data = flask.request.json
    session_id = data.get("session_id") or str(uuid.uuid4())
    title = data.get("title", "Untitled Chat")
    messages = data.get("messages", [])
    
    if chat_history.save_session(user, session_id, title, messages):
        return jsonify({"success": True, "session_id": session_id})
    return jsonify({"error": "Failed to save session"}), 500


@api.route("/api/v0/chat/sessions/<session_id>", methods=["GET"])
def get_chat_session(session_id):
    user = auth.get_user(request)
    if not auth.is_logged_in(user):
        return jsonify({"error": "Unauthorized"}), 401
    
    session = chat_history.get_session(user, session_id)
    if session:
        return jsonify({"session": session})
    return jsonify({"error": "Session not found"}), 404


@api.route("/api/v0/chat/sessions/<session_id>", methods=["DELETE"])
def delete_chat_session(session_id):
    user = auth.get_user(request)
    if not auth.is_logged_in(user):
        return jsonify({"error": "Unauthorized"}), 401
    
    if chat_history.delete_session(user, session_id):
        return jsonify({"success": True})
    return jsonify({"error": "Failed to delete session"}), 500


@api.route("/api/v0/chat/sessions/<session_id>/title", methods=["PUT"])
def update_chat_session_title(session_id):
    user = auth.get_user(request)
    if not auth.is_logged_in(user):
        return jsonify({"error": "Unauthorized"}), 401
    
    title = flask.request.json.get("title")
    if not title:
        return jsonify({"error": "Title is required"}), 400
    
    if chat_history.update_session_title(user, session_id, title):
        return jsonify({"success": True})
    return jsonify({"error": "Failed to update title"}), 500


@api.route("/api/v0/user/permissions", methods=["GET"])
def get_user_permissions():
    user = auth.get_user(request)
    if not auth.is_logged_in(user):
        return jsonify({"error": "Unauthorized"}), 401
    
    allowed_users = ["fauzi@hectronic.in", "mani@hectronic.in"]
    can_access_training = user in allowed_users
    
    return jsonify({
        "user": user,
        "can_access_training": can_access_training
    })


@api.route("/training-data", methods=["GET"])
@login_required
def training_data_page():
    user = auth.get_user(request)
    # Only allow specific users
    allowed_users = ["fauzi@hectronic.in", "mani@hectronic.in"]
    if user not in allowed_users:
        return "<h1>403 Forbidden</h1><p>You don't have permission to access this page.</p>", 403
    
    return flask.current_app.send_static_file("training-data.html")


@api.route("/")
@login_required
def root():
    return flask.current_app.send_static_file("index.html")
