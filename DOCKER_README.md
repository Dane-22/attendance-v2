# Docker Setup for JAJR Attendance System

This Docker setup allows you to run the JAJR Attendance System locally with the same configuration as your online server. The same code will work in both environments.

## Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) installed on your Windows machine
- Docker Compose (included with Docker Desktop)

## Quick Start

1. **Open PowerShell or Command Prompt** in the project folder:
   ```powershell
   cd c:\wamp64\www\jajr-v2
   ```

2. **Start Docker containers**:
   ```powershell
   docker-compose up -d
   ```

3. **Access the application**:
   - **Website**: http://localhost:8080
   - **phpMyAdmin**: http://localhost:8081 (login: root / root_password)

4. **Stop containers** when done:
   ```powershell
   docker-compose down
   ```

## Services Included

| Service | Container Name | Port | Description |
|---------|---------------|------|-------------|
| PHP/Apache | jajr-app | 8080 | Main application |
| MySQL | jajr-db | 3306 | Database server |
| phpMyAdmin | jajr-phpmyadmin | 8081 | Database management UI |

## Database Credentials

### For Application (in Docker):
- **Host**: `db` (service name, automatically resolved by Docker)
- **Database**: `attendance-system`
- **Username**: `attendance_user`
- **Password**: `JaJr12390786@`

### For phpMyAdmin:
- **Username**: `root`
- **Password**: `root_password`

## How It Works

The database connection files (`conn/db_connection.php` and `config/database.php`) now use environment variables:

```php
$host = getenv('DB_HOST') ?: 'localhost';
```

This means:
- **In Docker**: Uses environment variables from `docker-compose.yml`
- **On Production Server**: Uses your server's `.env` file or default values
- **Fallback**: If no environment variables are set, uses the default values

## Common Commands

```powershell
# Start all services
docker-compose up -d

# View logs
docker-compose logs -f app
docker-compose logs -f db

# Stop services
docker-compose down

# Stop and remove all data (including database)
docker-compose down -v

# Rebuild after code changes
docker-compose up -d --build

# Execute commands inside containers
docker exec -it jajr-app bash
docker exec -it jajr-db mysql -u root -p
```

## Troubleshooting

### Port Already in Use
If you get "port already in use" errors, change the ports in `docker-compose.yml`:
```yaml
ports:
  - "8082:80"  # Use 8082 instead of 8080
```

### Database Connection Issues
Check that the environment variables are set correctly in `docker-compose.yml`:
```yaml
environment:
  - DB_HOST=db  # Must match the MySQL service name
```

### Permission Issues on Windows
If you encounter file permission issues, run PowerShell as Administrator.

## Switching Between Docker and WAMP

Since the code now uses environment variables with fallbacks:

1. **Docker**: Set `DB_HOST=db` in docker-compose.yml
2. **WAMP**: No changes needed - it will use `localhost` as fallback
3. **Production**: Set environment variables in your hosting panel or `.env` file

## Updating Database Schema

When you have new SQL migrations:
1. Place `.sql` files in `database/migrations/`
2. Restart the database container:
   ```powershell
   docker-compose restart db
   ```

Or manually import via phpMyAdmin at http://localhost:8081

## Production Deployment

When deploying to production:
1. The code remains unchanged
2. Set environment variables on your server:
   ```bash
   export DB_HOST=localhost
   export DB_DATABASE=attendance_v2
   export DB_USERNAME=your_production_user
   export DB_PASSWORD=your_production_password
   ```
3. Or use your hosting provider's environment variable configuration

## File Structure

```
jajr-v2/
├── docker/
│   ├── apache/
│   │   └── 000-default.conf    # Apache virtual host config
│   └── php/
│       └── php.ini             # PHP configuration
├── Dockerfile                  # PHP/Apache container definition
├── docker-compose.yml          # All services configuration
├── .dockerignore              # Files to exclude from Docker build
└── DOCKER_README.md          # This file
```

## Support

If you encounter issues:
1. Check Docker Desktop is running
2. Run `docker-compose logs` to see error messages
3. Verify database credentials match between `docker-compose.yml` and your application
