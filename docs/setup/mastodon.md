# Mastodon Publisher Setup Guide

This guide describes how to configure OAuth authentication for the Mastodon publisher used by WP-XPub.

---

## 🔐 Step 1: Create a Mastodon Application

1. Log in to your Mastodon instance (e.g., `https://mastodon.social`)
2. Go to: `Preferences` → `Development` → `New Application`
3. Fill in the following details:
    - **Application Name**: WP-XPub
    - **Redirect URI**:
      ```
      https://yourdomain.com/wp-json/xpub/oauth/callback
      ```
    - **Scopes** (at minimum):
      ```
      write:statuses
      ```

4. After creation, copy the following values:
    - `Client ID`
    - `Client Secret`

---

## ⚙️ Step 2: Configure WP-XPub

Go to your WordPress admin dashboard:

1. Open **WP-XPub → OAuth Providers**
2. Click **Add Provider**
3. Choose **Platform**: `Mastodon`
4. Fill in:
    - **Client ID**: *(from previous step)*
    - **Client Secret**: *(from previous step)*
    - **Instance Domain**: e.g. `mastodon.social`
    - **Redirect URI**: (read-only, shown for convenience)

Click **Save**.

---

## 🔄 Step 3: Authorize the Account

After saving, a **Connect** button will appear. Click it to log in to Mastodon and authorize your app.

Once connected, the access token is stored securely.  
WP-XPub will refresh tokens and publish posts automatically.

---

## 📌 Notes

- Mastodon is a decentralized platform. This means every user belongs to an **instance** (server).
- WP-XPub supports custom instances (e.g. `chaos.social`, `fosstodon.org`, etc.)
- Only `write:statuses` is required for publishing. Read scopes are optional.

