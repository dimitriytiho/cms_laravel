<?php

namespace App\Modules\Form\Controllers;

use App\App;
use App\Modules\Form\Models\Form;
use App\Mail\SendMail;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class FormController extends AppController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $c = $this->c = Str::lower($this->class);
        //$view = $this->view = Str::snake($this->class);
        App::set('c', $c);
        View::share(compact('class', 'c'));
    }


    public function contactUs(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();

            $passwordDefault = '$2y$10$0v6wawOOs/cwp.wAPmbJNe4q3wUSnBqfV7UQL7YbpTtJE0dJ8bMKK'; // 123321q - такой пароль по-умолчанию у пользователей со статусом guest (не зарегистрированный пользователь)

            // Валидация
            $rules = [
                'name' => 'required',
                'tel' => 'required',
                'email' => 'required|email',
                'message' => 'required',
                'accept' => 'accepted',
            ];

            $this->validate($request, $rules);

            // Данные пользователя
            $dataUser['email'] = s($data['email'], null, true);
            $user = new User();
            $noChangeUser = null;

            // Проверяем существует ли такой пользователь
            $issetUser = $user->getUser($dataUser['email']);
            if ($issetUser) {
                $userID = $issetUser->id;

                // Проверяем не админ ли
                $noChangeUser = $user->getAdmin($issetUser);

                // Если текущий пользователь не админ, то обновим его данные
                if (!$noChangeUser) {
                    $dataUser['name'] = s($data['name']);
                    $dataUser['tel'] = s($data['tel']);
                    $dataUser['ip'] = $request->ip();

                    $issetUser->fill($dataUser);
                    $issetUser->update();
                }

                // Если не существует, то созданим нового
            } else {

                $dataUser['role_id'] = array_search('guest', config('admin.user_roles')) ?: 2; // Устанавливается роль guest
                $dataUser['name'] = s($data['name']);
                $dataUser['tel'] = s($data['tel']);
                $dataUser['password'] = $passwordDefault;
                $dataUser['accept'] = $data['accept'] ? '1' : '0';
                $dataUser['ip'] = $request->ip();

                $user->fill($dataUser);
                if ($user->save()) {
                    $userID = $user->id;

                } else {

                    // Сообщение что-то пошло не так
                    App::getError($this->class, __METHOD__);
                }
            }


            // Данные form
            $dataForm['user_id'] = $userID;
            $dataForm['message'] = s($data['message']);
            $dataForm['ip'] = $request->ip();

            $form = new Form();
            $form->fill($dataForm);

            $method = Str::kebab(__FUNCTION__); // Из contactUs будет contact-us
            if ($form->save()) {
                $data['date'] = App::$registry->get('settings')['date_format_admin'] ?? 'd.m.Y H:i';

                // Письмо пользователю
                try {
                    $title = __("{$this->lang}::s.You_have_filled_out_form") . config('add.domain');
                    $body = __("{$this->lang}::s.Your_form_has_been_received");

                    Mail::to($data['email'])
                        ->send(new SendMail($title, $body));

                } catch (\Exception $e) {
                    App::getError("Error sending email admin: $e", __METHOD__, false);
                }

                // Письмо администратору
                try {
                    $template = Str::snake(__FUNCTION__); // Из contactUs будет contact_us
                    $title = __("{$this->lang}::s.Completed_form", ['name' => __("{$this->lang}::c.{$template}")]) . config('add.domain');
                    $email_admin = \App\Helpers\Str::strToArr(App::$registry->get('settings')['admin_email'] ?? null);

                    Mail::to($email_admin)
                        ->send(new SendMail($title, null, $data, $template));

                } catch (\Exception $e) {
                    App::getError("Error sending email admin: $e", __METHOD__, false);
                }

                // Сообщение об успехе
                session()->put('success', __("{$this->lang}::s.Your_form_successfully"));
                return redirect()->route('index');
            }
        }
        // Сообщение что-то пошло не так
        App::getError("{$this->class} request", __METHOD__);
    }
}
