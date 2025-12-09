# FindIt - Setup Guide

This guide will help you clone the repository, install dependencies, and set up Ollama and n8n for the FindIt project.

## Prerequisites

Before starting, make sure you have the following installed:
- Git
- PHP 8.1 or higher
- Composer
- Node.js (v16+) and npm
- Docker (for Ollama and n8n)

## Step 1: Clone the Repository

```bash
git clone https://github.com/goterajohnallanmark/findit.git
cd findit
```

## Step 2: Install PHP Dependencies

```bash
composer install
```

## Step 3: Install Node.js Dependencies

```bash
npm install
```

## Step 4: Set Up Environment Variables

Copy the `.env.example` file to `.env`:

```bash
cp .env.example .env
```

Or on Windows (PowerShell):

```powershell
Copy-Item .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

## Step 5: Set Up Database

Run the database migrations:

```bash
php artisan migrate
```

(Optional) Seed the database with sample data:

```bash
php artisan db:seed
```

## Step 6: Build Frontend Assets

```bash
npm run build
```

Or for development with hot reload:

```bash
npm run dev
```

## Step 7: Start the Laravel Development Server

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

---

## Setting Up Ollama (Local LLM)

Ollama allows you to run large language models locally.

### Install Ollama

1. Download from: https://ollama.ai
2. Run the installer for your operating system

### Pull a Model

```bash
ollama pull mistral
```

Or choose another model:
- `ollama pull llama2` (13B model)
- `ollama pull neural-chat` (compact model)

### Run Ollama Server

```bash
ollama serve
```

By default, Ollama runs on `http://localhost:11434`

### Configure Your Laravel App

In your `.env` file, add:

```
OLLAMA_HOST=http://localhost:11434
OLLAMA_MODEL=mistral
```

Update your application code to use the Ollama API endpoint for AI features.

---

## Setting Up n8n (Workflow Automation)

n8n is a low-code workflow automation tool that can integrate with your application.

### Option 1: Using Docker (Recommended)

```bash
docker run -it --rm --name n8n -p 5678:5678 -v n8n_data:/home/node/.n8n n8nio/n8n
```

### Option 2: Using Docker Compose

Create a `docker-compose.yml` file:

```yaml
version: '3'

services:
  n8n:
    image: n8nio/n8n
    ports:
      - "5678:5678"
    environment:
      - N8N_BASIC_AUTH_ACTIVE=true
      - N8N_BASIC_AUTH_USER=admin
      - N8N_BASIC_AUTH_PASSWORD=your_password_here
    volumes:
      - n8n_data:/home/node/.n8n
    restart: unless-stopped

volumes:
  n8n_data:
```

Then run:

```bash
docker-compose up -d
```

### Access n8n

Open your browser and go to: `http://localhost:5678`

Default credentials:
- Username: `admin`
- Password: `your_password_here` (change in docker-compose.yml)

### Create Workflows in n8n

1. Click "New Workflow"
2. Add nodes to:
   - Receive webhooks from your Laravel app
   - Process data
   - Send notifications
   - Integrate with external services

### Connect n8n to Your Laravel App

In your Laravel app, you can trigger n8n workflows via webhooks:

```php
use Illuminate\Support\Facades\Http;

Http::post('http://localhost:5678/webhook/your-webhook-path', [
    'data' => $yourData
]);
```

---

## Complete Setup Script (Windows PowerShell)

```powershell
# Clone repository
git clone https://github.com/goterajohnallanmark/findit.git
cd findit

# Install dependencies
composer install
npm install

# Setup environment
Copy-Item .env.example .env
php artisan key:generate

# Setup database
php artisan migrate
php artisan db:seed

# Build assets
npm run build

# Start services in separate terminals:
# Terminal 1 - Laravel
php artisan serve

# Terminal 2 - Ollama (if using)
ollama serve

# Terminal 3 - n8n (if using Docker)
docker-compose up -d
```

---

## Complete Setup Script (Linux/Mac Bash)

```bash
# Clone repository
git clone https://github.com/goterajohnallanmark/findit.git
cd findit

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Setup database
php artisan migrate
php artisan db:seed

# Build assets
npm run build

# Start services in separate terminals:
# Terminal 1 - Laravel
php artisan serve

# Terminal 2 - Ollama (if using)
ollama serve

# Terminal 3 - n8n (if using Docker)
docker-compose up -d
```

---

## Troubleshooting

### Composer/PHP Issues
- Ensure PHP version is 8.1+: `php -v`
- Clear composer cache: `composer clear-cache`

### Node.js Issues
- Ensure Node.js version is v16+: `node -v`
- Clear npm cache: `npm cache clean --force`

### Database Issues
- Check database credentials in `.env`
- Ensure database server is running
- Check migration files for errors

### Ollama Issues
- Ensure Ollama is running: `ollama serve`
- Check connectivity: `curl http://localhost:11434/api/tags`

### n8n Issues
- Check Docker is running: `docker ps`
- View logs: `docker logs n8n`
- Clear browser cache if UI doesn't load

---

## Documentation

- [Laravel Documentation](https://laravel.com/docs)
- [Ollama Documentation](https://github.com/ollama/ollama)
- [n8n Documentation](https://docs.n8n.io)
- [n8n API](https://docs.n8n.io/api)

---

## Support

For issues or questions, please open an issue on GitHub: https://github.com/goterajohnallanmark/findit/issues
