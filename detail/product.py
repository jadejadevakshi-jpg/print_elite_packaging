"""
Products page blueprint.

Serves `/products.html` using your existing `products.html` template.
"""

from flask import Blueprint, render_template


def create_product_blueprint() -> Blueprint:
    """Create blueprint that serves the products page."""
    bp = Blueprint("pages_product", __name__)

    @bp.route("/products.html")
    def products_html():
        return render_template("products.html")

    return bp

