"# Stupidity Ink

Custom tattoo shop & gallery website - Chitwan, Nepal

## Tech Stack

- Static HTML/CSS/JS
- Decap CMS (headless Git-based CMS)
- Netlify hosting with Netlify Identity for authentication

## Deployment to Netlify

### 1. Push to GitHub
Make sure your repo is pushed to GitHub.

### 2. Create Netlify Site
1. Go to [Netlify](https://app.netlify.com)
2. Click "Add new site" → "Import an existing project"
3. Connect your GitHub repo
4. Deploy settings should auto-detect (publish directory: `.`)

### 3. Enable Netlify Identity
1. Go to **Site settings** → **Identity**
2. Click **Enable Identity**
3. Under **Registration preferences**, select "Invite only" (recommended)
4. Under **External providers**, optionally add Google/GitHub login
5. Go to **Services** → **Git Gateway** → **Enable Git Gateway**

### 4. Invite Users
1. Go to **Identity** tab
2. Click **Invite users**
3. Enter email addresses for admins

### 5. Access Admin Panel
- Visit `https://your-site.netlify.app/admin/`
- Log in with your Netlify Identity credentials
- Manage gallery images, blog posts, and site content

## Project Structure

```
├── admin/              # Decap CMS admin panel
│   ├── index.html
│   └── config.yml
├── images/             # Static images
├── uploads/            # User uploaded images
├── fonts/              # Custom fonts
├── index.html          # Homepage
├── gallery.html        # Gallery page
├── blog.html           # Blog page
├── login.html          # Admin login page
├── images.json         # Content data (managed via CMS)
├── styles.css          # Styles
└── netlify.toml        # Netlify configuration
```

## Local Development

Just open `index.html` in a browser. No server required!

For CMS to work locally, you need to run it through Netlify Dev:
```bash
npx netlify-cli dev
```

## Content Management

Access the CMS at `/admin/` to manage:
- **Gallery Images** - Add/edit/delete tattoo images
- **Blog Posts** - Create and manage blog content
- **Site Settings** - Update logo, featured article, etc.

Changes are automatically committed to your Git repo and trigger a new deployment.
" 
