# laravel-telescope-search-keyword

![alt text](https://greelogix.com/laravel-telescope-search-keyword.png)

1. In [app] directory place custom controller code in following path [app\Overrides\Telescope\EntryController.php]
2. Create a service provider using following command,

    *php artisan make:provider MyServiceProvider*
    
3. Place MyServiceProvider code in newly created provider.
4. Go into [config\app.php] file and register your new provider class,

 'providers' => [
 
      App\Providers\MyServiceProvider::class,
 ]
 
5. Now just run following command to regenerate the autoload files,

    *composer dump-autoload*

Thats it! Now if you type a keyword in Telescope Tag search field it will retrieve all concerned entries, Note if you use ":" in the search box then it will behave like default #Tag Search.
