<?php

namespace App\Modules\Admin\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    /*protected $fillable = [
        'name', 'email', 'password',
    ];*/
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];



    protected $class;
    protected $model;
    protected $table;
    protected $view;


    // Расширяем модель
    public function __construct()
    {
        parent::__construct();

        $this->class = class_basename(__CLASS__);
        $this->model = "\App\\{$this->class}";
        $this->table = with($this)->getTable();
        $this->view = Str::snake($this->class);
    }



    // Обратная связь один ко многим
    public function role() {
        return $this->belongsTo(Role::class);
    }


    /********************** Дополнительные методы **********************/


    // Проверить роли пользователей, которым разрешена админка. Возвращает true или false.
    public function Admin() {
        return $this->role->area === config('admin.user_areas')[2];
    }


    // Проверить пользователя с ролью админ. Возвращает true или false.
    public function isAdmin() {
        return $this->role->name === config('admin.user_roles')[3];
    }


    // Возвращает объект пользователя. Принимает email пользователя.
    public function getUser($email)
    {
        //return DB::table($this->table)->where('email', $email)->first();
        return $this->model::where('email', $email)->first();
    }


    // Проверяет переданного пользователя, является ли он админом или редактором.
    public function getAdmin($user)
    {
        return $user->role->area === config('admin.user_roles')[3];
    }
}
