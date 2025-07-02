<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('keywords', function (Blueprint $table) {
            Illuminate\Database\UniqueConstraintViolationException 

            SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry '1-2' for key 'article_keyword.article_keyword_article_id_keyword_id_unique' (Connection: mysql, SQL: insert into `article_keyword` (`article_id`, `keyword_id`) values (1, 41), (1, 35), (1, 2), (1, 81))
          
            at news-aggregator/vendor/laravel/framework/src/Illuminate/Database/Connection.php:817
              813▕         // message to include the bindings with SQL, which will make this exception a
              814▕         // lot more helpful to the developer instead of just the database's errors.
              815▕         catch (Exception $e) {
              816▕             if ($this->isUniqueConstraintError($e)) {
            ➜ 817▕                 throw new UniqueConstraintViolationException(
              818▕                     $this->getName(), $query, $this->prepareBindings($bindings), $e
              819▕                 );
              820▕             }
              821▕ 
          
                +7 vendor frames 
          
            8   news-aggregator/database/seeders/ArticleKeywordSeeder.php:53
                Illuminate\Database\Eloquent\Relations\BelongsToMany::attach()
                +8 vendor frames 
          
            17  news-aggregator/database/seeders/DatabaseSeeder.php:25
                      $table->string('name', 100)->unique()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed
    }
};
