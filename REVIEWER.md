# Note for the WordPress Plugin Reviewer

Hi there 👋

This plugin was developed following modern software architecture principles (including hexagonal architecture) to ensure
separation of concerns, testability, and long-term maintainability.

### About the use of `__('message', 'xpub-multi-channel-publisher')` with a hardcoded text domain:

We are aware that the use of a constant like `self::TEXT_DOMAIN` would be semantically cleaner in a structured
codebase.  
However, WordPress.org’s automated plugin scanner requires the text domain to be passed as a **literal string** in all
translation functions (e.g. `__()`, `_x()`, `_n()`).

To ensure compatibility with these requirements, we deliberately replaced the constant with the literal
`'xpub-multi-channel-publisher'` in all translation calls.

---

We hope this clarification helps during your review.

Thank you for your time and support!
