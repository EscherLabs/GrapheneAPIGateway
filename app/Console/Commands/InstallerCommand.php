<?php

namespace App\Console\Commands;

use App\Environment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class InstallerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installs and Configures GrapheneAPIGateway';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param  \App\DripEmailer  $drip
     * @return mixed
     */
    public function handle()
    {
        $this->info("The database connection is currently configured as follows:");
        $this->table([
            'DB_HOST','DB_PORT','DB_DATABASE','DB_USERNAME','DB_PASSWORD'
        ],[[
            env('DB_HOST'),env('DB_PORT'),env('DB_DATABASE'),env('DB_USERNAME'),'*****'
        ]]);
        $environments = null;
        try{
            $environments = Environment::select('domain','name')->get();
            $this->info("Sucessfully Connected to Database!");
        }
        catch(\Illuminate\Database\QueryException $e) {
            try{
                Artisan::call('migrate',['--force' => true]);
                $this->info("Sucessfully Ran Database Migrations!");
                if ($this->confirm('Would you like to see the database with some defaults?')) {
                    Artisan::call('db:seed',['--force' => true]);
                }
            }
            catch(\Illuminate\Database\QueryException $e) {
                if ($this->confirm('Unable to connect to the database.  Do you want to configure the database now?')) {
                    $DB_HOST = $this->anticipate('What is the mysql server host name? (example: 127.0.0.1)',['localhost','127.0.0.1']);
                    $DB_PORT = $this->anticipate('What port is the mysql server listening on? (example: 3306)',['3306']);
                    $DB_DATABASE = $this->anticipate('What is the GrapheneAPIGateway mysql database name? (example: GrapheneAPIGateway)',['GrapheneAPIGateway']);
                    $DB_USERNAME = $this->ask('What is the username for the '.$DB_DATABASE.' Database?');
                    $DB_PASSWORD = $this->ask('What is the password for the '.$DB_DATABASE.' Database?');

                    $this->info("If you have not already done so, please set up the mysql database:");
                    $this->info("(The commands below may be of assistance in setting this up)\n");
                    $this->line("$ mysql -u root -h $DB_HOST -P $DB_PORT");
                    $this->line("> CREATE DATABASE $DB_DATABASE;");
                    $this->line("> CREATE USER '$DB_USERNAME'@'$DB_HOST' IDENTIFIED BY '$DB_PASSWORD';");
                    $this->line("> GRANT ALL PRIVILEGES ON $DB_DATABASE.* TO '$DB_USERNAME'@'$DB_HOST' WITH GRANT OPTION;");

                    $this->info("\nOnce you have set up your database, create a new '.env' file at the root of the GrapheneAPIServer directory with the following contents:\n");
                    $this->line("DB_HOST=$DB_HOST");
                    $this->line("DB_PORT=$DB_PORT");
                    $this->line("DB_DATABASE=$DB_DATABASE");
                    $this->line("DB_USERNAME=$DB_USERNAME");
                    $this->line("DB_PASSWORD=$DB_PASSWORD");
                    echo "\n";
                    
                    $this->info('Please Re-Run the "php artisan install" script once the above steps have been completed!');
                } else {
                    $this->error('Database not configured properly! (Run php artisan install for assitance)');
                }
                exit();
            } 
        }
        $missing_params = false;
        if (env('APP_KEY') === null) {
            $this->info("An APP_KEY has not been defined.  Creating one now...");
            $missing_params = true;
            $APP_KEY = md5(microtime());
        }
        if (env('APP_DEBUG') === null) {
            if ($this->confirm("Will this be running in a production environment? (Should we enable APP_DEBUG?)")) {
                $APP_DEBUG = 'true';
            } else {
                $APP_DEBUG = 'false';
            }
            $missing_params = true;
        }
        if (env('AUTH_USER') === null) {
            $this->info("No Administrative API Account has been defined!");
            $this->info("(This is the master account (username/password) used to programiatically configure the GrapheneAPIServer)");
            $AUTH_USER = $this->ask("Please enter the Administrative API Account Username");
            $this->info("Auto-generating the Administiative API Account Password...");
            $AUTH_PASSWORD = md5(microtime());
            $missing_params = true;
        }

        if ($missing_params === true) {
            $this->info("\nPlease update the '.env' file at the root of the GrapheneAPIServer directory with the following contents:");
            $this->info("Warning! Do not overwrite the existing DB* Database Configuration!\n");
            if (isset($APP_KEY)){ $this->line("APP_KEY=$APP_KEY"); };
            if (isset($APP_DEBUG)){ $this->line("APP_DEBUG=$APP_DEBUG"); };
            if (isset($AUTH_USER)){ $this->line("AUTH_USER=$AUTH_USER"); };
            if (isset($AUTH_PASSWORD)){ $this->line("AUTH_PASSWORD=$AUTH_PASSWORD"); };
            echo "\n";
            $this->info('Please Re-Run the "php artisan install" script once the above steps have been completed!');
        } else {
            $this->info("Everything looks great! Your GrapheneAPIGateway is now set up!");
        }
    }
}