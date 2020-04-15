

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('tel')->nullable()->after('email');
            $table->index('tel');
            $table->string('address')->nullable()->after('tel');
            $table->integer('role_id')->unsigned()->after('address')->default('1');
            $table->foreign('role_id')->references('id')->on('roles');
            $table->string('status', 32)->default(config('add.user_statuses')[0])->after('password');
            $table->text('note')->nullable()->after('status');
            $table->string('ip', 32)->nullable()->after('note');
            $table->string('img')->default(config('add.img') . config('admin.imgUserDefault'))->after('ip');
            $table->enum('accept', ['0', '1'])->default('0')->after('ip');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
