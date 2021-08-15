This application is the test task for "ITRANSITION" company.

Application represents a command-line interface for data import from CSV table to MySQL database table.

Application provides an opportunity to test import from CSV without insertion data into database
(use --test option in command line for this).

ATTENTION!

- columns delimiter in CSV-file MUST be "," (comma)
- If Discontinued is pointed in CSV-file as supported for the Product, Discontinued date in database table sets to current date

Application does NOT import from CSV:
 - records with products, which costs less than 5 currency units and quantity of them is less than 10
 - records with products, which costs more than 1000 currency units
 - records with string columns, which contain "," and are not in quotes
   
Application imports WRONGLY from CSV
 - records with int columns, which contain non-numeric data. They might be set to 0 in the database table

INSTALLATION

After copying The Project files to the catalog you want, do the following.

    - make sure that php and composer are available by these names from console on your OS (environment variable contains correct path to them)
    - create ".env.local" file (if it has not been created yet) and put there the contents of the ".env" file
    - replace "DATABASE_URL" option value to the URL of your database
    - open console
    - change current directory of console to where you have just located the Project
    - import database "*.sql" file from "./import" directory of the Project to your MySQL database instance
    - execute the following commands

        composer install
        php bin/console doctrine:migrations:migrate

Use and enjoy!

USAGE

 Open console in your OS, change current directory to the directory of this Project and type:

    php bin/console app:product:import --filepath="/path/to/your/csv/file.csv"

 NOTICE: parameter "filepath" is required.

If you want to run this command in a test mode (import from CSV without insertion into database), run:

    php bin/console app:product:import --filepath="/path/to/your/csv/file.csv" --test

TESTING
 
 To run PHPUnit tests execute the following command.
    
    php bin/phpunit tests/

EXAMPLE of data from input CSV-file
 (copy this data to some CSV-file and use path to that file in the command option of "--filepath"):

    Product Code,Product Name,Product Description,Stock,Cost in GBP,Discontinued
    P0001,TV,32” Tv,10,399.99,
    P0002,Cd Player,Nice CD player,11,50.12,yes
    P0003,VCR,Top notch VCR,12,39.33,yes
    P0004,Bluray Player,Watch it in HD,1,24.55,
    P0005,XBOX360,Best.console.ever,5,30.44,
    P0006,PS3,Mind your details,3,24.99,
    P0007,24” Monitor,Awesome,,35.99,
    P0008,CPU,Speedy,12,25.43,
    P0009,Harddisk,Great for storing data,0,99.99,
    P0010,CD Bundle,Lots of fun,0,10,
    P0011,Misc Cables,error in export,,,
    P0012,TV,HD ready,45,50.55,
    P0013,Cd Player,Beats MP3,34,27.99,
    P0014,VCR,VHS rules,3,23,yes
    P0015,Bluray Player,Excellent picture,32,$4.33,
    P0015,Bluray Player,Excellent picture,32,4.33,
    P0016,24” Monitor,Visual candy,3,45,
    P0017,CPU,"Processing power, ideal for multimedia",4,4.22,
    P0018,Harddisk,More storage options,34,50,yes
    P0019,CD Bundle,Store all your data. Very convenient,23,3.44,
    P0020,Cd Player,Play CD's,56,30,
    P0021,VCR,Watch all those retro videos,12,3.55,yes
    P0022,Bluray Player,The future of home entertainment!,45,3,
    P0023,XBOX360,Amazing,23,50,
    P0024,PS3,Just don't go online,22,24.33,yes
    P0025,TV,Great for television,21,40,
    P0026,Cd Player,A personal favourite,0,34.55,
    P0027,VCR,Plays videos,34,1200.03,yes
    P0028,Bluray Player,Plays bluray's,32,1100.04,yes
    P0029,"MP3 Player","Plays MP3/WMA and WAV",32,988.00,"yes"
        