<?php

namespace App\Modules\Form\Controllers;

use App\Models\Main;
use App\Modules\Form\Models\Form;
use App\Mail\SendMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use App\Helpers\Str as HelpersStr;

class FormController extends AppController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $c = $this->c = Str::lower($this->class);
        //$view = $this->view = Str::snake($this->class);
        Main::set('c', $c);
        View::share(compact('class', 'c'));
    }


    public function contactUs(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();

            // Валидация
            $rules = [
                'name' => 'required|string|max:190',
                'tel' => 'required|string|max:190',
                'email' => 'required|string|email|max:190',
                'message' => 'required', 'string',
                'accept' => 'accepted',
                //'g-recaptcha-response' => 'required|recaptcha',
            ];
            $this->validate($request, $rules);

            //unset($data['g-recaptcha-response']);

            /*if (Form::googleReCaptcha($data)) {

                // code form...
            }*/

            $data['accept'] = $data['accept'] ? '1' : '0'; // В чекбокс запишем 1
            $data['ip'] = $request->ip();

            // Сохраним пользователя отправителя формы. Если есть пользователь, то обновим его данные, если нет, то создадим. Также если пользователь Админ или Редактор, то не будем обновлять его данные.
            $userId = Form::userFormSave($data);
            if (!$userId) {
                Main::getError($this->class, __METHOD__);
            }

            // Данные form
            $dataForm['user_id'] = $userId;
            $dataForm['message'] = s($data['message']);
            $dataForm['ip'] = $request->ip();

            $form = new Form();
            $form->fill($dataForm);

            //$method = Str::kebab(__FUNCTION__); // Из contactUs будет contact-us
            if ($form->save()) {
                $format = config('admin.date_format') ?? 'd.m.Y H:i';
                $data['date'] = date($format);

                // Письмо пользователю
                try {
                    $title = __("{$this->lang}::s.You_have_filled_out_form") . config('add.domain');
                    $body = __("{$this->lang}::s.Your_form_has_been_received");

                    Mail::to($data['email'])
                        ->send(new SendMail($title, $body));

                } catch (\Exception $e) {
                    Main::getError("Error sending email User: {$e}", __METHOD__, false);
                }

                // Письмо администратору
                try {
                    $formName = Str::snake(__FUNCTION__); // Из contactUs будет contact_us
                    $template = 'table_form'; // Все данные в таблице
                    $title = __("{$this->lang}::s.Completed_form", ['name' => $formName]) . config('add.domain');
                    $email_admin = HelpersStr::strToArr(Main::site('admin_email'));

                    if ($email_admin) {
                        Mail::to($email_admin)
                            ->send(new SendMail($title, null, $data, $template));
                    }

                } catch (\Exception $e) {
                    Main::getError("Error sending email Admin: {$e}", __METHOD__, false);
                }

                // Сообщение об успехе
                session()->flash('success', __("{$this->lang}::s.Your_form_successfully"));
                return redirect()->route('index');
            }
        }
        // Сообщение что-то пошло не так
        Main::getError("{$this->class} request", __METHOD__);
    }
}
