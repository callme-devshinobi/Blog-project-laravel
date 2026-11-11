# DevNotes

**A multi-blog publishing platform built with Laravel.** Anyone can sign up, get their own blog, write posts, and share it at their own address, or on a domain they own.

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?logo=php&logoColor=white)
![License](https://img.shields.io/badge/license-Non--Commercial-blue)
![Status](https://img.shields.io/badge/status-in%20development-orange)

<!-- Add screenshots here once you have them, for example:
![Home page](docs/screenshots/home.png)
![Dashboard](docs/screenshots/dashboard.png)
-->

## What is DevNotes?

DevNotes started as a personal blog and grew into a place where each writer gets a blog of their own. Every user has their own posts, profile, and public pages, and the main site brings all the blogs together in one directory.

## Features

**For readers**
- Home page, all-posts page, and category pages, on the main site and on every blog
- Search across the whole site, or inside a single blog
- Single post pages with next / previous navigation
- Light and dark theme with a toggle (remembers your choice)
- Fully responsive layout

**For writers**
- Sign up with a personal blog name and get a blog right away
- Dashboard with post counts and recent activity
- Write posts with a title, category, excerpt, cover image, image description and tags
- Save drafts, preview them, then publish when ready
- Manage only your own posts (publish, delete)
- Profile page: photo, bio, tech stack, what and why you write, and contact details
- Blog name that follows your profile (defaults to "Your Name's Blog")
- Settings page: change password and delete your account

**Multi-blog**
- Every blog lives at `/@blog-name`, with its own home, posts, categories, About and Contact pages
- A directory of all blogs at `/blogs`
- About and Contact pages are filled in from each writer's profile
- Custom domains: a writer can connect a domain they own, verified with a DNS TXT record (so far only tested locally, see below)

## Built with

- [Laravel](https://laravel.com) and PHP
- Blade templates and components
- SQLite (any database Laravel supports will work)
- Vite and plain CSS (a single shared stylesheet with a design-token system)

## Requirements

- PHP 8.2 or newer
- [Composer](https://getcomposer.org)
- Node.js and npm
- A local server such as [Laravel Herd](https://herd.laravel.com), or `php artisan serve`

## Getting started

```bash
# 1. Clone the project
git clone https://github.com/callme-devshinobi/Blog-project-laravel.git
cd Blog-project-laravel

# 2. Install PHP and JavaScript dependencies
composer install
npm install

# 3. Create your environment file and app key
cp .env.example .env
php artisan key:generate

# 4. Create the database tables
php artisan migrate

# 5. Make uploaded images (post covers, profile photos) reachable
php artisan storage:link

# 6. Start the front-end build, and the app
npm run dev
php artisan serve
```

On Windows PowerShell, use `copy .env.example .env` for step 3.

Then open the address shown in the terminal (or `http://your-folder-name.test` if you use Herd), register an account, and write your first post.

> Posts need a category. If your database has none yet, add a few before creating posts (for example with a seeder, or in `php artisan tinker`).

### Environment settings

Set these in your `.env`:

| Variable | What it is for |
|---|---|
| `APP_URL` | The address of the app, for example `http://blogging.test` locally |
| `MAIN_HOSTS` | Extra hostnames that belong to DevNotes itself (comma separated, optional) |
| `DOMAIN_TARGET` | The hostname writers point their custom-domain CNAME at (defaults to your `APP_URL` host) |
| `SERVER_IP` | The server's IP address, shown to writers who need an A record |

Never commit your `.env` file. It holds your app key and database settings.

## How custom domains work

1. In **Settings**, a writer enters a domain they own, for example `myblog.com`.
2. DevNotes shows the DNS records to add: a TXT record that proves ownership, and a CNAME or A record that sends visitors to the app.
3. After the writer clicks **Verify**, a middleware recognises requests for that domain and shows their blog. Only public pages are served there, so login and the dashboard stay on the main site.

**Trying it locally with Laravel Herd**

```bash
herd link demoblog          # serves the same app at demoblog.test
```

Add `demoblog.test` as the custom domain in Settings, then mark it verified in `php artisan tinker`, because real DNS lookups cannot succeed locally:

```php
App\Models\Blog::where('custom_domain', 'demoblog.test')->first()
    ->forceFill(['custom_domain_verified_at' => now()])->save();
```

Real custom domains need the app to be deployed on a server with a public IP, and a way to issue HTTPS certificates on demand (for example Caddy's on-demand TLS).

## Project structure (the parts that matter)

```
app/Http/Controllers/
  BlogController.php        main site pages, dashboard, create / publish / delete posts
  PublicBlogController.php  the public pages of each blog (/@name/...)
  AuthController.php        sign up, log in, log out
  ProfileController.php     profile page
  SettingsController.php    password, account deletion, custom domain
app/Http/Middleware/
  ResolveCustomDomain.php   serves a blog on its custom domain
app/Models/                 User, Blog, Post, Category
resources/views/            Blade views; components/ holds the shared layouts
resources/css/app.css       the single stylesheet (light and dark themes)
routes/web.php              all routes
```

## Roadmap

- [ ] Moderation: report button, and an admin switch to suspend a blog (which also disables its custom domain)
- [ ] A contact form that actually sends messages
- [ ] A seeder with sample categories and posts
- [ ] Deployment guide, including HTTPS for custom domains
- [ ] Comments and follow / subscribe

## Contributing

This is a personal project, and it is not currently open for contributions. If you find a bug, feel free to open an issue.

## License

This project is licensed under the DevNotes Non-Commercial License. You may view and use it for personal and educational purposes, but commercial use is not allowed. See [LICENSE.md](LICENSE.md) for the full terms.

DevNotes is built on the [Laravel](https://laravel.com) framework, which is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Author

Built by **Dennis Onwuegbu**.
