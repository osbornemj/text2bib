
## Converting references

If you want to convert a file of references to BibTeX format, go to

https://text2bib.economics.utoronto.ca

where an implementation of this system is publicly available.

## Modifying the system

If you want to install the system on your own server, fork or clone the repository.  The system uses the [Laravel](https://laravel.com) framework.

## Installation

- Create the database tables:
```
php artisan migrate
```

- Seed the database:
```
php artisan db:seed DatabaseSeeder
```

- Seed the examples:
```
php artisan db:seed ExampleSeeder
```

- Register as a user and then update the `is_admin` field in the `users` table for your user to 1, making you an administrator of the system.

## Code

The code that performs the conversion from text to BibTeX is in the `Converter` class in the `Services` directory.  The rest of the code creates a front end for users and administrators/developers.

When logged in as an administrator, you have access to the Admin page.  The most significant link on this page is `Examples`.  The `examples` and `example_fields` tables in the database contain the text (`source`) and correct conversion (`type` in `examples` table, fields in `example-fields` table) for a large number of references.  You can add examples by editing the `ExampleSeeder` (in `database/seeders`) and re-running that seeder.

## Workflow for modifying Converter

If you would like to modify the Converter to deal with a reference that is not correctly converted to BibTeX, here is a reasonable workflow.

- Convert the reference by uploading a file that contains it to the main public page.
- As an administrator, you will see a link above the resulting conversion "Format result for Examples Seeder".  Click that link, which creates an appropriate entry in a new window/tab.
- Copy the code into the `ExampleSeeder` to make it the last example in the array.
- Run ```php artisan db:seed EaxmpleSeeder```.
- Go to the Admin page and click "Examples".  The new example will be at the top of the page.  Click the "verbose" link under the example.
- Modify the code so that the example is converted correctly.
- Check that all other examples are still converted correctly, by clicking the link "Check all conversions" on the Examples page.

## License

This project, including Laravel framework, is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
