# News Aggregator API

A comprehensive Laravel-based news aggregation system that provides personalized news feeds based on user preferences. The system includes user authentication, news article management, categorization, and intelligent content filtering.

## 🚀 Features

- **User Authentication**: Secure registration and login with Laravel Sanctum
- **News Management**: Complete CRUD operations for articles, categories, publishers, and authors
- **Personalized Feeds**: User preference-based news recommendations
- **Advanced Filtering**: Multi-criteria filtering for articles (date, author, category, publisher)
- **Read Status Tracking**: Track read/unread status for user articles
- **Scheduled Updates**: Automated preferred news updates via Laravel scheduler
- **RESTful API**: Clean, well-documented REST API endpoints
- **Docker Support**: Containerized deployment with Docker Compose

## 🛠️ Tech Stack

- **Backend**: Laravel 11 (PHP 8.2+)
- **Database**: MySQL 8.0
- **Authentication**: Laravel Sanctum
- **Containerization**: Docker & Docker Compose
- **API Documentation**: Postman Collection
- **Scheduler**: Laravel Task Scheduling

## 📋 Prerequisites

- Docker and Docker Compose
- Git
- At least 4GB RAM available for Docker containers

## 🚀 Quick Start

### 1. Clone the Repository
```bash
cd /var/www/html/
git clone https://github.com/sameernuna/news_aggregator.git
cd news_aggregator
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Build and Run Docker Containers
```bash
docker compose up -d
```

### 4. Run Database Migrations
```bash
docker exec -it news-aggregator php /home/innoscripta/public_html/news-aggregator/artisan migrate
```

### 5. Seed Database with Sample Data
```bash
docker exec -it news-aggregator php /home/innoscripta/public_html/news-aggregator/artisan db:seed
```

### 6. Access the API
- **API Base URL**: `http://localhost:8086/api/v1`
- **Health Check**: `http://localhost:8086/api/test`

## 📚 API Documentation

### Postman Collection
- **Local Collection**: Import `News_Aggregator_API.postman_collection.json` into Postman

### API Documentation
- **Comprehensive Documentation**: See [API_Documentation.md](./API_Documentation.md) for detailed endpoint documentation

## 🔧 Scheduler Commands

The system includes automated commands for managing preferred news:

### Update Preferred News
```bash
# Update news for all users (last 3 days, max 50 articles per user)
docker exec -it news-aggregator php /home/innoscripta/public_html/news-aggregator/artisan news:update-preferred --days=3 --limit=50

# Update news for specific user
docker exec -it news-aggregator php /home/innoscripta/public_html/news-aggregator/artisan news:update-preferred --user-id=1
```

### Cleanup Old Preferred News
```bash
# Dry run to see what would be cleaned up
docker exec -it news-aggregator php /home/innoscripta/public_html/news-aggregator/artisan news:cleanup-preferred --dry-run

# Clean up articles older than 7 days
docker exec -it news-aggregator php /home/innoscripta/public_html/news-aggregator/artisan news:cleanup-preferred --days=7
```

## 📁 Project Structure

```
news_aggregator/
├── app/
│   ├── Console/Commands/          # Scheduler commands
│   ├── Http/Controllers/V1/       # API controllers
│   ├── Models/                    # Eloquent models
│   └── Helpers/                   # Helper classes
├── database/
│   ├── migrations/                # Database migrations
│   ├── seeders/                   # Database seeders
│   └── factories/                 # Model factories
├── routes/
│   └── api.php                    # API routes
├── docker-compose.yml             # Docker configuration
├── Dockerfile                     # Docker image definition
└── API_Documentation.md           # API documentation
```

## 🔐 Authentication

The API uses Laravel Sanctum for authentication:

1. **Register**: `POST /api/v1/auth/register`
2. **Login**: `POST /api/v1/auth/login`
3. **Use Token**: Include `Authorization: Bearer {token}` in headers
4. **Logout**: `POST /api/v1/auth/logout`

## 📊 API Endpoints Overview

### Public Endpoints
- `GET /articles` - Get all articles with filtering
- `GET /categories` - Get all categories
- `GET /publishers` - Get all publishers
- `GET /authors` - Get all authors

### Protected Endpoints (Require Authentication)
- `GET /preferences` - Get user preferences
- `GET /preferences/articles` - Get personalized news feed
- `POST /preferences/articles/mark-read` - Mark article as read
- `POST /preferences` - Create user preference
- `DELETE /preferences/clear` - Clear all preferences

## 🐳 Docker Configuration

### Services
- **news-aggregator**: Laravel application (Port 8086)
- **mysql**: MySQL database (Port 3307)

### Environment Variables
```env
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=news_db
DB_USERNAME=root
DB_PASSWORD=Innoscripta
```

## 🔍 Testing

### Test API Connection
```bash
curl http://localhost:8086/api/test
```

### Test Authentication
```bash
curl -H "Authorization: Bearer {your_token}" http://localhost:8086/api/test-auth
```

## 🚨 Troubleshooting

### Common Issues

1. **Port Already in Use**
   ```bash
   # Check what's using the port
   sudo lsof -i :8086
   # Stop conflicting service or change port in docker-compose.yml
   ```

2. **Database Connection Issues**
   ```bash
   # Check if MySQL container is running
   docker ps
   # Restart containers
   docker compose restart
   ```

3. **Permission Issues**
   ```bash
   # Fix storage permissions
   docker exec -it news-aggregator chmod -R 775 /home/innoscripta/public_html/news-aggregator/storage
   ```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 📞 Support

For support and questions:
- Create an issue in the GitHub repository
- Check the [API Documentation](./API_Documentation.md)
- Review the troubleshooting section above

## 🔄 Version History

- **v1.2.0**: Enhanced filtering capabilities for preferred articles
- **v1.1.0**: Added user preferences and preferred articles functionality
- **v1.0.0**: Initial release with basic CRUD operations

---

**Happy Coding! 🎉**