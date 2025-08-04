# LinkedIn Publisher Setup Guide

This guide explains how to configure the LinkedIn publisher in WP-XPub. The implementation uses LinkedIn's v2 API.

---

## 🔐 Step 1: Register a LinkedIn App

1. Visit: [https://www.linkedin.com/developers/apps](https://www.linkedin.com/developers/apps)
2. Click **"Create App"** and provide the required details:
   - **App Name** (e.g., "WordPress Publisher")
   - **Company/Organization**
   - **App Logo** (at least 100×100 PNG)
   - **Privacy Policy URL**
3. In the app's **Auth** tab:
   - Add the following **OAuth 2.0 scopes**:
     - `w_member_social`
     - `profile`
     - `openid`
   - Add the authorized redirect URL:
     ```
     https://yourdomain.com/wp-json/xpub/v1/oauth/linkedin/callback
     ```
4. Copy the generated `Client ID` and `Client Secret` for later use.

---

## ⚙️ Step 2: Configure WP-XPub

In your WordPress admin dashboard:

1. Navigate to **WP-XPub → OAuth Providers**
2. Click **Add Provider**
3. Select **Platform**: `LinkedIn`
4. Fill in:
   - **Client ID**
   - **Client Secret**
   - **Redirect URI**: (read-only, displayed for reference)

Click **Save** when finished.

---

## 🔄 Step 3: Authorize the Account

After saving the provider:

1. Click **Connect** to start the LinkedIn OAuth flow.
2. Sign in to LinkedIn and authorize the app.
3. On success, WP-XPub stores the access token and author URN.

The plugin can now publish posts to your LinkedIn profile.

---

## 📌 Notes

- The publisher posts to the authenticated user's personal profile using the LinkedIn v2 API.
- Required scopes: `w_member_social`, `profile`, `openid`.
- Publishing to organization pages is not currently supported.

