<?php

namespace App;

use App\Mail\SendServiceMail;
use App\Modules\Auth\Models\Role as RoleModel;
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
    protected $guarded = ['email_verified_at', 'remember_token', 'created_at', 'updated_at'];

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
    protected $lang;


    // Расширяем модель
    public function __construct()
    {
        parent::__construct();

        $this->class = class_basename(__CLASS__);
        $this->model = "\App\\{$this->class}";
        $this->table = with($this)->getTable();
        $this->view = Str::snake($this->class);
        $this->lang = lang();
    }



    // Обратная связь один ко многим
    public function role() {
        return $this->belongsTo(RoleModel::class);
    }


    // Меняем шаблон письма при сбросе пароля
    public function sendPasswordResetNotification($token)
    {
        $title = __("{$this->lang}::f.link_to_change_password");
        $values = [
            'title' => __("{$this->lang}::f.you_forgot_password"),
            'btn' => __("{$this->lang}::f.reset_password"),
            'link' => route('password.reset', $token),
        ];
        $this->notify(new SendServiceMail($title , null, $values, 'service'));
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


    // Возвращает id роли администратора.
    public function getRoleIdAdmin() {
        return 3;
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



    // Записать IP пользователя.
    public function saveIp()
    {
        $this->ip = request()->ip();
        $this->save();
    }


    /********************** Дополнительные статичные методы **********************/


    // Возвращает объект пользователя. Принимает email пользователя.
    public static function getUserStatic($email)
    {
        $self = new self();
        return $self->model::where('email', $email)->first();
    }


    /*
     * Записывает IP пользователя.
     * $user_id_or_email - id или email пользователя.
     * $ip - IP пользователя.
     */
    public static function saveIpStatic($user_id_or_email, $ip)
    {
        $column = is_int($user_id_or_email) ? 'id' : 'email';
        DB::table('users')
            ->where($column, $user_id_or_email)
            ->update(['ip' => $ip]);
    }


    // Возвращает в массиве id ролей пользователей с доступом в админку
    public static function roleIdAdmin()
    {
        // Взязь из кэша
        if (cache()->has('roles_admin_ids')) {
            return cache()->get('roles_admin_ids');

        } else {

            // Запрос в БД
            $ids = DB::table('roles')->where('area', config('admin.user_areas')[2])->pluck('id')->toArray();
            if ($ids) {

                // Кэшируется запрос
                cache()->forever('roles_admin_ids', $ids);

                return $ids;
            }
        }
        return false;
    }


    // Проверяет есть ли у авторизированного пользователя доступ в админку
    public static function isAdminEditor()
    {
        $adminEditor = self::roleIdAdmin();
        $userRole = auth()->check() ? auth()->user()->role_id : null;

        return $adminEditor && $userRole && in_array($userRole, $adminEditor);
    }
}
