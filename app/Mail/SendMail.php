<?php

namespace App\Mail;

use App\App;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    private $layout;
    public $title;
    public $body;
    public $values;
    public $template;
    public $h1;

    /**
     * Create a new message instance.
     *
     * @return void
     *
     * Переменные для отправки письма
     * $title - Заголовок письма.
     * $body - Содержимое письма, можно просто текст или вёрстку. Если используется $template дополнительный вид, то этот параметр не используется, передайте null, необязательный параметр.
     * $values - Данные для использования в видах, необязательный параметр.
     * $template - Название вида для оптравки письма из папки views/mail (к примеру user), необязательный параметр.
     * $h1 - Если нужно H1 передать из вида $template, то передайте null, тогда заголовок $title используйте в виде, который передаёте в $template, необязательный параметр.
     *
     */
    public function __construct($title, $body = null, $values = null, $template = null, $h1 = true)
    {
        $this->layout = 'layouts.mail';
        $this->title = $title;
        $this->body = $body;
        $this->values = $values;
        $this->template = $template;
        $this->h1 = $h1;

        if (!view()->exists($this->layout)) {
            App::getError("View {$this->layout} not found", __METHOD__, false);
        }
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $title = $this->title;
        $values = $this->values;
        $h1 = $this->h1;
        $body = $this->body;
        $view = null;


        if ($this->template && view()->exists("mail.{$this->template}")) {
            $view = view("mail.{$this->template}", compact('title', 'values', 'body'))->render();
        }


        $site_name = App::get('settings')['site_name'] ?? ' ';
        $color = config('add.scss.primary', '#ccc');
        $email = App::get('settings')['site_email'] ?? ' ';
        $tel = isset(App::get('settings')['tel']) ? __('s.or_call') .  App::get('settings')['tel'] : null;


        return $this->view($this->layout)
            ->subject(__('s.Information_letter'))
            ->with(compact('view', 'title', 'values', 'h1', 'body', 'site_name', 'color', 'email', 'tel'));
    }
}
