<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class ProfileAddProfileInstallMethod extends Migration
{
    private $tableName = 'profile';

    public function up()
    {
        $capsule = new Capsule();
        $capsule::schema()->table($this->tableName, function (Blueprint $table) {

            // New columns
            $table->string('profile_method')->nullable();

            $table->index('profile_method');
        });
     }
    
    public function down()
    {
        $capsule = new Capsule();
        $capsule::schema()->table($this->tableName, function (Blueprint $table) {
            
            // Remove new columns
            $table->dropColumn('profile_method');
        });
    }
}
