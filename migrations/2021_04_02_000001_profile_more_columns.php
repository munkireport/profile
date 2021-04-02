<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class ProfileMoreColumns extends Migration
{
    private $tableName = 'profile';

    public function up()
    {
        $capsule = new Capsule();
        $capsule::schema()->table($this->tableName, function (Blueprint $table) {

            // Allow nullable
            $table->string('profile_uuid')->nullable()->change();
            $table->string('profile_name')->nullable()->change();
            $table->string('profile_removal_allowed')->nullable()->change();
            $table->string('payload_name')->nullable()->change();
            $table->string('payload_display')->nullable()->change();
            $table->text('payload_data')->nullable()->change();

            // New columns
            $table->bigInteger('profile_install_date')->nullable();
            $table->string('profile_organization')->nullable();
            $table->string('profile_verification_state')->nullable();
            $table->string('user')->nullable();
            $table->text('profile_description')->nullable();

            $table->index('profile_organization');
            $table->index('profile_verification_state');
            $table->index('user');
        });
     }
    
    public function down()
    {
        $capsule = new Capsule();
        $capsule::schema()->table($this->tableName, function (Blueprint $table) {
            
            // Remove nullable
            $table->string('profile_uuid')->change();
            $table->string('profile_name')->change();
            $table->string('profile_removal_allowed')->change();
            $table->string('payload_name')->change();
            $table->string('payload_display')->change();
            $table->text('payload_data')->change();
            
            // Remove new columns
            $table->dropColumn('profile_install_date');
            $table->dropColumn('profile_organization');
            $table->dropColumn('profile_verification_state');
            $table->dropColumn('user');
            $table->dropColumn('profile_description');
        });
    }
}