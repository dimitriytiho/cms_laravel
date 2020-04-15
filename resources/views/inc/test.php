Если здесь нужны данные из БД, то в моделе сделать метод, в котором получить данные и вывести их, как в коде ниже.
Переменная $values передаётся из контроллера.
Можно использовать скрипты, они будут перенесены вниз страницы.
<br>
<?php

echo \App\Helpers\Str::arrToStr([1,2,3]);
echo '<br>';
echo \Illuminate\Support\Str::lower(' NEW Str');
echo '<br>';
if (!empty($values)) echo $values->title;

?>
<script>
    //alert(main_color)
    //$('body').css('background', '#ccc')
</script>
