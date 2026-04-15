"""
Home page blueprint.

Serves `/index.html` using your existing `index.html` template file.
"""

from flask import Blueprint, render_template


def create_index_blueprint() -> Blueprint:
    """Create blueprint that serves the home page."""
    bp = Blueprint("pages_index", __name__)

    @bp.route("/index.html")
    def index_html():
        return render_template("index.html")

    return bp

