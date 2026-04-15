"""
About page blueprint.

Serves `/about.html` using your existing `about.html` template.
"""

from flask import Blueprint, render_template


def create_about_blueprint() -> Blueprint:
    """Create blueprint that serves the About page."""
    bp = Blueprint("pages_about", __name__)

    @bp.route("/about.html")
    def about_html():
        return render_template("about.html")

    return bp

