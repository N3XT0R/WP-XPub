# LinkedIn Publisher Setup

This guide explains how to configure the LinkedIn Publisher to automatically post articles to your LinkedIn personal
profile or an organization page.

## Requirements

- A LinkedIn account
- Admin access to a LinkedIn organization page (if posting as a company)
- A configured redirect endpoint at `/xpub/v1/oauth/linkedin/callback`
- Access to your WordPress admin area

---

## Step 1: Register a LinkedIn App

1. Visit: [https://www.linkedin.com/developers/apps](https://www.linkedin.com/developers/apps)
2. Click **"Create App"**
3. Fill out the required details:
    - **App Name** (e.g., "WordPress Publisher")
    - **Company/Organization**: Your personal name or an organization
    - **App Logo**: Required (at least 100×100 PNG)
    - **Privacy Policy URL**: Required (e.g., your site’s privacy or legal page)

4. After creation, copy the following:
    - `Client ID`
    - `Client Secret`

---

## Step 2: Set Redirect URL

In the app's **"Auth"** tab, add this as an authorized redirect URL:

