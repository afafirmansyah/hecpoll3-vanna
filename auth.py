from vanna.flask.auth import AuthInterface, SimplePassword
import flask
from vanna.flask import VannaFlaskApp 

VannaFlaskApp(
    auth=SimplePassword(users=[{"email": "admin@example.com", "password": "password"}]),
    allow_llm_to_see_data=True,
    show_training_data=True,
    sql=True,
    table=True,
    chart=True,
    summarization=False,
    ask_results_correct=True,
).run()