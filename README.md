## build and run docker image
docker-compose up -d

#Create database and relevant tables
docker exec -it news-aggregator php /home/innoscripta/public_html/news-aggregator/artisan migrate

#insert seed data in tables
docker exec -it news-aggregator php /home/innoscripta/public_html/news-aggregator/artisan db:seed

------------------------scheduler -------------------------------
# Test with specific options, news published in last 3 days upto 50 per user
docker exec -it news-aggregator php /home/innoscripta/public_html/news-aggregator/artisan news:update-preferred --days=3 --limit=50

# Test for specific user
docker exec -it news-aggregator php /home/innoscripta/public_html/news-aggregator/artisan news:update-preferred --user-id=1

# Test cleanup command (dry run)
docker exec -it news-aggregator php /home/innoscripta/public_html/news-aggregator/artisan news:cleanup-preferred --dry-run

# Test cleanup command (actual)
docker exec -it news-aggregator php /home/innoscripta/public_html/news-aggregator/artisan news:cleanup-preferred --days=7