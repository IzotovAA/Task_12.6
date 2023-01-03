<?php
$example_persons_array = [
    [
        'fullname' => 'Иванов Иван Иванович',
        'job' => 'tester',
    ],
    [
        'fullname' => 'Степанова Наталья Степановна',
        'job' => 'frontend-developer',
    ],
    [
        'fullname' => 'Пащенко Владимир Александрович',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Громов Александр Иванович',
        'job' => 'fullstack-developer',
    ],
    [
        'fullname' => 'Славин Семён Сергеевич',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Цой Владимир Антонович',
        'job' => 'frontend-developer',
    ],
    [
        'fullname' => 'Быстрая Юлия Сергеевна',
        'job' => 'PR-manager',
    ],
    [
        'fullname' => 'Шматко Антонина Сергеевна',
        'job' => 'HR-manager',
    ],
    [
        'fullname' => 'аль-Хорезми Мухаммад ибн-Муса',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Бардо Жаклин Фёдоровна',
        'job' => 'android-developer',
    ],
    [
        'fullname' => 'Шварцнегер Арнольд Густавович',
        'job' => 'babysitter',
    ],
];


// принимает строку с ФИО, возвращает массив из разделённых ФИО
function getPartsFromFullname($string)
{
    $array_keys = ['surname', 'name', 'patronomyc'];
    $array_values = explode(' ', $string);
    return array_combine($array_keys, $array_values);
}
// ...


// принимает 3 аргумента (ФИО), возвращает аргументы склеенные в строку через пробел
function getFullnameFromParts($surname, $name, $patronomyc)
{
    return $surname . ' ' . $name . ' ' . $patronomyc;
}
// ...


// принимает строку ФИО, возвращает строку с именем и сокращённой фамилией
function getShortName($string)
{
    $array = getPartsFromFullname($string);
    return $array['name'] . ' ' . mb_substr($array['surname'], 0, 1) . '.';
}
// ...


// принимает строку ФИО, возвращает 1 (мужчина), -1 (женщина) или 0 (пол не определён)
function getGenderFromName($string)
{
    $sex_sign = 0;
    $array = getPartsFromFullname($string);
    if (mb_substr($array['patronomyc'], -3) === 'вна') {
        $sex_sign--;
    } else if (mb_substr($array['name'], -1) === 'а') {
        $sex_sign--;
    } else if (mb_substr($array['surname'], -2) === 'ва') {
        $sex_sign--;
    } else if (mb_substr($array['patronomyc'], -2) === 'ич') {
        $sex_sign++;
    } else if (
        mb_substr($array['name'], -1) === 'й' ||
        mb_substr($array['name'], -1) === 'н'
    ) {
        $sex_sign++;
    } else if (mb_substr($array['surname'], -1) === 'в') {
        $sex_sign++;
    }

    return $sex_sign <=> 0;
}
// ...


// принимает массив, формирует гендерный состав людей из массива (в процентах)
function getGenderDescription($array)
{
    $male_array = array_filter($array, function ($arr) {
        if (getGenderFromName($arr['fullname']) === 1) {
            return true;
        } else return false;
    });

    $female_array = array_filter($array, function ($arr) {
        if (getGenderFromName($arr['fullname']) === -1) {
            return true;
        } else return false;
    });

    $undefined_array = array_filter($array, function ($arr) {
        if (getGenderFromName($arr['fullname']) === 0) {
            return true;
        } else return false;
    });

    $male_amount = count($male_array);
    $female_amount = count($female_array);
    $undefined_amount = count($undefined_array);
    $total_amount = $male_amount + $female_amount + $undefined_amount;
    $male_percent = round($male_amount / $total_amount * 100);
    $female_percent = round($female_amount / $total_amount * 100);
    $undefined_percent = round($undefined_amount / $total_amount * 100);
    $output = <<<MYHEREDOCTEXT
<br/>
Гендерный состав аудитории:<br/>
---------------------------<br/>
Мужчины - $male_percent%<br/>
Женщины - $female_percent%<br/>
Не удалось определить - $undefined_percent%<br/>
<br/>
MYHEREDOCTEXT;
    echo $output;
}
// ...


// принимает три аргумента (ФИО) и массив, ищет пару противоположного пола в массиве
function getPerfectPartner($surname, $name, $patronomyc, $array)
{
    $surname = mb_convert_case($surname, MB_CASE_TITLE_SIMPLE);
    $name = mb_convert_case($name, MB_CASE_TITLE_SIMPLE);
    $patronomyc = mb_convert_case($patronomyc, MB_CASE_TITLE_SIMPLE);
    $first_full_name = getFullnameFromParts($surname, $name, $patronomyc);
    $first_sex = getGenderFromName($first_full_name);
    if ($first_sex === 0) {
        echo "<br/>" . "\n" . 'Не возможно подобрать пару, пол не определён' . "\n" . "<br/>";
        return 'Пол не определён';
    } else {
        $second_full_name = $array[rand(0, count($array) - 1)]['fullname'];
        $second_sex = getGenderFromName($second_full_name);
        if ($first_sex !== $second_sex && $second_sex !== 0) {
            $first_short_name = getShortName($first_full_name);
            $second_short_name = getShortName($second_full_name);
            $ideal_persent = round(random_float(50, 100), 2);
            $output = <<<MYHEREDOCTEXT
<br/>
$first_short_name + $second_short_name =<br/>
\u{2661} Идеально на $ideal_persent% \u{2661}<br/>

MYHEREDOCTEXT;
            echo $output;
        } else getPerfectPartner($surname, $name, $patronomyc, $array);
    }
}
// ...


// генерирует случайное число с плавающей точкой
function random_float($min, $max)
{
    return ($min + lcg_value() * (abs($max - $min)));
}
// ...


// для OSPanel
// проверка корректности работы функции getGenderFromName
echo 'Проверка корректности работы функции getGenderFromName' . "\n" . "<br/>";
echo '----------------------------------------------------------' . "\n" . "<br/>";
foreach ($example_persons_array as $key => $value) {
    echo $value['fullname'] . ': ' . getGenderFromName($value['fullname']) . "\n" . "<br/>";
}
echo '----------------------------------------------------------' . "\n" . "<br/>";
// ...

// проверка корректности работы функции getGenderDescription
echo "<br/>" . "\n" . 'Проверка корректности работы функции getGenderDescription' . "\n" . "<br/>";
echo '----------------------------------------------------------' . "\n" . "<br/>";
getGenderDescription($example_persons_array);
echo '----------------------------------------------------------' . "\n" . "<br/>";
// ...

// проверка корректности работы функции getPerfectPartner
echo "<br/>" . "\n" . 'проверка корректности работы функции getPerfectPartner' . "\n" . "<br/>";
echo '----------------------------------------------------------' . "\n" . "<br/>";
getPerfectPartner('аль-Хорезми', 'Мухаммад', 'ибн-Муса', $example_persons_array);
getPerfectPartner('ивАНоВ', 'иВаН', 'иВанОВиЧ', $example_persons_array);
getPerfectPartner('иВАноВа', 'еЛеНа', 'иванОВНа', $example_persons_array);
echo '----------------------------------------------------------';
// ...
// для OSPanel


// для PHP песочницы
// // проверка корректности работы функции getGenderFromName
// echo "\n" . "\n" . 'Проверка корректности работы функции getGenderFromName' . "\n";
// echo '----------------------------------------------------------' . "\n";
// foreach ($example_persons_array as $key => $value) {
//     echo $value['fullname'] . ': ' . getGenderFromName($value['fullname']) . "\n";
// }
// echo '----------------------------------------------------------' . "\n";
// // ...

// // проверка корректности работы функции getGenderDescription
// echo "\n" . 'Проверка корректности работы функции getGenderDescription' . "\n";
// echo '----------------------------------------------------------' . "\n";
// getGenderDescription($example_persons_array);
// echo '----------------------------------------------------------' . "\n";
// // ...

// // проверка корректности работы функции getPerfectPartner
// echo "\n" . 'проверка корректности работы функции getPerfectPartner' . "\n";
// echo '----------------------------------------------------------' . "\n";
// getPerfectPartner('аль-Хорезми', 'Мухаммад', 'ибн-Муса', $example_persons_array);
// getPerfectPartner('ивАНоВ', 'иВаН', 'иВанОВиЧ', $example_persons_array);
// getPerfectPartner('иВАноВа', 'еЛеНа', 'иванОВНа', $example_persons_array);
// echo '----------------------------------------------------------';
// // ...
// для PHP песочницы
