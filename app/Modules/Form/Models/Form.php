<?php

namespace App\Modules\Form\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];


    // Обратная связь один ко многим
    public function user() {
        return $this->belongsTo(User::class);
    }



    /*
     * Сохраним пользователя отправителя формы.
     * Если есть пользователь, то обновим его данные, если нет, то создадим.
     * Также если пользователь Админ или Редактор, то не будем обновлять его данные.
     * $data - данные формы.
     */
    public static function userFormSave($data)
    {
        $passwordDefault = '$2y$10$0v6wawOOs/cwp.wAPmbJNe4q3wUSnBqfV7UQL7YbpTtJE0dJ8bMKK'; // 123321q - такой пароль по-умолчанию у пользователей со статусом guest (не зарегистрированный пользователь)

        if (isset($data['email'])) {
            $dataUser['email'] = s($data['email'], null, true);
        } else {
            return false;
        }

        $user = new User();
        $noChangeUser = null;

        // Проверяем существует ли такой пользователь
        $issetUser = $user->getUser($dataUser['email']);
        if ($issetUser) {

            // Проверяем не админ ли или редактор
            $noChangeUser = $user->getAdmin($issetUser);

            // Если текущий пользователь не админ и не редактор, то обновим его данные
            if (!$noChangeUser) {

                if (isset($data['name'])) {
                    $dataUser['name'] = s($data['name']);
                }
                if (isset($data['tel'])) {
                    $dataUser['tel'] = s($data['tel']);
                }
                if (isset($data['ip'])) {
                    $dataUser['ip'] = $data['ip'];
                }

                $issetUser->fill($dataUser);
                $issetUser->update();
            }
            return $issetUser->id;


        // Если не существует, то созданим нового
        } else {

            $dataUser['role_id'] = array_search('guest', config('admin.user_roles')) ?: 2; // Устанавливается роль guest
            $dataUser['name'] = isset($data['name']) ? s($data['name']) : 'No name';
            if (isset($data['tel'])) {
                $dataUser['tel'] = s($data['tel']);
            }

            $dataUser['password'] = $passwordDefault;
            $dataUser['accept'] = $data['accept'] ?? '0';

            if (isset($data['ip'])) {
                $dataUser['ip'] = $data['ip'];
            }

            $user->fill($dataUser);
            if ($user->save()) {
                return $user->id;
            }
        }
        return false;
    }


    /*
     * Проверка google reCaptcha.
     * $data - данные формы.
     */
    public static function googleReCaptcha($data)
    {
        $recaptcha = $data['g-recaptcha-response'] ?? null;
        $postdata = http_build_query(
            [
                'secret' => env('RECAPTCHA_SECRET_KEY'),
                'response' => $recaptcha,
            ]
        );
        $opts = ['http' =>
            [
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postdata,
            ]
        ];
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $context  = stream_context_create($opts);
        $result = file_get_contents($url, false, $context);
        $check = json_decode($result);
        return $check->success;
    }
}
